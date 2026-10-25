<?php
// List entry point and layout: retrieve JSON from show_list.php and populate the HTML below.
// Database queries remain in show_list.php; this file contains no SQL.
require_once __DIR__ . '/includes/show_list.php';
$listResult = loadServiceListJson(isset($_GET['page']) ? $_GET['page'] : null, 'Event');
http_response_code($listResult['status']);
$listJson = $listResult['json'];
require_once __DIR__ . '/layout.php';
if (!function_exists('listEscape')) {
    function listEscape($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
}
$listData = json_decode($listJson, true);
$services = $listData['services'];
$pagination = $listData['pagination'];
$error = $listData['error'];
showHeader('Events', 'events');
?>
 <!-- Hero Section -->
  <section class="hero-section" aria-labelledby="hero-heading">
    <div class="hero-content">
      <span class="badge-text">WELCOME TO CITYLINK</span>
      <h1 id="hero-heading">Community Events</h1>
      <p>Discover free and low-cost activities, workshops and celebrations taking place across the CityLink community.</p>

    </div>
    <div class="hero-card-visual" aria-hidden="true">
      <div class="orange-circle"></div>
      <div class="floating-box">
        Your community<br>Your services<br>Your CityLink
      </div>
    </div>
  </section>


<!-- script.js updates the list using the data-pagination container ID; this behaviour can be reused on other pages. -->
<section id="events-results" class="service1-section" data-pagination aria-labelledby="event-list-heading">
  <div class="section-header"><span class="section-badge">Explore CityLink</span><h2 id="event-list-heading" data-pagination-heading tabindex="-1">Events</h2></div>
  <p data-pagination-status role="status" aria-live="polite"></p>
  <?php if ($error): ?>
    <p role="alert"><?= listEscape($error) ?></p><a class="card-link" href="./events.php">Return to events</a>
  <?php else: ?>
    <p data-pagination-summary><?= listEscape($pagination['summary']) ?></p>
    <div class="cards-grid services-list">
    <?php foreach ($services as $service): ?>
      <article class="service-card" data-service-id="<?= (int) $service['service_id'] ?>">
        <span class="section-badge"><?= listEscape($service['service_type_name']) ?></span>
        <h3 class="card-title"><?= listEscape($service['service_title']) ?></h3>
        <?php if ($service['service_short_desc']): ?><p class="card-desc"><?= listEscape($service['service_short_desc']) ?></p><?php endif; ?>
        <?php if ($service['service_location']): ?><p class="card-desc"><strong>Location:</strong> <?= listEscape($service['service_location']) ?></p><?php endif; ?>
        <p class="card-desc"><strong>Cost:</strong> <?= listEscape($service['fee_text']) ?></p>
        <a href="<?= listEscape($service['detail_url']) ?>" class="card-link" aria-label="<?= listEscape('View details: ' . $service['service_title']) ?>">View details &rarr;</a>
      </article>
    <?php endforeach; ?>
    </div>
    <?php if ($pagination['pages'] > 1): ?>
    <nav class="service-pagination" aria-label="Events pages">
      <?php if ($pagination['previous_url']): ?><a data-page-link class="btn btn-secondary" rel="prev" href="<?= listEscape($pagination['previous_url']) ?>">&larr; Previous</a><?php endif; ?>
      <span aria-current="page">Page <?= (int) $pagination['page'] ?> of <?= (int) $pagination['pages'] ?></span>
      <?php if ($pagination['next_url']): ?><a data-page-link class="btn btn-secondary" rel="next" href="<?= listEscape($pagination['next_url']) ?>">Next &rarr;</a><?php endif; ?>
    </nav>
    <?php endif; ?>
  <?php endif; ?>
</section>
<?php showFooter(); ?>
