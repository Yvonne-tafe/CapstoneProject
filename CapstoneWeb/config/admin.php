<?php
require_once __DIR__.'/auth.php';
function adminRequire() {
    header('Cache-Control: no-store');
    try { $user=authCurrentUser(); }
    catch (Exception $e) { error_log('Admin authentication: '.$e->getMessage()); http_response_code(503); exit('Administration is temporarily unavailable.'); }
    if (!$user) { header('Location: ./admin-login.php'); exit; }
    if ($user['role_name']!=='admin') { http_response_code(403); exit('Administrator access is required.'); }
    return $user;
}
function adminDefinitions() {
    return array(
      'booking'=>array('title'=>'Bookings','table'=>'Booking','id'=>'booking_id','status'=>'booking_status_id','statuses'=>'BookingStatus','status_name'=>'booking_status_name','date'=>'booked_at','note'=>'booking_note','prefix'=>'S-','page'=>'admin-booking.php'),
      'facility'=>array('title'=>'Facility bookings','table'=>'FacilityBooking','id'=>'facility_booking_id','status'=>'booking_status_id','statuses'=>'BookingStatus','status_name'=>'booking_status_name','date'=>'booked_at','note'=>'booking_note','prefix'=>'F-','page'=>'admin-booking.php'),
      'feedback'=>array('title'=>'Feedback','table'=>'Feedback','id'=>'feedback_id','status'=>'feedback_status_id','statuses'=>'FeedbackStatus','status_name'=>'feedback_status_name','date'=>'submitted_at','note'=>'feedback_note','resolution'=>'feedback_resolution','prefix'=>'FB-','page'=>'admin-feedback.php'),
      'rates'=>array('title'=>'Rates enquiries','table'=>'RatesEnquiry','id'=>'rates_enquiry_id','status'=>'rates_enquiry_status_id','statuses'=>'RatesEnquiryStatus','status_name'=>'rates_enquiry_status_name','date'=>'submitted_at','note'=>'rates_enquiry_note','resolution'=>'rates_enquiry_resolution','prefix'=>'R-','page'=>'admin-rate-enquiry.php')
    );
}
function adminGet($key) { return isset($_GET[$key]) && is_string($_GET[$key])?$_GET[$key]:''; }
function adminTime($value) { return $value?(new DateTime($value,new DateTimeZone('Australia/Perth')))->format('d M Y, g:i a'):'Not recorded'; }
function adminRows($kind) {
    $defs=adminDefinitions(); $d=$defs[$kind];
    $sort=adminGet($kind.'_sort'); $dir=adminGet($kind.'_dir')==='asc'?'ASC':'DESC';
    $column=$sort==='status'?'st.'.$d['status_name']:'b.'.$d['date'];
    $join='';
    if ($kind==='booking') { $join=' JOIN ServiceSession x ON x.service_session_id=b.service_session_id JOIN Service s ON s.service_id=x.service_id'; $fields='s.service_title AS title,x.session_start_at,b.booking_first_name AS first_name,b.booking_surname AS surname,b.booking_email AS email'; }
    elseif ($kind==='facility') { $join=' JOIN FacilitySession x ON x.facility_session_id=b.facility_session_id JOIN Facility f ON f.facility_id=x.facility_id'; $fields='f.facility_name AS title,x.session_start_at,b.booking_first_name AS first_name,b.booking_surname AS surname,b.booking_email AS email'; }
    elseif ($kind==='feedback') { $join=' JOIN FeedbackCategory c ON c.feedback_category_id=b.feedback_category_id'; $fields='c.feedback_category_name AS title,b.feedback_name AS first_name,NULL AS surname,b.feedback_email AS email'; }
    else { $fields='b.enquiry_type AS title,b.full_name AS first_name,NULL AS surname,b.email,b.property_address'; }
    $q=authDb()->query('SELECT b.'.$d['id'].' AS id,b.'.$d['date'].' AS date,st.'.$d['status_name'].' AS status,'.$fields.' FROM '.$d['table'].' b JOIN '.$d['statuses'].' st ON st.'.$d['status'].'=b.'.$d['status'].$join.' ORDER BY '.$column.' '.$dir.',b.'.$d['id'].' DESC');
    return $q->fetchAll(PDO::FETCH_ASSOC);
}
function adminSortUrl($kind,$field) {
    $params=array(); foreach (adminDefinitions() as $key=>$unused) { foreach (array('sort','dir') as $suffix) { $name=$key.'_'.$suffix; $value=adminGet($name); if ($value!=='') { $params[$name]=$value; } } }
    $params[$kind.'_sort']=$field;
    $currentSort=adminGet($kind.'_sort')==='status'?'status':'date';
    $params[$kind.'_dir']=$currentSort===$field && adminGet($kind.'_dir')!=='asc'?'asc':'desc';
    return './admin.php?'.http_build_query($params).'#'.$kind;
}
function adminEditUrl($kind,$id) { $defs=adminDefinitions(); return './'.$defs[$kind]['page'].'?'.http_build_query(array('kind'=>$kind,'id'=>$id)); }
function adminStatusOptions($d) { return authDb()->query('SELECT '.$d['status'].' AS id,'.$d['status_name'].' AS name FROM '.$d['statuses'].' ORDER BY '.$d['status'])->fetchAll(PDO::FETCH_ASSOC); }
function adminSave($kind,$id,$status,$note,$resolution,$version) {
    $defs=adminDefinitions(); $d=$defs[$kind]; $db=authDb(); $db->beginTransaction();
    try {
        // Match booking creation's service -> session -> booking lock order.
        if ($kind==='booking' || $kind==='facility') {
            $sessionTable=$kind==='booking'?'ServiceSession':'FacilitySession'; $sessionKey=$kind==='booking'?'service_session_id':'facility_session_id';
            $q=$db->prepare('SELECT '.$sessionKey.' FROM '.$d['table'].' WHERE '.$d['id'].'=?'); $q->execute(array($id)); $sessionId=$q->fetchColumn(); $q->closeCursor();
            if ($kind==='booking') {
                $q=$db->prepare('SELECT service_id FROM ServiceSession WHERE service_session_id=?'); $q->execute(array($sessionId)); $serviceId=$q->fetchColumn(); $q->closeCursor();
                $q=$db->prepare('SELECT active_status_id,service_type_id FROM Service WHERE service_id=? FOR UPDATE'); $q->execute(array($serviceId)); $parent=$q->fetch(); $q->closeCursor();
                $q=$db->prepare('SELECT active_status_id FROM ServiceType WHERE service_type_id=?'); $q->execute(array($parent['service_type_id'])); $typeActive=$q->fetchColumn(); $q->closeCursor();
            } else {
                $q=$db->prepare('SELECT facility_id FROM FacilitySession WHERE facility_session_id=?'); $q->execute(array($sessionId)); $facilityId=$q->fetchColumn(); $q->closeCursor();
                $q=$db->prepare('SELECT active_status_id FROM Facility WHERE facility_id=? FOR UPDATE'); $q->execute(array($facilityId)); $parent=$q->fetch(); $q->closeCursor();
            }
            $q=$db->prepare('SELECT *,session_start_at>NOW() AS future FROM '.$sessionTable.' WHERE '.$sessionKey.'=? FOR UPDATE'); $q->execute(array($sessionId)); $slot=$q->fetch(); $q->closeCursor();
        }
        $q=$db->prepare('SELECT * FROM '.$d['table'].' WHERE '.$d['id'].'=? FOR UPDATE'); $q->execute(array($id)); $row=$q->fetch(); $q->closeCursor();
        if (!$row) { throw new DomainException('This record no longer exists.'); }
        if (!hash_equals(hash('sha256',serialize($row)),$version)) { throw new DomainException('This record changed since you opened it. Reload the page before saving.'); }
        if ($kind==='booking' || $kind==='facility') {
            $old=(int)$row[$d['status']];
            $reserving=$kind==='booking'?$status===2:in_array($status,array(1,2),true);
            if ($status!==$old && $reserving) {
                if (!$slot || !$slot['future'] || (int)$slot['session_status_id']!==1) { throw new DomainException('Only future scheduled sessions can be reserved or confirmed.'); }
                if (!$parent || (int)$parent['active_status_id']!==1 || ($kind==='booking' && (int)$typeActive!==1)) { throw new DomainException('The service or facility is inactive.'); }
                if ($kind==='booking') {
                    if ((int)$slot['active_status_id']!==1) { throw new DomainException('This session is inactive.'); }
                    $q=$db->prepare('SELECT booking_id FROM Booking WHERE service_session_id=? AND booking_status_id=2 AND booking_id<>? FOR UPDATE');
                    $q->execute(array($sessionId,$id)); $count=count($q->fetchAll(PDO::FETCH_COLUMN)); $q->closeCursor();
                    if ($slot['session_capacity']!==null && $count>=(int)$slot['session_capacity']) { throw new DomainException('This session has no remaining places.'); }
                } else {
                    $q=$db->prepare('SELECT facility_booking_id FROM FacilityBooking WHERE facility_session_id=? AND booking_status_id IN (1,2) AND facility_booking_id<>? FOR UPDATE');
                    $q->execute(array($sessionId,$id)); $count=count($q->fetchAll(PDO::FETCH_COLUMN)); $q->closeCursor();
                    if ($count) { throw new DomainException('This facility slot is already reserved by another booking.'); }
                }
            }
            $q=$db->prepare('UPDATE '.$d['table'].' SET '.$d['status'].'=?,'.$d['note'].'=? WHERE '.$d['id'].'=?'); $q->execute(array($status,$note===''?null:$note,$id));
        } else {
            if ($status===3 && trim($resolution)==='') { throw new DomainException('A resolution is required for Resolved records.'); }
            if ($status===4 && $row['resolved_at']!==null && trim($resolution)==='') { throw new DomainException('Retain the resolution when closing a resolved record.'); }
            $q=$db->prepare('UPDATE '.$d['table'].' SET '.$d['status'].'=?,'.$d['note'].'=?,'.$d['resolution'].'=?,resolved_at=CASE WHEN ?=3 THEN COALESCE(resolved_at,NOW()) WHEN ?=4 THEN resolved_at ELSE NULL END WHERE '.$d['id'].'=?');
            $q->execute(array($status,$note===''?null:$note,$resolution===''?null:$resolution,$status,$status,$id));
        }
        $q=$db->prepare("INSERT INTO AuditLog (user_id,event_type,event_result,audit_log_note) VALUES (?,'admin_update','success',?)"); $admin=authCurrentUser(); $q->execute(array($admin['user_id'],$kind.' '.$id.' status '.$status));
        $db->commit();
    } catch (Exception $e) { if ($db->inTransaction()) { $db->rollBack(); } throw $e; }
}
function adminEditor($kind) {
    adminRequire(); $defs=adminDefinitions(); $d=$defs[$kind]; $id=servicePositiveInt(adminGet('id')); $errors=array(); $row=null; $options=array();
    if (!$id) { http_response_code(400); $errors[]='A valid record ID is required.'; }
    else { try {
        $options=adminStatusOptions($d);
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $status=servicePositiveInt(authInput('status')); $allowed=array(); foreach ($options as $option) { $allowed[]=(int)$option['id']; }
            if (!authCsrf()) { $errors[]='Your form expired. Please try again.'; }
            if (!in_array($status,$allowed,true)) { $errors[]='Choose a valid status.'; }
            $note=authInput('note'); $resolution=authInput('resolution');
            foreach (array($note,$resolution) as $text) { if (preg_match('//u',$text)!==1 || strlen($text)>20000 || strpos($text,chr(0))!==false) { $errors[]='Notes and resolution must contain valid text of no more than 20,000 bytes.'; } }
            if (!$errors) {
                adminSave($kind,$id,$status,$note,$resolution,authInput('version'));
                $_SESSION['admin_saved_record']=$d['prefix'].$id;
                header('Location: ./admin.php#'.$kind,true,303); exit;
            }
            http_response_code(422);
        }
        if ($kind==='feedback' || $kind==='rates') { $q=authDb()->prepare('UPDATE '.$d['table'].' SET read_at=COALESCE(read_at,NOW()) WHERE '.$d['id'].'=?'); $q->execute(array($id)); }
        $q=authDb()->prepare('SELECT * FROM '.$d['table'].' WHERE '.$d['id'].'=?'); $q->execute(array($id)); $row=$q->fetch(); $q->closeCursor();
        if (!$row) { http_response_code(404); $errors[]='This record was not found.'; }
    } catch (DomainException $e) { http_response_code(422); $errors[]=$e->getMessage(); $q=authDb()->prepare('SELECT * FROM '.$d['table'].' WHERE '.$d['id'].'=?'); $q->execute(array($id)); $row=$q->fetch(); }
      catch (Exception $e) { error_log('Admin edit: '.$e->getMessage()); http_response_code(503); $errors[]='This record is temporarily unavailable.'; }
    }
    require_once __DIR__.'/../layout.php'; showHeader('Edit '.$d['title']);
    echo '<section class="form-section"><h1>Edit '.serviceEscape($d['title']).'</h1><p><a href="./admin.php#'.$kind.'">Back to administration</a></p>'; authErrors($errors);
    if (isset($_GET['saved'])) { echo '<p role="status">Changes saved.</p>'; }
    if ($row) {
        echo '<h2>'.serviceEscape($d['prefix'].$id).'</h2><dl class="admin-record">';
        foreach ($row as $key=>$value) { if (in_array($key,array($d['status'],$d['note'],isset($d['resolution'])?$d['resolution']:''),true)) { continue; } echo '<dt>'.serviceEscape(ucwords(str_replace('_',' ',$key))).'</dt><dd>'.nl2br(serviceEscape($value===null?'Not provided':$value)).'</dd>'; }
        echo '</dl><form class="form-table-container" method="post" action="'.serviceEscape(adminEditUrl($kind,$id)).'"><input type="hidden" name="csrf_token" value="'.serviceEscape(authToken()).'"><input type="hidden" name="version" value="'.hash('sha256',serialize($row)).'"><table class="form-table"><tbody><tr><th><label for="status">Status</label></th><td><select name="status" id="status" required>';
        $selected=$_SERVER['REQUEST_METHOD']==='POST'?authInput('status'):(string)$row[$d['status']];
        foreach ($options as $option) { echo '<option value="'.(int)$option['id'].'"'.($selected===(string)$option['id']?' selected':'').'>'.serviceEscape($option['name']).'</option>'; }
        echo '</select></td></tr>';
        foreach (array('note'=>'Internal notes','resolution'=>'Resolution shown to the member') as $field=>$label) {
            if ($field==='resolution' && !isset($d['resolution'])) { continue; }
            $column=$field==='note'?$d['note']:$d['resolution']; $value=$_SERVER['REQUEST_METHOD']==='POST'?authInput($field):$row[$column];
            echo '<tr><th><label for="'.$field.'">'.$label.'</label></th><td><textarea id="'.$field.'" name="'.$field.'" rows="5" maxlength="20000">'.serviceEscape($value).'</textarea></td></tr>';
        }
        echo '</tbody></table><p>Booking status changes check capacity and conflicting reservations. A resolution is required to resolve feedback or a rates enquiry.</p><button class="btn btn-primary" type="submit">Save changes</button></form>';
    }
    echo '</section>'; showFooter();
}
