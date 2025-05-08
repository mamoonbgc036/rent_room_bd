/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE TABLE `agreement_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `agreement_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `deposit` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `agreement_details_user_id_foreign` (`user_id`),
  CONSTRAINT `agreement_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `amenities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `amenity_type_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `amenities_amenity_type_id_foreign` (`amenity_type_id`),
  KEY `amenities_user_id_foreign` (`user_id`),
  CONSTRAINT `amenities_amenity_type_id_foreign` FOREIGN KEY (`amenity_type_id`) REFERENCES `amenity_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `amenities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `amenity_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `amenity_types_user_id_foreign` (`user_id`),
  CONSTRAINT `amenity_types_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `areas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `country_id` bigint unsigned NOT NULL,
  `city_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `areas_country_id_foreign` (`country_id`),
  KEY `areas_city_id_foreign` (`city_id`),
  CONSTRAINT `areas_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `areas_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bank_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_details_user_id_foreign` (`user_id`),
  CONSTRAINT `bank_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `booking_amenities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `amenity_id` bigint unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_amenities_booking_id_foreign` (`booking_id`),
  KEY `booking_amenities_amenity_id_foreign` (`amenity_id`),
  CONSTRAINT `booking_amenities_amenity_id_foreign` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_amenities_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `booking_maintains` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `maintain_id` bigint unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_maintains_booking_id_foreign` (`booking_id`),
  KEY `booking_maintains_maintain_id_foreign` (`maintain_id`),
  CONSTRAINT `booking_maintains_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_maintains_maintain_id_foreign` FOREIGN KEY (`maintain_id`) REFERENCES `maintains` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `booking_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned DEFAULT NULL,
  `milestone_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `milestone_number` int DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_booking_fee` tinyint(1) NOT NULL DEFAULT '0',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_payments_booking_id_foreign` (`booking_id`),
  CONSTRAINT `booking_payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `booking_room_prices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `room_id` bigint unsigned NOT NULL,
  `price_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fixed_price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `booking_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_room_prices_booking_id_foreign` (`booking_id`),
  KEY `booking_room_prices_room_id_foreign` (`room_id`),
  CONSTRAINT `booking_room_prices_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_room_prices_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `package_id` bigint unsigned NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `number_of_days` int NOT NULL,
  `price_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_milestones` int DEFAULT NULL,
  `milestone_amount` decimal(10,2) DEFAULT NULL,
  `milestone_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `price` decimal(10,2) NOT NULL,
  `booking_price` decimal(10,2) NOT NULL,
  `payment_option` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_paid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `room_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `auto_renewal` tinyint(1) NOT NULL DEFAULT '0',
  `renewal_period_days` int DEFAULT NULL,
  `next_renewal_date` timestamp NULL DEFAULT NULL,
  `last_renewal_date` timestamp NULL DEFAULT NULL,
  `renewal_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookings_user_id_foreign` (`user_id`),
  KEY `bookings_package_id_foreign` (`package_id`),
  CONSTRAINT `bookings_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `country_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cities_country_id_foreign` (`country_id`),
  CONSTRAINT `cities_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `entire_properties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `package_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entire_properties_user_id_foreign` (`user_id`),
  KEY `entire_properties_package_id_foreign` (`package_id`),
  CONSTRAINT `entire_properties_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entire_properties_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `footer_section_4` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `footer_id` bigint unsigned NOT NULL,
  `section4_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_link_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_icon_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footer_section_4_footer_id_foreign` (`footer_id`),
  CONSTRAINT `footer_section_4_footer_id_foreign` FOREIGN KEY (`footer_id`) REFERENCES `footers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `footer_section_fours` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `footer_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footer_section_fours_footer_id_foreign` (`footer_id`),
  CONSTRAINT `footer_section_fours_footer_id_foreign` FOREIGN KEY (`footer_id`) REFERENCES `footers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `footer_section_threes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `footer_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footer_section_threes_footer_id_foreign` (`footer_id`),
  CONSTRAINT `footer_section_threes_footer_id_foreign` FOREIGN KEY (`footer_id`) REFERENCES `footers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `footer_section_twos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `footer_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footer_section_twos_footer_id_foreign` (`footer_id`),
  CONSTRAINT `footer_section_twos_footer_id_foreign` FOREIGN KEY (`footer_id`) REFERENCES `footers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `footers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `footer_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terms_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terms_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privacy_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privacy_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rights_reserves_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `headers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hero_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `background_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_small` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_big` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `home_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `section_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `home_data_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `home_data_id` bigint unsigned NOT NULL,
  `item_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_des` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `home_data_items_home_data_id_foreign` (`home_data_id`),
  CONSTRAINT `home_data_items_home_data_id_foreign` FOREIGN KEY (`home_data_id`) REFERENCES `home_data` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `maintain_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maintain_types_user_id_foreign` (`user_id`),
  CONSTRAINT `maintain_types_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `maintains` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `maintain_type_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maintains_maintain_type_id_foreign` (`maintain_type_id`),
  KEY `maintains_user_id_foreign` (`user_id`),
  CONSTRAINT `maintains_maintain_type_id_foreign` FOREIGN KEY (`maintain_type_id`) REFERENCES `maintain_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `maintains_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint unsigned NOT NULL,
  `recipient_id` bigint unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_recipient_id_foreign` (`recipient_id`),
  CONSTRAINT `messages_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `package_amenities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `package_id` bigint unsigned NOT NULL,
  `amenity_id` bigint unsigned NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `package_amenities_package_id_foreign` (`package_id`),
  KEY `package_amenities_amenity_id_foreign` (`amenity_id`),
  KEY `package_amenities_user_id_foreign` (`user_id`),
  CONSTRAINT `package_amenities_amenity_id_foreign` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_amenities_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_amenities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `package_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `package_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `package_documents_package_id_foreign` (`package_id`),
  CONSTRAINT `package_documents_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `package_instructions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `package_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `package_instructions_package_id_foreign` (`package_id`),
  KEY `package_instructions_user_id_foreign` (`user_id`),
  CONSTRAINT `package_instructions_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_instructions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `package_maintains` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `package_id` bigint unsigned NOT NULL,
  `maintain_id` bigint unsigned NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `package_maintains_package_id_foreign` (`package_id`),
  KEY `package_maintains_maintain_id_foreign` (`maintain_id`),
  KEY `package_maintains_user_id_foreign` (`user_id`),
  CONSTRAINT `package_maintains_maintain_id_foreign` FOREIGN KEY (`maintain_id`) REFERENCES `maintains` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_maintains_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_maintains_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `country_id` bigint unsigned NOT NULL,
  `city_id` bigint unsigned NOT NULL,
  `area_id` bigint unsigned NOT NULL,
  `property_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `map_link` longtext COLLATE utf8mb4_unicode_ci,
  `number_of_rooms` int NOT NULL,
  `number_of_kitchens` int NOT NULL,
  `common_bathrooms` int NOT NULL,
  `seating` int NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `video_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `expiration_date` date DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `packages_country_id_foreign` (`country_id`),
  KEY `packages_city_id_foreign` (`city_id`),
  KEY `packages_area_id_foreign` (`area_id`),
  KEY `packages_property_id_foreign` (`property_id`),
  KEY `packages_user_id_foreign` (`user_id`),
  KEY `packages_assigned_to_foreign` (`assigned_to`),
  KEY `packages_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `packages_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `packages_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`),
  CONSTRAINT `packages_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`),
  CONSTRAINT `packages_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `packages_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `packages_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `packages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `partner_terms_conditions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payment_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `booking_id` bigint unsigned NOT NULL,
  `booking_payment_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_links_unique_id_unique` (`unique_id`),
  KEY `payment_links_user_id_foreign` (`user_id`),
  KEY `payment_links_booking_id_foreign` (`booking_id`),
  KEY `payment_links_booking_payment_id_foreign` (`booking_payment_id`),
  CONSTRAINT `payment_links_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_links_booking_payment_id_foreign` FOREIGN KEY (`booking_payment_id`) REFERENCES `booking_payments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_links_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `booking_payment_id` bigint unsigned DEFAULT NULL,
  `payment_method` enum('cash','card','bank_transfer','Payment Link') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_type` enum('booking','rent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'booking',
  PRIMARY KEY (`id`),
  KEY `payments_booking_id_foreign` (`booking_id`),
  KEY `payments_booking_payment_id_foreign` (`booking_payment_id`),
  CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_booking_payment_id_foreign` FOREIGN KEY (`booking_payment_id`) REFERENCES `booking_payments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `package_id` bigint unsigned NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `photos_package_id_foreign` (`package_id`),
  KEY `photos_user_id_foreign` (`user_id`),
  CONSTRAINT `photos_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `photos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `privacy_policies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `properties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `country_id` bigint unsigned NOT NULL,
  `city_id` bigint unsigned NOT NULL,
  `property_type_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `properties_country_id_foreign` (`country_id`),
  KEY `properties_city_id_foreign` (`city_id`),
  KEY `properties_property_type_id_foreign` (`property_type_id`),
  KEY `properties_user_id_foreign` (`user_id`),
  CONSTRAINT `properties_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `properties_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `properties_property_type_id_foreign` FOREIGN KEY (`property_type_id`) REFERENCES `property_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `properties_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `property_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_types_user_id_foreign` (`user_id`),
  CONSTRAINT `property_types_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `room_prices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `room_id` bigint unsigned DEFAULT NULL,
  `type` enum('Day','Week','Month') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fixed_price` decimal(8,2) NOT NULL,
  `discount_price` decimal(8,2) DEFAULT NULL,
  `booking_price` decimal(8,2) NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `entire_property_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `room_prices_room_id_foreign` (`room_id`),
  KEY `room_prices_user_id_foreign` (`user_id`),
  KEY `room_prices_entire_property_id_foreign` (`entire_property_id`),
  CONSTRAINT `room_prices_entire_property_id_foreign` FOREIGN KEY (`entire_property_id`) REFERENCES `entire_properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_prices_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_prices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `package_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_of_beds` int NOT NULL,
  `number_of_bathrooms` int NOT NULL,
  `day_deposit` decimal(8,2) DEFAULT NULL,
  `weekly_deposit` decimal(8,2) DEFAULT NULL,
  `monthly_deposit` decimal(8,2) DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rooms_package_id_foreign` (`package_id`),
  KEY `rooms_user_id_foreign` (`user_id`),
  CONSTRAINT `rooms_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rooms_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `social_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `footer_section_four_id` bigint unsigned NOT NULL,
  `icon_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `social_links_footer_section_four_id_foreign` (`footer_section_four_id`),
  CONSTRAINT `social_links_footer_section_four_id_foreign` FOREIGN KEY (`footer_section_four_id`) REFERENCES `footer_section_fours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `terms_and_privacy` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `terms_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terms_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privacy_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privacy_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rights_reserves_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `terms_conditions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupied_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `package` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `booking_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('Pending','Paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `package_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `security_amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `stay_status` enum('staying','want_to') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `package_id` bigint unsigned DEFAULT NULL,
  `security_payment_status` enum('Pending','Paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  KEY `user_details_user_id_foreign` (`user_id`),
  KEY `user_details_package_id_foreign` (`package_id`),
  CONSTRAINT `user_details_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `person_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nid_or_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payslip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_card` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_documents_user_id_foreign` (`user_id`),
  CONSTRAINT `user_documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `partner_bank_details` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `photo_id_proof_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_id_proof_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_proof_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_proof_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_type_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_path_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_type_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_path_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_type_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_path_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_type_4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_path_4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `amenities` (`id`, `amenity_type_id`, `name`, `user_id`, `created_at`, `updated_at`) VALUES
(5, 7, 'Free Wife ', 3, '2024-08-05 03:37:43', '2024-08-05 03:37:43');


INSERT INTO `amenity_types` (`id`, `type`, `user_id`, `created_at`, `updated_at`) VALUES
(7, 'Internet ', 3, '2024-08-05 03:36:59', '2024-08-05 03:37:28');
INSERT INTO `amenity_types` (`id`, `type`, `user_id`, `created_at`, `updated_at`) VALUES
(8, 'Cleaning ', 3, '2024-08-05 03:51:55', '2024-08-05 03:51:55');


INSERT INTO `areas` (`id`, `country_id`, `city_id`, `name`, `photo`, `created_at`, `updated_at`) VALUES
(4, 1, 3, 'Kollanyanpur ', 'photos/DkKm2YebrI5c2i43Zw6UTi2kZavibpLyGQyzfP4P.jpg', '2024-08-05 03:06:16', '2024-08-05 03:06:16');








INSERT INTO `booking_payments` (`id`, `booking_id`, `milestone_type`, `milestone_number`, `due_date`, `amount`, `payment_status`, `payment_method`, `transaction_reference`, `paid_at`, `created_at`, `updated_at`, `is_booking_fee`, `start_date`, `end_date`) VALUES
(1, 13, 'Booking Fee', 0, '2025-01-19', 750.00, 'pending', 'bank_transfer', NULL, NULL, '2025-01-07 04:51:08', '2025-01-07 04:51:08', 0, NULL, NULL);
INSERT INTO `booking_payments` (`id`, `booking_id`, `milestone_type`, `milestone_number`, `due_date`, `amount`, `payment_status`, `payment_method`, `transaction_reference`, `paid_at`, `created_at`, `updated_at`, `is_booking_fee`, `start_date`, `end_date`) VALUES
(2, 13, 'Month', 1, '2025-02-19', 3000.00, 'pending', 'bank_transfer', NULL, NULL, '2025-01-07 04:51:08', '2025-01-07 04:51:08', 0, NULL, NULL);
INSERT INTO `booking_payments` (`id`, `booking_id`, `milestone_type`, `milestone_number`, `due_date`, `amount`, `payment_status`, `payment_method`, `transaction_reference`, `paid_at`, `created_at`, `updated_at`, `is_booking_fee`, `start_date`, `end_date`) VALUES
(3, 14, 'Week', 1, '2025-01-06', 1600.00, 'pending', 'bank_transfer', 'jjjjjj', NULL, '2025-01-08 04:14:53', '2025-01-08 04:14:53', 0, NULL, NULL);
INSERT INTO `booking_payments` (`id`, `booking_id`, `milestone_type`, `milestone_number`, `due_date`, `amount`, `payment_status`, `payment_method`, `transaction_reference`, `paid_at`, `created_at`, `updated_at`, `is_booking_fee`, `start_date`, `end_date`) VALUES
(4, 14, 'Booking Fee', 2, '2025-01-06', 750.00, 'paid', 'bank_transfer', 'jjjjjj', '2025-01-10 03:43:31', '2025-01-08 04:14:53', '2025-01-10 03:43:31', 0, NULL, NULL),
(5, 15, 'Month', 1, '2025-01-18', 3000.00, 'paid', 'bank_transfer', 'dfghdfgh', '2025-02-17 18:27:29', '2025-01-10 03:37:43', '2025-02-17 18:27:29', 0, NULL, NULL),
(6, 15, 'Month', 2, '2025-02-18', 3000.00, 'pending', NULL, NULL, NULL, '2025-01-10 03:37:43', '2025-01-10 03:37:43', 0, NULL, NULL),
(7, 15, 'Booking Fee', 3, '2025-01-18', 750.00, 'paid', 'bank_transfer', 'fdhdgdfr', '2025-01-10 03:43:38', '2025-01-10 03:37:43', '2025-01-10 03:43:38', 0, NULL, NULL);

INSERT INTO `booking_room_prices` (`id`, `booking_id`, `room_id`, `price_type`, `fixed_price`, `discount_price`, `booking_price`, `created_at`, `updated_at`) VALUES
(1, 13, 19, 'Month', 4000.00, 3000.00, 2500.00, '2025-01-07 04:51:08', '2025-01-07 04:51:08');


INSERT INTO `bookings` (`id`, `user_id`, `package_id`, `from_date`, `to_date`, `number_of_days`, `price_type`, `total_milestones`, `milestone_amount`, `milestone_breakdown`, `price`, `booking_price`, `payment_option`, `total_amount`, `status`, `payment_status`, `created_at`, `updated_at`, `room_ids`, `auto_renewal`, `renewal_period_days`, `next_renewal_date`, `last_renewal_date`, `renewal_status`) VALUES
(13, 3, 23, '2025-01-19', '2025-02-18', 30, 'Month', NULL, NULL, NULL, 3000.00, 750.00, 'booking_only', 750.00, 'pending', 'cancelled', '2025-01-07 04:51:08', '2025-01-08 04:18:22', '\"[19]\"', 0, NULL, NULL, NULL, NULL);
INSERT INTO `bookings` (`id`, `user_id`, `package_id`, `from_date`, `to_date`, `number_of_days`, `price_type`, `total_milestones`, `milestone_amount`, `milestone_breakdown`, `price`, `booking_price`, `payment_option`, `total_amount`, `status`, `payment_status`, `created_at`, `updated_at`, `room_ids`, `auto_renewal`, `renewal_period_days`, `next_renewal_date`, `last_renewal_date`, `renewal_status`) VALUES
(14, 21, 23, '2025-01-06', '2025-01-17', 11, 'Day', NULL, NULL, NULL, 1600.00, 750.00, 'booking_only', 750.00, 'approved', 'partially_paid', '2025-01-08 04:14:53', '2025-02-17 18:26:55', '\"[19]\"', 0, NULL, NULL, NULL, NULL);
INSERT INTO `bookings` (`id`, `user_id`, `package_id`, `from_date`, `to_date`, `number_of_days`, `price_type`, `total_milestones`, `milestone_amount`, `milestone_breakdown`, `price`, `booking_price`, `payment_option`, `total_amount`, `status`, `payment_status`, `created_at`, `updated_at`, `room_ids`, `auto_renewal`, `renewal_period_days`, `next_renewal_date`, `last_renewal_date`, `renewal_status`) VALUES
(15, 21, 23, '2025-01-18', '2025-02-19', 32, 'Day', NULL, NULL, NULL, 6000.00, 750.00, 'booking_only', 750.00, 'approved', 'partially_paid', '2025-01-10 03:37:43', '2025-02-17 18:27:29', '\"[19]\"', 0, NULL, NULL, NULL, NULL);

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('1b6453892473a467d07372d45eb05abc2031647a', 'i:3;', 1721083114);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('1b6453892473a467d07372d45eb05abc2031647a:timer', 'i:1721083114;', 1721083114);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('77de68daecd823babbb58edb1c8e14d7106e83bb', 'i:1;', 1722796886);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('77de68daecd823babbb58edb1c8e14d7106e83bb:timer', 'i:1722796886;', 1722796886),
('b3f0c7f6bb763af1be91d9e74eabfeb199dc1f1f', 'i:2;', 1724827121),
('b3f0c7f6bb763af1be91d9e74eabfeb199dc1f1f:timer', 'i:1724827121;', 1724827121),
('da4b9237bacccdf19c0760cab7aec4a8359010b0', 'i:1;', 1722745315),
('da4b9237bacccdf19c0760cab7aec4a8359010b0:timer', 'i:1722745315;', 1722745315),
('spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:15:{i:0;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:12:\"package.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:1;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:12:\"package.show\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:2;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:13:\"package.setup\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:3;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:9:\"dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:4;i:2;i:5;}}i:4;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:14:\"package.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:5;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:7:\"earning\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:15:\"role.permission\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:7;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:7:\"booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:4:\"user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:7:\"package\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:10;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:13:\"site.settings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:11;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:11:\"my-packages\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:4;}}i:12;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:14:\"Package Create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:13;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:7:\"massage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:14;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:11:\"send-emails\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:4:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"Super Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:7:\"Partner\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:4:\"User\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}}}', 1739903202);



INSERT INTO `cities` (`id`, `country_id`, `name`, `photo`, `created_at`, `updated_at`) VALUES
(3, 1, 'Dhaka', 'photos/tsKhWFnpEJwWItIMRfsCJ0eTrmN6aRfG2XAqDhS7.jpg', '2024-08-05 03:00:51', '2024-08-05 03:07:36');


INSERT INTO `countries` (`id`, `name`, `symbol`, `currency`, `photo`, `created_at`, `updated_at`) VALUES
(1, 'Bangladesh', 'BDT', '৳', 'photos/B8OaFxJZn2x3fkAR4xOP2BiJyiQOVK99QQdCFzPR.jpg', '2024-08-04 14:21:00', '2024-08-04 14:21:00');








INSERT INTO `footer_section_fours` (`id`, `footer_id`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Follow Us', 'Follow Our Social Network to get the best Deal ', '2024-07-03 11:48:51', '2024-07-11 01:04:23');


INSERT INTO `footer_section_threes` (`id`, `footer_id`, `title`, `link`, `created_at`, `updated_at`) VALUES
(4, 1, 'User policy ', 'https://rentandrooms.com/privacy-policy', '2024-07-03 14:06:13', '2024-07-31 06:59:15');


INSERT INTO `footer_section_twos` (`id`, `footer_id`, `title`, `link`, `created_at`, `updated_at`) VALUES
(6, 1, 'T&C ', 'https://rentandrooms.com/terms-condition', '2024-07-03 14:06:15', '2024-07-17 10:23:54');
INSERT INTO `footer_section_twos` (`id`, `footer_id`, `title`, `link`, `created_at`, `updated_at`) VALUES
(7, 1, 'Policy ', 'https://rentandrooms.com/privacy-policy', '2024-07-03 14:06:15', '2024-07-17 10:23:54');


INSERT INTO `footers` (`id`, `footer_logo`, `address`, `email`, `contact_number`, `website`, `terms_title`, `terms_link`, `privacy_title`, `privacy_link`, `rights_reserves_text`, `created_at`, `updated_at`) VALUES
(1, 'logos/PiwIHlOKtJVWlm8IxQyx5IybOmxLNGoQevyff8fO.jpg', '2/11 Dreamland Housing, Kawler Bazar, Dhaka 1229', 'Info@rentandrooms.com', '01740951100', '01740951100', 'Terms & Condition', 'https://www.rentandromms.com/terms', 'Privacy Policy', 'https://www.rentandromms.com/policy', 'Copy right © 2024 RentsandRooms', '2024-07-03 14:03:29', '2024-08-05 02:56:14');


INSERT INTO `headers` (`id`, `logo`, `created_at`, `updated_at`) VALUES
(1, 'logos/XTvhUGyx1XJCeVniG5bYE9jsPes8XayDP4cOtfC9.jpg', '2024-07-03 08:40:02', '2024-07-11 00:55:11');


INSERT INTO `hero_sections` (`id`, `background_image`, `title_small`, `title_big`, `created_at`, `updated_at`) VALUES
(1, 'hero_images/hi4cdZg6x7aXQ4TzvWh9nAbsHyasBWch1lf3eGGP.jpg', ' Locate  Your Area ', 'Find  The Best  Place to Stay ', '2024-07-03 08:36:39', '2024-07-25 07:41:37');


INSERT INTO `home_data` (`id`, `section_title`, `created_at`, `updated_at`) VALUES
(1, 'Book your Best Place ', '2024-07-08 07:18:50', '2024-07-11 00:43:32');


INSERT INTO `home_data_items` (`id`, `home_data_id`, `item_image`, `item_title`, `item_des`, `created_at`, `updated_at`) VALUES
(1, 1, 'home_data/cTv8NCQjbVMopLGLIMjWXpzm9bS9tW6XHpeTBAHu.png', 'Find', 'Find your location to get the right place ', '2024-07-08 07:18:50', '2024-07-17 07:44:41');
INSERT INTO `home_data_items` (`id`, `home_data_id`, `item_image`, `item_title`, `item_des`, `created_at`, `updated_at`) VALUES
(2, 1, 'home_data/vgC1KjNV81kKrEsXVizQd3MGvjhzfCq0CMiDpbr5.png', 'Select', 'Select your place to get the best deal ', '2024-07-08 07:23:20', '2024-07-17 07:44:41');
INSERT INTO `home_data_items` (`id`, `home_data_id`, `item_image`, `item_title`, `item_des`, `created_at`, `updated_at`) VALUES
(3, 1, 'home_data/MzVmbqdyX9chY1qEzWzgTda9apDozHq0ZxPZS5VH.png', 'Confirm ', 'Confirm your booking to stay ', '2024-07-08 07:23:20', '2024-07-17 07:44:41');





INSERT INTO `maintain_types` (`id`, `type`, `user_id`, `created_at`, `updated_at`) VALUES
(9, 'Plumbing ', 3, '2024-08-05 03:36:27', '2024-08-05 03:36:27');
INSERT INTO `maintain_types` (`id`, `type`, `user_id`, `created_at`, `updated_at`) VALUES
(10, 'Handy Works ', 3, '2024-08-05 03:36:35', '2024-08-05 03:36:35');


INSERT INTO `maintains` (`id`, `maintain_type_id`, `name`, `photo`, `user_id`, `created_at`, `updated_at`) VALUES
(5, 10, 'Rapid  Handy Works ', NULL, 3, '2024-08-05 03:50:46', '2024-08-05 03:50:46');




INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(4, '2024_03_09_225738_create_entire_properties_table', 1),
(5, '2024_05_04_085346_create_permission_tables', 1),
(6, '2024_05_31_122519_create_countries_table', 1),
(7, '2024_05_31_122551_create_cities_table', 1),
(8, '2024_05_31_122627_create_areas_table', 1),
(9, '2024_05_31_122652_create_property_types_table', 1),
(10, '2024_05_31_122719_create_properties_table', 1),
(11, '2024_05_31_122745_create_maintain_types_table', 1),
(12, '2024_05_31_122813_create_maintains_table', 1),
(13, '2024_05_31_122840_create_amenity_types_table', 1),
(14, '2024_05_31_122909_create_amenities_table', 1),
(15, '2024_06_03_075503_create_packages_table', 1),
(16, '2024_06_03_075543_create_rooms_table', 1),
(17, '2024_06_03_075649_create_package_maintains_table', 1),
(18, '2024_06_03_075728_create_package_amenities_table', 1),
(19, '2024_06_03_075831_create_photos_table', 1),
(20, '2024_06_06_103923_create_room_prices_table', 1),
(21, '2024_06_10_002741_add_video_link_to_packages_table', 1),
(22, '2024_06_10_010842_add_package_id_to_entire_properties_table', 1),
(23, '2024_06_10_105212_add_phone_to_users_table', 1),
(24, '2024_06_20_042545_create_bookings_table', 1),
(25, '2024_06_20_042641_create_payments_table', 1),
(26, '2024_07_02_081135_create_headers_table', 2),
(27, '2024_07_02_081156_create_hero_sections_table', 2),
(28, '2024_07_02_081216_create_footers_table', 2),
(29, '2024_07_02_081316_create_footer_links_table', 2),
(30, '2024_07_02_081352_create_footer_section_4_table', 2),
(31, '2024_07_02_081446_create_terms_and_privacy_table', 2),
(32, '2024_07_02_081317_create_footer_links_table', 3),
(33, '2024_07_02_112310_create_footer_section_twos_table', 4),
(34, '2024_07_02_112407_create_footer_section_threes_table', 4),
(35, '2024_07_02_114405_create_footer_section_fours_table', 5),
(36, '2024_07_02_114531_create_social_links_table', 5),
(37, '2024_07_02_081217_create_footers_table', 6),
(38, '2024_07_07_164304_create_home_data_table', 7),
(39, '2024_07_07_170550_create_home_data_items_table', 7),
(40, '2024_07_14_185728_create_privacy_policies_table', 8),
(41, '2024_07_14_185730_create_terms_conditions_table', 8),
(42, '2024_07_15_172202_add_proof_fields_to_users_table', 9),
(43, '2024_07_31_160825_create_partner_terms_conditions_table', 10),
(44, '2024_08_01_205830_add_room_id_to_bookings_table', 11),
(45, '2024_06_20_042642_create_payments_table', 12),
(46, '2024_08_20_044403_add_status_and_expiration_date_to_packages_table', 12),
(47, '2024_08_25_200419_add_proof_columns_to_users_table', 12),
(48, '2024_08_29_161017_add_partner_bank_details_to_users_table', 12),
(49, '2024_09_06_160710_create_user_documents_table', 12),
(50, '2024_09_06_164223_create_agreement_details_table', 12),
(51, '2024_09_06_171709_create_bank_details_table', 12),
(52, '2024_09_08_115417_add_name_to_bank_details_table', 12),
(53, '2024_09_08_122353_create_user_details_table', 12),
(54, '2024_09_08_183818_add_package_price_to_user_details_table', 12),
(55, '2024_09_08_191638_add_deposit_to_agreement_details_table', 12),
(56, '2024_09_17_174707_add_entry_date_to_user_details_table', 12),
(57, '2024_09_18_060306_create_messages_table', 12),
(58, '2024_09_24_150752_add_stay_status_and_package_to_user_details_table', 12),
(59, '2024_09_26_092926_add_booking_type_to_user_details_table', 12),
(60, '2024_09_29_062053_add_duration_type_and_payment_status_to_user_details_table', 12),
(61, '2024_09_30_064033_add_security_payment_status_to_user_details_table', 12),
(62, '2024_09_30_112515_create_payment_links_table', 12),
(63, '2024_10_01_062401_add_payslip_and_student_card_to_user_documents_table', 12),
(64, '2024_12_03_123916_create_booking_amenities_table', 12),
(65, '2024_12_03_124117_create_booking_maintains_table', 12),
(66, '2024_12_03_124143_create_booking_room_prices_table', 12),
(67, '2024_12_11_172701_create_booking_payments_table', 12),
(68, '2024_12_11_172708_add_milestone_columns_to_bookings_table', 12),
(69, '2024_12_13_184336_add_booking_payment_id_to_payment_links_table', 12),
(70, '2024_12_18_104023_add_is_booking_fee_to_booking_payments_table', 12),
(71, '2024_12_18_110244_add_booking_payment_id_to_payments_table', 12),
(72, '2024_12_18_111715_update_payment_method_enum_in_payments_table', 12),
(73, '2025_01_19_145341_create_package_instructions_table', 13),
(74, '2025_01_19_173524_add_renewal_columns_to_bookings_table', 13),
(75, '2025_01_27_154041_add_assigned_to_to_packages_table', 13),
(76, '2025_02_06_082103_create_package_documents_table', 13),
(77, '2025_02_12_185148_add_dates_to_booking_payments_table', 13),
(78, '2025_02_16_133818_add_payment_type_to_payments_table', 13),
(79, '2025_02_17_171325_add_status_to_bookings_table', 13);



INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(5, 'App\\Models\\User', 1);
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 2);
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(5, 'App\\Models\\User', 3);
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(4, 'App\\Models\\User', 4),
(4, 'App\\Models\\User', 5),
(5, 'App\\Models\\User', 6),
(4, 'App\\Models\\User', 8),
(4, 'App\\Models\\User', 9),
(4, 'App\\Models\\User', 10),
(4, 'App\\Models\\User', 12),
(4, 'App\\Models\\User', 13),
(4, 'App\\Models\\User', 14),
(4, 'App\\Models\\User', 15),
(4, 'App\\Models\\User', 16),
(4, 'App\\Models\\User', 17),
(5, 'App\\Models\\User', 18),
(4, 'App\\Models\\User', 19),
(5, 'App\\Models\\User', 20),
(4, 'App\\Models\\User', 21);

INSERT INTO `package_amenities` (`id`, `package_id`, `amenity_id`, `price`, `is_paid`, `user_id`, `created_at`, `updated_at`) VALUES
(37, 23, 5, NULL, 0, 3, NULL, NULL);
INSERT INTO `package_amenities` (`id`, `package_id`, `amenity_id`, `price`, `is_paid`, `user_id`, `created_at`, `updated_at`) VALUES
(38, 23, 5, 51.00, 1, 3, NULL, NULL);






INSERT INTO `package_maintains` (`id`, `package_id`, `maintain_id`, `price`, `is_paid`, `user_id`, `created_at`, `updated_at`) VALUES
(36, 23, 5, NULL, 0, 3, NULL, NULL);
INSERT INTO `package_maintains` (`id`, `package_id`, `maintain_id`, `price`, `is_paid`, `user_id`, `created_at`, `updated_at`) VALUES
(37, 23, 5, 50.00, 1, 3, NULL, NULL);


INSERT INTO `packages` (`id`, `country_id`, `city_id`, `area_id`, `property_id`, `name`, `address`, `map_link`, `number_of_rooms`, `number_of_kitchens`, `common_bathrooms`, `seating`, `details`, `video_link`, `status`, `expiration_date`, `user_id`, `created_at`, `updated_at`, `assigned_to`, `assigned_by`, `assigned_at`) VALUES
(23, 1, 3, 4, 7, 'Student Room To Let ', '2/11 Kolllanpur, dhaka 1230', 'https://www.google.com/maps/place/138+Hartington+St,+Newcastle+upon+Tyne+NE4+6PS/@54.9712497,-1.6430224,17z/data=!4m15!1m8!3m7!1s0x487e77474cf40825:0xbab6788959ddf1e7!2s138+Hartington+St,+Newcastle+upon+Tyne+NE4+6PS!3b1!8m2!3d54.9712497!4d-1.6404421!16s%2Fg%2F11c177ggf0!3m5!1s0x487e77474cf40825:0xbab6788959ddf1e7!8m2!3d54.9712497!4d-1.6404421!16s%2Fg%2F11c177ggf0?authuser=0&entry=ttu', 3, 1, 1, 1, 'Its close to Bus station ', 'https://www.youtube.com/watch?v=oipS9ch7hvo', 'active', NULL, 3, '2024-08-05 04:40:48', '2024-08-05 04:40:48', NULL, NULL, NULL);




INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('superadmin@mail.com', '$2y$12$Lu/vlW0sHPV3vO8YcAQqPO3EHNbXAwmnzGj3gGA6wElQIngPXA5pW', '2024-06-22 22:58:38');


INSERT INTO `payment_links` (`id`, `unique_id`, `user_id`, `booking_id`, `booking_payment_id`, `amount`, `status`, `created_at`, `updated_at`) VALUES
(1, '2cf48bfe-a208-4959-b863-e563f4a5e50a', 21, 14, 3, 1600.00, 'pending', '2025-01-10 03:42:45', '2025-01-10 03:42:45');
INSERT INTO `payment_links` (`id`, `unique_id`, `user_id`, `booking_id`, `booking_payment_id`, `amount`, `status`, `created_at`, `updated_at`) VALUES
(2, '844fd231-9cbd-442b-99d1-039961e3ba5b', 21, 14, 4, 750.00, 'completed', '2025-01-10 03:43:08', '2025-01-10 03:43:31');
INSERT INTO `payment_links` (`id`, `unique_id`, `user_id`, `booking_id`, `booking_payment_id`, `amount`, `status`, `created_at`, `updated_at`) VALUES
(3, '043bd979-0b5d-4900-a909-3d6abe4d0b5d', 21, 15, 5, 3000.00, 'completed', '2025-02-17 18:27:18', '2025-02-17 18:27:29');

INSERT INTO `payments` (`id`, `booking_id`, `booking_payment_id`, `payment_method`, `amount`, `transaction_id`, `status`, `created_at`, `updated_at`, `payment_type`) VALUES
(1, 13, NULL, 'bank_transfer', 750.00, 'fdfdssds', 'pending', '2025-01-07 04:51:08', '2025-01-07 04:51:08', 'booking');
INSERT INTO `payments` (`id`, `booking_id`, `booking_payment_id`, `payment_method`, `amount`, `transaction_id`, `status`, `created_at`, `updated_at`, `payment_type`) VALUES
(2, 14, NULL, 'bank_transfer', 750.00, 'jjjjjj', 'Paid', '2025-01-08 04:14:53', '2025-01-10 03:43:31', 'booking');
INSERT INTO `payments` (`id`, `booking_id`, `booking_payment_id`, `payment_method`, `amount`, `transaction_id`, `status`, `created_at`, `updated_at`, `payment_type`) VALUES
(3, 15, NULL, 'bank_transfer', 750.00, 'fdhdgdfr', 'Paid', '2025-01-10 03:37:43', '2025-01-10 03:43:38', 'booking');
INSERT INTO `payments` (`id`, `booking_id`, `booking_payment_id`, `payment_method`, `amount`, `transaction_id`, `status`, `created_at`, `updated_at`, `payment_type`) VALUES
(4, 15, 5, 'bank_transfer', 3000.00, 'dfghdfgh', 'Paid', '2025-02-17 18:27:24', '2025-02-17 18:27:29', 'rent');

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(2, 'package.edit', 'web', '2024-06-21 23:11:34', '2024-06-21 23:11:34');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(3, 'package.show', 'web', '2024-06-21 23:11:34', '2024-06-21 23:11:34');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(9, 'package.setup', 'web', '2024-06-21 23:11:34', '2024-06-21 23:11:34');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(10, 'dashboard', 'web', '2024-06-21 23:11:34', '2024-06-21 23:11:34'),
(14, 'package.delete', 'web', '2024-06-22 23:03:52', '2024-06-22 23:03:52'),
(17, 'earning', 'web', '2024-06-22 23:03:52', '2024-06-22 23:03:52'),
(18, 'role.permission', 'web', '2024-06-22 23:03:52', '2024-06-22 23:03:52'),
(19, 'booking', 'web', '2024-06-23 21:36:39', '2024-06-23 21:36:39'),
(20, 'user', 'web', '2024-06-23 21:36:39', '2024-06-23 21:36:39'),
(21, 'package', 'web', '2024-06-23 21:36:39', '2024-06-23 21:36:39'),
(22, 'site.settings', 'web', '2024-07-03 14:32:43', '2024-07-03 14:32:43'),
(24, 'my-packages', 'web', '2024-07-09 07:44:58', '2024-07-09 07:44:58'),
(26, 'Package Create', 'web', '2024-07-31 09:20:50', '2024-07-31 09:20:50'),
(27, 'massage', 'web', '2025-01-08 17:31:17', '2025-01-08 17:31:17'),
(28, 'send-emails', 'web', '2025-01-08 17:33:53', '2025-01-08 17:33:53');

INSERT INTO `photos` (`id`, `package_id`, `url`, `created_at`, `updated_at`, `user_id`) VALUES
(64, 23, 'photos/61T6DYXxBUotg4ZRRAgJPZbWbVCVktG2Lqpa3No6.jpg', '2024-08-05 04:40:49', '2024-08-05 04:40:49', 3);
INSERT INTO `photos` (`id`, `package_id`, `url`, `created_at`, `updated_at`, `user_id`) VALUES
(65, 23, 'photos/zDMv382tklhhoKfeLwWmWOu72TeWLH9KU2UuTkOO.jpg', '2024-08-05 04:40:49', '2024-08-05 04:40:49', 3);
INSERT INTO `photos` (`id`, `package_id`, `url`, `created_at`, `updated_at`, `user_id`) VALUES
(66, 23, 'photos/PVTcd9qv5mTHsjjXZCYmnLDPGEyjGP8lBJYnnU5X.jpg', '2024-08-05 04:40:49', '2024-08-05 04:40:49', 3);
INSERT INTO `photos` (`id`, `package_id`, `url`, `created_at`, `updated_at`, `user_id`) VALUES
(67, 23, 'photos/SSHjFAsmLhTOnnJ2EJLDqa73YgXUsVcmuW07trXQ.jpg', '2024-08-05 04:40:49', '2024-08-05 04:40:49', 3);

INSERT INTO `privacy_policies` (`id`, `title`, `content`, `created_at`, `updated_at`) VALUES
(1, 'Privacy Policy ', 'At Rents Room Service, we prioritize your privacy by collecting and using personal information, such as your name, contact details, and payment information, solely for processing bookings and improving our services. We may also gather usage data through cookies to enhance your experience. Your information may be shared with trusted service providers for operational purposes or as required by law. We implement reasonable security measures to protect your data, but cannot guarantee absolute security. You have rights to access, correct, or delete your personal information, and any updates to this policy will be posted on our website. For questions, please contact us at [Your Email Address] or [Your Phone Number].', '2024-07-15 10:05:48', '2024-07-15 10:05:48');


INSERT INTO `properties` (`id`, `country_id`, `city_id`, `property_type_id`, `name`, `photo`, `user_id`, `created_at`, `updated_at`) VALUES
(7, 1, 3, 2, 'Kollanpur Apartment ', NULL, 3, '2024-08-05 03:35:40', '2024-08-05 03:38:37');


INSERT INTO `property_types` (`id`, `type`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'House ', 3, '2024-06-23 20:32:50', '2024-06-23 20:32:50');
INSERT INTO `property_types` (`id`, `type`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 'Apartment ', 3, '2024-06-23 20:33:06', '2024-06-23 20:33:06');
INSERT INTO `property_types` (`id`, `type`, `user_id`, `created_at`, `updated_at`) VALUES
(4, 'Banglow ', 2, '2024-07-11 01:20:04', '2024-07-11 01:20:04');

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(2, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(3, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(9, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(10, 1),
(14, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(24, 1),
(26, 1),
(27, 1),
(28, 1),
(10, 4),
(24, 4),
(2, 5),
(3, 5),
(9, 5),
(10, 5),
(14, 5),
(21, 5),
(26, 5),
(22, 6);

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2024-06-21 23:11:34', '2024-06-21 23:11:34');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(4, 'User', 'web', '2024-06-21 23:11:34', '2024-06-21 23:11:34');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(5, 'Partner', 'web', '2024-06-23 19:45:54', '2024-06-23 19:45:54');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(6, 'Admin', 'web', '2024-06-23 21:36:39', '2024-06-23 21:36:39');

INSERT INTO `room_prices` (`id`, `room_id`, `type`, `fixed_price`, `discount_price`, `booking_price`, `user_id`, `entire_property_id`, `created_at`, `updated_at`) VALUES
(30, 19, 'Week', 1000.00, 800.00, 750.00, 3, NULL, '2024-08-05 04:40:48', '2024-08-05 04:40:48');
INSERT INTO `room_prices` (`id`, `room_id`, `type`, `fixed_price`, `discount_price`, `booking_price`, `user_id`, `entire_property_id`, `created_at`, `updated_at`) VALUES
(31, 19, 'Month', 4000.00, 3000.00, 2500.00, 3, NULL, '2024-08-05 04:40:48', '2024-08-05 04:40:48');


INSERT INTO `rooms` (`id`, `package_id`, `name`, `number_of_beds`, `number_of_bathrooms`, `day_deposit`, `weekly_deposit`, `monthly_deposit`, `user_id`, `created_at`, `updated_at`) VALUES
(19, 23, 'Double Room', 1, 1, NULL, NULL, NULL, 3, '2024-08-05 04:40:48', '2024-08-05 04:40:48');


INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('wFC5SLBE9ZvXnwjuYZ0dtLhwh1OaqUKEzPKwr8et', NULL, '220.152.115.135', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaUo1VHlEc2k2djJkV3VzeEhlNkJCZ3lFNElFazhYcEcwcnFuQXlNdCI7czoxODoiZmxhc2hlcjo6ZW52ZWxvcGVzIjthOjA6e31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozNToiaHR0cHM6Ly9yZW50YW5kcm9vbXMuY29tL3BhY2thZ2UvMjMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjE2OiJwYWNrYWdlXzIzX3ZpZXdzIjtpOjE7fQ==', 1739815783);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('YlgbG9Wqmxzo4Tj4tYz725vqYK7FKooaziQWu1U5', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoialpkSG9ISmVSdEdib1RSdklyN2VDN0VyWEZWYm00MWQ2T21UdHJHSyI7czoxODoiZmxhc2hlcjo6ZW52ZWxvcGVzIjthOjA6e31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2Rhc2hib2FyZC9tYWluIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxNjoicGFja2FnZV8yM192aWV3cyI7aTozO3M6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1739816958);


INSERT INTO `social_links` (`id`, `footer_section_four_id`, `icon_class`, `link`, `created_at`, `updated_at`) VALUES
(21, 1, 'fab fa-facebook-f', 'https://www.facebook.com', '2024-07-11 01:04:23', '2024-07-11 01:04:23');
INSERT INTO `social_links` (`id`, `footer_section_four_id`, `icon_class`, `link`, `created_at`, `updated_at`) VALUES
(22, 1, 'fab fa-twitter', 'https://www.twitter.com', '2024-07-11 01:04:23', '2024-07-11 01:04:23');
INSERT INTO `social_links` (`id`, `footer_section_four_id`, `icon_class`, `link`, `created_at`, `updated_at`) VALUES
(23, 1, 'fab fa-linkedin', 'https://www.twitter.com', '2024-07-11 01:04:23', '2024-07-11 01:04:23');
INSERT INTO `social_links` (`id`, `footer_section_four_id`, `icon_class`, `link`, `created_at`, `updated_at`) VALUES
(24, 1, 'fab fa-youtube', 'https://www.twitterm', '2024-07-11 01:04:23', '2024-07-11 01:04:23');



INSERT INTO `terms_conditions` (`id`, `title`, `content`, `created_at`, `updated_at`) VALUES
(1, 'Terms & Conditions', 'Welcome to Rents Room Service. By using our services, you agree to comply with and be bound by the following terms and conditions:\n\nBooking and Payment: All bookings must be made in advance through our website or customer service. Full payment is required at the time of booking. Accepted payment methods include credit/debit cards and other specified options.\n\nCancellation Policy: Cancellations made [insert cancellation period] before the scheduled service will receive a full refund. Cancellations made after this period may incur a fee.\n\nService Availability: We strive to provide timely service; however, we are not liable for delays due to unforeseen circumstances.\n\nUser Responsibilities: Users must provide accurate information when making a booking. You are responsible for maintaining the confidentiality of your account details.\n\nLimitation of Liability: Rents Room Service is not liable for any direct, indirect, incidental, or consequential damages arising from the use of our services.\n\nGoverning Law: These terms are governed by the laws of [Your State/Country]. Any disputes will be resolved in the courts of [Your Location].\n\nChanges to Terms: We reserve the right to modify these terms at any time. Continued use of our services constitutes acceptance of the updated terms.\n\nFor any questions, please contact us at [Your Email Address] or [Your Phone Number].', '2024-07-15 10:06:39', '2024-07-15 10:06:39');






INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `partner_bank_details`, `remember_token`, `created_at`, `updated_at`, `photo_id_proof_type`, `photo_id_proof_path`, `user_proof_type`, `user_proof_path`, `proof_type_1`, `proof_path_1`, `proof_type_2`, `proof_path_2`, `proof_type_3`, `proof_path_3`, `proof_type_4`, `proof_path_4`) VALUES
(2, 'Super Admin', 'superadmin@mail.com', NULL, NULL, '$2y$12$n5mJkozhqDKSBNtwM9B0i.AeuXwN3RCEdV8dbyY.zV.qUjOUuWeZW', NULL, 'zFRUqkKpGSjCQC7XE726rIgqKwWrEYODqJRgyU7AtU7Af2lzgDbPAMQxicSa', '2024-06-21 23:11:34', '2024-06-21 23:11:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `partner_bank_details`, `remember_token`, `created_at`, `updated_at`, `photo_id_proof_type`, `photo_id_proof_path`, `user_proof_type`, `user_proof_path`, `proof_type_1`, `proof_path_1`, `proof_type_2`, `proof_path_2`, `proof_type_3`, `proof_path_3`, `proof_type_4`, `proof_path_4`) VALUES
(3, 'Rent & Rooms', 'rentandrooms@gmail.com', NULL, NULL, '$2y$12$PYR4ubWlAVgkiKTXcwIBvumL8yjnNl7nSiUq99MK5e/ehGe8rNToW', NULL, NULL, '2024-06-23 19:50:15', '2024-06-23 19:50:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `partner_bank_details`, `remember_token`, `created_at`, `updated_at`, `photo_id_proof_type`, `photo_id_proof_path`, `user_proof_type`, `user_proof_path`, `proof_type_1`, `proof_path_1`, `proof_type_2`, `proof_path_2`, `proof_type_3`, `proof_path_3`, `proof_type_4`, `proof_path_4`) VALUES
(17, 'Rashel Mahmud', 'rashel.mahmud@yahoo.com', NULL, NULL, '$2y$12$eAX101Xf68eybzbduEtuROjEPs/.32tgZV2zq11vhrHbt/wKcWLkm', NULL, NULL, '2024-08-01 18:59:06', '2024-08-01 18:59:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `partner_bank_details`, `remember_token`, `created_at`, `updated_at`, `photo_id_proof_type`, `photo_id_proof_path`, `user_proof_type`, `user_proof_path`, `proof_type_1`, `proof_path_1`, `proof_type_2`, `proof_path_2`, `proof_type_3`, `proof_path_3`, `proof_type_4`, `proof_path_4`) VALUES
(19, 'Rash ahmed ', 'netsofnet@gmail.com', NULL, NULL, '$2y$12$Q60hlC9dEhCXoXHeEnhRP.cpRzUKIRuX0Ww7Gk0pkeqdKRiNL6nQ.', NULL, NULL, '2024-08-28 16:35:39', '2024-08-28 16:37:55', 'nid', 'proofs/tAExivU13cAs93NGbprolnGsoqMmjjdIn2HkN3V1.jpg', 'others', 'proofs/8i6jUV3CPvzA8p7SPPY6yWVtqpK9Qj6zgGv1JasP.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'Dark', 'darkh569@gmail.com', NULL, NULL, '$2y$12$qNkX00Hmap7m6crimLKP3OyulvzqHXLaTOLCJEfhNVOvYK2bhYx06', NULL, NULL, '2024-11-05 13:48:20', '2024-11-05 13:48:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'Shahed Hosen', 'shahedhosen01703@gmail.com', '01305166966', NULL, '$2y$12$220CyDjrKpeIyjMX.I1VaODtFIuqk8qOGQPVbgRVTNOU4FJ/.TZiy', NULL, NULL, '2025-01-07 04:55:44', '2025-01-07 04:55:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;