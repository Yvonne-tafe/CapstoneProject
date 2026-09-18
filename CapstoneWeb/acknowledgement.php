<?php
require_once __DIR__ . '/layout.php';
showHeader('Acknowledgement of Country', '');
?>

  <section id="acknowledgement-section" class="detail-section" aria-labelledby="acknowledgement-heading">
  <?php showBreadcrumb(['Acknowledgement of Country' => null]); ?>
  <div class="section-header">
    <span class="section-badge">RESPECT · RECOGNITION · COMMUNITY</span>
    <h1 id="acknowledgement-heading">Acknowledgement of Country</h1>
    <p>CityLink Initiatives acknowledges Aboriginal and Torres Strait Islander peoples as the First Peoples of Australia and the Traditional Custodians of the lands, waters and communities we serve.</p>
    <p>We pay our respects to Elders past and present, and recognise the continuing connection of Aboriginal and Torres Strait Islander peoples to Country, culture and community.</p>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <h2 class="card-title">Continuing connection to Country</h2>
      <p class="card-desc">We recognise that Country holds cultural, spiritual and social significance, and that Aboriginal cultures and knowledge continue to strengthen our wider community.</p>
    </article>
    <article class="service-card">
      <h2 class="card-title">Our responsibility</h2>
      <p class="card-desc">CityLink is committed to respectful communication, inclusive access to services and meaningful engagement with Aboriginal and Torres Strait Islander communities.</p>
    </article>
  </div>

  <div class="section-header">
    <span class="section-badge">A SHARED COMMITMENT</span>
    <h2>Respect in our services</h2>
    <p>This acknowledgement reflects our commitment to listening, learning and building respectful relationships. We recognise that an acknowledgement is one part of the ongoing work required to support inclusion and reconciliation.</p>
  </div>
</section>

<?php showFooter(); ?>
