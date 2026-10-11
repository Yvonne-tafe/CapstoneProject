<?php
require_once __DIR__.'/config/admin.php'; adminRequire();
$savedRecord=isset($_SESSION['admin_saved_record'])?$_SESSION['admin_saved_record']:null;
unset($_SESSION['admin_saved_record']);
$lists=array(); $errors=array();
foreach (adminDefinitions() as $kind=>$definition) { try { $lists[$kind]=adminRows($kind); } catch (Exception $e) { error_log('Admin list: '.$e->getMessage()); $errors[$kind]='Records are temporarily unavailable.'; } }
require_once __DIR__.'/layout.php'; showHeader('Administration');
?>
<section class="detail-section"><div class="section-header"><span class="section-badge">Administration</span><h1>CityLink administration</h1><p>Double-click a row or select Edit to manage a record. Dates use Perth time. Select Date or Status to toggle sorting.</p></div>
<p><a href="#booking">Bookings</a> · <a href="#facility">Facility bookings</a> · <a href="#feedback">Feedback</a> · <a href="#rates">Rates enquiries</a></p>
<?php if ($savedRecord): ?><p class="booking-notice" role="status">Changes saved for <?= serviceEscape($savedRecord) ?>.</p><?php endif; ?>
<?php foreach (adminDefinitions() as $kind=>$d): ?>
<section id="<?= $kind ?>" aria-labelledby="<?= $kind ?>-heading"><h2 id="<?= $kind ?>-heading"><?= serviceEscape($d['title']) ?></h2>
<?php if (isset($errors[$kind])): ?><p role="alert"><?= serviceEscape($errors[$kind]) ?></p><?php else: ?>
<div class="admin-table-wrap"><table class="admin-table"><caption><?= count($lists[$kind]) ?> records · Date refers to <?= $kind==='booking' || $kind==='facility'?'booking':'submission' ?> time</caption><thead><tr><th scope="col">Reference</th><th scope="col">Item / type</th><th scope="col">Contact</th><th scope="col" aria-sort="<?= adminGet($kind.'_sort')!=='status'?(adminGet($kind.'_dir')==='asc'?'ascending':'descending'):'none' ?>"><a href="<?= serviceEscape(adminSortUrl($kind,'date')) ?>">Date</a></th><th scope="col" aria-sort="<?= adminGet($kind.'_sort')==='status'?(adminGet($kind.'_dir')==='asc'?'ascending':'descending'):'none' ?>"><a href="<?= serviceEscape(adminSortUrl($kind,'status')) ?>">Status</a></th><th scope="col">Action</th></tr></thead><tbody>
<?php foreach ($lists[$kind] as $row): $url=adminEditUrl($kind,$row['id']); ?>
<tr data-admin-edit="<?= serviceEscape($url) ?>"><td><?= serviceEscape($d['prefix'].$row['id']) ?></td><td><?= serviceEscape($row['title']) ?><?php if (isset($row['session_start_at'])): ?><br><?= serviceEscape(adminTime($row['session_start_at'])) ?><?php endif; ?><?php if (isset($row['property_address'])): ?><br><?= serviceEscape($row['property_address']) ?><?php endif; ?></td><td><?= serviceEscape(trim($row['first_name'].' '.$row['surname'])?:'Not provided') ?><br><?= serviceEscape($row['email']?:'Not provided') ?></td><td><?= serviceEscape(adminTime($row['date'])) ?></td><td><?= serviceEscape($row['status']) ?></td><td><a href="<?= serviceEscape($url) ?>" aria-label="Edit <?= serviceEscape($d['prefix'].$row['id']) ?>">Edit</a></td></tr>
<?php endforeach; ?>
<?php if (!$lists[$kind]): ?><tr><td colspan="6">No records yet.</td></tr><?php endif; ?>
</tbody></table></div><?php endif; ?></section>
<?php endforeach; ?>
<form method="post" action="./logout.php"><input type="hidden" name="csrf_token" value="<?= serviceEscape(authToken()) ?>"><button class="btn btn-primary" type="submit">Log out</button></form></section>
<?php showFooter(); ?>
