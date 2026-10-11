<?php
require_once __DIR__.'/config/auth.php'; $errors=array(); $user=null;
try { $user=authRequireUser(); } catch (Exception $e) { error_log('Account edit: '.$e->getMessage()); http_response_code(503); $errors[]='Your account is temporarily unavailable. Please try again later.'; }
$keys=array('user_first_name','user_surname','user_email','user_phone_number','user_address','user_gender','preferred_contact_method','user_date_of_birth'); $data=array();
if ($user) {
 foreach ($keys as $key) { $data[$key]=$_SERVER['REQUEST_METHOD']==='POST'?authInput($key):(string)$user[$key]; }
 if ($_SERVER['REQUEST_METHOD']==='POST') {
  $errors=authProfileValidate($data); if (!authCsrf()) { $errors[]='Your form expired. Please try again.'; }
  $emailChanged=$data['user_email']!==$user['user_email'];
  if ($emailChanged && !password_verify(authPassword('current_password'),$user['password_hash'])) { $errors[]='Enter your current password to change your email address.'; }
  if (!$errors) { try {
   $db=authDb(); $db->beginTransaction(); $values=array(); foreach ($keys as $key) { $values[]=$data[$key]===''?null:$data[$key]; } $values[]=$user['user_id'];
   $q=$db->prepare('UPDATE User SET user_first_name=?,user_surname=?,user_email=?,user_phone_number=?,user_address=?,user_gender=?,preferred_contact_method=?,user_date_of_birth=? WHERE user_id=?'); $q->execute($values);
   if ($emailChanged) { $q=$db->prepare('UPDATE UserSession SET revoked_at=NOW() WHERE user_id=? AND session_token_hash<>? AND revoked_at IS NULL'); $q->execute(array($user['user_id'],hash('sha256',$_SESSION['user_session_token']))); }
   $db->commit(); header('Location: ./account.php?updated=1'); exit;
  } catch (Exception $e) {
   if (isset($db) && $db->inTransaction()) { $db->rollBack(); }
   if ($e instanceof PDOException && isset($e->errorInfo[1]) && (int)$e->errorInfo[1]===1062) { $errors[]='This email address is already registered.'; }
   else { error_log('Account save: '.$e->getMessage()); http_response_code(503); $errors[]='Your changes could not be saved. Please try again later.'; }
  } }
 }
}
require_once __DIR__.'/layout.php'; showHeader('Edit account');
?>
<section class="form-section" aria-labelledby="account-edit-heading"><?php showBreadcrumb(array('My account'=>'./account.php','Edit details'=>null)); ?>
<div class="section-header"><span class="section-badge">My account</span><h1 id="account-edit-heading">Edit your account details</h1><p>First name, surname and email are required. Other details are optional.</p></div><?php authErrors($errors); if ($user): ?>
<form class="form-table-container" method="post" action="./account-edit.php"><input type="hidden" name="csrf_token" value="<?= serviceEscape(authToken()) ?>"><table class="form-table"><tbody>
<?php authField('user_first_name','First name (required)',$data['user_first_name'],'text',true,100); authField('user_surname','Surname (required)',$data['user_surname'],'text',true,100); authField('user_email','Email address (required)',$data['user_email'],'email',true,254); ?>
<tr><th scope="row"><label for="current_password">Current password (when changing email)</label></th><td><input id="current_password" name="current_password" type="password" autocomplete="current-password"><p>Changing your email also signs out your other sessions.</p></td></tr>
<?php authField('user_phone_number','Contact number',$data['user_phone_number'],'tel',false,30); authField('user_address','Full address, including suburb and postcode',$data['user_address']); authField('user_date_of_birth','Date of birth',$data['user_date_of_birth'],'date'); ?>
<?php foreach (array('user_gender'=>array(''=>'Not provided','female'=>'Female','male'=>'Male','non-binary'=>'Non-binary','self-described'=>'Prefer to self-describe','not-say'=>'Prefer not to say'),'preferred_contact_method'=>array(''=>'No preference','email'=>'Email','phone'=>'Phone')) as $key=>$options): ?>
<tr><th scope="row"><label for="<?= serviceEscape($key) ?>"><?= $key==='user_gender'?'Gender':'Preferred contact method' ?></label></th><td><select id="<?= serviceEscape($key) ?>" name="<?= serviceEscape($key) ?>">
<?php foreach ($options as $value=>$label): ?><option value="<?= serviceEscape($value) ?>" <?= $data[$key]===$value?'selected':'' ?>><?= serviceEscape($label) ?></option><?php endforeach; ?></select></td></tr>
<?php endforeach; ?></tbody></table><hr><button class="btn btn-primary" type="submit">Save changes</button><p><a href="./account.php">Cancel</a></p></form>
<?php endif; ?></section><?php showFooter(); ?>
