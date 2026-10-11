-- CityLink database design v1.2. Run using setup_database.php on localhost.
-- Compatible with local MySQL 5.6.13. content_items uses TEXT for JSON data; PHP must validate its JSON array of strings.
-- Re-running creates missing objects only; it is not a schema migration.
-- No DROP, DELETE or sample personal data are included.
-- PHP must enforce documented validation rules: date/time ordering, positive
-- capacity/non-negative fees, valid emails, content-type fields, active parent
-- records, booking windows, audit ownership and allowed status transitions.
-- Reservation capacity and facility overlap checks require transactions/locking.
-- Pending AND Confirmed facility bookings reserve a slot. Service bookings
-- reserve participant places only when Confirmed. Do not rely on these tables
-- alone to prevent concurrent overbooking. Use the parent session row as a lock.
-- All application and database session timestamps use Australia/Perth (+08:00).

CREATE DATABASE IF NOT EXISTS `citylink` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `citylink`;
SET NAMES utf8mb4;
SET time_zone = '+08:00';

CREATE TABLE IF NOT EXISTS `ActiveStatus` (
  `active_status_id` INT NOT NULL PRIMARY KEY,
  `active_status_name` VARCHAR(20) NOT NULL UNIQUE,
  `active_status_desc` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `SessionStatus` (
  `session_status_id` INT NOT NULL PRIMARY KEY,
  `session_status_name` VARCHAR(20) NOT NULL UNIQUE,
  `session_status_desc` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `AnnouncementStatus` (
  `announcement_status_id` INT NOT NULL PRIMARY KEY,
  `announcement_status_name` VARCHAR(20) NOT NULL UNIQUE,
  `announcement_status_desc` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `FeedbackCategory` (
  `feedback_category_id` INT NOT NULL PRIMARY KEY,
  `feedback_category_name` VARCHAR(30) NOT NULL UNIQUE,
  `feedback_category_desc` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `FeedbackStatus` (
  `feedback_status_id` INT NOT NULL PRIMARY KEY,
  `feedback_status_name` VARCHAR(30) NOT NULL UNIQUE,
  `feedback_status_desc` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `BookingStatus` (
  `booking_status_id` INT NOT NULL PRIMARY KEY,
  `booking_status_name` VARCHAR(20) NOT NULL UNIQUE,
  `booking_status_desc` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `Role` (
  `role_id` INT NOT NULL PRIMARY KEY,
  `role_name` VARCHAR(30) NOT NULL UNIQUE,
  `role_desc` VARCHAR(255) NULL,
  `role_note` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ServiceType` (
  `service_type_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `service_type_name` VARCHAR(100) NOT NULL UNIQUE,
  `service_type_quick_desc` VARCHAR(50) NULL,
  `service_type_short_desc` VARCHAR(255) NULL,
  `service_type_desc` TEXT NULL,
  `service_type_note` TEXT NULL,
  `active_status_id` INT NOT NULL DEFAULT 1,
  CONSTRAINT `fk_ServiceType_active_status_id` FOREIGN KEY (`active_status_id`) REFERENCES `ActiveStatus` (`active_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `Service` (
  `service_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `service_type_id` INT NOT NULL,
  `service_title` VARCHAR(150) NOT NULL,
  `service_quick_desc` VARCHAR(50) NULL,
  `service_short_desc` VARCHAR(255) NULL,
  `service_desc` TEXT NULL,
  `service_link` VARCHAR(500) NULL,
  `service_fee` DECIMAL(10,2) NULL,
  `service_fee_basis` VARCHAR(100) NULL,
  `service_location` VARCHAR(255) NULL,
  `service_target_audience` TEXT NULL,
  `service_contact_name` VARCHAR(150) NULL,
  `service_contact_phone` VARCHAR(30) NULL,
  `service_contact_email` VARCHAR(254) NULL,
  `booking_required` BOOLEAN NOT NULL DEFAULT FALSE,
  `service_booking_requirements` TEXT NULL,
  `service_session_details` TEXT NULL,
  `service_note` TEXT NULL,
  `active_status_id` INT NOT NULL DEFAULT 1,
  CONSTRAINT `fk_Service_service_type_id` FOREIGN KEY (`service_type_id`) REFERENCES `ServiceType` (`service_type_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_Service_active_status_id` FOREIGN KEY (`active_status_id`) REFERENCES `ActiveStatus` (`active_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ServiceWebContent` (
  `service_web_content_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `service_id` INT NOT NULL,
  `content_key` VARCHAR(50) NOT NULL,
  `content_type` VARCHAR(30) NOT NULL,
  `content_heading` VARCHAR(150) NULL,
  `content_body` TEXT NULL,
  `content_items` TEXT NULL,
  `image_url` VARCHAR(500) NULL,
  `image_alt_text` VARCHAR(255) NULL,
  `image_caption` VARCHAR(255) NULL,
  `display_order` INT NOT NULL,
  `content_note` TEXT NULL,
  UNIQUE KEY `uq_ServiceWebContent` (`service_id`, `content_key`),
  CONSTRAINT `fk_ServiceWebContent_service_id` FOREIGN KEY (`service_id`) REFERENCES `Service` (`service_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ServiceSession` (
  `service_session_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `service_id` INT NOT NULL,
  `session_title` VARCHAR(150) NULL,
  `session_start_at` DATETIME NOT NULL,
  `session_end_at` DATETIME NOT NULL,
  `session_location` VARCHAR(255) NULL,
  `session_capacity` INT NULL,
  `booking_open_at` DATETIME NULL,
  `booking_close_at` DATETIME NULL,
  `session_note` TEXT NULL,
  `active_status_id` INT NOT NULL DEFAULT 1,
  `session_status_id` INT NOT NULL DEFAULT 1,
  CONSTRAINT `fk_ServiceSession_service_id` FOREIGN KEY (`service_id`) REFERENCES `Service` (`service_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_ServiceSession_active_status_id` FOREIGN KEY (`active_status_id`) REFERENCES `ActiveStatus` (`active_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_ServiceSession_session_status_id` FOREIGN KEY (`session_status_id`) REFERENCES `SessionStatus` (`session_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `AnnouncementType` (
  `announcement_type_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `announcement_type_name` VARCHAR(100) NOT NULL UNIQUE,
  `announcement_type_quick_desc` VARCHAR(50) NULL,
  `announcement_type_short_desc` VARCHAR(255) NULL,
  `announcement_type_desc` TEXT NULL,
  `announcement_type_note` TEXT NULL,
  `active_status_id` INT NOT NULL DEFAULT 1,
  CONSTRAINT `fk_AnnouncementType_active_status_id` FOREIGN KEY (`active_status_id`) REFERENCES `ActiveStatus` (`active_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `Announcement` (
  `announcement_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `announcement_type_id` INT NOT NULL,
  `announcement_title` VARCHAR(150) NOT NULL,
  `announcement_quick_desc` VARCHAR(50) NULL,
  `announcement_short_desc` TEXT NULL,
  `announcement_published_at` DATETIME NULL,
  `announcement_link` VARCHAR(500) NULL,
  `announcement_link_label` VARCHAR(150) NULL,
  `announcement_contact_name` VARCHAR(150) NULL,
  `announcement_contact_phone` VARCHAR(30) NULL,
  `announcement_contact_email` VARCHAR(254) NULL,
  `announcement_note` TEXT NULL,
  `announcement_status_id` INT NOT NULL DEFAULT 1,
  `active_status_id` INT NOT NULL DEFAULT 1,
  CONSTRAINT `fk_Announcement_announcement_type_id` FOREIGN KEY (`announcement_type_id`) REFERENCES `AnnouncementType` (`announcement_type_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_Announcement_announcement_status_id` FOREIGN KEY (`announcement_status_id`) REFERENCES `AnnouncementStatus` (`announcement_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_Announcement_active_status_id` FOREIGN KEY (`active_status_id`) REFERENCES `ActiveStatus` (`active_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `AnnouncementWebContent` (
  `announcement_web_content_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `announcement_id` INT NOT NULL,
  `content_key` VARCHAR(50) NOT NULL,
  `content_type` VARCHAR(30) NOT NULL,
  `content_heading` VARCHAR(150) NULL,
  `content_body` TEXT NULL,
  `content_items` TEXT NULL,
  `image_url` VARCHAR(500) NULL,
  `image_alt_text` VARCHAR(255) NULL,
  `image_caption` VARCHAR(255) NULL,
  `display_order` INT NOT NULL,
  `content_note` TEXT NULL,
  UNIQUE KEY `uq_AnnouncementWebContent` (`announcement_id`, `content_key`),
  CONSTRAINT `fk_AnnouncementWebContent_announcement_id` FOREIGN KEY (`announcement_id`) REFERENCES `Announcement` (`announcement_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `Feedback` (
  `feedback_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `feedback_category_id` INT NOT NULL,
  `feedback_name` VARCHAR(150) NULL,
  `feedback_email` VARCHAR(254) NULL,
  `feedback_message` TEXT NOT NULL,
  `response_request` BOOLEAN NOT NULL DEFAULT FALSE,
  `submitted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `feedback_status_id` INT NOT NULL DEFAULT 1,
  `read_at` DATETIME NULL,
  `resolved_at` DATETIME NULL,
  `feedback_resolution` TEXT NULL,
  `feedback_note` TEXT NULL,
  CONSTRAINT `fk_Feedback_feedback_category_id` FOREIGN KEY (`feedback_category_id`) REFERENCES `FeedbackCategory` (`feedback_category_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_Feedback_feedback_status_id` FOREIGN KEY (`feedback_status_id`) REFERENCES `FeedbackStatus` (`feedback_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `User` (
  `user_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `role_id` INT NOT NULL DEFAULT 1,
  `user_first_name` VARCHAR(100) NOT NULL,
  `user_middle_name` VARCHAR(100) NULL,
  `user_surname` VARCHAR(100) NOT NULL,
  `user_email` VARCHAR(254) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_address` VARCHAR(255) NULL,
  `user_phone_number` VARCHAR(30) NULL,
  `user_gender` VARCHAR(50) NULL,
  `user_date_of_birth` DATE NULL,
  `preferred_contact_method` VARCHAR(20) NULL,
  `user_note` TEXT NULL,
  CONSTRAINT `fk_User_role_id` FOREIGN KEY (`role_id`) REFERENCES `Role` (`role_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `Booking` (
  `booking_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `service_session_id` INT NOT NULL,
  `user_id` INT NULL,
  `booking_first_name` VARCHAR(100) NOT NULL,
  `booking_surname` VARCHAR(100) NOT NULL,
  `booking_email` VARCHAR(254) NOT NULL,
  `booked_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `booking_status_id` INT NOT NULL DEFAULT 1,
  `booking_note` TEXT NULL,
  KEY `idx_booking_session_status` (`service_session_id`, `booking_status_id`),
  CONSTRAINT `fk_Booking_service_session_id` FOREIGN KEY (`service_session_id`) REFERENCES `ServiceSession` (`service_session_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_Booking_user_id` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_Booking_booking_status_id` FOREIGN KEY (`booking_status_id`) REFERENCES `BookingStatus` (`booking_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `Facility` (
  `facility_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `facility_name` VARCHAR(150) NOT NULL UNIQUE,
  `facility_desc` TEXT NULL,
  `facility_location` VARCHAR(255) NOT NULL,
  `facility_capacity` INT NOT NULL,
  `facility_note` TEXT NULL,
  `active_status_id` INT NOT NULL DEFAULT 1,
  CONSTRAINT `fk_Facility_active_status_id` FOREIGN KEY (`active_status_id`) REFERENCES `ActiveStatus` (`active_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `FacilitySession` (
  `facility_session_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `facility_id` INT NOT NULL,
  `session_start_at` DATETIME NOT NULL,
  `session_end_at` DATETIME NOT NULL,
  `session_status_id` INT NOT NULL DEFAULT 1,
  `session_note` TEXT NULL,
  UNIQUE KEY `uq_FacilitySession` (`facility_id`, `session_start_at`),
  CONSTRAINT `fk_FacilitySession_facility_id` FOREIGN KEY (`facility_id`) REFERENCES `Facility` (`facility_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_FacilitySession_session_status_id` FOREIGN KEY (`session_status_id`) REFERENCES `SessionStatus` (`session_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `FacilityBooking` (
  `facility_booking_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `facility_session_id` INT NOT NULL,
  `user_id` INT NULL,
  `booking_first_name` VARCHAR(100) NOT NULL,
  `booking_surname` VARCHAR(100) NOT NULL,
  `booking_email` VARCHAR(254) NOT NULL,
  `booked_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `booking_status_id` INT NOT NULL DEFAULT 1,
  `booking_note` TEXT NULL,
  KEY `idx_facility_booking_session_status` (`facility_session_id`, `booking_status_id`),
  CONSTRAINT `fk_FacilityBooking_facility_session_id` FOREIGN KEY (`facility_session_id`) REFERENCES `FacilitySession` (`facility_session_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_FacilityBooking_user_id` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_FacilityBooking_booking_status_id` FOREIGN KEY (`booking_status_id`) REFERENCES `BookingStatus` (`booking_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `UserSession` (
  `user_session_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `session_token_hash` VARCHAR(64) NOT NULL UNIQUE,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` DATETIME NOT NULL,
  `revoked_at` DATETIME NULL,
  `user_session_note` TEXT NULL,
  CONSTRAINT `fk_UserSession_user_id` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `AuditLog` (
  `audit_log_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `user_session_id` INT NULL,
  `event_type` VARCHAR(50) NOT NULL,
  `event_result` VARCHAR(20) NOT NULL,
  `occurred_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `audit_log_note` TEXT NULL,
  CONSTRAINT `fk_AuditLog_user_id` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_AuditLog_user_session_id` FOREIGN KEY (`user_session_id`) REFERENCES `UserSession` (`user_session_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ActiveStatus` (`active_status_id`, `active_status_name`, `active_status_desc`)
SELECT 1, 'Active', 'Enabled and available for use.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ActiveStatus` WHERE `active_status_id` = 1);

INSERT INTO `ActiveStatus` (`active_status_id`, `active_status_name`, `active_status_desc`)
SELECT 2, 'Inactive', 'Disabled but retained for historical data.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `ActiveStatus` WHERE `active_status_id` = 2);

INSERT INTO `SessionStatus` (`session_status_id`, `session_status_name`, `session_status_desc`)
SELECT 1, 'Scheduled', 'Planned to take place.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `SessionStatus` WHERE `session_status_id` = 1);

INSERT INTO `SessionStatus` (`session_status_id`, `session_status_name`, `session_status_desc`)
SELECT 2, 'Cancelled', 'Cancelled and will not take place.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `SessionStatus` WHERE `session_status_id` = 2);

INSERT INTO `SessionStatus` (`session_status_id`, `session_status_name`, `session_status_desc`)
SELECT 3, 'Completed', 'The session has finished.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `SessionStatus` WHERE `session_status_id` = 3);

INSERT INTO `AnnouncementStatus` (`announcement_status_id`, `announcement_status_name`, `announcement_status_desc`)
SELECT 1, 'Draft', 'Being prepared and not publicly displayed.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `AnnouncementStatus` WHERE `announcement_status_id` = 1);

INSERT INTO `AnnouncementStatus` (`announcement_status_id`, `announcement_status_name`, `announcement_status_desc`)
SELECT 2, 'Published', 'Approved for display from its publication date.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `AnnouncementStatus` WHERE `announcement_status_id` = 2);

INSERT INTO `AnnouncementStatus` (`announcement_status_id`, `announcement_status_name`, `announcement_status_desc`)
SELECT 3, 'Archived', 'Retained and excluded from current public lists.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `AnnouncementStatus` WHERE `announcement_status_id` = 3);

INSERT INTO `FeedbackCategory` (`feedback_category_id`, `feedback_category_name`, `feedback_category_desc`)
SELECT 1, 'Complaint', 'Dissatisfaction with a service or experience.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `FeedbackCategory` WHERE `feedback_category_id` = 1);

INSERT INTO `FeedbackCategory` (`feedback_category_id`, `feedback_category_name`, `feedback_category_desc`)
SELECT 2, 'Suggestion', 'An improvement or new idea.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `FeedbackCategory` WHERE `feedback_category_id` = 2);

INSERT INTO `FeedbackCategory` (`feedback_category_id`, `feedback_category_name`, `feedback_category_desc`)
SELECT 3, 'Compliment', 'Appreciation or recognition of good service.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `FeedbackCategory` WHERE `feedback_category_id` = 3);

INSERT INTO `FeedbackCategory` (`feedback_category_id`, `feedback_category_name`, `feedback_category_desc`)
SELECT 4, 'Enquiry', 'A request for information.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `FeedbackCategory` WHERE `feedback_category_id` = 4);

INSERT INTO `FeedbackCategory` (`feedback_category_id`, `feedback_category_name`, `feedback_category_desc`)
SELECT 5, 'Other', 'Feedback outside the other categories.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `FeedbackCategory` WHERE `feedback_category_id` = 5);

INSERT INTO `FeedbackStatus` (`feedback_status_id`, `feedback_status_name`, `feedback_status_desc`)
SELECT 1, 'New', 'Received and not yet processed.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `FeedbackStatus` WHERE `feedback_status_id` = 1);

INSERT INTO `FeedbackStatus` (`feedback_status_id`, `feedback_status_name`, `feedback_status_desc`)
SELECT 2, 'In Progress', 'Being reviewed or addressed.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `FeedbackStatus` WHERE `feedback_status_id` = 2);

INSERT INTO `FeedbackStatus` (`feedback_status_id`, `feedback_status_name`, `feedback_status_desc`)
SELECT 3, 'Resolved', 'Processing complete with a resolution.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `FeedbackStatus` WHERE `feedback_status_id` = 3);

INSERT INTO `FeedbackStatus` (`feedback_status_id`, `feedback_status_name`, `feedback_status_desc`)
SELECT 4, 'Closed', 'No further action required.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `FeedbackStatus` WHERE `feedback_status_id` = 4);

INSERT INTO `BookingStatus` (`booking_status_id`, `booking_status_name`, `booking_status_desc`)
SELECT 1, 'Pending', 'Submitted and awaiting confirmation.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `BookingStatus` WHERE `booking_status_id` = 1);

INSERT INTO `BookingStatus` (`booking_status_id`, `booking_status_name`, `booking_status_desc`)
SELECT 2, 'Confirmed', 'Confirmed reservation.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `BookingStatus` WHERE `booking_status_id` = 2);

INSERT INTO `BookingStatus` (`booking_status_id`, `booking_status_name`, `booking_status_desc`)
SELECT 3, 'Cancelled', 'Cancelled and no longer reserves a place or slot.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `BookingStatus` WHERE `booking_status_id` = 3);

INSERT INTO `Role` (`role_id`, `role_name`, `role_desc`)
SELECT 1, 'general_user', 'Manages own account and makes bookings.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `Role` WHERE `role_id` = 1);

INSERT INTO `Role` (`role_id`, `role_name`, `role_desc`)
SELECT 2, 'admin', 'Manages website data and administrative functions.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `Role` WHERE `role_id` = 2);
