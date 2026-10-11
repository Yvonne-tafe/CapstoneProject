<?php
// 純顯示版型：只解讀 JSON，查詢與資料整理由 show_detail.php 負責。
if (!isset($detailJson)) { header('Location: ../announcements.php',true,302); exit; }
require_once __DIR__ . '/../layout.php';
function detailEscape($value) { return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8'); }
$pageData=json_decode($detailJson,true);
$announcement=$pageData['announcement'];
showHeader($pageData['error'] ? 'Announcement details' : $announcement['announcement_title'],'announcements');
?>
<article class="announcement-detail" aria-labelledby="announcement-heading">
  <?php showBreadcrumb(array('Announcements'=>'./announcements.php',$pageData['error'] ? 'Announcement details' : $announcement['announcement_title']=>null)); ?>
  <?php if ($pageData['error']): ?>
    <h1 id="announcement-heading">Announcement details</h1><p role="alert"><?= detailEscape($pageData['error']) ?></p>
  <?php else: ?>
    <header class="announcement-detail-header">
      <span class="section-badge"><?= detailEscape($announcement['badge']) ?></span>
      <h1 id="announcement-heading"><?= detailEscape($announcement['announcement_title']) ?></h1>
      <?php if ($announcement['announcement_short_desc']): ?><p class="announcement-lead"><?= detailEscape($announcement['announcement_short_desc']) ?></p><?php endif; ?>
      <dl class="announcement-meta"><div><dt>Published</dt><dd><time datetime="<?= detailEscape($announcement['published_iso']) ?>"><?= detailEscape($announcement['published_label']) ?></time></dd></div><div><dt>Category</dt><dd><?= detailEscape($announcement['announcement_type_name']) ?></dd></div></dl>
    </header>
    <?php foreach($pageData['feature_images'] as $block) { include __DIR__.'/service_content_block.php'; } ?>
    <div class="announcement-layout">
      <div class="announcement-body">
        <?php foreach($pageData['blocks'] as $block) { include __DIR__.'/service_content_block.php'; } ?>
      </div>
      <aside class="announcement-sidebar" aria-label="Announcement contact information">
        <?php if ($announcement['has_contact']): ?>
        <div class="announcement-contact-card"><h2>Need more information?</h2>
          <?php if ($announcement['announcement_contact_name']): ?><p><?= detailEscape($announcement['announcement_contact_name']) ?></p><?php endif; ?>
          <?php if ($announcement['announcement_contact_phone']): ?><p><strong>Phone</strong><br><a href="<?= detailEscape($announcement['phone_href']) ?>"><?= detailEscape($announcement['announcement_contact_phone']) ?></a></p><?php endif; ?>
          <?php if ($announcement['announcement_contact_email']): ?><p><strong>Email</strong><br><a href="<?= detailEscape($announcement['email_href']) ?>"><?= detailEscape($announcement['announcement_contact_email']) ?></a></p><?php endif; ?>
        </div><?php endif; ?>
        <?php if ($announcement['announcement_link']): ?><div class="announcement-update-card"><h2>Related information</h2><a class="card-link" href="<?= detailEscape($announcement['announcement_link']) ?>"><?= detailEscape($announcement['announcement_link_label'] ? $announcement['announcement_link_label'] : 'Find out more') ?> &rarr;</a></div><?php endif; ?>
      </aside>
    </div>
  <?php endif; ?>
  <footer class="announcement-detail-footer"><a class="card-link" href="./announcements.php">&larr; Back to all announcements</a></footer>
</article>
<?php showFooter(); ?>
