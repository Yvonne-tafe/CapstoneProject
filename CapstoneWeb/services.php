<?php
require_once __DIR__ . '/layout.php';
showHeader('Services', 'services');
?>
 <!-- Hero Section -->
  <section class="hero-section" aria-labelledby="hero-heading">
    <div class="hero-content">
      <span class="badge-text">WELCOME TO CITYLINK</span>
      <h1 id="hero-heading">Community services, made simple</h1>
      <p>Find council and community services, submit requests and access practical information for everyday life in CityLink.</p>

    </div>
    <div class="hero-card-visual" aria-hidden="true">
      <div class="orange-circle"></div>
      <div class="floating-box">
        Your community<br>Your services<br>Your CityLink
      </div>
    </div>
  </section>

  <!-- TODO: use php to read database, and fill data into Service Section -->
  <!-- Service Section -->
  <section id="service-1" class="service1-section" aria-labelledby="service-1-heading">
    <!-- Section heading -->
    <div class="section-header">
      <span class="section-badge">POPULAR SERVICE</span>
      <h2 id="service-1-heading">Waste and recycling</h2>
    </div>

    <!-- Service card grid -->
    <div class="cards-grid">
      <!-- Service card 1 -->
      <article class="service-card">
        <p class="card-desc">Check your next bin collection day, learn what belongs in each bin and report a missed or damaged bin.</p>
        <p class="card-desc"><strong>Available online:</strong> collection calendar, hard-waste requests and recycling guidance.</p>
        <a href="./waste-management.php" class="card-link">View waste and recycling information &rarr;</a>
      </article>
    </div>
  </section>

  <!-- Service Section -->
  <section id="service-2" class="service2-section" aria-labelledby="service-2-heading">
    <!-- Section heading -->
    <div class="section-header">
      <span class="section-badge">RESIDENT SUPPORT</span>
      <h2 id="service-2-heading">Rates and property enquiries</h2>
    </div>

    <!-- Service card grid -->
    <div class="cards-grid">
      <!-- Service card 2 -->
      <article class="service-card">
        <p class="card-desc">View payment options, request a copy of a rates notice or contact the Rates team about your property account.</p>
        <p class="card-desc"><strong>Before you begin:</strong> have your assessment number and property address ready.</p>
        <a href="./rates-enquiry.php" class="card-link">Make a rates enquiry &rarr;</a>
      </article>
    </div>
  </section>

  <!-- Service Section -->
<section id="service-3" class="service1-section" aria-labelledby="service-3-heading">
    <!-- Section heading -->
    <div class="section-header">
      <span class="section-badge">COMMUNITY</span>
      <h2 id="service-3-heading">Community facilities and programs</h2>
    </div>

    <!-- Service card grid -->
    <div class="cards-grid">
      <!-- Service card 3 -->
      <article class="service-card">
        <p class="card-desc">Discover local workshops, youth programs, support groups and spaces available for community hire.</p>
        <p class="card-desc"><strong>Bookings:</strong> select an activity or venue to check availability before submitting a request.</p>
        <a href="./events.php" class="card-link">Browse community events &rarr;</a>
        <a href="./booking.php" class="card-link">Start a facility booking &rarr;</a>
      </article>
    </div>
  </section>
 
  <?php showFooter(); ?>

