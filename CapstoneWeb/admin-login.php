<?php
require_once __DIR__.'/config/auth.php'; header('Cache-Control: no-store');
$errors=array(); $email=authInput('email');
if ($_SERVER['REQUEST_METHOD']==='POST') {
 if (!authCsrf()) { $errors[]='Your form expired. Please try again.'; }
 else { try {
  $keys=array('email:'.hash('sha256',strtolower($email)),'ip:'.hash('sha256',isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:''));
  $q=authDb()->prepare("SELECT COUNT(*) FROM AuditLog WHERE event_type='login_attempt' AND event_result='failure' AND occurred_at>DATE_SUB(NOW(),INTERVAL 15 MINUTE) AND audit_log_note IN (?,?)"); $q->execute($keys); $limited=(int)$q->fetchColumn()>=10; $q->closeCursor();
  if ($limited) { $errors[]='Too many attempts. Please try again in 15 minutes.'; }
  else {
   $q=authDb()->prepare('SELECT u.user_id,u.password_hash,r.role_name FROM User u JOIN Role r ON r.role_id=u.role_id WHERE u.user_email=?'); $q->execute(array($email)); $user=$q->fetch(); $q->closeCursor();
   $valid=password_verify(authPassword('password'),$user?$user['password_hash']:'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');
   if ($user && $valid && $user['role_name']==='admin') { authLogin($user['user_id']); header('Location: ./admin.php'); exit; }
   $q=authDb()->prepare("INSERT INTO AuditLog (event_type,event_result,audit_log_note) VALUES ('login_attempt','failure',?)"); foreach ($keys as $key) { $q->execute(array($key)); }
   $errors[]='Email, password or administrator access is incorrect.';
  }
 } catch (Exception $e) { error_log('Login: '.$e->getMessage()); http_response_code(503); $errors[]='Login is temporarily unavailable. Please try again later.'; } }
}
require_once __DIR__.'/layout.php'; showHeader('Administrator log in');
?>
<section class="form-section" aria-labelledby="login-heading">
<?php showBreadcrumb(array('Log in'=>null)); ?>
<div class="section-header"><span class="section-badge">Administration</span><h1 id="login-heading">Administrator log in</h1><p>Enter the email address and password for your administrator account.</p></div>
<?php authErrors($errors); ?>
<form class="form-table-container" method="post" action="./admin-login.php"><input type="hidden" name="csrf_token" value="<?= serviceEscape(authToken()) ?>">
<table class="form-table"><tbody><?php authField('email','Email address',$email,'email',true,254); ?>
<tr><th scope="row"><label for="password">Password</label></th><td><input id="password" name="password" type="password" autocomplete="current-password" required></td></tr>
</tbody></table><hr><button class="btn btn-primary" type="submit">Log in</button><p>Need a member account? <a href="./login.php">Member log in</a>.</p></form></section><?php showFooter(); ?>
