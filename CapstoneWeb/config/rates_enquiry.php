<?php
require_once __DIR__.'/auth.php';
function ratesEnquiryTypes() {
    return array('notice-copy'=>'Rates notice or account copy','payment'=>'Payment or account balance','payment-plan'=>'Payment arrangement','concession'=>'Pensioner or concession rebate','valuation'=>'Property valuation','ownership'=>'Ownership or postal address update','fees'=>'Fees, interest or overdue rates','other'=>'Other rates enquiry');
}
function ratesEnquiryValidate($values) {
    $errors=array(); $types=ratesEnquiryTypes();
    if (!isset($types[$values['enquiry_type']])) { $errors[]='Choose a valid enquiry type.'; }
    foreach (array('assessment_number'=>50,'property_address'=>255,'full_name'=>150,'email'=>254,'phone'=>30,'enquiry_details'=>2000) as $field=>$max) {
        $value=$values[$field]; $length=preg_match_all('/./us',$value,$matches);
        if ($length===false || $length>$max || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/',$value)) { $errors[]=ucfirst(str_replace('_',' ',$field)).' must contain valid text of no more than '.$max.' characters.'; }
    }
    foreach (array('property_address'=>'Property address','full_name'=>'Full name','enquiry_details'=>'Enquiry details') as $key=>$label) {
        if (preg_match('/\S/u',$values[$key])!==1) { $errors[]=$label.' is required.'; }
    }
    if (!filter_var($values['email'],FILTER_VALIDATE_EMAIL) || strlen($values['email'])>254) { $errors[]='Enter a valid email address.'; }
    if (!in_array($values['response_method'],array('email','phone'),true)) { $errors[]='Choose a valid response method.'; }
    if ($values['response_method']==='phone' && preg_match('/\S/u',$values['phone'])!==1) { $errors[]='Enter a contact number for a phone response.'; }
    return $errors;
}
function ratesEnquiryPageData() {
    header('Cache-Control: no-store');
    $data=array('values'=>array('enquiry_type'=>'','assessment_number'=>'','property_address'=>'','full_name'=>'','email'=>'','phone'=>'','enquiry_details'=>'','response_method'=>'email'),'errors'=>array(),'success'=>null);
    if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['new']) && $_GET['new']==='1') {
        unset($_SESSION['rates_receipt'],$_SESSION['rates_form_token']);
    }
    if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_SESSION['rates_receipt'])) { $data['success']=$_SESSION['rates_receipt']; }
    try {
        $member=authCurrentUser();
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            foreach ($data['values'] as $key=>$unused) { $data['values'][$key]=authInput($key); }
            $data['errors']=ratesEnquiryValidate($data['values']);
            if (!authCsrf() || empty($_SESSION['rates_form_token']) || !hash_equals($_SESSION['rates_form_token'],authInput('form_token'))) { $data['errors'][]='This form expired or was already submitted. Please try again.'; }
            if (!$data['errors']) {
                $v=$data['values'];
                $q=authDb()->prepare('INSERT INTO RatesEnquiry (user_id,enquiry_type,assessment_number,property_address,full_name,email,phone,enquiry_details,response_method) VALUES (?,?,?,?,?,?,?,?,?)');
                $q->execute(array($member?$member['user_id']:null,$v['enquiry_type'],$v['assessment_number']===''?null:$v['assessment_number'],$v['property_address'],$v['full_name'],$v['email'],$v['phone']===''?null:$v['phone'],$v['enquiry_details'],$v['response_method']));
                $_SESSION['rates_receipt']='R-'.authDb()->lastInsertId(); unset($_SESSION['rates_form_token']);
                header('Location: ./rates-enquiry.php',true,303); exit;
            }
            http_response_code(422);
        } elseif ($member) {
            $names=array(); foreach (array('user_first_name','user_middle_name','user_surname') as $key) { if (!empty($member[$key])) { $names[]=$member[$key]; } }
            $data['values']['full_name']=implode(' ',$names); $data['values']['email']=$member['user_email']; $data['values']['phone']=(string)$member['user_phone_number'];
            // The property may differ from the member's contact address; leave it blank.
        }
    } catch (Exception $e) { error_log('Rates enquiry: '.$e->getMessage()); http_response_code(503); $data['errors'][]='Rates enquiries are temporarily unavailable. Your enquiry has not been submitted. Please try again later.'; }
    if (empty($_SESSION['rates_form_token'])) { $_SESSION['rates_form_token']=bin2hex(random_bytes(32)); }
    $data['form_token']=$_SESSION['rates_form_token'];
    return $data;
}
