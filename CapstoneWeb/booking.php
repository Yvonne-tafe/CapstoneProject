<?php
require_once __DIR__ . '/layout.php';
showHeader('Bookings');
?>

<section id="booking-introduction" class="service1-section" aria-labelledby="booking-heading">
  <?php showBreadcrumb([
    'Bookings' => null,
  ]); ?>

  <div class="section-header">
    <span class="section-badge">Online bookings</span>
    <h1 id="booking-heading">Book a service or event</h1>
    <p>Choose an available activity, appointment or facility below. You will be asked to log in before confirming a booking.</p>
  </div>
</section>

<section id="event-bookings" class="service2-section" aria-labelledby="event-bookings-heading">
  <div class="section-header">
    <span class="section-badge">Events and programs</span>
    <h2 id="event-bookings-heading">Upcoming activities</h2>
    <p>Places are limited. Availability shown here is sample information for the current prototype.</p>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <span class="section-badge">12 places available</span>
      <h3 class="card-title">Introduction to home composting</h3>
      <p class="card-desc"><strong>Date:</strong> Thursday 17 September 2026, 6–7:30 pm</p>
      <p class="card-desc"><strong>Location:</strong> Riverside Library meeting room</p>
      <p class="card-desc"><strong>Cost:</strong> $5 per person</p>
      <a href="./login.php?return=booking.php&amp;item=compost-workshop" class="card-link">Log in to book &rarr;</a>
    </article>

    <article class="service-card">
      <span class="section-badge">6 sessions available</span>
      <h3 class="card-title">Seniors digital help drop-in</h3>
      <p class="card-desc"><strong>Date:</strong> Tuesday 22 September 2026, 9:30 am–12 pm</p>
      <p class="card-desc"><strong>Location:</strong> CityLink Community Hub</p>
      <p class="card-desc"><strong>Cost:</strong> Free</p>
      <a href="./login.php?return=booking.php&amp;item=digital-help" class="card-link">Reserve a session &rarr;</a>
    </article>

    <article class="service-card">
      <span class="section-badge">18 places available</span>
      <h3 class="card-title">School holiday art workshop</h3>
      <p class="card-desc"><strong>Date:</strong> Wednesday 30 September 2026, 10 am–12 pm</p>
      <p class="card-desc"><strong>Location:</strong> Civic Arts Studio</p>
      <p class="card-desc"><strong>Cost:</strong> $8 per child</p>
      <a href="./login.php?return=booking.php&amp;item=art-workshop" class="card-link">Log in to book &rarr;</a>
    </article>
  </div>
</section>

<section id="service-bookings" class="service1-section" aria-labelledby="service-bookings-heading">
  <div class="section-header">
    <span class="section-badge">Appointments and facilities</span>
    <h2 id="service-bookings-heading">Book a CityLink service</h2>
    <p>Select a service to begin. Appointment times and facility availability are confirmed during the booking process.</p>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <span class="section-badge">Appointments available</span>
      <h3 class="card-title">Rates support appointment</h3>
      <p class="card-desc">Speak with a Rates team member about a notice, concession application or payment arrangement.</p>
      <p class="card-desc"><strong>Location:</strong> CityLink Community Hub or phone appointment</p>
      <a href="./login.php?return=booking.php&amp;item=rates-support" class="card-link">Book an appointment &rarr;</a>
    </article>

    <article class="service-card">
      <span class="section-badge">Bookings open</span>
      <h3 class="card-title">Community room hire</h3>
      <p class="card-desc">Request a meeting room for a community group, workshop or small local event.</p>
      <p class="card-desc"><strong>Locations:</strong> Community Hub and Riverside Library</p>
      <a href="./login.php?return=booking.php&amp;item=room-hire" class="card-link">Check availability &rarr;</a>
    </article>

    <article class="service-card">
      <span class="section-badge">Weekday sessions</span>
      <h3 class="card-title">In-person online services help</h3>
      <p class="card-desc">Get assistance using CityLink forms, creating an account or finding the correct online service.</p>
      <p class="card-desc"><strong>Location:</strong> CityLink Community Hub</p>
      <a href="./login.php?return=booking.php&amp;item=online-services-help" class="card-link">Choose a time &rarr;</a>
    </article>
  </div>
</section>

<section id="booking-help" class="service2-section" aria-labelledby="booking-help-heading">
  <div class="section-header">
    <span class="section-badge">Booking help</span>
    <h2 id="booking-help-heading">Before you book</h2>
    <p>Have the attendee names and any accessibility requirements ready. Some services may also require a property address or account reference. If you cannot book online, call <a href="tel:+61890001234">(08) 9000 1234</a> during business hours.</p>
  </div>
</section>

<?php showFooter(); ?>
