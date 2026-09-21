CREATE DATABASE  IF NOT EXISTS `quotation` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `quotation`;
-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: quotation
-- ------------------------------------------------------
-- Server version	8.0.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `category_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `client_Id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_address` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`client_Id`),
  KEY `clients_created_by_foreign` (`created_by`),
  KEY `clients_updated_by_foreign` (`updated_by`),
  CONSTRAINT `clients_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `clients_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'kjiohg',NULL,2,NULL,'2026-08-26 21:45:57','2026-08-26 21:45:57'),(2,'vbfcnb',NULL,2,NULL,'2026-08-26 23:00:50','2026-08-26 23:00:50'),(3,'fghhh',NULL,2,NULL,'2026-08-26 23:07:43','2026-08-26 23:07:43'),(4,'tryuh',NULL,2,NULL,'2026-08-26 23:14:01','2026-08-26 23:14:01'),(5,'ghfhj',NULL,2,NULL,'2026-08-26 23:24:03','2026-08-26 23:24:03'),(6,'bhh huy hht',NULL,2,NULL,'2026-09-01 00:33:50','2026-09-01 00:33:50'),(7,'t654 iiy8k 5ggv','gftghg',NULL,NULL,'2026-09-01 23:22:22','2026-09-01 23:22:22'),(8,'vfgh ghtt hh','ghghy\njfkghfh\nmmmb',NULL,NULL,'2026-09-02 01:03:44','2026-09-02 01:03:44'),(9,'bbbh ggrfy ttj','ng\nmkh\nbbf',NULL,NULL,'2026-09-02 01:08:58','2026-09-02 01:08:58'),(10,'df rr gh','gfg\nhh\ntty',NULL,NULL,'2026-09-02 01:24:12','2026-09-02 01:24:12'),(11,'vnf ggd ffuy','nlij\nkkk\n,jbh',NULL,NULL,'2026-09-03 00:15:34','2026-09-03 00:15:34'),(13,'fg gfbfgb dd','fgfh\ngg\ndsd',NULL,NULL,'2026-09-05 22:57:21','2026-09-05 22:57:21'),(14,'fdf vv 3','dfsd\nfs\nsa',NULL,NULL,'2026-09-05 23:39:50','2026-09-05 23:39:50'),(15,'hv hgy yy','ggvh',NULL,NULL,'2026-09-05 23:46:35','2026-09-05 23:46:35'),(16,'nk hh ii','klk\nkk\nn',NULL,NULL,'2026-09-05 23:54:12','2026-09-05 23:54:12'),(17,'78 juy 66','tuy',NULL,NULL,'2026-09-06 00:01:52','2026-09-06 00:01:52'),(18,'xsc dd s','dc\ndd\nwe',NULL,NULL,'2026-09-06 00:07:26','2026-09-06 00:07:26'),(19,'gtrdb tgg ttt','ghfg\nfgd\ngfh',NULL,NULL,'2026-09-07 01:10:53','2026-09-07 01:10:53'),(20,'rgbfg ertg','rrg\ngdf\ndfd',NULL,NULL,'2026-09-07 23:13:47','2026-09-07 23:13:47'),(21,'bjg yjh uum','123 cjrd\nwefr \nrfrf',NULL,NULL,'2026-09-08 00:38:53','2026-09-08 00:38:53'),(22,'fghg uurt dfh','fghgh\nghhh\nyuyuj',NULL,NULL,'2026-09-08 19:33:50','2026-09-08 19:33:50'),(23,'fvg rrg 56y','rg5g\ngtgt\nrfr5g',NULL,NULL,'2026-09-14 17:44:33','2026-09-14 17:44:33'),(24,'gfhfgh fjdhd','dfdg\ngdg\nfgd',NULL,NULL,'2026-09-14 17:53:56','2026-09-14 17:53:56'),(25,'gf rgvg 5gt','rgfrg',NULL,NULL,'2026-09-14 17:54:44','2026-09-14 17:54:44'),(26,'bgn hty tyt','thyh\nokl',NULL,NULL,'2026-09-14 18:02:19','2026-09-14 18:02:19'),(27,'ggfnb','hgfn',NULL,NULL,'2026-09-14 18:10:58','2026-09-14 18:10:58'),(28,'ghnhm','ghjn',NULL,NULL,'2026-09-14 18:16:25','2026-09-14 18:16:25'),(29,'dgfgjh hgj','hgg\nioop\nopo',NULL,NULL,'2026-09-14 19:10:02','2026-09-14 19:10:02');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `invoice_Id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quotation_Id` bigint unsigned NOT NULL,
  `invoice_date` date DEFAULT NULL,
  `printed_date` timestamp NULL DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`invoice_Id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_quotation_id_foreign` (`quotation_Id`),
  KEY `invoices_created_by_foreign` (`created_by`),
  KEY `invoices_updated_by_foreign` (`updated_by`),
  CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_quotation_id_foreign` FOREIGN KEY (`quotation_Id`) REFERENCES `qt_invoice` (`quotation_Id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (25,'EHS/CP/BYU/015-INV/2026/001',23,NULL,NULL,NULL,NULL,2,NULL,'2026-09-08 19:32:50','2026-09-08 19:32:50'),(26,'EHS/CP/BYU/015-INV/2026/002',23,NULL,NULL,NULL,NULL,2,NULL,'2026-09-08 19:32:56','2026-09-08 19:32:56'),(27,'EHS/JP/FUD/016-INV/2026/003',24,NULL,NULL,NULL,NULL,2,NULL,'2026-09-08 19:34:14','2026-09-08 19:34:14'),(28,'EHS/JP/FUD/016-INV/2026/001',24,'2026-09-17','2026-09-08 19:55:23','2026-10-01',NULL,2,2,'2026-09-08 19:36:48','2026-09-08 19:55:23'),(29,'EHS/JP/FUD/016-INV/2026/002',24,NULL,NULL,NULL,NULL,2,NULL,'2026-09-08 19:37:06','2026-09-08 19:37:06'),(30,'EHS/GP/G/022-INV/2026/001',32,NULL,NULL,NULL,NULL,3,NULL,'2026-09-14 19:06:55','2026-09-14 19:06:55'),(31,'EHS/MP/DH/023-INV/2026/001',33,'2026-09-24','2026-09-14 19:10:43','2026-10-10','ghjjhk',3,3,'2026-09-14 19:10:28','2026-09-14 19:10:58');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
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
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
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
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_08_10_050111_create_modules_table',1),(5,'2026_08_10_050209_create_category_table',1),(6,'2026_08_10_050234_create_service_table',1),(7,'2026_08_11_020454_create_items_table',1),(8,'2026_08_12_070504_add_is_active_to_tables',1),(9,'2026_08_13_083000_create_clients_table',2),(10,'2026_08_13_084000_create_projects_table',2),(11,'2026_08_13_084123_create_qt_invoice_table',2),(14,'2026_08_24_084715_add_unit_id_to_items_table',4),(15,'2026_08_24_074259_create_units_table',5),(16,'2026_08_27_024706_add_period_to_projects_table',6),(17,'2026_08_27_030000_create_qt_invoice_table',7),(18,'2026_08_27_031012_create_qt_invoice_items_table',7),(19,'2026_09_06_040320_add_catalog_item_id_to_qt_invoice_items_table',8),(20,'2026_09_06_073002_add_payment_terms_and_notes_to_qt_invoice_table',9),(21,'2026_09_07_065831_create_payment_terms_table',10),(22,'2026_09_07_081736_create_invoices_table',11),(23,'2026_09_07_082532_alter_payment_terms_add_invoice_id',12),(24,'2026_09_08_023127_alter_payment_terms_add_source_term_id',12);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `module_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_terms`
--

DROP TABLE IF EXISTS `payment_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_terms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_Id` bigint unsigned DEFAULT NULL,
  `invoice_Id` bigint unsigned DEFAULT NULL,
  `source_term_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `condition` text COLLATE utf8mb4_unicode_ci,
  `amount` decimal(12,2) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_terms_created_by_foreign` (`created_by`),
  KEY `payment_terms_updated_by_foreign` (`updated_by`),
  KEY `payment_terms_quotation_id_foreign` (`quotation_Id`),
  KEY `payment_terms_invoice_id_foreign` (`invoice_Id`),
  KEY `payment_terms_source_term_id_foreign` (`source_term_id`),
  CONSTRAINT `payment_terms_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_terms_invoice_id_foreign` FOREIGN KEY (`invoice_Id`) REFERENCES `invoices` (`invoice_Id`) ON DELETE CASCADE,
  CONSTRAINT `payment_terms_quotation_id_foreign` FOREIGN KEY (`quotation_Id`) REFERENCES `qt_invoice` (`quotation_Id`) ON DELETE CASCADE,
  CONSTRAINT `payment_terms_source_term_id_foreign` FOREIGN KEY (`source_term_id`) REFERENCES `payment_terms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_terms_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_terms`
--

LOCK TABLES `payment_terms` WRITE;
/*!40000 ALTER TABLE `payment_terms` DISABLE KEYS */;
INSERT INTO `payment_terms` VALUES (1,21,NULL,NULL,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',3805.49,2,NULL,'2026-09-07 01:10:53','2026-09-07 01:10:53'),(2,21,NULL,NULL,'Payment 2',60.00,'Upon submission of First Draft Report',5708.23,2,NULL,'2026-09-07 01:10:53','2026-09-07 01:10:53'),(22,22,NULL,NULL,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',296.78,2,NULL,'2026-09-07 23:13:47','2026-09-07 23:13:47'),(23,22,NULL,NULL,'Payment 2',40.00,'Upon submission of First Draft Report',296.78,2,NULL,'2026-09-07 23:13:47','2026-09-07 23:13:47'),(24,22,NULL,NULL,'Payment 3',20.00,'Upon submission and acceptance of Final Report',148.39,2,NULL,'2026-09-07 23:13:47','2026-09-07 23:13:47'),(28,23,NULL,NULL,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',3709.58,2,NULL,'2026-09-08 00:38:53','2026-09-08 00:38:53'),(29,23,NULL,NULL,'Payment 2',60.00,'Upon submission and acceptance of Final Report',5564.38,2,NULL,'2026-09-08 00:38:53','2026-09-08 00:38:53'),(32,NULL,25,28,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',3709.58,2,NULL,'2026-09-08 19:32:50','2026-09-08 19:32:50'),(33,NULL,26,29,'Payment 2',60.00,'Upon submission and acceptance of Final Report',5564.38,2,NULL,'2026-09-08 19:32:56','2026-09-08 19:32:56'),(34,24,NULL,NULL,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',296.78,2,NULL,'2026-09-08 19:33:51','2026-09-08 19:33:51'),(35,24,NULL,NULL,'Payment 2',40.00,'Upon submission of First Draft Report',296.78,2,NULL,'2026-09-08 19:33:51','2026-09-08 19:33:51'),(36,24,NULL,NULL,'Payment 3',20.00,'Upon submission and acceptance of Final Report',148.39,2,NULL,'2026-09-08 19:33:51','2026-09-08 19:33:51'),(37,NULL,27,36,'Payment 3',20.00,'Upon submission and acceptance of Final Report',148.39,2,NULL,'2026-09-08 19:34:14','2026-09-08 19:34:14'),(38,NULL,28,34,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',296.78,2,NULL,'2026-09-08 19:36:48','2026-09-08 19:36:48'),(39,NULL,29,35,'Payment 2',40.00,'Upon submission of First Draft Report',296.78,2,NULL,'2026-09-08 19:37:06','2026-09-08 19:37:06'),(40,25,NULL,NULL,'Payment 1',50.00,'Upon acceptance of LOA or issuance of PO',6536.43,3,NULL,'2026-09-14 17:44:33','2026-09-14 17:44:33'),(41,25,NULL,NULL,'Payment 2',50.00,'Upon submission of First Draft Report',6536.43,3,NULL,'2026-09-14 17:44:33','2026-09-14 17:44:33'),(42,26,NULL,NULL,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',4320.00,3,NULL,'2026-09-14 17:53:56','2026-09-14 17:53:56'),(43,26,NULL,NULL,'Payment 2',40.00,'Upon submission of First Draft Report',4320.00,3,NULL,'2026-09-14 17:53:56','2026-09-14 17:53:56'),(44,26,NULL,NULL,'Payment 3',20.00,'Upon submission and acceptance of Final Report',2160.00,3,NULL,'2026-09-14 17:53:56','2026-09-14 17:53:56'),(45,27,NULL,NULL,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',864.00,3,NULL,'2026-09-14 17:54:44','2026-09-14 17:54:44'),(46,27,NULL,NULL,'Payment 2',40.00,'Upon submission of First Draft Report',864.00,3,NULL,'2026-09-14 17:54:44','2026-09-14 17:54:44'),(47,27,NULL,NULL,'Payment 3',20.00,'Upon submission and acceptance of Final Report',432.00,3,NULL,'2026-09-14 17:54:44','2026-09-14 17:54:44'),(48,28,NULL,NULL,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',216.00,3,NULL,'2026-09-14 18:02:19','2026-09-14 18:02:19'),(49,28,NULL,NULL,'Payment 2',40.00,'Upon submission of First Draft Report',216.00,3,NULL,'2026-09-14 18:02:19','2026-09-14 18:02:19'),(50,28,NULL,NULL,'Payment 3',20.00,'Upon submission and acceptance of Final Report',108.00,3,NULL,'2026-09-14 18:02:19','2026-09-14 18:02:19'),(51,29,NULL,NULL,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',383.96,3,NULL,'2026-09-14 18:10:58','2026-09-14 18:10:58'),(52,29,NULL,NULL,'Payment 2',40.00,'Upon submission of First Draft Report',383.96,3,NULL,'2026-09-14 18:10:58','2026-09-14 18:10:58'),(53,29,NULL,NULL,'Payment 3',20.00,'Upon submission and acceptance of Final Report',191.98,3,NULL,'2026-09-14 18:10:58','2026-09-14 18:10:58'),(54,30,NULL,NULL,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',383.96,3,NULL,'2026-09-14 18:12:17','2026-09-14 18:12:17'),(55,30,NULL,NULL,'Payment 2',40.00,'Upon submission of First Draft Report',383.96,3,NULL,'2026-09-14 18:12:17','2026-09-14 18:12:17'),(56,30,NULL,NULL,'Payment 3',20.00,'Upon submission and acceptance of Final Report',191.98,3,NULL,'2026-09-14 18:12:17','2026-09-14 18:12:17'),(57,31,NULL,NULL,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',383.96,3,NULL,'2026-09-14 18:15:32','2026-09-14 18:15:32'),(58,31,NULL,NULL,'Payment 2',40.00,'Upon submission of First Draft Report',383.96,3,NULL,'2026-09-14 18:15:32','2026-09-14 18:15:32'),(59,31,NULL,NULL,'Payment 3',20.00,'Upon submission and acceptance of Final Report',191.98,3,NULL,'2026-09-14 18:15:32','2026-09-14 18:15:32'),(60,32,NULL,NULL,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',1005.47,3,NULL,'2026-09-14 18:16:25','2026-09-14 18:16:25'),(61,32,NULL,NULL,'Payment 2',40.00,'Upon submission of First Draft Report',1005.47,3,NULL,'2026-09-14 18:16:25','2026-09-14 18:16:25'),(62,32,NULL,NULL,'Payment 3',20.00,'Upon submission and acceptance of Final Report',502.74,3,NULL,'2026-09-14 18:16:25','2026-09-14 18:16:25'),(63,NULL,30,60,'Payment 1',40.00,'Upon acceptance of LOA or issuance of PO',1005.47,3,NULL,'2026-09-14 19:06:55','2026-09-14 19:06:55'),(64,33,NULL,NULL,'Payment 1',50.00,'Upon acceptance of LOA or issuance of PO',1186.33,3,NULL,'2026-09-14 19:10:02','2026-09-14 19:10:02'),(65,33,NULL,NULL,'Payment 2',50.00,'Upon submission of First Draft Report',1186.33,3,NULL,'2026-09-14 19:10:02','2026-09-14 19:10:02'),(66,NULL,31,64,'Payment 1',50.00,'Upon acceptance of LOA or issuance of PO',1186.33,3,3,'2026-09-14 19:10:28','2026-09-14 19:10:58');
/*!40000 ALTER TABLE `payment_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `project_Id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_Id` bigint unsigned DEFAULT NULL,
  `pic_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`project_Id`),
  KEY `projects_client_id_foreign` (`client_Id`),
  KEY `projects_created_by_foreign` (`created_by`),
  KEY `projects_updated_by_foreign` (`updated_by`),
  CONSTRAINT `projects_client_id_foreign` FOREIGN KEY (`client_Id`) REFERENCES `clients` (`client_Id`) ON DELETE CASCADE,
  CONSTRAINT `projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (2,'6689','jjjjj',NULL,1,'hhghfcc',NULL,2,NULL,'2026-08-26 21:45:57','2026-08-26 21:45:57'),(3,'435','gdfgs',NULL,2,'bghnhg',NULL,2,NULL,'2026-08-26 23:00:50','2026-08-26 23:00:50'),(4,'3434','hthhj','2 Months 2 Days ; 64 Days',3,'fgfd',NULL,2,NULL,'2026-08-26 23:07:43','2026-08-26 23:07:43'),(5,'544','gfhfd','3 Months 2 Weeks 6 Days ; 113 Days',4,'hgfgh','544',2,NULL,'2026-08-26 23:14:01','2026-08-26 23:14:01'),(6,'56464','fhgfh','1 Month 4 Days ; 36 Days',5,'jjjttytr','4545',2,NULL,'2026-08-26 23:24:03','2026-08-26 23:24:03'),(7,'EHS/GP/BHH/001','hbuhu','1 Month 1 Week ; 38 Days',6,'dfdsfdg','454356',2,NULL,'2026-09-01 00:33:50','2026-09-01 00:33:50'),(8,'EHS/GP/TI5/002','hbuhu','2 Months 1 Week 3 Days ; 72 Days',7,'hhh','68565',2,NULL,'2026-09-01 23:22:22','2026-09-01 23:22:22'),(9,'EHS/JP/VGH/003','hbuhu','3 Months 1 Week 1 Day ; 100 Days',8,'ghytj','565',2,NULL,'2026-09-02 01:03:44','2026-09-02 01:03:44'),(10,'EHS/GP/BGT/004','hbuhu','2 Months 4 Days ; 66 Days',9,'ygcgfdf','9865',2,NULL,'2026-09-02 01:08:58','2026-09-02 01:08:58'),(11,'EHS/RP/DRG/005','idejhgf','1 Month 3 Weeks 6 Days ; 58 Days',10,'tgfbfh','4654',2,NULL,'2026-09-02 01:24:12','2026-09-02 01:24:12'),(12,'EHS/JP/VGF/006','hgf','1 Month 3 Weeks 4 Days ; 56 Days',11,'vbnvg','68786',2,NULL,'2026-09-03 00:15:34','2026-09-03 00:15:34'),(14,'EHS/MP/FGD/007','cbv fgf','2 Months 2 Days ; 64 Days',13,'dfdfb gf','433523',2,NULL,'2026-09-05 22:57:21','2026-09-05 22:57:21'),(15,'EHS/CP/FV3/008','asad reff','2 Months 1 Week 3 Days ; 72 Days',14,'dsfdf','23412',2,NULL,'2026-09-05 23:39:50','2026-09-05 23:39:50'),(16,'EHS/MP/HHY/009','bhj ygy','2 Months 3 Days ; 65 Days',15,'yuf','667',2,NULL,'2026-09-05 23:46:35','2026-09-05 23:46:35'),(17,'EHS/GP/NHI/010','hbu','2 Weeks ; 15 Days',16,'ygf','678',2,NULL,'2026-09-05 23:54:12','2026-09-05 23:54:12'),(18,'EHS/MP/7J6/011','tht u7u','2 Weeks ; 15 Days',17,'yuy','5656',2,NULL,'2026-09-06 00:01:52','2026-09-06 00:01:52'),(19,'EHS/RP/XDS/012','hbuhu','2 Months 1 Week 2 Days ; 71 Days',18,'sdsc','2324',2,NULL,'2026-09-06 00:07:26','2026-09-06 00:07:26'),(20,'EHS/CP/GTT/013','grtg tygrt','3 Months 1 Week ; 99 Days',19,'rgrt tg','56576',2,NULL,'2026-09-07 01:10:53','2026-09-07 01:10:53'),(21,'EHS/RP/RE/014','fdeg','3 Months 1 Week ; 99 Days',20,'dfdg','56567',2,NULL,'2026-09-07 23:13:47','2026-09-07 23:13:47'),(22,'EHS/CP/BYU/015','hbu y','1 Month 3 Weeks 6 Days ; 58 Days',21,'dsfvc','34532',2,NULL,'2026-09-08 00:38:53','2026-09-08 00:38:53'),(23,'EHS/JP/FUD/016','jjg gh','4 Months 2 Days ; 125 Days',22,'ghbgb','564456',2,NULL,'2026-09-08 19:33:50','2026-09-08 19:33:50'),(24,'EHS/MP/FR5/017','frgtb','1 Month 3 Weeks 4 Days ; 56 Days',23,'frgt tybv','36655',3,NULL,'2026-09-14 17:44:33','2026-09-14 17:44:33'),(25,'EHS/RP/GF/018','gdfgfb','3 Months 3 Weeks ; 113 Days',24,'dgdg','5657',3,NULL,'2026-09-14 17:53:56','2026-09-14 17:53:56'),(26,'EHS/RP/GR5/019','fdsv tgtr','2 Months 3 Weeks 2 Days ; 85 Days',25,'frer gr','3456',3,NULL,'2026-09-14 17:54:44','2026-09-14 17:54:44'),(27,'EHS/GP/BHT/020','hgf','2 Months 1 Week 2 Days ; 71 Days',26,'bcvbn drg','46547',3,NULL,'2026-09-14 18:02:19','2026-09-14 18:02:19'),(28,'EHS/MP/G/021','dfgfd','3 Months 6 Days ; 98 Days',27,'gfgnb','5467',3,NULL,'2026-09-14 18:10:58','2026-09-14 18:10:58'),(29,'EHS/MP/G/021','dfgfd','3 Months 6 Days ; 98 Days',27,'gfgnb','5467',3,NULL,'2026-09-14 18:12:17','2026-09-14 18:12:17'),(30,'EHS/MP/G/021','dfgfd','3 Months 6 Days ; 98 Days',27,'gfgnb','5467',3,NULL,'2026-09-14 18:15:32','2026-09-14 18:15:32'),(31,'EHS/GP/G/022','fghgn','1 Day ; 2 Days',28,'hhh','5657',3,NULL,'2026-09-14 18:16:25','2026-09-14 18:16:25'),(32,'EHS/MP/DH/023','hjhgk','1 Month 3 Weeks 4 Days ; 56 Days',29,'fdg','456',3,NULL,'2026-09-14 19:10:02','2026-09-14 19:10:02');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qt_invoice`
--

DROP TABLE IF EXISTS `qt_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qt_invoice` (
  `quotation_Id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_Id` bigint unsigned DEFAULT NULL,
  `quotation_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grand_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_terms` text COLLATE utf8mb4_unicode_ci,
  `additional_notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`quotation_Id`),
  KEY `fk_qt_invoice_project` (`project_Id`),
  KEY `fk_qt_invoice_created_by` (`created_by`),
  KEY `fk_qt_invoice_updated_by` (`updated_by`),
  CONSTRAINT `fk_qt_invoice_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_qt_invoice_project` FOREIGN KEY (`project_Id`) REFERENCES `projects` (`project_Id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qt_invoice_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qt_invoice`
--

LOCK TABLES `qt_invoice` WRITE;
/*!40000 ALTER TABLE `qt_invoice` DISABLE KEYS */;
INSERT INTO `qt_invoice` VALUES (3,2,'QT-1787809557',11053.79,NULL,NULL,2,NULL,'2026-08-26 21:45:57','2026-08-26 21:45:57'),(4,3,'QT-1787814050',2200.00,NULL,NULL,2,NULL,'2026-08-26 23:00:50','2026-08-26 23:00:50'),(5,4,'QT-1787814463',3234.00,NULL,NULL,2,NULL,'2026-08-26 23:07:43','2026-08-26 23:07:43'),(6,5,'QT-1787814841',554643.00,NULL,NULL,2,NULL,'2026-08-26 23:14:01','2026-08-26 23:14:01'),(7,6,'QT-1787815443',23024.00,NULL,NULL,2,NULL,'2026-08-26 23:24:03','2026-08-26 23:24:03'),(8,7,'QT-1788251630',7789.00,NULL,NULL,2,NULL,'2026-09-01 00:33:50','2026-09-01 00:33:50'),(9,8,'QT-1788333742',17707.00,NULL,NULL,2,NULL,'2026-09-01 23:22:22','2026-09-01 23:22:22'),(10,9,'EHS/JP/VGH/003-QUO/2026/001',21884485.91,NULL,NULL,2,NULL,'2026-09-02 01:03:44','2026-09-02 01:03:44'),(11,10,'EHS/GP/BGT/004-QUO/2026/001',53809.00,NULL,NULL,2,NULL,'2026-09-02 01:08:58','2026-09-02 01:08:58'),(12,11,'EHS/RP/DRG/005-QUO/2026/001',172899.13,NULL,NULL,2,NULL,'2026-09-02 01:24:12','2026-09-02 01:24:12'),(13,12,'EHS/JP/VGF/006-QUO/2026/001',76533.00,NULL,NULL,2,NULL,'2026-09-03 00:15:34','2026-09-03 00:15:34'),(15,14,'EHS/MP/FGD/007-QUO/2026/001',270.41,NULL,NULL,2,NULL,'2026-09-05 22:57:21','2026-09-05 22:57:21'),(16,15,'EHS/CP/FV3/008-QUO/2026/001',1662.33,NULL,NULL,2,NULL,'2026-09-05 23:39:50','2026-09-05 23:39:50'),(17,16,'EHS/MP/HHY/009-QUO/2026/001',1779.80,NULL,NULL,2,NULL,'2026-09-05 23:46:35','2026-09-05 23:46:35'),(18,17,'EHS/GP/NHI/010-QUO/2026/001',7110.00,NULL,'nnj',2,NULL,'2026-09-05 23:54:12','2026-09-05 23:54:12'),(19,18,'EHS/MP/7J6/011-QUO/2026/001',22995.00,'[{\"percentage\":\"40%\",\"condition\":\"Upon acceptance of LOA or issuance of PO\"},{\"percentage\":\"40%\",\"condition\":\"Upon submission of First Draft Report\"},{\"percentage\":\"20%\",\"condition\":\"Upon submission and acceptance of Final Report\"}]',NULL,2,NULL,'2026-09-06 00:01:52','2026-09-06 00:01:52'),(20,19,'EHS/RP/XDS/012-QUO/2026/001',6756.00,'[{\"percentage\":\"55%\",\"condition\":\"Upon acceptance of LOA or issuance of PO\"},{\"percentage\":\"45%\",\"condition\":\"Upon submission of First Draft Report\"}]','test',2,NULL,'2026-09-06 00:07:26','2026-09-06 00:07:26'),(21,20,'EHS/CP/GTT/013-QUO/2026/001',8809.00,'Payment 1 : 40% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 60% - Upon submission of First Draft Report',NULL,2,NULL,'2026-09-07 01:10:53','2026-09-07 01:10:53'),(22,21,'EHS/RP/RE/014-QUO/2026/001',687.00,'Payment 1 : 40% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 40% - Upon submission of First Draft Report\nPayment 3 : 20% - Upon submission and acceptance of Final Report','ghg',2,NULL,'2026-09-07 23:13:47','2026-09-07 23:13:47'),(23,22,'EHS/CP/BYU/015-QUO/2026/001',8587.00,'Payment 1 : 40% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 60% - Upon submission and acceptance of Final Report',NULL,2,NULL,'2026-09-08 00:38:53','2026-09-08 00:38:53'),(24,23,'EHS/JP/FUD/016-QUO/2026/001',687.00,'Payment 1 : 40% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 40% - Upon submission of First Draft Report\nPayment 3 : 20% - Upon submission and acceptance of Final Report',NULL,2,NULL,'2026-09-08 19:33:51','2026-09-08 19:33:51'),(25,24,'EHS/MP/FR5/017-QUO/2026/001',12104.51,'Payment 1 : 50% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 50% - Upon submission of First Draft Report',NULL,3,NULL,'2026-09-14 17:44:33','2026-09-14 17:44:33'),(26,25,'EHS/RP/GF/018-QUO/2026/001',10000.00,'Payment 1 : 40% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 40% - Upon submission of First Draft Report\nPayment 3 : 20% - Upon submission and acceptance of Final Report',NULL,3,NULL,'2026-09-14 17:53:56','2026-09-14 17:53:56'),(27,26,'EHS/RP/GR5/019-QUO/2026/001',2000.00,'Payment 1 : 40% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 40% - Upon submission of First Draft Report\nPayment 3 : 20% - Upon submission and acceptance of Final Report',NULL,3,NULL,'2026-09-14 17:54:44','2026-09-14 17:54:44'),(28,27,'EHS/GP/BHT/020-QUO/2026/001',500.00,'Payment 1 : 40% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 40% - Upon submission of First Draft Report\nPayment 3 : 20% - Upon submission and acceptance of Final Report',NULL,3,NULL,'2026-09-14 18:02:19','2026-09-14 18:02:19'),(29,28,'EHS/MP/G/021-QUO/2026/001',888.80,'Payment 1 : 40% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 40% - Upon submission of First Draft Report\nPayment 3 : 20% - Upon submission and acceptance of Final Report',NULL,3,NULL,'2026-09-14 18:10:58','2026-09-14 18:10:58'),(30,29,'EHS/MP/G/021-QUO/2026/001',888.80,'Payment 1 : 40% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 40% - Upon submission of First Draft Report\nPayment 3 : 20% - Upon submission and acceptance of Final Report',NULL,3,NULL,'2026-09-14 18:12:17','2026-09-14 18:12:17'),(31,30,'EHS/MP/G/021-QUO/2026/001',888.80,'Payment 1 : 40% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 40% - Upon submission of First Draft Report\nPayment 3 : 20% - Upon submission and acceptance of Final Report',NULL,3,NULL,'2026-09-14 18:15:32','2026-09-14 18:15:32'),(32,31,'EHS/GP/G/022-QUO/2026/001',2327.49,'Payment 1 : 40% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 40% - Upon submission of First Draft Report\nPayment 3 : 20% - Upon submission and acceptance of Final Report',NULL,3,NULL,'2026-09-14 18:16:25','2026-09-14 18:16:25'),(33,32,'EHS/MP/DH/023-QUO/2026/001',2196.90,'Payment 1 : 50% - Upon acceptance of LOA or issuance of PO\nPayment 2 : 50% - Upon submission of First Draft Report',NULL,3,NULL,'2026-09-14 19:10:02','2026-09-14 19:10:02');
/*!40000 ALTER TABLE `qt_invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qt_invoice_items`
--

DROP TABLE IF EXISTS `qt_invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qt_invoice_items` (
  `item_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `module_id` bigint unsigned DEFAULT NULL,
  `catalog_item_id` bigint unsigned DEFAULT NULL,
  `unit_qty` int NOT NULL DEFAULT '1',
  `days` int NOT NULL DEFAULT '1',
  `daily_rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `mark_up` decimal(8,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `qt_invoice_items_quotation_id_foreign` (`quotation_id`),
  KEY `qt_invoice_items_module_id_foreign` (`module_id`),
  KEY `qt_invoice_items_catalog_item_id_foreign` (`catalog_item_id`),
  CONSTRAINT `qt_invoice_items_catalog_item_id_foreign` FOREIGN KEY (`catalog_item_id`) REFERENCES `items` (`item_id`) ON DELETE SET NULL,
  CONSTRAINT `qt_invoice_items_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`module_id`) ON DELETE SET NULL,
  CONSTRAINT `qt_invoice_items_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `qt_invoice` (`quotation_Id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qt_invoice_items`
--

LOCK TABLES `qt_invoice_items` WRITE;
/*!40000 ALTER TABLE `qt_invoice_items` DISABLE KEYS */;
INSERT INTO `qt_invoice_items` VALUES (4,3,102,NULL,2,1,778.00,7.00,1664.92,'2026-08-26 21:45:57','2026-08-26 21:45:57'),(5,3,103,NULL,2,2,2343.00,0.18,9388.87,'2026-08-26 21:45:57','2026-08-26 21:45:57'),(6,4,1,NULL,1,1,2200.00,0.00,2200.00,'2026-08-26 23:00:50','2026-08-26 23:00:50'),(7,5,103,NULL,1,1,3234.00,0.00,3234.00,'2026-08-26 23:07:43','2026-08-26 23:07:43'),(8,6,3,NULL,1,1,554643.00,0.00,554643.00,'2026-08-26 23:14:01','2026-08-26 23:14:01'),(9,7,2,NULL,4,1,5756.00,0.00,23024.00,'2026-08-26 23:24:03','2026-08-26 23:24:03'),(10,8,102,NULL,1,1,7789.00,0.00,7789.00,'2026-09-01 00:33:50','2026-09-01 00:33:50'),(11,9,1,NULL,1,1,0.00,0.00,0.00,'2026-09-01 23:22:22','2026-09-01 23:22:22'),(12,9,102,NULL,1,1,775.00,0.00,775.00,'2026-09-01 23:22:22','2026-09-01 23:22:22'),(13,9,103,NULL,3,1,5644.00,0.00,16932.00,'2026-09-01 23:22:22','2026-09-01 23:22:22'),(14,10,3,NULL,4,1,5464564.00,0.12,21884485.91,'2026-09-02 01:03:44','2026-09-02 01:03:44'),(15,11,102,NULL,7,1,7687.00,0.00,53809.00,'2026-09-02 01:08:58','2026-09-02 01:08:58'),(16,12,2,NULL,5,1,34566.00,0.04,172899.13,'2026-09-02 01:24:12','2026-09-02 01:24:12'),(17,13,103,NULL,1,1,76533.00,0.00,76533.00,'2026-09-03 00:15:34','2026-09-03 00:15:34'),(18,15,1,13,3,1,90.00,0.15,270.41,'2026-09-05 22:57:21','2026-09-05 22:57:21'),(19,16,1,7,1,3,554.00,0.02,1662.33,'2026-09-05 23:39:50','2026-09-05 23:39:50'),(20,17,1,14,1,2,889.90,0.00,1779.80,'2026-09-05 23:46:35','2026-09-05 23:46:35'),(21,18,2,2,2,1,3555.00,0.00,7110.00,'2026-09-05 23:54:12','2026-09-05 23:54:12'),(22,19,103,16,3,1,7665.00,0.00,22995.00,'2026-09-06 00:01:52','2026-09-06 00:01:52'),(23,20,102,8,1,1,6756.00,0.00,6756.00,'2026-09-06 00:07:26','2026-09-06 00:07:26'),(24,21,1,14,1,1,8809.00,0.00,8809.00,'2026-09-07 01:10:53','2026-09-07 01:10:53'),(25,22,102,8,1,1,687.00,0.00,687.00,'2026-09-07 23:13:47','2026-09-07 23:13:47'),(26,23,1,7,3,3,559.00,0.00,5031.00,'2026-09-08 00:38:53','2026-09-08 00:38:53'),(27,23,1,14,4,1,889.00,0.00,3556.00,'2026-09-08 00:38:53','2026-09-08 00:38:53'),(28,24,102,8,1,1,687.00,0.00,687.00,'2026-09-08 19:33:51','2026-09-08 19:33:51'),(29,25,2,2,1,1,3500.00,0.07,3502.45,'2026-09-14 17:44:33','2026-09-14 17:44:33'),(30,25,2,11,1,1,7870.00,0.17,7883.38,'2026-09-14 17:44:33','2026-09-14 17:44:33'),(31,25,102,8,1,1,678.00,6.00,718.68,'2026-09-14 17:44:33','2026-09-14 17:44:33'),(32,26,3,4,1,1,10000.00,0.00,10000.00,'2026-09-14 17:53:56','2026-09-14 17:53:56'),(33,27,1,1,1,1,2000.00,0.00,2000.00,'2026-09-14 17:54:44','2026-09-14 17:54:44'),(34,28,1,7,1,1,500.00,0.00,500.00,'2026-09-14 18:02:19','2026-09-14 18:02:19'),(35,29,1,14,1,1,888.00,0.09,888.80,'2026-09-14 18:10:58','2026-09-14 18:10:58'),(36,30,1,14,1,1,888.00,0.09,888.80,'2026-09-14 18:12:17','2026-09-14 18:12:17'),(37,31,1,14,1,1,888.00,0.09,888.80,'2026-09-14 18:15:32','2026-09-14 18:15:32'),(38,32,103,16,1,1,2324.00,0.15,2327.49,'2026-09-14 18:16:25','2026-09-14 18:16:25'),(39,33,1,7,1,1,500.00,0.14,500.70,'2026-09-14 19:10:02','2026-09-14 19:10:02'),(40,33,1,14,1,1,888.00,15.00,1021.20,'2026-09-14 19:10:02','2026-09-14 19:10:02'),(41,33,3,10,1,1,675.00,0.00,675.00,'2026-09-14 19:10:02','2026-09-14 19:10:02');
/*!40000 ALTER TABLE `qt_invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service` (
  `service_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('gGFz5cmtXtFuYRUvMI6NQFNnW0Luyt6o4mXUJnQ3',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMzNsOVcxRWE2R21YVERxMkpUcXZ2NjJsSW9UNW9UT0dCZmdlZ01jeSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9ob21lIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=',1789447879);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `units` (
  `unit_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
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

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin User','admin@example.com',NULL,'$2y$12$2UhPvgPscI1nX4ItaCrxcOAnoZDfSYPrNXDcPHrqYA2rojRJvdFo2',NULL,'2026-08-19 18:30:01','2026-08-19 18:30:01'),(2,'alya','alya@gmail.com',NULL,'$2y$12$wqrs2LksG/FjhBmvB.umhene323bfEKpwZbo4RI4kl9X0u42Etg76',NULL,'2026-08-22 19:32:41','2026-08-22 19:32:41'),(3,'hsjdn','try123@gmail.com',NULL,'$2y$12$KrKTj3MDJo4OcYwnWl135Ogp2vOjOKs9H5eNTcWw0pjBoRJvHZyjC',NULL,'2026-09-14 01:21:26','2026-09-14 01:21:26');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'quotation'
--

--
-- Dumping routines for database 'quotation'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-15 14:13:15
