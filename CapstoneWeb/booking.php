<?php
// 畫面層：資料與可預約條件集中於 show_booking_list.php。
require_once __DIR__ . '/includes/show_booking_list.php';
require_once __DIR__ . '/config/booking_user.php';
$result = loadBookingListJson();
http_response_code($result['status']);
$bookingList = json_decode($result['json'], true);
$bookingUser = null;
try { $bookingUser = currentBookingUser(); }
catch (Exception $e) { error_log('Booking login state: '.$e->getMessage()); http_response_code(503); $bookingList['error']='Bookings are temporarily unavailable. Please try again later.'; }
require_once __DIR__ . '/layout.php';
showHeader('Bookings');
?>
<section id="booking-introduction" class="service1-section" aria-labelledby="booking-heading">
  <?php showBreadcrumb(array('Bookings'=>null)); ?>
  <div class="section-header">
    <span class="section-badge">Online bookings</span>
    <h1 id="booking-heading">Book a service, facility or event</h1>
    <p>Choose an item with available sessions. Log in to book with your account, or book as a guest.</p>
  </div>
  <?php if ($bookingList['error']): ?><p role="alert"><?= serviceEscape($bookingList['error']) ?></p><?php endif; ?>
</section>
<?php if (!$bookingList['error']): ?>
<section id="event-bookings" class="service2-section" aria-labelledby="event-bookings-heading">
  <div class="section-header">
    <span class="section-badge">Events and programs</span>
    <h2 id="event-bookings-heading">Upcoming activities</h2>
  </div>
  <?php if (!$bookingList['activities']): ?><p>No events or programs are currently available for booking.</p><?php endif; ?>
  <div class="cards-grid booking-grid">
    <?php foreach ($bookingList['activities'] as $item) { include __DIR__ . '/template/booking_card.php'; } ?>
  </div>
</section>
<section id="service-bookings" class="service1-section" aria-labelledby="service-bookings-heading">
  <div class="section-header">
    <span class="section-badge">Services and facilities</span>
    <h2 id="service-bookings-heading">Book a CityLink service or facility</h2>
  </div>
  <?php if (!$bookingList['services']): ?><p>No services or facilities are currently available for booking.</p><?php endif; ?>
  <div class="cards-grid booking-grid">
    <?php foreach ($bookingList['services'] as $item) { include __DIR__ . '/template/booking_card.php'; } ?>
  </div>
</section>
<?php endif; ?>
<section id="booking-help" class="service2-section" aria-labelledby="booking-help-heading">
  <div class="section-header">
    <span class="section-badge">Booking help</span>
    <h2 id="booking-help-heading">Before you book</h2>
    <p>Have your first name, surname and email address ready. Availability is checked again when you submit. If you need help, call <a href="tel:+61890001234">(08) 9000 1234</a> during business hours.</p>
  </div>
</section>
<?php showFooter(); ?>
