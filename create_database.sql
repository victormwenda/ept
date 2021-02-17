
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `Add_Indexing_in_reference_result_tb` ()  CREATE INDEX indexing_reference_result_tb ON reference_result_tb (shipment_id,sample_id,is_excluded)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Add_Index_in_respose_result_tb` ()  CREATE INDEX index_in_response_res_tb ON response_result_tb (sample_id,shipment_map_id)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Add_Index_in_r_tb_assay` ()  CREATE INDEX r_tb_assay_indexes ON r_tb_assay (id,short_name)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Add_Index_in_Shipment_Participent_map` ()  CREATE INDEX indexing_in_spm  ON shipment_participant_map (shipment_id,is_excluded,map_id,participant_id)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `api_credentials`
--

CREATE TABLE `api_credentials` (
                                   `id` int(11) NOT NULL,
                                   `username` varchar(255) DEFAULT NULL,
                                   `password` varchar(255) DEFAULT NULL,
                                   `active` int(1) UNSIGNED NOT NULL DEFAULT 1,
                                   `created_on` datetime DEFAULT NULL,
                                   `created_by` varchar(255) DEFAULT NULL,
                                   `updated_on` datetime DEFAULT NULL,
                                   `updated_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
                              `contact_id` int(11) NOT NULL,
                              `first_name` varchar(255) DEFAULT NULL,
                              `last_name` varchar(255) DEFAULT NULL,
                              `email` varchar(255) DEFAULT NULL,
                              `phone` varchar(255) DEFAULT NULL,
                              `reason` varchar(255) DEFAULT NULL,
                              `lab` varchar(255) DEFAULT NULL,
                              `additional_info` text DEFAULT NULL,
                              `contacted_on` datetime DEFAULT NULL,
                              `ip_address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
                             `id` int(10) UNSIGNED NOT NULL,
                             `iso_name` varchar(255) COLLATE utf8_bin NOT NULL,
                             `iso2` varchar(2) COLLATE utf8_bin NOT NULL,
                             `iso3` varchar(3) COLLATE utf8_bin NOT NULL,
                             `numeric_code` smallint(6) NOT NULL,
                             `gxalert_url` varchar(255) COLLATE utf8_bin DEFAULT NULL,
                             `gxalert_api_credentials` varchar(255) COLLATE utf8_bin DEFAULT NULL,
                             `show_monthly_indicators` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `country_shipment_map`
--

CREATE TABLE `country_shipment_map` (
                                        `country_id` int(10) UNSIGNED NOT NULL,
                                        `shipment_id` int(11) NOT NULL,
                                        `due_date_text` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_manager`
--

