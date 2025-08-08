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

 Date: 07/08/2025 18:28:49
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for payment_rents
-- ----------------------------
DROP TABLE IF EXISTS `payment_rents`;
CREATE TABLE `payment_rents`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `rent_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `midtrans_order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `va_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `transaction_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `gross_amount` decimal(15, 2) NOT NULL,
  `payment_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `settlement_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `payments_midtrans_order_id_unique`(`midtrans_order_id` ASC) USING BTREE,
  INDEX `payments_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `payment_rents_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of payment_rents
-- ----------------------------
INSERT INTO `payment_rents` VALUES (1, 1, 21, 'RENT-1-1754509308', NULL, NULL, NULL, 'completed', NULL, 256000.00, NULL, NULL, '2025-08-07 02:40:32', '2025-08-07 02:41:53');
INSERT INTO `payment_rents` VALUES (3, 6, 21, 'RENT-6-1754564665', NULL, NULL, NULL, 'pending', NULL, 184000.00, NULL, NULL, '2025-08-07 18:04:25', '2025-08-07 18:04:25');

-- ----------------------------
-- Table structure for payment_sales
-- ----------------------------
DROP TABLE IF EXISTS `payment_sales`;
CREATE TABLE `payment_sales`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `midtrans_order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `va_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `transaction_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `gross_amount` decimal(15, 2) NOT NULL,
  `payment_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `settlement_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `payments_midtrans_order_id_unique`(`midtrans_order_id` ASC) USING BTREE,
  INDEX `payments_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `payment_sales_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of payment_sales
-- ----------------------------

-- ----------------------------
-- Table structure for product_branches
-- ----------------------------
DROP TABLE IF EXISTS `product_branches`;
CREATE TABLE `product_branches`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `color_id` tinyint NULL DEFAULT NULL,
  `storage_id` tinyint NULL DEFAULT NULL,
  `rent_price` decimal(12, 0) NOT NULL,
  `sale_price` decimal(12, 0) NOT NULL DEFAULT 0,
  `icloud` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `imei` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_publish` tinyint(1) NOT NULL DEFAULT 1,
  `views` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `product_branches_branch_id_foreign`(`branch_id` ASC) USING BTREE,
  INDEX `product_branches_product_id_foreign`(`product_id` ASC) USING BTREE,
  CONSTRAINT `product_branches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `product_branches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_branches
