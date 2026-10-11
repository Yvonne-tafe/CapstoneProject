<?php
require_once __DIR__.'/auth.php';
function feedbackCategories() {
    $q=authDb()->query('SELECT feedback_category_id,feedback_category_name FROM FeedbackCategory ORDER BY feedback_category_id'); return $q->fetchAll(PDO::FETCH_ASSOC);
}
function feedbackPageData() {
    header('Cache-Control: no-store');
    $data=array('values'=>array('feedback_type'=>'','subject'=>'','message'=>'','full_name'=>'','email'=>'','response_requested'=>''),'categories'=>array(),'errors'=>array(),'success'=>null);
    if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['new']) && $_GET['new']==='1') {
        unset($_SESSION['feedback_receipt'],$_SESSION['feedback_form_token']);
    }
    if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_SESSION['feedback_receipt'])) { $data['success']=$_SESSION['feedback_receipt']; }
    try {
        $data['categories']=feedbackCategories(); $member=authCurrentUser();
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            foreach ($data['values'] as $key=>$unused) { $data['values'][$key]=authInput($key); }
            $v=$data['values']; $ids=array(); foreach ($data['categories'] as $category) { $ids[]=(string)$category['feedback_category_id']; }
            if (!in_array($v['feedback_type'],$ids,true)) { $data['errors'][]='Choose a valid feedback type.'; }
            foreach (array('subject'=>150,'message'=>2000,'full_name'=>150,'email'=>254) as $key=>$limit) {
                $length=preg_match_all('/./us',$v[$key],$matches);
                if ($length===false || $length>$limit || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/',$v[$key])) { $data['errors'][]=ucfirst(str_replace('_',' ',$key)).' must contain valid text of no more than '.$limit.' characters.'; }
            }
            if (preg_match('/\S/u',$v['subject'])!==1 || preg_match('/\S/u',$v['message'])!==1) { $data['errors'][]='Subject and feedback message are required.'; }
            if (!in_array($v['response_requested'],array('','yes'),true)) { $data['errors'][]='Choose a valid response preference.'; }
            if (($v['email']!=='' || $v['response_requested']==='yes') && (!filter_var($v['email'],FILTER_VALIDATE_EMAIL) || strlen($v['email'])>254)) { $data['errors'][]='Enter a valid email address; it is required when requesting a response.'; }
            if (!authCsrf() || empty($_SESSION['feedback_form_token']) || !hash_equals($_SESSION['feedback_form_token'],authInput('form_token'))) { $data['errors'][]='This form expired or was already submitted. Please try again.'; }
            if (!$data['errors']) {
                $q=authDb()->prepare('INSERT INTO Feedback (user_id,feedback_category_id,feedback_name,feedback_email,feedback_message,response_request) VALUES (?,?,?,?,?,?)');
                $q->execute(array($member?$member['user_id']:null,$v['feedback_type'],$v['full_name']===''?null:$v['full_name'],$v['email']===''?null:$v['email'], 'Subject: '.$v['subject']."\n\n".$v['message'],$v['response_requested']==='yes'?1:0));
                $_SESSION['feedback_receipt']='FB-'.authDb()->lastInsertId(); unset($_SESSION['feedback_form_token']); header('Location: ./feedback.php',true,303); exit;
            }
            http_response_code(422);
        } elseif ($member) {
            $data['values']['full_name']=trim($member['user_first_name'].' '.($member['user_middle_name']?$member['user_middle_name'].' ':'').$member['user_surname']); $data['values']['email']=$member['user_email'];
        }
    } catch (Exception $e) { error_log('Feedback: '.$e->getMessage()); http_response_code(503); $data['errors'][]='Feedback is temporarily unavailable. Your feedback has not been submitted. Please try again later.'; }
    if (empty($_SESSION['feedback_form_token'])) { $_SESSION['feedback_form_token']=bin2hex(random_bytes(32)); }
    $data['form_token']=$_SESSION['feedback_form_token']; return $data;
}
