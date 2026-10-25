<?php // Shared booking card: display only data prepared by show_booking_list.php. ?>
<article class="service-card" data-bookable-service-id="<?= (int)$item['service_id'] ?>">
  <span class="section-badge"><?= serviceEscape($item['service_type_name']) ?></span>
  <h3 class="card-title"><?= serviceEscape($item['service_title']) ?></h3>
  <?php if ($item['service_short_desc']): ?><p class="card-desc"><?= serviceEscape($item['service_short_desc']) ?></p><?php endif; ?>
  <p class="card-desc"><strong>Next available:</strong> <?= serviceEscape($item['next_session']) ?> (Perth time)</p>
  <p class="card-desc"><?= (int)$item['session_count'] ?> sessions currently available</p>
  <?php if ($item['location']): ?><p class="card-desc"><strong>Location:</strong> <?= serviceEscape($item['location']) ?></p><?php endif; ?>
  <p class="card-desc"><strong>Cost:</strong> <?= serviceEscape($item['fee_text']) ?></p>
  <div class="booking-card-actions">
    <?php if ($bookingUser): ?>
      <a class="btn btn-primary" href="<?= serviceEscape($item['booking_url']) ?>">Choose a session &rarr;</a>
    <?php else: ?>
      <a class="card-link" href="<?= serviceEscape($item['login_url']) ?>">Log in to book &rarr;</a>
      <a class="card-link" href="<?= serviceEscape($item['booking_url']) ?>">Book as a guest &rarr;</a>
    <?php endif; ?>
    <a class="card-link" href="<?= serviceEscape($item['detail_url']) ?>">View details &rarr;</a>
  </div>
</article>
