<?php
require_once __DIR__ . '/layout.php';
showHeader('Services', 'services');
?>
 <!-- Hero Section -->
  <section class="hero-section" aria-labelledby="hero-heading">
    <div class="hero-content">
      <span class="badge-text">WELCOME TO CITYLINK</span>
      <h1 id="hero-heading">Community services, made simple</h1>
      <p>roughly introduce the function of this page?</p>

    </div>
    <div class="hero-card-visual" aria-hidden="true">
      <div class="orange-circle"></div>
      <div class="floating-box">
        Your community<br>Your Item<br>Your CityLink
      </div>
    </div>
  </section>

  <!-- TODO: use php to read database, and fill data into  Section -->
  <!-- Item Section -->
  <section id="Item-1" class="service1-section" aria-labelledby="Item-1-heading">
    <!-- Section heading -->
    <div class="section-header">
      <h2 id="Item-1-heading">Item 1 title</h2>
    </div>

    <!-- Item card grid -->
    <div class="cards-grid">
      <!-- Item card 1 -->
      <article class="service-card">
        <p>Descrption</p>
        <p>Descrption</p>
        <a href="./#" class="card-link">Link &rarr;</a>
      </article>
    </div>
  </section>

  <!-- Item Section -->
  <section id="Item-2" class="service2-section" aria-labelledby="Item-2-heading">
    <!-- Section heading -->
    <div class="section-header">
      <h2 id="Item-2-heading">Item 2 title</h2>
    </div>

    <!-- Item card grid -->
    <div class="cards-grid">
      <!-- Item card 2 -->
      <article class="service-card">
        <p>Descrption</p>
        <p>Descrption</p>
                <a href="./#" class="card-link">Link &rarr;</a>
      </article>
    </div>
  </section>

  <!-- Item Section -->
<section id="Item-3" class="service1-section" aria-labelledby="Item-3-heading">
    <!-- Section heading -->
    <div class="section-header">
      <h2 id="Item-3-heading">Item 3 title</h2>
    </div>

    <!-- Item card grid -->
    <div class="cards-grid">
      <!-- Item card 3 -->
      <article class="service-card">
        <p>Descrption</p>
        <p>Descrption</p>
        <a href="./#" class="card-link">Link &rarr;</a>
      </article>
    </div>
  </section>
 
  <?php showFooter(); ?>