CREATE TABLE `data_manager` (
                                `dm_id` int(11) NOT NULL,
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
                                `force_password_reset` int(1) NOT NULL DEFAULT 0,
                                `qc_access` varchar(100) DEFAULT NULL,
                                `enable_adding_test_response_date` varchar(45) DEFAULT NULL,
                                `enable_choosing_mode_of_receipt` varchar(45) DEFAULT NULL,
                                `view_only_access` varchar(45) DEFAULT NULL,
                                `status` varchar(255) NOT NULL DEFAULT 'inactive',
                                `created_on` datetime DEFAULT NULL,
                                `created_by` varchar(255) DEFAULT NULL,
                                `updated_on` datetime DEFAULT NULL,
                                `updated_by` varchar(255) DEFAULT NULL,
                                `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A PT user Table for Data entry or report printing';

-- --------------------------------------------------------

--
-- Table structure for table `distributions`
--

CREATE TABLE `distributions` (
                                 `distribution_id` int(11) NOT NULL,
                                 `distribution_code` varchar(255) NOT NULL,
                                 `distribution_date` date NOT NULL,
                                 `status` varchar(255) NOT NULL,
                                 `created_on` datetime DEFAULT NULL,
                                 `created_by` varchar(255) DEFAULT NULL,
                                 `updated_on` datetime DEFAULT NULL,
                                 `updated_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dts_recommended_testkits`
--

CREATE TABLE `dts_recommended_testkits` (
                                            `test_no` int(11) NOT NULL,
                                            `testkit` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dts_shipment_corrective_action_map`
--

CREATE TABLE `dts_shipment_corrective_action_map` (
                                                      `shipment_map_id` int(11) NOT NULL,
                                                      `corrective_action_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
                               `scheme_id` varchar(255) NOT NULL,
                               `participant_id` int(11) NOT NULL,
                               `enrolled_on` date DEFAULT NULL,
                               `enrollment_ended_on` date DEFAULT NULL,
                               `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `global_config`
--

CREATE TABLE `global_config` (
                                 `name` varchar(255) NOT NULL,
                                 `value` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gxalert_result`
--

CREATE TABLE `gxalert_result` (
                                  `result_id` int(11) NOT NULL,
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
                                  `updated_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Result Data received from GxAlert';

-- --------------------------------------------------------

--
-- Table structure for table `home_banner`
--

CREATE TABLE `home_banner` (
                               `banner_id` int(11) NOT NULL,
                               `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `instrument`
--

CREATE TABLE `instrument` (
                              `instrument_id` int(11) NOT NULL,
                              `participant_id` int(11) NOT NULL,
                              `instrument_serial` varchar(45) DEFAULT NULL,
                              `instrument_installed_on` date DEFAULT NULL,
                              `instrument_last_calibrated_on` date DEFAULT NULL,
                              `created_by` varchar(45) DEFAULT NULL,
                              `created_on` datetime DEFAULT NULL,
                              `updated_by` varchar(45) DEFAULT NULL,
                              `updated_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mail_template`
--

CREATE TABLE `mail_template` (
                                 `mail_temp_id` int(11) NOT NULL,
                                 `mail_purpose` varchar(255) NOT NULL,
                                 `from_name` varchar(255) DEFAULT NULL,
                                 `mail_from` varchar(255) DEFAULT NULL,
                                 `mail_cc` varchar(255) DEFAULT NULL,
                                 `mail_bcc` varchar(255) DEFAULT NULL,
                                 `mail_subject` varchar(255) DEFAULT NULL,
                                 `mail_content` text DEFAULT NULL,
                                 `mail_footer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `participant`
--

CREATE TABLE `participant` (
                               `participant_id` int(11) NOT NULL,
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
                               `status` varchar(255) NOT NULL DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `participant_enrolled_programs_map`
--

CREATE TABLE `participant_enrolled_programs_map` (
                                                     `participant_id` int(11) NOT NULL,
                                                     `ep_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `participant_manager_map`
--

CREATE TABLE `participant_manager_map` (
                                           `participant_id` int(11) NOT NULL,
                                           `dm_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `participant_monthly_indicators`
--

CREATE TABLE `participant_monthly_indicators` (
                                                  `submission_id` int(11) NOT NULL,
                                                  `participant_id` int(11) NOT NULL,
                                                  `attributes` mediumtext DEFAULT NULL,
                                                  `created_by` varchar(45) DEFAULT NULL,
                                                  `created_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `participant_temp`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
                            `partner_id` int(11) NOT NULL,
                            `partner_name` varchar(500) DEFAULT NULL,
                            `link` varchar(500) DEFAULT NULL,
                            `sort_order` int(11) DEFAULT NULL,
                            `added_by` int(11) NOT NULL,
                            `added_on` datetime NOT NULL,
                            `status` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ptcc_country_map`
--

CREATE TABLE `ptcc_country_map` (
                                    `admin_id` int(11) NOT NULL,
                                    `country_id` int(10) UNSIGNED NOT NULL,
                                    `show_details_on_report` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE `publications` (
                                `publication_id` int(11) NOT NULL,
                                `content` text DEFAULT NULL,
                                `file_name` varchar(255) DEFAULT NULL,
                                `sort_order` int(11) DEFAULT NULL,
                                `added_by` int(11) NOT NULL,
                                `added_on` datetime NOT NULL,
                                `status` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `push_notification_token`
--

CREATE TABLE `push_notification_token` (
                                           `push_notification_token_id` int(11) NOT NULL,
                                           `dm_id` int(11) NOT NULL,
                                           `platform` varchar(20) NOT NULL,
                                           `push_notification_token` varchar(255) NOT NULL,
                                           `last_seen` datetime DEFAULT NULL,
                                           `updated_on` datetime DEFAULT NULL,
                                           `created_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reference_dbs_eia`
--

CREATE TABLE `reference_dbs_eia` (
                                     `id` int(11) NOT NULL,
                                     `shipment_id` int(11) NOT NULL,
                                     `sample_id` int(11) NOT NULL,
                                     `eia` int(11) NOT NULL,
                                     `lot` varchar(255) DEFAULT NULL,
                                     `exp_date` date DEFAULT NULL,
                                     `od` varchar(255) DEFAULT NULL,
                                     `cutoff` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reference_dbs_wb`
--

CREATE TABLE `reference_dbs_wb` (
                                    `id` int(11) NOT NULL,
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
                                    `17` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reference_dts_eia`
--

CREATE TABLE `reference_dts_eia` (
                                     `id` int(11) NOT NULL,
                                     `shipment_id` int(11) NOT NULL,
                                     `sample_id` int(11) NOT NULL,
                                     `eia` int(11) NOT NULL,
                                     `lot` varchar(255) DEFAULT NULL,
                                     `exp_date` date DEFAULT NULL,
                                     `od` varchar(255) DEFAULT NULL,
                                     `cutoff` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reference_dts_rapid_hiv`
--

CREATE TABLE `reference_dts_rapid_hiv` (
                                           `id` int(11) NOT NULL,
                                           `shipment_id` varchar(255) NOT NULL,
                                           `sample_id` varchar(255) NOT NULL,
                                           `testkit` varchar(255) NOT NULL,
                                           `lot_no` varchar(255) NOT NULL,
                                           `expiry_date` date NOT NULL,
                                           `result` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reference_dts_wb`
--

CREATE TABLE `reference_dts_wb` (
                                    `id` int(11) NOT NULL,
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
                                    `17` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reference_result_dbs`
--

CREATE TABLE `reference_result_dbs` (
                                        `shipment_id` int(11) NOT NULL,
                                        `sample_id` int(11) NOT NULL,
                                        `sample_label` varchar(45) DEFAULT NULL,
                                        `reference_result` varchar(45) DEFAULT NULL,
                                        `control` int(11) DEFAULT NULL,
                                        `mandatory` int(11) NOT NULL DEFAULT 0,
                                        `sample_score` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Referance Result for DBS Shipment';

-- --------------------------------------------------------

--
-- Table structure for table `reference_result_dts`
--

CREATE TABLE `reference_result_dts` (
                                        `shipment_id` int(11) NOT NULL,
                                        `sample_id` int(11) NOT NULL,
                                        `sample_label` varchar(45) DEFAULT NULL,
                                        `reference_result` varchar(45) DEFAULT NULL,
                                        `control` int(11) DEFAULT NULL,
                                        `mandatory` int(11) NOT NULL DEFAULT 0,
                                        `sample_score` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Referance Result for DTS Shipment';

-- --------------------------------------------------------

--
-- Table structure for table `reference_result_eid`
--

CREATE TABLE `reference_result_eid` (
                                        `shipment_id` int(11) NOT NULL,
                                        `sample_id` int(11) NOT NULL,
                                        `sample_label` varchar(255) DEFAULT NULL,
                                        `reference_result` varchar(255) DEFAULT NULL,
                                        `control` int(11) DEFAULT NULL,
                                        `reference_hiv_ct_od` varchar(45) DEFAULT NULL,
                                        `reference_ic_qs` varchar(45) DEFAULT NULL,
                                        `mandatory` int(11) NOT NULL DEFAULT 0,
                                        `sample_score` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reference_result_tb`
--

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
                                       `mandatory` int(11) NOT NULL DEFAULT 0,
                                       `sample_score` int(11) NOT NULL DEFAULT 1,
                                       `is_excluded` varchar(5) NOT NULL DEFAULT 'no',
                                       `is_exempt` varchar(5) NOT NULL DEFAULT 'no',
                                       `excluded_reason` text DEFAULT NULL,
                                       `sample_content` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reference_result_vl`
--

CREATE TABLE `reference_result_vl` (
                                       `shipment_id` int(11) NOT NULL,
                                       `sample_id` int(11) NOT NULL,
                                       `sample_label` varchar(255) DEFAULT NULL,
                                       `reference_result` varchar(45) DEFAULT NULL,
                                       `control` int(11) DEFAULT NULL,
                                       `mandatory` int(11) NOT NULL DEFAULT 0,
                                       `sample_score` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reference_vl_calculation`
--

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
                                            `use_range` varchar(255) NOT NULL DEFAULT 'calculated'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reference_vl_methods`
--

CREATE TABLE `reference_vl_methods` (
                                        `shipment_id` int(11) NOT NULL,
                                        `sample_id` int(11) NOT NULL,
                                        `assay` int(11) NOT NULL,
                                        `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `report_config`
--

CREATE TABLE `report_config` (
                                 `name` varchar(255) NOT NULL DEFAULT '',
                                 `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `report_download_log`
--

CREATE TABLE `report_download_log` (
                                       `shipment_id` int(11) NOT NULL,
                                       `participant_id` int(11) NOT NULL,
                                       `request_data` text DEFAULT NULL,
                                       `timestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Logs all report downloads';

-- --------------------------------------------------------

--
-- Table structure for table `response_not_tested_reason`
--

CREATE TABLE `response_not_tested_reason` (
                                              `not_tested_reason_id` int(11) NOT NULL,
                                              `not_tested_reason` varchar(500) DEFAULT NULL,
                                              `status` varchar(45) NOT NULL DEFAULT 'active',
                                              `scheme_type` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `response_result_dbs`
--

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
                                       `updated_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `response_result_dts`
--

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
                                       `updated_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `response_result_eid`
--

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
                                       `updated_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `response_result_tb`
--

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
                                      `updated_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `response_result_vl`
--

CREATE TABLE `response_result_vl` (
                                      `shipment_map_id` int(11) NOT NULL,
                                      `sample_id` varchar(45) NOT NULL,
                                      `reported_viral_load` varchar(255) DEFAULT NULL,
                                      `calculated_score` varchar(45) DEFAULT NULL,
                                      `is_tnd` varchar(45) DEFAULT NULL,
                                      `created_by` varchar(45) DEFAULT NULL,
                                      `created_on` datetime DEFAULT NULL,
                                      `updated_by` varchar(45) DEFAULT NULL,
                                      `updated_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_control`
--

CREATE TABLE `r_control` (
                             `control_id` int(11) NOT NULL,
                             `control_name` varchar(255) DEFAULT NULL,
                             `for_scheme` varchar(255) DEFAULT NULL,
                             `is_active` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_dbs_eia`
--

CREATE TABLE `r_dbs_eia` (
                             `eia_id` int(11) NOT NULL,
                             `eia_name` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_dbs_wb`
--

CREATE TABLE `r_dbs_wb` (
                            `wb_id` int(11) NOT NULL,
                            `wb_name` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_dts_corrective_actions`
--

CREATE TABLE `r_dts_corrective_actions` (
                                            `action_id` int(11) NOT NULL,
                                            `corrective_action` text NOT NULL,
                                            `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_eid_detection_assay`
--

CREATE TABLE `r_eid_detection_assay` (
                                         `id` int(11) NOT NULL,
                                         `name` varchar(255) NOT NULL,
                                         `status` varchar(45) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_eid_extraction_assay`
--

CREATE TABLE `r_eid_extraction_assay` (
                                          `id` int(11) NOT NULL,
                                          `name` varchar(255) NOT NULL,
                                          `status` varchar(45) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_enrolled_programs`
--

CREATE TABLE `r_enrolled_programs` (
                                       `r_epid` int(11) NOT NULL,
                                       `enrolled_programs` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_evaluation_comments`
--

CREATE TABLE `r_evaluation_comments` (
                                         `comment_id` int(11) NOT NULL,
                                         `scheme` varchar(255) NOT NULL,
                                         `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_modes_of_receipt`
--

CREATE TABLE `r_modes_of_receipt` (
                                      `mode_id` int(11) NOT NULL,
                                      `mode_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_network_tiers`
--

CREATE TABLE `r_network_tiers` (
                                   `network_id` int(11) NOT NULL,
                                   `network_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_participant_affiliates`
--

CREATE TABLE `r_participant_affiliates` (
                                            `aff_id` int(11) NOT NULL,
                                            `affiliate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_possibleresult`
--

CREATE TABLE `r_possibleresult` (
                                    `id` int(11) NOT NULL,
                                    `scheme_id` varchar(45) NOT NULL,
                                    `scheme_sub_group` varchar(45) DEFAULT NULL,
                                    `response` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_results`
--

CREATE TABLE `r_results` (
                             `result_id` int(11) NOT NULL,
                             `result_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_site_type`
--

CREATE TABLE `r_site_type` (
                               `r_stid` int(11) NOT NULL,
                               `site_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_tb_assay`
--

CREATE TABLE `r_tb_assay` (
                              `id` int(11) NOT NULL,
                              `name` varchar(255) NOT NULL,
                              `short_name` varchar(255) NOT NULL,
                              `analyte1Label` varchar(45) NOT NULL DEFAULT 'Probe D',
                              `analyte2Label` varchar(45) NOT NULL DEFAULT 'Probe C',
                              `analyte3Label` varchar(45) NOT NULL DEFAULT 'Probe E',
                              `analyte4Label` varchar(45) NOT NULL DEFAULT 'Probe B',
                              `analyte5Label` varchar(45) NOT NULL DEFAULT 'SPC',
                              `analyte6Label` varchar(45) NOT NULL DEFAULT 'Probe A',
                              `includeTraceForMtbDetected` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_testkitname_dts`
--

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
                                     `Approval` int(1) DEFAULT 1 COMMENT '1 = Approved , 0 not approved.',
                                     `TestKit_ApprovalAgency` varchar(20) DEFAULT NULL COMMENT 'USAID, FDA, LOCAL',
                                     `source_reference` varchar(50) DEFAULT NULL,
                                     `CountryAdapted` int(11) DEFAULT NULL COMMENT '0= Not allowed in the country 1 = approved in country ',
                                     `testkit_1` int(11) NOT NULL DEFAULT 0,
                                     `testkit_2` int(11) NOT NULL DEFAULT 0,
                                     `testkit_3` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `r_vl_assay`
--

CREATE TABLE `r_vl_assay` (
                              `id` int(11) NOT NULL,
                              `name` varchar(255) NOT NULL,
                              `short_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `scheme_list`
--

CREATE TABLE `scheme_list` (
                               `scheme_id` varchar(10) NOT NULL,
                               `scheme_name` varchar(255) NOT NULL,
                               `response_table` varchar(45) DEFAULT NULL,
                               `reference_result_table` varchar(45) DEFAULT NULL,
                               `attribute_list` varchar(255) DEFAULT NULL,
                               `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shipment`
--

CREATE TABLE `shipment` (
                            `shipment_id` int(11) NOT NULL,
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
                            `shipment_comment` text DEFAULT NULL,
                            `created_by_admin` varchar(255) DEFAULT NULL,
                            `created_on_admin` datetime DEFAULT NULL,
                            `updated_by_admin` varchar(255) DEFAULT NULL,
                            `updated_on_admin` datetime DEFAULT NULL,
                            `status` varchar(255) NOT NULL DEFAULT 'pending',
                            `follows_up_from` int(11) DEFAULT NULL,
                            `is_official` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shipment_participant_map`
--

CREATE TABLE `shipment_participant_map` (
                                            `map_id` int(11) NOT NULL,
                                            `shipment_id` int(11) NOT NULL,
                                            `participant_id` int(11) NOT NULL,
                                            `attributes` mediumtext DEFAULT NULL,
                                            `evaluation_status` varchar(10) DEFAULT NULL COMMENT 'Shipment Status					\nUse this to flag - 					\nABCDEFG					',
                                            `shipment_score` decimal(5,2) DEFAULT NULL,
                                            `documentation_score` decimal(5,2) DEFAULT 0.00,
                                            `shipment_test_date` date DEFAULT NULL,
                                            `is_pt_test_not_performed` varchar(45) DEFAULT NULL,
                                            `pt_test_not_performed_comments` text DEFAULT NULL,
                                            `pt_support_comments` text DEFAULT NULL,
                                            `shipment_receipt_date` date DEFAULT NULL,
                                            `shipment_test_report_date` datetime DEFAULT NULL,
                                            `participant_supervisor` varchar(255) DEFAULT NULL,
                                            `supervisor_approval` varchar(45) DEFAULT NULL,
                                            `review_date` date DEFAULT NULL,
                                            `final_result` int(11) DEFAULT 0,
                                            `failure_reason` text DEFAULT NULL,
                                            `evaluation_comment` int(11) DEFAULT 0,
                                            `optional_eval_comment` text DEFAULT NULL,
                                            `is_followup` varchar(255) DEFAULT 'no',
                                            `is_excluded` varchar(255) NOT NULL DEFAULT 'no',
                                            `user_comment` text DEFAULT NULL,
                                            `custom_field_1` text DEFAULT NULL,
                                            `custom_field_2` text DEFAULT NULL,
                                            `created_on_admin` datetime DEFAULT NULL,
                                            `updated_on_admin` datetime DEFAULT NULL,
                                            `updated_by_admin` varchar(45) DEFAULT NULL,
                                            `updated_on_user` datetime DEFAULT NULL,
                                            `updated_by_user` varchar(45) DEFAULT NULL,
                                            `created_by_admin` varchar(45) DEFAULT NULL,
                                            `created_on_user` datetime DEFAULT NULL,
                                            `report_generated` varchar(100) DEFAULT NULL,
                                            `last_new_shipment_mailed_on` datetime DEFAULT NULL,
                                            `new_shipment_mail_count` int(11) NOT NULL DEFAULT 0,
                                            `last_not_participated_mailed_on` datetime DEFAULT NULL,
                                            `last_not_participated_mail_count` int(11) NOT NULL DEFAULT 0,
                                            `qc_done` varchar(45) NOT NULL DEFAULT 'no',
                                            `qc_date` date DEFAULT NULL,
                                            `qc_done_by` varchar(255) DEFAULT NULL,
                                            `qc_created_on` datetime DEFAULT NULL,
                                            `mode_id` int(11) DEFAULT NULL,
                                            `not_tested_reason` int(11) DEFAULT NULL,
                                            `date_submitted` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Shipment for DTS Samples';

-- --------------------------------------------------------

--
-- Table structure for table `system_admin`
--

CREATE TABLE `system_admin` (
                                `admin_id` int(11) NOT NULL,
                                `first_name` varchar(255) DEFAULT NULL,
                                `last_name` varchar(255) DEFAULT NULL,
                                `primary_email` varchar(255) DEFAULT NULL,
                                `password` varchar(255) DEFAULT NULL,
                                `secondary_email` varchar(255) DEFAULT NULL,
                                `phone` varchar(255) DEFAULT NULL,
                                `force_password_reset` int(11) DEFAULT NULL,
                                `status` varchar(255) DEFAULT 'inactive',
                                `is_ptcc_coordinator` int(1) UNSIGNED NOT NULL DEFAULT 0,
                                `created_on` datetime DEFAULT NULL,
                                `created_by` varchar(255) DEFAULT NULL,
                                `updated_on` datetime DEFAULT NULL,
                                `updated_by` varchar(255) DEFAULT NULL,
                                `include_as_pecc_in_reports` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `temp_mail`
--

CREATE TABLE `temp_mail` (
                             `temp_id` int(11) NOT NULL,
                             `message` text DEFAULT NULL,
                             `from_mail` varchar(255) DEFAULT NULL,
                             `to_email` varchar(255) NOT NULL,
                             `bcc` text DEFAULT NULL,
                             `cc` text DEFAULT NULL,
                             `subject` text DEFAULT NULL,
                             `from_full_name` varchar(255) DEFAULT NULL,
                             `status` varchar(255) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reference_result_tb`
--
ALTER TABLE `reference_result_tb`
    ADD KEY `indexing_reference_result_tb` (`shipment_id`,`sample_id`,`is_excluded`);

--
-- Indexes for table `response_result_tb`
--
ALTER TABLE `response_result_tb`
    ADD KEY `index_in_response_res_tb` (`sample_id`,`shipment_map_id`);

--
-- Indexes for table `r_tb_assay`
--
ALTER TABLE `r_tb_assay`
    ADD KEY `r_tb_assay_indexes` (`id`,`short_name`);

--
-- Indexes for table `r_vl_assay`
--
ALTER TABLE `r_vl_assay`
    ADD KEY `r_tb_assay_indexes` (`id`,`short_name`);

--
-- Indexes for table `shipment`
--
ALTER TABLE `shipment`
    ADD PRIMARY KEY (`shipment_id`);

--
-- Indexes for table `shipment_participant_map`
--
ALTER TABLE `shipment_participant_map`
    ADD KEY `indexing_in_spm` (`shipment_id`,`is_excluded`,`map_id`,`participant_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
