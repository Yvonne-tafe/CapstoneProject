<?php
require_once __DIR__ . '/layout.php';
showHeader('Digital confidence for everyday life', 'services');
?>

<article class="announcement-detail" aria-labelledby="program-detail-heading">
  <?php showBreadcrumb([
    'Services' => './services.php',
    'Community programs' => './community-programs.php',
    'Digital confidence for everyday life' => null
  ]); ?>

  <header class="announcement-detail-header">
    <span class="section-badge">DIGITAL SKILLS · FREE PROGRAM</span>
    <h1 id="program-detail-heading">Digital confidence for everyday life</h1>
    <p class="announcement-lead">Build practical computer, tablet and smartphone skills in a friendly six-week program designed for beginners.</p>
    <dl class="announcement-meta">
      <div><dt>Dates</dt><dd><time datetime="2026-10-06">6 October</time>–<time datetime="2026-11-10">10 November 2026</time></dd></div>
      <div><dt>Time</dt><dd>Tuesdays, 10–11:30 am</dd></div>
      <div><dt>Location</dt><dd>Riverside Library computer room</dd></div>
      <div><dt>Cost</dt><dd>Free</dd></div>
    </dl>
  </header>

  <figure class="announcement-feature-image">
    <img src="./images/digital-confidence-program.png" alt="Adult learners receiving friendly help while using laptops and a tablet in a bright community library">
    <figcaption>Participants practise everyday digital tasks with guidance from a CityLink community learning facilitator.</figcaption>
  </figure>

  <div class="announcement-layout">
    <div class="announcement-body">
      <p>Digital Confidence for Everyday Life is a small-group program for adults who want to feel safer and more independent online. Each session combines a short demonstration with supported practice, and participants can ask questions about the devices they use at home.</p>

      <h2>What you will learn</h2>
      <ul>
        <li>how to use common phone, tablet and computer controls</li>
        <li>how to create, read and organise email</li>
        <li>how to make video calls and share photos safely</li>
        <li>how to recognise suspicious messages and protect personal information</li>
        <li>how to find trusted information and use CityLink online services</li>
      </ul>

      <aside class="announcement-notice" aria-labelledby="program-equipment-heading">
        <h2 id="program-equipment-heading">Equipment provided</h2>
        <p>Library computers are available for every session. You are also welcome to bring your own charged laptop, tablet or smartphone so the facilitator can help you practise on a familiar device.</p>
      </aside>

      <h2>Who can join</h2>
      <p>The program is intended for adults with limited digital experience. No previous computer skills are required. Class sizes are limited to ten participants so everyone can receive individual support.</p>

      <h2>Accessibility and support</h2>
      <p>The computer room has step-free access, adjustable workstations and an accessible toilet nearby. Large-print instructions are available. Tell us about any communication, mobility or learning support you need when booking.</p>

      <h2>Attendance and cancellations</h2>
      <p>Your booking covers all six sessions. Please contact the Community Programs team if you cannot attend so we can help you catch up or offer the place to someone on the waiting list.</p>
    </div>

    <aside class="announcement-sidebar" aria-label="Program booking and contact information">
      <div class="announcement-contact-card">
        <h2>Reserve your place</h2>
        <p>The program is free, but bookings are required because places and computers are limited.</p>
        <a href="./booking.php" class="btn btn-primary">Book this program</a>
      </div>
      <div class="announcement-update-card">
        <h2>Program contact</h2>
        <p><strong>Phone</strong><br><a href="tel:+61890001234">(08) 9000 1234</a></p>
        <p><strong>Email</strong><br><a href="mailto:programs@citylink.example">programs@citylink.example</a></p>
        <p><strong>Location</strong><br>Riverside Library<br>8 River Road, CityLink WA 6000</p>
      </div>
    </aside>
  </div>

  <footer class="announcement-detail-footer">
    <a href="./community-programs.php" class="card-link">&larr; Back to all community programs</a>
  </footer>
</article>

<?php showFooter(); ?>
