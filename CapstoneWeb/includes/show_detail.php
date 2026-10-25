<?php
// Data layer: return data only, without HTML output; this is not a public JSON API.
require_once __DIR__ . '/../config/service_data.php';
require_once __DIR__ . '/../config/catalog_pages.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../config/announcement_data.php';
function loadServiceDetailJson($rawId, $type = 'Service') {
    $config = catalogPageConfig($type);
    $filter = catalogTypeFilter($config);
    $status = 200;
    $data = array('error' => null, 'service' => null, 'blocks' => array(), 'feature_images' => array(), 'sessions' => array(), 'fallback_description' => '');
    $id = servicePositiveInt($rawId);
    if ($id === false) {
        $status = 400; $data['error'] = 'Please choose an item from the ' . $config['noun'] . ' list.';
    } else {
        try {
            $pdo = serviceDatabase();
            $q = $pdo->prepare('SELECT s.*, t.service_type_name FROM Service s JOIN ServiceType t ON t.service_type_id=s.service_type_id WHERE s.service_id=:id AND s.active_status_id=1 AND t.active_status_id=1 AND ' . $filter['sql']);
            $q->execute(array_merge(array(':id' => $id), $filter['params'])); $service = $q->fetch(); $q->closeCursor();
            if (!$service) { $status = 404; $data['error'] = 'This ' . strtolower($config['singular']) . ' is not available.'; }
            else {
                // Expose only the public fields required by the template; omit administrator notes.
                $fields = array('service_id','service_title','service_type_name','service_quick_desc','service_short_desc','service_location','service_session_details','booking_required','service_booking_requirements','service_contact_name','service_contact_phone','service_contact_email');
                $public = array(); foreach ($fields as $field) { $public[$field] = $service[$field]; }
                $public['badge'] = $service['service_quick_desc'] ? $service['service_quick_desc'] : $service['service_type_name'];
                $public['fee_text'] = serviceFee($service);
                $public['phone_href'] = 'tel:' . preg_replace('/[^0-9+]/', '', $service['service_contact_phone']);
                $public['email_href'] = 'mailto:' . rawurlencode($service['service_contact_email']);
                $public['has_contact'] = (bool) ($service['service_contact_name'] || $service['service_contact_phone'] || $service['service_contact_email']);
                $data['service'] = $public;
                $q = $pdo->prepare('SELECT content_key,content_type,content_heading,content_body,content_items,image_url,image_alt_text,image_caption FROM ServiceWebContent WHERE service_id=:id ORDER BY display_order,service_web_content_id');
                $q->execute(array(':id' => $id)); $blocks = $q->fetchAll(); $q->closeCursor();
                $hasIntroduction = false;
                foreach ($blocks as $block) {
                    if (!in_array($block['content_type'], array('paragraph','list','image'), true)) { continue; }
                    $block['items'] = array();
                    if ($block['content_type'] === 'image') {
                        $block['image_url'] = serviceImageUrl($block['image_url']);
                        if ($block['image_url'] === '') { continue; }
                    }
                    if ($block['content_type'] === 'list') {
                        $items = json_decode($block['content_items'], true);
                        if (!is_array($items) || array_filter($items, function($item) { return !is_string($item); })) { continue; }
                        $block['items'] = array_values($items);
                    }
                    unset($block['content_items']);
                    if ($block['content_key'] === 'introduction') { $hasIntroduction = true; }
                    if ($block['content_key'] === 'feature_image' && $block['content_type'] === 'image') { $data['feature_images'][] = $block; }
                    else { $data['blocks'][] = $block; }
                }
                $data['fallback_description'] = !$hasIntroduction ? $service['service_desc'] : '';
                $q = $pdo->prepare('SELECT session_title,session_start_at,session_end_at,session_location FROM ServiceSession WHERE service_id=:id AND active_status_id=1 AND session_status_id=1 AND session_end_at>NOW() ORDER BY session_start_at,service_session_id');
                $q->execute(array(':id' => $id)); $data['sessions'] = $q->fetchAll(); $q->closeCursor();
                foreach ($data['sessions'] as &$session) {
                    $session['start_iso'] = str_replace(' ', 'T', $session['session_start_at']) . '+08:00';
                    $session['end_iso'] = str_replace(' ', 'T', $session['session_end_at']) . '+08:00';
                }
                unset($session);
                $booking = bookingContext($pdo,$id);
                // Populate rolling facility dates first so expired sessions do not leave users without a booking entry point.
                if ($booking && $booking['kind']==='facility') { bookingEnsureFacilitySlots($pdo,$booking); }
                $available = $booking ? bookingAvailableSessions($pdo,$booking) : array();
                $data['service']['booking_url'] = $available ? './booking-sessions.php?service_id=' . $id : null;
                $data['service']['booking_message'] = $available ? 'Choose an available date and time to submit a booking request.' : 'No sessions are currently available for booking. Contact the team for updates.';
            }
        } catch (Exception $e) {
            error_log('Service detail: ' . $e->getMessage());
            $status = 503; $data['error'] = 'Service details are temporarily unavailable. Please try again later.';
        }
    }
    $data['page'] = $config;
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        $status = 503;
        $json = '{"error":"Service details are temporarily unavailable.","service":null,"blocks":[],"feature_images":[],"sessions":[],"fallback_description":""}';
    }
    return array('status' => $status, 'json' => $json);
}

