<?php
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

  <form class="form-table-container" method="post" action="">
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
              <option value="notice-copy">Rates notice or account copy</option>
              <option value="payment">Payment or account balance</option>
              <option value="payment-plan">Payment arrangement</option>
              <option value="concession">Pensioner or concession rebate</option>
              <option value="valuation">Property valuation</option>
              <option value="ownership">Ownership or postal address update</option>
              <option value="fees">Fees, interest or overdue rates</option>
              <option value="other">Other rates enquiry</option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="assessment-number">Assessment number (optional)</label></th>
          <td>
            <input type="text" id="assessment-number" name="assessment_number" inputmode="numeric" autocomplete="off">
            <p>You can find this number near the top of your rates notice.</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="property-address">Property address (required)</label></th>
          <td>
            <input type="text" id="property-address" name="property_address" autocomplete="street-address" required>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="full-name">Full name (required)</label></th>
          <td>
            <input type="text" id="full-name" name="full_name" autocomplete="name" required>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="email">Email address (required)</label></th>
          <td>
            <input type="email" id="email" name="email" autocomplete="email" required>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="phone">Contact number (optional)</label></th>
          <td>
            <input type="tel" id="phone" name="phone" autocomplete="tel">
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="enquiry-details">Enquiry details (required)</label></th>
          <td>
            <textarea id="enquiry-details" name="enquiry_details" rows="7" maxlength="2000" aria-describedby="enquiry-help" required></textarea>
            <p id="enquiry-help">Explain what you need help with and include relevant notice dates or reference numbers. Do not enter bank account or payment card details.</p>
          </td>
        </tr>
        <tr>
          <th scope="row">Preferred response method</th>
          <td>
            <label for="response-email">
              <input type="radio" id="response-email" name="response_method" value="email" checked>
              Email
            </label>
            <label for="response-phone">
              <input type="radio" id="response-phone" name="response_method" value="phone">
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

  <div class="section-header">
    <span class="section-badge">Before you submit</span>
    <h2>Urgent payment difficulties</h2>
    <p>If you are having difficulty paying your rates, select “Payment arrangement” above and explain the assistance you need. Submitting this form does not pause an existing due date, fee or recovery action until CityLink confirms an arrangement.</p>
  </div>
</section>

<?php showFooter(); ?>
