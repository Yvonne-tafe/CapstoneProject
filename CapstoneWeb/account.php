<?php
require_once __DIR__.'/config/auth.php';
try { $user=authRequireUser(); } catch (Exception $e) { error_log('Account: '.$e->getMessage()); http_response_code(503); $user=null; }
require_once __DIR__.'/config/account_bookings.php';
require_once __DIR__.'/config/account_requests.php';
require_once __DIR__.'/config/rates_enquiry.php';
$rateEnquiries=array(); $feedbackRecords=array(); $rateError=false; $feedbackError=false;
if ($user) {
    try { $rateEnquiries=accountRateEnquiries($user['user_id']); }
    catch (Exception $e) { error_log('Account rates: '.$e->getMessage()); $rateError=true; }
    try { $feedbackRecords=accountFeedback($user['user_id']); }
    catch (Exception $e) { error_log('Account feedback: '.$e->getMessage()); $feedbackError=true; }
}
$bookings=array(); $bookingError=false;
if ($user) {
    try { $bookings=accountBookings($user['user_id']); }
    catch (Exception $e) { error_log('Account bookings: '.$e->getMessage()); $bookingError=true; }
}
require_once __DIR__.'/layout.php'; showHeader('My account');
?>
<section class="detail-section" aria-labelledby="account-heading"><?php showBreadcrumb(array('My account'=>null)); ?>
<div class="section-header"><span class="section-badge">My account</span><h1 id="account-heading">Your account details</h1></div>
<?php if (!$user): ?><p role="alert">Your account is temporarily unavailable. Please try again later.</p>
<?php else: if (isset($_GET['updated'])): ?><p role="status">Your account details have been saved.</p><?php endif; ?>
<div class="cards-grid">
<?php foreach (array('Account information'=>array('user_first_name'=>'First name','user_surname'=>'Surname','user_email'=>'Email address','role_name'=>'Account role'),'Contact details'=>array('user_phone_number'=>'Contact number','user_address'=>'Full address','preferred_contact_method'=>'Preferred contact method'),'Booking profile'=>array('user_date_of_birth'=>'Date of birth','user_gender'=>'Gender')) as $heading=>$fields): ?>
<article class="service-card"><h2 class="card-title"><?= serviceEscape($heading) ?></h2>
<?php foreach ($fields as $key=>$label): ?><p class="card-desc"><strong><?= serviceEscape($label) ?></strong><br><?= serviceEscape($user[$key]!==null && $user[$key]!==''?$user[$key]:'Not provided') ?></p><?php endforeach; ?></article>
<?php endforeach; ?></div><p><a class="btn btn-primary" href="./account-edit.php">Edit account details</a></p>
<section aria-labelledby="my-bookings-heading">
<div class="section-header"><span class="section-badge">Booking history</span><h2 id="my-bookings-heading">My Bookings</h2><p>Your activity, service and facility bookings, including past and cancelled bookings. All times are Perth time.</p></div>
<?php if ($bookingError): ?><p role="alert">Your bookings are temporarily unavailable. Please try again later.</p>
<?php elseif (!$bookings): ?><p>You have no bookings linked to your account yet.</p><p><a class="card-link" href="./booking.php">Browse available bookings</a></p>
<?php else: ?>
<div class="cards-grid">
<?php foreach ($bookings as $booking): ?>
<article class="service-card">
<h3 class="card-title"><?= serviceEscape($booking['title']) ?></h3>
<p class="card-desc"><strong>Reference:</strong> <?= serviceEscape($booking['reference']) ?><br><strong>Type:</strong> <?= serviceEscape($booking['booking_type']) ?><br><strong>Status:</strong> <?= serviceEscape($booking['booking_status_name']) ?></p>
<?php if (!empty($booking['session_title'])): ?><p class="card-desc"><strong>Session:</strong> <?= serviceEscape($booking['session_title']) ?></p><?php endif; ?>
<p class="card-desc"><strong>Start:</strong> <?= serviceEscape(accountBookingTime($booking['session_start_at'])) ?><br><strong>End:</strong> <?= serviceEscape(accountBookingTime($booking['session_end_at'])) ?></p>
<p class="card-desc"><strong>Location:</strong> <?= serviceEscape($booking['location'] ?: 'Not provided') ?></p>
<p class="card-desc"><strong>Participant:</strong> <?= serviceEscape($booking['booking_first_name'].' '.$booking['booking_surname']) ?><br><strong>Email:</strong> <?= serviceEscape($booking['booking_email']) ?></p>
<p class="card-desc"><strong>Booked:</strong> <?= serviceEscape(accountBookingTime($booking['booked_at'])) ?></p>
</article>
<?php endforeach; ?></div>
<?php endif; ?>
</section>
<section aria-labelledby="my-rates-heading">
<div class="section-header"><span class="section-badge">Rates enquiries</span><h2 id="my-rates-heading">My Rate Enquiry</h2><p>Your rates enquiries, with the most recent submissions first. All times are Perth time.</p></div>
<?php if ($rateError): ?><p role="alert">Your rates enquiries are temporarily unavailable. Please try again later.</p>
<?php elseif (!$rateEnquiries): ?><p>You have no rates enquiries linked to your account yet.</p>
<?php else: $rateTypes=ratesEnquiryTypes(); ?><div class="cards-grid">
<?php foreach ($rateEnquiries as $enquiry): ?><article class="service-card">
<h3 class="card-title"><?= serviceEscape(isset($rateTypes[$enquiry['enquiry_type']])?$rateTypes[$enquiry['enquiry_type']]:$enquiry['enquiry_type']) ?></h3>
<p class="card-desc"><strong>Reference:</strong> R-<?= (int)$enquiry['rates_enquiry_id'] ?><br><strong>Status:</strong> <?= serviceEscape($enquiry['rates_enquiry_status_name']) ?><br><strong>Submitted:</strong> <?= serviceEscape(accountBookingTime($enquiry['submitted_at'])) ?></p>
<p class="card-desc"><strong>Property address:</strong> <?= serviceEscape($enquiry['property_address']) ?><br><strong>Assessment number:</strong> <?= serviceEscape($enquiry['assessment_number']?:'Not provided') ?></p>
<p class="card-desc"><strong>Contact:</strong> <?= serviceEscape($enquiry['full_name']) ?><br><strong>Email:</strong> <?= serviceEscape($enquiry['email']) ?><br><strong>Phone:</strong> <?= serviceEscape($enquiry['phone']?:'Not provided') ?><br><strong>Preferred response:</strong> <?= serviceEscape(ucfirst($enquiry['response_method'])) ?></p>
<p class="card-desc"><strong>Enquiry details:</strong><br><?= nl2br(serviceEscape($enquiry['enquiry_details'])) ?></p>
<?php if ($enquiry['rates_enquiry_resolution']): ?><p class="card-desc"><strong>Resolution:</strong><br><?= nl2br(serviceEscape($enquiry['rates_enquiry_resolution'])) ?></p><?php endif; ?>
<?php if ($enquiry['resolved_at']): ?><p class="card-desc"><strong>Resolved:</strong> <?= serviceEscape(accountBookingTime($enquiry['resolved_at'])) ?></p><?php endif; ?>
</article><?php endforeach; ?></div><?php endif; ?>
<p><a class="card-link" href="./rates-enquiry.php">Make a rates enquiry</a></p>
</section>
<section aria-labelledby="my-feedback-heading">
<div class="section-header"><span class="section-badge">Feedback history</span><h2 id="my-feedback-heading">My Feedback</h2><p>Your feedback submissions, with the most recent submissions first.</p></div>
<?php if ($feedbackError): ?><p role="alert">Your feedback is temporarily unavailable. Please try again later.</p>
<?php elseif (!$feedbackRecords): ?><p>You have no feedback linked to your account yet.</p>
<?php else: ?><div class="cards-grid">
<?php foreach ($feedbackRecords as $feedback): ?><article class="service-card">
<h3 class="card-title"><?= serviceEscape($feedback['feedback_category_name']) ?></h3>
<p class="card-desc"><strong>Reference:</strong> FB-<?= (int)$feedback['feedback_id'] ?><br><strong>Status:</strong> <?= serviceEscape($feedback['feedback_status_name']) ?><br><strong>Submitted:</strong> <?= serviceEscape(accountBookingTime($feedback['submitted_at'])) ?></p>
<p class="card-desc"><strong>Your feedback:</strong><br><?= nl2br(serviceEscape($feedback['feedback_message'])) ?></p>
<p class="card-desc"><strong>Name:</strong> <?= serviceEscape($feedback['feedback_name']?:'Not provided') ?><br><strong>Email:</strong> <?= serviceEscape($feedback['feedback_email']?:'Not provided') ?><br><strong>Response requested:</strong> <?= $feedback['response_request']?'Yes':'No' ?></p>
<?php if ($feedback['feedback_resolution']): ?><p class="card-desc"><strong>Resolution:</strong><br><?= nl2br(serviceEscape($feedback['feedback_resolution'])) ?></p><?php endif; ?>
<?php if ($feedback['resolved_at']): ?><p class="card-desc"><strong>Resolved:</strong> <?= serviceEscape(accountBookingTime($feedback['resolved_at'])) ?></p><?php endif; ?>
</article><?php endforeach; ?></div><?php endif; ?>
<p><a class="card-link" href="./feedback.php">Submit feedback</a></p>
</section>
<form method="post" action="./logout.php"><input type="hidden" name="csrf_token" value="<?= serviceEscape(authToken()) ?>"><button class="btn btn-primary" type="submit">Log out</button></form>
<?php endif; ?></section><?php showFooter(); ?>
