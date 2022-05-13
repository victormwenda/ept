SELECT FlattenedEvaluationResults.`Country`, FlattenedEvaluationResults.`Site No.`, FlattenedEvaluationResults.`Site Name/Location`, FlattenedEvaluationResults.`PT-ID`,
FlattenedEvaluationResults.Submitted, FlattenedEvaluationResults.`Submission Excluded`,
FlattenedEvaluationResults.`Date PT Received`, FlattenedEvaluationResults.`Date PT Results Reported`,
JSON_UNQUOTE(FlattenedEvaluationResults.attributes_json->"$.cartridge_lot_no") AS `Cartridge Lot Number`,
FlattenedEvaluationResults.assay_name AS `Assay`,
CASE WHEN JSON_UNQUOTE(FlattenedEvaluationResults.attributes_json->"$.expiry_date") = '0000-00-00' THEN NULL
ELSE COALESCE(
    STR_TO_DATE(JSON_UNQUOTE(FlattenedEvaluationResults.attributes_json->"$.expiry_date"), '%d-%b-%Y'),
    STR_TO_DATE(JSON_UNQUOTE(FlattenedEvaluationResults.attributes_json->"$.expiry_date"), '%Y-%b-%d'),
    STR_TO_DATE(JSON_UNQUOTE(FlattenedEvaluationResults.attributes_json->"$.expiry_date"), '%d-%m-%Y'),
    STR_TO_DATE(JSON_UNQUOTE(FlattenedEvaluationResults.attributes_json->"$.expiry_date"), '%Y-%m-%d'))
END AS `Expiry Date`,
FlattenedEvaluationResults.`Date of last instrument calibration`, FlattenedEvaluationResults.`Participated`, FlattenedEvaluationResults.`Reason for No Submission`,

FlattenedEvaluationResults.`1-Date Tested`, FlattenedEvaluationResults.`1-Instrument Serial`, FlattenedEvaluationResults.`1-Instrument Last Calibrated`,
FlattenedEvaluationResults.`1-MTB`, FlattenedEvaluationResults.`1-Rif`, FlattenedEvaluationResults.`1-Probe 1`, FlattenedEvaluationResults.`1-Probe 2`,
FlattenedEvaluationResults.`1-Probe 3`, FlattenedEvaluationResults.`1-Probe 4`, FlattenedEvaluationResults.`1-Probe 5`, FlattenedEvaluationResults.`1-Probe 6`,

FlattenedEvaluationResults.`2-Date Tested`, FlattenedEvaluationResults.`2-Instrument Serial`, FlattenedEvaluationResults.`2-Instrument Last Calibrated`,
FlattenedEvaluationResults.`2-MTB`, FlattenedEvaluationResults.`2-Rif`, FlattenedEvaluationResults.`2-Probe 1`, FlattenedEvaluationResults.`2-Probe 2`,
FlattenedEvaluationResults.`2-Probe 3`, FlattenedEvaluationResults.`2-Probe 4`, FlattenedEvaluationResults.`2-Probe 5`, FlattenedEvaluationResults.`2-Probe 6`,

FlattenedEvaluationResults.`3-Date Tested`, FlattenedEvaluationResults.`3-Instrument Serial`, FlattenedEvaluationResults.`3-Instrument Last Calibrated`,
FlattenedEvaluationResults.`3-MTB`, FlattenedEvaluationResults.`3-Rif`, FlattenedEvaluationResults.`3-Probe 1`, FlattenedEvaluationResults.`3-Probe 2`,
FlattenedEvaluationResults.`3-Probe 3`, FlattenedEvaluationResults.`3-Probe 4`, FlattenedEvaluationResults.`3-Probe 5`, FlattenedEvaluationResults.`3-Probe 6`,

