<?php
/**
 * CityLink shared page frame, based on services.html.
 * Keep this file and calling pages beside style.css and script.js.
 * Run through a PHP web server (opening a PHP file directly will not work).
 * Including this file only defines functions; it does not print a page.
 *
 * Example usage in a new page such as services.php:
 *   <?php
 *   require_once __DIR__ . '/template.php';
 *   showHeader('Services', 'services');
 *   ?>
 *   <section class="form-section">
 *     <h1>Services</h1>
 *     <p>Write this page's content here.</p>
 *   </section>
 *   <?php showFooter(); ?>
 *
 * showHeader opens <main>; showFooter closes it. Do not add another <main>.
 * Hero sections belong to the calling page, not this shared template.
 * Navigation currently targets existing HTML files. Update these links to
 * PHP destinations as those pages are converted. No authentication is added.
 */

function showHeader($pageTitle = 'CityLink', $activePage = '')
{
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | CityLink Initiatives</title>
    <!-- Refresh the cached stylesheet whenever style.css is edited. -->
    <link rel="stylesheet" href="./style.css?v=<?= filemtime(__DIR__ . '/style.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body id="top">

  <!-- Top Bar -->
  <div class="top-bar">
    <div class="container-inner">
      <span><a href="#main-content" class="skip-link">Skip To Main Content</a></span>
      <span> &nbsp;|&nbsp; <a href="./accessibility.php" class="top-bar-link">Accessibility</a></span>
      <span> &nbsp;|&nbsp; <a href="./contact.php" class="top-bar-link">Contact</a></span>
    </div>
  </div>

  <!-- Header -->
  <header class="site-header">
    <div class="logo-area">
      <div class="logo-icon" aria-hidden="true">CL</div>
      <div class="logo-text">
        <span class="title">CityLink</span>
        <span class="subtitle">INITIATIVES</span>
      </div>
    </div>
    <div class="header-actions">
      <form class="search-box" role="search" action="./search.php" method="get">
        <input type="search" name="q" aria-label="Search CityLink services and information" placeholder="Search services and information">
        <button type="submit" class="search-icon" aria-label="Submit search">⌕</button>
      </form>
      <a href="./login.php" class="btn btn-primary">Login / Sign Up</a>
    </div>
  </header>

  
  <!-- Navigation -->
  <nav class="site-nav" aria-label="Primary navigation">
    <a href="./index.php" class="nav-link<?= $activePage === 'home' ? ' active' : '' ?>"<?= $activePage === 'home' ? ' aria-current="page"' : '' ?>>Home</a>
    <a href="./services.php" class="nav-link<?= $activePage === 'services' ? ' active' : '' ?>"<?= $activePage === 'services' ? ' aria-current="page"' : '' ?>>Services</a>
    <a href="./events.php" class="nav-link<?= $activePage === 'events' ? ' active' : '' ?>"<?= $activePage === 'events' ? ' aria-current="page"' : '' ?>>Events</a>
    <a href="./announcements.php" class="nav-link<?= $activePage === 'announcements' ? ' active' : '' ?>"<?= $activePage === 'announcements' ? ' aria-current="page"' : '' ?>>Announcements</a>
    <a href="./feedback.php" class="nav-link<?= $activePage === 'feedback' ? ' active' : '' ?>"<?= $activePage === 'feedback' ? ' aria-current="page"' : '' ?>>Feedback</a>
    <a href="./about.php" class="nav-link<?= $activePage === 'about' ? ' active' : '' ?>"<?= $activePage === 'about' ? ' aria-current="page"' : '' ?>>About</a>
  </nav>

<main id="main-content" tabindex="-1">
<?php
}

/**
 * Show a breadcrumb path. Home is added automatically.
 *
 * Example:
 * showBreadcrumb([
 *     'Announcements' => './announcements.php',
 *     'Community centre renewal works' => null
 * ]);
 *
 * Use null for the current (final) page. More parent levels can be added
 * without changing this function.
 */
function showBreadcrumb($items = [])
{
    $path = array_merge(['Home' => './index.php'], $items);
    $labels = array_keys($path);
    $firstLabel = $labels[0];
    $lastLabel = $labels[count($labels) - 1];
?>
  <nav class="breadcrumb" aria-label="Breadcrumb">
<?php foreach ($path as $label => $url): ?>
<?php if ($label !== $firstLabel): ?>
    <span aria-hidden="true">/</span>
<?php endif; ?>
<?php if ($label === $lastLabel || $url === null): ?>
    <span aria-current="page"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
<?php else: ?>
    <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
<?php endif; ?>
<?php endforeach; ?>
  </nav>
<?php
}

function showFooter()
{
?>
</main><!-- Footer Section -->
  <footer class="site-footer">
    <!-- Four-column footer navigation and information -->
    <div class="footer-columns">
      
      <!-- Footer column 1 -->
      <div id="about-citylink" class="footer-col">
        <h3 class="footer-heading">CityLink Initiatives</h3>
        <p class="footer-text">Serving our community online and in person.</p>
      </div>

      <!-- Footer column 2 -->
      <div id="accessibility-information" class="footer-col">
        <h3 class="footer-heading">Useful links</h3>
        <nav aria-label="Footer navigation">
          <p class="footer-text">
            <a href="./privacy.php">Privacy Policy</a><br>
            <a href="./accessibility.php">Accessibility</a><br>
            <a href="./contact.php">Contact Us</a>
          </p>
        </nav>
      </div>

      <!-- Footer column 3 -->
      <div id="contact-citylink" class="footer-col">
        <h3 class="footer-heading">Visit or call</h3>
        <p class="footer-text">
          <a href="./about.php">About this Site</a><br>
          <a href="tel:+61890001234">(08) 9000 1234</a>
        </p>
      </div>

      <!-- Footer column 4 -->
      <div class="footer-col">
        <h3 class="footer-heading">Follow us</h3>
        <p class="footer-text">
          <a href="./acknowledgement.php">Acknowledgment of Country</a><br>
          <!-- Add the real CityLink LinkedIn URL when available. -->
          <span>LinkedIn (coming soon)</span>
        </p>
      </div>

    </div>

    <!-- Copyright information -->
    <div class="footer-bottom">
      © 2026 CityLink Initiatives. All rights reserved.
    </div>
  </footer>
  <script src="./script.js" defer></script>
</body>
</html>
<?php
}

