<?php
// 預約資料層（PHP 5.4）：查詢、日期整理、驗證及寫入；不輸出畫面。
require_once __DIR__ . '/../config/service_data.php';
require_once __DIR__ . '/../config/booking_user.php';

function bookingContext($pdo, $serviceId, $lock = false) {
    $q = $pdo->prepare('SELECT s.service_id,s.service_title,s.service_location,s.booking_required,t.service_type_name FROM Service s JOIN ServiceType t ON t.service_type_id=s.service_type_id WHERE s.service_id=? AND s.active_status_id=1 AND t.active_status_id=1' . ($lock ? ' FOR UPDATE' : ''));
    $q->execute(array($serviceId)); $context = $q->fetch(PDO::FETCH_ASSOC); $q->closeCursor();
    if (!$context || !$context['booking_required']) { return false; }
    $context['kind'] = 'service';
    $context['parent_id'] = (int) $context['service_id'];
    if ($context['service_type_name'] === 'Facility') {
        $map = require __DIR__ . '/../config/facility_services.php';
        if (!isset($map[$serviceId])) { return false; }
        $q = $pdo->prepare('SELECT facility_id,facility_location FROM Facility WHERE facility_id=? AND active_status_id=1' . ($lock ? ' FOR UPDATE' : ''));
        $q->execute(array($map[$serviceId])); $facility = $q->fetch(PDO::FETCH_ASSOC); $q->closeCursor();
        if (!$facility) { return false; }
        $context['kind'] = 'facility';
        $context['parent_id'] = (int) $facility['facility_id'];
        $context['service_location'] = $facility['facility_location'];
    }
    return $context;
}

// 場地頁開啟時補齊今天到第 89 天；每天開放。保留取消／既有場次，不覆寫。
function bookingEnsureFacilitySlots($pdo, $context) {
    $pdo->beginTransaction();
    try {
        $q = $pdo->prepare('SELECT facility_id FROM Facility WHERE facility_id=? AND active_status_id=1 FOR UPDATE');
        $q->execute(array($context['parent_id'])); $active = $q->fetchColumn(); $q->closeCursor();
        if (!$active) { $pdo->commit(); return; }
        $today = $pdo->query('SELECT CURDATE()')->fetchColumn();
        $check = $pdo->prepare('SELECT facility_session_id FROM FacilitySession WHERE facility_id=? AND (session_start_at=? OR (session_status_id<>2 AND session_start_at<? AND session_end_at>?)) LIMIT 1');
        $insert = $pdo->prepare('INSERT INTO FacilitySession (facility_id,session_start_at,session_end_at,session_status_id,session_note) VALUES (?,?,?,1,?)');
        $day = new DateTime($today, new DateTimeZone('Australia/Perth'));
        for ($i=0; $i<90; $i++, $day->modify('+1 day')) {
            foreach (array(array('09:00:00','12:00:00'),array('13:00:00','17:00:00')) as $slot) {
                $start = $day->format('Y-m-d') . ' ' . $slot[0]; $end = $day->format('Y-m-d') . ' ' . $slot[1];
                $check->execute(array($context['parent_id'],$start,$end,$start)); $found = $check->fetchColumn(); $check->closeCursor();
                if ($found === false) { $insert->execute(array($context['parent_id'],$start,$end,'Standard half-day slot; open Monday-Sunday, Australia/Perth.')); }
            }
        }
        $pdo->commit();
    } catch (Exception $e) { if ($pdo->inTransaction()) { $pdo->rollBack(); } throw $e; }
}

