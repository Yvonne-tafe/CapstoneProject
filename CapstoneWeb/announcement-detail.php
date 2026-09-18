<?php
require_once __DIR__ . '/layout.php';
showHeader('Community centre renewal works', 'announcements');
?>

<article class="announcement-detail" aria-labelledby="announcement-heading">
  <?php
  showBreadcrumb([
    'Announcements' => './announcements.php',
    'Community centre renewal works' => null
  ]);
  ?>

  <header class="announcement-detail-header">
    <span class="section-badge">COMMUNITY FACILITIES</span>
    <h1 id="announcement-heading">Community centre renewal works begin in September</h1>
    <p class="announcement-lead">Riverside Community Centre is being upgraded to provide safer, more accessible and more flexible spaces for local programs and events.</p>
    <dl class="announcement-meta">
      <div><dt>Published</dt><dd><time datetime="2026-09-01">1 September 2026</time></dd></div>
      <div><dt>Works period</dt><dd>7 September–18 December 2026</dd></div>
      <div><dt>Location</dt><dd>Riverside Community Centre</dd></div>
    </dl>
  </header>

  <figure class="announcement-feature-image">
    <img src="./images/community-centre-renewal.png" alt="Riverside Community Centre with temporary fencing around a landscaped construction area">
    <figcaption>Access to the reception entrance will remain available while landscaping and facility improvements are completed.</figcaption>
  </figure>

  <div class="announcement-layout">
    <div class="announcement-body">
      <p>CityLink Initiatives will begin renewal works at Riverside Community Centre on Monday 7 September. The project will improve accessibility, refresh shared activity rooms and create a more welcoming outdoor area for residents and community groups.</p>

      <h2>What the project includes</h2>
      <ul>
        <li>an upgraded accessible entrance and improved wayfinding</li>
        <li>refreshed multipurpose rooms with new lighting and flooring</li>
        <li>accessible toilet improvements and baby-change facilities</li>
        <li>new shaded seating, landscaping and bicycle parking</li>
        <li>energy-efficient lighting and improved building ventilation</li>
      </ul>

      <h2>Changes during construction</h2>
      <p>The centre will remain partially open. Reception and the northern meeting rooms will operate from 9 am to 5 pm, Monday to Friday. Some evening and weekend activities will move to nearby venues while noisy work is underway.</p>

      <aside class="announcement-notice" aria-labelledby="access-heading">
        <h2 id="access-heading">Access information</h2>
        <p>The main accessible path and two accessible parking bays will remain available. Follow temporary signs and allow extra time when arriving. Please contact us before your visit if you require assistance.</p>
      </aside>

      <h2>Temporary program locations</h2>
      <p>Registered participants will receive an email if their activity is relocated. Updated room and venue information will also appear in the CityLink event listing. Bookings already confirmed will remain valid.</p>

      <h2>Keeping the community informed</h2>
      <p>Construction updates will be published on this page as the project progresses. Work hours are generally 7 am to 5 pm on weekdays. We appreciate the community's patience while these improvements are completed.</p>
    </div>

    <aside class="announcement-sidebar" aria-label="Announcement contact information">
      <div class="announcement-contact-card">
        <h2>Need more information?</h2>
        <p>Contact the Community Facilities team.</p>
        <p><strong>Phone</strong><br><a href="tel:+61890001234">(08) 9000 1234</a></p>
        <p><strong>Email</strong><br><a href="mailto:community@citylink.example">community@citylink.example</a></p>
        <a class="btn btn-primary" href="./contact.html">Contact CityLink</a>
      </div>
      <div class="announcement-update-card">
        <h2>Latest update</h2>
        <p><time datetime="2026-09-01">1 September 2026</time></p>
        <p>Site fencing will be installed from 7 September. The reception entrance remains open.</p>
      </div>
    </aside>
  </div>

  <footer class="announcement-detail-footer">
    <a href="./announcements.php" class="card-link">&larr; Back to all announcements</a>
  </footer>
</article>

<?php showFooter(); ?>
