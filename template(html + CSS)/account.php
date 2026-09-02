<?php
require_once __DIR__ . '/layout.php';
showHeader('My account');
?>

<section id="account-details" class="detail-section" aria-labelledby="account-heading">
  <?php showBreadcrumb([
    'My account' => null,
  ]); ?>

  <div class="section-header">
    <span class="section-badge">My account</span>
    <h1 id="account-heading">Your account details</h1>
    <p>Review the information saved to your CityLink account. Optional details can help complete service bookings more quickly.</p>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <h2 class="card-title">Account information</h2>
      <p class="card-desc"><strong>Full name</strong><br>Jordan Lee</p>
      <p class="card-desc"><strong>Email address</strong><br>jordan.lee@example.com</p>
      <p class="card-desc"><strong>Account status</strong><br>Active</p>
    </article>

    <article class="service-card">
      <h2 class="card-title">Contact and address</h2>
      <p class="card-desc"><strong>Contact number</strong><br>0412 345 678</p>
      <p class="card-desc"><strong>Street address</strong><br>18 Riverside Avenue</p>
      <p class="card-desc"><strong>Suburb and postcode</strong><br>CityLink WA 6000</p>
    </article>

    <article class="service-card">
      <h2 class="card-title">Booking profile</h2>
      <p class="card-desc"><strong>Date of birth</strong><br>15 March 1992</p>
      <p class="card-desc"><strong>Age</strong><br>34</p>
      <p class="card-desc"><strong>Gender</strong><br>Prefer not to say</p>
      <p class="card-desc"><strong>Preferred contact method</strong><br>Email</p>
    </article>
  </div>

  <div class="section-header">
    <span class="section-badge">Manage your details</span>
    <h2>Need to update something?</h2>
    <p>You can change your contact, address and booking information at any time. Required account details must remain completed.</p>
    <p><a href="./announcement-detail.php" class="card-link">Edit account details &rarr;</a></p>
    
  </div>

  <div class="section-header">
    <span class="section-badge">Privacy</span>
    <h2>How your information is used</h2>
    <p>Your saved information is used to manage your account and pre-fill relevant service booking details. Review the <a href="./privacy.php">Privacy Policy</a> to learn more.</p>
  </div>
</section>

<?php showFooter(); ?>
