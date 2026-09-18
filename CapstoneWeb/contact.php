<?php
require_once __DIR__ . '/layout.php';
showHeader('Contact', '');
?>

<section id="contact-section" class="detail-section" aria-labelledby="contact-heading">
  <?php showBreadcrumb(['Contact' => null]); ?>

  <div class="section-header">
    <span class="section-badge">CONTACT CITYLINK</span>
    <h1 id="contact-heading">How can we help?</h1>
    <p>Contact CityLink for help finding a service, using the portal or following up an existing request. Choose the contact option that best suits your enquiry.</p>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <div class="card-icon" aria-hidden="true">01</div>
      <h2 class="card-title">Call us</h2>
      <p class="card-desc"><a href="tel:+61890001234">(08) 9000 1234</a></p>
      <p class="card-desc">Monday to Friday, 8:30 am–5 pm, excluding public holidays.</p>
      <p class="card-desc">If you use the National Relay Service, ask to be connected to the CityLink number above.</p>
    </article>

    <article class="service-card">
      <div class="card-icon" aria-hidden="true">02</div>
      <h2 class="card-title">Email us</h2>
      <p class="card-desc"><a href="mailto:contact@citylink.example">contact@citylink.example</a></p>
      <p class="card-desc">General enquiries are usually acknowledged within two business days.</p>
      <p class="card-desc">Do not send passwords, payment card numbers or unnecessary sensitive information by email.</p>
    </article>

    <article class="service-card">
      <div class="card-icon" aria-hidden="true">03</div>
      <h2 class="card-title">Visit the service centre</h2>
      <p class="card-desc">CityLink Community Hub<br>25 Civic Square<br>CityLink WA 6000</p>
      <p class="card-desc">Monday to Friday, 9 am–4:30 pm. The entrance and customer counter are wheelchair accessible.</p>
    </article>
  </div>

  <div class="section-header">
    <span class="section-badge">BEFORE YOU CONTACT US</span>
    <h2>Information that helps us respond</h2>
    <p>Include your name, preferred contact method, the service involved and a clear description of what you need. If you are following up a request, include the reference number but do not publish it in a public forum.</p>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <h2 class="card-title">Service request</h2>
      <p class="card-desc">For waste, rates or facility enquiries, begin on the Services page so your request reaches the appropriate team.</p>
      <a href="./services.php" class="card-link">Browse CityLink services &rarr;</a>
    </article>
    <article class="service-card">
      <h2 class="card-title">Website feedback</h2>
      <p class="card-desc">Tell us about unclear information, a broken link or an accessibility problem with the CityLink portal.</p>
      <a href="./feedback.php" class="card-link">Send website feedback &rarr;</a>
    </article>
  </div>

  <div class="section-header">
    <span class="section-badge">URGENT HELP</span>
    <h2>Emergencies and urgent hazards</h2>
    <p>CityLink is not an emergency service. For a life-threatening emergency, call <strong>000</strong>. Do not use email or website feedback to report an immediate danger.</p>
  </div>
</section>

<?php showFooter(); ?>
