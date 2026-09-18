<?php
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
        <a href="./Template_AfterClick.html" class="card-link">View Rates Enquiries service &rarr;</a>
      </article>

      <!-- Service card 4 -->
      <article class="service-card">
        <div class="card-icon">04</div>
        <h3 class="card-title">Community Programs</h3>
        <p class="card-desc">Discover grants, workshops and local programs.</p>
        <a href="./services.php" class="card-link">View Community Programs service &rarr;</a>
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
    <div class="announcements-grid">
      
      <!-- Announcement card 1 -->
      <article class="announcement-card">
        <div class="announcement-date">30 July 2026</div>
        <h3 class="announcement-title">Community centre renewal works</h3>
        <p class="announcement-desc">The Riverside Community Centre upgrade begins this month, with temporary access arrangements in place.</p>
        <a href="./announcement-detail.php" class="btn btn-primary btn-sm">Read more</a>
      </article>

      <!-- Announcement card 2 -->
      <article class="announcement-card">
        <div class="announcement-date">24 July 2026</div>
        <h3 class="announcement-title">Spring event registrations open</h3>
        <p class="announcement-desc">Bookings are now available for family activities, workshops and community celebrations.</p>
        <a href="./announcement-detail.php" class="btn btn-primary btn-sm">Read more</a>
      </article>

      <!-- Announcement card 3 -->
      <article class="announcement-card">
        <div class="announcement-date">18 July 2026</div>
        <h3 class="announcement-title">Updated waste collection schedule</h3>
        <p class="announcement-desc">Some collection days will change during the August public holiday period.</p>
        <a href="./announcement-detail.php" class="btn btn-primary btn-sm">Read more</a>
      </article>

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
    <div class="around-grid">
      
      <!-- Community information card 1 -->
      <article class="around-card">
        <div class="around-tag">UPCOMING EVENTS</div>
        <h3 class="around-title">Neighbourhood Spring Fair</h3>
        <p class="around-desc">Saturday 15 August · Civic Square</p>
        <a href="./event-detail.php" class="around-link">Learn more about the Neighbourhood Spring Fair &rarr;</a>
      </article>

      <!-- Community information card 2 -->
      <article class="around-card">
        <div class="around-tag">COMMUNITY NEWS</div>
        <h3 class="around-title">New youth mentoring hub</h3>
        <p class="around-desc">A welcoming space for young people opens next month.</p>
        <a href="./event-detail.php" class="around-link">Learn more about the youth mentoring hub &rarr;</a>
      </article>

      <!-- Community information card 3 -->
      <article class="around-card">
        <div class="around-tag">POPULAR SERVICES</div>
        <h3 class="around-title">Need help with your rates?</h3>
        <p class="around-desc">Find payment options, due dates and support information.</p>
        <a href="./event-detail.php" class="around-link">Learn more about rates support &rarr;</a>
      </article>

    </div>
  </section>
 
  <?php showFooter(); ?>


