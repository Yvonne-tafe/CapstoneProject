<?php
// Use the same identity and expiry checks as member pages.
require_once __DIR__ . '/auth.php';
function currentBookingUser() {
    header('Cache-Control: no-store');
    return authCurrentUser();
}
