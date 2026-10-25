<?php
// Trusted configuration for shared list/detail data layers; never accept table names or file paths directly from URL parameters.
function catalogPageConfig($type) {
    $pages = array(
        'Service' => array('label' => 'Services', 'singular' => 'Service', 'noun' => 'services', 'active' => 'services', 'list_url' => './services.php', 'detail_url' => './service_detail.php'),
        'Event' => array('label' => 'Events', 'singular' => 'Event', 'noun' => 'events', 'active' => 'events', 'list_url' => './events.php', 'detail_url' => './event.php'),
        'Program' => array('label' => 'Community programs', 'singular' => 'Program', 'noun' => 'programs', 'active' => 'services', 'list_url' => './community-programs.php', 'detail_url' => './program-detail.php')
    );
    if (!isset($pages[$type])) { throw new InvalidArgumentException('Unsupported catalog type.'); }
    // The Services page includes services, programs, and facilities; the Events page displays events only.
    $pages[$type]['types'] = $type === 'Service' ? array('Service', 'Program', 'Facility') : array($type);
    return $pages[$type];
}

// Share category filters so list counts, pagination, and detail pages remain consistent.
function catalogTypeFilter($config) {
    $params = array();
    foreach ($config['types'] as $index => $name) {
        $params[':type' . $index] = $name;
    }
    return array('sql' => 't.service_type_name IN (' . implode(',', array_keys($params)) . ')', 'params' => $params);
}
