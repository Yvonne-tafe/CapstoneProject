<?php
require_once __DIR__ . '/layout.php';
showHeader('Waste and recycling', 'services');
?>

<article class="announcement-detail" aria-labelledby="waste-management-heading">
  <?php showBreadcrumb([
    'Services' => './services.php',
    'Waste and recycling' => null
  ]); ?>

  <header class="announcement-detail-header">
    <span class="section-badge">WASTE AND RECYCLING</span>
    <h1 id="waste-management-heading">Household bin collections and recycling</h1>
    <p class="announcement-lead">Find collection information, sort household waste correctly and request help with a missed, damaged or additional bin.</p>
    <dl class="announcement-meta">
      <div><dt>General waste</dt><dd>Collected weekly</dd></div>
      <div><dt>Recycling</dt><dd>Collected fortnightly</dd></div>
      <div><dt>Garden organics</dt><dd>Collected fortnightly</dd></div>
    </dl>
  </header>

  <figure class="announcement-feature-image">
    <img src="./images/waste-and-recycling-bins.png" alt="Green wheelie bins with green, yellow and red lids placed neatly near a suburban kerb">
    <figcaption>Place bins near the kerb before 6 am on collection day, with lids closed and space between each bin.</figcaption>
  </figure>

  <div class="announcement-layout">
    <div class="announcement-body">
      <p>CityLink provides kerbside waste, recycling and garden-organics collections to eligible residential properties. Correctly sorting items helps recover useful materials, reduces contamination and keeps collection services safe.</p>

      <h2>Using your household bins</h2>
      <ul>
        <li><strong>Red lid:</strong> general household waste that cannot be recycled or composted</li>
        <li><strong>Yellow lid:</strong> clean paper, cardboard, glass bottles and jars, metal cans and accepted rigid plastic containers</li>
        <li><strong>Green lid:</strong> lawn clippings, leaves, small branches and accepted food-organic material</li>
      </ul>

      <aside class="announcement-notice" aria-labelledby="bin-safety-heading">
        <h2 id="bin-safety-heading">Put your bins out safely</h2>
        <p>Place bins out before 6 am with the wheels facing your property. Keep lids closed and leave at least 50 cm between bins. Do not place bins beneath trees, beside parked vehicles or where they block a footpath or driveway.</p>
      </aside>

      <h2>Items that need another disposal option</h2>
      <p>Batteries, electronic waste, chemicals, gas cylinders, paint and building materials must not be placed in household bins. Use an approved drop-off service or contact CityLink for disposal advice.</p>

      <h2>Missed or damaged bins</h2>
      <p>Report a missed collection after 3 pm on your scheduled day. Before reporting it, check that the bin was presented on time, was not overfilled and did not contain prohibited items. Damaged or stolen bins can also be reported online.</p>

      <h2>Public holiday collections</h2>
      <p>Collection dates may change around public holidays. Check the Announcements page for confirmed arrangements before placing bins out.</p>
    </div>

    <aside class="announcement-sidebar" aria-label="Waste service actions">
      <div class="announcement-contact-card">
        <h2>Request assistance</h2>
        <p>Use the service request form for a missed collection, damaged bin or new residential bin request.</p>
        <a class="btn btn-primary" href="./booking.php">Start a waste request</a>
      </div>
      <div class="announcement-update-card">
        <h2>Waste Services</h2>
        <p><strong>Phone</strong><br><a href="tel:+61890001234">(08) 9000 1234</a></p>
        <p>Monday to Friday, 8:30 am–5 pm</p>
        <a href="./contact.php" class="card-link">Contact the Waste team &rarr;</a>
      </div>
    </aside>
  </div>

  <footer class="announcement-detail-footer">
    <a href="./services.php" class="card-link">&larr; Back to all services</a>
  </footer>
</article>

<?php showFooter(); ?>
