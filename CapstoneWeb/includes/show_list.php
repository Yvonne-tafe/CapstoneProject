<?php
// 列表資料層：查詢、驗證分頁與整理 JSON；不輸出 HTML。
require_once __DIR__ . '/../config/service_data.php';
require_once __DIR__ . '/../config/catalog_pages.php';
require_once __DIR__ . '/../config/announcement_data.php';
// 同一份查詢支援 Service / Event；預設保留既有 Service 呼叫方式。
function loadServiceListJson($rawPage, $type = 'Service') {
    $config = catalogPageConfig($type);
    $filter = catalogTypeFilter($config);
    $status = 200;
    $data = array('error' => null, 'services' => array(), 'pagination' => array('page' => 1, 'page_size' => 10, 'total' => 0, 'pages' => 1, 'summary' => '', 'previous_url' => null, 'next_url' => null));
    $page = $rawPage === null ? 1 : servicePositiveInt($rawPage);
    if ($page === false) {
        $status = 400; $data['error'] = 'Please choose a valid page number.';
    } else {
        try {
            $pdo = serviceDatabase();
            // 總數與當頁使用相同條件：只顯示指定的啟用分類。
            $from = ' FROM Service s JOIN ServiceType t ON t.service_type_id=s.service_type_id WHERE s.active_status_id=1 AND t.active_status_id=1 AND ' . $filter['sql'];
            $q = $pdo->prepare('SELECT COUNT(*)' . $from);
            $q->execute($filter['params']);
            $total = (int) $q->fetchColumn(); $q->closeCursor();
            $pages = max(1, (int) ceil($total / 10));
            $data['pagination']['page'] = $page;
            $data['pagination']['total'] = $total;
            $data['pagination']['pages'] = $pages;
            if ($page > $pages) {
                $status = 404; $data['error'] = 'This page does not exist. Please return to the first page.';
            } else {
                // 僅查詢公開顯示欄位；每次從 DB 取最多 10 筆，不把所有服務載入瀏覽器。
                $q = $pdo->prepare('SELECT s.service_id,s.service_title,s.service_short_desc,s.service_location,s.service_fee,s.service_fee_basis,t.service_type_name' . $from . ' ORDER BY s.service_id LIMIT :limit OFFSET :offset');
                foreach ($filter['params'] as $parameter => $value) {
                    $q->bindValue($parameter, $value, PDO::PARAM_STR);
                }
                $q->bindValue(':limit', 10, PDO::PARAM_INT);
                $q->bindValue(':offset', ($page - 1) * 10, PDO::PARAM_INT);
                $q->execute(); $rows = $q->fetchAll(PDO::FETCH_ASSOC); $q->closeCursor();
                foreach ($rows as $row) {
                    $row['fee_text'] = serviceFee($row);
                    $row['detail_url'] = $config['detail_url'] . '?service_id=' . (int) $row['service_id'];
                    unset($row['service_fee'], $row['service_fee_basis']);
                    $data['services'][] = $row;
                }
                $data['pagination']['summary'] = $total ? 'Showing ' . (($page - 1) * 10 + 1) . '–' . min($page * 10, $total) . ' of ' . $total . ' ' . $config['noun'] : 'No ' . $config['noun'] . ' are available at the moment. Please check again later.';
                $data['pagination']['previous_url'] = $page > 1 ? $config['list_url'] . '?page=' . ($page - 1) : null;
                $data['pagination']['next_url'] = $page < $pages ? $config['list_url'] . '?page=' . ($page + 1) : null;
            }
        } catch (Exception $e) {
            error_log('Service list: ' . $e->getMessage());
            $status = 503; $data['error'] = $config['label'] . ' are temporarily unavailable. Please try again later.';
        }
    }
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        $status = 503;
        $json = '{"error":"Services are temporarily unavailable.","services":[],"pagination":{"page":1,"pages":1,"summary":"","previous_url":null,"next_url":null}}';
    }
    return array('status' => $status, 'json' => $json);
}

// 公告同樣回傳 JSON；保留公告獨有的發布狀態及時間規則。
function loadAnnouncementListJson($rawPage) {
    $status=200;
    $data=array('error'=>null,'announcements'=>array(),'pagination'=>array('page'=>1,'page_size'=>10,'total'=>0,'pages'=>1,'summary'=>'','previous_url'=>null,'next_url'=>null));
    $page=$rawPage===null ? 1 : servicePositiveInt($rawPage);
    if ($page===false) { $status=400; $data['error']='Please choose a valid page number.'; }
    else {
        try {
            $pdo=serviceDatabase(); $from=announcementPublicFrom();
            $total=(int)$pdo->query('SELECT COUNT(*)'.$from)->fetchColumn();
            $pages=max(1,(int)ceil($total/10));
            $data['pagination']['page']=$page; $data['pagination']['total']=$total; $data['pagination']['pages']=$pages;
            if ($page>$pages) { $status=404; $data['error']='This page does not exist. Please return to the first page.'; }
            else {
                $q=$pdo->prepare('SELECT a.announcement_id,a.announcement_title,a.announcement_short_desc,a.announcement_quick_desc,a.announcement_published_at,t.announcement_type_name'.$from.' ORDER BY a.announcement_published_at DESC,a.announcement_id DESC LIMIT :limit OFFSET :offset');
                $q->bindValue(':limit',10,PDO::PARAM_INT); $q->bindValue(':offset',($page-1)*10,PDO::PARAM_INT);
                $q->execute(); $rows=$q->fetchAll(PDO::FETCH_ASSOC); $q->closeCursor();
                foreach($rows as $row) { $data['announcements'][]=announcementDisplayData($row); }
                $data['pagination']['summary']=$total ? 'Showing '.(($page-1)*10+1).'–'.min($page*10,$total).' of '.$total.' announcements' : 'No announcements are available at the moment.';
                $data['pagination']['previous_url']=$page>1 ? './announcements.php?page='.($page-1) : null;
                $data['pagination']['next_url']=$page<$pages ? './announcements.php?page='.($page+1) : null;
            }
        } catch(Exception $e) { error_log('Announcement list: '.$e->getMessage()); $status=503; $data['error']='Announcements are temporarily unavailable. Please try again later.'; }
    }
    $json=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    if ($json===false) { $status=503; $json='{"error":"Announcements are temporarily unavailable.","announcements":[],"pagination":{}}'; }
    return array('status'=>$status,'json'=>$json);
}
