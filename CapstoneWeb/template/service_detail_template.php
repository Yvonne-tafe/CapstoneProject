<?php
// 純版型：接收 service_detail.php 傳來的 JSON，不查詢資料庫。
// 直接開啟版型時，轉到網站根目錄的控制器。
if (!isset($detailJson)) {
    $query = isset($_GET['service_id']) && is_string($_GET['service_id']) ? '?service_id=' . rawurlencode($_GET['service_id']) : '';
    header('Location: ../service_detail.php' . $query, true, 302); exit;
}
require_once __DIR__ . '/../layout.php';
function detailEscape($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
$pageData = json_decode($detailJson, true);
$page = isset($pageData['page']) ? $pageData['page'] : array('label'=>'Services','singular'=>'Service','active'=>'services','list_url'=>'./services.php');
$error = $pageData['error'];
$service = $pageData['service'];
$blocks = $pageData['blocks'];
$sessions = $pageData['sessions'];
showHeader($error ? $page['singular'] . ' details' : $service['service_title'], $page['active']);
?>
<article class="announcement-detail" aria-labelledby="service-detail-heading">
<?php showBreadcrumb(array($page['label'] => $page['list_url'], $error ? $page['singular'] . ' details' : $service['service_title'] => null)); ?>
<?php if ($error): ?>
  <h1 id="service-detail-heading"><?= detailEscape($page['singular']) ?> details</h1><p role="alert"><?= detailEscape($error) ?></p>
<?php else: ?>
  <header class="announcement-detail-header">
    <span class="section-badge"><?= detailEscape($service['badge']) ?></span>
    <h1 id="service-detail-heading"><?= detailEscape($service['service_title']) ?></h1>
    <?php if ($service['service_short_desc']): ?><p class="announcement-lead"><?= detailEscape($service['service_short_desc']) ?></p><?php endif; ?>
    <dl class="announcement-meta">
      <?php if ($service['service_location']): ?><div><dt>Location</dt><dd><?= detailEscape($service['service_location']) ?></dd></div><?php endif; ?>
      <div><dt>Cost</dt><dd><?= detailEscape($service['fee_text']) ?></dd></div>
      <?php if ($service['service_session_details']): ?><div><dt>Schedule</dt><dd><?= detailEscape($service['service_session_details']) ?></dd></div><?php endif; ?>
    </dl>
  </header>
  <?php foreach ($pageData['feature_images'] as $block) { include __DIR__ . '/service_content_block.php'; } ?>
  <div class="announcement-layout">
    <div class="announcement-body">
      <?php if ($pageData['fallback_description']): ?><p><?= nl2br(detailEscape($pageData['fallback_description'])) ?></p><?php endif; ?>
      <?php foreach ($blocks as $block) { include __DIR__ . '/service_content_block.php'; } ?>
      <?php if ($sessions): ?>
      <section aria-labelledby="service-sessions-heading"><h2 id="service-sessions-heading">Upcoming sessions</h2><ul>
      <?php foreach ($sessions as $session): ?>
        <li><?php if ($session['session_title']): ?><strong><?= detailEscape($session['session_title']) ?></strong><br><?php endif; ?>
        <time datetime="<?= detailEscape($session['start_iso']) ?>"><?= detailEscape($session['session_start_at']) ?></time>
        – <time datetime="<?= detailEscape($session['end_iso']) ?>"><?= detailEscape($session['session_end_at']) ?></time> (Perth time)
        <?php if ($session['session_location']): ?><br><?= detailEscape($session['session_location']) ?><?php endif; ?></li>
      <?php endforeach; ?></ul></section>
      <?php endif; ?>
    </div>
    <aside class="announcement-sidebar" aria-label="<?= detailEscape($page['singular']) ?> booking and contact information">
      <?php if ($service['booking_required']): ?>
      <div class="announcement-contact-card"><h2>Booking information</h2>
        <?php if ($service['service_booking_requirements']): ?><p><?= nl2br(detailEscape($service['service_booking_requirements'])) ?></p><?php endif; ?>
        <p><?= detailEscape($service['booking_message']) ?></p>
        <?php if (!empty($service['booking_url'])): ?><a class="btn btn-primary" href="<?= detailEscape($service['booking_url']) ?>">Book now</a><?php endif; ?>
      </div><?php endif; ?>
      <?php if ($service['has_contact']): ?>
      <div class="announcement-update-card"><h2><?= detailEscape($page['singular']) ?> contact</h2>
        <?php if ($service['service_contact_name']): ?><p><?= detailEscape($service['service_contact_name']) ?></p><?php endif; ?>
        <?php if ($service['service_contact_phone']): ?><p><strong>Phone</strong><br><a href="<?= detailEscape($service['phone_href']) ?>"><?= detailEscape($service['service_contact_phone']) ?></a></p><?php endif; ?>
        <?php if ($service['service_contact_email']): ?><p><strong>Email</strong><br><a href="<?= detailEscape($service['email_href']) ?>"><?= detailEscape($service['service_contact_email']) ?></a></p><?php endif; ?>
      </div><?php endif; ?>
    </aside>
  </div>
<?php endif; ?>
  <footer class="announcement-detail-footer"><a href="<?= detailEscape($page['list_url']) ?>" class="card-link">&larr; Back to all <?= detailEscape(strtolower($page['label'])) ?></a></footer>
</article>
<?php showFooter(); ?>
