<?php
require_once __DIR__ . '/layout.php';
showHeader('Events', 'events');
?>
 <!-- Hero Section -->
  <section class="hero-section" aria-labelledby="hero-heading">
    <div class="hero-content">
      <span class="badge-text">WELCOME TO CITYLINK</span>
      <h1 id="hero-heading">Community Events</h1>
      <p>Discover free and low-cost activities, workshops and celebrations taking place across the CityLink community.</p>

    </div>
    <div class="hero-card-visual" aria-hidden="true">
      <div class="orange-circle"></div>
      <div class="floating-box">
        Your community<br>Your services<br>Your CityLink
      </div>
    </div>
  </section>

  <!-- TODO: use php to read database, and fill data into Event Section -->
  <!-- Service Section -->
  <section id="event-1" class="event1-section" aria-labelledby="event-1-heading">
    <!-- Section heading -->
    <div class="section-header">
      <span class="section-badge">FAMILY EVENT</span>
      <h2 id="event-1-heading">Neighbourhood Spring Fair</h2>
    </div>

    <!-- Service card grid -->
    <div class="cards-grid">
      <!-- Service card 1 -->
      <article class="service-card">
        <p class="announcement-date"><time datetime="2026-09-12T10:00">Saturday 12 September, 10 am–3 pm</time></p>
        <p class="card-desc"><strong>Civic Square</strong> · Free entry</p>
        <p class="card-desc">Enjoy local food stalls, live performances, children's activities and information from community organisations.</p>
        <a href="./event-detail.php" class="card-link">View event and accessibility details &rarr;</a>
      </article>
    </div>
  </section>

  <!-- Service Section -->
  <section id="event-2" class="event2-section" aria-labelledby="event-2-heading">
    <!-- Section heading -->
    <div class="section-header">
      <span class="section-badge">PRACTICAL WORKSHOP</span>
      <h2 id="event-2-heading">Introduction to home composting</h2>
    </div>

    <!-- Service card grid -->
    <div class="cards-grid">
      <!-- Service card 2 -->
      <article class="service-card">
        <p class="announcement-date"><time datetime="2026-09-17T18:00">Thursday 17 September, 6–7:30 pm</time></p>
        <p class="card-desc"><strong>Riverside Library meeting room</strong> · $5 per person</p>
        <p class="card-desc">Learn how to choose a compost system, balance food and garden waste and prevent common composting problems.</p>
        <a href="./booking.php" class="card-link">Book a workshop place &rarr;</a>
      </article>
    </div>
  </section>

  <!-- Service Section -->
<section id="event-3" class="event1-section" aria-labelledby="event-3-heading">
    <!-- Section heading -->
    <div class="section-header">
      <span class="section-badge">COMMUNITY WELLBEING</span>
      <h2 id="event-3-heading">Seniors digital help drop-in</h2>
    </div>

    <!-- Service card grid -->
    <div class="cards-grid">
      <!-- Service card 3 -->
      <article class="service-card">
        <p class="announcement-date"><time datetime="2026-09-22T09:30">Tuesday 22 September, 9:30 am–12 pm</time></p>
        <p class="card-desc"><strong>CityLink Community Hub</strong> · Free</p>
        <p class="card-desc">Bring your phone, tablet or laptop and receive friendly one-to-one help with email, video calls and online services.</p>
        <a href="./booking.php" class="card-link">Reserve a support session &rarr;</a>
      </article>
    </div>
  </section>
 
  <?php showFooter(); ?>

