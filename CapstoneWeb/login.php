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
   $q=authDb()->prepare('SELECT user_id,password_hash FROM User WHERE user_email=?'); $q->execute(array($email)); $user=$q->fetch(); $q->closeCursor();
   $valid=password_verify(authPassword('password'),$user?$user['password_hash']:'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');
   if ($user && $valid) { authLogin($user['user_id']); header('Location: ./account.php'); exit; }
   $q=authDb()->prepare("INSERT INTO AuditLog (event_type,event_result,audit_log_note) VALUES ('login_attempt','failure',?)"); foreach ($keys as $key) { $q->execute(array($key)); }
   $errors[]='Email or password is incorrect.';
  }
 } catch (Exception $e) { error_log('Login: '.$e->getMessage()); http_response_code(503); $errors[]='Login is temporarily unavailable. Please try again later.'; } }
}
require_once __DIR__.'/layout.php'; showHeader('Log in');
?>
<section class="form-section" aria-labelledby="login-heading">
<?php showBreadcrumb(array('Log in'=>null)); ?>
<div class="section-header"><span class="section-badge">Account access</span><h1 id="login-heading">Log in to CityLink</h1><p>Enter your email address and password.</p></div>
<?php authErrors($errors); if (isset($_GET['registered'])): ?><p role="status">Your account has been created. Please log in.</p><?php endif; ?>
<form class="form-table-container" method="post" action="./login.php"><input type="hidden" name="csrf_token" value="<?= serviceEscape(authToken()) ?>">
<table class="form-table"><tbody><?php authField('email','Email address',$email,'email',true,254); ?>
<tr><th scope="row"><label for="password">Password</label></th><td><input id="password" name="password" type="password" autocomplete="current-password" required></td></tr>
</tbody></table><hr><button class="btn btn-primary" type="submit">Log in</button><p>Do not have an account? <a href="./signup.php">Create an account</a>.</p></form></section><?php showFooter(); ?>
