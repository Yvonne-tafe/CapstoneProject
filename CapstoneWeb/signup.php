<?php
require_once __DIR__.'/config/auth.php'; header('Cache-Control: no-store');
$errors=array(); $data=array(); foreach (array('user_first_name','user_surname','user_email','user_phone_number','user_address','user_gender','preferred_contact_method','user_date_of_birth') as $key) { $data[$key]=authInput($key); }
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $errors=authProfileValidate($data); if (!authCsrf()) { $errors[]='Your form expired. Please try again.'; }
 $password=authPassword('password');
 if (strpos($password,chr(0))!==false) { $errors[]='The password contains an unsupported character.'; }
 if (strlen($password)<15 || strlen($password)>72) { $errors[]='Use a password between 15 and 72 bytes (15–72 characters for English text).'; }
 if ($password!==authPassword('confirm_password')) { $errors[]='The passwords do not match.'; }
 if (authInput('accept_privacy')!=='yes') { $errors[]='Please agree to the Privacy Policy.'; }
 if (!$errors) { try {
  $q=authDb()->prepare('INSERT INTO User (role_id,user_first_name,user_surname,user_email,password_hash) VALUES (1,?,?,?,?)'); $q->execute(array($data['user_first_name'],$data['user_surname'],$data['user_email'],password_hash($password,PASSWORD_DEFAULT)));
  header('Location: ./login.php?registered=1'); exit;
 } catch (Exception $e) {
  if ($e instanceof PDOException && isset($e->errorInfo[1]) && (int)$e->errorInfo[1]===1062) { $errors[]='This email address is already registered. Please log in.'; }
  else { error_log('Signup: '.$e->getMessage()); http_response_code(503); $errors[]='Registration is temporarily unavailable. Please try again later.'; }
 } }
}
require_once __DIR__.'/layout.php'; showHeader('Create an account');
?>
<section class="form-section" aria-labelledby="signup-heading"><?php showBreadcrumb(array('Create an account'=>null)); ?>
<div class="section-header"><span class="section-badge">Sign up</span><h1 id="signup-heading">Create your CityLink account</h1><p>Enter your name, email and password. All fields are required.</p></div><?php authErrors($errors); ?>
<form class="form-table-container" method="post" action="./signup.php"><input type="hidden" name="csrf_token" value="<?= serviceEscape(authToken()) ?>"><table class="form-table"><tbody>
<?php authField('user_first_name','First name',$data['user_first_name'],'text',true,100); authField('user_surname','Surname',$data['user_surname'],'text',true,100); authField('user_email','Email address',$data['user_email'],'email',true,254); authField('password','Password','','password',true,72); authField('confirm_password','Confirm password','','password',true,72); ?>
</tbody></table><p>Use a long, unique password: 15–72 English characters. Spaces are welcome. Non-English characters can use more than one byte; the maximum is 72 bytes.</p>
<label><input type="checkbox" name="accept_privacy" value="yes" required <?= authInput('accept_privacy')==='yes'?'checked':'' ?>> I agree to the <a href="./privacy.php">Privacy Policy</a>.</label>
<hr><button class="btn btn-primary" type="submit">Create account</button><p>Already have an account? <a href="./login.php">Log in</a>.</p></form></section><?php showFooter(); ?>
