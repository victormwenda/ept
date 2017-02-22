<?php

class Application_Model_DbTable_ResponseTb extends Zend_Db_Table_Abstract {
    protected $_name = 'response_result_tb';
    protected $_primary = array('shipment_map_id', 'sample_id');

    public function updateResults($params) {
        $sampleIds = $params['sampleId'];
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        $dataManagerId = $authNameSpace->dm_id;
        foreach ($sampleIds as $key => $sampleId) {
            $res = $this->fetchRow("shipment_map_id = " . $params['smid'] . " and sample_id = " . $sampleId);
            $instrumentInstalledOn = Pt_Commons_General::dateFormat($params['instrumentInstalledOn'][$key]);
            if (!isset($params['instrumentInstalledOn'][$key]) ||
                $params['instrumentInstalledOn'][$key] == "") {
                $instrumentInstalledOn = null;
            }
            $instrumentLastCalibratedOn = Pt_Commons_General::dateFormat($params['instrumentLastCalibratedOn'][$key]);
            if (!isset($params['instrumentLastCalibratedOn'][$key]) ||
                $params['instrumentLastCalibratedOn'][$key] == "") {
                $instrumentLastCalibratedOn = null;
            }
            if ($res == null || count($res) == 0) {
                $this->insert(array(
                    'shipment_map_id' => $params['smid'],
                    'sample_id' => $sampleId,
                    'date_tested' => Pt_Commons_General::dateFormat($params['dateTested'][$key]),
                    'mtb_detected' => $params['mtbDetected'][$key],
                    'rif_resistance' => $params['rifResistance'][$key],
                    'probe_d' => $params['probeD'][$key],
                    'probe_c' => $params['probeC'][$key],
                    'probe_e' => $params['probeE'][$key],
                    'probe_b' => $params['probeB'][$key],
                    'spc' => $params['spc'][$key],
                    'probe_a' => $params['probeA'][$key],
                    'instrument_serial' => $params['instrumentSerial'][$key],
                    'instrument_installed_on' => $instrumentInstalledOn,
                    'instrument_last_calibrated_on' => $instrumentLastCalibratedOn,
                    'module_name' => $params['moduleName'][$key],
                    'instrument_user' => $params['instrumentUser'][$key],
                    'cartridge_expiration_date' => Pt_Commons_General::dateFormat($params['cartridgeExpirationDate'][$key]),
                    'reagent_lot_id' => $params['reagentLotId'][$key],
                    'error_code' => $params['errorCode'][$key],
                    'created_by' => $dataManagerId,
                    'created_on' => new Zend_Db_Expr('now()')
                ));
            } else {
                $this->update(array(
                    'shipment_map_id' => $params['smid'],
                    'sample_id' => $sampleId,
                    'date_tested' => Pt_Commons_General::dateFormat($params['dateTested'][$key]),
                    'mtb_detected' => $params['mtbDetected'][$key],
                    'rif_resistance' => $params['rifResistance'][$key],
                    'probe_d' => $params['probeD'][$key],
                    'probe_c' => $params['probeC'][$key],
                    'probe_e' => $params['probeE'][$key],
                    'probe_b' => $params['probeB'][$key],
                    'spc' => $params['spc'][$key],
                    'probe_a' => $params['probeA'][$key],
                    'instrument_serial' => $params['instrumentSerial'][$key],
                    'instrument_installed_on' => $instrumentInstalledOn,
                    'instrument_last_calibrated_on' => $instrumentLastCalibratedOn,
                    'module_name' => $params['moduleName'][$key],
                    'instrument_user' => $params['instrumentUser'][$key],
                    'cartridge_expiration_date' => Pt_Commons_General::dateFormat($params['cartridgeExpirationDate'][$key]),
                    'reagent_lot_id' => $params['reagentLotId'][$key],
                    'error_code' => $params['errorCode'][$key],
                    'updated_by' => $dataManagerId,
                    'updated_on' => new Zend_Db_Expr('now()')
                ), "shipment_map_id = " . $params['smid'] . " and sample_id = " . $sampleId);
            }
        }
    }

    public function updateResult($params) {
        $sampleId = $params['sampleId'];
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        $dataManagerId = $authNameSpace->dm_id;
        $res = $this->fetchRow("shipment_map_id = " . $params['smid'] . " and sample_id = " . $sampleId);
        $instrumentInstalledOn = Pt_Commons_General::dateFormatOrNull($params['instrumentInstalledOn']);
        if (!isset($params['instrumentInstalledOn']) ||
            $params['instrumentInstalledOn'] == "") {
            $instrumentInstalledOn = null;
        }
        $instrumentLastCalibratedOn = Pt_Commons_General::dateFormatOrNull($params['instrumentLastCalibratedOn']);
        if (!isset($params['instrumentLastCalibratedOn']) ||
            $params['instrumentLastCalibratedOn'] == "") {
            $instrumentLastCalibratedOn = null;
        }
        if ($res == null || count($res) == 0) {
            $this->insert(array(
                'shipment_map_id' => $params['smid'],
                'sample_id' => $sampleId,
                'date_tested' => Pt_Commons_General::dateFormatOrNull($params['dateTested']),
                'mtb_detected' => $params['mtbDetected'],
                'rif_resistance' => $params['rifResistance'],
                'probe_d' => $params['probeD'],
                'probe_c' => $params['probeC'],
                'probe_e' => $params['probeE'],
                'probe_b' => $params['probeB'],
                'spc' => $params['spc'],
                'probe_a' => $params['probeA'],
                'instrument_serial' => $params['instrumentSerial'],
                'instrument_installed_on' => $instrumentInstalledOn,
                'instrument_last_calibrated_on' => $instrumentLastCalibratedOn,
                'module_name' => $params['moduleName'],
                'instrument_user' => $params['instrumentUser'],
                'cartridge_expiration_date' => Pt_Commons_General::dateFormatOrNull($params['cartridgeExpirationDate']),
                'reagent_lot_id' => $params['reagentLotId'],
                'error_code' => $params['errorCode'],
                'created_by' => $dataManagerId,
                'created_on' => new Zend_Db_Expr('now()')
            ));
        } else {
            $this->update(array(
                'shipment_map_id' => $params['smid'],
                'sample_id' => $sampleId,
                'date_tested' => Pt_Commons_General::dateFormatOrNull($params['dateTested']),
                'mtb_detected' => $params['mtbDetected'],
                'rif_resistance' => $params['rifResistance'],
                'probe_d' => $params['probeD'],
                'probe_c' => $params['probeC'],
                'probe_e' => $params['probeE'],
                'probe_b' => $params['probeB'],
                'spc' => $params['spc'],
                'probe_a' => $params['probeA'],
                'instrument_serial' => $params['instrumentSerial'],
                'instrument_installed_on' => $instrumentInstalledOn,
                'instrument_last_calibrated_on' => $instrumentLastCalibratedOn,
                'module_name' => $params['moduleName'],
                'instrument_user' => $params['instrumentUser'],
                'cartridge_expiration_date' => Pt_Commons_General::dateFormatOrNull($params['cartridgeExpirationDate']),
                'reagent_lot_id' => $params['reagentLotId'],
                'error_code' => $params['errorCode'],
                'updated_by' => $dataManagerId,
                'updated_on' => new Zend_Db_Expr('now()')
            ), "shipment_map_id = " . $params['smid'] . " and sample_id = " . $sampleId);
        }
    }
}