FlattenedEvaluationResults.`4-Date Tested`, FlattenedEvaluationResults.`4-Instrument Serial`, FlattenedEvaluationResults.`4-Instrument Last Calibrated`,
FlattenedEvaluationResults.`4-MTB`, FlattenedEvaluationResults.`4-Rif`, FlattenedEvaluationResults.`4-Probe 1`, FlattenedEvaluationResults.`4-Probe 2`,
FlattenedEvaluationResults.`4-Probe 3`, FlattenedEvaluationResults.`4-Probe 4`, FlattenedEvaluationResults.`4-Probe 5`, FlattenedEvaluationResults.`4-Probe 6`,

FlattenedEvaluationResults.`5-Date Tested`, FlattenedEvaluationResults.`5-Instrument Serial`, FlattenedEvaluationResults.`5-Instrument Last Calibrated`,
FlattenedEvaluationResults.`5-MTB`, FlattenedEvaluationResults.`5-Rif`, FlattenedEvaluationResults.`5-Probe 1`, FlattenedEvaluationResults.`5-Probe 2`,
FlattenedEvaluationResults.`5-Probe 3`, FlattenedEvaluationResults.`5-Probe 4`, FlattenedEvaluationResults.`5-Probe 5`, FlattenedEvaluationResults.`5-Probe 6`,

FlattenedEvaluationResults.`Comments`, FlattenedEvaluationResults.`Comments for reports`,
FlattenedEvaluationResults.`1-Score`, FlattenedEvaluationResults.`2-Score`, FlattenedEvaluationResults.`3-Score`, FlattenedEvaluationResults.`4-Score`,
FlattenedEvaluationResults.`5-Score`,

