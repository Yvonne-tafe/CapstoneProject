<?php // 純呈現片段：$block 已由資料層整理，這裡只填值與迴圈。 ?>
<?php if ($block['content_type'] === 'image'): ?>
<figure class="announcement-feature-image" data-content-key="<?= detailEscape($block['content_key']) ?>">
  <img src="<?= detailEscape($block['image_url']) ?>" alt="<?= detailEscape($block['image_alt_text']) ?>">
  <?php if ($block['image_caption']): ?><figcaption><?= detailEscape($block['image_caption']) ?></figcaption><?php endif; ?>
</figure>
<?php else: ?>
<section<?= $block['content_key'] === 'accessibility' ? ' class="announcement-notice"' : '' ?> data-content-key="<?= detailEscape($block['content_key']) ?>">
  <?php if ($block['content_heading']): ?><h2><?= detailEscape($block['content_heading']) ?></h2><?php endif; ?>
  <?php if ($block['content_type'] === 'paragraph'): ?>
    <p><?= nl2br(detailEscape($block['content_body'])) ?></p>
  <?php elseif ($block['content_type'] === 'list'): ?>
    <ul><?php foreach ($block['items'] as $item): ?><li><?= detailEscape($item) ?></li><?php endforeach; ?></ul>
  <?php endif; ?>
</section>
<?php endif; ?>
