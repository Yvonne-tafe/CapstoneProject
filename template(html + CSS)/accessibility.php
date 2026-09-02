<?php
require_once __DIR__ . '/layout.php';
showHeader('Accessibility', 'accessibility');
?>

  <section id="accessibility-section" class="detail-section" aria-labelledby="accessibility-heading">
  <?php showBreadcrumb(['Accessibility' => null]); ?>
  <div class="section-header">
    <span class="section-badge">ACCESS FOR EVERYONE</span>
    <h1 id="accessibility-heading">Accessibility at CityLink</h1>
    <p>CityLink Initiatives is committed to providing information and online services that can be used by as many people as possible, including people who use assistive technologies or access the website on different devices.</p>
    <p>Our current accessibility target is the Web Content Accessibility Guidelines (WCAG) 2.2 Level AA. The portal is still being developed and tested, so some content may not yet meet this target.</p>
  </div>

  <div class="cards-grid">
    <article class="service-card">
      <h2 class="card-title">Keyboard access</h2>
      <p class="card-desc">Pages use a logical heading structure, visible keyboard focus and a “Skip to main content” link to reduce repeated navigation.</p>
    </article>
    <article class="service-card">
      <h2 class="card-title">Readable content</h2>
      <p class="card-desc">We use plain language, meaningful link text, clear labels and strong colour contrast to make information easier to understand.</p>
    </article>
    <article class="service-card">
      <h2 class="card-title">Flexible display</h2>
      <p class="card-desc">The layout adapts to mobile and desktop screens and is intended to remain usable when browser text is enlarged.</p>
    </article>
  </div>

  <div class="section-header">
    <span class="section-badge">USING THIS WEBSITE</span>
    <h2>Adjusting the website to suit your needs</h2>
    <p>You can use your browser settings to enlarge text, zoom the page or change display preferences. Most pages can be navigated using the Tab, Shift + Tab and Enter keys without requiring a mouse.</p>
    <p>Links and form controls are intended to have descriptive labels. Decorative content is hidden from assistive technologies where appropriate.</p>
  </div>

  <div class="section-header">
    <span class="section-badge">KNOWN LIMITATIONS</span>
    <h2>Areas still being improved</h2>
    <p>Some prototype links, forms and account functions are not yet connected to completed services. Documents supplied by third parties may also have different accessibility standards. These areas will be reviewed as development continues.</p>
  </div>

  <div class="section-header">
    <span class="section-badge">REPORT A PROBLEM</span>
    <h2>Tell us about an accessibility issue</h2>
    <p>If you cannot access information or complete a task, contact CityLink and describe the page, the problem and the format you need. We will try to provide the information in an accessible alternative.</p>
    <a href="./contact.html" class="card-link">Contact CityLink about accessibility &rarr;</a>
  </div>
</section>

<?php showFooter(); ?>
