<?php
require_once __DIR__ . '/layout.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$pages = [
    [
        'title' => 'Waste and recycling',
        'category' => 'Service',
        'description' => 'Find bin collection information, recycling guidance and help with missed or damaged bins.',
        'url' => './waste-management.php',
        'keywords' => 'waste rubbish recycling bins collection hard waste',
    ],
    [
        'title' => 'Rates and property enquiries',
        'category' => 'Service',
        'description' => 'Ask about a rates notice, account balance, concession, valuation or payment arrangement.',
        'url' => './rates-enquiry.php',
        'keywords' => 'rates property notice payment account valuation concession',
    ],
    [
        'title' => 'Community events',
        'category' => 'Events',
        'description' => 'Browse upcoming workshops, community activities and local events across CityLink.',
        'url' => './events.php',
        'keywords' => 'events activities workshops community calendar',
    ],
    [
        'title' => 'Book a service or event',
        'category' => 'Bookings',
        'description' => 'View activities, appointments and community facilities currently available for booking.',
        'url' => './booking.php',
        'keywords' => 'booking reserve appointment facility venue workshop',
    ],
    [
        'title' => 'CityLink announcements',
        'category' => 'News',
        'description' => 'Read current notices about community facilities, service interruptions and local projects.',
        'url' => './announcements.php',
        'keywords' => 'announcements news notices updates works closure',
    ],
    [
        'title' => 'Contact CityLink',
        'category' => 'Contact',
        'description' => 'Find phone, email and service centre details for help with CityLink services.',
        'url' => './contact.php',
        'keywords' => 'contact phone email address help support',
    ],
    [
        'title' => 'Send feedback',
        'category' => 'Feedback',
        'description' => 'Submit a compliment, suggestion, complaint or report an issue with the website.',
        'url' => './feedback.php',
        'keywords' => 'feedback complaint compliment suggestion website issue',
    ],
    [
        'title' => 'Accessibility',
        'category' => 'Website information',
        'description' => 'Learn about CityLink accessibility support and ways to request information in another format.',
        'url' => './accessibility.php',
        'keywords' => 'accessibility accessible assistance format disability',
    ],
    [
        'title' => 'Privacy at CityLink',
        'category' => 'Website information',
        'description' => 'Read how CityLink intends to collect, use, store and protect personal information.',
        'url' => './privacy.php',
        'keywords' => 'privacy personal information data security policy',
    ],
];

$results = $pages;
if ($query !== '') {
    $results = array_values(array_filter($pages, function ($page) use ($query) {
        $searchableText = $page['title'] . ' ' . $page['category'] . ' ' . $page['description'] . ' ' . $page['keywords'];
        return stripos($searchableText, $query) !== false;
    }));
}

showHeader('Search');
?>

<div class="service1-section">
  <?php showBreadcrumb([
    'Search' => null,
  ]); ?>

  <div class="section-header">
    <span class="section-badge">Search CityLink</span>
    <h1 id="search-results-heading">Result of Search: <?= $query === '' ? 'All pages' : '“' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8') . '”' ?></h1>
    <p><?= count($results) ?> <?= count($results) === 1 ? 'result' : 'results' ?> found. Select a result to open the related CityLink page.</p>
  </div>
</div>

<?php if (count($results) > 0): ?>
  <?php foreach ($results as $index => $result): ?>
    <?php $sectionClass = $index % 2 === 0 ? 'service1-section' : 'service2-section'; ?>
    <section class="<?= $sectionClass ?>" aria-labelledby="search-result-<?= $index ?>">
      <div class="section-header">
        <h2 id="search-result-<?= $index ?>"><?= htmlspecialchars($result['title'], ENT_QUOTES, 'UTF-8') ?></h2>
      </div>

    <div class="cards-grid">
        <article class="service-card">
          <p class="card-desc"><?= htmlspecialchars($result['description'], ENT_QUOTES, 'UTF-8') ?></p>
          <a href="<?= htmlspecialchars($result['url'], ENT_QUOTES, 'UTF-8') ?>" class="card-link">View page &rarr;</a>
        </article>
    </div>
    </section>
  <?php endforeach; ?>
<?php else: ?>
  <section class="service1-section" aria-labelledby="no-results-heading">
    <div class="section-header">
      <span class="section-badge">No results</span>
      <h2 id="no-results-heading">No matching pages found</h2>
    </div>

    <div class="cards-grid">
      <article class="service-card">
        <p class="card-desc">Check the spelling, use fewer words or try a broader term such as “rates”, “waste”, “events” or “contact”.</p>
        <a href="./services.php" class="card-link">Browse all services &rarr;</a>
      </article>
    </div>
  </section>
<?php endif; ?>

<section id="search-help" class="service2-section" aria-labelledby="search-help-heading">
  <div class="section-header">
    <span class="section-badge">Search help</span>
    <h2 id="search-help-heading">Cannot find what you need?</h2>
    <p>Try searching for the name of a service or task. You can also <a href="./contact.php">contact CityLink</a> for help finding the correct information.</p>
  </div>
</section>

<?php showFooter(); ?>
