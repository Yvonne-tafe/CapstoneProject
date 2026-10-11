<?php
// 畫面層：session.php 處理資料及提交；本頁只將資料填入 HTML。
require_once __DIR__ . '/includes/session.php';
$bookingData = bookingPageData();
require_once __DIR__ . '/layout.php';
showHeader('Book a session', $bookingData['context'] && $bookingData['context']['service_type_name']==='Event' ? 'events' : 'services');
?>
<section class="booking-page" aria-labelledby="booking-heading" data-booking-calendar>
  <?php if ($bookingData['error']): ?>
    <h1 id="booking-heading">Book a session</h1>
    <p role="alert"><?= serviceEscape($bookingData['error']) ?></p>
    <a href="./services.php">Return to services</a>
  <?php else: ?>
    <?php showBreadcrumb(array($bookingData['context']['service_title']=>$bookingData['detail_url'],'Book a session'=>null)); ?>
    <span class="section-badge">Bookings</span>
    <h1 id="booking-heading"><?= serviceEscape($bookingData['context']['service_title']) ?></h1>
    <p>Choose an available date, then select a session and enter your details. All times are Perth time.</p>
    <?php if ($bookingData['success']): ?>
      <div class="booking-notice" role="status"><h2>Booking request received</h2>
        <p>Reference: <strong><?= serviceEscape($bookingData['success']['reference']) ?></strong> · Status: Pending</p>
        <p><?= serviceEscape($bookingData['success']['session']) ?> (Perth time)</p>
        <p><?= $bookingData['success']['facility'] ? 'Your selected facility slot is reserved while your request is awaiting confirmation.' : 'Your request is awaiting confirmation. A place is only reserved once confirmed.' ?></p>
      </div>
    <?php endif; ?>
    <?php if ($bookingData['errors']): ?>
      <div class="booking-errors" role="alert"><h2>Please check your booking</h2><ul>
        <?php foreach ($bookingData['errors'] as $message): ?><li><?= serviceEscape($message) ?></li><?php endforeach; ?>
      </ul></div>
    <?php endif; ?>
    <?php if (!$bookingData['dates']): ?>
      <p role="status">No sessions are currently available for booking. Please check again later.</p>
    <?php else: ?>
      <div class="booking-calendar-header">
        <?php if ($bookingData['previous_url']): ?><a class="btn btn-secondary" href="<?= serviceEscape($bookingData['previous_url']) ?>" aria-label="Previous month">&larr; Previous</a><?php else: ?><span></span><?php endif; ?>
        <h2 id="booking-month"><?= serviceEscape($bookingData['month_title']) ?></h2>
        <?php if ($bookingData['next_url']): ?><a class="btn btn-secondary" href="<?= serviceEscape($bookingData['next_url']) ?>" aria-label="Next month">Next &rarr;</a><?php else: ?><span></span><?php endif; ?>
      </div>
      <p id="calendar-help">Outlined dates are available. Grey dates are unavailable.</p>
      <table class="booking-calendar" aria-labelledby="booking-month" aria-describedby="calendar-help">
        <thead><tr><?php foreach (array('Mon','Tue','Wed','Thu','Fri','Sat','Sun') as $weekday): ?><th scope="col"><?= $weekday ?></th><?php endforeach; ?></tr></thead>
        <tbody><?php foreach ($bookingData['weeks'] as $week): ?><tr>
          <?php foreach ($week as $day): ?><td>
            <?php if ($day && $day['available']): ?>
              <a href="<?= serviceEscape($day['url']) ?>" data-booking-date="<?= serviceEscape($day['date']) ?>" aria-label="<?= serviceEscape($day['date']) ?>, available"<?= $bookingData['selected']===$day['date'] ? ' aria-current="date"' : '' ?>><?= $day['number'] ?></a>
            <?php elseif ($day): ?><span aria-label="<?= serviceEscape($day['date']) ?>, unavailable"><?= $day['number'] ?></span><?php endif; ?>
          </td><?php endforeach; ?>
        </tr><?php endforeach; ?></tbody>
      </table>
      <p role="status" aria-live="polite" data-booking-date-status><?= $bookingData['selected_sessions'] ? 'Selected date: '.serviceEscape($bookingData['selected']) : 'Select an available date to show the booking form.' ?></p>
      <form id="booking-form" class="booking-form" method="post" action="<?= serviceEscape($bookingData['base_url']) ?>#booking-form"<?= !$bookingData['selected_sessions'] ? ' hidden' : '' ?>>
        <h2 tabindex="-1">Your booking details</h2>
        <p data-booking-form-date>Selected date: <?= serviceEscape($bookingData['selected']) ?> (Perth time)</p>
        <p>All fields are required. <?= $bookingData['context']['kind']==='facility' ? 'One request reserves one half-day slot. For a full day, submit a separate request for each half-day.' : 'Submit one request per participant, per session.' ?></p>
        <input type="hidden" name="token" value="<?= serviceEscape($bookingData['token']) ?>">
        <input type="hidden" name="date" value="<?= serviceEscape($bookingData['selected']) ?>">
        <label for="booking-session">Available session</label>
        <select id="booking-session" name="session_id" required>
          <option value="">Choose a time</option>
          <?php foreach ($bookingData['selected_sessions'] as $slot): ?><option value="<?= (int)$slot['id'] ?>"<?= (string)$slot['id']===$bookingData['selected_session'] ? ' selected' : '' ?>><?= serviceEscape($slot['label']) ?></option><?php endforeach; ?>
        </select>
        <p data-booking-session-summary aria-live="polite"><?php foreach ($bookingData['selected_sessions'] as $slot) { if ((string)$slot['id']===$bookingData['selected_session']) { echo serviceEscape($slot['label']); } } ?></p>
        <label for="booking-first-name">First name</label>
        <input id="booking-first-name" name="first_name" autocomplete="given-name" maxlength="100" required value="<?= serviceEscape($bookingData['values']['first_name']) ?>">
        <label for="booking-surname">Surname</label>
        <input id="booking-surname" name="surname" autocomplete="family-name" maxlength="100" required value="<?= serviceEscape($bookingData['values']['surname']) ?>">
        <label for="booking-email">Email address</label>
        <input type="email" id="booking-email" name="email" autocomplete="email" maxlength="254" required value="<?= serviceEscape($bookingData['values']['email']) ?>">
        <p><?= $bookingData['context']['kind']==='facility' ? 'Your request will be Pending and reserve this slot until it is confirmed or cancelled.' : 'Your request will be Pending. A place is only reserved once confirmed.' ?></p>
        <button class="btn btn-primary" type="submit">Submit booking</button>
      </form>
      <script type="application/json" id="booking-dates"><?= json_encode($bookingData['dates'],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?></script>
    <?php endif; ?>
    <p><a class="card-link" href="<?= serviceEscape($bookingData['detail_url']) ?>">&larr; Back to details</a></p>
  <?php endif; ?>
</section>
<?php showFooter(); ?>
