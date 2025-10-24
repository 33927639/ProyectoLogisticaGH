-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sanalogistics
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `admin_sessions`
--

DROP TABLE IF EXISTS `admin_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_sessions_user_id_index` (`user_id`),
  KEY `admin_sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_sessions`
--

LOCK TABLES `admin_sessions` WRITE;
/*!40000 ALTER TABLE `admin_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alert_statuses`
--

DROP TABLE IF EXISTS `alert_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alert_statuses` (
  `id_alert` int NOT NULL AUTO_INCREMENT,
  `name_alert` varchar(50) NOT NULL,
  `description` text,
  `threshold_km` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_alert`),
  UNIQUE KEY `name_alert` (`name_alert`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alert_statuses`
--

LOCK TABLES `alert_statuses` WRITE;
/*!40000 ALTER TABLE `alert_statuses` DISABLE KEYS */;
INSERT INTO `alert_statuses` VALUES (1,'Mantenimiento 5,000 km','Mantenimiento preventivo cada 5,000 km',5000.00,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(2,'Mantenimiento 10,000 km','Mantenimiento mayor cada 10,000 km',10000.00,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(3,'Cambio de aceite','Cambio de aceite cada 3,000 km',3000.00,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(4,'Revisión general','Revisión completa cada 15,000 km',15000.00,'2025-10-21 22:35:16','2025-10-21 22:35:16');
/*!40000 ALTER TABLE `alert_statuses` ENABLE KEYS */;
UNLOCK TABLES;

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
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('proyecto-cache-delivery_stats_2025-10-23','a:4:{i:0;O:41:\"Filament\\Widgets\\StatsOverviewWidget\\Stat\":17:{s:9:\"\0*\0except\";a:0:{}s:13:\"componentName\";N;s:10:\"attributes\";N;s:8:\"\0*\0chart\";N;s:13:\"\0*\0chartColor\";N;s:8:\"\0*\0color\";s:7:\"primary\";s:7:\"\0*\0icon\";N;s:14:\"\0*\0description\";s:17:\"Total programadas\";s:18:\"\0*\0descriptionIcon\";s:24:\"heroicon-m-calendar-days\";s:26:\"\0*\0descriptionIconPosition\";N;s:19:\"\0*\0descriptionColor\";N;s:18:\"\0*\0extraAttributes\";a:0:{}s:24:\"\0*\0shouldOpenUrlInNewTab\";b:0;s:6:\"\0*\0url\";N;s:5:\"\0*\0id\";N;s:8:\"\0*\0label\";s:12:\"Entregas Hoy\";s:8:\"\0*\0value\";i:2;}i:1;O:41:\"Filament\\Widgets\\StatsOverviewWidget\\Stat\":17:{s:9:\"\0*\0except\";a:0:{}s:13:\"componentName\";N;s:10:\"attributes\";N;s:8:\"\0*\0chart\";N;s:13:\"\0*\0chartColor\";N;s:8:\"\0*\0color\";s:4:\"gray\";s:7:\"\0*\0icon\";N;s:14:\"\0*\0description\";s:11:\"Por aceptar\";s:18:\"\0*\0descriptionIcon\";s:16:\"heroicon-m-clock\";s:26:\"\0*\0descriptionIconPosition\";N;s:19:\"\0*\0descriptionColor\";N;s:18:\"\0*\0extraAttributes\";a:0:{}s:24:\"\0*\0shouldOpenUrlInNewTab\";b:0;s:6:\"\0*\0url\";N;s:5:\"\0*\0id\";N;s:8:\"\0*\0label\";s:10:\"Pendientes\";s:8:\"\0*\0value\";i:2;}i:2;O:41:\"Filament\\Widgets\\StatsOverviewWidget\\Stat\":17:{s:9:\"\0*\0except\";a:0:{}s:13:\"componentName\";N;s:10:\"attributes\";N;s:8:\"\0*\0chart\";N;s:13:\"\0*\0chartColor\";N;s:8:\"\0*\0color\";s:7:\"warning\";s:7:\"\0*\0icon\";N;s:14:\"\0*\0description\";s:25:\"Agarradas: 0 | En Ruta: 0\";s:18:\"\0*\0descriptionIcon\";s:16:\"heroicon-m-truck\";s:26:\"\0*\0descriptionIconPosition\";N;s:19:\"\0*\0descriptionColor\";N;s:18:\"\0*\0extraAttributes\";a:0:{}s:24:\"\0*\0shouldOpenUrlInNewTab\";b:0;s:6:\"\0*\0url\";N;s:5:\"\0*\0id\";N;s:8:\"\0*\0label\";s:10:\"En Proceso\";s:8:\"\0*\0value\";i:0;}i:3;O:41:\"Filament\\Widgets\\StatsOverviewWidget\\Stat\":17:{s:9:\"\0*\0except\";a:0:{}s:13:\"componentName\";N;s:10:\"attributes\";N;s:8:\"\0*\0chart\";N;s:13:\"\0*\0chartColor\";N;s:8:\"\0*\0color\";s:7:\"success\";s:7:\"\0*\0icon\";N;s:14:\"\0*\0description\";s:13:\"0% completado\";s:18:\"\0*\0descriptionIcon\";s:23:\"heroicon-m-check-circle\";s:26:\"\0*\0descriptionIconPosition\";N;s:19:\"\0*\0descriptionColor\";N;s:18:\"\0*\0extraAttributes\";a:0:{}s:24:\"\0*\0shouldOpenUrlInNewTab\";b:0;s:6:\"\0*\0url\";N;s:5:\"\0*\0id\";N;s:8:\"\0*\0label\";s:11:\"Completadas\";s:8:\"\0*\0value\";i:0;}}',1761233938),('proyecto-cache-fleet_stats','a:4:{i:0;O:41:\"Filament\\Widgets\\StatsOverviewWidget\\Stat\":17:{s:9:\"\0*\0except\";a:0:{}s:13:\"componentName\";N;s:10:\"attributes\";N;s:8:\"\0*\0chart\";N;s:13:\"\0*\0chartColor\";N;s:8:\"\0*\0color\";s:7:\"primary\";s:7:\"\0*\0icon\";N;s:14:\"\0*\0description\";s:18:\"Vehículos activos\";s:18:\"\0*\0descriptionIcon\";s:16:\"heroicon-m-truck\";s:26:\"\0*\0descriptionIconPosition\";N;s:19:\"\0*\0descriptionColor\";N;s:18:\"\0*\0extraAttributes\";a:0:{}s:24:\"\0*\0shouldOpenUrlInNewTab\";b:0;s:6:\"\0*\0url\";N;s:5:\"\0*\0id\";N;s:8:\"\0*\0label\";s:11:\"Flota Total\";s:8:\"\0*\0value\";i:4;}i:1;O:41:\"Filament\\Widgets\\StatsOverviewWidget\\Stat\":17:{s:9:\"\0*\0except\";a:0:{}s:13:\"componentName\";N;s:10:\"attributes\";N;s:8:\"\0*\0chart\";N;s:13:\"\0*\0chartColor\";N;s:8:\"\0*\0color\";s:7:\"success\";s:7:\"\0*\0icon\";N;s:14:\"\0*\0description\";s:15:\"0% utilización\";s:18:\"\0*\0descriptionIcon\";s:15:\"heroicon-m-play\";s:26:\"\0*\0descriptionIconPosition\";N;s:19:\"\0*\0descriptionColor\";N;s:18:\"\0*\0extraAttributes\";a:0:{}s:24:\"\0*\0shouldOpenUrlInNewTab\";b:0;s:6:\"\0*\0url\";N;s:5:\"\0*\0id\";N;s:8:\"\0*\0label\";s:13:\"En Operación\";s:8:\"\0*\0value\";i:0;}i:2;O:41:\"Filament\\Widgets\\StatsOverviewWidget\\Stat\":17:{s:9:\"\0*\0except\";a:0:{}s:13:\"componentName\";N;s:10:\"attributes\";N;s:8:\"\0*\0chart\";N;s:13:\"\0*\0chartColor\";N;s:8:\"\0*\0color\";s:7:\"warning\";s:7:\"\0*\0icon\";N;s:14:\"\0*\0description\";s:14:\"No disponibles\";s:18:\"\0*\0descriptionIcon\";s:29:\"heroicon-m-wrench-screwdriver\";s:26:\"\0*\0descriptionIconPosition\";N;s:19:\"\0*\0descriptionColor\";N;s:18:\"\0*\0extraAttributes\";a:0:{}s:24:\"\0*\0shouldOpenUrlInNewTab\";b:0;s:6:\"\0*\0url\";N;s:5:\"\0*\0id\";N;s:8:\"\0*\0label\";s:16:\"En Mantenimiento\";s:8:\"\0*\0value\";i:0;}i:3;O:41:\"Filament\\Widgets\\StatsOverviewWidget\\Stat\":17:{s:9:\"\0*\0except\";a:0:{}s:13:\"componentName\";N;s:10:\"attributes\";N;s:8:\"\0*\0chart\";N;s:13:\"\0*\0chartColor\";N;s:8:\"\0*\0color\";s:4:\"info\";s:7:\"\0*\0icon\";N;s:14:\"\0*\0description\";s:19:\"Listos para asignar\";s:18:\"\0*\0descriptionIcon\";s:23:\"heroicon-m-check-circle\";s:26:\"\0*\0descriptionIconPosition\";N;s:19:\"\0*\0descriptionColor\";N;s:18:\"\0*\0extraAttributes\";a:0:{}s:24:\"\0*\0shouldOpenUrlInNewTab\";b:0;s:6:\"\0*\0url\";N;s:5:\"\0*\0id\";N;s:8:\"\0*\0label\";s:11:\"Disponibles\";s:8:\"\0*\0value\";i:4;}}',1761233953),('proyecto-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3','i:1;',1761233783),('proyecto-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer','i:1761233783;',1761233783);
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
  PRIMARY KEY (`key`)
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
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id_customer` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `nit` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text,
  `municipality_id` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_customer`),
  KEY `municipality_id` (`municipality_id`),
  KEY `idx_customers_status_created` (`status`,`created_at`),
  CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id_municipality`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Supermercados La Torre','12345678-9','2234-5678','compras@latorre.com.gt','Zona 10, Ciudad de Guatemala',1,1,'2025-10-21 23:09:29','2025-10-21 23:09:29'),(2,'Distribuidora El Sol','87654321-0','2456-7890','distribuciones@elsol.com','Zona 11, Mixco',2,1,'2025-10-21 23:09:29','2025-10-21 23:09:29'),(3,'Farmacia Central','11223344-5','2333-4444','gerencia@farmaciacentral.gt','Zona 1, Centro Histórico',1,1,'2025-10-21 23:09:29','2025-10-21 23:09:29'),(4,'Restaurante Los Arcos','55667788-9','2567-8901','administracion@losarcos.gt','Zona 14, Ciudad de Guatemala',1,1,'2025-10-21 23:09:29','2025-10-21 23:09:29');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deliveries`
--

DROP TABLE IF EXISTS `deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deliveries` (
  `id_delivery` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `delivery_date` date NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `route_id` int NOT NULL,
  `status_id` int NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_delivery`),
  KEY `route_id` (`route_id`),
  KEY `status_id` (`status_id`),
  KEY `idx_deliveries_status_created` (`status`,`created_at`),
  CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id_route`),
  CONSTRAINT `deliveries_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `delivery_statuses` (`id_status`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deliveries`
--

LOCK TABLES `deliveries` WRITE;
/*!40000 ALTER TABLE `deliveries` DISABLE KEYS */;
INSERT INTO `deliveries` VALUES (1,NULL,'2025-10-23',NULL,NULL,NULL,1,1,1,'2025-10-22 01:43:03','2025-10-22 01:43:03'),(2,NULL,'2025-10-23',NULL,NULL,NULL,1,3,1,'2025-10-22 01:48:05','2025-10-22 22:49:23'),(3,NULL,'2025-10-24',NULL,NULL,NULL,2,3,1,'2025-10-22 01:48:05','2025-10-22 02:57:03'),(4,NULL,'2025-10-25',NULL,NULL,NULL,3,2,1,'2025-10-22 01:48:05','2025-10-22 01:48:05'),(5,NULL,'2025-10-24',NULL,NULL,NULL,2,3,1,'2025-10-22 04:20:30','2025-10-22 23:19:30'),(7,NULL,'2025-10-22',NULL,NULL,NULL,4,1,1,'2025-10-22 21:18:26','2025-10-22 21:18:26'),(8,NULL,'2025-10-22',NULL,NULL,NULL,4,3,1,'2025-10-22 21:20:41','2025-10-22 22:35:27');
/*!40000 ALTER TABLE `deliveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_assignments`
--

DROP TABLE IF EXISTS `delivery_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_assignments` (
  `delivery_id` int NOT NULL,
  `vehicle_id` int NOT NULL,
  `driver_id` int NOT NULL,
  `assignment_date` date NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `driver_status` enum('pendiente','agarrado','en_ruta','completado','cancelado') NOT NULL DEFAULT 'pendiente',
  `assigned_at` timestamp NULL DEFAULT NULL,
  `notes` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`delivery_id`,`vehicle_id`,`driver_id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `driver_id` (`driver_id`),
  CONSTRAINT `delivery_assignments_ibfk_1` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id_delivery`),
  CONSTRAINT `delivery_assignments_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id_vehicle`),
  CONSTRAINT `delivery_assignments_ibfk_3` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id_driver`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_assignments`
--

LOCK TABLES `delivery_assignments` WRITE;
/*!40000 ALTER TABLE `delivery_assignments` DISABLE KEYS */;
INSERT INTO `delivery_assignments` VALUES (1,1,1,'2025-10-22',1,'pendiente','2025-10-23 04:17:29','Asignación de prueba para testing','2025-10-22 16:17:29',NULL),(2,1,1,'2025-10-22',0,'completado',NULL,NULL,'2025-10-22 01:48:05','2025-10-22 22:49:23'),(2,1,2,'2025-10-22',1,'pendiente','2025-10-23 04:17:29','Segunda asignación de prueba','2025-10-22 16:17:29',NULL),(3,2,2,'2025-10-22',0,'completado',NULL,NULL,'2025-10-22 01:48:05','2025-10-22 02:57:03'),(4,1,1,'2025-10-22',1,'completado',NULL,NULL,'2025-10-22 01:48:05','2025-10-22 22:04:48'),(5,1,1,'2025-10-22',1,'completado','2025-10-23 04:21:16','Envio','2025-10-22 22:21:41','2025-10-22 23:19:30'),(8,3,1,'2025-10-22',1,'completado','2025-10-23 04:23:59','Envio','2025-10-22 22:24:12','2025-10-22 22:35:27');
/*!40000 ALTER TABLE `delivery_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_guides`
--

DROP TABLE IF EXISTS `delivery_guides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_guides` (
  `id_guide` int NOT NULL AUTO_INCREMENT,
  `delivery_id` int NOT NULL,
  `guide_number` varchar(50) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_guide`),
  UNIQUE KEY `guide_number` (`guide_number`),
  KEY `delivery_id` (`delivery_id`),
  CONSTRAINT `delivery_guides_ibfk_1` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id_delivery`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_guides`
--

LOCK TABLES `delivery_guides` WRITE;
/*!40000 ALTER TABLE `delivery_guides` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_guides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_logs`
--

DROP TABLE IF EXISTS `delivery_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_logs` (
  `id_log` int NOT NULL AUTO_INCREMENT,
  `delivery_id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` varchar(100) NOT NULL,
  `note` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `delivery_id` (`delivery_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `delivery_logs_ibfk_1` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id_delivery`),
  CONSTRAINT `delivery_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_logs`
--

LOCK TABLES `delivery_logs` WRITE;
/*!40000 ALTER TABLE `delivery_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_payments`
--

DROP TABLE IF EXISTS `delivery_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_payments` (
  `id_payment` int NOT NULL AUTO_INCREMENT,
  `delivery_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('CASH','CARD','TRANSFER','OTHER') DEFAULT 'CASH',
  `payment_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_payment`),
  UNIQUE KEY `delivery_id` (`delivery_id`),
  CONSTRAINT `delivery_payments_ibfk_1` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id_delivery`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_payments`
--

LOCK TABLES `delivery_payments` WRITE;
/*!40000 ALTER TABLE `delivery_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_products`
--

DROP TABLE IF EXISTS `delivery_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_products` (
  `delivery_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `unit_price` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(12,2) GENERATED ALWAYS AS ((`quantity` * `unit_price`)) STORED,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`delivery_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `delivery_products_ibfk_1` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id_delivery`),
  CONSTRAINT `delivery_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_products`
--

LOCK TABLES `delivery_products` WRITE;
/*!40000 ALTER TABLE `delivery_products` DISABLE KEYS */;
INSERT INTO `delivery_products` (`delivery_id`, `product_id`, `quantity`, `unit_price`, `created_at`, `updated_at`) VALUES (8,2,1,85.50,'2025-10-22 21:20:41','2025-10-22 21:20:41');
/*!40000 ALTER TABLE `delivery_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_statuses`
--

DROP TABLE IF EXISTS `delivery_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_statuses` (
  `id_status` int NOT NULL AUTO_INCREMENT,
  `name_status` varchar(50) NOT NULL,
  `description` text,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_status`),
  UNIQUE KEY `name_status` (`name_status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_statuses`
--

LOCK TABLES `delivery_statuses` WRITE;
/*!40000 ALTER TABLE `delivery_statuses` DISABLE KEYS */;
INSERT INTO `delivery_statuses` VALUES (1,'Pendiente','Entrega programada',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(2,'En Ruta','Vehículo en camino',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(3,'Entregado','Entrega completada',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(4,'Cancelado','Entrega cancelada',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(5,'Reprogramado','Nueva fecha asignada',1,'2025-10-21 22:35:16','2025-10-21 22:35:16');
/*!40000 ALTER TABLE `delivery_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id_department` int NOT NULL AUTO_INCREMENT,
  `name_department` varchar(100) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_department`),
  UNIQUE KEY `name_department` (`name_department`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Guatemala',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(2,'Sacatepéquez',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(3,'Chimaltenango',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(4,'Escuintla',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(5,'Santa Rosa',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(6,'Sololá',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(7,'Totonicapán',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(8,'Quetzaltenango',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(9,'Suchitepéquez',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(10,'Retalhuleu',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(11,'San Marcos',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(12,'Huehuetenango',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(13,'Quiché',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(14,'Baja Verapaz',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(15,'Alta Verapaz',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(16,'Petén',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(17,'Izabal',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(18,'Zacapa',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(19,'Chiquimula',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(20,'Jalapa',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(21,'Jutiapa',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(22,'El Progreso',1,'2025-10-21 22:35:16','2025-10-21 22:35:16');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `driver_sessions`
--

DROP TABLE IF EXISTS `driver_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `driver_sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `driver_sessions_user_id_index` (`user_id`),
  KEY `driver_sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `driver_sessions`
--

LOCK TABLES `driver_sessions` WRITE;
/*!40000 ALTER TABLE `driver_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `driver_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drivers`
--

DROP TABLE IF EXISTS `drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drivers` (
  `id_driver` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `license` varchar(50) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_driver`),
  UNIQUE KEY `license` (`license`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drivers`
--

LOCK TABLES `drivers` WRITE;
/*!40000 ALTER TABLE `drivers` DISABLE KEYS */;
INSERT INTO `drivers` VALUES (1,'Juan Carlos Pérez López','GT-001-2023',1,'2025-10-21 23:09:29','2025-10-21 23:09:29'),(2,'María Elena García Morales','GT-002-2023',1,'2025-10-21 23:09:29','2025-10-21 23:09:29'),(3,'Roberto Martínez Flores','GT-003-2022',1,'2025-10-21 23:09:29','2025-10-21 23:09:29'),(4,'Ana Lucía Rodríguez Castro','GT-004-2024',1,'2025-10-21 23:09:29','2025-10-21 23:09:29'),(5,'María Elena García Morales','LIC002',1,'2025-10-22 01:37:56','2025-10-22 01:37:56'),(6,'Carlos Alberto Rodríguez Hernández','LIC003',1,'2025-10-22 01:37:56','2025-10-22 01:37:56');
/*!40000 ALTER TABLE `drivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_types`
--

DROP TABLE IF EXISTS `expense_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_types` (
  `id_expense_type` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_expense_type`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_types`
--

LOCK TABLES `expense_types` WRITE;
/*!40000 ALTER TABLE `expense_types` DISABLE KEYS */;
INSERT INTO `expense_types` VALUES (1,'Combustible',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(2,'Mantenimiento',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(3,'Reparaciones',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(4,'Seguros',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(5,'Peajes',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(6,'Multas',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(7,'Salarios',1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(8,'Otros gastos operativos',1,'2025-10-21 22:35:16','2025-10-21 22:35:16');
/*!40000 ALTER TABLE `expense_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id_expense` int NOT NULL AUTO_INCREMENT,
  `expense_type_id` int NOT NULL,
  `user_id` int NOT NULL,
  `vehicle_id` int DEFAULT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_expense`),
  KEY `expense_type_id` (`expense_type_id`),
  KEY `user_id` (`user_id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `idx_expenses_status_created` (`status`,`created_at`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`expense_type_id`) REFERENCES `expense_types` (`id_expense_type`),
  CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`),
  CONSTRAINT `expenses_ibfk_3` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id_vehicle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
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
-- Table structure for table `fuel_logs`
--

DROP TABLE IF EXISTS `fuel_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fuel_logs` (
  `id_fuel_log` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `user_id` int NOT NULL,
  `liters` decimal(10,2) NOT NULL,
  `price_per_liter` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) GENERATED ALWAYS AS ((`liters` * `price_per_liter`)) STORED,
  `distance_km` decimal(10,2) DEFAULT NULL,
  `refuel_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_fuel_log`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fuel_logs_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id_vehicle`),
  CONSTRAINT `fuel_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fuel_logs`
--

LOCK TABLES `fuel_logs` WRITE;
/*!40000 ALTER TABLE `fuel_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `fuel_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incomes`
--

DROP TABLE IF EXISTS `incomes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `incomes` (
  `id_income` int NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) DEFAULT NULL,
  `description` text NOT NULL,
  `income_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  `delivery_id` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_income`),
  KEY `user_id` (`user_id`),
  KEY `delivery_id` (`delivery_id`),
  KEY `idx_incomes_status_created` (`status`,`created_at`),
  CONSTRAINT `incomes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`),
  CONSTRAINT `incomes_ibfk_2` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id_delivery`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incomes`
--

LOCK TABLES `incomes` WRITE;
/*!40000 ALTER TABLE `incomes` DISABLE KEYS */;
INSERT INTO `incomes` VALUES (1,85.50,'Ingreso por entrega #8 - Ruta: Sanarate → Mixco','2025-10-22 22:35:27',2,8,1,'2025-10-22 22:35:27','2025-10-22 22:35:27'),(2,100.00,'Ingreso por entrega #2 - Ruta: Guatemala → Mixco','2025-10-22 22:49:23',2,2,1,'2025-10-22 22:49:23','2025-10-22 22:49:23'),(3,100.00,'Ingreso por entrega #5 - Ruta: Guatemala → Villa Nueva','2025-10-22 23:19:30',2,5,1,'2025-10-22 23:19:30','2025-10-22 23:19:30');
/*!40000 ALTER TABLE `incomes` ENABLE KEYS */;
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
-- Table structure for table `kilometers`
--

DROP TABLE IF EXISTS `kilometers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kilometers` (
  `id_kilometer` int NOT NULL AUTO_INCREMENT,
  `delivery_id` int NOT NULL,
  `vehicle_id` int NOT NULL,
  `alert_id` int DEFAULT NULL,
  `kilometers_traveled` decimal(10,2) NOT NULL,
  `record_date` date NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_kilometer`),
  KEY `delivery_id` (`delivery_id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `alert_id` (`alert_id`),
  CONSTRAINT `kilometers_ibfk_1` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id_delivery`),
  CONSTRAINT `kilometers_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id_vehicle`),
  CONSTRAINT `kilometers_ibfk_3` FOREIGN KEY (`alert_id`) REFERENCES `alert_statuses` (`id_alert`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kilometers`
--

LOCK TABLES `kilometers` WRITE;
/*!40000 ALTER TABLE `kilometers` DISABLE KEYS */;
/*!40000 ALTER TABLE `kilometers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance_requests`
--

DROP TABLE IF EXISTS `maintenance_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenance_requests` (
  `id_request` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `request_date` date NOT NULL,
  `reason` text NOT NULL,
  `approved_by` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_request`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id_vehicle`),
  CONSTRAINT `maintenance_requests_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance_requests`
--

LOCK TABLES `maintenance_requests` WRITE;
/*!40000 ALTER TABLE `maintenance_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenance_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenances`
--

DROP TABLE IF EXISTS `maintenances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenances` (
  `id_maintenance` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `request_id` int DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  `maintenance_date` date NOT NULL,
  `approved` tinyint(1) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_maintenance`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `request_id` (`request_id`),
  KEY `idx_maintenances_status_created` (`status`,`created_at`),
  CONSTRAINT `maintenances_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id_vehicle`),
  CONSTRAINT `maintenances_ibfk_2` FOREIGN KEY (`request_id`) REFERENCES `maintenance_requests` (`id_request`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenances`
--

LOCK TABLES `maintenances` WRITE;
/*!40000 ALTER TABLE `maintenances` DISABLE KEYS */;
INSERT INTO `maintenances` VALUES (1,1,NULL,'Cambio de llantas','2025-10-22',1,1,'2025-10-22 03:57:21','2025-10-22 03:57:21');
/*!40000 ALTER TABLE `maintenances` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_10_21_224518_create_cache_table',1),(2,'2025_10_21_224525_create_sessions_table',1),(3,'2025_10_21_224532_create_jobs_table',1),(4,'2025_10_21_224552_create_failed_jobs_table',2),(5,'2025_10_21_224735_add_remember_token_to_users_table',3),(6,'2025_10_21_225128_add_name_column_to_users_table',4),(7,'2025_10_22_014351_add_driver_status_to_delivery_assignments_table',5),(8,'2025_10_22_031247_create_admin_sessions_table',6),(9,'2025_10_22_031257_create_driver_sessions_table',7),(11,'2025_10_22_040835_add_assigned_at_to_delivery_assignments_table',8),(13,'2025_10_22_182250_add_estimated_duration_to_routes_table',9),(15,'2025_10_22_224233_add_total_kilometers_to_vehicles_table',10),(17,'2025_10_22_233409_add_order_id_to_deliveries_table',11),(18,'2025_10_22_233439_create_order_products_table',12),(19,'2025_10_23_000155_add_product_id_to_order_products_table',13);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `municipalities`
--

DROP TABLE IF EXISTS `municipalities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `municipalities` (
  `id_municipality` int NOT NULL AUTO_INCREMENT,
  `name_municipality` varchar(100) NOT NULL,
  `department_id` int NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_municipality`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `municipalities_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id_department`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `municipalities`
--

LOCK TABLES `municipalities` WRITE;
/*!40000 ALTER TABLE `municipalities` DISABLE KEYS */;
INSERT INTO `municipalities` VALUES (1,'Guatemala',1,1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(2,'Mixco',1,1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(3,'Villa Nueva',1,1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(4,'Petapa',1,1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(5,'San José Pinula',1,1,'2025-10-21 22:35:16','2025-10-21 22:35:16'),(6,'Sanarate',22,1,'2025-10-22 18:59:37','2025-10-22 18:59:37');
/*!40000 ALTER TABLE `municipalities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id_notification` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `vehicle_id` int DEFAULT NULL,
  `maintenance_id` int DEFAULT NULL,
  `delivery_id` int DEFAULT NULL,
  `type` enum('MAINTENANCE','DELIVERY') NOT NULL,
  `channel` enum('EMAIL') DEFAULT 'EMAIL',
  `message` text NOT NULL,
  `trigger_km` decimal(10,2) DEFAULT NULL,
  `sent` tinyint(1) DEFAULT '0',
  `sent_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notification`),
  KEY `user_id` (`user_id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `maintenance_id` (`maintenance_id`),
  KEY `delivery_id` (`delivery_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`),
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id_vehicle`),
  CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenances` (`id_maintenance`),
  CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id_delivery`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_products`
--

DROP TABLE IF EXISTS `order_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_products` (
  `id_order_product` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidad',
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_order_product`),
  KEY `order_products_order_id_index` (`order_id`),
  KEY `order_products_product_id_index` (`product_id`),
  CONSTRAINT `order_products_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE,
  CONSTRAINT `order_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id_product`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_products`
--

LOCK TABLES `order_products` WRITE;
/*!40000 ALTER TABLE `order_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id_order` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(12,2) DEFAULT NULL,
  `status` enum('PENDING','CONFIRMED','CANCELLED','DELIVERED') DEFAULT 'PENDING',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_order`),
  KEY `customer_id` (`customer_id`),
  KEY `idx_orders_status_created` (`status`,`created_at`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id_customer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
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
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id_product` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `description` text,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `weight_kg` decimal(10,2) DEFAULT NULL,
  `volume_m3` decimal(10,3) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_product`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Medicamentos Básicos','MED-001','Paquete de medicamentos básicos para farmacias',150.00,5.50,0.025,1,'2025-10-21 23:15:05','2025-10-21 23:15:05'),(2,'Productos de Limpieza','CLEAN-001','Kit completo de productos de limpieza',85.50,12.00,0.040,1,'2025-10-21 23:15:05','2025-10-21 23:15:05'),(3,'Alimentos Enlatados','FOOD-001','Variedad de alimentos enlatados para supermercados',75.25,18.50,0.030,1,'2025-10-21 23:15:05','2025-10-21 23:15:05'),(4,'Bebidas Refrescantes','DRINK-001','Paquete de 24 bebidas gaseosas',120.00,25.00,0.055,1,'2025-10-21 23:15:05','2025-10-21 23:15:05');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_role` int NOT NULL AUTO_INCREMENT,
  `name_role` varchar(100) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `name_role` (`name_role`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Administrador',1,'2025-10-21 22:35:15','2025-10-21 22:35:15'),(2,'Administrador',1,'2025-10-21 22:35:15','2025-10-21 22:35:15'),(3,'Supervisor',1,'2025-10-21 22:35:15','2025-10-21 22:35:15'),(4,'Conductor',1,'2025-10-21 22:35:15','2025-10-21 22:35:15'),(5,'Operador',1,'2025-10-21 22:35:15','2025-10-21 22:35:15');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_users`
--

DROP TABLE IF EXISTS `roles_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_users` (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_users`
--

LOCK TABLES `roles_users` WRITE;
/*!40000 ALTER TABLE `roles_users` DISABLE KEYS */;
INSERT INTO `roles_users` VALUES (1,1,'2025-10-21 16:35:16',NULL),(2,4,'2025-10-21 19:36:58',NULL),(4,4,'2025-10-21 19:37:56',NULL),(5,4,'2025-10-21 19:37:56',NULL),(6,1,'2025-10-21 20:49:34',NULL),(6,4,'2025-10-21 20:49:34',NULL);
/*!40000 ALTER TABLE `roles_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `routes` (
  `id_route` int NOT NULL AUTO_INCREMENT,
  `origin_id` int NOT NULL,
  `destination_id` int NOT NULL,
  `distance_km` decimal(10,2) NOT NULL,
  `estimated_duration` int DEFAULT NULL COMMENT 'Tiempo estimado en minutos',
  `total_distance` decimal(8,2) DEFAULT NULL COMMENT 'Distancia total calculada en km',
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_route`),
  KEY `origin_id` (`origin_id`),
  KEY `destination_id` (`destination_id`),
  CONSTRAINT `routes_ibfk_1` FOREIGN KEY (`origin_id`) REFERENCES `municipalities` (`id_municipality`),
  CONSTRAINT `routes_ibfk_2` FOREIGN KEY (`destination_id`) REFERENCES `municipalities` (`id_municipality`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes`
--

LOCK TABLES `routes` WRITE;
/*!40000 ALTER TABLE `routes` DISABLE KEYS */;
INSERT INTO `routes` VALUES (1,1,2,303.95,NULL,NULL,1,'2025-10-21 23:15:05','2025-10-22 19:01:38'),(2,1,3,45.20,NULL,NULL,1,'2025-10-21 23:15:05','2025-10-21 23:15:05'),(3,2,3,16.15,NULL,NULL,1,'2025-10-21 23:15:05','2025-10-22 19:02:00'),(4,6,2,68.85,NULL,NULL,1,'2025-10-22 19:00:22','2025-10-22 19:00:22');
/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_kilometers`
--

DROP TABLE IF EXISTS `tbl_kilometers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_kilometers` (
  `id_kilometer` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_vehicle` bigint unsigned NOT NULL,
  `kilometers` decimal(8,2) NOT NULL,
  `date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `id_user` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kilometer`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_kilometers`
--

LOCK TABLES `tbl_kilometers` WRITE;
/*!40000 ALTER TABLE `tbl_kilometers` DISABLE KEYS */;
INSERT INTO `tbl_kilometers` VALUES (1,1,45.20,'2025-10-22','Entrega #5 -  (Ruta preestablecida: 45.20 km)',1,'2025-10-23 05:19:30','2025-10-23 05:19:30');
/*!40000 ALTER TABLE `tbl_kilometers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_tokens`
--

DROP TABLE IF EXISTS `user_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_tokens` (
  `id_token` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `revoked` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_token`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_tokens`
--

LOCK TABLES `user_tokens` WRITE;
/*!40000 ALTER TABLE `user_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Administrador','Super','Administrador','superadmin','admin@sanalogistics.com','2025-10-22 04:48:34','$2y$12$vNwfpoSXkSNbsS1xKKAl1ewOGShm9stN96KGYsk49m6j2aYyivX8y','FfMwcBSfsDRnn0wmJ429OHNJDr4xhE4skmtx3P4m2Z50YO2q0UYqZlqwtBwr',1,'2025-10-21 22:35:16','2025-10-21 22:48:34'),(2,NULL,'Juan Carlos','Pérez López','juan.conductor','juan.conductor@sanalogistics.com',NULL,'$2y$12$gGEsULoEjfUcyztBZZao8u5PyEkp5UHU6Q9ol0sFYSV4Y/3YWUaby',NULL,1,'2025-10-22 01:36:58','2025-10-22 01:36:58'),(4,NULL,'María Elena','García Morales','maria.conductor','maria.conductor@sanalogistics.com',NULL,'$2y$12$6Iix1Rc.P2JweXf/6qfPhuzpqBARecWjxvqPIndiMLKPmbjEbryJe',NULL,1,'2025-10-22 01:37:56','2025-10-22 01:37:56'),(5,NULL,'Carlos Alberto','Rodríguez Hernández','carlos.conductor','carlos.conductor@sanalogistics.com',NULL,'$2y$12$kyHHXKgpuLqKxjwEJzcC.OvDnlJ9pmsrA3OXavH4d/iD3LHou.BYi',NULL,1,'2025-10-22 01:37:56','2025-10-22 01:37:56'),(6,NULL,'Super','Usuario','superusuario','super@sanalogistics.com','2025-10-22 08:49:34','$2y$12$3Yw58HXv0kvcBIdUkGF9N.beVCCyVIKF4VEb/3vVjEDd1NIkyG11S',NULL,1,'2025-10-22 02:49:34','2025-10-22 02:49:34');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_tracking`
--

DROP TABLE IF EXISTS `vehicle_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle_tracking` (
  `id_tracking` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `speed_kmh` decimal(6,2) DEFAULT NULL,
  `recorded_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_tracking`),
  KEY `idx_tracking_vehicle_time` (`vehicle_id`,`recorded_at`),
  CONSTRAINT `vehicle_tracking_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id_vehicle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_tracking`
--

LOCK TABLES `vehicle_tracking` WRITE;
/*!40000 ALTER TABLE `vehicle_tracking` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicle_tracking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id_vehicle` int NOT NULL AUTO_INCREMENT,
  `license_plate` varchar(20) NOT NULL,
  `capacity` int NOT NULL,
  `available` tinyint(1) DEFAULT '1',
  `status` tinyint(1) DEFAULT '1',
  `total_kilometers` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_vehicle`),
  UNIQUE KEY `license_plate` (`license_plate`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (1,'P-001-ABC',3500,1,1,0.00,'2025-10-21 23:09:29','2025-10-22 23:19:30'),(2,'P-002-DEF',5000,1,1,0.00,'2025-10-21 23:09:29','2025-10-21 23:09:29'),(3,'P-003-GHI',2500,0,1,0.00,'2025-10-21 23:09:29','2025-10-21 23:09:29'),(4,'P-004-JKL',4000,1,1,0.00,'2025-10-21 23:09:29','2025-10-21 23:09:29');
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-23 20:18:53
