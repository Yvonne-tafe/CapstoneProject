# CityLink Community Portal

CityLink is a PHP and MySQL community portal developed for a capstone project. It provides service and event information, community announcements, activity and facility bookings, rates enquiries, feedback and role-based account access.

## Current development environment

The application runs locally on Windows using USBWebserver 8.6. Apache serves `CapstoneWeb` as the website document root, PHP handles requests on the server, and MySQL stores application data.

| Component | Verified local configuration |
| --- | --- |
| Web server | Apache 2.4.6, Win32 |
| Server-side language | PHP 5.4.17 |
| Database server | MySQL Community Server 5.6.13, Win32 |
| Website address | `http://localhost:8080/` |
| Database host and port | `localhost:3307` |
| Database name | `citylink` |
| Character set | `utf8mb4` |
| Application time zone | Australia/Perth, UTC+08:00 |
| Front end | HTML5, CSS and JavaScript |
| Shared configuration | `navigation.json` and `footer.json` |

Environment details were checked on 25 October 2026. These are the existing classroom development versions, not a production deployment specification. Changes to the PHP/MySQL versions, operating system or hosting environment require compatibility testing.

## Main features

- Database-driven services, events, community programs and announcement pages.
- Search across public content and selected information pages.
- Member registration, login, profile editing and personal submission history.
- Service/session bookings and exclusive half-day facility reservations, including guest bookings.
- Rates enquiry and feedback forms with confirmation references and validation.
- Administrator management of bookings, facility reservations, feedback and rates enquiries.
- JSON-driven primary navigation and footer, parsed by PHP in the shared layout.

## Repository structure

```text
CapstoneProject/
  README.md
  CapstoneWeb/              Website document root
    index.php              Home page
    layout.php             Shared header, navigation, breadcrumbs and footer
    style.css              Styles and responsive layout
    script.js              Browser interactions and form enhancements
    navigation.json        Primary menu configuration
    footer.json            Footer content and links
    config/                Database, authentication and feature logic
    includes/              Shared list, detail, search and booking processing
    template/              Reusable page and content templates
    images/                Website images
    *.php                  Public, member and administrator page entry points
```

## Run locally

### 1. Obtain the application and database

Clone or download this repository. Obtain the approved CityLink database schema and seed/export file separately from the project owner. A database installation script is **not currently included in this repository**, so cloning alone does not create a working database.

The project handover materials currently contain `citylink_virtual_content.sql`. Use the approved copy supplied with those materials; review its contents before importing into an isolated development database. Do not overwrite an existing database containing records you need to retain.

### 2. Configure the local web server

For the existing USBWebserver environment:

1. Open USBWebserver and configure Apache to listen on port `8080`.
2. Set the Apache document root and its matching directory configuration to the **absolute path of `CapstoneWeb`**, rather than the repository root.
3. Configure MySQL to use port `3307`, or adjust the application connection settings to match the port in your environment.
4. Start Apache and MySQL. Restart the affected server after changing its configuration.


### 3. Import and configure the database

1. Import the approved schema/data into the local MySQL server using phpMyAdmin or a MySQL client. The supplied export creates/uses the `citylink` database.
2. Set the local host, port, database username and password in `CapstoneWeb/config/database.php`. Use credentials for your own development database; do not publish them in documentation or commits.
3. The shared connection helper in `config/service_data.php` selects `citylink`, enables PDO exceptions and sets the database session time zone to `+08:00`.
4. Ensure PHP has PDO, `pdo_mysql` and OpenSSL support. The application uses PHP sessions and secure random tokens.
5. Keep the schema and seed data consistent with the application. In particular, `config/facility_services.php` maps service IDs to facility IDs, and the configured role/status records are required by the booking and authentication logic.

The current connection code uses `config/database.php`; it does not automatically read `.env` files. Do not assume that adding a `.env` file changes the database connection.

The supplied export uses lowercase table names, while PHP queries use mixed-case names. The verified Windows setup accepts these names. Check identifier case compatibility before moving to a case-sensitive database host.

### 4. Open the website

Visit **http://localhost:8080/**. Opening PHP files directly from File Explorer will not run the application.

`localhost` refers to the computer running the server. Access from another device requires that computer's LAN address, suitable server binding and network/firewall configuration; the local URL is not a public deployment.

## Useful routes

| Function | Route |
| --- | --- |
| Home | `/index.php` |
| Services / events / programs | `/services.php`, `/events.php`, `/community-programs.php` |
| Announcements | `/announcements.php` |
| Booking directory | `/booking.php` |
| Booking calendar | `/booking-sessions.php?service_id={id}` |
| Rates enquiry / feedback | `/rates-enquiry.php`, `/feedback.php` |
| Search | `/search.php?q={keyword}` |
| Registration / member login | `/signup.php`, `/login.php` |
| Member account | `/account.php` |
| Administrator login / dashboard | `/admin-login.php`, `/admin.php` |

Replace `{id}` and `{keyword}` with valid values. Pages with record IDs require corresponding active database records.

## Accounts and operating rules

Public registration creates a `general_user` account. Administrator access requires a separately provisioned account with the `admin` role. Request test credentials through the project handover channel; no passwords are documented here.

- Member pages require a valid login session; administrator pages also check the account role.
- Logout revokes the authenticated session. Login attempts are rate limited; if instructed to wait, allow the normal cooldown to expire.
- Service bookings reserve participant places when Confirmed. Facility bookings reserve an exclusive slot while Pending or Confirmed.
- Cancelled bookings remain as history and release their reservation.
- Rates enquiries and feedback require a resolution before being marked Resolved. Internal notes are not displayed in member histories.


## Project scope

This repository is an educational capstone development project. Placeholder contact addresses and inactive social links are sample content. The environment described above demonstrates a locally integrated full-stack system; it does not claim public hosting or production readiness.
