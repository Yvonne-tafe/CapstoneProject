<?php
require_once __DIR__.'/config/admin.php';
$kind=adminGet('kind')==='facility'?'facility':'booking';
adminEditor($kind);
