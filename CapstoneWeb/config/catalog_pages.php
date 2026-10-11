<?php
// 共用列表／詳情資料層的可信設定，不接受網址直接指定表名或檔案路徑。
function catalogPageConfig($type) {
    $pages = array(
        'Service' => array('label' => 'Services', 'singular' => 'Service', 'noun' => 'services', 'active' => 'services', 'list_url' => './services.php', 'detail_url' => './service_detail.php'),
        'Event' => array('label' => 'Events', 'singular' => 'Event', 'noun' => 'events', 'active' => 'events', 'list_url' => './events.php', 'detail_url' => './event.php'),
        'Program' => array('label' => 'Community programs', 'singular' => 'Program', 'noun' => 'programs', 'active' => 'services', 'list_url' => './community-programs.php', 'detail_url' => './program-detail.php')
    );
    if (!isset($pages[$type])) { throw new InvalidArgumentException('Unsupported catalog type.'); }
    // Services 頁包含服務、課程及設施；Events 頁仍只顯示活動。
    $pages[$type]['types'] = $type === 'Service' ? array('Service', 'Program', 'Facility') : array($type);
    return $pages[$type];
}

// 共用相同的分類條件，確保列表總數、分頁及詳細頁一致。
function catalogTypeFilter($config) {
    $params = array();
    foreach ($config['types'] as $index => $name) {
        $params[':type' . $index] = $name;
    }
    return array('sql' => 't.service_type_name IN (' . implode(',', array_keys($params)) . ')', 'params' => $params);
}
