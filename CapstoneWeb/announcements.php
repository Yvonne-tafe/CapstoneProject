<?php
// 畫面層：由 show_list.php 取得公告 JSON；每頁最多 10 筆。
require_once __DIR__ . '/includes/show_list.php';
$result=loadAnnouncementListJson(isset($_GET['page']) ? $_GET['page'] : null);
http_response_code($result['status']);
$listData=json_decode($result['json'],true);
$pagination=$listData['pagination'];
require_once __DIR__ . '/layout.php';
showHeader('Announcements','announcements');
?>
 <!-- Hero Section -->
  <section class="hero-section" aria-labelledby="hero-heading">
    <div class="hero-content">
      <span class="badge-text">WELCOME TO CITYLINK</span>
      <h1 id="hero-heading">Community announcements</h1>
      <p>Stay informed about facility updates, service changes, road works and other notices affecting the CityLink community.</p>

    </div>
    <div class="hero-card-visual" aria-hidden="true">
      <div class="orange-circle"></div>
      <div class="floating-box">
        Your community<br>Your services<br>Your CityLink
      </div>
    </div>
  </section>


<section id="announcements-results" class="announcement1-section" data-pagination aria-labelledby="announcement-list-heading">
  <div class="section-header"><span class="section-badge">Community updates</span><h2 id="announcement-list-heading" data-pagination-heading tabindex="-1">Announcements</h2></div>
  <p data-pagination-status role="status" aria-live="polite"></p>
  <?php if ($listData['error']): ?>
    <p role="alert"><?= serviceEscape($listData['error']) ?></p><a href="./announcements.php">Return to announcements</a>
  <?php else: ?>
    <p data-pagination-summary><?= serviceEscape($pagination['summary']) ?></p>
    <div class="cards-grid services-list">
      <?php foreach($listData['announcements'] as $item): ?>
      <article class="announcement-card" data-announcement-id="<?= (int)$item['announcement_id'] ?>">
        <span class="section-badge"><?= serviceEscape($item['badge']) ?></span>
        <h3 class="card-title"><?= serviceEscape($item['announcement_title']) ?></h3>
        <p class="announcement-date"><time datetime="<?= serviceEscape($item['published_iso']) ?>"><?= serviceEscape($item['published_label']) ?></time></p>
        <?php if ($item['announcement_short_desc']): ?><p class="announcement-desc"><?= serviceEscape($item['announcement_short_desc']) ?></p><?php endif; ?>
        <a class="card-link" href="<?= serviceEscape($item['detail_url']) ?>" aria-label="<?= serviceEscape('Read more: '.$item['announcement_title']) ?>">Read more &rarr;</a>
      </article>
      <?php endforeach; ?>
    </div>
    <?php if ($pagination['pages']>1): ?>
    <nav class="service-pagination" aria-label="Announcements pages">
      <?php if ($pagination['previous_url']): ?><a data-page-link class="btn btn-secondary" rel="prev" href="<?= serviceEscape($pagination['previous_url']) ?>">&larr; Previous</a><?php endif; ?>
      <span aria-current="page">Page <?= (int)$pagination['page'] ?> of <?= (int)$pagination['pages'] ?></span>
      <?php if ($pagination['next_url']): ?><a data-page-link class="btn btn-secondary" rel="next" href="<?= serviceEscape($pagination['next_url']) ?>">Next &rarr;</a><?php endif; ?>
    </nav>
    <?php endif; ?>
  <?php endif; ?>
</section>
<?php showFooter(); ?>
