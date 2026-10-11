<?php
require_once __DIR__ . '/service_data.php';
// 清單與詳細頁使用完全相同的公開條件。
function announcementPublicFrom() {
    return ' FROM Announcement a JOIN AnnouncementType t ON t.announcement_type_id=a.announcement_type_id WHERE a.active_status_id=1 AND t.active_status_id=1 AND a.announcement_status_id=2 AND a.announcement_published_at<=NOW()';
}
function announcementDisplayData($row) {
    $date = new DateTime($row['announcement_published_at'],new DateTimeZone('Australia/Perth'));
    $row['published_label']=$date->format('j F Y');
    $row['published_iso']=$date->format('c');
    $row['badge']=$row['announcement_quick_desc'] ? $row['announcement_quick_desc'] : $row['announcement_type_name'];
    $row['detail_url']='./announcement-detail.php?announcement_id='.(int)$row['announcement_id'];
    return $row;
}
function announcementContentData($rows) {
    $data=array('blocks'=>array(),'feature_images'=>array());
    foreach ($rows as $block) {
        if (!in_array($block['content_type'],array('paragraph','list','image'),true)) { continue; }
        $block['items']=array();
        if ($block['content_type']==='list') {
            $items=json_decode($block['content_items'],true);
            if (!is_array($items) || array_filter($items,function($item){ return !is_string($item); })) { continue; }
            $block['items']=array_values($items);
        }
        if ($block['content_type']==='image') {
            $block['image_url']=serviceImageUrl($block['image_url']);
            if ($block['image_url']==='') { continue; }
        }
        unset($block['content_items']);
        $key=$block['content_type']==='image' && $block['content_key']==='feature_image' ? 'feature_images' : 'blocks';
        $data[$key][]=$block;
    }
    return $data;
}
