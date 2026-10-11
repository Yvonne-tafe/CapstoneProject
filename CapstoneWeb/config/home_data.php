<?php
require_once __DIR__.'/announcement_data.php';
function homePageData() {
    $data=array('announcements'=>array(),'featured'=>array(),'announcement_error'=>false,'featured_error'=>false);
    try {
        $db=serviceDatabase();
        $q=$db->query('SELECT a.announcement_id,a.announcement_title,a.announcement_short_desc,a.announcement_published_at,a.announcement_quick_desc,t.announcement_type_name'.announcementPublicFrom().' ORDER BY a.announcement_published_at DESC,a.announcement_id DESC LIMIT 3');
        foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) { $data['announcements'][]=announcementDisplayData($row); } $q->closeCursor();
    } catch (Exception $e) { error_log('Home announcements: '.$e->getMessage()); $data['announcement_error']=true; }
    try {
        if (!isset($db)) { $db=serviceDatabase(); }
        // Service has no created_at field; the largest ID is the newest record.
        $q=$db->prepare('SELECT s.service_id,s.service_title,s.service_short_desc,s.service_quick_desc FROM Service s JOIN ServiceType t ON t.service_type_id=s.service_type_id WHERE s.active_status_id=1 AND t.active_status_id=1 AND t.service_type_name=? ORDER BY s.service_id DESC LIMIT 1');
        foreach (array('Event'=>'EVENT','Program'=>'COMMUNITY PROGRAM','Service'=>'COUNCIL SERVICE') as $type=>$label) {
            $q->execute(array($type)); $row=$q->fetch(PDO::FETCH_ASSOC); $q->closeCursor();
            if ($row) { $row['label']=$label; $row['detail_url']=($type==='Event'?'./event.php':'./service_detail.php').'?service_id='.(int)$row['service_id']; $data['featured'][]=$row; }
        }
    } catch (Exception $e) { error_log('Home featured: '.$e->getMessage()); $data['featured_error']=true; }
    return $data;
}
