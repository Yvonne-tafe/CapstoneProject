<?php
require_once __DIR__ . '/layout.php';
showHeader('Privacy', '');
?>

<section id="privacy-section" class="detail-section" aria-labelledby="privacy-heading">
  <?php showBreadcrumb(['Privacy' => null]); ?>

  <div class="section-header">
    <span class="section-badge">YOUR INFORMATION</span>
    <h1 id="privacy-heading">Privacy at CityLink</h1>
    <p>CityLink Initiatives respects your privacy and is committed to handling personal information responsibly, transparently and only for legitimate service purposes.</p>
    <p>This prototype policy explains the intended information-handling practices for the CityLink portal. It will be reviewed against the final system, organisational responsibilities and legal requirements before public release.</p>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <div class="card-icon" aria-hidden="true">01</div>
      <h2 class="card-title">Information we may collect</h2>
      <p class="card-desc">Your name, contact details, account information, service request details, event bookings, feedback and information needed to respond to an enquiry.</p>
    </article>
    <article class="service-card">
      <div class="card-icon" aria-hidden="true">02</div>
      <h2 class="card-title">Why we collect it</h2>
      <p class="card-desc">To provide requested services, manage bookings, respond to questions, communicate service updates and improve the portal.</p>
    </article>
    <article class="service-card">
      <div class="card-icon" aria-hidden="true">03</div>
      <h2 class="card-title">How we protect it</h2>
      <p class="card-desc">The completed service will use access controls, secure system practices and authorised staff access appropriate to the information being handled.</p>
    </article>
  </div>

  <div class="section-header">
    <span class="section-badge">COLLECTION AND USE</span>
    <h2>When you use CityLink</h2>
    <p>We aim to collect only the information reasonably needed for the service you choose. Forms should explain why information is requested and identify required fields. If you do not provide required information, we may be unable to complete the request.</p>
    <p>Basic technical information, such as browser type, page visits and error information, may be collected to maintain security and improve website performance. Analytics information should be aggregated or de-identified where practical.</p>
  </div>

  <div class="section-header">
    <span class="section-badge">DISCLOSURE</span>
    <h2>When information may be shared</h2>
    <p>Personal information may be provided to the CityLink team responsible for your request or to an authorised service provider supporting that service. Information should not be used or disclosed for an unrelated purpose unless you consent or the use is authorised or required by law.</p>
    <p>CityLink does not intend to sell personal information or provide it to advertisers.</p>
  </div>

  <div class="section-header">
    <span class="section-badge">STORAGE AND RETENTION</span>
    <h2>Keeping information secure</h2>
    <p>Information should be retained only for as long as it is required for service delivery, accountability, recordkeeping or legal purposes. When no longer required, records should be securely destroyed or de-identified in accordance with approved requirements.</p>
    <p>Email and online services always carry some risk. Do not include passwords, payment card details or unnecessary sensitive information in general enquiries.</p>
  </div>

  <div class="section-header">
    <span class="section-badge">YOUR CHOICES</span>
    <h2>Access, correction and privacy enquiries</h2>
    <p>You may contact CityLink if you believe personal information held about you is inaccurate, if you want to ask how it has been handled or if you wish to make a privacy complaint. We may need to verify your identity before discussing or changing personal information.</p>
    <a href="./contact.php" class="card-link">Contact CityLink about privacy &rarr;</a>
  </div>

  <div class="section-header">
    <span class="section-badge">POLICY STATUS</span>
    <h2>Updates to this policy</h2>
    <p>This policy may be updated when CityLink services, technology or legal requirements change. The current version will be published on this page.</p>
    <p><strong>Last reviewed:</strong> 1 September 2026</p>
  </div>
</section>

<?php showFooter(); ?>
