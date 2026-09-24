# CityLink database setup

1. Start Apache and MySQL in USBWebServer.
2. Open `setup_database.php` through the same localhost website address used for the other CityLink PHP pages.
3. Click **Run SQL file**. The page uses `../config/database.php`, reads `citylink.sql`, and executes each statement in order.
4. Check that all 48 statements complete. The script creates `citylink`, 22 tables and 22 initial lookup records from specification v1.2.

The existing connection file connects to the MySQL server without selecting a database; the SQL file handles `CREATE DATABASE` and `USE citylink`. After setup, add `dbname=citylink` to the DSN in `config/database.php` so normal website requests select the database too. The setup request's `USE` does not carry over to other requests.

The SQL is adapted for this USBWebServer installation (PHP 5.4.17 / MySQL 5.6.13). The two `content_items` fields store JSON text in TEXT columns; PHP must encode/decode and validate arrays of strings. The unique `user_email` column uses MySQL's three-byte utf8 character set to fit the older InnoDB index limit. Other text columns use utf8mb4.

Primary keys, foreign keys and specified unique keys are included. Business validation from the specification, including booking capacity, non-overlapping facility sessions, valid dates and conditional required fields, must be implemented in PHP. Concurrent reservations require a transaction and a lock on the relevant session row before checking and inserting/updating bookings.

Re-running skips existing tables and existing lookup IDs. It does not migrate or verify existing table structures or replace existing lookup values. If an error occurs, fix it and re-run; MySQL DDL may already have committed earlier successful statements. There are no DROP or DELETE statements.

The runner supports ordinary semicolon-separated SQL, quotes, and ordinary comments. It does not support DELIMITER, stored routines, executable comments, or NO_BACKSLASH_ESCAPES mode. SELECT statements are executed but returned rows are not displayed. The file path is fixed in `setup_database.php`; there is no SQL upload or arbitrary file selection.

This is a local development setup tool, not a public website page. Run it through localhost. Keep it out of a public deployment.
