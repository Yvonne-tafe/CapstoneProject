<?php
require_once __DIR__ . '/layout.php';
showHeader('Edit account');
?>

<section id="account-edit-form" class="form-section" aria-labelledby="account-edit-heading">
  <?php showBreadcrumb([
    'My account' => './account.php',
    'Edit details' => null,
  ]); ?>

  <div class="section-header">
    <span class="section-badge">My account</span>
    <h1 id="account-edit-heading">Edit your account details</h1>
    <p>Keep your contact details up to date. Fields marked required must be completed. Optional information may be used to make service bookings easier.</p>
  </div>

  <form class="form-table-container" method="post" action="">
    <table class="form-table">
      <thead>
        <tr>
          <th scope="col">Field</th>
          <th scope="col">Your details</th>
        </tr>
      </thead>
      <tbody>
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
            <p>Booking confirmations and account notices will be sent to this address.</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="phone">Contact number (optional)</label></th>
          <td>
            <input type="tel" id="phone" name="phone" autocomplete="tel">
            <p>Include an Australian area code where applicable.</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="street-address">Street address (optional)</label></th>
          <td>
            <input type="text" id="street-address" name="street_address" autocomplete="street-address">
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="suburb">Suburb (optional)</label></th>
          <td>
            <input type="text" id="suburb" name="suburb" autocomplete="address-level2">
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="postcode">Postcode (optional)</label></th>
          <td>
            <input type="text" id="postcode" name="postcode" autocomplete="postal-code" inputmode="numeric" maxlength="4" pattern="[0-9]{4}">
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="date-of-birth">Date of birth (optional)</label></th>
          <td>
            <input type="date" id="date-of-birth" name="date_of_birth" autocomplete="bday">
            <p>Your age can be calculated from your date of birth when a service has age requirements.</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="gender">Gender (optional)</label></th>
          <td>
            <select id="gender" name="gender" autocomplete="sex">
              <option value="">Select an option</option>
              <option value="female">Female</option>
              <option value="male">Male</option>
              <option value="non-binary">Non-binary</option>
              <option value="self-described">Prefer to self-describe</option>
              <option value="not-say">Prefer not to say</option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">Booking contact preference (optional)</th>
          <td>
            <label for="contact-email">
              <input type="radio" id="contact-email" name="contact_preference" value="email">
              Email
            </label>
            <label for="contact-phone">
              <input type="radio" id="contact-phone" name="contact_preference" value="phone">
              Phone
            </label>
          </td>
        </tr>
      </tbody>
    </table>

    <hr>
    <button type="submit" class="btn btn-primary">Save changes</button>
    <p>Your personal information will be handled according to our <a href="./privacy.php">Privacy Policy</a>.</p>
  </form>
</section>

<?php showFooter(); ?>
