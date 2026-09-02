<?php
require_once __DIR__ . '/layout.php';
showHeader('Community programs', 'services');
?>

<section class="hero-section" aria-labelledby="hero-heading">
  <div class="hero-content">
    <span class="badge-text">CITYLINK COMMUNITY PROGRAMS</span>
    <h1 id="hero-heading">Connect, learn and take part</h1>
    <p>Explore affordable local programs that support wellbeing, practical skills and stronger community connections.</p>
  </div>
</section>

<section id="program-1" class="service1-section" aria-labelledby="program-1-heading">
  <div class="section-header">
    <span class="section-badge">DIGITAL SKILLS</span>
    <h2 id="program-1-heading">Digital confidence for everyday life</h2>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <p class="announcement-date">Tuesdays, 10 am–11:30 am · 6 October–10 November 2026</p>
      <p class="card-desc"><strong>Location:</strong> Riverside Library computer room</p>
      <p class="card-desc"><strong>Cost:</strong> Free · Bookings required</p>
      <p class="card-desc">A six-week beginner program covering email, online safety, video calls and access to government and CityLink services. Library computers are available, or participants may bring their own device.</p>
      <p class="card-desc"><strong>Suitable for:</strong> Adults with limited computer, tablet or smartphone experience.</p>
      <a href="./program-detail.php" class="card-link">View details &rarr;</a>
    </article>
  </div>
</section>

<section id="program-2" class="service2-section" aria-labelledby="program-2-heading">
  <div class="section-header">
    <span class="section-badge">YOUTH PROGRAM</span>
    <h2 id="program-2-heading">After-school creative studio</h2>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <p class="announcement-date">Wednesdays, 3:45 pm–5:15 pm · During school term</p>
      <p class="card-desc"><strong>Location:</strong> Civic Arts Studio</p>
      <p class="card-desc"><strong>Cost:</strong> $4 per session · Materials included</p>
      <p class="card-desc">Young people can try drawing, painting, printmaking and simple digital design in a relaxed, supervised environment. Each session includes a new project and time to work on individual ideas.</p>
      <p class="card-desc"><strong>Suitable for:</strong> Young people aged 12–17. A parent or guardian must complete the first registration.</p>
      <a href="./program-detail.php" class="card-link">View details &rarr;</a>
    </article>
  </div>
</section>

<section id="program-3" class="service1-section" aria-labelledby="program-3-heading">
  <div class="section-header">
    <span class="section-badge">HEALTH AND WELLBEING</span>
    <h2 id="program-3-heading">Community walking group</h2>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <p class="announcement-date">Fridays, 8 am–9 am · Weekly</p>
      <p class="card-desc"><strong>Meeting point:</strong> Main entrance, CityLink Community Hub</p>
      <p class="card-desc"><strong>Cost:</strong> Free · Registration recommended</p>
      <p class="card-desc">Join a friendly, volunteer-led walk through local parks and shared paths. Routes are approximately three kilometres and include a short rest stop. New participants are welcome throughout the year.</p>
      <p class="card-desc"><strong>Suitable for:</strong> Adults comfortable walking for up to one hour. Contact CityLink to discuss mobility or accessibility needs.</p>
      <a href="./program-detail.php" class="card-link">View details &rarr;</a>
    </article>
  </div>
</section>

<section id="program-help" class="service2-section" aria-labelledby="program-help-heading">
  <div class="section-header">
    <span class="section-badge">PROGRAM SUPPORT</span>
    <h2 id="program-help-heading">Need help taking part?</h2>
    <p>Tell us about accessibility, communication or participation requirements when you book. For help choosing a program or registering, contact CityLink on <a href="tel:+61890001234">(08) 9000 1234</a> or visit the Community Hub.</p>
  </div>
</section>

<?php showFooter(); ?>
