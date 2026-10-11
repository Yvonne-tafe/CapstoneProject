<?php
require_once __DIR__ . '/config/rates_enquiry.php';
$ratesData = ratesEnquiryPageData();
require_once __DIR__ . '/layout.php';
showHeader('Rates enquiry', 'services');
?>

<section id="rates-enquiry-form" class="form-section" aria-labelledby="rates-enquiry-heading">
  <?php showBreadcrumb([
    'Services' => './services.php',
    'Rates enquiry' => null,
  ]); ?>

  <div class="section-header">
    <span class="section-badge">Rates</span>
    <h1 id="rates-enquiry-heading">Make a rates enquiry</h1>
    <p>Use this form to ask CityLink about a property rates notice, payment, concession, valuation or account detail. Fields marked required must be completed.</p>
  </div>

  <?php authErrors($ratesData['errors']); ?>
  <?php if ($ratesData['success']): ?>
    <div class="booking-notice" role="status"><h2>Your rates enquiry has been received</h2><p>Reference: <strong><?= serviceEscape($ratesData['success']) ?></strong> · Status: New</p><p>Your enquiry has been saved for review by the CityLink team.</p></div>
  <?php endif; ?>
  <?php if (!$ratesData['success']): ?>
  <form id="rates-enquiry" class="form-table-container" method="post" action="./rates-enquiry.php">
    <input type="hidden" name="csrf_token" value="<?= serviceEscape(authToken()) ?>">
    <input type="hidden" name="form_token" value="<?= serviceEscape($ratesData['form_token']) ?>">
    <table class="form-table">
      <thead>
        <tr>
          <th scope="col">Field</th>
          <th scope="col">Your response</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row"><label for="enquiry-type">Enquiry type (required)</label></th>
          <td>
            <select id="enquiry-type" name="enquiry_type" required>
              <option value="">Select an option</option>
              <?php foreach (ratesEnquiryTypes() as $value=>$label): ?>
              <option value="<?= serviceEscape($value) ?>"<?= $ratesData['values']['enquiry_type']===$value?' selected':'' ?>><?= serviceEscape($label) ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="assessment-number">Assessment number (optional)</label></th>
          <td>
            <input type="text" id="assessment-number" name="assessment_number" maxlength="50" value="<?= serviceEscape($ratesData['values']['assessment_number']) ?>" autocomplete="off">
            <p>You can find this number near the top of your rates notice.</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="property-address">Property address (required)</label></th>
          <td>
            <input type="text" id="property-address" name="property_address" maxlength="255" value="<?= serviceEscape($ratesData['values']['property_address']) ?>" autocomplete="street-address" required>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="full-name">Full name (required)</label></th>
          <td>
            <input type="text" id="full-name" name="full_name" maxlength="150" value="<?= serviceEscape($ratesData['values']['full_name']) ?>" autocomplete="name" required>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="email">Email address (required)</label></th>
          <td>
            <input type="email" id="email" name="email" maxlength="254" value="<?= serviceEscape($ratesData['values']['email']) ?>" autocomplete="email" required>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="phone">Contact number (required for a phone response)</label></th>
          <td>
            <input type="tel" id="phone" name="phone" <?= $ratesData['values']['response_method']==='phone'?'required':'' ?> maxlength="30" value="<?= serviceEscape($ratesData['values']['phone']) ?>" autocomplete="tel">
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="enquiry-details">Enquiry details (required)</label></th>
          <td>
            <textarea id="enquiry-details" name="enquiry_details" rows="7" maxlength="2000" aria-describedby="enquiry-help" required><?= serviceEscape($ratesData['values']['enquiry_details']) ?></textarea>
            <p id="enquiry-help">Explain what you need help with and include relevant notice dates or reference numbers. Do not enter bank account or payment card details.</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Preferred response method</th>
          <td>
            <label for="response-email">
              <input type="radio" id="response-email" name="response_method" value="email" required <?= $ratesData['values']['response_method']==='email'?'checked':'' ?>>
              Email
            </label>
            <label for="response-phone">
              <input type="radio" id="response-phone" name="response_method" value="phone" <?= $ratesData['values']['response_method']==='phone'?'checked':'' ?>>
              Phone
            </label>
          </td>
        </tr>
      </tbody>
    </table>

    <hr>
    <button type="submit" class="btn btn-primary">Submit rates enquiry</button>
    <p>CityLink may need to confirm your identity before providing account-specific information. Personal information will be handled according to our <a href="./privacy.php">Privacy Policy</a>.</p>
  </form>
  <?php else: ?>
    <p><a class="btn btn-primary" href="./rates-enquiry.php?new=1">Submit another enquiry</a></p>
    <p><a class="card-link" href="./services.php">Return to services</a></p>
  <?php endif; ?>

  <div class="section-header">
    <span class="section-badge">Before you submit</span>
    <h2>Urgent payment difficulties</h2>
    <p>If you are having difficulty paying your rates, select “Payment arrangement” above and explain the assistance you need. Submitting this form does not pause an existing due date, fee or recovery action until CityLink confirms an arrangement.</p>
  </div>
</section>

<?php showFooter(); ?>
