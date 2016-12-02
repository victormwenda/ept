CREATE TABLE `reference_result_tb` (
  `shipment_id` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `sample_label` varchar(255) DEFAULT NULL,
  `mtb_detected` varchar(255) DEFAULT NULL,
  `rif_resistance` varchar(255) DEFAULT NULL,
  `probe_d` varchar(255) DEFAULT NULL,
  `probe_c` varchar(255) DEFAULT NULL,
  `probe_e` varchar(255) DEFAULT NULL,
  `probe_b` varchar(255) DEFAULT NULL,
  `spc` varchar(255) DEFAULT NULL,
  `probe_a` varchar(255) DEFAULT NULL,
  `control` int(11) DEFAULT NULL,
  `mandatory` int(11) NOT NULL DEFAULT '0',
  `sample_score` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1