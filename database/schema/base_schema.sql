-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.27

/*!40103 SET @OLD_TIME_ZONE = @@TIME_ZONE */;
/*!40103 SET TIME_ZONE = '+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;
DROP TABLE IF EXISTS `addons`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addons`
(
    `id`                int(11)   NOT NULL AUTO_INCREMENT,
    `name`              varchar(255)       DEFAULT NULL,
    `unique_identifier` varchar(255)       DEFAULT NULL,
    `version`           varchar(255)       DEFAULT NULL,
    `activated`         int(1)    NOT NULL DEFAULT 1,
    `image`             varchar(1000)      DEFAULT NULL,
    `purchase_code`     varchar(255)       DEFAULT NULL,
    `created_at`        timestamp NULL     DEFAULT current_timestamp(),
    `updated_at`        timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addresses`
(
    `id`          int(11)   NOT NULL AUTO_INCREMENT,
    `user_id`     int(11)   NOT NULL,
    `address`     varchar(255)       DEFAULT NULL,
    `country_id`  int(11)            DEFAULT NULL,
    `state_id`    int(11)            DEFAULT NULL,
    `city_id`     int(11)            DEFAULT NULL,
    `area_id`     int(11)            DEFAULT NULL,
    `longitude`   float(17, 15)      DEFAULT NULL,
    `latitude`    float(17, 15)      DEFAULT NULL,
    `postal_code` varchar(255)       DEFAULT NULL,
    `phone`       varchar(255)       DEFAULT NULL,
    `set_default` int(1)    NOT NULL DEFAULT 0,
    `created_at`  timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at`  timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `app_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_translations`
(
    `id`         int(11)   NOT NULL AUTO_INCREMENT,
    `lang`       varchar(10)        DEFAULT NULL,
    `lang_key`   varchar(255)       DEFAULT NULL,
    `lang_value` varchar(255)       DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `area_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area_translations`
(
    `id`         int(11)      NOT NULL AUTO_INCREMENT,
    `area_id`    int(11)      NOT NULL,
    `name`       varchar(255) NOT NULL,
    `lang`       varchar(10)  NOT NULL,
    `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `areas`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `areas`
(
    `id`         bigint(20)    NOT NULL AUTO_INCREMENT,
    `name`       varchar(255)  NOT NULL,
    `city_id`    int(11)       NOT NULL,
    `cost`       double(20, 2) NOT NULL DEFAULT 0.00,
    `status`     int(11)       NOT NULL DEFAULT 1,
    `created_at` timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp     NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `deleted_at` timestamp     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attribute_category`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attribute_category`
(
    `id`           int(11)   NOT NULL AUTO_INCREMENT,
    `category_id`  int(11)   NOT NULL,
    `attribute_id` int(11)   NOT NULL,
    `created_at`   timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at`   timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attribute_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attribute_translations`
(
    `id`           bigint(20)   NOT NULL AUTO_INCREMENT,
    `attribute_id` bigint(20)   NOT NULL,
    `name`         varchar(50)  NOT NULL,
    `lang`         varchar(100) NOT NULL,
    `created_at`   timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`   timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attribute_values`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attribute_values`
(
    `id`           int(11)      NOT NULL AUTO_INCREMENT,
    `attribute_id` int(11)      NOT NULL,
    `value`        varchar(255) NOT NULL,
    `color_code`   varchar(100)          DEFAULT NULL,
    `created_at`   timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`   timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attributes`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attributes`
(
    `id`         int(11)   NOT NULL AUTO_INCREMENT,
    `name`       varchar(255)       DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_categories`
(
    `id`            bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `category_name` varchar(255)        NOT NULL,
    `slug`          varchar(255)        NOT NULL,
    `created_at`    timestamp           NULL DEFAULT NULL,
    `updated_at`    timestamp           NULL DEFAULT NULL,
    `deleted_at`    timestamp           NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blogs`
(
    `id`                bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `category_id`       int(11)             NOT NULL,
    `title`             varchar(255)        NOT NULL,
    `slug`              varchar(255)        NOT NULL,
    `short_description` text                         DEFAULT NULL,
    `description`       longtext                     DEFAULT NULL,
    `banner`            int(11)                      DEFAULT NULL,
    `meta_title`        varchar(255)                 DEFAULT NULL,
    `meta_img`          int(11)                      DEFAULT NULL,
    `meta_description`  text                         DEFAULT NULL,
    `meta_keywords`     text                         DEFAULT NULL,
    `status`            int(1)              NOT NULL DEFAULT 1,
    `created_at`        timestamp           NULL     DEFAULT NULL,
    `updated_at`        timestamp           NULL     DEFAULT NULL,
    `deleted_at`        timestamp           NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `brand_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brand_translations`
(
    `id`         bigint(20)   NOT NULL AUTO_INCREMENT,
    `brand_id`   bigint(20)   NOT NULL,
    `name`       varchar(50)  NOT NULL,
    `lang`       varchar(100) NOT NULL,
    `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands`
(
    `id`               int(11)     NOT NULL AUTO_INCREMENT,
    `name`             varchar(50) NOT NULL,
    `logo`             varchar(100)         DEFAULT NULL,
    `top`              int(1)      NOT NULL DEFAULT 0,
    `slug`             varchar(255)         DEFAULT NULL,
    `meta_title`       varchar(255)         DEFAULT NULL,
    `meta_description` text                 DEFAULT NULL,
    `created_at`       timestamp   NOT NULL DEFAULT current_timestamp(),
    `updated_at`       timestamp   NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `business_settings`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_settings`
(
    `id`         int(11)      NOT NULL AUTO_INCREMENT,
    `type`       varchar(255) NOT NULL,
    `value`      longtext              DEFAULT NULL,
    `lang`       varchar(30)           DEFAULT NULL,
    `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp    NULL     DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `carrier_range_prices`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrier_range_prices`
(
    `id`               int(11)      NOT NULL AUTO_INCREMENT,
    `carrier_id`       int(11)      NOT NULL,
    `carrier_range_id` int(11)      NOT NULL,
    `zone_id`          int(11)      NOT NULL,
    `price`            double(8, 2) NOT NULL,
    `created_at`       timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`       timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `carrier_ranges`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrier_ranges`
(
    `id`           int(11)       NOT NULL AUTO_INCREMENT,
    `carrier_id`   int(11)       NOT NULL,
    `billing_type` varchar(20)   NOT NULL,
    `delimiter1`   double(25, 2) NOT NULL,
    `delimiter2`   double(25, 2) NOT NULL,
    `created_at`   timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`   timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `carriers`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carriers`
(
    `id`            int(11)      NOT NULL AUTO_INCREMENT,
    `name`          varchar(255) NOT NULL,
    `logo`          int(11)               DEFAULT NULL,
    `transit_time`  varchar(255) NOT NULL,
    `free_shipping` tinyint(1)   NOT NULL DEFAULT 0,
    `status`        tinyint(1)   NOT NULL DEFAULT 1,
    `created_at`    timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`    timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts`
(
    `id`                    int(11) unsigned NOT NULL AUTO_INCREMENT,
    `status`                tinyint(1)       NOT NULL DEFAULT 1,
    `owner_id`              int(11)                   DEFAULT NULL,
    `user_id`               int(11)                   DEFAULT NULL,
    `temp_user_id`          varchar(255)              DEFAULT NULL,
    `address_id`            int(11)          NOT NULL DEFAULT 0,
    `product_id`            int(11)                   DEFAULT NULL,
    `variation`             text                      DEFAULT NULL,
    `price`                 double(20, 2)             DEFAULT 0.00,
    `tax`                   double(20, 2)             DEFAULT 0.00,
    `shipping_cost`         double(20, 2)    NOT NULL DEFAULT 0.00,
    `shipping_type`         varchar(30)      NOT NULL DEFAULT '',
    `pickup_point`          int(11)                   DEFAULT NULL,
    `carrier_id`            int(11)                   DEFAULT NULL,
    `discount`              double(10, 2)    NOT NULL DEFAULT 0.00,
    `product_referral_code` varchar(255)              DEFAULT NULL,
    `coupon_code`           varchar(255)              DEFAULT NULL,
    `coupon_applied`        tinyint(4)       NOT NULL DEFAULT 0,
    `quantity`              int(11)          NOT NULL DEFAULT 0,
    `created_at`            timestamp        NULL     DEFAULT current_timestamp(),
    `updated_at`            timestamp        NULL     DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories`
(
    `id`                  int(11)       NOT NULL AUTO_INCREMENT,
    `parent_id`           int(11)                DEFAULT 0,
    `level`               int(11)       NOT NULL DEFAULT 0,
    `name`                varchar(50)   NOT NULL,
    `order_level`         int(11)       NOT NULL DEFAULT 0,
    `commision_rate`      double(8, 2)  NOT NULL DEFAULT 0.00,
    `discount`            double(20, 2) NOT NULL DEFAULT 0.00,
    `discount_start_date` int(11)                DEFAULT NULL,
    `discount_end_date`   int(11)                DEFAULT NULL,
    `banner`              varchar(100)           DEFAULT NULL,
    `icon`                varchar(100)           DEFAULT NULL,
    `cover_image`         varchar(100)           DEFAULT NULL,
    `featured`            int(1)        NOT NULL DEFAULT 0,
    `top`                 int(1)        NOT NULL DEFAULT 0,
    `digital`             int(1)        NOT NULL DEFAULT 0,
    `slug`                varchar(255)           DEFAULT NULL,
    `refund_request_time` int(10) unsigned       DEFAULT NULL,
    `meta_title`          varchar(255)           DEFAULT NULL,
    `meta_description`    text                   DEFAULT NULL,
    `created_at`          timestamp     NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `updated_at`          timestamp     NULL     DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `slug` (`slug`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `category_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category_translations`
(
    `id`          bigint(20)   NOT NULL AUTO_INCREMENT,
    `category_id` bigint(20)   NOT NULL,
    `name`        varchar(50)  NOT NULL,
    `lang`        varchar(100) NOT NULL,
    `created_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cities`
(
    `id`         bigint(20)    NOT NULL AUTO_INCREMENT,
    `name`       varchar(255)  NOT NULL,
    `state_id`   int(11)                DEFAULT NULL,
    `country_id` int(11)                DEFAULT NULL,
    `cost`       double(20, 2) NOT NULL DEFAULT 0.00,
    `status`     int(11)       NOT NULL DEFAULT 1,
    `created_at` timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp     NOT NULL DEFAULT current_timestamp(),
    `deleted_at` timestamp     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `city_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `city_translations`
(
    `id`         int(11)      NOT NULL AUTO_INCREMENT,
    `city_id`    int(11)      NOT NULL,
    `name`       varchar(255) NOT NULL,
    `lang`       varchar(10)  NOT NULL,
    `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `colors`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `colors`
(
    `id`         int(11)   NOT NULL AUTO_INCREMENT,
    `name`       varchar(30)        DEFAULT NULL,
    `code`       varchar(10)        DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `combined_orders`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `combined_orders`
(
    `id`               int(11)       NOT NULL AUTO_INCREMENT,
    `user_id`          int(11)       NOT NULL,
    `shipping_address` text                   DEFAULT NULL,
    `grand_total`      double(20, 2) NOT NULL DEFAULT 0.00,
    `created_at`       timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`       timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `commission_histories`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commission_histories`
(
    `id`               int(11)       NOT NULL AUTO_INCREMENT,
    `order_id`         int(11)       NOT NULL,
    `order_detail_id`  int(11)       NOT NULL,
    `seller_id`        int(11)       NOT NULL,
    `admin_commission` double(25, 2) NOT NULL,
    `seller_earning`   double(25, 2) NOT NULL,
    `created_at`       timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`       timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts`
(
    `id`         int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name`       varchar(255)     NOT NULL,
    `email`      varchar(191)     NOT NULL,
    `phone`      varchar(20)               DEFAULT NULL,
    `content`    text             NOT NULL,
    `image`      varchar(191)              DEFAULT NULL,
    `reply`      text                      DEFAULT NULL,
    `created_at` timestamp        NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp        NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversations`
(
    `id`              int(11)   NOT NULL AUTO_INCREMENT,
    `sender_id`       int(11)   NOT NULL,
    `receiver_id`     int(11)   NOT NULL,
    `title`           varchar(1000)      DEFAULT NULL,
    `sender_viewed`   int(1)    NOT NULL DEFAULT 1,
    `receiver_viewed` int(1)    NOT NULL DEFAULT 0,
    `created_at`      timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at`      timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries`
(
    `id`         int(11)      NOT NULL AUTO_INCREMENT,
    `code`       varchar(2)   NOT NULL DEFAULT '',
    `name`       varchar(100) NOT NULL DEFAULT '',
    `zone_id`    int(11)      NOT NULL DEFAULT 0,
    `status`     int(1)       NOT NULL DEFAULT 1,
    `created_at` timestamp    NULL     DEFAULT current_timestamp(),
    `updated_at` timestamp    NULL     DEFAULT NULL,
    `deleted_at` timestamp    NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coupon_usages`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupon_usages`
(
    `id`         int(11)   NOT NULL AUTO_INCREMENT,
    `user_id`    int(11)   NOT NULL,
    `coupon_id`  int(11)   NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons`
(
    `id`            int(11)       NOT NULL AUTO_INCREMENT,
    `user_id`       int(11)       NOT NULL,
    `type`          varchar(255)  NOT NULL,
    `code`          varchar(255)  NOT NULL,
    `details`       longtext      NOT NULL,
    `discount`      double(20, 2) NOT NULL,
    `discount_type` varchar(100)  NOT NULL,
    `start_date`    int(15)                DEFAULT NULL,
    `end_date`      int(15)                DEFAULT NULL,
    `status`        tinyint(1)    NOT NULL DEFAULT 1,
    `created_at`    timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`    timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currencies`
(
    `id`            int(11)       NOT NULL AUTO_INCREMENT,
    `name`          varchar(255)  NOT NULL,
    `symbol`        varchar(255)  NOT NULL,
    `exchange_rate` double(10, 5) NOT NULL,
    `status`        int(10)       NOT NULL DEFAULT 0,
    `code`          varchar(20)            DEFAULT NULL,
    `created_at`    timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`    timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_alerts`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_alerts`
(
    `id`               int(20) unsigned NOT NULL AUTO_INCREMENT,
    `status`           int(1)           NOT NULL DEFAULT 0,
    `type`             varchar(191)     NOT NULL,
    `banner`           varchar(191)              DEFAULT NULL,
    `link`             varchar(191)     NOT NULL,
    `description`      text             NOT NULL,
    `text_color`       varchar(191)              DEFAULT NULL,
    `background_color` varchar(191)              DEFAULT NULL,
    `created_at`       timestamp        NOT NULL DEFAULT current_timestamp(),
    `updated_at`       timestamp        NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_package_payments`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_package_payments`
(
    `id`                  int(11)       NOT NULL AUTO_INCREMENT,
    `user_id`             int(11)       NOT NULL,
    `customer_package_id` int(11)       NOT NULL,
    `payment_method`      varchar(255)  NOT NULL,
    `amount`              double(20, 2) NOT NULL,
    `payment_details`     longtext               DEFAULT NULL,
    `approval`            int(1)        NOT NULL DEFAULT 1,
    `offline_payment`     int(1)        NOT NULL DEFAULT 2 COMMENT '1=offline payment\r\n2=online paymnet',
    `reciept`             varchar(150)  NOT NULL,
    `created_at`          timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`          timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_package_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_package_translations`
(
    `id`                  bigint(20)   NOT NULL AUTO_INCREMENT,
    `customer_package_id` bigint(20)   NOT NULL,
    `name`                varchar(50)  NOT NULL,
    `lang`                varchar(100) NOT NULL,
    `created_at`          timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`          timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_packages`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_packages`
(
    `id`             int(11)   NOT NULL AUTO_INCREMENT,
    `name`           varchar(255)   DEFAULT NULL,
    `amount`         double(20, 2)  DEFAULT NULL,
    `product_upload` int(11)        DEFAULT NULL,
    `logo`           varchar(150)   DEFAULT NULL,
    `created_at`     timestamp NULL DEFAULT NULL,
    `updated_at`     timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_product_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_product_translations`
(
    `id`                  bigint(20)   NOT NULL AUTO_INCREMENT,
    `customer_product_id` bigint(20)   NOT NULL,
    `name`                varchar(200)          DEFAULT NULL,
    `unit`                varchar(20)           DEFAULT NULL,
    `description`         longtext              DEFAULT NULL,
    `lang`                varchar(100) NOT NULL,
    `created_at`          timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`          timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_products`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_products`
(
    `id`                int(11)   NOT NULL AUTO_INCREMENT,
    `name`              varchar(255)       DEFAULT NULL,
    `published`         int(1)    NOT NULL DEFAULT 0,
    `status`            int(1)    NOT NULL DEFAULT 0,
    `added_by`          varchar(50)        DEFAULT NULL,
    `user_id`           int(11)            DEFAULT NULL,
    `category_id`       int(11)            DEFAULT NULL,
    `subcategory_id`    int(11)            DEFAULT NULL,
    `subsubcategory_id` int(11)            DEFAULT NULL,
    `brand_id`          int(11)            DEFAULT NULL,
    `photos`            varchar(255)       DEFAULT NULL,
    `thumbnail_img`     varchar(150)       DEFAULT NULL,
    `conditon`          varchar(50)        DEFAULT NULL,
    `location`          text               DEFAULT NULL,
    `video_provider`    varchar(100)       DEFAULT NULL,
    `video_link`        varchar(200)       DEFAULT NULL,
    `unit`              varchar(200)       DEFAULT NULL,
    `tags`              varchar(255)       DEFAULT NULL,
    `description`       mediumtext         DEFAULT NULL,
    `unit_price`        double(20, 2)      DEFAULT 0.00,
    `meta_title`        varchar(200)       DEFAULT NULL,
    `meta_description`  varchar(500)       DEFAULT NULL,
    `meta_img`          varchar(150)       DEFAULT NULL,
    `pdf`               varchar(200)       DEFAULT NULL,
    `slug`              varchar(200)       DEFAULT NULL,
    `created_at`        timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at`        timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dynamic_popups`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dynamic_popups`
(
    `id`                   int(20) unsigned NOT NULL AUTO_INCREMENT,
    `status`               int(1)           NOT NULL DEFAULT 0,
    `title`                varchar(191)     NOT NULL,
    `summary`              text             NOT NULL,
    `banner`               varchar(191)              DEFAULT NULL,
    `btn_link`             varchar(191)     NOT NULL,
    `btn_text`             varchar(191)              DEFAULT NULL,
    `btn_text_color`       varchar(191)              DEFAULT NULL,
    `btn_background_color` varchar(191)              DEFAULT NULL,
    `show_subscribe_form`  varchar(191)              DEFAULT NULL,
    `created_at`           timestamp        NOT NULL DEFAULT current_timestamp(),
    `updated_at`           timestamp        NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `element_styles`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `element_styles`
(
    `id`              int(11)      NOT NULL AUTO_INCREMENT,
    `element_type_id` int(11)      NOT NULL,
    `name`            varchar(100) NOT NULL,
    `value`           text              DEFAULT NULL,
    `created_at`      timestamp    NULL DEFAULT NULL,
    `updated_at`      timestamp    NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `element_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `element_translations`
(
    `id`         bigint(20)   NOT NULL,
    `element_id` bigint(20)   NOT NULL,
    `name`       varchar(50)  NOT NULL,
    `lang`       varchar(100) NOT NULL,
    `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `element_types`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `element_types`
(
    `id`         int(11)      NOT NULL AUTO_INCREMENT,
    `element_id` int(11)      NOT NULL,
    `name`       varchar(100) NOT NULL,
    `is_default` tinyint(1)   NOT NULL DEFAULT 0,
    `created_at` timestamp    NULL     DEFAULT NULL,
    `updated_at` timestamp    NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `elements`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `elements`
(
    `id`         int(11)      NOT NULL AUTO_INCREMENT,
    `name`       varchar(255) NOT NULL,
    `created_at` timestamp    NULL DEFAULT NULL,
    `updated_at` timestamp    NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_templates`
(
    `id`                       int(11)      NOT NULL AUTO_INCREMENT,
    `receiver`                 varchar(20)  NOT NULL,
    `identifier`               varchar(100) NOT NULL,
    `email_type`               varchar(255) NOT NULL,
    `subject`                  varchar(255) NOT NULL,
    `default_text`             text                  DEFAULT NULL,
    `status`                   tinyint(1)   NOT NULL DEFAULT 1,
    `is_status_changeable`     tinyint(1)   NOT NULL DEFAULT 1 COMMENT '1 = changeable ; 0 = non-changeable',
    `is_dafault_text_editable` tinyint(1)   NOT NULL DEFAULT 1 COMMENT '1 = editable ; 0 = non-editable\r\n\r\n',
    `addon`                    varchar(50)           DEFAULT NULL,
    `created_at`               timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`               timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `firebase_notifications`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `firebase_notifications`
(
    `id`           int(11)      NOT NULL AUTO_INCREMENT,
    `title`        varchar(255)          DEFAULT NULL,
    `text`         text                  DEFAULT NULL,
    `item_type`    varchar(255) NOT NULL,
    `item_type_id` int(11)      NOT NULL,
    `receiver_id`  int(11)      NOT NULL,
    `is_read`      tinyint(1)   NOT NULL DEFAULT 0,
    `created_at`   timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`   timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `flash_deal_products`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flash_deal_products`
(
    `id`            int(11)   NOT NULL AUTO_INCREMENT,
    `flash_deal_id` int(11)   NOT NULL,
    `product_id`    int(11)   NOT NULL,
    `discount`      double(20, 2)      DEFAULT 0.00,
    `discount_type` varchar(20)        DEFAULT NULL,
    `created_at`    timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at`    timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `flash_deal_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flash_deal_translations`
(
    `id`            bigint(20)   NOT NULL AUTO_INCREMENT,
    `flash_deal_id` bigint(20)   NOT NULL,
    `title`         varchar(50)  NOT NULL,
    `lang`          varchar(100) NOT NULL,
    `created_at`    timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`    timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `flash_deals`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flash_deals`
(
    `id`               int(11)   NOT NULL AUTO_INCREMENT,
    `title`            varchar(255)       DEFAULT NULL,
    `start_date`       int(20)            DEFAULT NULL,
    `end_date`         int(20)            DEFAULT NULL,
    `status`           int(1)    NOT NULL DEFAULT 0,
    `featured`         int(1)    NOT NULL DEFAULT 0,
    `background_color` varchar(255)       DEFAULT NULL,
    `text_color`       varchar(255)       DEFAULT NULL,
    `banner`           varchar(255)       DEFAULT NULL,
    `slug`             varchar(255)       DEFAULT NULL,
    `created_at`       timestamp NULL     DEFAULT current_timestamp(),
    `updated_at`       timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `follow_sellers`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `follow_sellers`
(
    `user_id` bigint(20) NOT NULL,
    `shop_id` bigint(20) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `frequently_bought_products`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `frequently_bought_products`
(
    `product_id`                   int(11) NOT NULL,
    `frequently_bought_product_id` int(11) DEFAULT NULL,
    `category_id`                  int(11) DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `home_categories`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `home_categories`
(
    `id`               int(11)   NOT NULL AUTO_INCREMENT,
    `category_id`      int(11)   NOT NULL,
    `subsubcategories` varchar(1000)      DEFAULT NULL,
    `status`           int(1)    NOT NULL DEFAULT 1,
    `created_at`       timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at`       timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages`
(
    `id`            int(11)      NOT NULL AUTO_INCREMENT,
    `name`          varchar(100) NOT NULL,
    `code`          varchar(100) NOT NULL,
    `app_lang_code` varchar(255)          DEFAULT 'en',
    `rtl`           int(1)       NOT NULL DEFAULT 0,
    `status`        tinyint(1)   NOT NULL DEFAULT 1,
    `created_at`    timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`    timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `last_viewed_products`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `last_viewed_products`
(
    `id`         int(20)   NOT NULL AUTO_INCREMENT,
    `user_id`    int(11)   NOT NULL,
    `product_id` int(11)   NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `measurement_points`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `measurement_points`
(
    `id`         int(20)      NOT NULL AUTO_INCREMENT,
    `name`       varchar(191) NOT NULL,
    `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages`
(
    `id`              int(11)   NOT NULL AUTO_INCREMENT,
    `conversation_id` int(11)   NOT NULL,
    `user_id`         int(11)   NOT NULL,
    `message`         text               DEFAULT NULL,
    `created_at`      timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at`      timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `note_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `note_translations`
(
    `id`          bigint(20)   NOT NULL AUTO_INCREMENT,
    `note_id`     bigint(20)   NOT NULL,
    `description` longtext     NOT NULL,
    `lang`        varchar(100) NOT NULL,
    `created_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes`
(
    `id`            bigint(20)  NOT NULL AUTO_INCREMENT,
    `user_id`       int(11)     NOT NULL,
    `note_type`     varchar(50) NOT NULL,
    `description`   longtext    NOT NULL,
    `seller_access` tinyint(4)  NOT NULL DEFAULT 0 COMMENT 'Seller can access admin note;\r\n0 = No\r\n1 = Yes',
    `created_at`    timestamp   NULL     DEFAULT NULL,
    `updated_at`    timestamp   NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notification_type_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_type_translations`
(
    `id`                   bigint(20)   NOT NULL AUTO_INCREMENT,
    `notification_type_id` int(11)      NOT NULL,
    `name`                 varchar(100) NOT NULL,
    `default_text`         varchar(255) NOT NULL,
    `lang`                 varchar(100) NOT NULL,
    `created_at`           timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`           timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notification_types`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_types`
(
    `id`           int(11)      NOT NULL AUTO_INCREMENT,
    `user_type`    varchar(20)  NOT NULL DEFAULT 'customer',
    `type`         varchar(100) NOT NULL,
    `name`         varchar(100) NOT NULL,
    `image`        varchar(100)          DEFAULT NULL,
    `default_text` varchar(255) NOT NULL,
    `status`       tinyint(4)   NOT NULL DEFAULT 1,
    `addon`        varchar(50)           DEFAULT NULL,
    `created_at`   timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`   timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications`
(
    `id`                   char(36)            NOT NULL,
    `notification_type_id` int(11)             NOT NULL,
    `type`                 varchar(191)        NOT NULL,
    `notifiable_type`      varchar(191)        NOT NULL,
    `notifiable_id`        bigint(20) unsigned NOT NULL,
    `data`                 text                NOT NULL,
    `read_at`              timestamp           NULL DEFAULT NULL,
    `created_at`           timestamp           NULL DEFAULT NULL,
    `updated_at`           timestamp           NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`, `notifiable_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `order_details`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_details`
(
    `id`                    int(11)       NOT NULL AUTO_INCREMENT,
    `order_id`              int(11)       NOT NULL,
    `seller_id`             int(11)                DEFAULT NULL,
    `product_id`            int(11)       NOT NULL,
    `variation`             longtext               DEFAULT NULL,
    `price`                 double(20, 2)          DEFAULT NULL,
    `tax`                   double(20, 2) NOT NULL DEFAULT 0.00,
    `shipping_cost`         double(20, 2) NOT NULL DEFAULT 0.00,
    `quantity`              int(11)                DEFAULT NULL,
    `payment_status`        varchar(10)   NOT NULL DEFAULT 'unpaid',
    `delivery_status`       varchar(20)            DEFAULT 'pending',
    `refund_days`           int(11)       NOT NULL DEFAULT 0,
    `reviewed`              tinyint(1)    NOT NULL DEFAULT 0,
    `shipping_type`         varchar(255)           DEFAULT NULL,
    `pickup_point_id`       int(11)                DEFAULT NULL,
    `product_referral_code` varchar(255)           DEFAULT NULL,
    `earn_point`            double(25, 2) NOT NULL DEFAULT 0.00,
    `created_at`            timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`            timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders`
(
    `id`                    int(11)       NOT NULL AUTO_INCREMENT,
    `combined_order_id`     int(11)                DEFAULT NULL,
    `user_id`               int(11)                DEFAULT NULL,
    `guest_id`              int(11)                DEFAULT NULL,
    `seller_id`             int(11)                DEFAULT NULL,
    `shipping_address`      longtext               DEFAULT NULL,
    `additional_info`       longtext               DEFAULT NULL,
    `shipping_type`         varchar(50)   NOT NULL,
    `order_from`            varchar(20)   NOT NULL DEFAULT 'web',
    `pickup_point_id`       int(11)       NOT NULL DEFAULT 0,
    `carrier_id`            int(11)                DEFAULT NULL,
    `delivery_status`       varchar(20)            DEFAULT 'pending',
    `payment_type`          varchar(20)            DEFAULT NULL,
    `payment_status`        varchar(20)            DEFAULT 'unpaid',
    `payment_details`       longtext               DEFAULT NULL,
    `grand_total`           double(20, 2)          DEFAULT NULL,
    `coupon_discount`       double(20, 2) NOT NULL DEFAULT 0.00,
    `code`                  mediumtext             DEFAULT NULL,
    `tracking_code`         varchar(255)           DEFAULT NULL,
    `date`                  int(20)       NOT NULL,
    `viewed`                int(1)        NOT NULL DEFAULT 0,
    `delivery_viewed`       int(1)        NOT NULL DEFAULT 1,
    `payment_status_viewed` int(1)                 DEFAULT 1,
    `commission_calculated` int(11)       NOT NULL DEFAULT 0,
    `notified`              tinyint(1)    NOT NULL DEFAULT 0,
    `delivered_date`        timestamp     NULL     DEFAULT NULL,
    `created_at`            timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`            timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `page_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_translations`
(
    `id`         bigint(20)   NOT NULL AUTO_INCREMENT,
    `page_id`    bigint(20)   NOT NULL,
    `title`      varchar(255) NOT NULL,
    `content`    longtext     NOT NULL,
    `lang`       varchar(100) NOT NULL,
    `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages`
(
    `id`               int(11)     NOT NULL AUTO_INCREMENT,
    `type`             varchar(50) NOT NULL,
    `title`            varchar(255)         DEFAULT NULL,
    `slug`             varchar(255)         DEFAULT NULL,
    `content`          longtext             DEFAULT NULL,
    `meta_title`       text                 DEFAULT NULL,
    `meta_description` varchar(1000)        DEFAULT NULL,
    `keywords`         varchar(1000)        DEFAULT NULL,
    `meta_image`       varchar(255)         DEFAULT NULL,
    `created_at`       timestamp   NOT NULL DEFAULT current_timestamp(),
    `updated_at`       timestamp   NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets`
(
    `email`      varchar(191) NOT NULL,
    `token`      varchar(191) NOT NULL,
    `created_at` timestamp    NULL DEFAULT NULL,
    KEY `password_resets_email_index` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_methods`
(
    `id`               tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `name`             varchar(255)        NOT NULL,
    `active`           tinyint(1)          NOT NULL DEFAULT 0,
    `addon_identifier` varchar(191)                 DEFAULT NULL,
    `created_at`       timestamp           NOT NULL DEFAULT current_timestamp(),
    `updated_at`       timestamp           NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments`
(
    `id`              int(11)       NOT NULL AUTO_INCREMENT,
    `seller_id`       int(11)       NOT NULL,
    `amount`          double(20, 2) NOT NULL DEFAULT 0.00,
    `payment_details` longtext               DEFAULT NULL,
    `payment_method`  varchar(255)           DEFAULT NULL,
    `txn_code`        varchar(100)           DEFAULT NULL,
    `created_at`      timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`      timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pickup_point_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pickup_point_translations`
(
    `id`              bigint(20)   NOT NULL AUTO_INCREMENT,
    `pickup_point_id` bigint(20)   NOT NULL,
    `name`            varchar(50)  NOT NULL,
    `address`         text         NOT NULL,
    `lang`            varchar(100) NOT NULL,
    `created_at`      timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`      timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pickup_points`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pickup_points`
(
    `id`                    int(11)      NOT NULL AUTO_INCREMENT,
    `staff_id`              int(11)      NOT NULL,
    `name`                  varchar(255) NOT NULL,
    `address`               mediumtext   NOT NULL,
    `phone`                 varchar(15)  NOT NULL,
    `pick_up_status`        int(1)                DEFAULT NULL,
    `cash_on_pickup_status` int(1)                DEFAULT NULL,
    `created_at`            timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`            timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_categories`
(
    `product_id`  int(11) NOT NULL,
    `category_id` int(11) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_queries`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_queries`
(
    `id`          bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `customer_id` int(11)             NOT NULL,
    `seller_id`   int(11)             NOT NULL,
    `product_id`  int(11)             NOT NULL,
    `question`    longtext            NOT NULL,
    `reply`       longtext                 DEFAULT NULL,
    `created_at`  timestamp           NULL DEFAULT NULL,
    `updated_at`  timestamp           NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_stocks`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_stocks`
(
    `id`         int(11)       NOT NULL AUTO_INCREMENT,
    `product_id` int(11)       NOT NULL,
    `variant`    varchar(255)  NOT NULL,
    `sku`        varchar(255)           DEFAULT NULL,
    `price`      double(20, 2) NOT NULL DEFAULT 0.00,
    `qty`        int(11)       NOT NULL DEFAULT 0,
    `image`      int(11)                DEFAULT NULL,
    `created_at` timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_taxes`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_taxes`
(
    `id`         int(11)       NOT NULL AUTO_INCREMENT,
    `product_id` int(11)       NOT NULL,
    `tax_id`     int(11)       NOT NULL,
    `tax`        double(20, 2) NOT NULL,
    `tax_type`   varchar(10)   NOT NULL,
    `created_at` timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_translations`
(
    `id`          bigint(20)   NOT NULL AUTO_INCREMENT,
    `product_id`  bigint(20)   NOT NULL,
    `name`        varchar(200)          DEFAULT NULL,
    `unit`        varchar(20)           DEFAULT NULL,
    `description` longtext              DEFAULT NULL,
    `lang`        varchar(100) NOT NULL,
    `created_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products`
(
    `id`                               int(11)       NOT NULL AUTO_INCREMENT,
    `name`                             varchar(200)  NOT NULL,
    `added_by`                         varchar(6)    NOT NULL DEFAULT 'admin',
    `user_id`                          int(11)       NOT NULL,
    `category_id`                      int(11)       NOT NULL,
    `brand_id`                         int(11)                DEFAULT NULL,
    `photos`                           varchar(2000)          DEFAULT NULL,
    `thumbnail_img`                    varchar(100)           DEFAULT NULL,
    `short_video`                      varchar(255)           DEFAULT NULL,
    `short_video_thumbnail`            varchar(255)           DEFAULT NULL,
    `video_provider`                   varchar(20)            DEFAULT NULL,
    `video_link`                       longtext               DEFAULT NULL,
    `tags`                             varchar(500)           DEFAULT NULL,
    `description`                      longtext               DEFAULT NULL,
    `unit_price`                       double(20, 2) NOT NULL,
    `purchase_price`                   double(20, 2)          DEFAULT NULL,
    `variant_product`                  int(1)        NOT NULL DEFAULT 0,
    `attributes`                       varchar(1000) NOT NULL DEFAULT '[]',
    `choice_options`                   mediumtext             DEFAULT NULL,
    `colors`                           mediumtext             DEFAULT NULL,
    `variations`                       text                   DEFAULT NULL,
    `todays_deal`                      int(11)       NOT NULL DEFAULT 0,
    `published`                        int(11)       NOT NULL DEFAULT 1,
    `approved`                         tinyint(1)    NOT NULL DEFAULT 1,
    `stock_visibility_state`           varchar(10)   NOT NULL DEFAULT 'quantity',
    `cash_on_delivery`                 tinyint(1)    NOT NULL DEFAULT 0 COMMENT '1 = On, 0 = Off',
    `featured`                         int(11)       NOT NULL DEFAULT 0,
    `seller_featured`                  int(11)       NOT NULL DEFAULT 0,
    `current_stock`                    int(10)       NOT NULL DEFAULT 0,
    `unit`                             varchar(20)            DEFAULT NULL,
    `weight`                           double(8, 2)  NOT NULL DEFAULT 0.00,
    `min_qty`                          int(11)       NOT NULL DEFAULT 1,
    `low_stock_quantity`               int(11)                DEFAULT NULL,
    `discount`                         double(20, 2) NOT NULL DEFAULT 0.00,
    `discount_type`                    varchar(10)   NOT NULL DEFAULT 'amount',
    `discount_start_date`              int(11)                DEFAULT NULL,
    `discount_end_date`                int(11)                DEFAULT NULL,
    `tax`                              double(20, 2)          DEFAULT NULL,
    `tax_type`                         varchar(10)            DEFAULT NULL,
    `shipping_type`                    varchar(20)            DEFAULT 'flat_rate',
    `shipping_cost`                    double(20, 2) NOT NULL DEFAULT 0.00,
    `is_quantity_multiplied`           tinyint(1)    NOT NULL DEFAULT 0 COMMENT '1 = Mutiplied with shipping cost',
    `est_shipping_days`                int(11)                DEFAULT NULL,
    `num_of_sale`                      int(11)       NOT NULL DEFAULT 0,
    `meta_title`                       mediumtext             DEFAULT NULL,
    `meta_description`                 longtext               DEFAULT NULL,
    `meta_img`                         varchar(255)           DEFAULT NULL,
    `pdf`                              varchar(255)           DEFAULT NULL,
    `slug`                             mediumtext    NOT NULL,
    `rating`                           double(8, 2)  NOT NULL DEFAULT 0.00,
    `barcode`                          varchar(255)           DEFAULT NULL,
    `digital`                          int(1)        NOT NULL DEFAULT 0,
    `auction_product`                  int(1)        NOT NULL DEFAULT 0,
    `file_name`                        varchar(255)           DEFAULT NULL,
    `file_path`                        varchar(255)           DEFAULT NULL,
    `external_link`                    varchar(500)           DEFAULT NULL,
    `external_link_btn`                varchar(255)           DEFAULT 'Buy Now',
    `wholesale_product`                int(1)        NOT NULL DEFAULT 0,
    `frequently_bought_selection_type` varchar(19)            DEFAULT 'product',
    `has_warranty`                     tinyint(4)    NOT NULL DEFAULT 0,
    `warranty_id`                      int(11)                DEFAULT NULL,
    `warranty_note_id`                 int(11)                DEFAULT NULL,
    `created_at`                       timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`                       timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `name` (`name`),
    KEY `tags` (`tags`(255)),
    KEY `unit_price` (`unit_price`),
    KEY `created_at` (`created_at`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proxypay_payments`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proxypay_payments`
(
    `id`           int(11)       NOT NULL AUTO_INCREMENT,
    `payment_type` varchar(20)   NOT NULL,
    `reference_id` varchar(20)   NOT NULL,
    `order_id`     int(11)                DEFAULT NULL,
    `package_id`   int(11)                DEFAULT NULL,
    `user_id`      int(11)       NOT NULL,
    `amount`       double(25, 2) NOT NULL DEFAULT 0.00,
    `created_at`   timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`   timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `registration_verification_codes`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registration_verification_codes`
(
    `id`          int(11)    NOT NULL AUTO_INCREMENT,
    `email`       varchar(191)        DEFAULT NULL,
    `phone`       varchar(20)         DEFAULT NULL,
    `code`        text       NOT NULL,
    `is_verified` tinyint(4) NOT NULL DEFAULT 0,
    `created_at`  timestamp  NOT NULL DEFAULT current_timestamp(),
    `updated_at`  timestamp  NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    UNIQUE KEY `phone` (`phone`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews`
(
    `id`                    int(11)     NOT NULL AUTO_INCREMENT,
    `type`                  varchar(10) NOT NULL DEFAULT 'real',
    `product_id`            int(11)     NOT NULL,
    `user_id`               int(11)              DEFAULT NULL,
    `custom_reviewer_name`  varchar(100)         DEFAULT NULL,
    `custom_reviewer_image` varchar(100)         DEFAULT NULL,
    `rating`                int(11)     NOT NULL DEFAULT 0,
    `comment`               mediumtext  NOT NULL,
    `photos`                varchar(191)         DEFAULT NULL,
    `status`                int(1)      NOT NULL DEFAULT 1,
    `viewed`                int(1)      NOT NULL DEFAULT 0,
    `created_at_is_custom`  tinyint(1)  NOT NULL DEFAULT 0,
    `created_at`            timestamp   NOT NULL DEFAULT current_timestamp(),
    `updated_at`            timestamp   NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_translations`
(
    `id`         bigint(20)   NOT NULL AUTO_INCREMENT,
    `role_id`    bigint(20)   NOT NULL,
    `name`       varchar(50)  NOT NULL,
    `lang`       varchar(100) NOT NULL,
    `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `searches`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searches`
(
    `id`         int(11)       NOT NULL AUTO_INCREMENT,
    `query`      varchar(1000) NOT NULL,
    `count`      int(11)       NOT NULL DEFAULT 1,
    `created_at` timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `seller_categories`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seller_categories`
(
    `id`                  int(11)       NOT NULL AUTO_INCREMENT,
    `seller_id`           int(11)       NOT NULL,
    `category_id`         int(11)       NOT NULL,
    `discount`            double(20, 2) NOT NULL DEFAULT 0.00,
    `discount_start_date` int(11)                DEFAULT NULL,
    `discount_end_date`   int(11)                DEFAULT NULL,
    `created_at`          timestamp     NULL     DEFAULT NULL,
    `updated_at`          timestamp     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `seller_withdraw_requests`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seller_withdraw_requests`
(
    `id`         int(11)   NOT NULL AUTO_INCREMENT,
    `user_id`    int(11)            DEFAULT NULL,
    `amount`     double(20, 2)      DEFAULT NULL,
    `message`    longtext           DEFAULT NULL,
    `status`     int(1)             DEFAULT NULL,
    `viewed`     int(1)             DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sellers`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sellers`
(
    `id`                      int(11)       NOT NULL AUTO_INCREMENT,
    `user_id`                 int(11)       NOT NULL,
    `rating`                  double(3, 2)  NOT NULL DEFAULT 0.00,
    `num_of_reviews`          int(11)       NOT NULL DEFAULT 0,
    `num_of_sale`             int(11)       NOT NULL DEFAULT 0,
    `verification_status`     int(1)        NOT NULL DEFAULT 0,
    `verification_info`       longtext               DEFAULT NULL,
    `cash_on_delivery_status` int(1)        NOT NULL DEFAULT 0,
    `admin_to_pay`            double(20, 2) NOT NULL DEFAULT 0.00,
    `bank_name`               varchar(255)           DEFAULT NULL,
    `bank_acc_name`           varchar(200)           DEFAULT NULL,
    `bank_acc_no`             varchar(50)            DEFAULT NULL,
    `bank_routing_no`         int(50)                DEFAULT NULL,
    `bank_payment_status`     int(11)       NOT NULL DEFAULT 0,
    `created_at`              timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`              timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_id` (`user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shops`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shops`
(
    `id`                         int(11)       NOT NULL AUTO_INCREMENT,
    `user_id`                    int(11)       NOT NULL,
    `name`                       varchar(200)           DEFAULT NULL,
    `logo`                       varchar(255)           DEFAULT NULL,
    `sliders`                    longtext               DEFAULT NULL,
    `top_banner`                 varchar(191)           DEFAULT NULL,
    `banner_full_width_1`        varchar(191)           DEFAULT NULL,
    `banners_half_width`         varchar(191)           DEFAULT NULL,
    `banner_full_width_2`        varchar(191)           DEFAULT NULL,
    `phone`                      varchar(255)           DEFAULT NULL,
    `address`                    varchar(500)           DEFAULT NULL,
    `rating`                     double(3, 2)  NOT NULL DEFAULT 0.00,
    `num_of_reviews`             int(11)       NOT NULL DEFAULT 0,
    `num_of_sale`                int(11)       NOT NULL DEFAULT 0,
    `seller_package_id`          int(11)                DEFAULT NULL,
    `product_upload_limit`       int(11)       NOT NULL DEFAULT 0,
    `package_invalid_at`         date                   DEFAULT NULL,
    `registration_approval`      tinyint(1)    NOT NULL DEFAULT 0,
    `verification_status`        int(1)        NOT NULL DEFAULT 0,
    `verification_info`          longtext               DEFAULT NULL,
    `cash_on_delivery_status`    int(1)        NOT NULL DEFAULT 0,
    `admin_to_pay`               double(20, 2) NOT NULL DEFAULT 0.00,
    `facebook`                   varchar(255)           DEFAULT NULL,
    `instagram`                  varchar(255)           DEFAULT NULL,
    `google`                     varchar(255)           DEFAULT NULL,
    `twitter`                    varchar(255)           DEFAULT NULL,
    `youtube`                    varchar(255)           DEFAULT NULL,
    `slug`                       varchar(255)           DEFAULT NULL,
    `meta_title`                 varchar(255)           DEFAULT NULL,
    `meta_description`           text                   DEFAULT NULL,
    `pick_up_point_id`           text                   DEFAULT NULL,
    `shipping_cost`              double(20, 2) NOT NULL DEFAULT 0.00,
    `commission_percentage`      double(8, 2)  NOT NULL DEFAULT 0.00,
    `delivery_pickup_latitude`   float(17, 15)          DEFAULT NULL,
    `delivery_pickup_longitude`  float(17, 15)          DEFAULT NULL,
    `bank_name`                  varchar(255)           DEFAULT NULL,
    `bank_acc_name`              varchar(200)           DEFAULT NULL,
    `bank_acc_no`                varchar(50)            DEFAULT NULL,
    `bank_routing_no`            int(50)                DEFAULT NULL,
    `bank_payment_status`        int(11)       NOT NULL DEFAULT 0,
    `top_banner_image`           longtext               DEFAULT NULL,
    `top_banner_link`            longtext               DEFAULT NULL,
    `slider_images`              longtext               DEFAULT NULL,
    `slider_links`               longtext               DEFAULT NULL,
    `banner_full_width_1_images` longtext               DEFAULT NULL,
    `banner_full_width_1_links`  longtext               DEFAULT NULL,
    `banners_half_width_images`  longtext               DEFAULT NULL,
    `banners_half_width_links`   longtext               DEFAULT NULL,
    `banner_full_width_2_images` longtext               DEFAULT NULL,
    `banner_full_width_2_links`  longtext               DEFAULT NULL,
    `custom_followers`           int(11)                DEFAULT 0,
    `created_at`                 timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`                 timestamp     NULL     DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `size_chart_details`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `size_chart_details`
(
    `id`                   int(20) unsigned NOT NULL AUTO_INCREMENT,
    `size_chart_id`        int(20)          NOT NULL,
    `measurement_point_id` int(20)          NOT NULL,
    `attribute_value_id`   int(20)          NOT NULL,
    `inch_value`           varchar(191)              DEFAULT NULL,
    `cen_value`            varchar(191)              DEFAULT NULL,
    `created_at`           timestamp        NOT NULL DEFAULT current_timestamp(),
    `updated_at`           timestamp        NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `size_charts`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `size_charts`
(
    `id`                 int(20) unsigned NOT NULL AUTO_INCREMENT,
    `name`               varchar(255)     NOT NULL,
    `category_id`        int(11)          NOT NULL,
    `fit_type`           varchar(191)              DEFAULT NULL,
    `stretch_type`       varchar(191)              DEFAULT NULL,
    `photos`             varchar(191)              DEFAULT NULL,
    `description`        text                      DEFAULT NULL,
    `measurement_points` varchar(255)     NOT NULL,
    `size_options`       varchar(255)     NOT NULL,
    `measurement_option` varchar(191)              DEFAULT NULL,
    `created_at`         timestamp        NOT NULL DEFAULT current_timestamp(),
    `updated_at`         timestamp        NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `category_id` (`category_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff`
(
    `id`         int(11)   NOT NULL AUTO_INCREMENT,
    `user_id`    int(11)   NOT NULL,
    `role_id`    int(11)   NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `states`
(
    `id`         bigint(20)   NOT NULL AUTO_INCREMENT,
    `name`       varchar(255) NOT NULL,
    `country_id` int(11)      NOT NULL,
    `status`     int(11)      NOT NULL DEFAULT 0,
    `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `deleted_at` timestamp    NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscribers`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscribers`
(
    `id`         int(11)     NOT NULL AUTO_INCREMENT,
    `email`      varchar(50) NOT NULL,
    `created_at` timestamp   NULL     DEFAULT current_timestamp(),
    `updated_at` timestamp   NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `taxes`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxes`
(
    `id`         int(11)      NOT NULL AUTO_INCREMENT,
    `name`       varchar(255) NOT NULL,
    `tax_status` tinyint(1)   NOT NULL DEFAULT 1 COMMENT '0 = Inactive, 1 = Active',
    `created_at` timestamp    NULL     DEFAULT current_timestamp(),
    `updated_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_replies`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_replies`
(
    `id`         int(11)   NOT NULL AUTO_INCREMENT,
    `ticket_id`  int(11)   NOT NULL,
    `user_id`    int(11)   NOT NULL,
    `reply`      longtext  NOT NULL,
    `files`      longtext           DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets`
(
    `id`            int(11)      NOT NULL AUTO_INCREMENT,
    `code`          bigint(23)   NOT NULL,
    `user_id`       int(11)      NOT NULL,
    `subject`       varchar(255) NOT NULL,
    `details`       longtext              DEFAULT NULL,
    `files`         longtext              DEFAULT NULL,
    `status`        varchar(10)  NOT NULL DEFAULT 'pending',
    `viewed`        int(1)       NOT NULL DEFAULT 0,
    `client_viewed` int(1)       NOT NULL DEFAULT 0,
    `created_at`    timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `updated_at`    timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions`
(
    `id`                 int(11)   NOT NULL AUTO_INCREMENT,
    `user_id`            int(11)   NOT NULL,
    `gateway`            varchar(255)       DEFAULT NULL,
    `payment_type`       varchar(255)       DEFAULT NULL,
    `additional_content` text               DEFAULT NULL,
    `mpesa_request`      varchar(255)       DEFAULT NULL,
    `mpesa_receipt`      varchar(255)       DEFAULT NULL,
    `status`             int(1)    NOT NULL DEFAULT 0,
    `created_at`         timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at`         timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  COLLATE = latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translations`
(
    `id`         int(11)   NOT NULL AUTO_INCREMENT,
    `lang`       varchar(10)        DEFAULT NULL,
    `lang_key`   text               DEFAULT NULL,
    `lang_value` text               DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `uploads`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploads`
(
    `id`                 int(11)   NOT NULL AUTO_INCREMENT,
    `file_original_name` varchar(255)       DEFAULT NULL,
    `file_name`          varchar(255)       DEFAULT NULL,
    `user_id`            int(11)            DEFAULT NULL,
    `file_size`          int(11)            DEFAULT NULL,
    `extension`          varchar(10)        DEFAULT NULL,
    `type`               varchar(15)        DEFAULT NULL,
    `external_link`      varchar(500)       DEFAULT NULL,
    `created_at`         timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at`         timestamp NOT NULL DEFAULT current_timestamp(),
    `deleted_at`         timestamp NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_coupons`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_coupons`
(
    `user_id`         int(11)       NOT NULL,
    `coupon_id`       int(11)       NOT NULL,
    `coupon_code`     varchar(255)  NOT NULL,
    `min_buy`         double(20, 2) NOT NULL,
    `validation_days` int(11)       NOT NULL,
    `discount`        double(20, 2) NOT NULL,
    `discount_type`   varchar(20)   NOT NULL,
    `expiry_date`     int(11)       NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users`
(
    `id`                           int(9) unsigned NOT NULL AUTO_INCREMENT,
    `referred_by`                  int(11)                  DEFAULT NULL,
    `provider`                     varchar(255)             DEFAULT NULL,
    `provider_id`                  varchar(50)              DEFAULT NULL,
    `refresh_token`                text                     DEFAULT NULL,
    `access_token`                 longtext                 DEFAULT NULL,
    `user_type`                    varchar(20)     NOT NULL DEFAULT 'customer',
    `name`                         varchar(191)    NOT NULL,
    `email`                        varchar(191)             DEFAULT NULL,
    `email_verified_at`            timestamp       NULL     DEFAULT NULL,
    `verification_code`            text                     DEFAULT NULL,
    `new_email_verificiation_code` text                     DEFAULT NULL,
    `password`                     varchar(191)             DEFAULT NULL,
    `remember_token`               varchar(100)             DEFAULT NULL,
    `device_token`                 varchar(255)             DEFAULT NULL,
    `avatar`                       varchar(256)             DEFAULT NULL,
    `avatar_original`              varchar(256)             DEFAULT NULL,
    `address`                      varchar(300)             DEFAULT NULL,
    `country`                      varchar(30)              DEFAULT NULL,
    `state`                        varchar(30)              DEFAULT NULL,
    `city`                         varchar(30)              DEFAULT NULL,
    `postal_code`                  varchar(20)              DEFAULT NULL,
    `phone`                        varchar(20)              DEFAULT NULL,
    `balance`                      double(20, 2)   NOT NULL DEFAULT 0.00,
    `banned`                       tinyint(4)      NOT NULL DEFAULT 0,
    `is_suspicious`                tinyint(4)               DEFAULT 0,
    `referral_code`                varchar(255)             DEFAULT NULL,
    `customer_package_id`          int(11)                  DEFAULT NULL,
    `remaining_uploads`            int(11)                  DEFAULT 0,
    `created_at`                   timestamp       NULL     DEFAULT NULL,
    `updated_at`                   timestamp       NULL     DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wallets`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wallets`
(
    `id`              int(11)       NOT NULL AUTO_INCREMENT,
    `user_id`         int(11)       NOT NULL,
    `amount`          double(20, 2) NOT NULL,
    `payment_method`  varchar(255)           DEFAULT NULL,
    `payment_details` longtext               DEFAULT NULL,
    `created_at`      timestamp     NOT NULL DEFAULT current_timestamp(),
    `updated_at`      timestamp     NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `warranties`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warranties`
(
    `id`         int(11)      NOT NULL AUTO_INCREMENT,
    `text`       varchar(100) NOT NULL,
    `logo`       int(11)               DEFAULT NULL,
    `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `warranty_translations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warranty_translations`
(
    `id`          bigint(20)   NOT NULL AUTO_INCREMENT,
    `warranty_id` bigint(20)   NOT NULL,
    `text`        varchar(50)  NOT NULL,
    `lang`        varchar(100) NOT NULL,
    `created_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlists`
(
    `id`         int(11)   NOT NULL AUTO_INCREMENT,
    `user_id`    int(11)   NOT NULL,
    `product_id` int(11)   NOT NULL,
    `created_at` timestamp NULL     DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `zones`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zones`
(
    `id`         int(11)      NOT NULL AUTO_INCREMENT,
    `name`       varchar(255) NOT NULL,
    `status`     tinyint(1)   NOT NULL COMMENT '0 = Inactive, 1 = Active',
    `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp    NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE = @OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;
