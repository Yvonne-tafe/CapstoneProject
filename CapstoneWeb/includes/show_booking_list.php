<?php
// 與詳細頁及提交表單共用可預約條件，回傳公開清單 JSON。
require_once __DIR__ . '/session.php';
function loadBookingListJson() {
    $data = array('error'=>null,'activities'=>array(),'services'=>array());
    $status = 200;
    try {
        $pdo = serviceDatabase();
        $q = $pdo->query("SELECT s.service_id,s.service_title,s.service_short_desc,s.service_fee,s.service_fee_basis,t.service_type_name FROM Service s JOIN ServiceType t ON t.service_type_id=s.service_type_id WHERE s.active_status_id=1 AND t.active_status_id=1 AND s.booking_required=1 AND t.service_type_name IN ('Event','Program','Service','Facility') ORDER BY s.service_id");
        $rows = $q->fetchAll(PDO::FETCH_ASSOC); $q->closeCursor();
        foreach ($rows as $row) {
            $context = bookingContext($pdo,(int)$row['service_id']);
            if (!$context) { continue; }
            if ($context['kind']==='facility') { bookingEnsureFacilitySlots($pdo,$context); }
            $slots = bookingAvailableSessions($pdo,$context);
            if (!$slots) { continue; }
            $row['location'] = $context['service_location'];
            $row['fee_text'] = serviceFee($row);
            $row['session_count'] = count($slots);
            $row['next_session'] = $slots[0]['session_start_at'];
            $row['booking_url'] = './booking-sessions.php?service_id='.(int)$row['service_id'];
            $row['login_url'] = './login.php?service_id='.(int)$row['service_id'];
            $row['detail_url'] = ($row['service_type_name']==='Event' ? './event.php' : './service_detail.php').'?service_id='.(int)$row['service_id'];
            unset($row['service_fee'],$row['service_fee_basis']);
            $group = in_array($row['service_type_name'],array('Event','Program'),true) ? 'activities' : 'services';
            $data[$group][] = $row;
        }
    } catch (Exception $e) {
        error_log('Booking list: '.$e->getMessage());
        $status=503; $data['error']='Bookings are temporarily unavailable. Please try again later.';
        $data['activities']=array(); $data['services']=array();
    }
    $json=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    if ($json===false) { $status=503; $json='{"error":"Bookings are temporarily unavailable.","activities":[],"services":[]}'; }
    return array('status'=>$status,'json'=>$json);
}
