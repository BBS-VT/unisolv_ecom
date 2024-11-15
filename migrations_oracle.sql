-- MySQL dump 10.13  Distrib 8.0.29, for Linux (aarch64)
--
-- Host: localhost    Database: unisolvcrm_dev
-- ------------------------------------------------------
-- Server version	8.0.29-0ubuntu0.20.04.3

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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2014_10_12_200000_add_two_factor_columns_to_users_table',1),(4,'2016_06_01_000001_create_oauth_auth_codes_table',1),(5,'2016_06_01_000002_create_oauth_access_tokens_table',1),(6,'2016_06_01_000003_create_oauth_refresh_tokens_table',1),(7,'2016_06_01_000004_create_oauth_clients_table',1),(8,'2016_06_01_000005_create_oauth_personal_access_clients_table',1),(9,'2019_08_19_000000_create_failed_jobs_table',1),(10,'2021_03_02_175958_create_permissions_table',1),(11,'2021_03_02_180044_create_roles_table',1),(12,'2021_03_02_180048_create_settings_table',1),(13,'2021_03_02_180049_create_countries_table',1),(14,'2021_03_02_180050_create_buying_groups_table',1),(15,'2021_03_02_180051_create_customer_categories_table',1),(16,'2021_03_02_180120_create_customers_table',1),(17,'2021_03_02_204533_create_role_user_pivot_table',1),(18,'2021_03_02_204608_create_permission_role_pivot_table',1),(19,'2021_03_10_110424_create_package_types_table',1),(20,'2021_03_11_114058_create_product_categories_table',1),(21,'2021_03_11_123040_create_product_tags_table',1),(22,'2021_03_11_123059_create_products_table',1),(23,'2021_03_11_133252_create_product_product_category_pivot_table',1),(24,'2021_03_11_143359_create_product_product_tag_pivot_table',1),(25,'2021_03_14_121235_create_orders_table',1),(26,'2021_03_14_121300_create_orders_items_table',1),(27,'2021_03_23_110404_create_media_table',1),(28,'2021_03_28_130901_create_special_deals_table',1),(29,'2021_04_13_074932_create_order_status_table',1),(30,'2021_04_13_080224_add_status_to_orders_table',1),(31,'2021_07_05_111158_create_stock_item_holdings_table',1),(32,'2021_07_08_104618_add_discount_to_product_table',1),(33,'2021_07_15_075254_create_customer_balances_table',1),(34,'2021_07_18_165436_add_authorization_to_orders_table',1),(35,'2021_10_31_164848_create_companies_table',1),(36,'2021_10_31_164924_add_uuid_to_users_table',1),(37,'2021_11_01_064739_add_vat_number_to_companies',1),(38,'2021_11_01_064850_create_tax_types_table',1),(39,'2021_11_01_064917_create_taxes_table',1),(40,'2021_11_01_123035_create_addresses_table',1),(41,'2021_11_01_223340_add_uuid_to_orders_table',1),(42,'2021_11_01_225120_add_discount_to_orders_items_table',1),(43,'2021_11_03_234412_add_uuid_to_customers_table',1),(44,'2021_11_04_075105_add_uuid_to_products_table',1),(45,'2021_11_07_145107_create_currencies_table',1),(46,'2022_05_13_092800_add_uuid_to_customer_categories_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-05-17 12:31:00
