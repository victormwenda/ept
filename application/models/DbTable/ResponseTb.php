<?php

class Application_Model_DbTable_ResponseTb extends Zend_Db_Table_Abstract {
    protected $_name = 'response_result_tb';
    protected $_primary = array('shipment_map_id', 'sample_id');

    public function updateResults($params, $submitted) {
        $sampleIds = $params['sampleId'];
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        $dataManagerId = $authNameSpace->dm_id;

        $headerInstrumentSerials = $params['headerInstrumentSerial'];
        $instrumentDetails = array();
        foreach ($headerInstrumentSerials as $key => $headerInstrumentSerial) {
            if (isset($headerInstrumentSerial) &&
                $headerInstrumentSerial != "") {
                $instrumentDetails[$headerInstrumentSerial] = array(
                    'instrument_installed_on' => $params['headerInstrumentInstalledOn'][$key],
                    'instrument_last_calibrated_on' => $params['headerInstrumentLastCalibratedOn'][$key]
                );
            }
        }
        if ($params['ableToEnterResults'] == "no" && $submitted) {
            $this->delete("shipment_map_id = " . $params['smid']);
        } else {
            foreach ($sampleIds as $key => $sampleId) {
                $res = $this->fetchRow("shipment_map_id = " . $params['smid'] . " and sample_id = " . $sampleId);

                $dateTested = Application_Service_Common::ParseDate($params['dateTested'][$key]);
                $instrumentInstalledOn = null;
                $instrumentLastCalibratedOn = null;
                if (isset($params['instrumentSerial'][$key]) &&
                    isset($instrumentDetails[$params['instrumentSerial'][$key]])) {
                    if (isset($instrumentDetails[$params['instrumentSerial'][$key]]['instrument_installed_on'])) {
                        $instrumentInstalledOn = Application_Service_Common::ParseDate(
                            $instrumentDetails[$params['instrumentSerial'][$key]]['instrument_installed_on']
                        );
                    }
                    if (isset($instrumentDetails[$params['instrumentSerial'][$key]]['instrument_last_calibrated_on'])) {
                        $instrumentLastCalibratedOn = Application_Service_Common::ParseDate(
                            $instrumentDetails[$params['instrumentSerial'][$key]]['instrument_last_calibrated_on']
                        );
                    }
                }
                $cartridgeExpirationDate = Application_Service_Common::ParseDate($params['expiryDate']);
                if ($res == null || count($res) == 0) {
                    $this->insert(array(
                        'shipment_map_id' => $params['smid'],
                        'sample_id' => $sampleId,
                        'date_tested' => $dateTested,
                        'mtb_detected' => $params['mtbDetected'][$key],
                        'rif_resistance' => $params['rifResistance'][$key],
                        'probe_1' => $params['probe1'][$key],
                        'probe_2' => $params['probe2'][$key],
                        'probe_3' => $params['probe3'][$key],
                        'probe_4' => $params['probe4'][$key],
                        'probe_5' => $params['probe5'][$key],
                        'probe_6' => $params['probe6'][$key],
                        'instrument_serial' => $params['instrumentSerial'][$key],
                        'instrument_installed_on' => $instrumentInstalledOn,
                        'instrument_last_calibrated_on' => $instrumentLastCalibratedOn,
                        'module_name' => $params['moduleName'][$key],
                        'instrument_user' => $params['instrumentUser'][$key],
                        'cartridge_expiration_date' => $cartridgeExpirationDate,
                        'reagent_lot_id' => $params['mtbRifKitLotNo'],
                        'error_code' => $params['errorCode'][$key],
                        'created_by' => $dataManagerId,
                        'created_on' => new Zend_Db_Expr('now()')
                    ));
                } else {
                    $this->update(array(
                        'shipment_map_id' => $params['smid'],
                        'sample_id' => $sampleId,
                        'date_tested' => $dateTested,
                        'mtb_detected' => $params['mtbDetected'][$key],
                        'rif_resistance' => $params['rifResistance'][$key],
                        'probe_1' => $params['probe1'][$key],
                        'probe_2' => $params['probe2'][$key],
                        'probe_3' => $params['probe3'][$key],
                        'probe_4' => $params['probe4'][$key],
                        'probe_5' => $params['probe5'][$key],
                        'probe_6' => $params['probe6'][$key],
                        'instrument_serial' => $params['instrumentSerial'][$key],
                        'instrument_installed_on' => $instrumentInstalledOn,
                        'instrument_last_calibrated_on' => $instrumentLastCalibratedOn,
                        'module_name' => $params['moduleName'][$key],
                        'instrument_user' => $params['instrumentUser'][$key],
                        'cartridge_expiration_date' => $cartridgeExpirationDate,
                        'reagent_lot_id' => $params['mtbRifKitLotNo'],
                        'error_code' => $params['errorCode'][$key],
                        'updated_by' => $dataManagerId,
                        'updated_on' => new Zend_Db_Expr('now()')
                    ), "shipment_map_id = " . $params['smid'] . " and sample_id = " . $sampleId);
                }
            }
        }
    }