// 列表、Book now 與 POST 共用規則；$sessionId 用來驗證指定時段的所屬及可用性。
function bookingAvailableSessions($pdo, $context, $sessionId = null) {
    if ($context['kind'] === 'facility') {
        $sql = "SELECT x.facility_session_id AS id,x.session_start_at,x.session_end_at,'' AS session_title,NULL AS session_location,NULL AS remaining FROM FacilitySession x WHERE x.facility_id=? AND x.session_status_id=1 AND x.session_start_at>NOW() AND DATE(x.session_start_at) BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 89 DAY) AND NOT EXISTS (SELECT 1 FROM FacilityBooking b WHERE b.facility_session_id=x.facility_session_id AND b.booking_status_id IN (1,2))";
        $idColumn = 'x.facility_session_id';
    } else {
        $used = '(SELECT COUNT(*) FROM Booking b WHERE b.service_session_id=x.service_session_id AND b.booking_status_id=2)';
        $sql = 'SELECT x.service_session_id AS id,x.session_start_at,x.session_end_at,x.session_title,x.session_location,(x.session_capacity-' . $used . ') AS remaining FROM ServiceSession x WHERE x.service_id=? AND x.active_status_id=1 AND x.session_status_id=1 AND x.session_start_at>NOW() AND (x.booking_open_at IS NULL OR x.booking_open_at<=NOW()) AND (x.booking_close_at IS NULL OR x.booking_close_at>NOW()) AND (x.session_capacity IS NULL OR x.session_capacity>' . $used . ')';
        $idColumn = 'x.service_session_id';
    }
    $params = array($context['parent_id']);
    if ($sessionId !== null) { $sql .= ' AND ' . $idColumn . '=?'; $params[] = $sessionId; }
    $q = $pdo->prepare($sql . ' ORDER BY x.session_start_at,' . $idColumn);
    $q->execute($params); $rows = $q->fetchAll(PDO::FETCH_ASSOC); $q->closeCursor();
    foreach ($rows as &$row) {
        $row['date'] = substr($row['session_start_at'],0,10);
        $row['label'] = substr($row['session_start_at'],11,5) . '–' . substr($row['session_end_at'],11,5);
        if (substr($row['session_end_at'],0,10) !== $row['date']) { $row['label'] .= ' (' . substr($row['session_end_at'],0,10) . ')'; }
        if ($row['session_title']) { $row['label'] .= ' — ' . $row['session_title']; }
        $row['location'] = $row['session_location'] === null ? $context['service_location'] : $row['session_location'];
        if ($row['remaining'] !== null) { $row['label'] .= ' (' . (int)$row['remaining'] . ' places remaining)'; }
        if ($row['location']) { $row['label'] .= ' — ' . $row['location']; }
    }
    unset($row);
    return $rows;
}

