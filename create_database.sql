/* README
   ------
   - Decide on a database name, username & password that this system will use to query and update data
   - Create a new database on a MySQL 5.7 Server

CREATE SCHEMA `<DATABASE NAME>` ;

   - Create a User that this system wll use to query and update data

CREATE USER '<USER NAME>'@'%'
IDENTIFIED WITH 'mysql_native_password' AS '<USER PASSWORD>'
REQUIRE NONE PASSWORD EXPIRE DEFAULT ACCOUNT UNLOCK

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, REFERENCES, INDEX, ALTER, SHOW DATABASES, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EVENT, TRIGGER ON *.* TO '<USER NAME>'@'%' WITH GRANT OPTION
GRANT ALL PRIVILEGES ON `<DATABASE NAME>`.* TO '<USER NAME>'@'%' WITH GRANT OPTION

 */

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `api_credentials`
--

DROP TABLE IF EXISTS `api_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_credentials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `active` int(1) unsigned NOT NULL DEFAULT '1',
  `created_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_us`
--

DROP TABLE IF EXISTS `contact_us`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_us` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `lab` varchar(255) DEFAULT NULL,
  `additional_info` text,
  `contacted_on` datetime DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `iso_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `iso2` varchar(2) COLLATE utf8_bin NOT NULL,
  `iso3` varchar(3) COLLATE utf8_bin NOT NULL,
  `numeric_code` smallint(6) NOT NULL,
  `gxalert_url` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `gxalert_api_credentials` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `show_monthly_indicators` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country_shipment_map`
--

