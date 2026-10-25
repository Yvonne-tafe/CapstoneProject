<?php
require_once __DIR__ . '/service_data.php';
// PHP 5.4 compatibility: bcrypt via crypt, OpenSSL CSPRNG and timing-safe comparison.
if (!function_exists('random_bytes')) {
    function random_bytes($length) {
        $strong=false; $bytes=openssl_random_pseudo_bytes($length,$strong);
        if (!$strong || $bytes===false || strlen($bytes)!==$length) { throw new RuntimeException('Secure randomness unavailable.'); }
        return $bytes;
    }
}
if (!function_exists('hash_equals')) {
    function hash_equals($known,$supplied) {
        if (!is_string($known) || !is_string($supplied) || strlen($known)!==strlen($supplied)) { return false; }
        $difference=0; for ($i=0;$i<strlen($known);$i++) { $difference |= ord($known[$i]) ^ ord($supplied[$i]); }
        return $difference===0;
    }
}
if (!function_exists('password_hash')) {
    define('PASSWORD_DEFAULT',1);
    function password_hash($password,$algorithm) {
        $salt=substr(str_replace('+','.',base64_encode(random_bytes(16))),0,22);
        $hash=crypt($password,'$2y$10$'.$salt);
        if (strlen($hash)!==60) { throw new RuntimeException('Password hashing unavailable.'); }
        return $hash;
    }
    function password_verify($password,$hash) { return is_string($hash) && strlen($hash)===60 && hash_equals($hash,crypt($password,$hash)); }
}
/**
 * Start the PHP session once and configure its cookie transport settings.
 * The secure-cookie flag follows the current HTTPS request.
 */
function authStart() {
    if (session_id() === '') {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        $secure=!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off';
        if (PHP_VERSION_ID>=70300) { session_set_cookie_params(array('lifetime'=>0,'path'=>'/','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax')); }
        else { session_set_cookie_params(0,'/; SameSite=Lax','',$secure,true); }
        session_start();
    }
}
authStart();
/**
 * Reuse the shared PDO connection for authentication and account queries.
 * @return PDO
 */
function authDb() { static $db; if (!$db) { $db=serviceDatabase(); } return $db; }
/**
 * Return the session CSRF token, creating it on first use.
 * @return string Hexadecimal token used by server-rendered forms.
 */
function authToken() { if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token']=bin2hex(random_bytes(32)); } return $_SESSION['csrf_token']; }
/**
 * Compare the submitted CSRF token with the current session token.
 * @return bool False for absent, non-string or mismatched tokens.
 */
function authCsrf() { return isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) && hash_equals(authToken(), $_POST['csrf_token']); }
/**
 * Read a scalar POST value and trim surrounding whitespace.
 * @return string Empty string for absent or non-string input.
 */
function authInput($key) { return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : ''; }
/**
 * Read a POST password without trimming meaningful spaces.
 * @return string Empty string for absent or non-string input.
 */
function authPassword($key) { return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : ''; }
/**
 * Resolve identity from the server-side login token, not a request user ID.
 * Only unrevoked, unexpired UserSession records are accepted.
 * @return array|null User fields plus role_name, or null when unauthenticated.
 * @throws PDOException When the database lookup fails.
 */
function authCurrentUser() {
    if (empty($_SESSION['user_session_token']) || !is_string($_SESSION['user_session_token'])) { return null; }
    $q=authDb()->prepare('SELECT u.*, r.role_name FROM UserSession s JOIN User u ON u.user_id=s.user_id JOIN Role r ON r.role_id=u.role_id WHERE s.session_token_hash=? AND s.revoked_at IS NULL AND s.expires_at>NOW()');
    $q->execute(array(hash('sha256',$_SESSION['user_session_token'])));
    $user=$q->fetch(PDO::FETCH_ASSOC); $q->closeCursor();
    return $user ?: null;
}
/**
 * Require authentication before rendering a member page.
 * Unauthenticated requests redirect to login and terminate.
 * @return array The authenticated user; database errors propagate to the caller.
 */
