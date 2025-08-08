/*
 Navicat Premium Dump SQL

 Source Server         : LOCALHOST
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : adamasanya

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 07/08/2025 02:01:58
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for promo_branches
-- ----------------------------
DROP TABLE IF EXISTS `promo_branches`;
CREATE TABLE `promo_branches`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `promo_id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `promo_branches_promo_id_branch_id_unique`(`promo_id` ASC, `branch_id` ASC) USING BTREE,
  INDEX `promo_branches_branch_id_foreign`(`branch_id` ASC) USING BTREE,
  CONSTRAINT `promo_branches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `promo_branches_promo_id_foreign` FOREIGN KEY (`promo_id`) REFERENCES `promos` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of promo_branches
-- ----------------------------

-- ----------------------------
-- Table structure for promo_categories
-- ----------------------------
DROP TABLE IF EXISTS `promo_categories`;
CREATE TABLE `promo_categories`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `promo_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `promo_categories_promo_id_category_id_unique`(`promo_id` ASC, `category_id` ASC) USING BTREE,
  INDEX `promo_categories_category_id_foreign`(`category_id` ASC) USING BTREE,
  CONSTRAINT `promo_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `promo_categories_promo_id_foreign` FOREIGN KEY (`promo_id`) REFERENCES `promos` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of promo_categories
-- ----------------------------

-- ----------------------------
-- Table structure for promo_products
-- ----------------------------
DROP TABLE IF EXISTS `promo_products`;
CREATE TABLE `promo_products`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `promo_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `promo_products_promo_id_product_id_unique`(`promo_id` ASC, `product_id` ASC) USING BTREE,
  INDEX `promo_products_product_id_foreign`(`product_id` ASC) USING BTREE,
  CONSTRAINT `promo_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `promo_products_promo_id_foreign` FOREIGN KEY (`promo_id`) REFERENCES `promos` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of promo_products
-- ----------------------------

-- ----------------------------
-- Table structure for promos
-- ----------------------------
DROP TABLE IF EXISTS `promos`;
CREATE TABLE `promos`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `type` enum('percentage','fixed_amount','buy_x_get_y','free_shipping') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10, 2) NULL DEFAULT NULL,
  `buy_quantity` int NULL DEFAULT NULL,
  `get_quantity` int NULL DEFAULT NULL,
  `free_product_id` bigint UNSIGNED NULL DEFAULT NULL,
  `min_order_amount` decimal(12, 2) NULL DEFAULT NULL,
  `max_uses` int NULL DEFAULT NULL,
  `max_uses_per_user` int NULL DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `scope` enum('all','products','categories','branches') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `day_restriction` enum('all','weekday','weekend') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'all',
  `applicable_for` enum('all','rent','sale') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'all',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `promos_code_unique`(`code` ASC) USING BTREE,
  INDEX `promos_free_product_id_foreign`(`free_product_id` ASC) USING BTREE,
  INDEX `promos_code_is_active_start_date_end_date_index`(`code` ASC, `is_active` ASC, `start_date` ASC, `end_date` ASC) USING BTREE,
  CONSTRAINT `promos_free_product_id_foreign` FOREIGN KEY (`free_product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of promos
-- ----------------------------
INSERT INTO `promos` VALUES (1, 'Sewa 2 Hari Gratis 1 Hari', 'sewa-2-hari-gratis-1-hari', 'ADAMASANYAJULI', 'Sewa 2 hari Gratis 1 hari <br/> <ul>Syarat dan Ketentuan : <li>Repost Postingan DPO</li></ul>', 'buy_x_get_y', 1.00, 2, 1, NULL, 1.00, NULL, NULL, '2025-07-01 00:00:01', '2025-07-31 23:59:59', 1, 'all', 'all', 'rent', '2025-07-01 00:00:01', NULL, NULL);
INSERT INTO `promos` VALUES (2, 'Diskon HUT RI 80', 'diskon-hut-ri-80', 'HUTRI80', 'Diskon 30% <br/> <ul>Syarat dan Ketentuan : <li> Pengambilan : khusus Weekday</li></ul>', 'percentage', 30.00, 1, NULL, NULL, 1.00, NULL, NULL, '2025-08-01 00:00:01', '2025-08-30 23:59:59', 1, 'all', 'weekday', 'rent', '2025-08-01 00:00:01', NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
