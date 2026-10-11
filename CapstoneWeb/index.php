<?php
require_once __DIR__ . '/config/home_data.php';
$homeData=homePageData();
require_once __DIR__ . '/layout.php';
showHeader('Home', 'home');
?>
  <!-- Hero Section -->
   
  <section class="hero-section" aria-labelledby="hero-heading">
    <div class="hero-content">
      <span class="badge-text">WELCOME TO CITYLINK</span>
      <h1 id="hero-heading">Community services, made simple</h1>
      <p>Book local events, manage council services, find community programs and stay informed—all in one secure place.</p>
      <div class="hero-buttons">
        <button type="button" class="btn btn-primary">Explore services</button>
        <button type="button" class="btn btn-primary">Announcements</button>
      </div>
    </div>
    <div class="hero-card-visual" aria-hidden="true">
      <div class="orange-circle"></div>
      <div class="floating-box">
        Your community<br>Your services<br>Your CityLink
      </div>
    </div>
  </section>

  <!-- MOST USED Section -->
  <section id="quick-services" class="most-used-section" aria-labelledby="quick-services-heading">
    <!-- Section heading -->
    <div class="section-header">
      <span class="section-badge">MOST USED</span>
      <h2 id="quick-services-heading">Quick services</h2>
      <p>Start with one of our most popular community services.</p>
    </div>

    <!-- Service card grid -->
    <div class="cards-grid">
      
      <!-- Service card 1 -->
      <article class="service-card">
        <div class="card-icon">01</div>
        <h3 class="card-title">Event Bookings</h3>
        <p class="card-desc">Reserve places at council events and activities.</p>
        <a href="./events.php" class="card-link">View Event Bookings service &rarr;</a>
      </article>

      <!-- Service card 2 -->
      <article class="service-card">
        <div class="card-icon">02</div>
        <h3 class="card-title">Waste Management</h3>
        <p class="card-desc">Check bin days, recycling and collection updates.</p>
        <a href="./waste-management.php" class="card-link">View Waste Management service &rarr;</a>
      </article>

      <!-- Service card 3 -->
      <article class="service-card">
        <div class="card-icon">03</div>
        <h3 class="card-title">Rates Enquiries</h3>
        <p class="card-desc">View rates information and request support.</p>
        <a href="./rates-enquiry.php" class="card-link">View Rates Enquiries service &rarr;</a>
      </article>

      <!-- Service card 4 -->
      <article class="service-card">
        <div class="card-icon">04</div>
        <h3 class="card-title">Community Programs</h3>
        <p class="card-desc">Discover grants, workshops and local programs.</p>
        <a href="./community-programs.php" class="card-link">View Community Programs &rarr;</a>
      </article>

    </div>
  </section>
  <!-- STAY INFORMED Section -->
  <section id="announcements" class="stay-informed-section" aria-labelledby="announcements-heading">
    <!-- Section heading -->
    <div class="section-header">
      <span class="section-badge">STAY INFORMED</span>
      <h2 id="announcements-heading">Latest announcements</h2>
      <p>Council updates and important notices for our community.</p>
    </div>

    <!-- Announcement card grid -->
    <?php if ($homeData['announcement_error']): ?><p role="alert">Announcements are temporarily unavailable. Please try again later.</p>
    <?php elseif (!$homeData['announcements']): ?><p>No announcements are currently available.</p><?php endif; ?>
    <div class="announcements-grid">
      <?php foreach ($homeData['announcements'] as $announcement): ?>
      <article class="announcement-card">
        <div class="announcement-date"><time datetime="<?= serviceEscape($announcement['published_iso']) ?>"><?= serviceEscape($announcement['published_label']) ?></time></div>
        <h3 class="announcement-title"><?= serviceEscape($announcement['announcement_title']) ?></h3>
        <p class="announcement-desc"><?= serviceEscape($announcement['announcement_short_desc']) ?></p>
        <a href="<?= serviceEscape($announcement['detail_url']) ?>" class="btn btn-primary btn-sm">Read more</a>
      </article>
      <?php endforeach; ?>
    </div>
  </section>
<!-- AROUND CITYLINK Section -->
  <section class="around-citylink-section" aria-labelledby="featured-information-heading">
    <!-- Section heading -->
    <div class="section-header">
      <span class="section-badge">AROUND CITYLINK</span>
      <h2 id="featured-information-heading">Featured community information</h2>
      <p>What is happening, what is new and what residents use most.</p>
    </div>

    <!-- Community information card grid -->
    <?php if ($homeData['featured_error']): ?><p role="alert">Featured information is temporarily unavailable. Please try again later.</p>
    <?php elseif (!$homeData['featured']): ?><p>No featured information is currently available.</p><?php endif; ?>
    <div class="around-grid">
      <?php foreach ($homeData['featured'] as $item): ?>
      <article class="around-card">
        <div class="around-tag"><?= serviceEscape($item['label']) ?></div>
        <h3 class="around-title"><?= serviceEscape($item['service_title']) ?></h3>
        <p class="around-desc"><?= serviceEscape($item['service_short_desc']) ?></p>
        <a href="<?= serviceEscape($item['detail_url']) ?>" class="around-link">Learn more &rarr;</a>
      </article>
      <?php endforeach; ?>
    </div>
  </section>
 
  <?php showFooter(); ?>