-- ----------------------------
INSERT INTO `product_branches` VALUES (1, 2, 116, 46, 47, 90000, 0, 'icloud.com', '1', 1, 0, '2025-08-05 00:22:19', '2025-08-05 00:22:19');
INSERT INTO `product_branches` VALUES (2, 2, 119, 62, 68, 140000, 0, 'icloud.com', '1', 1, 0, '2025-08-05 00:22:54', '2025-08-05 00:22:54');
INSERT INTO `product_branches` VALUES (3, 2, 120, 71, 76, 160000, 0, 'icloud.com', '1', 1, 0, '2025-08-05 00:26:16', '2025-08-05 00:26:16');
INSERT INTO `product_branches` VALUES (4, 3, 116, 41, 47, 90000, 0, 'icloud.com', '1', 1, 0, '2025-08-05 00:27:52', '2025-08-05 00:27:52');
INSERT INTO `product_branches` VALUES (5, 3, 116, 42, 47, 90000, 0, 'icloud.com', '1', 1, 0, '2025-08-05 00:32:53', '2025-08-05 00:32:53');
INSERT INTO `product_branches` VALUES (6, 3, 119, 62, 68, 140000, 0, 'icloud.com', '1', 1, 0, '2025-08-05 00:39:08', '2025-08-05 00:39:08');
INSERT INTO `product_branches` VALUES (7, 3, 120, 71, 75, 160000, 0, 'icloud.com', '1', 1, 0, '2025-08-05 00:40:02', '2025-08-05 00:40:02');
INSERT INTO `product_branches` VALUES (8, 3, 121, 79, 82, 170000, 0, 'icloud.com', '1', 1, 0, '2025-08-05 00:41:01', '2025-08-05 00:41:01');
INSERT INTO `product_branches` VALUES (9, 3, 122, 89, 91, 170000, 0, 'icloud.com', '1', 1, 0, '2025-08-05 00:42:21', '2025-08-05 00:42:21');
INSERT INTO `product_branches` VALUES (10, 3, 125, 110, 114, 200000, 0, 'icloud.com', '1', 1, 0, '2025-08-05 00:50:28', '2025-08-05 00:50:28');
INSERT INTO `product_branches` VALUES (11, 3, 129, 1, 1, 250000, 0, NULL, NULL, 1, 0, NULL, NULL);
INSERT INTO `product_branches` VALUES (12, 3, 130, NULL, NULL, 250000, 0, NULL, NULL, 1, 0, NULL, NULL);
INSERT INTO `product_branches` VALUES (13, 3, 130, NULL, NULL, 250000, 0, NULL, NULL, 1, 0, NULL, NULL);
INSERT INTO `product_branches` VALUES (14, 3, 130, NULL, NULL, 250000, 0, NULL, NULL, 1, 0, NULL, NULL);
INSERT INTO `product_branches` VALUES (15, 3, 130, NULL, NULL, 250000, 0, NULL, NULL, 1, 0, NULL, NULL);
INSERT INTO `product_branches` VALUES (16, 4, 113, 19, 25, 50000, 0, 'adamasanya.buahbatu@icloud.com', '1', 1, 0, '2025-08-05 00:52:59', '2025-08-05 00:59:14');
INSERT INTO `product_branches` VALUES (17, 4, 116, 42, 47, 90000, 0, 'adamasanya.buahbatu@icloud.com', '1', 1, 0, '2025-08-05 00:53:29', '2025-08-05 00:53:29');
INSERT INTO `product_branches` VALUES (18, 4, 116, 44, 47, 90000, 0, 'adamasanya.buahbatu@icloud.com', '1', 1, 0, '2025-08-05 00:55:35', '2025-08-05 00:55:35');
INSERT INTO `product_branches` VALUES (19, 4, 116, 45, 47, 90000, 0, 'adamasanya.buahbatu@icloud.com', '1', 1, 0, '2025-08-05 00:56:13', '2025-08-05 00:56:13');

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `brand_id` bigint UNSIGNED NULL DEFAULT NULL,
  `category_id` bigint UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description_rent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `products_slug_unique`(`slug` ASC) USING BTREE,
  UNIQUE INDEX `products_code_unique`(`code` ASC) USING BTREE,
  INDEX `products_brand_id_foreign`(`brand_id` ASC) USING BTREE,
  INDEX `products_category_id_foreign`(`category_id` ASC) USING BTREE,
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 363 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of products
-- ----------------------------
INSERT INTO `products` VALUES (1, 11, 1, 'Osmo Pocket 1', 'dji-osmo-pocket-1', 'D0001', 'dji-osmo-pocket-1.png', 'Include: ✓ DJI Osmo Pocket 1', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (2, 11, 1, 'Osmo Pocket 2', 'dji-osmo-pocket-2', 'D0002', 'dji-osmo-pocket-2.png', 'Include: ✓ DJI Osmo Pocket 2', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (3, 11, 1, 'Osmo Pocket 3 Combo', 'dji-osmo-pocket-3-combo', 'D0003', 'dji-osmo-pocket-3-combo.png', 'Include: ✓ DJI Osmo Pocket 3 Combo', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (4, 17, 1, 'Hero 11', 'go-pro-hero-11', 'G0001', 'go-pro-hero-11.png', 'Include: ✓ Go Pro Hero 11', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (5, 17, 1, 'Hero 8', 'go-pro-hero-8', 'G0002', 'go-pro-hero-8.png', 'Include: ✓ Go Pro Hero 8', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (6, 17, 1, 'Hero 9', 'go-pro-hero-9', 'G0003', 'go-pro-hero-9.png', 'Include: ✓ Go Pro Hero 9', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (7, 17, 1, 'Max', 'go-pro-max', 'G0004', 'go-pro-max.png', 'Include: ✓ Go Pro Max', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (8, 19, 1, 'Nano S', 'insta360-nano-s', 'I0001', 'insta360-nano-s.png', 'Include: ✓ Go Pro Nano S', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (9, 19, 1, 'One X', 'insta360-one-x', 'I0002', 'insta360-one-x.png', 'Include: ✓ Go Pro One X', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (10, 19, 1, 'One X3', 'insta360-one-x3', 'I0003', 'insta360-one-x3.png', 'Include: ✓ Go Pro One X3', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (11, 19, 1, 'One X4', 'insta360-x4', 'I0004', 'insta360-x4.png', 'Include: ✓ Go Pro One X4', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (12, 19, 1, 'Waterproof One X3', 'insta360-waterproof-one-x3', 'I0005', 'insta360-waterproof-one-x3.png', 'Include: ✓ Insta 360 Waterproof One X3', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (13, 7, 2, 'Adapter EF-FX1', 'canon-adapter-ef-fx1', 'C0001', 'canon-adapter-ef-fx1.png', 'Include: ✓ Canon EF-FX1 Adapter', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (14, 7, 2, 'EF to Eos M', 'canon-ef-to-eos-m', 'C0002', 'canon-ef-to-eos-m', 'Include: ✓ EF to EOS M Mount Adapter', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (15, 7, 2, 'Eos R', 'canon-eos-r-adapter', 'C0003', 'canon-eos-r-adapter', 'Include: ✓ EOS R Mount Adapter', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (16, 8, 2, 'Eos R', 'comlite-eos-r-adapter', 'CM0001', 'comlite-eos-r-adapter', 'Include: ✓ EOS R Mount Adapter by Comlite', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (17, 25, 2, 'Adapter FIZ', 'nikon-adapter-fiz', 'N0001', 'nikon-adapter-fiz', 'Include: ✓ Nikon FIZ Adapter', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (18, 27, 2, 'Adapter IV (Canon to Sony)', 'procore-adapter-iv-canon-to-sony', 'P0001', 'procore-adapter-iv-canon-to-sony', 'Include: ✓ Canon to Sony Adapter IV', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (19, 41, 2, 'EF to Z2 (body Nikon to Canon)', 'viltrox-ef-to-z2', 'V0001', 'viltrox-ef-to-z2', 'Include: ✓ EF to Z2 Adapter (Nikon to Canon)', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (20, 20, 3, 'Baterai Kamera', 'kingma-baterai-kamera', 'K0001', 'kingma-baterai-kamera', 'Include: ✓ Camera Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (21, 35, 3, 'Baterai NP-FZ100', 'sony-baterai-np-fz100', 'S0001', 'sony-baterai-np-fz100', 'Include: ✓ NP-FZ100 Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (22, 7, 4, '100D kit', 'canon-100d-kit', 'C0004', 'canon-100d-kit', 'Include: ✓ Canon 100D Body ✓ Kit Lens ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (23, 7, 4, '1100D kit', 'canon-1100d-kit', 'C0005', 'canon-1100d-kit', 'Include: ✓ Canon 1100D Body ✓ Kit Lens ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (24, 7, 4, '1200D kit', 'canon-1200d-kit', 'C0006', 'canon-1200d-kit', 'Include: ✓ Canon 1200D Body ✓ Kit Lens ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (25, 7, 4, '1300D kit', 'canon-1300d-kit', 'C0007', 'canon-1300d-kit', 'Include: ✓ Canon 1300D Body ✓ Kit Lens ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (26, 7, 4, '200D kit', 'canon-200d-kit', 'C0008', 'canon-200d-kit', 'Include: ✓ Canon 200D Body ✓ Kit Lens ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (27, 7, 4, '3000D kit', 'canon-3000d-kit', 'C0009', 'canon-3000d-kit', 'Include: ✓ Canon 3000D Body ✓ Kit Lens ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (28, 7, 4, '550D kit', 'canon-550d-kit', 'C0010', 'canon-550d-kit', 'Include: ✓ Canon 550D Body ✓ Kit Lens ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (29, 7, 4, '5D Mark II BO', 'canon-5d-mark-ii-bo', 'C0011', 'canon-5d-mark-ii-bo', 'Include: ✓ Canon 5D Mark II Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (30, 7, 4, '5D Mark III BO', 'canon-5d-mark-iii-bo', 'C0012', 'canon-5d-mark-iii-bo', 'Include: ✓ Canon 5D Mark III Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (31, 7, 4, '600D kit', 'canon-600d-kit', 'C0013', 'canon-600d-kit', 'Include: ✓ Canon 600D Body ✓ Kit Lens ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (32, 7, 4, '60D kit', 'canon-60d-kit', 'C0014', 'canon-60d-kit', 'Include: ✓ Canon 60D Body ✓ Kit Lens ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (33, 7, 4, '6D BO', 'canon-6d-bo', 'C0015', 'canon-6d-bo', 'Include: ✓ Canon 6D Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (34, 7, 4, '6D Mark II BO', 'canon-6d-mark-ii-bo', 'C0016', 'canon-6d-mark-ii-bo', 'Include: ✓ Canon 6D Mark II Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (35, 7, 4, '700D kit', 'canon-700d-kit', 'C0017', 'canon-700d-kit', 'Include: ✓ Canon 700D Body ✓ Kit Lens ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (36, 7, 4, '750D kit', 'canon-750d-kit', 'C0018', 'canon-750d-kit', 'Include: ✓ Canon 750D Body ✓ Kit Lens ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (37, 7, 4, '760D kit', 'canon-760d-kit', 'C0019', 'canon-760d-kit', 'Include: ✓ Canon 760D Body ✓ Kit Lens ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (38, 7, 4, '7D BO', 'canon-7d-bo', 'C0020', 'canon-7d-bo', 'Include: ✓ Canon 7D Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (39, 7, 4, '7D Mark II BO', 'canon-7d-mark-ii-bo', 'C0021', 'canon-7d-mark-ii-bo', 'Include: ✓ Canon 7D Mark II Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (40, 7, 4, '80D BO', 'canon-80d-bo', 'C0022', 'canon-80d-bo', 'Include: ✓ Canon 80D Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (41, 7, 4, 'Eos M2', 'canon-eos-m2', 'C0023', 'canon-eos-m2', 'Include: ✓ Canon EOS M2 Mirrorless Camera ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (42, 7, 4, 'Eos R BO', 'canon-eos-r-bo', 'C0024', 'canon-eos-r-bo', 'Include: ✓ Canon EOS R Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (43, 7, 4, 'Eos RP BO', 'canon-eos-rp-bo', 'C0025', 'canon-eos-rp-bo', 'Include: ✓ Canon EOS RP Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (44, 7, 4, 'G7x Mark II', 'canon-g7x-mark-ii', 'C0026', 'canon-g7x-mark-ii', 'Include: ✓ Canon G7X Mark II Compact Camera ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (45, 7, 4, 'M10 kit', 'canon-m10-kit', 'C0027', 'canon-m10-kit', 'Include: ✓ Canon M10 Mirrorless Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (46, 7, 4, 'M100 kit', 'canon-m100-kit', 'C0028', 'canon-m100-kit', 'Include: ✓ Canon M100 Mirrorless Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (47, 7, 4, 'M3 kit', 'canon-m3-kit', 'C0029', 'canon-m3-kit', 'Include: ✓ Canon M3 Mirrorless Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (48, 7, 4, 'M50 kit', 'canon-m50-kit', 'C0030', 'canon-m50-kit', 'Include: ✓ Canon M50 Mirrorless Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (49, 7, 4, 'M6 kit', 'canon-m6-kit', 'C0031', 'canon-m6-kit', 'Include: ✓ Canon M6 Mirrorless Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (50, 7, 4, 'SX410', 'canon-sx410', 'C0032', 'canon-sx410', 'Include: ✓ Canon SX410 Superzoom Camera ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (51, 14, 4, 'Instax Mini Evo', 'fujifilm-instax-mini-evo', 'F0001', 'fujifilm-instax-mini-evo', 'Include: ✓ Fujifilm Instax Mini Evo Instant Camera ✓ Film ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (52, 14, 4, 'X100T', 'fujifilm-x100t', 'F0002', 'fujifilm-x100t', 'Include: ✓ Fujifilm X100T Camera ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (53, 14, 4, 'XA 5', 'fujifilm-xa5', 'F0003', 'fujifilm-xa5', 'Include: ✓ Fujifilm XA5 Camera ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (54, 14, 4, 'XA-2 kit', 'fujifilm-xa2-kit', 'F0004', 'fujifilm-xa2-kit', 'Include: ✓ Fujifilm XA2 Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (55, 14, 4, 'XA-3 kit', 'fujifilm-xa3-kit', 'F0005', 'fujifilm-xa3-kit', 'Include: ✓ Fujifilm XA3 Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (56, 14, 4, 'XH-1 BO', 'fujifilm-xh1-bo', 'F0006', 'fujifilm-xh1-bo', 'Include: ✓ Fujifilm XH1 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (57, 14, 4, 'XM 1', 'fujifilm-xm1', 'F0007', 'fujifilm-xm1', 'Include: ✓ Fujifilm XM1 Camera ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (58, 14, 4, 'XS-10', 'fujifilm-xs10', 'F0008', 'fujifilm-xs10', 'Include: ✓ Fujifilm XS10 Camera ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (59, 14, 4, 'XT-1 BO', 'fujifilm-xt1-bo', 'F0009', 'fujifilm-xt1-bo', 'Include: ✓ Fujifilm XT1 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (60, 14, 4, 'XT-10 BO', 'fujifilm-xt10-bo', 'F0010', 'fujifilm-xt10-bo', 'Include: ✓ Fujifilm XT10 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (61, 14, 4, 'XT-100 kit', 'fujifilm-xt100-kit', 'F0011', 'fujifilm-xt100-kit', 'Include: ✓ Fujifilm XT100 Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (62, 14, 4, 'XT-2 BO', 'fujifilm-xt2-bo', 'F0012', 'fujifilm-xt2-bo', 'Include: ✓ Fujifilm XT2 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (63, 14, 4, 'XT-20 BO', 'fujifilm-xt20-bo', 'F0013', 'fujifilm-xt20-bo', 'Include: ✓ Fujifilm XT20 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (64, 14, 4, 'XT-200 BO', 'fujifilm-xt200-bo', 'F0014', 'fujifilm-xt200-bo', 'Include: ✓ Fujifilm XT200 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (65, 14, 4, 'XT-30 BO', 'fujifilm-xt30-bo', 'F0015', 'fujifilm-xt30-bo', 'Include: ✓ Fujifilm XT30 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (66, 14, 4, 'XT-4 BO', 'fujifilm-xt4-bo', 'F0016', 'fujifilm-xt4-bo', 'Include: ✓ Fujifilm XT4 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (67, 21, 4, 'Q', 'leica-q', 'L0001', 'leica-q', 'Include: ✓ Leica Q Camera ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (68, 22, 4, 'G7', 'lumix-g7', 'LM0001', 'lumix-g7', 'Include: ✓ Lumix G7 Camera ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (69, 22, 4, 'G85 KIT', 'lumix-g85-kit', 'LM0002', 'lumix-g85-kit', 'Include: ✓ Lumix G85 Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (70, 22, 4, 'G90', 'lumix-g90', 'LM0003', 'lumix-g90', 'Include: ✓ Lumix G90 Camera ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (71, 22, 4, 'GF9W', 'lumix-gf9w', 'LM0004', 'lumix-gf9w', 'Include: ✓ Lumix GF9W Camera ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (72, 25, 4, 'D3200 Kit APSC', 'nikon-d3200-kit-apsc', 'N0002', 'nikon-d3200-kit-apsc', 'Include: ✓ Nikon D3200 APS-C Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (73, 25, 4, 'D5300 Kit APSC', 'nikon-d5300-kit-apsc', 'N0003', 'nikon-d5300-kit-apsc', 'Include: ✓ Nikon D5300 APS-C Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (74, 25, 4, 'D5500 Kit APSC', 'nikon-d5500-kit-apsc', 'N0004', 'nikon-d5500-kit-apsc', 'Include: ✓ Nikon D5500 APS-C Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (75, 25, 4, 'D610 BO Full Frame', 'nikon-d610-bo-full-frame', 'N0005', 'nikon-d610-bo-full-frame', 'Include: ✓ Nikon D610 Full Frame Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (76, 25, 4, 'D700 BO Full Frame', 'nikon-d700-bo-full-frame', 'N0006', 'nikon-d700-bo-full-frame', 'Include: ✓ Nikon D700 Full Frame Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (77, 25, 4, 'D7000 BO APSC', 'nikon-d7000-bo-apsc', 'N0007', 'nikon-d7000-bo-apsc', 'Include: ✓ Nikon D7000 APS-C Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (78, 25, 4, 'D7100 BO APSC', 'nikon-d7100-bo-apsc', 'N0008', 'nikon-d7100-bo-apsc', 'Include: ✓ Nikon D7100 APS-C Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (79, 25, 4, 'D7200 BO APSC', 'nikon-d7200-bo-apsc', 'N0009', 'nikon-d7200-bo-apsc', 'Include: ✓ Nikon D7200 APS-C Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (80, 25, 4, 'D750 BO Full Frame', 'nikon-d750-bo-full-frame', 'N0010', 'nikon-d750-bo-full-frame', 'Include: ✓ Nikon D750 Full Frame Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (81, 25, 4, 'D7500 BO APSC', 'nikon-d7500-bo-apsc', 'N0011', 'nikon-d7500-bo-apsc', 'Include: ✓ Nikon D7500 APS-C Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (82, 25, 4, 'D800 BO Full Frame', 'nikon-d800-bo-full-frame', 'N0012', 'nikon-d800-bo-full-frame', 'Include: ✓ Nikon D800 Full Frame Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (83, 25, 4, 'Z5 Full Frame', 'nikon-z5-full-frame', 'N0013', 'nikon-z5-full-frame', 'Include: ✓ Nikon Z5 Full Frame Mirrorless ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (84, 25, 4, 'Z6 Full Frame', 'nikon-z6-full-frame', 'N0014', 'nikon-z6-full-frame', 'Include: ✓ Nikon Z6 Full Frame Mirrorless ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (85, 25, 4, 'Z7 Full Frame', 'nikon-z7-full-frame', 'N0015', 'nikon-z7-full-frame', 'Include: ✓ Nikon Z7 Full Frame Mirrorless ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (86, 35, 4, 'A5000 Kit', 'sony-a5000-kit', 'S0002', 'sony-a5000-kit', 'Include: ✓ Sony A5000 Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (87, 35, 4, 'A5100 Kit', 'sony-a5100-kit', 'S0003', 'sony-a5100-kit', 'Include: ✓ Sony A5100 Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (88, 35, 4, 'A6000 BO', 'sony-a6000-bo', 'S0004', 'sony-a6000-bo', 'Include: ✓ Sony A6000 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (89, 35, 4, 'A6000 Kit', 'sony-a6000-kit', 'S0005', 'sony-a6000-kit', 'Include: ✓ Sony A6000 Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (90, 35, 4, 'A6100 BO', 'sony-a6100-bo', 'S0006', 'sony-a6100-bo', 'Include: ✓ Sony A6100 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (91, 35, 4, 'A6300 BO', 'sony-a6300-bo', 'S0007', 'sony-a6300-bo', 'Include: ✓ Sony A6300 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (92, 35, 4, 'A6400 BO', 'sony-a6400-bo', 'S0008', 'sony-a6400-bo', 'Include: ✓ Sony A6400 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (93, 35, 4, 'A6500 BO', 'sony-a6500-bo', 'S0009', 'sony-a6500-bo', 'Include: ✓ Sony A6500 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (94, 35, 4, 'A6600 BO', 'sony-a6600-bo', 'S0010', 'sony-a6600-bo', 'Include: ✓ Sony A6600 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (95, 35, 4, 'A7 BO', 'sony-a7-bo', 'S0011', 'sony-a7-bo', 'Include: ✓ Sony A7 Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (96, 35, 4, 'A7 II BO', 'sony-a7-ii-bo', 'S0012', 'sony-a7-ii-bo', 'Include: ✓ Sony A7 II Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (97, 35, 4, 'A7 III BO', 'sony-a7-iii-bo', 'S0013', 'sony-a7-iii-bo', 'Include: ✓ Sony A7 III Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (98, 35, 4, 'A7 IV', 'sony-a7-iv', 'S0014', 'sony-a7-iv', 'Include: ✓ Sony A7 IV ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (99, 35, 4, 'A7 R IV', 'sony-a7-r-iv', 'S0015', 'sony-a7-r-iv', 'Include: ✓ Sony A7 R IV ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (100, 35, 4, 'A7C BO', 'sony-a7c-bo', 'S0016', 'sony-a7c-bo', 'Include: ✓ Sony A7C Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (101, 35, 4, 'A7S BO', 'sony-a7s-bo', 'S0017', 'sony-a7s-bo', 'Include: ✓ Sony A7S Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (102, 35, 4, 'A9 BO Full Frame', 'sony-a9-bo-full-frame', 'S0018', 'sony-a9-bo-full-frame', 'Include: ✓ Sony A9 Full Frame Body Only ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (103, 35, 4, 'Fx30', 'sony-fx30', 'S0019', 'sony-fx30', 'Include: ✓ Sony FX30 Cinema Camera ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (104, 35, 4, 'ZV-E10 kit', 'sony-zv-e10-kit', 'S0020', 'sony-zv-e10-kit', 'Include: ✓ Sony ZV-E10 Kit ✓ Battery ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (105, 20, 6, 'Charger', 'kingma-charger', 'K0002', 'kingma-charger', 'Include: ✓ Camera Battery Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (106, 11, 7, 'Baterai Tambahan', 'dji-baterai-tambahan', 'D0004', 'dji-baterai-tambahan', 'Include: ✓ Additional Battery for DJI Drones', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (107, 11, 7, 'Mini 3 Basic', 'dji-mini-3-basic', 'D0005', 'dji-mini-3-basic', 'Include: ✓ DJI Mini 3 Basic Drone ✓ Battery ✓ Controller', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (108, 11, 7, 'Mini 3 Pro', 'dji-mini-3-pro', 'D0006', 'dji-mini-3-pro', 'Include: ✓ DJI Mini 3 Pro Drone ✓ Battery ✓ Controller', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (109, 11, 7, 'Mini 4 Pro', 'dji-mini-4-pro', 'D0007', 'dji-mini-4-pro', 'Include: ✓ DJI Mini 4 Pro Drone ✓ Battery ✓ Controller', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (110, 11, 7, 'Spark Basic', 'dji-spark-basic', 'D0008', 'dji-spark-basic', 'Include: ✓ DJI Spark Basic Drone ✓ Battery ✓ Controller', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (111, 4, 8, 'iPhone 7', 'apple-iphone-7', 'A0001', 'apple-iphone-7', 'Include: ✓ iPhone 7 ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (112, 4, 8, 'iPhone 7+', 'apple-iphone-7-plus', 'A0002', 'apple-iphone-7-plus', 'Include: ✓ iPhone 7 ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (113, 4, 8, 'iPhone 8', 'apple-iphone-8', 'A0003', 'apple-iphone-8', 'Include: ✓ iPhone 8 ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (114, 4, 8, 'iPhone 8+', 'apple-iphone-8-plus', 'A0004', 'apple-iphone-8-plus', 'Include: ✓ iPhone 8 Plus ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (115, 4, 8, 'iPhone X', 'apple-iphone-x', 'A0005', 'apple-iphone-x', 'Include: ✓ iPhone X ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (116, 4, 8, 'iPhone Xr', 'apple-iphone-xr', 'A0006', 'apple-iphone-xr', 'Include: ✓ iPhone XR ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (117, 4, 8, 'iPhone Xs', 'apple-iphone-xs', 'A0007', 'apple-iphone-xs', 'Include: ✓ iPhone XS ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (118, 4, 8, 'iPhone Xs Max', 'apple-iphone-xs-max', 'A0008', 'apple-iphone-xs-max', 'Include: ✓ iPhone XS Max ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (119, 4, 8, 'iPhone 11', 'apple-iphone-11', 'A0009', 'apple-iphone-11', 'Include: ✓ iPhone 11 ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (120, 4, 8, 'iPhone 11 Pro', 'apple-iphone-11-pro', 'A0010', 'apple-iphone-11-pro', 'Include: ✓ iPhone 11 Pro ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (121, 4, 8, 'iPhone 11 Pro Max', 'apple-iphone-11-pro-max', 'A0011', 'apple-iphone-11-pro-max', 'Include: ✓ iPhone 11 Pro Max ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (122, 4, 8, 'iPhone 12', 'apple-iphone-12', 'A0012', 'apple-iphone-12', 'Include: ✓ iPhone 12 ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (123, 4, 8, 'iPhone 12 Mini', 'apple-iphone-12-mini', 'A0013', 'apple-iphone-12-mini', 'Include: ✓ iPhone 12 ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (124, 4, 8, 'iPhone 12 Pro', 'apple-iphone-12-pro', 'A0014', 'apple-iphone-12-pro', 'Include: ✓ iPhone 12 Pro ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (125, 4, 8, 'iPhone 12 Pro Max', 'apple-iphone-12-pro-max', 'A0015', 'apple-iphone-12-pro-max', 'Include: ✓ iPhone 12 Pro Max ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (126, 4, 8, 'iPhone 13', 'apple-iphone-13', 'A0016', 'apple-iphone-13', 'Include: ✓ iPhone 13 ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (127, 4, 8, 'iPhone 13 Mini', 'apple-iphone-13-mini', 'A0017', 'apple-iphone-13-mini', 'Include: ✓ iPhone 13 ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (128, 4, 8, 'iPhone 13 Pro', 'apple-iphone-13-pro', 'A0018', 'apple-iphone-13-pro', 'Include: ✓ iPhone 13 Pro ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (129, 4, 8, 'iPhone 13 Pro Max', 'apple-iphone-13-pro-max', 'A0019', 'apple-iphone-13-pro-max', 'Include: ✓ iPhone 13 Pro Max ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (130, 4, 8, 'iPhone 14', 'apple-iphone-14', 'A0020', 'apple-iphone-14', 'Include: ✓ iPhone 13 Pro Max ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (131, 4, 8, 'iPhone 14+', 'apple-iphone-14-plus', 'A0021', 'apple-iphone-14-plus', 'Include: ✓ iPhone 14 Plus ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (132, 4, 8, 'iPhone 14 Pro', 'apple-iphone-14-pro', 'A0022', 'apple-iphone-14-pro', 'Include: ✓ iPhone 14 Pro ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (133, 4, 8, 'iPhone 14 Pro Max', 'apple-iphone-14-pro-max', 'A0023', 'apple-iphone-14-pro-max', 'Include: ✓ iPhone 14 Pro Max ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (134, 4, 8, 'iPhone 15', 'apple-iphone-15', 'A0024', 'apple-iphone-15', 'Include: ✓ iPhone 15 Pro ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (135, 4, 8, 'iPhone 15 Plus', 'apple-iphone-15-plus', 'A0025', 'apple-iphone-15-plus', 'Include: ✓ iPhone 15 Pro ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (136, 4, 8, 'iPhone 15 Pro', 'apple-iphone-15-pro', 'A0026', 'apple-iphone-15-pro', 'Include: ✓ iPhone 15 Pro ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (137, 4, 8, 'iPhone 15 Promax', 'apple-iphone-15-promax', 'A0027', 'apple-iphone-15-promax', 'Include: ✓ iPhone 15 Pro ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (138, 4, 8, 'iPhone 16', 'apple-iphone-16', 'A0028', 'apple-iphone-16', 'Include: ✓ iPhone 16 ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (139, 4, 8, 'iPhone 16e', 'apple-iphone-16-e', 'A0029', 'apple-iphone-16-e', 'Include: ✓ iPhone 16 ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (140, 4, 8, 'iPhone 16 Pro', 'apple-iphone-16-pro', 'A0030', 'apple-iphone-16-pro', 'Include: ✓ iPhone 16 Pro ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (141, 4, 8, 'iPhone 16 Pro Max', 'apple-iphone-16-pro-max', 'A0031', 'apple-iphone-16-pro-max', 'Include: ✓ iPhone 16 Pro Max ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (142, 16, 8, 'Pixel', 'google-pixel', 'G0005', 'google-pixel', 'Include: ✓ Google Pixel Phone ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (143, 29, 8, 'A50s', 'samsung-a50s', 'SS0001', 'samsung-a50s', 'Include: ✓ Samsung A50s Phone ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (144, 29, 8, 'S22 Ultra', 'samsung-s22-ultra', 'SS0002', 'samsung-s22-ultra', 'Include: ✓ Samsung S22 Ultra Phone ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (145, 29, 8, 'S23 Ultra', 'samsung-s23-ultra', 'SS0003', 'samsung-s23-ultra', 'Include: ✓ Samsung S23 Ultra Phone ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (146, 29, 8, 'S24 Ultra', 'samsung-s24-ultra', 'SS0004', 'samsung-s24-ultra', 'Include: ✓ Samsung S24 Ultra Phone ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (147, 29, 8, 'S25 Ultra', 'samsung-s25-ultra', 'SS0005', 'samsung-s25-ultra', 'Include: ✓ Samsung S25 Ultra Phone ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (148, 32, 9, 'Skuter', 'segway-ninebot-skuter', 'SN0001', 'segway-ninebot-skuter', 'Include: ✓ Segway Ninebot Scooter ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (149, 44, 9, 'Motor Aerox ABS 2021', 'yamaha-aerox-abs-2021', 'Y0001', 'yamaha-aerox-abs-2021', 'Include: ✓ Yamaha Aerox ABS 2021 Motorcycle ✓ Key', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (150, 40, 10, 'Kursi Portable', 'tnw-kursi-portable', 'T0001', 'tnw-kursi-portable', 'Include: ✓ Portable Chair', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (151, 1, 12, '35mm F1.2', '7artisans-35mm-f1-2', '7A0001', '7artisans-35mm-f1-2', 'Include: ✓ 7Artisans 35mm F1.2 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (152, 6, 12, 'Star 50 F1.4', 'brightin-star-50mm-f1-4', 'BS0001', 'brightin-star-50mm-f1-4', 'Include: ✓ Brightin Star 50mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (153, 7, 12, '10-18mm F4.5 STM', 'canon-10-18mm-f4-5-stm', 'C0033', 'canon-10-18mm-f4-5-stm', 'Include: ✓ Canon 10-18mm F4.5 STM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (154, 7, 12, '100mm f.28 macro', 'canon-100mm-f2-8-macro', 'C0034', 'canon-100mm-f2-8-macro', 'Include: ✓ Canon 100mm F2.8 Macro Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (155, 7, 12, '135mm F2 L USM', 'canon-135mm-f2-l-usm', 'C0035', 'canon-135mm-f2-l-usm', 'Include: ✓ Canon 135mm F2 L USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (156, 7, 12, '15mm F2.8', 'canon-15mm-f2-8', 'C0036', 'canon-15mm-f2-8', 'Include: ✓ Canon 15mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (157, 7, 12, '16-35mm F2.8 L USM', 'canon-16-35mm-f2-8-l-usm', 'C0037', 'canon-16-35mm-f2-8-l-usm', 'Include: ✓ Canon 16-35mm F2.8 L USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (158, 7, 12, '17-40mm F4 L USM', 'canon-17-40mm-f4-l-usm', 'C0038', 'canon-17-40mm-f4-l-usm', 'Include: ✓ Canon 17-40mm F4 L USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (159, 7, 12, '18-135mm F3.5', 'canon-18-135mm-f3-5', 'C0039', 'canon-18-135mm-f3-5', 'Include: ✓ Canon 18-135mm F3.5 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (160, 7, 12, '20mm F2.8 USM', 'canon-20mm-f2-8-usm', 'C0040', 'canon-20mm-f2-8-usm', 'Include: ✓ Canon 20mm F2.8 USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (161, 7, 12, '22mm Eos M F.2', 'canon-22mm-eos-m-f2', 'C0041', 'canon-22mm-eos-m-f2', 'Include: ✓ Canon 22mm EOS M F2 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (162, 7, 12, '24-105mm F4 II USM', 'canon-24-105mm-f4-ii-usm', 'C0042', 'canon-24-105mm-f4-ii-usm', 'Include: ✓ Canon 24-105mm F4 II USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (163, 7, 12, '24-105mm F4 L USM', 'canon-24-105mm-f4-l-usm', 'C0043', 'canon-24-105mm-f4-l-usm', 'Include: ✓ Canon 24-105mm F4 L USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (164, 7, 12, '24-70mm F2.8', 'canon-24-70mm-f2-8', 'C0044', 'canon-24-70mm-f2-8', 'Include: ✓ Canon 24-70mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (165, 7, 12, '24-70mm F2.8 II', 'canon-24-70mm-f2-8-ii', 'C0045', 'canon-24-70mm-f2-8-ii', 'Include: ✓ Canon 24-70mm F2.8 II Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (166, 7, 12, '24-70mm F4 L USM', 'canon-24-70mm-f4-l-usm', 'C0046', 'canon-24-70mm-f4-l-usm', 'Include: ✓ Canon 24-70mm F4 L USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (167, 7, 12, '24mm F2.8 STM', 'canon-24mm-f2-8-stm', 'C0047', 'canon-24mm-f2-8-stm', 'Include: ✓ Canon 24mm F2.8 STM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (168, 7, 12, '28mm F1.8 USM', 'canon-28mm-f1-8-usm', 'C0048', 'canon-28mm-f1-8-usm', 'Include: ✓ Canon 28mm F1.8 USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (169, 7, 12, '35mm F2 i USM', 'canon-35mm-f2-i-usm', 'C0049', 'canon-35mm-f2-i-usm', 'Include: ✓ Canon 35mm F2 i USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (170, 7, 12, '50mm F1.2 L USM', 'canon-50mm-f1-2-l-usm', 'C0050', 'canon-50mm-f1-2-l-usm', 'Include: ✓ Canon 50mm F1.2 L USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (171, 7, 12, '50mm F1.8 STM', 'canon-50mm-f1-8-stm', 'C0051', 'canon-50mm-f1-8-stm', 'Include: ✓ Canon 50mm F1.8 STM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (172, 7, 12, '55-250mm F.4 IS II', 'canon-55-250mm-f4-is-ii', 'C0052', 'canon-55-250mm-f4-is-ii', 'Include: ✓ Canon 55-250mm F4 IS II Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (173, 7, 12, '60mm F2.8 USM', 'canon-60mm-f2-8-usm', 'C0053', 'canon-60mm-f2-8-usm', 'Include: ✓ Canon 60mm F2.8 USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (174, 7, 12, '70-200mm F2.8', 'canon-70-200mm-f2-8', 'C0054', 'canon-70-200mm-f2-8', 'Include: ✓ Canon 70-200mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (175, 7, 12, '70-200mm F4 II L USM', 'canon-70-200mm-f4-ii-l-usm', 'C0055', 'canon-70-200mm-f4-ii-l-usm', 'Include: ✓ Canon 70-200mm F4 II L USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (176, 7, 12, '70-200mm F4 L USM', 'canon-70-200mm-f4-l-usm', 'C0056', 'canon-70-200mm-f4-l-usm', 'Include: ✓ Canon 70-200mm F4 L USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (177, 7, 12, '75-300mm F4', 'canon-75-300mm-f4', 'C0057', 'canon-75-300mm-f4', 'Include: ✓ Canon 75-300mm F4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (178, 7, 12, '75-300mm USM III F4', 'canon-75-300mm-usm-iii-f4', 'C0058', 'canon-75-300mm-usm-iii-f4', 'Include: ✓ Canon 75-300mm USM III F4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (179, 7, 12, '85mm F1.2 II', 'canon-85mm-f1-2-ii', 'C0059', 'canon-85mm-f1-2-ii', 'Include: ✓ Canon 85mm F1.2 II Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (180, 7, 12, '85mm F1.8 USM', 'canon-85mm-f1-8-usm', 'C0060', 'canon-85mm-f1-8-usm', 'Include: ✓ Canon 85mm F1.8 USM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (181, 7, 12, 'RF 50mm F1.8', 'canon-rf-50mm-f1-8', 'C0061', 'canon-rf-50mm-f1-8', 'Include: ✓ Canon RF 50mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (182, 14, 12, '16mm F2.8', 'fujifilm-16mm-f2-8', 'F0017', 'fujifilm-16mm-f2-8', 'Include: ✓ Fujifilm 16mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (183, 14, 12, '18-55mm F2.8', 'fujifilm-18-55mm-f2-8', 'F0018', 'fujifilm-18-55mm-f2-8', 'Include: ✓ Fujifilm 18-55mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (184, 14, 12, '23mm F1.4', 'fujifilm-23mm-f1-4', 'F0019', 'fujifilm-23mm-f1-4', 'Include: ✓ Fujifilm 23mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (185, 14, 12, '23mm F2', 'fujifilm-23mm-f2', 'F0020', 'fujifilm-23mm-f2', 'Include: ✓ Fujifilm 23mm F2 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (186, 14, 12, '35mm F1.4', 'fujifilm-35mm-f1-4', 'F0021', 'fujifilm-35mm-f1-4', 'Include: ✓ Fujifilm 35mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (187, 14, 12, '35mm F2', 'fujifilm-35mm-f2', 'F0022', 'fujifilm-35mm-f2', 'Include: ✓ Fujifilm 35mm F2 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (188, 14, 12, '50mm F2', 'fujifilm-50mm-f2', 'F0023', 'fujifilm-50mm-f2', 'Include: ✓ Fujifilm 50mm F2 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (189, 14, 12, '55-200mm XF F3.5', 'fujifilm-55-200mm-xf-f3-5', 'F0024', 'fujifilm-55-200mm-xf-f3-5', 'Include: ✓ Fujifilm 55-200mm XF F3.5 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (190, 14, 12, '56mm F1.2', 'fujifilm-56mm-f1-2', 'F0025', 'fujifilm-56mm-f1-2', 'Include: ✓ Fujifilm 56mm F1.2 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (191, 22, 12, '25mm F1.7', 'lumix-25mm-f1-7', 'LM0005', 'lumix-25mm-f1-7', 'Include: ✓ Lumix 25mm F1.7 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (192, 22, 12, '45-150mm F.4', 'lumix-45-150mm-f4', 'LM0006', 'lumix-45-150mm-f4', 'Include: ✓ Lumix 45-150mm F4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (193, 23, 12, '85mm F1.8', 'meike-85mm-f1-8', 'MK0001', 'meike-85mm-f1-8', 'Include: ✓ Meike 85mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (194, 25, 12, '105mm F2.8 N', 'nikon-105mm-f2-8-n', 'N0016', 'nikon-105mm-f2-8-n', 'Include: ✓ Nikon 105mm F2.8 N Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (195, 25, 12, '14-24mm F2.8 N Full Frame', 'nikon-14-24mm-f2-8-n-full-frame', 'N0017', 'nikon-14-24mm-f2-8-n-full-frame', 'Include: ✓ Nikon 14-24mm F2.8 N Full Frame Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (196, 25, 12, '16-35mm F4 N Full Frame', 'nikon-16-35mm-f4-n-full-frame', 'N0018', 'nikon-16-35mm-f4-n-full-frame', 'Include: ✓ Nikon 16-35mm F4 N Full Frame Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (197, 25, 12, '17-35mm F2.8 AF-S', 'nikon-17-35mm-f2-8-af-s', 'N0019', 'nikon-17-35mm-f2-8-af-s', 'Include: ✓ Nikon 17-35mm F2.8 AF-S Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (198, 25, 12, '18-200mm F3.5 DX APSC', 'nikon-18-200mm-f3-5-dx-apsc', 'N0020', 'nikon-18-200mm-f3-5-dx-apsc', 'Include: ✓ Nikon 18-200mm F3.5 DX APS-C Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (199, 25, 12, '18-200mm F3.5 VR', 'nikon-18-200mm-f3-5-vr', 'N0021', 'nikon-18-200mm-f3-5-vr', 'Include: ✓ Nikon 18-200mm F3.5 VR Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (200, 25, 12, '200mm F2 VR Full Frame', 'nikon-200mm-f2-vr-full-frame', 'N0022', 'nikon-200mm-f2-vr-full-frame', 'Include: ✓ Nikon 200mm F2 VR Full Frame Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (201, 25, 12, '24-70mm F2.8 N Full Frame', 'nikon-24-70mm-f2-8-n-full-frame', 'N0023', 'nikon-24-70mm-f2-8-n-full-frame', 'Include: ✓ Nikon 24-70mm F2.8 N Full Frame Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (202, 25, 12, '24mm F1.8 Nano', 'nikon-24mm-f1-8-nano', 'N0024', 'nikon-24mm-f1-8-nano', 'Include: ✓ Nikon 24mm F1.8 Nano Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (203, 25, 12, '28mm F1.8 Nano', 'nikon-28mm-f1-8-nano', 'N0025', 'nikon-28mm-f1-8-nano', 'Include: ✓ Nikon 28mm F1.8 Nano Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (204, 25, 12, '28mm F2.8 AF-D', 'nikon-28mm-f2-8-af-d', 'N0026', 'nikon-28mm-f2-8-af-d', 'Include: ✓ Nikon 28mm F2.8 AF-D Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (205, 25, 12, '300mm F2.8 N Full Frame', 'nikon-300mm-f2-8-n-full-frame', 'N0027', 'nikon-300mm-f2-8-n-full-frame', 'Include: ✓ Nikon 300mm F2.8 N Full Frame Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (206, 25, 12, '35mm F1.8 APSC', 'nikon-35mm-f1-8-apsc', 'N0028', 'nikon-35mm-f1-8-apsc', 'Include: ✓ Nikon 35mm F1.8 APS-C Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (207, 25, 12, '50mm F1.4 AF-S', 'nikon-50mm-f1-4-af-s', 'N0029', 'nikon-50mm-f1-4-af-s', 'Include: ✓ Nikon 50mm F1.4 AF-S Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (208, 25, 12, '60mm F2.8 N APSC', 'nikon-60mm-f2-8-n-apsc', 'N0030', 'nikon-60mm-f2-8-n-apsc', 'Include: ✓ Nikon 60mm F2.8 N APS-C Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (209, 25, 12, '70-200mm F2.8 VR', 'nikon-70-200mm-f2-8-vr', 'N0031', 'nikon-70-200mm-f2-8-vr', 'Include: ✓ Nikon 70-200mm F2.8 VR Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (210, 25, 12, '80-200mm F2.8 Gen III', 'nikon-80-200mm-f2-8-gen-iii', 'N0032', 'nikon-80-200mm-f2-8-gen-iii', 'Include: ✓ Nikon 80-200mm F2.8 Gen III Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (211, 25, 12, '85mm F1.8 AFD', 'nikon-85mm-f1-8-afd', 'N0033', 'nikon-85mm-f1-8-afd', 'Include: ✓ Nikon 85mm F1.8 AFD Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (212, 30, 12, '24-70 F2.8', 'samyang-24-70-f2-8', 'SY0001', 'samyang-24-70-f2-8', 'Include: ✓ Samyang 24-70 F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (213, 30, 12, '24mm F2.8', 'samyang-24mm-f2-8', 'SY0002', 'samyang-24mm-f2-8', 'Include: ✓ Samyang 24mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (214, 30, 12, '24mm V-AF F1.8', 'samyang-24mm-v-af-f1-8', 'SY0003', 'samyang-24mm-v-af-f1-8', 'Include: ✓ Samyang 24mm V-AF F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (215, 30, 12, '35mm F1.4', 'samyang-35mm-f1-4', 'SY0004', 'samyang-35mm-f1-4', 'Include: ✓ Samyang 35mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (216, 30, 12, '35mm F1.4 manual', 'samyang-35mm-f1-4-manual', 'SY0005', 'samyang-35mm-f1-4-manual', 'Include: ✓ Samyang 35mm F1.4 Manual Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (217, 30, 12, '35mm F1.8', 'samyang-35mm-f1-8', 'SY0006', 'samyang-35mm-f1-8', 'Include: ✓ Samyang 35mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (218, 30, 12, '7.5mm F3.5', 'samyang-7-5mm-f3-5', 'SY0007', 'samyang-7-5mm-f3-5', 'Include: ✓ Samyang 7.5mm F3.5 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (219, 30, 12, '75mm V-AF F1.8', 'samyang-75mm-v-af-f1-8', 'SY0008', 'samyang-75mm-v-af-f1-8', 'Include: ✓ Samyang 75mm V-AF F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (220, 30, 12, '8mm F2.8', 'samyang-8mm-f2-8', 'SY0009', 'samyang-8mm-f2-8', 'Include: ✓ Samyang 8mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (221, 30, 12, '8mm F3.5', 'samyang-8mm-f3-5', 'SY0010', 'samyang-8mm-f3-5', 'Include: ✓ Samyang 8mm F3.5 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (222, 33, 12, '150-500mm F4', 'sigma-150-500mm-f4', 'SG0001', 'sigma-150-500mm-f4', 'Include: ✓ Sigma 150-500mm F4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (223, 33, 12, '16mm F1.4 Fujifilm', 'sigma-16mm-f1-4-fujifilm', 'SG0002', 'sigma-16mm-f1-4-fujifilm', 'Include: ✓ Sigma 16mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (224, 33, 12, '16mm F1.4 Sony', 'sigma-16mm-f1-4-sony', 'SG0003', 'sigma-16mm-f1-4-sony', 'Include: ✓ Sigma 16mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (225, 33, 12, '16mm F1.4 (Eos M)', 'sigma-16mm-f1-4-eos-m', 'SG0004', 'sigma-16mm-f1-4-eos-m', 'Include: ✓ Sigma 16mm F1.4 (Eos M) Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (226, 33, 12, '16mm F1.4 Lumix', 'sigma-16mm-f1-4-lumix', 'SG0005', 'sigma-16mm-f1-4-lumix', 'Include: ✓ Sigma 16mm F1.4 Lumix Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (227, 33, 12, '17–50mm F2.8 APSC', 'sigma-17-50mm-f2-8-apsc', 'SG0006', 'sigma-17-50mm-f2-8-apsc', 'Include: ✓ Sigma 17-50mm F2.8 APS-C Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (228, 33, 12, '18–35mm F1.8 APSC', 'sigma-18-35mm-f1-8-apsc', 'SG0007', 'sigma-18-35mm-f1-8-apsc', 'Include: ✓ Sigma 18-35mm F1.8 APS-C Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (229, 33, 12, '19mm F2.8', 'sigma-19mm-f2-8', 'SG0008', 'sigma-19mm-f2-8', 'Include: ✓ Sigma 19mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (230, 33, 12, '24-35mm F2 Full Frame', 'sigma-24-35mm-f2-full-frame', 'SG0009', 'sigma-24-35mm-f2-full-frame', 'Include: ✓ Sigma 24-35mm F2 Full Frame Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (231, 33, 12, '24mm F1.8 APSC', 'sigma-24mm-f1-8-apsc', 'SG0010', 'sigma-24mm-f1-8-apsc', 'Include: ✓ Sigma 24mm F1.8 APS-C Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (232, 33, 12, '28-70 F2.8', 'sigma-28-70-f2-8', 'SG0011', 'sigma-28-70-f2-8', 'Include: ✓ Sigma 28-70 F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (233, 33, 12, '30mm F1.4', 'sigma-30mm-f1-4', 'SG0012', 'sigma-30mm-f1-4', 'Include: ✓ Sigma 30mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (234, 33, 12, '30mm F1.4', 'sigma-30mm-f1-4-sony', 'SG0013', 'sigma-30mm-f1-4-sony', 'Include: ✓ Sigma 30mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (235, 33, 12, '30mm F1.4 (Eos M)', 'sigma-30mm-f1-4-eos-m', 'SG0014', 'sigma-30mm-f1-4-eos-m', 'Include: ✓ Sigma 30mm F1.4 (Eos M) Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (236, 33, 12, '30mm F1.4 APSC', 'sigma-30mm-f1-4-apsc', 'SG0015', 'sigma-30mm-f1-4-apsc', 'Include: ✓ Sigma 30mm F1.4 APS-C Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (237, 33, 12, '30mm F1.4 art APSC', 'sigma-30mm-f1-4-art-apsc', 'SG0016', 'sigma-30mm-f1-4-art-apsc', 'Include: ✓ Sigma 30mm F1.4 art APS-C Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (238, 33, 12, '30mm F1.4 Lumix', 'sigma-30mm-f1-4-lumix', 'SG0017', 'sigma-30mm-f1-4-lumix', 'Include: ✓ Sigma 30mm F1.4 Lumix Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (239, 33, 12, '35mm art F1.4', 'sigma-35mm-art-f1-4', 'SG0018', 'sigma-35mm-art-f1-4', 'Include: ✓ Sigma 35mm art F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (240, 33, 12, '35mm F1.4 art', 'sigma-35mm-f1-4-art', 'SG0019', 'sigma-35mm-f1-4-art', 'Include: ✓ Sigma 35mm F1.4 art Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (241, 33, 12, '35mm F1.4 art Full Frame', 'sigma-35mm-f1-4-art-full-frame', 'SG0020', 'sigma-35mm-f1-4-art-full-frame', 'Include: ✓ Sigma 35mm F1.4 art Full Frame Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (242, 33, 12, '35mm F1.4 DGDN', 'sigma-35mm-f1-4-dgdn', 'SG0021', 'sigma-35mm-f1-4-dgdn', 'Include: ✓ Sigma 35mm F1.4 DGDN Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (243, 33, 12, '50-150mm F2.8 II Apo Nikon', 'sigma-50-150mm-f2-8-ii-apo-nikon', 'SG0022', 'sigma-50-150mm-f2-8-ii-apo-nikon', 'Include: ✓ Sigma 50-150mm F2.8 II Apo Nikon Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (244, 33, 12, '50-500mm F4 art Full Frame', 'sigma-50-500mm-f4-art-full-frame', 'SG0023', 'sigma-50-500mm-f4-art-full-frame', 'Include: ✓ Sigma 50-500mm F4 art Full Frame Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (245, 33, 12, '50–150mm F2.8', 'sigma-50-150mm-f2-8', 'SG0024', 'sigma-50-150mm-f2-8', 'Include: ✓ Sigma 50-150mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (246, 33, 12, '50mm F.14', 'sigma-50mm-f1-4', 'SG0025', 'sigma-50mm-f1-4', 'Include: ✓ Sigma 50mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (247, 33, 12, '50mm F1.4 ART Nikon', 'sigma-50mm-f1-4-art-nikon', 'SG0026', 'sigma-50mm-f1-4-art-nikon', 'Include: ✓ Sigma 50mm F1.4 ART Nikon Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (248, 33, 12, '56mm F1.4', 'sigma-56mm-f1-4', 'SG0027', 'sigma-56mm-f1-4', 'Include: ✓ Sigma 56mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (249, 33, 12, '70–300mm F4', 'sigma-70-300mm-f4', 'SG0028', 'sigma-70-300mm-f4', 'Include: ✓ Sigma 70-300mm F4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (250, 33, 12, '85mm F1.4', 'sigma-85mm-f1-4', 'SG0029', 'sigma-85mm-f1-4', 'Include: ✓ Sigma 85mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (251, 33, 12, '85mm F1.4 art', 'sigma-85mm-f1-4-art', 'SG0030', 'sigma-85mm-f1-4-art', 'Include: ✓ Sigma 85mm F1.4 art Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (252, 33, 12, '85mm F1.4 EX DG HSM', 'sigma-85mm-f1-4-ex-dg-hsm', 'SG0031', 'sigma-85mm-f1-4-ex-dg-hsm', 'Include: ✓ Sigma 85mm F1.4 EX DG HSM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (253, 33, 12, 'EX 85MM F1.4 DG HSM Nikon', 'sigma-ex-85mm-f1-4-dg-hsm-nikon', 'SG0032', 'sigma-ex-85mm-f1-4-dg-hsm-nikon', 'Include: ✓ Sigma EX 85MM F1.4 DG HSM Nikon Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (254, 34, 12, '23mm F1.2', 'sirui-23mm-f1-2', 'SR0001', 'sirui-23mm-f1-2', 'Include: ✓ Sirui 23mm F1.2 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (255, 35, 12, '10-18mm F4', 'sony-10-18mm-f4', 'S0021', 'sony-10-18mm-f4', 'Include: ✓ Sony 10-18mm F4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (256, 35, 12, '16-35mm F2.8 FE GM', 'sony-16-35mm-f2-8-fe-gm', 'S0022', 'sony-16-35mm-f2-8-fe-gm', 'Include: ✓ Sony 16-35mm F2.8 FE GM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (257, 35, 12, '18-135mm F3.5 OSS', 'sony-18-135mm-f3-5-oss', 'S0023', 'sony-18-135mm-f3-5-oss', 'Include: ✓ Sony 18-135mm F3.5 OSS Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (258, 35, 12, '24-240mm F3.5 FE', 'sony-24-240mm-f3-5-fe', 'S0024', 'sony-24-240mm-f3-5-fe', 'Include: ✓ Sony 24-240mm F3.5 FE Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (259, 35, 12, '24-70 F2.8 GM FE', 'sony-24-70-f2-8-gm-fe', 'S0025', 'sony-24-70-f2-8-gm-fe', 'Include: ✓ Sony 24-70 F2.8 GM FE Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (260, 35, 12, '24-70mm F2.8 II GM', 'sony-24-70mm-f2-8-ii-gm', 'S0026', 'sony-24-70mm-f2-8-ii-gm', 'Include: ✓ Sony 24-70mm F2.8 II GM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (261, 35, 12, '28-60mm', 'sony-28-60mm', 'S0027', 'sony-28-60mm', 'Include: ✓ Sony 28-60mm Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (262, 35, 12, '35mm F1.8 FE', 'sony-35mm-f1-8-fe', 'S0028', 'sony-35mm-f1-8-fe', 'Include: ✓ Sony 35mm F1.8 FE Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (263, 35, 12, '35mm F1.8 OSS', 'sony-35mm-f1-8-oss', 'S0029', 'sony-35mm-f1-8-oss', 'Include: ✓ Sony 35mm F1.8 OSS Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (264, 35, 12, '50mm F1.8 FE', 'sony-50mm-f1-8-fe', 'S0030', 'sony-50mm-f1-8-fe', 'Include: ✓ Sony 50mm F1.8 FE Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (265, 35, 12, '50mm F1.8 OSS', 'sony-50mm-f1-8-oss', 'S0031', 'sony-50mm-f1-8-oss', 'Include: ✓ Sony 50mm F1.8 OSS Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (266, 35, 12, '70-200mm F2.8 GM', 'sony-70-200mm-f2-8-gm', 'S0032', 'sony-70-200mm-f2-8-gm', 'Include: ✓ Sony 70-200mm F2.8 GM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (267, 35, 12, '70-200mm F2.8 GM II', 'sony-70-200mm-f2-8-gm-ii', 'S0033', 'sony-70-200mm-f2-8-gm-ii', 'Include: ✓ Sony 70-200mm F2.8 GM II Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (268, 35, 12, '70-200mm F4 FE G OSS', 'sony-70-200mm-f4-fe-g-oss', 'S0034', 'sony-70-200mm-f4-fe-g-oss', 'Include: ✓ Sony 70-200mm F4 FE G OSS Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (269, 35, 12, '70-350mm F4.5 OSS', 'sony-70-350mm-f4-5-oss', 'S0035', 'sony-70-350mm-f4-5-oss', 'Include: ✓ Sony 70-350mm F4.5 OSS Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (270, 35, 12, '85mm F1.4 FE GM', 'sony-85mm-f1-4-fe-gm', 'S0036', 'sony-85mm-f1-4-fe-gm', 'Include: ✓ Sony 85mm F1.4 FE GM Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (271, 35, 12, 'FE 35mm F1.8', 'sony-fe-35mm-f1-8', 'S0037', 'sony-fe-35mm-f1-8', 'Include: ✓ Sony FE 35mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (272, 35, 12, 'FE 85mm F1.8', 'sony-fe-85mm-f1-8', 'S0038', 'sony-fe-85mm-f1-8', 'Include: ✓ Sony FE 85mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (273, 37, 12, '11-18mm', 'tamron-11-18mm', 'T0002', 'tamron-11-18mm', 'Include: ✓ Tamron 11-18mm Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (274, 37, 12, '17-28 F2.8', 'tamron-17-28-f2-8', 'T0003', 'tamron-17-28-f2-8', 'Include: ✓ Tamron 17-28 F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (275, 37, 12, '17-50mm F2.8', 'tamron-17-50mm-f2-8', 'T0004', 'tamron-17-50mm-f2-8', 'Include: ✓ Tamron 17-50mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (276, 37, 12, '17–50mm F2.8', 'tamron-17-50mm-f2-8-2', 'T0005', 'tamron-17-50mm-f2-8-2', 'Include: ✓ Tamron 17-50mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (277, 37, 12, '20mm F2.8', 'tamron-20mm-f2-8', 'T0006', 'tamron-20mm-f2-8', 'Include: ✓ Tamron 20mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (278, 37, 12, '24-70mm F.28', 'tamron-24-70mm-f2-8', 'T0007', 'tamron-24-70mm-f2-8', 'Include: ✓ Tamron 24-70mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (279, 37, 12, '28-200mm F2.8', 'tamron-28-200mm-f2-8', 'T0008', 'tamron-28-200mm-f2-8', 'Include: ✓ Tamron 28-200mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (280, 37, 12, '28-75 F2.8', 'tamron-28-75-f2-8', 'T0009', 'tamron-28-75-f2-8', 'Include: ✓ Tamron 28-75 F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (281, 37, 12, '70-300mm', 'tamron-70-300mm', 'T0010', 'tamron-70-300mm', 'Include: ✓ Tamron 70-300mm Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (282, 37, 12, '85mm F.18', 'tamron-85mm-f1-8', 'T0011', 'tamron-85mm-f1-8', 'Include: ✓ Tamron 85mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (283, 40, 12, '35 F1.8', 'ttartisan-35-f1-8', 'TT0001', 'ttartisan-35-f1-8', 'Include: ✓ TTArtisan 35 F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (284, 40, 12, '35mm F1.4', 'ttartisan-35mm-f1-4', 'TT0002', 'ttartisan-35mm-f1-4', 'Include: ✓ TTArtisan 35mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (285, 40, 12, '35mm F1.4 Z APSC', 'ttartisan-35mm-f1-4-z-apsc', 'TT0003', 'ttartisan-35mm-f1-4-z-apsc', 'Include: ✓ TTArtisan 35mm F1.4 Z APS-C Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (286, 40, 12, '50mm F1.2', 'ttartisan-50mm-f1-2', 'TT0004', 'ttartisan-50mm-f1-2', 'Include: ✓ TTArtisan 50mm F1.2 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (287, 40, 12, '56mm F1.8', 'ttartisan-56mm-f1-8', 'TT0005', 'ttartisan-56mm-f1-8', 'Include: ✓ TTArtisan 56mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (288, 41, 12, '23mm F1.4', 'viltrox-23mm-f1-4', 'V0002', 'viltrox-23mm-f1-4', 'Include: ✓ Viltrox 23mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (289, 41, 12, '23mm F1.4', 'viltrox-23mm-f1-4-2', 'V0003', 'viltrox-23mm-f1-4-2', 'Include: ✓ Viltrox 23mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (290, 41, 12, '24mm F1.8 Nikon Z', 'viltrox-24mm-f1-8-nikon-z', 'V0004', 'viltrox-24mm-f1-8-nikon-z', 'Include: ✓ Viltrox 24mm F1.8 Nikon Z Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (291, 41, 12, '33 F1.4 Z for Nikon Z', 'viltrox-33-f1-4-z-for-nikon-z', 'V0005', 'viltrox-33-f1-4-z-for-nikon-z', 'Include: ✓ Viltrox 33 F1.4 Z for Nikon Z Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (292, 41, 12, '33mm F1.4', 'viltrox-33mm-f1-4', 'V0006', 'viltrox-33mm-f1-4', 'Include: ✓ Viltrox 33mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (293, 41, 12, '33mm F1.4', 'viltrox-33mm-f1-4-2', 'V0007', 'viltrox-33mm-f1-4-2', 'Include: ✓ Viltrox 33mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (294, 41, 12, '56mm F1.4', 'viltrox-56mm-f1-4', 'V0008', 'viltrox-56mm-f1-4', 'Include: ✓ Viltrox 56mm F1.4 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (295, 41, 12, '75mm F1.2', 'viltrox-75mm-f1-2', 'V0009', 'viltrox-75mm-f1-2', 'Include: ✓ Viltrox 75mm F1.2 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (296, 41, 12, '85mm F1.8', 'viltrox-85mm-f1-8', 'V0010', 'viltrox-85mm-f1-8', 'Include: ✓ Viltrox 85mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (297, 41, 12, '85mm F1.8 II', 'viltrox-85mm-f1-8-ii', 'V0011', 'viltrox-85mm-f1-8-ii', 'Include: ✓ Viltrox 85mm F1.8 II Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (298, 41, 12, 'FE 24mm F1.8', 'viltrox-fe-24mm-f1-8', 'V0012', 'viltrox-fe-24mm-f1-8', 'Include: ✓ Viltrox FE 24mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (299, 45, 12, '35mm F2', 'yongnuo-35mm-f2', 'YN0001', 'yongnuo-35mm-f2', 'Include: ✓ Yongnuo 35mm F2 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (300, 45, 12, '35mm F2', 'yongnuo-35mm-f2-2', 'YN0002', 'yongnuo-35mm-f2-2', 'Include: ✓ Yongnuo 35mm F2 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (301, 45, 12, '35mm F2 APSC', 'yongnuo-35mm-f2-apsc', 'YN0003', 'yongnuo-35mm-f2-apsc', 'Include: ✓ Yongnuo 35mm F2 APS-C Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (302, 45, 12, '50mm F1.8', 'yongnuo-50mm-f1-8', 'YN0004', 'yongnuo-50mm-f1-8', 'Include: ✓ Yongnuo 50mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (303, 45, 12, '50mm F1.8', 'yongnuo-50mm-f1-8-2', 'YN0005', 'yongnuo-50mm-f1-8-2', 'Include: ✓ Yongnuo 50mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (304, 45, 12, '85mm F1.8', 'yongnuo-85mm-f1-8', 'YN0006', 'yongnuo-85mm-f1-8', 'Include: ✓ Yongnuo 85mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (305, 47, 12, '24-70mm F4 FE', 'zeiss-24-70mm-f4-fe', 'Z0001', 'zeiss-24-70mm-f4-fe', 'Include: ✓ Zeiss 24-70mm F4 FE Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (306, 47, 12, '35mm F2.8', 'zeiss-35mm-f2-8', 'Z0002', 'zeiss-35mm-f2-8', 'Include: ✓ Zeiss 35mm F2.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (307, 47, 12, '55mm F1.8', 'zeiss-55mm-f1-8', 'Z0003', 'zeiss-55mm-f1-8', 'Include: ✓ Zeiss 55mm F1.8 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (308, 49, 12, 'Telezoom 18x25', 'telezoom-18x25', 'GEN0001', 'telezoom-18x25', 'Include: ✓ Telezoom 18x25 Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (309, 49, 12, 'Telezoom 22x', 'telezoom-22x', 'GEN0002', 'telezoom-22x', 'Include: ✓ Telezoom 22x Lens ✓ Front/Rear Caps', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (310, 7, 13, 'X1T Canon', 'canon-x1t', 'C0062', 'canon-x1t', 'Include: ✓ Canon X1T Flash Trigger ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (311, 14, 13, 'X2T Fuji', 'fujifilm-x2t', 'F0026', 'fujifilm-x2t', 'Include: ✓ Fujifilm X2T Flash Trigger ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (312, 15, 13, 'T1520 II (universal)', 'godox-t1520-ii-universal', 'GD0001', 'godox-t1520-ii-universal', 'Include: ✓ Godox T1520 II (universal) Flash ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (313, 15, 13, 'T1600 (sony)', 'godox-t1600-sony', 'GD0002', 'godox-t1600-sony', 'Include: ✓ Godox T1600 (sony) Flash ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (314, 15, 13, 'T1600 (universal)', 'godox-t1600-universal', 'GD0003', 'godox-t1600-universal', 'Include: ✓ Godox T1600 (universal) Flash ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (315, 15, 13, 'T1685 (canon)', 'godox-t1685-canon', 'GD0004', 'godox-t1685-canon', 'Include: ✓ Godox T1685 (canon) Flash ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (316, 15, 13, 'T1685 (fuji)', 'godox-t1685-fuji', 'GD0005', 'godox-t1685-fuji', 'Include: ✓ Godox T1685 (fuji) Flash ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (317, 15, 13, 'V350 + batre (canon/sony)', 'godox-v350-batre-canon-sony', 'GD0006', 'godox-v350-batre-canon-sony', 'Include: ✓ Godox V350 + Battery (canon/sony) Flash ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (318, 15, 13, 'V850 II + batre (universal)', 'godox-v850-ii-batre-universal', 'GD0007', 'godox-v850-ii-batre-universal', 'Include: ✓ Godox V850 II + Battery (universal) Flash ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (319, 15, 13, 'V860 (sony)', 'godox-v860-sony', 'GD0008', 'godox-v860-sony', 'Include: ✓ Godox V860 (sony) Flash ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (320, 25, 13, 'X2T Nikon', 'nikon-x2t', 'N0034', 'nikon-x2t', 'Include: ✓ Nikon X2T Flash Trigger ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (321, 26, 13, 'Bateral Enelop', 'panasonic-bateral-enelop', 'P0002', 'panasonic-bateral-enelop', 'Include: ✓ Panasonic Eneloop Batteries ✓ Charger', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (322, 35, 13, 'X2T Sony', 'sony-x2t', 'S0039', 'sony-x2t', 'Include: ✓ Sony X2T Flash Trigger ✓ Battery', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (323, 49, 14, 'Memori', 'memory-card', 'GEN0007', 'memory-card', 'Include: ✓ 8GB Memory Card', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (324, 49, 14, 'Memori XQD', 'memory-card-xqd', 'GEN0009', 'memory-card-xqd', 'Include: ✓ 64GB XQD Memory Card', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (325, 9, 15, 'BIONX-11', 'costa-bionx-11', 'CS0001', 'costa-bionx-11', 'Include: ✓ Costa BIONX-11 Microphone', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (326, 15, 15, 'Movelink M2', 'godox-movelink-m2', 'GD0009', 'godox-movelink-m2', 'Include: ✓ Godox Movelink M2 Wireless Mic', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (327, 18, 15, 'M2 Combo', 'hollyland-m2-combo', 'H0001', 'hollyland-m2-combo', 'Include: ✓ Hollyland M2 Combo Wireless Mic System', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (328, 28, 15, 'Pro Rycote', 'rode-pro-rycote', 'R0001', 'rode-pro-rycote', 'Include: ✓ Rode Pro Rycote Microphone', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (329, 31, 15, 'Blink 500 B2', 'saramonic-blink-500-b2', 'SRM0001', 'saramonic-blink-500-b2', 'Include: ✓ Saramonic Blink 500 B2 Wireless Mic', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (330, 35, 15, 'ECM-XYST 1M', 'sony-ecm-xyst-1m', 'S0040', 'sony-ecm-xyst-1m', 'Include: ✓ Sony ECM-XYST 1M Microphone', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (331, 35, 15, 'ICD 240', 'sony-icd-240', 'S0041', 'sony-icd-240', 'Include: ✓ Sony ICD 240 Recorder', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (332, 35, 15, 'ICD PX240', 'sony-icd-px240', 'S0042', 'sony-icd-px240', 'Include: ✓ Sony ICD PX240 Recorder', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (333, 38, 15, 'DR-05X', 'tascam-dr-05x', 'TS0001', 'tascam-dr-05x', 'Include: ✓ Tascam DR-05X Recorder', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (334, 39, 15, 'A37', 'tnw-a37', 'T0012', 'tnw-a37', 'Include: ✓ TNW A37 Microphone', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (335, 42, 15, 'AM18', 'ulanzi-am18', 'U0001', 'ulanzi-am18', 'Include: ✓ Ulanzi AM18 Microphone', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (336, 42, 15, 'J12', 'ulanzi-j12', 'U0002', 'ulanzi-j12', 'Include: ✓ Ulanzi J12 Microphone', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (337, 48, 15, 'H1n', 'zoom-h1n', 'ZM0001', 'zoom-h1n', 'Include: ✓ Zoom H1n Recorder', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (338, 48, 15, 'H6', 'zoom-h6', 'ZM0002', 'zoom-h6', 'Include: ✓ Zoom H6 Recorder', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (339, 12, 16, 'Monitor FW568', 'feelworld-monitor-fw568', 'FW0001', 'feelworld-monitor-fw568', 'Include: ✓ Feelworld FW568 Monitor ✓ Power Cable', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (340, 3, 21, 'Advance K1506', 'advance-k1506', 'AD0001', 'advance-k1506', 'Include: ✓ Advance K1506 Speaker', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (341, 49, 19, 'Powerbank', 'powerbank', 'GEN0010', 'powerbank', 'Include: ✓ Powerbank ✓ Charging Cable', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (342, 5, 20, 'B-Steady Nova', 'brica-b-steady-nova', 'BR0001', 'brica-b-steady-nova', 'Include: ✓ Brica B-Steady Nova Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (343, 5, 20, 'B-Steady Pro 2023 (Hp)', 'brica-b-steady-pro-2023-hp', 'BR0002', 'brica-b-steady-pro-2023-hp', 'Include: ✓ Brica B-Steady Pro 2023 (Hp) Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (344, 5, 20, 'XS HP', 'brica-xs-hp', 'BR0003', 'brica-xs-hp', 'Include: ✓ Brica XS HP Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (345, 11, 20, 'OM 4 SE HP', 'dji-om-4-se-hp', 'D0009', 'dji-om-4-se-hp', 'Include: ✓ DJI OM 4 SE HP Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (346, 11, 20, 'Ronin SC', 'dji-ronin-sc', 'D0010', 'dji-ronin-sc', 'Include: ✓ DJI Ronin SC Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (347, 11, 20, 'Ronin SC 2', 'dji-ronin-sc-2', 'D0011', 'dji-ronin-sc-2', 'Include: ✓ DJI Ronin SC 2 Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (348, 11, 20, 'RS Mini 3', 'dji-rs-mini-3', 'D0012', 'dji-rs-mini-3', 'Include: ✓ DJI RS Mini 3 Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (349, 13, 20, 'AK2000C', 'feiyu-ak2000c', 'FY0001', 'feiyu-ak2000c', 'Include: ✓ Feiyu AK2000C Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (350, 13, 20, 'G6Max', 'feiyu-g6max', 'FY0002', 'feiyu-g6max', 'Include: ✓ Feiyu G6Max Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (351, 13, 20, 'Scrop C', 'feiyu-scrop-c', 'FY0003', 'feiyu-scrop-c', 'Include: ✓ Feiyu Scrop C Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (352, 19, 20, 'Air Flow', 'insta360-air-flow', 'I0006', 'insta360-air-flow', 'Include: ✓ Insta360 Air Flow Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (353, 24, 20, 'Mini Mx (Hp)', 'moza-mini-mx-hp', 'MZ0001', 'moza-mini-mx-hp', 'Include: ✓ Moza Mini MX (Hp) Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (354, 46, 20, 'Crane M', 'zhiyun-crane-m', 'ZY0001', 'zhiyun-crane-m', 'Include: ✓ Zhiyun Crane M Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (355, 46, 20, 'Crane M2', 'zhiyun-crane-m2', 'ZY0002', 'zhiyun-crane-m2', 'Include: ✓ Zhiyun Crane M2 Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (356, 46, 20, 'Crane M2s', 'zhiyun-crane-m2s', 'ZY0003', 'zhiyun-crane-m2s', 'Include: ✓ Zhiyun Crane M2s Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (357, 46, 20, 'Crane M3', 'zhiyun-crane-m3', 'ZY0004', 'zhiyun-crane-m3', 'Include: ✓ Zhiyun Crane M3 Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (358, 46, 20, 'Webil Lab', 'zhiyun-webil-lab', 'ZY0005', 'zhiyun-webil-lab', 'Include: ✓ Zhiyun Webil Lab Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (359, 46, 20, 'Webil S', 'zhiyun-webil-s', 'ZY0006', 'zhiyun-webil-s', 'Include: ✓ Zhiyun Webil S Stabilizer', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (360, 42, 25, 'Rotating Plate', 'ulanzi-rotating-plate', 'U0003', 'ulanzi-rotating-plate', 'Include: ✓ Ulanzi Rotating Plate', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (361, 49, 25, 'Magnetic Camera Grip', 'magnetic-camera-grip', 'GEN0011', 'magnetic-camera-grip', 'Include: ✓ Magnetic Camera Grip', '2025-01-01 00:00:01', '2025-01-01 00:00:01');
INSERT INTO `products` VALUES (362, 49, 25, 'Production Direction', 'production-direction', 'GEN0012', 'production-direction', 'Include: ✓ Production Direction Tools', '2025-01-01 00:00:01', '2025-01-01 00:00:01');

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

-- ----------------------------
-- Table structure for promotion_usages
-- ----------------------------
DROP TABLE IF EXISTS `promotion_usages`;
CREATE TABLE `promotion_usages`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `promo_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `applicable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `applicable_id` bigint UNSIGNED NOT NULL,
  `discount_amount` decimal(12, 2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `promotion_usages_user_id_foreign`(`user_id` ASC) USING BTREE,
  INDEX `promotion_usages_applicable_type_applicable_id_index`(`applicable_type` ASC, `applicable_id` ASC) USING BTREE,
  INDEX `promotion_usages_promo_id_user_id_index`(`promo_id` ASC, `user_id` ASC) USING BTREE,
  INDEX `promotion_usages_created_at_index`(`created_at` ASC) USING BTREE,
  CONSTRAINT `promotion_usages_promo_id_foreign` FOREIGN KEY (`promo_id`) REFERENCES `promos` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `promotion_usages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of promotion_usages
-- ----------------------------

-- ----------------------------
-- Table structure for rent_items
-- ----------------------------
DROP TABLE IF EXISTS `rent_items`;
CREATE TABLE `rent_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `rent_id` bigint UNSIGNED NOT NULL,
  `product_branch_id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL,
  `price` decimal(12, 0) NOT NULL DEFAULT 0,
  `discount` decimal(12, 0) NOT NULL DEFAULT 0,
  `subtotal` decimal(12, 0) NOT NULL DEFAULT 0,
  `repair_fee` decimal(12, 0) NOT NULL DEFAULT 0,
  `penalty_fee` decimal(12, 0) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `rent_items_rent_id_foreign`(`rent_id` ASC) USING BTREE,
  INDEX `rent_items_product_branch_id_foreign`(`product_branch_id` ASC) USING BTREE,
  CONSTRAINT `rent_items_product_branch_id_foreign` FOREIGN KEY (`product_branch_id`) REFERENCES `product_branches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `rent_items_rent_id_foreign` FOREIGN KEY (`rent_id`) REFERENCES `rents` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of rent_items
-- ----------------------------
INSERT INTO `rent_items` VALUES (1, 1, 17, 1, 90000, 0, 360000, 0, 0, NULL, NULL);
INSERT INTO `rent_items` VALUES (2, 6, 18, 2, 90000, 0, 180000, 0, 0, '2025-08-07 17:17:20', '2025-08-07 17:17:20');

-- ----------------------------
-- Table structure for rents
-- ----------------------------
DROP TABLE IF EXISTS `rents`;
CREATE TABLE `rents`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `total_days` int NOT NULL,
  `total_hour_late` int NOT NULL DEFAULT 0,
  `total_repair_fee` decimal(12, 0) NOT NULL DEFAULT 0,
  `discount_amount` decimal(12, 0) NOT NULL DEFAULT 0,
  `deposit_amount` decimal(12, 0) NOT NULL DEFAULT 0,
  `total_price` decimal(12, 0) NOT NULL DEFAULT 0,
  `total_paid` decimal(12, 0) NOT NULL DEFAULT 0,
  `proof_of_collection` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `type` enum('online','offline') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_type` enum('transfer','cash','qris','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','confirmed','on_rent','completed','canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_paid` enum('pending','partial','completed','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `rents_code_unique`(`code` ASC) USING BTREE,
  INDEX `rents_branch_id_foreign`(`branch_id` ASC) USING BTREE,
  INDEX `rents_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `rents_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `rents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of rents
-- ----------------------------
INSERT INTO `rents` VALUES (1, 4, 21, 'RENT0001', '2025-08-07', '2025-08-11', '12:00:00', '12:00:00', NULL, 4, 0, 0, 108000, 0, 256000, 0, 'proof_of_collection/RENT0001.png', 'online', 'transfer', 'on_rent', 'completed', '2025-08-06 13:47:40', '2025-08-07 15:34:37');
INSERT INTO `rents` VALUES (6, 4, 21, 'RENT25080001', '2025-08-07', '2025-08-09', '17:19:00', '17:19:00', NULL, 2, 0, 0, 0, 0, 184000, 0, NULL, 'online', 'transfer', 'pending', 'pending', '2025-08-07 17:17:20', '2025-08-07 17:28:28');

-- ----------------------------
-- Table structure for sale_items
-- ----------------------------
DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE `sale_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` bigint UNSIGNED NOT NULL,
  `product_branch_id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL,
  `price` decimal(12, 0) NOT NULL DEFAULT 0,
  `discount` decimal(12, 0) NOT NULL DEFAULT 0,
  `subtotal` decimal(12, 0) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sale_items_sale_id_foreign`(`sale_id` ASC) USING BTREE,
  INDEX `sale_items_product_branch_id_foreign`(`product_branch_id` ASC) USING BTREE,
  CONSTRAINT `sale_items_product_branch_id_foreign` FOREIGN KEY (`product_branch_id`) REFERENCES `product_branches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sale_items
-- ----------------------------

-- ----------------------------
-- Table structure for sales
-- ----------------------------
DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sales`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch_id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_date` datetime NOT NULL,
  `discount_amount` decimal(12, 0) NULL DEFAULT NULL,
  `total_price` decimal(12, 0) NOT NULL DEFAULT 0,
  `total_paid` decimal(12, 0) NULL DEFAULT 0,
  `receipt_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `shipping_price` decimal(12, 0) NULL DEFAULT 0,
  `payment_type` enum('transfer','cash','qris','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','completed','canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sales_code_unique`(`code` ASC) USING BTREE,
  INDEX `sales_branch_id_foreign`(`branch_id` ASC) USING BTREE,
  INDEX `sales_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `sales_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sales
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch_id` bigint UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `google_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `love_reacter_id` bigint UNSIGNED NULL DEFAULT NULL,
  `st` enum('pending','unverified','verified','suspend') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `last_seen` timestamp NULL DEFAULT NULL,
  `banned_at` timestamp NULL DEFAULT NULL,
  `banned_by` int NULL DEFAULT NULL,
  `verified_by` int NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE,
  UNIQUE INDEX `users_phone_unique`(`phone` ASC) USING BTREE,
  INDEX `users_branch_id_index`(`branch_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 23 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 4, 'Rizky Ramadhan', 'rizky.ramadhan@adamasanya.com', '0811176321', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$CO7fOu1qO6SCLcqN07Zun.EtASKHOUaSBlGishSu7vSxIzLErrBcG', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, NULL, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (2, 1, 'Rizal Prasetya', 'rizal@adamasanya.com', '88218418962', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$bpBOVNljFiB383AjIpc2DOBGByfQ2BlhLx7qCVIyxQjqvnVRYWsyK', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (3, 1, 'Karisma', 'karisma@adamasanya.com', '81563170313', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$bLifrHANG3LVzjc/6Ni6Oe03icZL0WAbBp1R7ZJynO.riMUA87FA.', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (4, 1, 'Admin Pusat', 'pusat@adamasanya.com', '87765346368', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$zBDdDoBhd2D9SFqN0Jeva.LQN3MxULiD67AvIuyZQKgH8RKLYCyQW', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (5, 2, 'Admin Bali', 'bali@adamasanya.com', '1', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$d/9s0PhAPYcJmN8I0dbJeOR/1SG2ItvgtN6IFf1qTlV9pS2N52pPS', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (6, 3, 'Admin Bekasi', 'bekasi@adamasanya.com', '82310731018', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$Ys3.eXLUWIT7dRwfZM2bOOvqSe5M6tPTbN61eNEOUTL45K6G5VjGi', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (7, 4, 'Admin Buahbatu', 'buahbatu@adamasanya.com', '811172321', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$2FXAPwvBIR69k8jk2/iJJuFjlXMs35SXoJpKI7G9EsydcM.9aCS72', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (8, 5, 'Admin Garut', 'garut@adamasanya.com', '85156155794', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$nBthNWGHJIN/2SRgP4lHLeb9xlsn1DpYuSndWdkCFQNEIMXJH02XW', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (9, 6, 'Admin Gununghalu', 'gununghalu@adamasanya.com', '2', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$znowVF4ccKGHDXEUsmoiEO0bOTj65xcRSoegtuiGyaf2exsOtH1n2', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (10, 7, 'Admin Jatinangor', 'jatinangor@adamasanya.com', '3', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$GCb5Al88wKMTg72FDRvwQeklGJH5yfzHzWct8FsDcj6qzLLXwREN6', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (11, 8, 'Admin Karawang', 'karawang@adamasanya.com', '88202222222', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$MHdkLOVv.qgE/3xqutI4P.aTGQemE4zA6rKFtjwt1C1bOXLdeRn0G', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (12, 9, 'Admin Purwakarta', 'purwakarta@adamasanya.com', '87822874287', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$wmw12JM2Y5v6/r5d4hcBWOo1DBA/QKwJG43ZFHjsQv.2XFc/BO3BK', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (13, 10, 'Admin Sukabumi', 'sukabumi@adamasanya.com', '85939717250', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$GIpKjWRuZ1Cw8DR1V.7PauIcQAmqLytKGWlpy94ELUtynSFvlkPvu', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (14, 11, 'Admin Tangerang', 'tangerang@adamasanya.com', '87708310148', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$6PwDGeASN1Zs9jkmLTkEAebM7eUpj0QHNN2EsBvZR5l9ANzpmjaKy', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (15, 12, 'Admin Outfit', 'outfit@adamasanya.com', '87829333930', '2023-01-01 00:00:01', '2023-01-01 00:00:01', '$2y$12$7c92mrhRkrOvkEOGWPPb8eXpuAX3aUb9.P2hWUpsCLy8HvWOApWVO', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, 1, '2023-01-01 00:00:01', NULL, '2023-01-01 00:00:01', NULL, NULL);
INSERT INTO `users` VALUES (16, 4, 'Louis Yudha Pratama', 'louisyudhapratama@gmail.com', '81382194483', NULL, NULL, '$2y$12$7c92mrhRkrOvkEOGWPPb8eXpuAX3aUb9.P2hWUpsCLy8HvWOApWVO', NULL, NULL, NULL, NULL, 'verified', NULL, '2024-03-02 02:11:34', 2, 5, '2024-03-02 02:11:34', NULL, '2024-03-02 02:11:34', NULL, NULL);
INSERT INTO `users` VALUES (17, 3, 'Richard albert samuel sianipar', 'richardalbert@gmail.com', '897656342', NULL, NULL, '$2y$12$7c92mrhRkrOvkEOGWPPb8eXpuAX3aUb9.P2hWUpsCLy8HvWOApWVO', NULL, NULL, NULL, NULL, 'verified', NULL, '2023-11-27 02:14:06', 6, 9, '2023-11-27 02:14:06', NULL, '2023-11-27 02:14:06', NULL, NULL);
INSERT INTO `users` VALUES (18, 11, 'Reza Chaerulloh', 'rezachaerulloh@gmail.com', '00', NULL, NULL, '$2y$12$7c92mrhRkrOvkEOGWPPb8eXpuAX3aUb9.P2hWUpsCLy8HvWOApWVO', NULL, NULL, NULL, NULL, 'verified', NULL, '2023-11-09 00:00:00', 14, 14, '2023-11-09 00:00:00', NULL, '2023-11-09 00:00:00', NULL, NULL);
INSERT INTO `users` VALUES (19, 1, 'Rangga Romansah', 'ranggaromansah134@gmail.com', '85797536723', NULL, NULL, '$2y$12$7c92mrhRkrOvkEOGWPPb8eXpuAX3aUb9.P2hWUpsCLy8HvWOApWVO', NULL, NULL, NULL, NULL, 'verified', NULL, '2023-12-20 00:00:00', 7, 4, '2023-12-20 00:00:00', NULL, '2023-12-20 00:00:00', NULL, NULL);
INSERT INTO `users` VALUES (20, 11, 'Valentino Mustika', 'wanted@gmail.com', '897654356', NULL, NULL, '$2y$12$EkJfKuYF3fBI2rBF4h3OR.PuTiFHgxMc0jT18ZTOBe8IV0ijs8TXi', NULL, NULL, NULL, NULL, 'verified', NULL, '2025-06-05 20:05:46', 14, 14, '2024-10-30 00:00:00', NULL, '2024-10-30 00:00:00', NULL, NULL);
INSERT INTO `users` VALUES (21, NULL, 'Rizky Ramadhan', 'officialrizkyr@gmail.com', '818195241', NULL, NULL, '$2y$12$2JnAJcGcpR2vPSkXHjRSTevHKEIKurhRE9Y5dC6IHA3vOV2rVrBy2', NULL, NULL, NULL, NULL, 'verified', NULL, NULL, NULL, NULL, NULL, '1mAkwA5g8xA5WwZBMzvkLJrsyYNOkdDSRpLs0md3Pn1ROQpzNepSTSacvGwM', '2025-07-29 16:13:52', '2025-08-05 01:24:37', NULL);
INSERT INTO `users` VALUES (22, 4, 'Sheila Andiani ', 'sheiladwi56@gmail.com', '811145245', NULL, NULL, '$2y$12$jsXF9wXgjWLmkjZV5T7C/eBpGpF.R8pA/N9brySLW2eMvXyCVWcwy', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, 'UaWyrWeGRx0vuaOp9JswSyrtljs0MqUUt0EuaJM7drz3A6ucD3fao5CHzzxs', '2025-08-06 15:24:40', '2025-08-06 15:32:15', NULL);

SET FOREIGN_KEY_CHECKS = 1;
