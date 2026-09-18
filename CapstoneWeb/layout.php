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
  
   <!-- Navigation bar -->
  <nav class="site-nav" aria-label="Primary navigation">
  <?php
    //read navigtion from a Json file
    $jsonFile = __DIR__ . '/navigation.json';
    $menuData = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : ['navigation' => []];
    $currentPath = basename($_SERVER['PHP_SELF']);
  ?>
  <?php //if there is any issue, do Not display the navigation bar ?>
  <?php if (!empty($menuData['navigation']) && is_array($menuData['navigation'])): ?>
  <?php foreach ($menuData['navigation'] as $item): ?>
        <?php 
            $isActive = (basename($item['url']) === $currentPath);
            $activeClass = $isActive ? ' active' : '';
            $ariaCurrent = $isActive ? ' aria-current="page"' : '';
        ?>
        <a href="<?= htmlspecialchars($item['url']) ?>" class="nav-link<?= $activeClass ?>"<?= $ariaCurrent ?>><?= htmlspecialchars($item['label']) ?></a>
    <?php endforeach; ?>
  <?php endif; ?>
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
  
<?php
// Read navigation from a JSON file
$jsonFile = __DIR__ . '/footer.json';
$data = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : null;
$columns = (isset($data['main-navigation']) && is_array($data['main-navigation'])) ? $data['main-navigation'] : [];

// Map column headings to their specific IDs used in the original HTML
$columnIds = [
    "CityLink Initiatives" => "about-citylink",
    "Useful links" => "accessibility-information",
    "Visit or call" => "contact-citylink"
];
?>
<footer class="site-footer">
    <div class="footer-columns">
        <?php foreach ($columns as $col): 
            // Handle different key naming conventions ("main-label" vs others)
            $titleKey = isset($col['main-label']) ? 'main-label' : key($col);
            $title = isset($col[$titleKey]) ? $col[$titleKey] : '';
            
            // Assign ID if defined in mapping
            $idAttr = isset($columnIds[$title]) ? ' id="' . $columnIds[$title] . '"' : '';
        ?>
            <div<?= $idAttr ?> class="footer-col">
                <h3 class="footer-heading"><?= htmlspecialchars($title) ?></h3>
                
                <?php if (isset($col['navigation']['label'])): ?>
                    <!-- Single content block (Column 1) -->
                    <p class="footer-text"><?= htmlspecialchars($col['navigation']['label']) ?></p>
                <?php else: ?>
                    <!-- Multi-item navigation block (Columns 2, 3, 4) -->
                    <?php if ($title === "Useful links"): ?>
                    <nav aria-label="Footer navigation">
                    <?php endif; ?>
                    
                        <p class="footer-text">
                            <?php 
                            $navItems = (isset($col['navigation']) && is_array($col['navigation'])) ? $col['navigation'] : [];
                            $total = count($navItems);
                            foreach ($navItems as $i => $item):
                                $url = isset($item['url']) ? $item['url'] : '';
                                $label = isset($item['label']) ? htmlspecialchars($item['label']) : '';
                                
                                if (!empty($url)) {
                                    echo '<a href="' . htmlspecialchars($url) . '">' . $label . '</a>';
                                } else {
                                    echo '<span>' . $label . '</span>';
                                }
                                
                                if ($i < $total - 1) {
                                    echo "<br>\n";
                                }
                            endforeach; 
                            ?>
                        </p>
                        
                    <?php if ($title === "Useful links"): ?>
                    </nav>
                    <?php endif; ?>
                    
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="footer-bottom">
        &copy; 2026 CityLink Initiatives. All rights reserved.
    </div>
</footer>
  <script src="./script.js" defer></script>
</body>
</html>
<?php
}