function authRequireUser() {
    header('Cache-Control: no-store');
    $user=authCurrentUser();
    if (!$user) { header('Location: ./login.php'); exit; }
    return $user;
}
/**
 * Create a two-hour database session after the caller verifies credentials.
 * Store only the token hash in MySQL; keep the original in the PHP session.
 * Revoke the previous login token and rotate the PHP session ID and CSRF token.
 * @param int $id The user ID already authenticated by the login controller.
 */
function authLogin($id) {
    $token=bin2hex(random_bytes(32));
    $q=authDb()->prepare('INSERT INTO UserSession (user_id,session_token_hash,expires_at) VALUES (?,?,DATE_ADD(NOW(),INTERVAL 2 HOUR))');
    $q->execute(array($id,hash('sha256',$token)));
    if (!empty($_SESSION['user_session_token'])) {
        $q=authDb()->prepare('UPDATE UserSession SET revoked_at=NOW() WHERE session_token_hash=?');
        $q->execute(array(hash('sha256',$_SESSION['user_session_token'])));
    }
    session_regenerate_id(true);
    $_SESSION['user_session_token']=$token;
    $_SESSION['csrf_token']=bin2hex(random_bytes(32));
}
/**
 * Render escaped validation messages in an accessible alert region.
 */
function authErrors($errors) {
    if (!$errors) { return; }
    echo '<div role="alert" class="booking-errors"><h2>Please check your details</h2><ul>';
    foreach ($errors as $error) { echo '<li>'.serviceEscape($error).'</li>'; }
    echo '</ul></div>';
}
/**
 * Render one labelled, escaped input row for account-related forms.
 */
function authField($key,$label,$value,$type='text',$required=false,$max=255) {
    echo '<tr><th scope="row"><label for="'.serviceEscape($key).'">'.serviceEscape($label).'</label></th><td><input id="'.serviceEscape($key).'" name="'.serviceEscape($key).'" type="'.serviceEscape($type).'" value="'.serviceEscape($value).'" maxlength="'.(int)$max.'"'.($required?' required':'').($type==='password'?' autocomplete="new-password"':'').'></td></tr>';
}
/**
 * Validate the shared registration/profile field structure.
 * Checks names, email, allowed options, contact preference and date of birth.
 * String length limits here use bytes; date checks use Perth local dates.
 * Password, CSRF and duplicate-email checks are handled by the calling page.
 * @return array Validation messages; an empty array indicates no detected errors.
 */
function authProfileValidate($data) {
    $errors=array();
    foreach (array('user_first_name'=>100,'user_surname'=>100,'user_email'=>254,'user_phone_number'=>30,'user_address'=>255,'user_gender'=>50,'preferred_contact_method'=>20) as $key=>$max) {
        if (strlen($data[$key])>$max) { $errors[]='The '.str_replace('_',' ',$key).' is too long.'; }
    }
    if ($data['user_first_name']==='' || $data['user_surname']==='') { $errors[]='First name and surname are required.'; }
    if (!filter_var($data['user_email'],FILTER_VALIDATE_EMAIL)) { $errors[]='Enter a valid email address.'; }
    if (!in_array($data['user_gender'],array('','female','male','non-binary','self-described','not-say'),true)) { $errors[]='Choose a valid gender option.'; }
    if (!in_array($data['preferred_contact_method'],array('','email','phone'),true)) { $errors[]='Choose a valid contact preference.'; }
    if ($data['preferred_contact_method']==='phone' && $data['user_phone_number']==='') { $errors[]='Enter a phone number for phone contact.'; }
    if ($data['user_date_of_birth']!=='') {
        $date=DateTime::createFromFormat('!Y-m-d',$data['user_date_of_birth'],new DateTimeZone('Australia/Perth'));
        if (!$date || $date->format('Y-m-d')!==$data['user_date_of_birth'] || $data['user_date_of_birth']>gmdate('Y-m-d',time()+28800) || $data['user_date_of_birth']<'1900-01-01') { $errors[]='Enter a valid date of birth between 1900 and today.'; }
    }
    return $errors;
}
