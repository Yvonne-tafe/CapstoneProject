<?php
require_once __DIR__ . '/layout.php';
showHeader('About', 'about');
?>

<section id="about-section" class="detail-section" aria-labelledby="about-heading">
  <?php showBreadcrumb(['About CityLink' => null]); ?>
  <div class="section-header">
    <span class="section-badge">ABOUT CITYLINK</span>
    <h1 id="about-heading">Connecting residents with local services</h1>
    <p>CityLink is a community portal designed to make local information, services and events easier to find and use. Residents can explore council services, read important announcements, discover community activities and submit enquiries from one central location.</p>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <div class="card-icon" aria-hidden="true">01</div>
      <h2 class="card-title">Our purpose</h2>
      <p class="card-desc">We help residents complete common tasks with fewer steps and provide clear information about the services that support everyday community life.</p>
    </article>
    <article class="service-card">
      <div class="card-icon" aria-hidden="true">02</div>
      <h2 class="card-title">Our community</h2>
      <p class="card-desc">CityLink is intended for residents, families, community groups, local organisations and visitors who need reliable local information.</p>
    </article>
    <article class="service-card">
      <div class="card-icon" aria-hidden="true">03</div>
      <h2 class="card-title">Our commitment</h2>
      <p class="card-desc">We aim to provide inclusive, accessible and easy-to-understand digital services that work across desktop, tablet and mobile devices.</p>
    </article>
  </div>

  <div class="section-header">
    <span class="section-badge">WHAT YOU CAN DO</span>
    <h2>Using the CityLink portal</h2>
    <p>Use CityLink to check waste and recycling information, make a rates enquiry, browse community events, read service updates and contact the appropriate team. New online services will be added as the portal develops.</p>
    <p>Information on this prototype is provided for demonstration purposes. Final service details, eligibility requirements and processing times will be confirmed before public release.</p>
  </div>

  <div class="section-header">
    <span class="section-badge">CONTACT AND FEEDBACK</span>
    <h2>Help us improve CityLink</h2>
    <p>Community feedback helps us improve navigation, content and online services. If information is unclear, unavailable or difficult to use, please tell us through the feedback page.</p>
    <a href="./feedback.php" class="card-link">Send feedback about CityLink &rarr;</a>
  </div>
</section>

<?php showFooter(); ?>