function loadAnnouncementDetailJson($rawId) {
    $status=200; $data=array('error'=>null,'announcement'=>null,'blocks'=>array(),'feature_images'=>array());
    $id=servicePositiveInt($rawId);
    if ($id===false) { $status=400; $data['error']='Please choose an item from the announcements list.'; }
    else {
        try {
            $pdo=serviceDatabase();
            $q=$pdo->prepare('SELECT a.announcement_id,a.announcement_title,a.announcement_short_desc,a.announcement_quick_desc,a.announcement_published_at,a.announcement_link,a.announcement_link_label,a.announcement_contact_name,a.announcement_contact_phone,a.announcement_contact_email,t.announcement_type_name'.announcementPublicFrom().' AND a.announcement_id=?');
            $q->execute(array($id)); $row=$q->fetch(PDO::FETCH_ASSOC); $q->closeCursor();
            if (!$row) { $status=404; $data['error']='This announcement is not available.'; }
            else {
                $row=announcementDisplayData($row);
                $row['announcement_link']=serviceImageUrl($row['announcement_link']);
                $row['phone_href']='tel:'.preg_replace('/[^0-9+]/','',$row['announcement_contact_phone']);
                $row['email_href']='mailto:'.rawurlencode($row['announcement_contact_email']);
                $row['has_contact']=(bool)($row['announcement_contact_name'] || $row['announcement_contact_phone'] || $row['announcement_contact_email']);
                $data['announcement']=$row;
                $q=$pdo->prepare('SELECT content_key,content_type,content_heading,content_body,content_items,image_url,image_alt_text,image_caption,display_order FROM AnnouncementWebContent WHERE announcement_id=? ORDER BY display_order,announcement_web_content_id');
                $q->execute(array($id)); $content=announcementContentData($q->fetchAll(PDO::FETCH_ASSOC)); $q->closeCursor();
                $data['blocks']=$content['blocks']; $data['feature_images']=$content['feature_images'];
            }
        } catch(Exception $e) { error_log('Announcement detail: '.$e->getMessage()); $status=503; $data['error']='Announcement details are temporarily unavailable. Please try again later.'; }
    }
    $json=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    if($json===false) { $status=503; $json='{"error":"Announcement details are temporarily unavailable.","announcement":null,"blocks":[],"feature_images":[]}'; }
    return array('status'=>$status,'json'=>$json);
}
