-- =====================================================================
--  AutoSOFT — Esquema da base de dados (MySQL 5.7+ / MariaDB 10.3+)
--  Codificação utf8mb4. Crie a base e importe este ficheiro, depois seed.sql
--  No phpMyAdmin / linha de comando:
--    mysql -u root -p < schema.sql
--    mysql -u root -p autosoft < seed.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `autosoft`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `autosoft`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `vehicle_images`;
DROP TABLE IF EXISTS `leads`;
DROP TABLE IF EXISTS `vehicles`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `brands`;
DROP TABLE IF EXISTS `admin_users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
--  Utilizadores do painel administrativo
-- ---------------------------------------------------------------------
CREATE TABLE `admin_users` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(120)  NOT NULL,
  `email`         VARCHAR(180)  NOT NULL,
  `password_hash` VARCHAR(255)  NOT NULL,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admin_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Marcas (Toyota, Hyundai, ...)
-- ---------------------------------------------------------------------
CREATE TABLE `brands` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `slug`       VARCHAR(140) NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_brand_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Categorias (SUV, Pick-up, Sedan, Citadino, ...)
-- ---------------------------------------------------------------------
CREATE TABLE `categories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120) NOT NULL,
  `slug`        VARCHAR(140) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_category_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Viaturas (estoque)
-- ---------------------------------------------------------------------
CREATE TABLE `vehicles` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `brand_id`     INT UNSIGNED NOT NULL,
  `category_id`  INT UNSIGNED DEFAULT NULL,
  `model`        VARCHAR(140) NOT NULL,
  `version`      VARCHAR(180) DEFAULT NULL,
  `slug`         VARCHAR(220) NOT NULL,
  `year`         VARCHAR(12)  DEFAULT NULL,        -- ex.: 2023/2023
  `km`           INT UNSIGNED NOT NULL DEFAULT 0,
  `fuel`         VARCHAR(40)  DEFAULT NULL,        -- Gasolina / Gasóleo / Híbrida
  `transmission` VARCHAR(40)  DEFAULT NULL,        -- Manual / Automática
  `power`        VARCHAR(40)  DEFAULT NULL,        -- ex.: 204 cv
  `color`        VARCHAR(60)  DEFAULT NULL,
  `price`        BIGINT UNSIGNED NOT NULL DEFAULT 0, -- em Kwanza (inteiro)
  `installment`  VARCHAR(120) DEFAULT NULL,        -- ex.: ou 48x de Kz 870.000
  `location`     VARCHAR(80)  DEFAULT NULL,
  `condition`    ENUM('novo','seminova') NOT NULL DEFAULT 'seminova',
  `badges`       VARCHAR(180) DEFAULT NULL,        -- separadas por vírgula: 4x4,Blindada
  `description`  TEXT         DEFAULT NULL,
  `status`       ENUM('disponivel','reservado','vendido') NOT NULL DEFAULT 'disponivel',
  `featured`     TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_vehicle_slug` (`slug`),
  KEY `idx_vehicle_brand` (`brand_id`),
  KEY `idx_vehicle_category` (`category_id`),
  KEY `idx_vehicle_status` (`status`),
  CONSTRAINT `fk_vehicle_brand` FOREIGN KEY (`brand_id`)
      REFERENCES `brands` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vehicle_category` FOREIGN KEY (`category_id`)
      REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Fotografias das viaturas
-- ---------------------------------------------------------------------
CREATE TABLE `vehicle_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `path`       VARCHAR(255) NOT NULL,             -- relativo a /uploads
  `sort`       INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_image_vehicle` (`vehicle_id`),
  CONSTRAINT `fk_image_vehicle` FOREIGN KEY (`vehicle_id`)
      REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Pedidos de interesse / contactos (leads)
-- ---------------------------------------------------------------------
CREATE TABLE `leads` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vehicle_id` INT UNSIGNED DEFAULT NULL,
  `name`       VARCHAR(140) NOT NULL,
  `phone`      VARCHAR(60)  NOT NULL,
  `email`      VARCHAR(180) DEFAULT NULL,
  `message`    TEXT         DEFAULT NULL,
  `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lead_vehicle` (`vehicle_id`),
  CONSTRAINT `fk_lead_vehicle` FOREIGN KEY (`vehicle_id`)
      REFERENCES `vehicles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