function bookingInput($data, $key) { return isset($data[$key]) && is_string($data[$key]) ? trim($data[$key]) : ''; }
function bookingValidDate($value) {
    if (!preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/D', $value, $m)) { return false; }
    return checkdate((int)$m[2],(int)$m[3],(int)$m[1]);
}
function bookingValidateParticipant($values) {
    $errors = array();
    foreach (array('first_name'=>'First name','surname'=>'Surname') as $key=>$label) {
        $value = $values[$key];
        if (preg_match('/\S/u', $value)!==1 || preg_match('/[\x00-\x1F\x7F]/u',$value) || preg_match_all('/./us',$value,$unused)>100) { $errors[] = $label . ' is required and must contain no more than 100 characters.'; }
    }
    if (strlen($values['email'])>254 || !filter_var($values['email'],FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid email address (up to 254 characters).'; }
    return $errors;
}

// 呼叫端先驗證 CSRF／欄位。鎖定服務及時段後重新檢查，避免同時送出搶到場地。
function bookingInsert($pdo, $serviceId, $sessionId, $date, $values) {
    $context = bookingContext($pdo,$serviceId,true);
    if (!$context) { throw new DomainException('This item is no longer accepting bookings.'); }
    $facility = $context['kind'] === 'facility';
    $table = $facility ? 'FacilitySession' : 'ServiceSession';
    $idColumn = $facility ? 'facility_session_id' : 'service_session_id';
    $q = $pdo->prepare('SELECT ' . $idColumn . ' FROM ' . $table . ' WHERE ' . $idColumn . '=? FOR UPDATE');
    $q->execute(array($sessionId)); $q->fetchColumn(); $q->closeCursor();
    $rows = bookingAvailableSessions($pdo,$context,$sessionId);
    if (!$rows || $rows[0]['date'] !== $date) { throw new DomainException('This session is no longer available. Please choose another date or time.'); }
    $table = $facility ? 'FacilityBooking' : 'Booking';
    $member = currentBookingUser();
    $userId = $member ? $member['user_id'] : null;
    $q = $pdo->prepare('INSERT INTO ' . $table . ' (' . $idColumn . ',user_id,booking_first_name,booking_surname,booking_email,booking_status_id) VALUES (?,?,?,?,?,1)');
    $q->execute(array($sessionId,$userId,$values['first_name'],$values['surname'],$values['email']));
    return array('reference'=>($facility ? 'F-' : 'S-') . $pdo->lastInsertId(),'facility'=>$facility,'session'=>$rows[0]['session_start_at']);
}

function bookingPageData() {
    authStart();
    header('Cache-Control: no-store');
    $data = array('error'=>null,'errors'=>array(),'success'=>null,'context'=>null,'dates'=>array(),'weeks'=>array(),'selected'=>'','selected_sessions'=>array(),'values'=>array('first_name'=>'','surname'=>'','email'=>''),'selected_session'=>'');
    try {
        // Read the current profile through the validated login session, never a URL user ID.
        // Preserve submitted participant details when showing validation errors.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $member = currentBookingUser();
            if ($member) {
                $data['values'] = array('first_name'=>$member['user_first_name'], 'surname'=>$member['user_surname'], 'email'=>$member['user_email']);
            }
        }
        if (empty($_SESSION['booking_token'])) {
            $strong = false; $bytes = openssl_random_pseudo_bytes(32,$strong);
            if ($bytes===false || !$strong) { throw new RuntimeException('Secure token unavailable'); }
            $_SESSION['booking_token'] = bin2hex($bytes);
        }
        $data['token'] = $_SESSION['booking_token'];
        $id = servicePositiveInt(bookingInput($_GET,'service_id'));
        if (!$id) { http_response_code(400); $data['error']='Please open booking from a service detail page.'; return $data; }
        $pdo = serviceDatabase(); $context = bookingContext($pdo,$id);
        if (!$context) { http_response_code(404); $data['error']='This item is not accepting bookings.'; return $data; }
        $data['context'] = $context;
        $data['base_url'] = './booking-sessions.php?service_id=' . $id;
        $data['detail_url'] = ($context['service_type_name']==='Event' ? './event.php' : './service_detail.php') . '?service_id=' . $id;
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            foreach ($data['values'] as $key=>$value) { $data['values'][$key]=bookingInput($_POST,$key); }
            $data['selected'] = bookingInput($_POST,'date');
            $data['selected_session'] = bookingInput($_POST,'session_id');
            $data['errors'] = bookingValidateParticipant($data['values']);
            if (bookingInput($_POST,'token')!==$_SESSION['booking_token']) { $data['errors'][]='This form has expired or was already submitted. Please try again.'; }
            $sessionId = servicePositiveInt($data['selected_session']);
            if (!$sessionId || !bookingValidDate($data['selected'])) { $data['errors'][]='Choose an available date and session.'; }
            if (!$data['errors']) {
                try {
                    $pdo->beginTransaction();
                    $receipt = bookingInsert($pdo,$id,$sessionId,$data['selected'],$data['values']);
                    $pdo->commit();
                    $_SESSION['booking_receipt']=array('service_id'=>$id,'receipt'=>$receipt);
                    unset($_SESSION['booking_token']);
                    header('Location: ' . $data['base_url'],true,303); exit;
                } catch (DomainException $e) { if ($pdo->inTransaction()) { $pdo->rollBack(); } $data['errors'][]=$e->getMessage(); }
            }
            http_response_code(422);
        } else {
            $data['selected'] = bookingInput($_GET,'date');
            if (isset($_SESSION['booking_receipt']) && $_SESSION['booking_receipt']['service_id']===$id) {
                $data['success']=$_SESSION['booking_receipt']['receipt']; unset($_SESSION['booking_receipt']);
            }
        }
        if ($context['kind']==='facility') { bookingEnsureFacilitySlots($pdo,$context); }
        foreach (bookingAvailableSessions($pdo,$context) as $row) { $data['dates'][$row['date']][]=$row; }
        $data['selected_sessions'] = isset($data['dates'][$data['selected']]) ? $data['dates'][$data['selected']] : array();
        $today = $pdo->query('SELECT CURDATE()')->fetchColumn();
        $dateKeys = array_keys($data['dates']);
        $firstMonth = $dateKeys ? substr($dateKeys[0],0,7) : substr($today,0,7);
        $lastMonth = $dateKeys ? substr($dateKeys[count($dateKeys)-1],0,7) : $firstMonth;
        $month = bookingInput($_GET,'month');
        if ($data['selected_sessions']) { $month = substr($data['selected'],0,7); }
        if (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])$/D',$month) || $month<$firstMonth || $month>$lastMonth) { $month=$firstMonth; }
        $calendar = new DateTime($month.'-01',new DateTimeZone('Australia/Perth'));
        $data['month_title']=$calendar->format('F Y');
        $prev=clone $calendar; $prev->modify('-1 month'); $next=clone $calendar; $next->modify('+1 month');
        $data['previous_url']=$month>$firstMonth ? $data['base_url'].'&month='.$prev->format('Y-m') : null;
        $data['next_url']=$month<$lastMonth ? $data['base_url'].'&month='.$next->format('Y-m') : null;
        $cells=array_fill(0,(int)$calendar->format('N')-1,null);
        for($day=1;$day<=(int)$calendar->format('t');$day++) {
            $date=$month.'-'.str_pad($day,2,'0',STR_PAD_LEFT);
            $cells[]=array('number'=>$day,'date'=>$date,'available'=>isset($data['dates'][$date]),'url'=>$data['base_url'].'&date='.$date.'#booking-form');
        }
        while(count($cells)%7) { $cells[]=null; }
        $data['weeks']=array_chunk($cells,7);
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
        error_log('Booking: '.$e->getMessage()); http_response_code(503); $data['error']='Bookings are temporarily unavailable. Please try again later.';
    }
    return $data;
}
