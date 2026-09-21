-- MySQL dump 10.13  Distrib 8.0.32, for Linux (x86_64)
--
-- Host: localhost    Database: quotation
-- ------------------------------------------------------
-- Server version	8.0.32

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `module_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`module_id`),
  KEY `modules_created_by_foreign` (`created_by`),
  KEY `modules_updated_by_foreign` (`updated_by`),
  CONSTRAINT `modules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `modules_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES (1,'coastal','2026-08-16 22:33:53','2026-08-16 22:33:53',NULL,NULL,1),(2,'aerial mapping','2026-08-16 22:35:34','2026-08-16 22:35:34',NULL,NULL,1),(3,'Bathymetry Survey','2026-08-16 22:37:17','2026-08-16 22:37:17',NULL,NULL,1),(102,'try','2026-08-22 19:36:44','2026-08-22 19:36:44',NULL,NULL,1),(103,'123','2026-08-22 19:46:34','2026-08-22 19:46:34',NULL,NULL,1);
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `category_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `module_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`category_id`),
  KEY `category_module_id_foreign` (`module_id`),
  KEY `category_created_by_foreign` (`created_by`),
  KEY `category_updated_by_foreign` (`updated_by`),
  CONSTRAINT `category_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `category_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`module_id`) ON DELETE CASCADE,
  CONSTRAINT `category_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'data collection/surveys',1,'2026-08-16 22:33:53','2026-08-16 22:33:53',NULL,NULL,1),(2,'data analysis & reporting',2,'2026-08-16 22:35:34','2026-08-16 22:35:34',NULL,NULL,1),(4,'data analysis & reporting',3,'2026-08-17 00:24:57','2026-08-17 00:24:57',NULL,NULL,1),(5,'survey',102,'2026-08-22 19:36:44','2026-08-22 19:36:44',NULL,NULL,1),(6,'456',103,'2026-08-22 19:46:34','2026-08-22 19:46:34',NULL,NULL,1),(7,'survey',2,'2026-08-22 23:24:31','2026-08-22 23:24:31',NULL,NULL,1),(8,'mhmhiuh',1,'2026-08-23 20:41:17','2026-08-23 20:41:17',NULL,NULL,1);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service` (
  `service_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`service_id`),
  KEY `service_category_id_foreign` (`category_id`),
  KEY `service_created_by_foreign` (`created_by`),
  KEY `service_updated_by_foreign` (`updated_by`),
  CONSTRAINT `service_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE,
  CONSTRAINT `service_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `service_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service`
--

LOCK TABLES `service` WRITE;
/*!40000 ALTER TABLE `service` DISABLE KEYS */;
INSERT INTO `service` VALUES (1,'beach profiling',1,'2026-08-16 22:33:53','2026-08-16 22:33:53',NULL,NULL,1),(2,'data analysis',2,'2026-08-16 22:35:34','2026-08-16 22:35:34',NULL,NULL,1),(4,'data analysis',4,'2026-08-17 00:24:57','2026-08-17 00:24:57',NULL,NULL,1),(5,'hdtr',5,'2026-08-22 19:36:44','2026-08-22 19:36:44',NULL,NULL,1),(6,'789',6,'2026-08-22 19:46:34','2026-08-22 19:46:34',NULL,NULL,1),(7,'data analysis',7,'2026-08-22 23:24:31','2026-08-22 23:24:31',NULL,NULL,1),(8,'yuftyf',8,'2026-08-23 20:41:17','2026-08-23 20:41:17',NULL,NULL,1),(9,'bguygg',8,'2026-08-23 20:41:56','2026-08-23 20:41:56',NULL,NULL,1);
/*!40000 ALTER TABLE `service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `item_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `module_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NOT NULL,
  `unit_id` bigint unsigned DEFAULT NULL,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `items_module_id_foreign` (`module_id`),
  KEY `items_category_id_foreign` (`category_id`),
  KEY `items_service_id_foreign` (`service_id`),
  KEY `items_unit_id_foreign` (`unit_id`),
  CONSTRAINT `items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE,
  CONSTRAINT `items_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`module_id`) ON DELETE CASCADE,
  CONSTRAINT `items_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `service` (`service_id`) ON DELETE CASCADE,
  CONSTRAINT `items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,1,1,1,2,'drone',2000.00,'test',NULL,2,'2026-08-16 22:33:53','2026-08-25 18:47:11'),(2,2,2,2,NULL,'Orthophoto Generation',3500.00,NULL,NULL,NULL,'2026-08-16 22:35:34','2026-08-16 22:35:34'),(4,3,4,4,3,'Seabed Mapping & Interpretation',10000.00,NULL,NULL,2,'2026-08-16 22:45:17','2026-08-31 22:30:21'),(7,1,1,1,3,'drone2',500.00,NULL,2,2,'2026-08-22 19:36:07','2026-09-05 18:32:23'),(8,102,5,5,NULL,'drone3',678.00,NULL,2,NULL,'2026-08-22 19:36:44','2026-08-22 19:36:44'),(9,103,6,6,NULL,'123476546',10000.00,NULL,2,NULL,'2026-08-22 19:46:34','2026-08-22 19:46:34'),(10,3,4,4,2,'ktry',87980.00,NULL,NULL,2,'2026-08-22 22:07:30','2026-08-26 17:03:05'),(11,2,7,7,NULL,'jhiyfyh',7870.00,NULL,2,NULL,'2026-08-22 23:24:31','2026-08-22 23:24:31'),(13,1,8,8,NULL,'jhi',90.00,NULL,2,NULL,'2026-08-23 20:41:17','2026-08-23 20:41:17'),(14,1,8,9,NULL,'uy878y',888.00,NULL,2,NULL,'2026-08-23 20:41:56','2026-08-23 20:41:56'),(15,103,6,6,1,'rrrrr',2222.00,NULL,2,NULL,'2026-08-24 01:39:56','2026-08-24 01:39:56'),(16,103,6,6,1,'44444',2324.00,NULL,2,NULL,'2026-08-25 17:45:22','2026-08-25 17:45:22');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `units` (
  `unit_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`unit_id`),
  UNIQUE KEY `units_unit_name_unique` (`unit_name`),
  KEY `units_created_by_foreign` (`created_by`),
  KEY `units_updated_by_foreign` (`updated_by`),
  CONSTRAINT `units_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `units_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` VALUES (1,'KG',2,NULL,'2026-08-24 01:39:56','2026-08-24 01:39:56'),(2,'PCS',2,NULL,'2026-08-25 18:47:11','2026-08-25 18:47:11'),(3,'LUMP SUM',2,NULL,'2026-08-31 22:30:21','2026-08-31 22:30:21');
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-20  1:13:31
