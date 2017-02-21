<?php

class Api_ResultsController extends Zend_Controller_Action {
    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')
            ->initContext();
        $this->_helper->layout()->setLayout('api');
    }

    public function resultHeaderAction() {
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        if (!isset($authNameSpace->dm_id)) {
            $this->getResponse()->setHttpResponseCode(401);
            Zend_Session::namespaceUnset('datamanagers');
        } else {
            $sID = intval($this->getRequest()->getParam('sid'));
            $pID = intval($this->getRequest()->getParam('pid'));
            $schemeService = new Application_Service_Schemes();
            if ($this->getRequest()->isPut()) {
                $params = Zend_Json::decode($this->getRequest()->getRawBody());
                $params['shipmentId'] = $sID;
                $params['participantId'] = $pID;
                $shipmentService = new Application_Service_Shipments();
                $shipmentService->updateTbResultHeader($params);
                $this->getResponse()->setBody('OK');
                $this->getResponse()->setHttpResponseCode(200);
            } else {
                $shipment = $schemeService->getShipmentData($sID, $pID);
                $shipment['attributes'] = json_decode($shipment['attributes'],true);
                $assays = $schemeService->getTbAssayReferenceMap();
                $sampleIds = $schemeService->getTbSampleIds($sID, $pID);
                $commonService = new Application_Service_Common();
                $modesOfReceipt = $commonService->getAllModeOfReceiptReferenceMap();

                $response = array(
                    'sampleRehydrationDate' => $this->formatDate($shipment['attributes']['sample_rehydration_date']),
	                'testDate' => $this->formatDate($shipment['shipment_test_date']),
                    'mtbRifKitLotNo' => $shipment['attributes']['mtb_rif_kit_lot_no'],
	                'expiryDate' => $this->formatDate($shipment['attributes']['expiry_date']),
	                'testReceiptDate' => $this->formatDate($shipment['shipment_test_report_date']),
	                'modeOfReceipt' => $shipment['mode_id'],
	                'assay' => $shipment['attributes']['assay'],
	                'countTestsConductedOverMonth' => $shipment['attributes']['count_tests_conducted_over_month'],
	                'countErrorsEncounteredOverMonth' => $shipment['attributes']['count_errors_encountered_over_month'],
	                'errorCodesEncounteredOverMonth' => $shipment['attributes']['error_codes_encountered_over_month'],
	                'qcDone' => $shipment['qc_done'],
	                'qcDate' => $this->formatDate($shipment['qc_date']),
	                'qcDoneBy' => $shipment['qc_done_by'],
                    'dateReceived' => $this->formatDate($shipment['shipment_receipt_date']),
	                'smid' => $shipment['map_id'],
                    'assays' => $assays,
                    'modesOfReceipt' => $modesOfReceipt,
                    'sampleIds' => $sampleIds
                );

                echo json_encode($response);
            }
        }
    }

    public function resultItemAction() {
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        if (!isset($authNameSpace->dm_id)) {
            $this->getResponse()->setHttpResponseCode(401);
            Zend_Session::namespaceUnset('datamanagers');
        } else {
            $sID = intval($this->getRequest()->getParam('sid'));
            $pID = intval($this->getRequest()->getParam('pid'));
            $sampleID = intval($this->getRequest()->getParam('id'));
            $schemeService = new Application_Service_Schemes();
            if ($this->getRequest()->isPut()) {
                $params = Zend_Json::decode($this->getRequest()->getRawBody());
                $params['shipmentId'] = $sID;
                $params['participantId'] = $pID;
                $params['sampleId'] = $sampleID;
                $shipmentService = new Application_Service_Shipments();
                $shipmentService->updateTbResult($params);
                $this->getResponse()->setBody('OK');
                $this->getResponse()->setHttpResponseCode(200);
            } else {
                $sample = $schemeService->getTbSample($sID,$pID,$sampleID);
                $instrumentDb = new Application_Model_DbTable_Instruments();
                $instruments = $instrumentDb->getInstrumentsReferenceMap($pID);
                $response = array(
                    'sampleId' => $sample['sample_id'],
                    'sampleLabel' => $sample['sample_label'],
                    'instrumentSerial' => $sample['res_instrument_serial'],
                    'instrumentInstalledOn' => $this->formatDate($sample['res_instrument_installed_on']),
                    'instrumentLastCalibratedOn' => $this->formatDate($sample['res_instrument_last_calibrated_on']),
                    'dateTested' => $this->formatDate($sample['res_date_tested']),
                    'mtbDetected' => $sample['res_mtb_detected'],
                    'rifResistance' => $sample['res_rif_resistance'],
                    'probeD' => $sample['res_probe_d'],
                    'probeC' => $sample['res_probe_c'],
                    'probeE' => $sample['res_probe_e'],
                    'probeB' => $sample['res_probe_b'],
                    'spc' => $sample['res_spc'],
                    'probeA' => $sample['res_probe_a'],
                    'moduleName' => $sample['res_reagent_lot_id'],
                    'instrumentUser' => $sample['res_instrument_user'],
                    'cartridgeExpirationDate' => $this->formatDate($sample['res_cartridge_expiration_date']),
                    'reagentLotId' => $sample['res_reagent_lot_id'],
                    'errorCode' => $sample['res_error_code'],
                    'smid' => $sample['map_id'],
                    'instruments' => $instruments
                );

                echo json_encode($response);
            }
        }
    }

    public function resultFooterAction() {
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        if (!isset($authNameSpace->dm_id)) {
            $this->getResponse()->setHttpResponseCode(401);
            Zend_Session::namespaceUnset('datamanagers');
        } else {
            $sID = intval($this->getRequest()->getParam('sid'));
            $pID = intval($this->getRequest()->getParam('pid'));
            $schemeService = new Application_Service_Schemes();
            if ($this->getRequest()->isPut()) {
                $params = Zend_Json::decode($this->getRequest()->getRawBody());
                $params['shipmentId'] = $sID;
                $params['participantId'] = $pID;
                $shipmentService = new Application_Service_Shipments();
                $shipmentService->updateTbResultFooter($params);
                $this->getResponse()->setBody('OK');
                $this->getResponse()->setHttpResponseCode(200);
            } else {
                $shipment = $schemeService->getShipmentData($sID,$pID);
                $response = array(
                    'supervisorApproval' => $shipment['supervisor_approval'],
                    'participantSupervisor' => $shipment['participant_supervisor'],
                    'userComments' => $shipment['user_comment'],
                    'testReceiptDate' => $this->formatDate($shipment['shipment_test_report_date']),
                    'dateReceived' => $this->formatDate($shipment['shipment_receipt_date']),
                    'smid' => $shipment['map_id']
                );

                echo json_encode($response);
            }
        }
    }

    private function formatDate($dateVal) {
        if (isset($dateVal) && $dateVal != "") {
            return Pt_Commons_General::dateFormat($dateVal);
        }
        return null;
    }
}



