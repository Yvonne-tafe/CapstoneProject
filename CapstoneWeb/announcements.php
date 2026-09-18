<?php
require_once __DIR__ . '/layout.php';
showHeader('Announcements', 'announcements');
?>
 <!-- Hero Section -->
  <section class="hero-section" aria-labelledby="hero-heading">
    <div class="hero-content">
      <span class="badge-text">WELCOME TO CITYLINK</span>
      <h1 id="hero-heading">Community announcements</h1>
      <p>Stay informed about facility updates, service changes, road works and other notices affecting the CityLink community.</p>

    </div>
    <div class="hero-card-visual" aria-hidden="true">
      <div class="orange-circle"></div>
      <div class="floating-box">
        Your community<br>Your services<br>Your CityLink
      </div>
    </div>
  </section>

  <!-- TODO: use php to read database, and fill data into Announcement Section -->
  <!-- Announcement Section -->
  <section id="announcement-1" class="announcement1-section" aria-labelledby="announcement-1-heading">
    <!-- Section heading -->
    <div class="section-header">
      <span class="section-badge">COMMUNITY FACILITIES</span>
      <h2 id="announcement-1-heading">Community centre renewal works begin in September</h2>
    </div>

    <!-- Announcement card grid -->
    <div class="cards-grid">
      <!-- Announcement card 1 -->
      <article class="announcement-card">
        <p class="announcement-date"><time datetime="2026-09-01">1 September 2026</time></p>
        <p class="announcement-desc">Riverside Community Centre will receive accessibility, activity-room and landscaping improvements. The centre remains partially open during construction.</p>
        <a href="./announcement-detail.php" class="card-link">Read the renewal works update &rarr;</a>
      </article>
    </div>
  </section>

  <!-- Announcement Section -->
  <section id="announcement-2" class="announcement2-section" aria-labelledby="announcement-2-heading">
    <!-- Section heading -->
    <div class="section-header">
      <span class="section-badge">WASTE SERVICES</span>
      <h2 id="announcement-2-heading">Public holiday bin collections move back one day</h2>
    </div>

    <!-- Announcement card grid -->
    <div class="cards-grid">
      <!-- Announcement card 2 -->
      <article class="announcement-card">
        <p class="announcement-date"><time datetime="2026-08-28">28 August 2026</time></p>
        <p class="announcement-desc">Collections scheduled from Monday 28 September to Friday 2 October will take place one day later than usual. Friday services will occur on Saturday.</p>
        <a href="./announcement-detail.php?id=2" class="card-link">Check the revised collection schedule &rarr;</a>
      </article>
    </div>
  </section>

  <!-- Announcement Section -->
<section id="announcement-3" class="announcement1-section" aria-labelledby="announcement-3-heading">
    <!-- Section heading -->
    <div class="section-header">
      <span class="section-badge">COMMUNITY GRANTS</span>
      <h2 id="announcement-3-heading">2026 neighbourhood grants now open</h2>
    </div>

    <!-- Announcement card grid -->
    <div class="cards-grid">
      <!-- Announcement card 3 -->
      <article class="announcement-card">
        <p class="announcement-date"><time datetime="2026-08-24">24 August 2026</time></p>
        <p class="announcement-desc">Local not-for-profit groups can apply for grants of up to $5,000 for projects that build connection, inclusion and neighbourhood participation.</p>
        <a href="./announcement-detail.php?id=3" class="card-link">View eligibility and application dates &rarr;</a>
      </article>
    </div>
  </section>
 
  <?php showFooter(); ?>

