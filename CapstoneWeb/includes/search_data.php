<?php
require_once __DIR__.'/../config/announcement_data.php';
require_once __DIR__.'/search_pages.php';

function searchPageData($rawQuery, $rawPage, $db=null) {
    $data=array('query'=>'','results'=>array(),'total'=>0,'page'=>1,'pages'=>1,'error'=>null);
    if (!is_string($rawQuery)) { http_response_code(400); $data['error']='Please enter a valid search term.'; return $data; }
    $data['query']=trim($rawQuery);
    if ($data['query']==='') { return $data; }
    if (strlen($data['query'])>400 || !preg_match('//u',$data['query'])) { http_response_code(400); $data['error']='Please use a shorter search term (up to 100 characters).'; return $data; }
    $page=$rawPage===null?1:servicePositiveInt($rawPage);
    if ($page===false) { http_response_code(400); $data['error']='Please choose a valid page number.'; return $data; }
    // Escape LIKE wildcards so user input is matched literally, including % and _.
    $pattern='%'.strtr($data['query'],array('!'=>'!!','%'=>'!%','_'=>'!_')).'%';
    $params=array(); $parts=array();
    $parts[]="SELECT CONCAT('service-',s.service_id) AS result_key,s.service_title AS title,t.service_type_name AS category,COALESCE(NULLIF(s.service_short_desc,''),s.service_desc,'') AS description,CONCAT(CASE t.service_type_name WHEN 'Event' THEN './event.php' WHEN 'Program' THEN './program-detail.php' ELSE './service_detail.php' END,'?service_id=',s.service_id) AS url,(s.service_title LIKE ? ESCAPE '!') AS title_match FROM Service s JOIN ServiceType t ON t.service_type_id=s.service_type_id WHERE s.active_status_id=1 AND t.active_status_id=1 AND t.service_type_name IN ('Event','Program','Service','Facility') AND (s.service_title LIKE ? ESCAPE '!' OR s.service_short_desc LIKE ? ESCAPE '!' OR s.service_desc LIKE ? ESCAPE '!' OR EXISTS (SELECT 1 FROM ServiceWebContent w WHERE w.service_id=s.service_id AND w.content_type IN ('paragraph','list','image') AND (w.content_heading LIKE ? ESCAPE '!' OR w.content_body LIKE ? ESCAPE '!' OR w.content_items LIKE ? ESCAPE '!' OR w.image_caption LIKE ? ESCAPE '!')))";
    $params=array_fill(0,8,$pattern);
    $parts[]="SELECT CONCAT('announcement-',a.announcement_id),a.announcement_title,'Announcement',COALESCE(a.announcement_short_desc,''),CONCAT('./announcement-detail.php?announcement_id=',a.announcement_id),(a.announcement_title LIKE ? ESCAPE '!')".announcementPublicFrom()." AND (a.announcement_title LIKE ? ESCAPE '!' OR a.announcement_short_desc LIKE ? ESCAPE '!' OR EXISTS (SELECT 1 FROM AnnouncementWebContent w WHERE w.announcement_id=a.announcement_id AND w.content_type IN ('paragraph','list','image') AND (w.content_heading LIKE ? ESCAPE '!' OR w.content_body LIKE ? ESCAPE '!' OR w.content_items LIKE ? ESCAPE '!' OR w.image_caption LIKE ? ESCAPE '!')))";
    $params=array_merge($params,array_fill(0,7,$pattern));
    foreach (searchFixedPages() as $index=>$fixed) {
        $parts[]="SELECT ? AS result_key,? AS title,? AS category,? AS description,? AS url,(? LIKE ? ESCAPE '!') AS title_match FROM DUAL WHERE ? LIKE ? ESCAPE '!'";
        $params=array_merge($params,array('page-'.$index,$fixed['title'],$fixed['category'],$fixed['description'],$fixed['url'],$fixed['title'],$pattern,$fixed['title'].' '.$fixed['category'].' '.$fixed['description'].' '.$fixed['keywords'],$pattern));
    }
    // EXISTS avoids duplicates when several content blocks match the same record.
    $from=' FROM ('.implode(' UNION ALL ',$parts).') search_results';
    try {
        if ($db===null) { $db=serviceDatabase(); } $q=$db->prepare('SELECT COUNT(*)'.$from); $q->execute($params);
        $data['total']=(int)$q->fetchColumn(); $q->closeCursor();
        $data['pages']=max(1,(int)ceil($data['total']/10)); $data['page']=$page;
        if ($page>$data['pages']) { http_response_code(404); $data['error']='This page does not exist. Please return to the first page.'; return $data; }
        $q=$db->prepare('SELECT *'.$from.' ORDER BY title_match DESC,title ASC,result_key ASC LIMIT 10 OFFSET '.(($page-1)*10));
        $q->execute($params); $data['results']=$q->fetchAll(PDO::FETCH_ASSOC); $q->closeCursor();
        foreach ($data['results'] as &$row) {
            $text=trim(preg_replace('/\s+/u',' ',strip_tags($row['description'])));
            preg_match('/^.{0,200}/us',$text,$match); $row['description']=$match[0].(strlen($match[0])<strlen($text)?'…':'');
        } unset($row);
    } catch (Exception $e) { error_log('Search: '.$e->getMessage()); http_response_code(503); $data['error']='Search is temporarily unavailable. Please try again later.'; }
    return $data;
}

function searchPageUrl($query,$page) { return './search.php?'.http_build_query(array('q'=>$query,'page'=>$page)); }