DROP TABLE IF EXISTS `country_shipment_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country_shipment_map` (
  `country_id` int(10) unsigned NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `due_date_text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`country_id`,`shipment_id`),
  KEY `country_id` (`country_id`),
  KEY `shipment_id` (`shipment_id`),
  CONSTRAINT `country_shipment_map_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipment` (`shipment_id`) ON DELETE CASCADE,
  CONSTRAINT `country_shipment_map_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `data_manager`
--

DROP TABLE IF EXISTS `data_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_manager` (
  `dm_id` int(11) NOT NULL AUTO_INCREMENT,
  `primary_email` varchar(255) NOT NULL,
  `password` varchar(45) DEFAULT NULL,
  `institute` varchar(500) DEFAULT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `secondary_email` varchar(45) DEFAULT NULL,
  `UserFld1` varchar(45) DEFAULT NULL,
  `UserFld2` varchar(45) DEFAULT NULL,
  `UserFld3` varchar(45) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL,
  `force_password_reset` int(1) NOT NULL DEFAULT '0',
  `qc_access` varchar(100) DEFAULT NULL,
  `enable_adding_test_response_date` varchar(45) DEFAULT NULL,
  `enable_choosing_mode_of_receipt` varchar(45) DEFAULT NULL,
  `view_only_access` varchar(45) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'inactive',
  `created_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`dm_id`),
  UNIQUE KEY `primary_email` (`primary_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A PT user Table for Data entry or report printing';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `distributions`
--

DROP TABLE IF EXISTS `distributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distributions` (
  `distribution_id` int(11) NOT NULL AUTO_INCREMENT,
  `distribution_code` varchar(255) NOT NULL,
  `distribution_date` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`distribution_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dts_recommended_testkits`
--

DROP TABLE IF EXISTS `dts_recommended_testkits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dts_recommended_testkits` (
  `test_no` int(11) NOT NULL,
  `testkit` varchar(255) NOT NULL,
  PRIMARY KEY (`test_no`,`testkit`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dts_shipment_corrective_action_map`
--

DROP TABLE IF EXISTS `dts_shipment_corrective_action_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dts_shipment_corrective_action_map` (
  `shipment_map_id` int(11) NOT NULL,
  `corrective_action_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enrollments` (
  `scheme_id` varchar(255) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `enrolled_on` date DEFAULT NULL,
  `enrollment_ended_on` date DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`scheme_id`,`participant_id`),
  KEY `participant_id` (`participant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `global_config`
--

DROP TABLE IF EXISTS `global_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_config` (
  `name` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gxalert_result`
--

DROP TABLE IF EXISTS `gxalert_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gxalert_result` (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `map_id` int(11) DEFAULT NULL,
  `sample_id` int(11) NOT NULL,
  `participant_unique_id` varchar(255) DEFAULT NULL,
  `gxalert_test_id` int(11) NOT NULL,
  `gxalert_deployment_id` int(11) NOT NULL,
  `gxalert_message_sent_on` datetime DEFAULT NULL,
  `result_patient_id` varchar(255) DEFAULT NULL,
  `result_sample_id` varchar(255) DEFAULT NULL,
  `test_started_on` datetime DEFAULT NULL,
  `test_ended_on` datetime DEFAULT NULL,
  `mtb_result` varchar(50) DEFAULT NULL,
  `rif_result` varchar(50) DEFAULT NULL,
  `error_code` varchar(50) DEFAULT NULL,
  `probe_d` varchar(255) DEFAULT NULL,
  `probe_c` varchar(255) DEFAULT NULL,
  `probe_e` varchar(255) DEFAULT NULL,
  `probe_b` varchar(255) DEFAULT NULL,
  `spc` varchar(255) DEFAULT NULL,
  `probe_a` varchar(255) DEFAULT NULL,
  `assay_name` varchar(255) DEFAULT NULL,
  `reagent_lot_id` varchar(255) DEFAULT NULL,
  `cartridge_expiration_date` datetime DEFAULT NULL,
  `cartridge_serial_number` varchar(255) DEFAULT NULL,
  `lab_name` varchar(255) DEFAULT NULL,
  `xpert_host_id` varchar(255) DEFAULT NULL,
  `xpert_sender_user` varchar(255) DEFAULT NULL,
  `instrument_serial_number` varchar(255) DEFAULT NULL,
  `instrument_installed_on` datetime DEFAULT NULL,
  `instrument_last_calibrated_on` datetime DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `module_serial_number` varchar(255) DEFAULT NULL,
  `module_name` varchar(20) DEFAULT NULL,
  `test_count_last_30_days` int(11) DEFAULT NULL,
  `error_count_last_30_days` int(11) DEFAULT NULL,
  `error_codes_encountered_last_30_days` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`result_id`),
  UNIQUE KEY `gxalert_result_result_id` (`map_id`,`sample_id`),
  CONSTRAINT `gxalert_result_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `shipment_participant_map` (`map_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Result Data received from GxAlert';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `home_banner`
--

DROP TABLE IF EXISTS `home_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `home_banner` (
  `banner_id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `instrument`
--

DROP TABLE IF EXISTS `instrument`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instrument` (
  `instrument_id` int(11) NOT NULL AUTO_INCREMENT,
  `participant_id` int(11) NOT NULL,
  `instrument_serial` varchar(45) DEFAULT NULL,
  `instrument_installed_on` date DEFAULT NULL,
  `instrument_last_calibrated_on` date DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`instrument_id`),
  KEY `participant_id` (`participant_id`),
  CONSTRAINT `instrument_ibfk_2` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`participant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail_template`
--

DROP TABLE IF EXISTS `mail_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_template` (
  `mail_temp_id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_purpose` varchar(255) NOT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `mail_from` varchar(255) DEFAULT NULL,
  `mail_cc` varchar(255) DEFAULT NULL,
  `mail_bcc` varchar(255) DEFAULT NULL,
  `mail_subject` varchar(255) DEFAULT NULL,
  `mail_content` text,
  `mail_footer` text,
  PRIMARY KEY (`mail_temp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `participant`
--

DROP TABLE IF EXISTS `participant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participant` (
  `participant_id` int(11) NOT NULL AUTO_INCREMENT,
  `unique_identifier` varchar(255) NOT NULL,
  `lab_name` varchar(500) NOT NULL,
  `institute_name` varchar(255) DEFAULT NULL,
  `department_name` varchar(255) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` int(11) NOT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `long` varchar(255) DEFAULT NULL,
  `lat` varchar(255) DEFAULT NULL,
  `shipping_address` varchar(1000) DEFAULT NULL,
  `funding_source` varchar(255) DEFAULT NULL,
  `testing_volume` varchar(255) DEFAULT NULL,
  `enrolled_programs` varchar(255) DEFAULT NULL,
  `site_type` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `affiliation` varchar(45) DEFAULT NULL,
  `network_tier` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`participant_id`),
  UNIQUE KEY `unique_identifier` (`unique_identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `participant_enrolled_programs_map`
--

DROP TABLE IF EXISTS `participant_enrolled_programs_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participant_enrolled_programs_map` (
  `participant_id` int(11) NOT NULL,
  `ep_id` int(11) NOT NULL,
  PRIMARY KEY (`participant_id`,`ep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `participant_manager_map`
--

DROP TABLE IF EXISTS `participant_manager_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participant_manager_map` (
  `participant_id` int(11) NOT NULL,
  `dm_id` int(11) NOT NULL,
  PRIMARY KEY (`participant_id`,`dm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `participant_monthly_indicators`
--

DROP TABLE IF EXISTS `participant_monthly_indicators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participant_monthly_indicators` (
  `submission_id` int(11) NOT NULL AUTO_INCREMENT,
  `participant_id` int(11) NOT NULL,
  `attributes` mediumtext,
  `created_by` varchar(45) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`submission_id`),
  KEY `participant_id` (`participant_id`),
  CONSTRAINT `participant_monthly_indicators_ibfk_1` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`participant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `participant_temp`
--

DROP TABLE IF EXISTS `participant_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participant_temp` (
  `unique_identifier` varchar(255) NOT NULL,
  `lab_name` varchar(500) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `active` varchar(45) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `partners`
--

DROP TABLE IF EXISTS `partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `partners` (
  `partner_id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_name` varchar(500) DEFAULT NULL,
  `link` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `added_on` datetime NOT NULL,
  `status` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`partner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ptcc_country_map`
--

DROP TABLE IF EXISTS `ptcc_country_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ptcc_country_map` (
  `admin_id` int(11) NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `show_details_on_report` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`admin_id`,`country_id`),
  KEY `admin_id` (`admin_id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `ptcc_country_map_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `system_admin` (`admin_id`) ON DELETE CASCADE,
  CONSTRAINT `ptcc_country_map_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publications`
--

DROP TABLE IF EXISTS `publications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publications` (
  `publication_id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text,
  `file_name` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `added_on` datetime NOT NULL,
  `status` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`publication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `push_notification_token`
--

DROP TABLE IF EXISTS `push_notification_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `push_notification_token` (
  `push_notification_token_id` int(11) NOT NULL AUTO_INCREMENT,
  `dm_id` int(11) NOT NULL,
  `platform` varchar(20) NOT NULL,
  `push_notification_token` varchar(255) NOT NULL,
  `last_seen` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`push_notification_token_id`),
  KEY `dm_id` (`dm_id`),
  KEY `push_notification_token` (`push_notification_token`),
  CONSTRAINT `dm_id` FOREIGN KEY (`dm_id`) REFERENCES `data_manager` (`dm_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_control`
--

DROP TABLE IF EXISTS `r_control`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_control` (
  `control_id` int(11) NOT NULL AUTO_INCREMENT,
  `control_name` varchar(255) DEFAULT NULL,
  `for_scheme` varchar(255) DEFAULT NULL,
  `is_active` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`control_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_dbs_eia`
--

DROP TABLE IF EXISTS `r_dbs_eia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_dbs_eia` (
  `eia_id` int(11) NOT NULL AUTO_INCREMENT,
  `eia_name` varchar(500) NOT NULL,
  PRIMARY KEY (`eia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_dbs_wb`
--

DROP TABLE IF EXISTS `r_dbs_wb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_dbs_wb` (
  `wb_id` int(11) NOT NULL AUTO_INCREMENT,
  `wb_name` varchar(500) NOT NULL,
  PRIMARY KEY (`wb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_dts_corrective_actions`
--

DROP TABLE IF EXISTS `r_dts_corrective_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_dts_corrective_actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `corrective_action` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_eid_detection_assay`
--

DROP TABLE IF EXISTS `r_eid_detection_assay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_eid_detection_assay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` varchar(45) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_eid_extraction_assay`
--

DROP TABLE IF EXISTS `r_eid_extraction_assay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_eid_extraction_assay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` varchar(45) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_enrolled_programs`
--

DROP TABLE IF EXISTS `r_enrolled_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_enrolled_programs` (
  `r_epid` int(11) NOT NULL AUTO_INCREMENT,
  `enrolled_programs` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`r_epid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_evaluation_comments`
--

DROP TABLE IF EXISTS `r_evaluation_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_evaluation_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `scheme` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_modes_of_receipt`
--

DROP TABLE IF EXISTS `r_modes_of_receipt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_modes_of_receipt` (
  `mode_id` int(11) NOT NULL AUTO_INCREMENT,
  `mode_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`mode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_network_tiers`
--

DROP TABLE IF EXISTS `r_network_tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_network_tiers` (
  `network_id` int(11) NOT NULL AUTO_INCREMENT,
  `network_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`network_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_participant_affiliates`
--

DROP TABLE IF EXISTS `r_participant_affiliates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_participant_affiliates` (
  `aff_id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliate` varchar(255) NOT NULL,
  PRIMARY KEY (`aff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_possibleresult`
--

DROP TABLE IF EXISTS `r_possibleresult`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_possibleresult` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scheme_id` varchar(45) NOT NULL,
  `scheme_sub_group` varchar(45) DEFAULT NULL,
  `response` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_results`
--

DROP TABLE IF EXISTS `r_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_results` (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `result_name` varchar(255) NOT NULL,
  PRIMARY KEY (`result_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_site_type`
--

DROP TABLE IF EXISTS `r_site_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_site_type` (
  `r_stid` int(11) NOT NULL AUTO_INCREMENT,
  `site_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`r_stid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_tb_assay`
--

DROP TABLE IF EXISTS `r_tb_assay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_tb_assay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  `analyte1Label` varchar(45) NOT NULL DEFAULT 'Probe D',
  `analyte2Label` varchar(45) NOT NULL DEFAULT 'Probe C',
  `analyte3Label` varchar(45) NOT NULL DEFAULT 'Probe E',
  `analyte4Label` varchar(45) NOT NULL DEFAULT 'Probe B',
  `analyte5Label` varchar(45) NOT NULL DEFAULT 'SPC',
  `analyte6Label` varchar(45) NOT NULL DEFAULT 'Probe A',
  `includeTraceForMtbDetected` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `r_tb_assay_indexes`(`short_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_testkitname_dts`
--

DROP TABLE IF EXISTS `r_testkitname_dts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_testkitname_dts` (
  `TestKitName_ID` varchar(50) NOT NULL,
  `scheme_type` varchar(255) NOT NULL,
  `TestKit_Name` varchar(100) DEFAULT NULL,
  `TestKit_Name_Short` varchar(50) DEFAULT NULL,
  `TestKit_Comments` varchar(50) DEFAULT NULL,
  `Updated_On` datetime DEFAULT NULL,
  `Updated_By` int(11) DEFAULT NULL,
  `Installation_id` varchar(50) DEFAULT NULL,
  `TestKit_Manufacturer` varchar(50) DEFAULT NULL,
  `Created_On` datetime DEFAULT NULL,
  `Created_By` int(11) DEFAULT NULL,
  `Approval` int(1) DEFAULT '1' COMMENT '1 = Approved , 0 not approved.',
  `TestKit_ApprovalAgency` varchar(20) DEFAULT NULL COMMENT 'USAID, FDA, LOCAL',
  `source_reference` varchar(50) DEFAULT NULL,
  `CountryAdapted` int(11) DEFAULT NULL COMMENT '0= Not allowed in the country 1 = approved in country ',
  `testkit_1` int(11) NOT NULL DEFAULT '0',
  `testkit_2` int(11) NOT NULL DEFAULT '0',
  `testkit_3` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`TestKitName_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `r_vl_assay`
--

DROP TABLE IF EXISTS `r_vl_assay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `r_vl_assay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reference_dbs_eia`
--

DROP TABLE IF EXISTS `reference_dbs_eia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reference_dbs_eia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipment_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `eia` int(11) NOT NULL,
  `lot` varchar(255) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `od` varchar(255) DEFAULT NULL,
  `cutoff` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reference_dbs_wb`
--

DROP TABLE IF EXISTS `reference_dbs_wb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reference_dbs_wb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipment_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `wb` int(11) NOT NULL,
  `lot` varchar(255) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `160` int(11) DEFAULT NULL,
  `120` int(11) DEFAULT NULL,
  `66` int(11) DEFAULT NULL,
  `55` int(11) DEFAULT NULL,
  `51` int(11) DEFAULT NULL,
  `41` int(11) DEFAULT NULL,
  `31` int(11) DEFAULT NULL,
  `24` int(11) DEFAULT NULL,
  `17` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reference_dts_eia`
--

DROP TABLE IF EXISTS `reference_dts_eia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reference_dts_eia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipment_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `eia` int(11) NOT NULL,
  `lot` varchar(255) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `od` varchar(255) DEFAULT NULL,
  `cutoff` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reference_dts_rapid_hiv`
--

DROP TABLE IF EXISTS `reference_dts_rapid_hiv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reference_dts_rapid_hiv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipment_id` varchar(255) NOT NULL,
  `sample_id` varchar(255) NOT NULL,
  `testkit` varchar(255) NOT NULL,
  `lot_no` varchar(255) NOT NULL,
  `expiry_date` date NOT NULL,
  `result` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reference_dts_wb`
--

DROP TABLE IF EXISTS `reference_dts_wb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reference_dts_wb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipment_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `wb` int(11) NOT NULL,
  `lot` varchar(255) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `160` int(11) DEFAULT NULL,
  `120` int(11) DEFAULT NULL,
  `66` int(11) DEFAULT NULL,
  `55` int(11) DEFAULT NULL,
  `51` int(11) DEFAULT NULL,
  `41` int(11) DEFAULT NULL,
  `31` int(11) DEFAULT NULL,
  `24` int(11) DEFAULT NULL,
  `17` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reference_result_dbs`
--

DROP TABLE IF EXISTS `reference_result_dbs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reference_result_dbs` (
  `shipment_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `sample_label` varchar(45) DEFAULT NULL,
  `reference_result` varchar(45) DEFAULT NULL,
  `control` int(11) DEFAULT NULL,
  `mandatory` int(11) NOT NULL DEFAULT '0',
  `sample_score` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`shipment_id`,`sample_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Referance Result for DBS Shipment';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reference_result_dts`
--

DROP TABLE IF EXISTS `reference_result_dts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reference_result_dts` (
  `shipment_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `sample_label` varchar(45) DEFAULT NULL,
  `reference_result` varchar(45) DEFAULT NULL,
  `control` int(11) DEFAULT NULL,
  `mandatory` int(11) NOT NULL DEFAULT '0',
  `sample_score` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`shipment_id`,`sample_id`),
  CONSTRAINT `reference_result_dts_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipment` (`shipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Referance Result for DTS Shipment';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reference_result_eid`
--

DROP TABLE IF EXISTS `reference_result_eid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reference_result_eid` (
  `shipment_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `sample_label` varchar(255) DEFAULT NULL,
  `reference_result` varchar(255) DEFAULT NULL,
  `control` int(11) DEFAULT NULL,
  `reference_hiv_ct_od` varchar(45) DEFAULT NULL,
  `reference_ic_qs` varchar(45) DEFAULT NULL,
  `mandatory` int(11) NOT NULL DEFAULT '0',
  `sample_score` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`shipment_id`,`sample_id`),
  CONSTRAINT `reference_result_eid_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipment` (`shipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reference_result_tb`
--

DROP TABLE IF EXISTS `reference_result_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reference_result_tb` (
  `shipment_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `sample_label` varchar(255) DEFAULT NULL,
  `mtb_rif_mtb_detected` varchar(255) DEFAULT NULL,
  `mtb_rif_rif_resistance` varchar(255) DEFAULT NULL,
  `ultra_mtb_detected` varchar(255) DEFAULT NULL,
  `ultra_rif_resistance` varchar(255) DEFAULT NULL,
  `mtb_rif_probe_d` varchar(255) DEFAULT NULL,
  `mtb_rif_probe_c` varchar(255) DEFAULT NULL,
  `mtb_rif_probe_e` varchar(255) DEFAULT NULL,
  `mtb_rif_probe_b` varchar(255) DEFAULT NULL,
  `mtb_rif_probe_spc` varchar(255) DEFAULT NULL,
  `mtb_rif_probe_a` varchar(255) DEFAULT NULL,
  `ultra_probe_spc` varchar(255) DEFAULT NULL,
  `ultra_probe_is1081_is6110` varchar(255) DEFAULT NULL,
  `ultra_probe_rpo_b1` varchar(255) DEFAULT NULL,
  `ultra_probe_rpo_b2` varchar(255) DEFAULT NULL,
  `ultra_probe_rpo_b3` varchar(255) DEFAULT NULL,
  `ultra_probe_rpo_b4` varchar(255) DEFAULT NULL,
  `control` int(11) DEFAULT NULL,
  `mandatory` int(11) NOT NULL DEFAULT '0',
  `sample_score` int(11) NOT NULL DEFAULT '1',
  `is_excluded` varchar(5) NOT NULL DEFAULT 'no',
  `is_exempt` varchar(5) NOT NULL DEFAULT 'no',
  `excluded_reason` text,
  `sample_content` varchar(255) DEFAULT NULL,
  KEY `indexing_reference_result_tb`(`shipment_id`, `sample_id`, `is_excluded`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reference_result_vl`
--

DROP TABLE IF EXISTS `reference_result_vl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reference_result_vl` (
  `shipment_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `sample_label` varchar(255) DEFAULT NULL,
  `reference_result` varchar(45) DEFAULT NULL,
  `control` int(11) DEFAULT NULL,
  `mandatory` int(11) NOT NULL DEFAULT '0',
  `sample_score` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`shipment_id`,`sample_id`),
  CONSTRAINT `reference_result_vl_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipment` (`shipment_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reference_vl_calculation`
--

DROP TABLE IF EXISTS `reference_vl_calculation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reference_vl_calculation` (
  `shipment_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `vl_assay` int(11) NOT NULL,
  `q1` double(20,10) DEFAULT NULL,
  `q3` double(20,10) DEFAULT NULL,
  `iqr` double(20,10) DEFAULT NULL,
  `quartile_low` double(20,10) DEFAULT NULL,
  `quartile_high` double(20,10) DEFAULT NULL,
  `mean` double(20,10) DEFAULT NULL,
  `sd` double(20,10) DEFAULT NULL,
  `cv` double(20,10) DEFAULT NULL,
  `low_limit` double(20,10) DEFAULT NULL,
  `high_limit` double(20,10) DEFAULT NULL,
  `calculated_on` datetime DEFAULT NULL,
  `manual_mean` double(20,10) DEFAULT NULL,
  `manual_sd` double(20,10) DEFAULT NULL,
  `manual_cv` double(20,10) DEFAULT NULL,
  `manual_q1` double(20,10) DEFAULT NULL,
  `manual_q3` double(20,10) DEFAULT NULL,
  `manual_iqr` double(20,10) DEFAULT NULL,
  `manual_quartile_low` double(20,10) DEFAULT NULL,
  `manual_quartile_high` double(20,10) DEFAULT NULL,
  `manual_low_limit` double(20,10) DEFAULT NULL,
  `manual_high_limit` double(20,10) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `use_range` varchar(255) NOT NULL DEFAULT 'calculated',
  PRIMARY KEY (`shipment_id`,`sample_id`,`vl_assay`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reference_vl_methods`
--

DROP TABLE IF EXISTS `reference_vl_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reference_vl_methods` (
  `shipment_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `assay` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`shipment_id`,`sample_id`,`assay`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_config`
--

DROP TABLE IF EXISTS `report_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_config` (
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_download_log`
--

DROP TABLE IF EXISTS `report_download_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_download_log` (
  `shipment_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `request_data` text,
  `timestamp` datetime DEFAULT NULL,
  KEY `shipment_id` (`shipment_id`),
  KEY `participant_id` (`participant_id`),
  CONSTRAINT `report_download_log_ibfk_3` FOREIGN KEY (`shipment_id`) REFERENCES `shipment` (`shipment_id`) ON DELETE CASCADE,
  CONSTRAINT `report_download_log_ibfk_4` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`participant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Logs all report downloads';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `response_not_tested_reason`
--

DROP TABLE IF EXISTS `response_not_tested_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `response_not_tested_reason` (
  `not_tested_reason_id` int(11) NOT NULL AUTO_INCREMENT,
  `not_tested_reason` varchar(500) DEFAULT NULL,
  `status` varchar(45) NOT NULL DEFAULT 'active',
  `scheme_type` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`not_tested_reason_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `response_result_dbs`
--

DROP TABLE IF EXISTS `response_result_dbs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `response_result_dbs` (
  `shipment_map_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `eia_1` int(11) DEFAULT NULL,
  `lot_no_1` varchar(45) DEFAULT NULL,
  `exp_date_1` date DEFAULT NULL,
  `od_1` varchar(45) DEFAULT NULL,
  `cutoff_1` varchar(45) DEFAULT NULL,
  `eia_2` int(11) DEFAULT NULL,
  `lot_no_2` varchar(45) DEFAULT NULL,
  `exp_date_2` date DEFAULT NULL,
  `od_2` varchar(45) DEFAULT NULL,
  `cutoff_2` varchar(45) DEFAULT NULL,
  `eia_3` int(11) DEFAULT NULL,
  `lot_no_3` varchar(45) DEFAULT NULL,
  `exp_date_3` date DEFAULT NULL,
  `od_3` varchar(45) DEFAULT NULL,
  `cutoff_3` varchar(45) DEFAULT NULL,
  `wb` int(11) DEFAULT NULL,
  `wb_lot` varchar(45) DEFAULT NULL,
  `wb_exp_date` date DEFAULT NULL,
  `wb_160` varchar(45) DEFAULT NULL,
  `wb_120` varchar(45) DEFAULT NULL,
  `wb_66` varchar(45) DEFAULT NULL,
  `wb_55` varchar(45) DEFAULT NULL,
  `wb_51` varchar(45) DEFAULT NULL,
  `wb_41` varchar(45) DEFAULT NULL,
  `wb_31` varchar(45) DEFAULT NULL,
  `wb_24` varchar(45) DEFAULT NULL,
  `wb_17` varchar(45) DEFAULT NULL,
  `reported_result` int(11) DEFAULT NULL,
  `calculated_score` varchar(45) DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`shipment_map_id`,`sample_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `response_result_dts`
--

DROP TABLE IF EXISTS `response_result_dts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `response_result_dts` (
  `shipment_map_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `test_kit_name_1` varchar(45) DEFAULT NULL,
  `lot_no_1` varchar(45) DEFAULT NULL,
  `exp_date_1` date DEFAULT NULL,
  `test_result_1` varchar(45) DEFAULT NULL,
  `test_kit_name_2` varchar(45) DEFAULT NULL,
  `lot_no_2` varchar(45) DEFAULT NULL,
  `exp_date_2` date DEFAULT NULL,
  `test_result_2` varchar(45) DEFAULT NULL,
  `test_kit_name_3` varchar(45) DEFAULT NULL,
  `lot_no_3` varchar(45) DEFAULT NULL,
  `exp_date_3` date DEFAULT NULL,
  `test_result_3` varchar(45) DEFAULT NULL,
  `reported_result` varchar(45) DEFAULT NULL,
  `calculated_score` varchar(45) DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`shipment_map_id`,`sample_id`),
  CONSTRAINT `response_result_dts_ibfk_1` FOREIGN KEY (`shipment_map_id`) REFERENCES `shipment_participant_map` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `response_result_eid`
--

DROP TABLE IF EXISTS `response_result_eid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `response_result_eid` (
  `shipment_map_id` int(11) NOT NULL,
  `sample_id` varchar(45) NOT NULL,
  `reported_result` varchar(45) DEFAULT NULL,
  `hiv_ct_od` varchar(45) DEFAULT NULL,
  `ic_qs` varchar(45) DEFAULT NULL,
  `calculated_score` varchar(45) DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`shipment_map_id`,`sample_id`),
  CONSTRAINT `response_result_eid_ibfk_1` FOREIGN KEY (`shipment_map_id`) REFERENCES `shipment_participant_map` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `response_result_tb`
--

DROP TABLE IF EXISTS `response_result_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `response_result_tb` (
  `shipment_map_id` int(11) NOT NULL,
  `sample_id` varchar(45) NOT NULL,
  `instrument_serial` varchar(45) DEFAULT NULL,
  `instrument_installed_on` date DEFAULT NULL,
  `instrument_last_calibrated_on` date DEFAULT NULL,
  `reagent_lot_id` varchar(20) DEFAULT NULL,
  `cartridge_expiration_date` date DEFAULT NULL,
  `module_name` varchar(2) DEFAULT NULL,
  `instrument_user` varchar(100) DEFAULT NULL,
  `error_code` varchar(50) DEFAULT NULL,
  `date_tested` date DEFAULT NULL,
  `mtb_detected` varchar(255) DEFAULT NULL,
  `rif_resistance` varchar(255) DEFAULT NULL,
  `probe_1` varchar(255) DEFAULT NULL,
  `probe_2` varchar(255) DEFAULT NULL,
  `probe_3` varchar(255) DEFAULT NULL,
  `probe_4` varchar(255) DEFAULT NULL,
  `probe_5` varchar(255) DEFAULT NULL,
  `probe_6` varchar(255) DEFAULT NULL,
  `calculated_score` varchar(45) DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  KEY `index_in_response_res_tb`(`sample_id`, `shipment_map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `response_result_vl`
--

DROP TABLE IF EXISTS `response_result_vl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `response_result_vl` (
  `shipment_map_id` int(11) NOT NULL,
  `sample_id` varchar(45) NOT NULL,
  `reported_viral_load` varchar(255) DEFAULT NULL,
  `calculated_score` varchar(45) DEFAULT NULL,
  `is_tnd` varchar(45) DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`shipment_map_id`,`sample_id`),
  CONSTRAINT `response_result_vl_ibfk_1` FOREIGN KEY (`shipment_map_id`) REFERENCES `shipment_participant_map` (`map_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheme_list`
--

DROP TABLE IF EXISTS `scheme_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scheme_list` (
  `scheme_id` varchar(10) NOT NULL,
  `scheme_name` varchar(255) NOT NULL,
  `response_table` varchar(45) DEFAULT NULL,
  `reference_result_table` varchar(45) DEFAULT NULL,
  `attribute_list` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`scheme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shipment`
--

DROP TABLE IF EXISTS `shipment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipment` (
  `shipment_id` int(11) NOT NULL AUTO_INCREMENT,
  `shipment_code` varchar(255) NOT NULL,
  `scheme_type` varchar(10) DEFAULT NULL,
  `shipment_date` date DEFAULT NULL,
  `lastdate_response` date DEFAULT NULL,
  `distribution_id` int(11) NOT NULL,
  `number_of_samples` int(11) DEFAULT NULL,
  `number_of_controls` int(11) NOT NULL,
  `response_switch` varchar(255) NOT NULL DEFAULT 'off',
  `max_score` int(11) DEFAULT NULL,
  `average_score` varchar(255) DEFAULT '0',
  `shipment_comment` text,
  `created_by_admin` varchar(255) DEFAULT NULL,
  `created_on_admin` datetime DEFAULT NULL,
  `updated_by_admin` varchar(255) DEFAULT NULL,
  `updated_on_admin` datetime DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `follows_up_from` int(11) DEFAULT NULL,
  `is_official` int(11) DEFAULT '1',
  PRIMARY KEY (`shipment_id`),
  KEY `scheme_type` (`scheme_type`),
  KEY `distribution_id` (`distribution_id`),
  KEY `shipment_ibfk_3_idx` (`follows_up_from`),
  CONSTRAINT `shipment_ibfk_1` FOREIGN KEY (`scheme_type`) REFERENCES `scheme_list` (`scheme_id`),
  CONSTRAINT `shipment_ibfk_3` FOREIGN KEY (`follows_up_from`) REFERENCES `shipment` (`shipment_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `shipment_ibfk_4` FOREIGN KEY (`distribution_id`) REFERENCES `distributions` (`distribution_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shipment_participant_map`
--

DROP TABLE IF EXISTS `shipment_participant_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipment_participant_map` (
  `map_id` int(11) NOT NULL AUTO_INCREMENT,
  `shipment_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `attributes` mediumtext,
  `evaluation_status` varchar(10) DEFAULT NULL COMMENT 'Shipment Status					\nUse this to flag - 					\nABCDEFG					',
  `shipment_score` decimal(5,2) DEFAULT NULL,
  `documentation_score` decimal(5,2) DEFAULT '0.00',
  `shipment_test_date` date DEFAULT NULL,
  `is_pt_test_not_performed` varchar(45) DEFAULT NULL,
  `pt_test_not_performed_comments` text,
  `pt_support_comments` text,
  `shipment_receipt_date` date DEFAULT NULL,
  `shipment_test_report_date` datetime DEFAULT NULL,
  `participant_supervisor` varchar(255) DEFAULT NULL,
  `supervisor_approval` varchar(45) DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `final_result` int(11) DEFAULT '0',
  `failure_reason` text,
  `evaluation_comment` int(11) DEFAULT '0',
  `optional_eval_comment` text,
  `is_followup` varchar(255) DEFAULT 'no',
  `is_excluded` varchar(255) NOT NULL DEFAULT 'no',
  `user_comment` text,
  `custom_field_1` text,
  `custom_field_2` text,
  `created_on_admin` datetime DEFAULT NULL,
  `updated_on_admin` datetime DEFAULT NULL,
  `updated_by_admin` varchar(45) DEFAULT NULL,
  `updated_on_user` datetime DEFAULT NULL,
  `updated_by_user` varchar(45) DEFAULT NULL,
  `created_by_admin` varchar(45) DEFAULT NULL,
  `created_on_user` datetime DEFAULT NULL,
  `report_generated` varchar(100) DEFAULT NULL,
  `last_new_shipment_mailed_on` datetime DEFAULT NULL,
  `new_shipment_mail_count` int(11) NOT NULL DEFAULT '0',
  `last_not_participated_mailed_on` datetime DEFAULT NULL,
  `last_not_participated_mail_count` int(11) NOT NULL DEFAULT '0',
  `qc_done` varchar(45) NOT NULL DEFAULT 'no',
  `qc_date` date DEFAULT NULL,
  `qc_done_by` varchar(255) DEFAULT NULL,
  `qc_created_on` datetime DEFAULT NULL,
  `mode_id` int(11) DEFAULT NULL,
  `not_tested_reason` int(11) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  PRIMARY KEY (`map_id`),
  UNIQUE KEY `shipment_id_2` (`shipment_id`,`participant_id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `participant_id` (`participant_id`),
  CONSTRAINT `shipment_participant_map_ibfk_3` FOREIGN KEY (`shipment_id`) REFERENCES `shipment` (`shipment_id`) ON DELETE CASCADE,
  CONSTRAINT `shipment_participant_map_ibfk_4` FOREIGN KEY (`participant_id`) REFERENCES `participant` (`participant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Shipment for DTS Samples';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_admin`
--

DROP TABLE IF EXISTS `system_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `primary_email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `secondary_email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `force_password_reset` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'inactive',
  `is_ptcc_coordinator` int(1) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `include_as_pecc_in_reports` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp_mail`
--

DROP TABLE IF EXISTS `temp_mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp_mail` (
  `temp_id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text,
  `from_mail` varchar(255) DEFAULT NULL,
  `to_email` varchar(255) NOT NULL,
  `bcc` text,
  `cc` text,
  `subject` text,
  `from_full_name` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`temp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp_push_notification`
--

DROP TABLE IF EXISTS `temp_push_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp_push_notification` (
  `temp_id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(50) NOT NULL,
  `sound` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `data` longtext,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`temp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `raw_submission`
--

DROP TABLE IF EXISTS `raw_submission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `raw_submission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `details` longtext NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'create_database'
--

/*!50003 DROP PROCEDURE IF EXISTS `CreateParticipant` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `CreateParticipant`(IN countryName VARCHAR(255), IN labName VARCHAR(500), IN labIdentifier VARCHAR(255))
BEGIN
    IF NOT EXISTS(SELECT unique_identifier FROM participant WHERE unique_identifier = labIdentifier) THEN
        SET @dataManagerIdentifier = 0;

        INSERT INTO data_manager (primary_email,`password`,first_name,force_password_reset,qc_access,enable_adding_test_response_date,enable_choosing_mode_of_receipt,view_only_access,`status`,created_on,created_by)
        VALUES (CONCAT(labIdentifier,'@ept.systemone.id'),labIdentifier,substr(labName,1,45),1,'yes','yes','yes','no','active',NOW(),1);

        SET @dataManagerIdentifier = LAST_INSERT_ID();

        SET @countryId = 0;
        SET @countryId = (SELECT id FROM countries WHERE iso_name = countryName);

        SET @participantId = 0;

        INSERT INTO participant (unique_identifier,individual,lab_name,country,site_type,first_name,last_name,contact_name,created_on,created_by,`status`)
        VALUES (labIdentifier,'no',labName,@countryId,7,labName,'',SUBSTR(labName,1,255),NOW(),1,'active');

        SET @participantId = LAST_INSERT_ID();

        INSERT INTO participant_manager_map (participant_id,dm_id) VALUES (@participantId,@dataManagerIdentifier);
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `CreateResponseSample` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `CreateResponseSample`(IN shipmentId INT, IN mapId INT, IN sampleId INT,
                                        IN dateTested VARCHAR(20), IN mtbDetected VARCHAR(255), IN rifResistance VARCHAR(255),
                                        IN instrumentLastCalibrated VARCHAR(20), IN probeD VARCHAR(255), IN probeC VARCHAR(255),
                                        IN probeE VARCHAR(255), IN probeB VARCHAR(255), IN spcProbe VARCHAR(255), IN probeA VARCHAR(255),
                                        OUT sampleScore INT, OUT maxSampleScore INT)
BEGIN
	SELECT mtb_detected, rif_resistance, sample_score
    INTO @expectedSampleMtbDetected, @expectedSampleRifResistance, @samplePassScore
	FROM reference_result_tb
	WHERE shipment_id = shipmentId
	AND sample_id = sampleId;

    SET maxSampleScore = @samplePassScore;

	SET @formattedSampleMtbDetected = CASE WHEN TRIM(mtbDetected) = 'Very Low' THEN 'veryLow'
	  WHEN TRIM(mtbDetected) = 'No Result'
		OR TRIM(mtbDetected) = '' THEN 'noResult'
	  WHEN TRIM(mtbDetected) = 'Not Detected' THEN 'notDetected'
	  WHEN mtbDetected LIKE '%Power%' THEN 'invalid'
	  WHEN mtbDetected LIKE '%Error%' THEN 'error'
	  ELSE LCASE(TRIM(mtbDetected))
	END;

	SET @formattedSampleRifResistance = CASE WHEN TRIM(rifResistance) = 'Low'
												OR TRIM(rifResistance) = 'detected'
												OR TRIM(rifResistance) = 'Very Low' THEN 'detected'
	  WHEN TRIM(rifResistance) = 'Not Detected' THEN 'notDetected'
	  WHEN rifResistance LIKE '%Power%'
		OR rifResistance LIKE '%Error%'
		OR TRIM(rifResistance) = 'Indeteminant'
		OR TRIM(rifResistance) = 'Indeterminate'
		OR TRIM(rifResistance) = 'NOT REPORTED'
		OR TRIM(rifResistance) = ''
		OR TRIM(rifResistance) = 'INVALID' THEN ''
	  ELSE 'na'
	END;

    SET @formattedErrorCode = CASE WHEN mtbDetected LIKE '%Error%'
		AND mtbDetected NOT LIKE '%Power%' THEN TRIM(REPLACE(mtbDetected, 'Error', ''))
	  WHEN rifResistance LIKE '%Error%'
		AND rifResistance NOT LIKE '%Power%' THEN TRIM(REPLACE(rifResistance, 'Error', ''))
	END;

	SET sampleScore = 0;

	IF @expectedSampleMtbDetected = @formattedSampleMtbDetected
    AND (@expectedSampleRifResistance = @formattedSampleRifResistance OR
         (@expectedSampleRifResistance IN ('', 'na') AND @formattedSampleRifResistance IN ('', 'na'))) THEN
	  SET sampleScore = @samplePassScore;
	END IF;

    IF EXISTS (SELECT shipment_map_id FROM response_result_tb WHERE sample_id = sampleId AND shipment_map_id = mapId) THEN
        SET SQL_SAFE_UPDATES = 0;
		UPDATE response_result_tb
          SET instrument_last_calibrated_on = CASE WHEN instrumentLastCalibrated = '' THEN instrument_last_calibrated_on ELSE instrumentLastCalibrated END,
              error_code = @formattedErrorCode,
		      date_tested = CASE WHEN dateTested = '' THEN date_tested ELSE dateTested END,
		      mtb_detected = @formattedSampleMtbDetected,
		      rif_resistance = @formattedSampleRifResistance,
		      probe_d = CASE WHEN probeD IN ('', 'No Result', 'ERROR', 'NA', 'POSITIVE', 'NEGATIVE', 'NOT REPORTED', 'APROVED', 'PASS', 'NEG', 'POS', 'N/A', '-') THEN '' ELSE CAST(TRIM(REPLACE(REPLACE(probeD, ',', '.'), 'O', '0')) AS DECIMAL(10,1)) END,
		      probe_c = CASE WHEN probeC IN ('', 'No Result', 'ERROR', 'NA', 'POSITIVE', 'NEGATIVE', 'NOT REPORTED', 'APROVED', 'PASS', 'NEG', 'POS', 'N/A', '-') THEN '' ELSE CAST(TRIM(REPLACE(REPLACE(probeC, ',', '.'), 'O', '0')) AS DECIMAL(10,1)) END,
              probe_e = CASE WHEN probeE IN ('', 'No Result', 'ERROR', 'NA', 'POSITIVE', 'NEGATIVE', 'NOT REPORTED', 'APROVED', 'PASS', 'NEG', 'POS', 'N/A', '-') THEN '' ELSE CAST(TRIM(REPLACE(REPLACE(probeE, ',', '.'), 'O', '0')) AS DECIMAL(10,1)) END,
              probe_b = CASE WHEN probeB IN ('', 'No Result', 'ERROR', 'NA', 'POSITIVE', 'NEGATIVE', 'NOT REPORTED', 'APROVED', 'PASS', 'NEG', 'POS', 'N/A', '-') THEN '' ELSE CAST(TRIM(REPLACE(REPLACE(probeB, ',', '.'), 'O', '0')) AS DECIMAL(10,1)) END,
              spc = CASE WHEN spcProbe IN ('', 'No Result', 'ERROR', 'NA', 'POSITIVE', 'NEGATIVE', 'NOT REPORTED', 'APROVED', 'PASS', 'NEG', 'POS', 'N/A', '-') THEN '' ELSE CAST(TRIM(REPLACE(REPLACE(spcProbe, ',', '.'), 'O', '0')) AS DECIMAL(10,1)) END,
              probe_a = CASE WHEN probeA IN ('', 'No Result', 'ERROR', 'NA', 'POSITIVE', 'NEGATIVE', 'NOT REPORTED', 'APROVED', 'PASS', 'NEG', 'POS', 'N/A', '-') THEN '' ELSE CAST(TRIM(REPLACE(REPLACE(probeA, ',', '.'), 'O', '0')) AS DECIMAL(10,1)) END,
		      calculated_score = CASE WHEN sampleScore > 0 THEN 'pass' ELSE 'fail' END
		WHERE shipment_map_id = mapId
		AND sample_id = sampleId;
    ELSEIF mapId IS NOT NULL AND mapId <> '' AND mapId > 0 THEN
        INSERT INTO response_result_tb (shipment_map_id, sample_id, instrument_last_calibrated_on, error_code, date_tested,
                                        mtb_detected, rif_resistance, probe_d, probe_c, probe_e, probe_b, spc, probe_a,
                                        calculated_score, created_by, created_on)
        VALUES (mapId, sampleId, CASE WHEN instrumentLastCalibrated = '' THEN NULL ELSE instrumentLastCalibrated END,
                @formattedErrorCode, CASE WHEN dateTested = '' THEN NULL ELSE dateTested END, @formattedSampleMtbDetected,
                @formattedSampleRifResistance,
                CASE WHEN probeD IN ('', 'No Result', 'ERROR', 'NA', 'POSITIVE', 'NEGATIVE', 'NOT REPORTED', 'APROVED', 'PASS', 'NEG', 'POS', 'N/A', '-') THEN '' ELSE CAST(TRIM(REPLACE(REPLACE(probeD, ',', '.'), 'O', '0')) AS DECIMAL(10,1)) END,
                CASE WHEN probeC IN ('', 'No Result', 'ERROR', 'NA', 'POSITIVE', 'NEGATIVE', 'NOT REPORTED', 'APROVED', 'PASS', 'NEG', 'POS', 'N/A', '-') THEN '' ELSE CAST(TRIM(REPLACE(REPLACE(probeC, ',', '.'), 'O', '0')) AS DECIMAL(10,1)) END,
                CASE WHEN probeE IN ('', 'No Result', 'ERROR', 'NA', 'POSITIVE', 'NEGATIVE', 'NOT REPORTED', 'APROVED', 'PASS', 'NEG', 'POS', 'N/A', '-') THEN '' ELSE CAST(TRIM(REPLACE(REPLACE(probeE, ',', '.'), 'O', '0')) AS DECIMAL(10,1)) END,
                CASE WHEN probeB IN ('', 'No Result', 'ERROR', 'NA', 'POSITIVE', 'NEGATIVE', 'NOT REPORTED', 'APROVED', 'PASS', 'NEG', 'POS', 'N/A', '-') THEN '' ELSE CAST(TRIM(REPLACE(REPLACE(probeB, ',', '.'), 'O', '0')) AS DECIMAL(10,1)) END,
                CASE WHEN spcProbe IN ('', 'No Result', 'ERROR', 'NA', 'POSITIVE', 'NEGATIVE', 'NOT REPORTED', 'APROVED', 'PASS', 'NEG', 'POS', 'N/A', '-') THEN '' ELSE CAST(TRIM(REPLACE(REPLACE(spcProbe, ',', '.'), 'O', '0')) AS DECIMAL(10,1)) END,
                CASE WHEN probeA IN ('', 'No Result', 'ERROR', 'NA', 'POSITIVE', 'NEGATIVE', 'NOT REPORTED', 'APROVED', 'PASS', 'NEG', 'POS', 'N/A', '-') THEN '' ELSE CAST(TRIM(REPLACE(REPLACE(probeA, ',', '.'), 'O', '0')) AS DECIMAL(10,1)) END,
                CASE WHEN sampleScore > 0 THEN 'pass' ELSE 'fail' END, 1, NOW());
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `UpdateResponse` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `UpdateResponse`(IN labName VARCHAR(500), IN ptId VARCHAR(255),
                                        IN shipmentCode VARCHAR(255), IN testingDate VARCHAR(20), IN shipmentReceiptDate VARCHAR(20),
                                        IN responseReceiptDate VARCHAR(20), IN mtbRifKitLotNo VARCHAR(255), IN expiryDate VARCHAR(20),
                                        IN userComment VARCHAR(500), IN optionalEvalComment VARCHAR(1000),
                                        IN instrumentLastCalibrated VARCHAR(20),
                                        IN dateTested1 VARCHAR(20), IN mtbDetected1 VARCHAR(255), IN rifResistance1 VARCHAR(255),
                                        IN probeD1 VARCHAR(255), IN probeC1 VARCHAR(255), IN probeE1 VARCHAR(255),
                                        IN probeB1 VARCHAR(255), IN spc1 VARCHAR(255), IN probeA1 VARCHAR(255),
                                        IN dateTested2 VARCHAR(20), IN mtbDetected2 VARCHAR(255), IN rifResistance2 VARCHAR(255),
                                        IN probeD2 VARCHAR(255), IN probeC2 VARCHAR(255), IN probeE2 VARCHAR(255),
                                        IN probeB2 VARCHAR(255), IN spc2 VARCHAR(255), IN probeA2 VARCHAR(255),
                                        IN dateTested3 VARCHAR(20), IN mtbDetected3 VARCHAR(255), IN rifResistance3 VARCHAR(255),
                                        IN probeD3 VARCHAR(255), IN probeC3 VARCHAR(255), IN probeE3 VARCHAR(255),
                                        IN probeB3 VARCHAR(255), IN spc3 VARCHAR(255), IN probeA3 VARCHAR(255),
                                        IN dateTested4 VARCHAR(20), IN mtbDetected4 VARCHAR(255), IN rifResistance4 VARCHAR(255),
                                        IN probeD4 VARCHAR(255), IN probeC4 VARCHAR(255), IN probeE4 VARCHAR(255),
                                        IN probeB4 VARCHAR(255), IN spc4 VARCHAR(255), IN probeA4 VARCHAR(255),
                                        IN dateTested5 VARCHAR(20), IN mtbDetected5 VARCHAR(255), IN rifResistance5 VARCHAR(255),
                                        IN probeD5 VARCHAR(255), IN probeC5 VARCHAR(255), IN probeE5 VARCHAR(255),
                                        IN probeB5 VARCHAR(255), IN spc5 VARCHAR(255), IN probeA5 VARCHAR(255))
BEGIN
	SET @participantId = (SELECT participant_id FROM participant WHERE lab_name = labName AND unique_identifier = ptId);
	SET @shipmentId = (SELECT shipment_id FROM shipment WHERE shipment_code = shipmentCode);
	SET @mapId = (SELECT map_id FROM shipment_participant_map WHERE shipment_id = @shipmentId AND participant_id = @participantId);
	SET @dmId = (SELECT dm_id FROM participant_manager_map WHERE participant_id = @participantId);

	SET @shipmentScore = 0;
	SET @shipmentMaxScore = 0;

	CALL CreateResponseSample (@shipmentId, @mapId, 1, dateTested1, mtbDetected1, rifResistance1, instrumentLastCalibrated, probeD1, probeC1, probeE1,
                               probeB1, spc1, probeA1, @sampleScore, @maxSampleScore);

	SET @shipmentScore = @shipmentScore + @sampleScore;
	SET @shipmentMaxScore = @shipmentMaxScore + @maxSampleScore;

	CALL CreateResponseSample (@shipmentId, @mapId, 2, dateTested2, mtbDetected2, rifResistance2, instrumentLastCalibrated, probeD2, probeC2, probeE2,
                               probeB2, spc2, probeA2, @sampleScore, @maxSampleScore);

	SET @shipmentScore = @shipmentScore + @sampleScore;
	SET @shipmentMaxScore = @shipmentMaxScore + @maxSampleScore;

	CALL CreateResponseSample (@shipmentId, @mapId, 3, dateTested3, mtbDetected3, rifResistance3, instrumentLastCalibrated, probeD3, probeC3, probeE3,
                               probeB3, spc3, probeA3, @sampleScore, @maxSampleScore);

	SET @shipmentScore = @shipmentScore + @sampleScore;
	SET @shipmentMaxScore = @shipmentMaxScore + @maxSampleScore;

	CALL CreateResponseSample (@shipmentId, @mapId, 4, dateTested4, mtbDetected4, rifResistance4, instrumentLastCalibrated, probeD4, probeC4, probeE4,
                               probeB4, spc4, probeA4, @sampleScore, @maxSampleScore);

	SET @shipmentScore = @shipmentScore + @sampleScore;
	SET @shipmentMaxScore = @shipmentMaxScore + @maxSampleScore;

	CALL CreateResponseSample (@shipmentId, @mapId, 5, dateTested5, mtbDetected5, rifResistance5, instrumentLastCalibrated, probeD5, probeC5, probeE5,
                               probeB5, spc5, probeA5, @sampleScore, @maxSampleScore);

	SET @shipmentScore = @shipmentScore + @sampleScore;
	SET @shipmentMaxScore = @shipmentMaxScore + @maxSampleScore;

	SET @finalResult = CASE WHEN @shipmentScore >= 80 THEN 1 ELSE 2 END; -- 1 = pass, 2 = fail
	SET @failureReason = CASE WHEN @shipmentScore < 80 THEN CONCAT('[{"warning":"Participant did not meet the score criteria (Participant Score - <strong>', @shipmentScore, '<\/strong> out of <strong>', @shipmentMaxScore, '<\/strong>)"}]') ELSE '[]' END;

	UPDATE shipment_participant_map
	SET attributes = CONCAT('{', CASE WHEN testingDate <> '' THEN CONCAT('"sample_rehydration_date":"', testingDate, '",') ELSE '' END, '"mtb_rif_kit_lot_no":"', mtbRifKitLotNo, '"', CASE WHEN expiryDate <> '' THEN CONCAT(',"expiry_date":"', expiryDate, '",') ELSE '' END, '"assay":"1","count_tests_conducted_over_month":"","count_errors_encountered_over_month":"","error_codes_encountered_over_month":""}'),
	  evaluation_status = '19111190',
	  shipment_score = @shipmentScore,
	  documentation_score = 0,
	  shipment_test_date = CASE WHEN testingDate = '' THEN shipment_test_date ELSE testingDate END,
	  shipment_receipt_date = CASE WHEN shipmentReceiptDate = '' THEN shipment_receipt_date ELSE shipmentReceiptDate END,
	  shipment_test_report_date = CASE WHEN responseReceiptDate = '' THEN shipment_test_report_date ELSE responseReceiptDate END,
	  participant_supervisor = '',
	  supervisor_approval = 'no',
	  final_result = @finalResult,
	  failure_reason = @failureReason,
	  user_comment = TRIM(userComment),
      optional_eval_comment = TRIM(optionalEvalComment),
	  updated_on_user = NOW(),
	  updated_by_user = @dmId,
	  mode_id = 1
	WHERE map_id = @mapId;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
