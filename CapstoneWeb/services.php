<?php
// List entry point and layout: retrieve JSON from show_list.php and populate the HTML below.
// Database queries remain in show_list.php; this file contains no SQL.
require_once __DIR__ . '/includes/show_list.php';
$listResult = loadServiceListJson(isset($_GET['page']) ? $_GET['page'] : null);
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
showHeader('Services', 'services');
?>
 <!-- Hero Section -->
  <section class="hero-section" aria-labelledby="hero-heading">
    <div class="hero-content">
      <span class="badge-text">WELCOME TO CITYLINK</span>
      <h1 id="hero-heading">Community services, made simple</h1>
      <p>Find council and community services, submit requests and access practical information for everyday life in CityLink.</p>

    </div>
    <div class="hero-card-visual" aria-hidden="true">
      <div class="orange-circle"></div>
      <div class="floating-box">
        Your community<br>Your services<br>Your CityLink
      </div>
    </div>
  </section>


<!-- Fixed service links stay outside the pagination container so they remain available when changing pages. -->
<section id="service-1" class="service1-section" aria-labelledby="service-1-heading">
  <div class="section-header">
    <span class="section-badge">POPULAR SERVICE</span>
    <h2 id="service-1-heading">Waste and recycling</h2>
  </div>
  <div class="cards-grid">
    <article class="service-card">
      <p class="card-desc">Check your next bin collection day, learn what belongs in each bin and report a missed or damaged bin.</p>
      <p class="card-desc"><strong>Available online:</strong> collection calendar, hard-waste requests and recycling guidance.</p>
      <a href="./waste-management.php" class="card-link">View waste and recycling information &rarr;</a>
    </article>
  </div>
</section>

<section id="service-2" class="service2-section" aria-labelledby="service-2-heading">
  <div class="section-header">
    <span class="section-badge">RESIDENT SUPPORT</span>
    <h2 id="service-2-heading">Rates and property enquiries</h2>
  </div>
  <div class="cards-grid">
    <article class="service-card">
      <p class="card-desc">View payment options, request a copy of a rates notice or contact the Rates team about your property account.</p>
      <p class="card-desc"><strong>Before you begin:</strong> have your assessment number and property address ready.</p>
      <a href="./rates-enquiry.php" class="card-link">Make a rates enquiry &rarr;</a>
    </article>
  </div>
</section>

<section id="service-3" class="service1-section" aria-labelledby="service-3-heading">
  <div class="section-header">
    <span class="section-badge">COMMUNITY</span>
    <h2 id="service-3-heading">Community facilities and programs</h2>
  </div>
  <div class="cards-grid">
    <article class="service-card">
      <p class="card-desc">Discover local workshops, youth programs, support groups and spaces available for community hire.</p>
      <p class="card-desc"><strong>Bookings:</strong> select an activity or venue to check availability before submitting a request.</p>
      <a href="./community-programs.php" class="card-link">Browse community programs &rarr;</a>
      <a href="./events.php" class="card-link">Browse community events &rarr;</a>
      <a href="./booking.php" class="card-link">Start a booking &rarr;</a>
    </article>
  </div>
</section>

<!-- script.js updates the list using the data-pagination container ID; this behaviour can be reused on other pages. -->
<section id="services-results" class="service1-section" data-pagination aria-labelledby="service-list-heading">
  <div class="section-header"><span class="section-badge">Explore CityLink</span><h2 id="service-list-heading" data-pagination-heading tabindex="-1">Services</h2></div>
  <p data-pagination-status role="status" aria-live="polite"></p>
  <?php if ($error): ?>
    <p role="alert"><?= listEscape($error) ?></p><a class="card-link" href="./services.php">Return to services</a>
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
    <nav class="service-pagination" aria-label="Services pages">
      <?php if ($pagination['previous_url']): ?><a data-page-link class="btn btn-secondary" rel="prev" href="<?= listEscape($pagination['previous_url']) ?>">&larr; Previous</a><?php endif; ?>
      <span aria-current="page">Page <?= (int) $pagination['page'] ?> of <?= (int) $pagination['pages'] ?></span>
      <?php if ($pagination['next_url']): ?><a data-page-link class="btn btn-secondary" rel="next" href="<?= listEscape($pagination['next_url']) ?>">Next &rarr;</a><?php endif; ?>
    </nav>
    <?php endif; ?>
  <?php endif; ?>
</section>
<?php showFooter(); ?>
