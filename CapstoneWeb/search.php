<?php
require_once __DIR__.'/includes/search_data.php';
$data=searchPageData(isset($_GET['q'])?$_GET['q']:'',isset($_GET['page'])?$_GET['page']:null);
$query=$data['query'];
require_once __DIR__.'/layout.php';
showHeader('Search');
?>
<div class="service1-section">
  <?php showBreadcrumb(array('Search'=>null)); ?>
  <div class="section-header">
    <span class="section-badge">Search CityLink</span>
    <h1 id="search-results-heading"><?= $query===''?'Search services and information':'Result of Search: “'.serviceEscape($query).'”' ?></h1>
    <?php if ($data['error']): ?>
      <p role="alert"><?= serviceEscape($data['error']) ?> <a href="<?= serviceEscape(searchPageUrl($query,1)) ?>">Return to search</a></p>
    <?php elseif ($query===''): ?>
      <p>Enter a keyword in the search box to find services, events, programs and announcements.</p>
    <?php elseif ($data['total']): ?>
      <p><?= $data['total'] ?> results found. Showing <?= ($data['page']-1)*10+1 ?>–<?= min($data['page']*10,$data['total']) ?>. Select a result to open the related CityLink page.</p>
    <?php endif; ?>
  </div>
</div>
<?php if (!$data['error'] && $query!==''): ?>
  <?php foreach ($data['results'] as $index=>$result): ?>
    <section class="<?= $index%2===0?'service1-section':'service2-section' ?>" aria-labelledby="search-result-<?= $index ?>">
      <div class="section-header">
        <span class="section-badge"><?= serviceEscape($result['category']) ?></span>
        <h2 id="search-result-<?= $index ?>"><?= serviceEscape($result['title']) ?></h2>
      </div>
      <div class="cards-grid">
        <article class="service-card">
          <p class="card-desc"><?= serviceEscape($result['description']) ?></p>
          <a href="<?= serviceEscape($result['url']) ?>" class="card-link" aria-label="<?= serviceEscape('View '.$result['title']) ?>">View details &rarr;</a>
        </article>
      </div>
    </section>
  <?php endforeach; ?>
  <?php if (!$data['total']): ?>
    <section class="service1-section" aria-labelledby="no-results-heading">
      <div class="section-header"><span class="section-badge">No results</span><h2 id="no-results-heading">沒有符合的結果</h2></div>
      <div class="cards-grid"><article class="service-card">
        <p class="card-desc">Check the spelling, use fewer words or try a broader term such as “rates”, “waste”, “events” or “contact”.</p>
        <a href="./services.php" class="card-link">Browse all services &rarr;</a>
      </article></div>
    </section>
  <?php elseif ($data['pages']>1): ?>
    <nav class="service-pagination" aria-label="Search result pages">
      <?php if ($data['page']>1): ?><a class="btn btn-secondary" href="<?= serviceEscape(searchPageUrl($query,$data['page']-1)) ?>">&larr; Previous</a><?php endif; ?>
      <span>Page <?= $data['page'] ?> of <?= $data['pages'] ?></span>
      <?php if ($data['page']<$data['pages']): ?><a class="btn btn-secondary" href="<?= serviceEscape(searchPageUrl($query,$data['page']+1)) ?>">Next &rarr;</a><?php endif; ?>
    </nav>
  <?php endif; ?>
<?php endif; ?>
<section id="search-help" class="service2-section" aria-labelledby="search-help-heading">
  <div class="section-header">
    <span class="section-badge">Search help</span><h2 id="search-help-heading">Cannot find what you need?</h2>
    <p>Try searching for the name of a service or task. You can also <a href="./contact.php">contact CityLink</a> for help finding the correct information.</p>
  </div>
</section>
<?php showFooter(); ?>