FlattenedEvaluationResults.`Fin Score`, FlattenedEvaluationResults.`Sat/Unsat`
FROM (
SELECT countries.iso_name AS `Country`,
participant.participant_id AS `Site No.`,
CONCAT(participant.lab_name,
COALESCE(CONCAT(' - ', CASE WHEN participant.state = '' THEN NULL ELSE participant.state END),
        CONCAT(' - ', CASE WHEN participant.city = '' THEN NULL ELSE participant.city END), '')) AS `Site Name/Location`,
participant.unique_identifier AS `PT-ID`,
CASE
    WHEN SUBSTRING(shipment_participant_map.evaluation_status,3,1) = '9' OR SUBSTRING(shipment_participant_map.evaluation_status,4,1) = '0' THEN 'No'
    WHEN SUBSTRING(shipment_participant_map.evaluation_status,3,1) = '1' AND SUBSTRING(shipment_participant_map.evaluation_status,4,1) = '1' THEN 'Yes'
    WHEN SUBSTRING(shipment_participant_map.evaluation_status,4,1) = '2' THEN 'Yes (Late)'
END AS Submitted,
CASE
    WHEN shipment_participant_map.is_excluded = 'yes' THEN 'Yes'
    ELSE 'No'
END AS `Submission Excluded`,
shipment_participant_map.shipment_receipt_date AS `Date PT Received`,
CAST(shipment_participant_map.shipment_test_report_date AS DATE) AS `Date PT Results Reported`,
CAST(attributes AS JSON) AS attributes_json,
r_tb_assay.name AS assay_name,
GREATEST(MAX(instrument.instrument_last_calibrated_on),
        response_result_tb_1.instrument_last_calibrated_on,
        response_result_tb_2.instrument_last_calibrated_on,
        response_result_tb_3.instrument_last_calibrated_on,
        response_result_tb_4.instrument_last_calibrated_on,
        response_result_tb_5.instrument_last_calibrated_on) AS `Date of last instrument calibration`,
CASE
WHEN IFNULL(shipment_participant_map.is_pt_test_not_performed, 'no') = 'no' THEN 'Yes'
ELSE 'No'
END AS `Participated`,
IFNULL(shipment_participant_map.pt_test_not_performed_comments, response_not_tested_reason.not_tested_reason) AS `Reason for No Submission`,

response_result_tb_1.date_tested AS `1-Date Tested`,
response_result_tb_1.instrument_serial AS `1-Instrument Serial`,
response_result_tb_1.instrument_last_calibrated_on AS `1-Instrument Last Calibrated`,
CASE
WHEN response_result_tb_1.error_code = 'error' THEN 'Error'
WHEN IFNULL(response_result_tb_1.error_code, '') != '' THEN CONCAT('Error ', response_result_tb_1.error_code)
WHEN response_result_tb_1.mtb_detected = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_1.mtb_detected = 'noResult' THEN 'No Result'
WHEN response_result_tb_1.mtb_detected = 'veryLow' THEN 'Very Low'
WHEN response_result_tb_1.mtb_detected = 'trace' THEN 'Trace'
WHEN response_result_tb_1.mtb_detected = 'na' THEN 'N/A'
WHEN IFNULL(response_result_tb_1.mtb_detected, '') = '' THEN NULL
ELSE CONCAT(UPPER(SUBSTRING(response_result_tb_1.mtb_detected, 1, 1)), SUBSTRING(response_result_tb_1.mtb_detected, 2, 254))
END AS `1-MTB`,
CASE
WHEN response_result_tb_1.error_code = 'error' THEN 'Error'
WHEN IFNULL(response_result_tb_1.error_code, '') != '' THEN CONCAT('Error ', response_result_tb_1.error_code)
WHEN response_result_tb_1.mtb_detected = 'noResult' THEN 'No Result'
WHEN response_result_tb_1.mtb_detected = 'invalid' THEN 'Invalid'
WHEN response_result_tb_1.mtb_detected = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_1.mtb_detected IN ('detected', 'veryLow', 'low', 'medium', 'high') AND IFNULL(response_result_tb_1.rif_resistance, 'na') = 'na' THEN 'Not Detected'
WHEN response_result_tb_1.rif_resistance = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_1.rif_resistance = 'noResult' THEN 'No Result'
WHEN response_result_tb_1.rif_resistance = 'veryLow' THEN 'Very Low'
WHEN response_result_tb_1.rif_resistance = 'na' THEN 'N/A'
WHEN response_result_tb_1.mtb_detected = 'notDetected' AND IFNULL(response_result_tb_1.rif_resistance, '') = '' THEN 'N/A'
WHEN response_result_tb_1.mtb_detected NOT IN ('noResult', 'notDetected', 'invalid') AND IFNULL(response_result_tb_1.rif_resistance, '') = '' THEN 'N/A'
ELSE CONCAT(UPPER(SUBSTRING(response_result_tb_1.rif_resistance, 1, 1)), SUBSTRING(response_result_tb_1.rif_resistance, 2, 254))
END AS `1-Rif`,
response_result_tb_1.probe_1 AS `1-Probe 1`,
response_result_tb_1.probe_2 AS `1-Probe 2`,
response_result_tb_1.probe_3 AS `1-Probe 3`,
response_result_tb_1.probe_4 AS `1-Probe 4`,
response_result_tb_1.probe_5 AS `1-Probe 5`,
response_result_tb_1.probe_6 AS `1-Probe 6`,

response_result_tb_2.date_tested AS `2-Date Tested`,
response_result_tb_2.instrument_serial AS `2-Instrument Serial`,
response_result_tb_2.instrument_last_calibrated_on AS `2-Instrument Last Calibrated`,
CASE
WHEN response_result_tb_2.error_code = 'error' THEN 'Error'
WHEN IFNULL(response_result_tb_2.error_code, '') != '' THEN CONCAT('Error ', response_result_tb_2.error_code)
WHEN response_result_tb_2.mtb_detected = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_2.mtb_detected = 'noResult' THEN 'No Result'
WHEN response_result_tb_2.mtb_detected = 'veryLow' THEN 'Very Low'
WHEN response_result_tb_2.mtb_detected = 'trace' THEN 'Trace'
WHEN response_result_tb_2.mtb_detected = 'na' THEN 'N/A'
WHEN IFNULL(response_result_tb_2.mtb_detected, '') = '' THEN NULL
ELSE CONCAT(UPPER(SUBSTRING(response_result_tb_2.mtb_detected, 1, 1)), SUBSTRING(response_result_tb_2.mtb_detected, 2, 254))
END AS `2-MTB`,
CASE
WHEN response_result_tb_2.error_code = 'error' THEN 'Error'
WHEN IFNULL(response_result_tb_2.error_code, '') != '' THEN CONCAT('Error ', response_result_tb_2.error_code)
WHEN response_result_tb_2.mtb_detected = 'noResult' THEN 'No Result'
WHEN response_result_tb_2.mtb_detected = 'invalid' THEN 'Invalid'
WHEN response_result_tb_2.mtb_detected = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_2.mtb_detected IN ('detected', 'veryLow', 'low', 'medium', 'high') AND IFNULL(response_result_tb_2.rif_resistance, 'na') = 'na' THEN 'Not Detected'
WHEN response_result_tb_2.rif_resistance = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_2.rif_resistance = 'noResult' THEN 'No Result'
WHEN response_result_tb_2.rif_resistance = 'veryLow' THEN 'Very Low'
WHEN response_result_tb_2.rif_resistance = 'na' THEN 'N/A'
WHEN response_result_tb_2.mtb_detected = 'notDetected' AND IFNULL(response_result_tb_2.rif_resistance, '') = '' THEN 'N/A'
WHEN response_result_tb_2.mtb_detected NOT IN ('noResult', 'notDetected', 'invalid') AND IFNULL(response_result_tb_2.rif_resistance, '') = '' THEN 'N/A'
ELSE CONCAT(UPPER(SUBSTRING(response_result_tb_2.rif_resistance, 1, 1)), SUBSTRING(response_result_tb_2.rif_resistance, 2, 254))
END AS `2-Rif`,
response_result_tb_2.probe_1 AS `2-Probe 1`,
response_result_tb_2.probe_2 AS `2-Probe 2`,
response_result_tb_2.probe_3 AS `2-Probe 3`,
response_result_tb_2.probe_4 AS `2-Probe 4`,
response_result_tb_2.probe_5 AS `2-Probe 5`,
response_result_tb_2.probe_6 AS `2-Probe 6`,

response_result_tb_3.date_tested AS `3-Date Tested`,
response_result_tb_3.instrument_serial AS `3-Instrument Serial`,
response_result_tb_3.instrument_last_calibrated_on AS `3-Instrument Last Calibrated`,
CASE
WHEN response_result_tb_3.error_code = 'error' THEN 'Error'
WHEN IFNULL(response_result_tb_3.error_code, '') != '' THEN CONCAT('Error ', response_result_tb_3.error_code)
WHEN response_result_tb_3.mtb_detected = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_3.mtb_detected = 'noResult' THEN 'No Result'
WHEN response_result_tb_3.mtb_detected = 'veryLow' THEN 'Very Low'
WHEN response_result_tb_3.mtb_detected = 'trace' THEN 'Trace'
WHEN response_result_tb_3.mtb_detected = 'na' THEN 'N/A'
WHEN IFNULL(response_result_tb_3.mtb_detected, '') = '' THEN NULL
ELSE CONCAT(UPPER(SUBSTRING(response_result_tb_3.mtb_detected, 1, 1)), SUBSTRING(response_result_tb_3.mtb_detected, 2, 254))
END AS `3-MTB`,
CASE
WHEN response_result_tb_3.error_code = 'error' THEN 'Error'
WHEN IFNULL(response_result_tb_3.error_code, '') != '' THEN CONCAT('Error ', response_result_tb_3.error_code)
WHEN response_result_tb_3.mtb_detected = 'noResult' THEN 'No Result'
WHEN response_result_tb_3.mtb_detected = 'invalid' THEN 'Invalid'
WHEN response_result_tb_3.mtb_detected = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_3.mtb_detected IN ('detected', 'veryLow', 'low', 'medium', 'high') AND IFNULL(response_result_tb_3.rif_resistance, 'na') = 'na' THEN 'Not Detected'
WHEN response_result_tb_3.rif_resistance = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_3.rif_resistance = 'noResult' THEN 'No Result'
WHEN response_result_tb_3.rif_resistance = 'veryLow' THEN 'Very Low'
WHEN response_result_tb_3.rif_resistance = 'na' THEN 'N/A'
WHEN response_result_tb_3.mtb_detected = 'notDetected' AND IFNULL(response_result_tb_3.rif_resistance, '') = '' THEN 'N/A'
WHEN response_result_tb_3.mtb_detected NOT IN ('noResult', 'notDetected', 'invalid') AND IFNULL(response_result_tb_3.rif_resistance, '') = '' THEN 'N/A'
ELSE CONCAT(UPPER(SUBSTRING(response_result_tb_3.rif_resistance, 1, 1)), SUBSTRING(response_result_tb_3.rif_resistance, 2, 254))
END AS `3-Rif`,
response_result_tb_3.probe_1 AS `3-Probe 1`,
response_result_tb_3.probe_2 AS `3-Probe 2`,
response_result_tb_3.probe_3 AS `3-Probe 3`,
response_result_tb_3.probe_4 AS `3-Probe 4`,
response_result_tb_3.probe_5 AS `3-Probe 5`,
response_result_tb_3.probe_6 AS `3-Probe 6`,

response_result_tb_4.date_tested AS `4-Date Tested`,
response_result_tb_4.instrument_serial AS `4-Instrument Serial`,
response_result_tb_4.instrument_last_calibrated_on AS `4-Instrument Last Calibrated`,
CASE
WHEN response_result_tb_4.error_code = 'error' THEN 'Error'
WHEN IFNULL(response_result_tb_4.error_code, '') != '' THEN CONCAT('Error ', response_result_tb_4.error_code)
WHEN response_result_tb_4.mtb_detected = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_4.mtb_detected = 'noResult' THEN 'No Result'
WHEN response_result_tb_4.mtb_detected = 'veryLow' THEN 'Very Low'
WHEN response_result_tb_4.mtb_detected = 'trace' THEN 'Trace'
WHEN response_result_tb_4.mtb_detected = 'na' THEN 'N/A'
WHEN IFNULL(response_result_tb_4.mtb_detected, '') = '' THEN NULL
ELSE CONCAT(UPPER(SUBSTRING(response_result_tb_4.mtb_detected, 1, 1)), SUBSTRING(response_result_tb_4.mtb_detected, 2, 254))
END AS `4-MTB`,
CASE
WHEN response_result_tb_4.error_code = 'error' THEN 'Error'
WHEN IFNULL(response_result_tb_4.error_code, '') != '' THEN CONCAT('Error ', response_result_tb_4.error_code)
WHEN response_result_tb_4.mtb_detected = 'noResult' THEN 'No Result'
WHEN response_result_tb_4.mtb_detected = 'invalid' THEN 'Invalid'
WHEN response_result_tb_4.mtb_detected = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_4.mtb_detected IN ('detected', 'veryLow', 'low', 'medium', 'high') AND IFNULL(response_result_tb_4.rif_resistance, 'na') = 'na' THEN 'Not Detected'
WHEN response_result_tb_4.rif_resistance = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_4.rif_resistance = 'noResult' THEN 'No Result'
WHEN response_result_tb_4.rif_resistance = 'veryLow' THEN 'Very Low'
WHEN response_result_tb_4.rif_resistance = 'na' THEN 'N/A'
WHEN response_result_tb_4.mtb_detected = 'notDetected' AND IFNULL(response_result_tb_4.rif_resistance, '') = '' THEN 'N/A'
WHEN response_result_tb_4.mtb_detected NOT IN ('noResult', 'notDetected', 'invalid') AND IFNULL(response_result_tb_4.rif_resistance, '') = '' THEN 'N/A'
ELSE CONCAT(UPPER(SUBSTRING(response_result_tb_4.rif_resistance, 1, 1)), SUBSTRING(response_result_tb_4.rif_resistance, 2, 254))
END AS `4-Rif`,
response_result_tb_4.probe_1 AS `4-Probe 1`,
response_result_tb_4.probe_2 AS `4-Probe 2`,
response_result_tb_4.probe_3 AS `4-Probe 3`,
response_result_tb_4.probe_4 AS `4-Probe 4`,
response_result_tb_4.probe_5 AS `4-Probe 5`,
response_result_tb_4.probe_6 AS `4-Probe 6`,

response_result_tb_5.date_tested AS `5-Date Tested`,
response_result_tb_5.instrument_serial AS `5-Instrument Serial`,
response_result_tb_5.instrument_last_calibrated_on AS `5-Instrument Last Calibrated`,
CASE
WHEN response_result_tb_5.error_code = 'error' THEN 'Error'
WHEN IFNULL(response_result_tb_5.error_code, '') != '' THEN CONCAT('Error ', response_result_tb_5.error_code)
WHEN response_result_tb_5.mtb_detected = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_5.mtb_detected = 'noResult' THEN 'No Result'
WHEN response_result_tb_5.mtb_detected = 'veryLow' THEN 'Very Low'
WHEN response_result_tb_5.mtb_detected = 'trace' THEN 'Trace'
WHEN response_result_tb_5.mtb_detected = 'na' THEN 'N/A'
WHEN IFNULL(response_result_tb_5.mtb_detected, '') = '' THEN NULL
ELSE CONCAT(UPPER(SUBSTRING(response_result_tb_5.mtb_detected, 1, 1)), SUBSTRING(response_result_tb_5.mtb_detected, 2, 254))
END AS `5-MTB`,
CASE
WHEN response_result_tb_5.error_code = 'error' THEN 'Error'
WHEN IFNULL(response_result_tb_5.error_code, '') != '' THEN CONCAT('Error ', response_result_tb_5.error_code)
WHEN response_result_tb_5.mtb_detected = 'noResult' THEN 'No Result'
WHEN response_result_tb_5.mtb_detected = 'invalid' THEN 'Invalid'
WHEN response_result_tb_5.mtb_detected = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_5.mtb_detected IN ('detected', 'veryLow', 'low', 'medium', 'high') AND IFNULL(response_result_tb_5.rif_resistance, 'na') = 'na' THEN 'Not Detected'
WHEN response_result_tb_5.rif_resistance = 'notDetected' THEN 'Not Detected'
WHEN response_result_tb_5.rif_resistance = 'noResult' THEN 'No Result'
WHEN response_result_tb_5.rif_resistance = 'veryLow' THEN 'Very Low'
WHEN response_result_tb_5.rif_resistance = 'na' THEN 'N/A'
WHEN response_result_tb_5.mtb_detected = 'notDetected' AND IFNULL(response_result_tb_5.rif_resistance, '') = '' THEN 'N/A'
WHEN response_result_tb_5.mtb_detected NOT IN ('noResult', 'notDetected', 'invalid') AND IFNULL(response_result_tb_5.rif_resistance, '') = '' THEN 'N/A'
ELSE CONCAT(UPPER(SUBSTRING(response_result_tb_5.rif_resistance, 1, 1)), SUBSTRING(response_result_tb_5.rif_resistance, 2, 254))
END AS `5-Rif`,
response_result_tb_5.probe_1 AS `5-Probe 1`,
response_result_tb_5.probe_2 AS `5-Probe 2`,
response_result_tb_5.probe_3 AS `5-Probe 3`,
response_result_tb_5.probe_4 AS `5-Probe 4`,
response_result_tb_5.probe_5 AS `5-Probe 5`,
response_result_tb_5.probe_6 AS `5-Probe 6`,

TRIM(shipment_participant_map.user_comment) AS `Comments`,
TRIM(COALESCE(CASE WHEN r_evaluation_comments.`comment` = '' THEN NULL ELSE r_evaluation_comments.`comment` END, shipment_participant_map.optional_eval_comment)) AS `Comments for reports`,

CASE
WHEN response_result_tb_1.calculated_score IN ('pass', 'concern', 'exempt') THEN 20
WHEN response_result_tb_1.calculated_score = 'partial' THEN 10
WHEN response_result_tb_1.calculated_score = 'noresult' THEN 5
WHEN response_result_tb_1.calculated_score IN ('fail', 'excluded') THEN 0
ELSE 0
END AS `1-Score`,
CASE
WHEN response_result_tb_2.calculated_score IN ('pass', 'concern', 'exempt') THEN 20
WHEN response_result_tb_2.calculated_score = 'partial' THEN 10
WHEN response_result_tb_2.calculated_score = 'noresult' THEN 5
WHEN response_result_tb_2.calculated_score IN ('fail', 'excluded') THEN 0
ELSE 0
END AS `2-Score`,
CASE
WHEN response_result_tb_3.calculated_score IN ('pass', 'concern', 'exempt') THEN 20
WHEN response_result_tb_3.calculated_score = 'partial' THEN 10
WHEN response_result_tb_3.calculated_score = 'noresult' THEN 5
WHEN response_result_tb_3.calculated_score IN ('fail', 'excluded') THEN 0
ELSE 0
END AS `3-Score`,
CASE
WHEN response_result_tb_4.calculated_score IN ('pass', 'concern', 'exempt') THEN 20
WHEN response_result_tb_4.calculated_score = 'partial' THEN 10
WHEN response_result_tb_4.calculated_score = 'noresult' THEN 5
WHEN response_result_tb_4.calculated_score IN ('fail', 'excluded') THEN 0
ELSE 0
END AS `4-Score`,
CASE
WHEN response_result_tb_5.calculated_score IN ('pass', 'concern', 'exempt') THEN 20
WHEN response_result_tb_5.calculated_score = 'partial' THEN 10
WHEN response_result_tb_5.calculated_score = 'noresult' THEN 5
WHEN response_result_tb_5.calculated_score IN ('fail', 'excluded') THEN 0
ELSE 0
END AS `5-Score`,

IFNULL(shipment_participant_map.documentation_score, 0) + IFNULL(shipment_participant_map.shipment_score, 0) AS `Fin Score`,
CASE
WHEN r_results.result_name = 'Pass' THEN 'Satisfactory'
ELSE 'Unsatisfactory'
END AS `Sat/Unsat`
FROM shipment
JOIN shipment_participant_map ON shipment_participant_map.shipment_id = shipment.shipment_id
JOIN participant ON participant.participant_id = shipment_participant_map.participant_id
JOIN countries ON countries.id = participant.country
LEFT JOIN instrument ON instrument.participant_id = shipment_participant_map.participant_id
LEFT JOIN response_not_tested_reason ON response_not_tested_reason.not_tested_reason_id = shipment_participant_map.not_tested_reason
LEFT JOIN r_evaluation_comments ON r_evaluation_comments.comment_id = shipment_participant_map.evaluation_comment
LEFT JOIN r_results ON r_results.result_id = shipment_participant_map.final_result
LEFT JOIN r_tb_assay ON r_tb_assay.id = JSON_UNQUOTE(JSON_EXTRACT(shipment_participant_map.attributes, "$.assay"))
LEFT JOIN response_result_tb AS response_result_tb_1 ON response_result_tb_1.shipment_map_id = shipment_participant_map.map_id AND response_result_tb_1.sample_id = '1'
LEFT JOIN response_result_tb AS response_result_tb_2 ON response_result_tb_2.shipment_map_id = shipment_participant_map.map_id AND response_result_tb_2.sample_id = '2'
LEFT JOIN response_result_tb AS response_result_tb_3 ON response_result_tb_3.shipment_map_id = shipment_participant_map.map_id AND response_result_tb_3.sample_id = '3'
LEFT JOIN response_result_tb AS response_result_tb_4 ON response_result_tb_4.shipment_map_id = shipment_participant_map.map_id AND response_result_tb_4.sample_id = '4'
LEFT JOIN response_result_tb AS response_result_tb_5 ON response_result_tb_5.shipment_map_id = shipment_participant_map.map_id AND response_result_tb_5.sample_id = '5'
WHERE shipment.shipment_id = ?
GROUP BY shipment_participant_map.map_id) AS FlattenedEvaluationResults
ORDER BY FlattenedEvaluationResults.`PT-ID` * 1 ASC;