    public function updateResult($params, $cartridgeExpirationDate, $cartridgeLotNo) {
        $sampleId = $params['sampleId'];
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        $dataManagerId = $authNameSpace->dm_id;
        $res = $this->fetchRow("shipment_map_id = " . $params['smid'] . " and sample_id = " . $sampleId);

        $dateTested = Application_Service_Common::ParseDate($params['dateTested']);
        $instrumentInstalledOn = Application_Service_Common::ParseDbDate($params['instrumentInstalledOn']);
        $instrumentLastCalibratedOn = Application_Service_Common::ParseDbDate($params['instrumentLastCalibratedOn']);
        if ($res == null || count($res) == 0) {
            $responseResult = array(
                'shipment_map_id' => $params['smid'],
                'sample_id' => $sampleId,
                'date_tested' => $dateTested,
                'mtb_detected' => $params['mtbDetected'],
                'rif_resistance' => $params['rifResistance'],
                'probe_1' => $params['probe1'],
                'probe_2' => $params['probe2'],
                'probe_3' => $params['probe3'],
                'probe_4' => $params['probe4'],
                'probe_5' => $params['probe5'],
                'probe_6' => $params['probe6'],
                'instrument_serial' => $params['instrumentSerial'],
                'instrument_installed_on' => $instrumentInstalledOn,
                'instrument_last_calibrated_on' => $instrumentLastCalibratedOn,
                'module_name' => $params['moduleName'],
                'instrument_user' => $params['instrumentUser'],
                'error_code' => $params['errorCode'],
                'created_by' => $dataManagerId,
                'created_on' => new Zend_Db_Expr('now()')
            );
            if (isset($cartridgeExpirationDate)) {
                $responseResult['cartridge_expiration_date'] = $cartridgeExpirationDate;
            }
            if (isset($cartridgeLotNo)) {
                $responseResult['reagent_lot_id'] = $cartridgeLotNo;
            }
            $this->insert($responseResult);
        } else {
            $responseResult = array(
                'shipment_map_id' => $params['smid'],
                'sample_id' => $sampleId,
                'date_tested' => $dateTested,
                'mtb_detected' => $params['mtbDetected'],
                'rif_resistance' => $params['rifResistance'],
                'probe_1' => $params['probe1'],
                'probe_2' => $params['probe2'],
                'probe_3' => $params['probe3'],
                'probe_4' => $params['probe4'],
                'probe_5' => $params['probe5'],
                'probe_6' => $params['probe6'],
                'instrument_serial' => $params['instrumentSerial'],
                'instrument_installed_on' => $instrumentInstalledOn,
                'instrument_last_calibrated_on' => $instrumentLastCalibratedOn,
                'module_name' => $params['moduleName'],
                'instrument_user' => $params['instrumentUser'],
                'error_code' => $params['errorCode'],
                'updated_by' => $dataManagerId,
                'updated_on' => new Zend_Db_Expr('now()')
            );
            if (isset($cartridgeExpirationDate)) {
                $responseResult['cartridge_expiration_date'] = $cartridgeExpirationDate;
            }
            if (isset($cartridgeLotNo)) {
                $responseResult['reagent_lot_id'] = $cartridgeLotNo;
            }
            $this->update($responseResult,
                "shipment_map_id = " . $params['smid'] . " and sample_id = " . $sampleId);
        }
    }
}
