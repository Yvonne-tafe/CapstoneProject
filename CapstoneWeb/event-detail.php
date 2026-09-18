<?php
require_once __DIR__ . '/layout.php';
showHeader('Neighbourhood Spring Fair', 'events');
?>

<article class="announcement-detail" aria-labelledby="event-detail-heading">
  <?php showBreadcrumb([
    'Events' => './events.php',
    'Neighbourhood Spring Fair' => null
  ]); ?>

  <header class="announcement-detail-header">
    <span class="section-badge">FAMILY EVENT · FREE ENTRY</span>
    <h1 id="event-detail-heading">Neighbourhood Spring Fair</h1>
    <p class="announcement-lead">Celebrate the start of spring with local food, live entertainment, family activities and community organisations at Civic Square.</p>
    <dl class="announcement-meta">
      <div><dt>Date</dt><dd><time datetime="2026-09-12">Saturday 12 September 2026</time></dd></div>
      <div><dt>Time</dt><dd>10 am–3 pm</dd></div>
      <div><dt>Location</dt><dd>Civic Square, CityLink</dd></div>
      <div><dt>Cost</dt><dd>Free</dd></div>
    </dl>
  </header>

  <figure class="announcement-feature-image">
    <img src="./images/neighbourhood-spring-fair.png" alt="Families and community members visiting market stalls in a landscaped civic square">
    <figcaption>The Spring Fair brings local groups, performers and families together in the heart of CityLink.</figcaption>
  </figure>

  <div class="announcement-layout">
    <div class="announcement-body">
      <p>The Neighbourhood Spring Fair is a relaxed community day for residents of all ages. Drop in at any time to enjoy local entertainment, meet community groups and discover programs happening across CityLink.</p>

      <h2>What you can enjoy</h2>
      <ul>
        <li>local food, produce and handmade market stalls</li>
        <li>live acoustic music and community performances</li>
        <li>free children's craft, nature and outdoor activities</li>
        <li>information stalls from local clubs and support services</li>
        <li>a quiet activity space and shaded seating areas</li>
      </ul>

      <aside class="announcement-notice" aria-labelledby="event-access-heading">
        <h2 id="event-access-heading">Accessibility</h2>
        <p>Civic Square has step-free access, accessible toilets and designated accessible parking nearby. A quiet space and seating will be available. Contact the Events team before the day if you require additional assistance.</p>
      </aside>

      <h2>Getting there</h2>
      <p>Walking, cycling and public transport are encouraged. Bicycle parking is available at the eastern entrance. Limited public parking is available beneath the Civic Centre, with two-hour limits applying in surrounding streets.</p>

      <h2>What to bring</h2>
      <p>Bring a reusable water bottle, sun protection and a reusable shopping bag. Water refill stations will be available. Children must remain under the supervision of a responsible adult.</p>

      <h2>Weather updates</h2>
      <p>The fair will proceed in light rain. If unsafe weather requires cancellation, an update will be posted on the Events and Announcements pages by 8 am on the day.</p>
    </div>

    <aside class="announcement-sidebar" aria-label="Event booking and contact information">
      <div class="announcement-contact-card">
        <h2>Planning to attend?</h2>
        <p>General entry does not require a booking. Register if you would like event reminders and weather updates.</p>
        <a href="./booking.php" class="btn btn-primary">Register for updates</a>
      </div>
      <div class="announcement-update-card">
        <h2>Event contact</h2>
        <p><strong>Phone</strong><br><a href="tel:+61890001234">(08) 9000 1234</a></p>
        <p><strong>Email</strong><br><a href="mailto:events@citylink.example">events@citylink.example</a></p>
      </div>
    </aside>
  </div>

  <footer class="announcement-detail-footer">
    <a href="./events.php" class="card-link">&larr; Back to all events</a>
  </footer>
</article>

<?php showFooter(); ?>
