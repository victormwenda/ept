ALTER TABLE `r_tb_assay` ADD INDEX( `id`,`short_name`);
ALTER TABLE `reference_result_tb` ADD INDEX( `shipment_id`,`sample_id`,`is_excluded`);
ALTER TABLE `response_result_tb` ADD INDEX( `shipment_map_id`,`sample_id`);
ALTER TABLE `shipment` ADD INDEX( `shipment_id`);
ALTER TABLE `shipment_participant_map` ADD INDEX( `attributes`,`map_id`,`is_excluded`);
