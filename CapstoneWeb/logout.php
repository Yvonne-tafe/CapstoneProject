<?php
require_once __DIR__.'/config/auth.php'; header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD']!=='POST' || !authCsrf()) { http_response_code(403); exit('Invalid logout request. Return to your account and try again.'); }
try {
 if (!empty($_SESSION['user_session_token'])) { $q=authDb()->prepare('UPDATE UserSession SET revoked_at=NOW() WHERE session_token_hash=?'); $q->execute(array(hash('sha256',$_SESSION['user_session_token']))); }
} catch (Exception $e) { error_log('Logout: '.$e->getMessage()); http_response_code(503); exit('Logout is temporarily unavailable. Please try again.'); }
$_SESSION=array(); $params=session_get_cookie_params(); setcookie(session_name(),'',time()-3600,$params['path'],$params['domain'],$params['secure'],$params['httponly']); session_destroy(); header('Location: ./login.php'); exit;
