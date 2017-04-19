<?php

class Application_Service_Response {
    public function getAllDistributions($parameters) {
        $aColumns = array("DATE_FORMAT(distribution_date,'%d-%b-%Y')", 'distribution_code', 's.shipment_code', 'd.status');
        $orderColumns = array('distribution_date', 'distribution_code', 's.shipment_code', 'd.status');
        /* Indexed column (used for fast and accurate table cardinality) */
        $sIndexColumn = 'distribution_id';

        /*
         * Paging
         */
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }

        /*
         * Ordering
         */
        $sOrder = "";
        if (isset($parameters['iSortCol_0'])) {
            $sOrder = "";
            for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
                if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                    $sOrder .= $orderColumns[intval($parameters['iSortCol_' . $i])] . "
				 	" . ($parameters['sSortDir_' . $i]) . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
        }

        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
            $searchArray = explode(" ", $parameters['sSearch']);
            $sWhereSub = "";
            foreach ($searchArray as $search) {
                if ($sWhereSub == "") {
                    $sWhereSub .= "(";
                } else {
                    $sWhereSub .= " AND (";
                }
                $colSize = count($aColumns);

                for ($i = 0; $i < $colSize; $i++) {
                    if ($aColumns[$i] == "" || $aColumns[$i] == null) {
                        continue;
                    }
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                } else {
                    $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                }
            }
        }

        /*
         * SQL queries
         * Get data to display
         */
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sQuery = $dbAdapter->select()->from(array('d' => 'distributions'))
                ->joinLeft(array('s' => 'shipment'), 's.distribution_id=d.distribution_id', array('shipments' => new Zend_Db_Expr("GROUP_CONCAT(DISTINCT s.shipment_code SEPARATOR ', ')"),'not_finalized_count' => new Zend_Db_Expr("SUM(IF(s.status!='finalized',1,0))")))
                ->where("d.status='shipped'")
                ->group('d.distribution_id');

        if (isset($sWhere) && $sWhere != "") {
            $sQuery = $sQuery->where($sWhere);
        }
        if (isset($sOrder) && $sOrder != "") {
            $sQuery = $sQuery->order($sOrder);
        }
        if (isset($sLimit) && isset($sOffset)) {
            $sQuery = $sQuery->limit($sLimit, $sOffset);
        }
		$sQuery = $dbAdapter->select()->from(array('temp' => $sQuery))->where("not_finalized_count > 0");
        $rResult = $dbAdapter->fetchAll($sQuery);

        /* Data set length after filtering */
        $sQuery = $sQuery->reset(Zend_Db_Select::LIMIT_COUNT);
        $sQuery = $sQuery->reset(Zend_Db_Select::LIMIT_OFFSET);
        $aResultFilterTotal = $dbAdapter->fetchAll($sQuery);
        $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
        $aResultTotal = $dbAdapter->fetchAll($sQuery);
        $iTotal = count($aResultTotal);

        /*
         * Output
         */
        $output = array(
            "sEcho" => isset($parameters['sEcho']) ? intval($parameters['sEcho']) : 0,
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        $shipmentDb = new Application_Model_DbTable_Shipments();

        foreach ($rResult as $aRow) {
            $shipmentResults = $shipmentDb->getPendingShipmentsByDistribution($aRow['distribution_id']);
            $row = array();
            $row['DT_RowId'] = "dist" . $aRow['distribution_id'];
            $row[] = Pt_Commons_General::humanDateFormat($aRow['distribution_date']);
            $row[] = $aRow['distribution_code'];
            $row[] = $aRow['shipments'];
            $row[] = ucwords($aRow['status']);
            $row[] = '<a class="btn btn-primary btn-xs" href="javascript:void(0);" onclick="getShipments(\'' . ($aRow['distribution_id']) . '\')"><span><i class="icon-search"></i> View</span></a>';
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function getShipments($distributionId) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->select()->from(array('s' => 'shipment'))
                ->join(array('d' => 'distributions'), 'd.distribution_id=s.distribution_id')
                ->join(array('sp' => 'shipment_participant_map'), 'sp.shipment_id=s.shipment_id', array(
                    'map_id',
                    'responseDate' => 'shipment_test_report_date',
                    'participant_count' => new Zend_Db_Expr('count("participant_id")'),
                    'reported_count' => new Zend_Db_Expr("SUM(substr(sp.evaluation_status,3,1) = 1)"),
                    'number_passed' => new Zend_Db_Expr("SUM(final_result = 1)"),
                    'last_not_participated_mailed_on',
                    'last_not_participated_mail_count',
                    'shipment_status' => 's.status'))
                ->join(array('sl' => 'scheme_list'), 'sl.scheme_id=s.scheme_type')
                ->joinLeft(array('rr' => 'r_results'), 'sp.final_result=rr.result_id')
                ->where("s.distribution_id = ?", $distributionId)
                ->group('s.shipment_id');
        return $db->fetchAll($sql);
    }
    
     public function getResponseCount($shipmentId,$distributionId) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->select()->from(array('s' => 'shipment'),array(''))
                ->join(array('d' => 'distributions'), 'd.distribution_id=s.distribution_id',array(''))
                ->join(array('sp' => 'shipment_participant_map'), 'sp.shipment_id=s.shipment_id', array(
                    'reported_count' => new Zend_Db_Expr("COUNT(CASE substr(sp.evaluation_status,4,1) WHEN '1' THEN 1 WHEN '2' THEN 1 END)")))
                ->join(array('sl' => 'scheme_list'), 'sl.scheme_id=s.scheme_type',array(''))
                ->joinLeft(array('rr' => 'r_results'), 'sp.final_result=rr.result_id',array(''))
		        ->where("s.shipment_id = ?", $shipmentId)
                ->where("s.distribution_id = ?", $distributionId)
                ->group('s.shipment_id');
        return $db->fetchRow($sql);
    }

    public function getShipmentToEdit($shipmentId) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->select()->from(array('s' => 'shipment'), array('s.shipment_id', 's.shipment_code', 's.scheme_type', 's.shipment_date', 's.lastdate_response', 's.distribution_id', 's.number_of_samples', 's.max_score', 's.shipment_comment', 's.created_by_admin', 's.created_on_admin', 's.updated_by_admin', 's.updated_on_admin', 'shipment_status' => 's.status'))
                ->join(array('d' => 'distributions'), 'd.distribution_id=s.distribution_id')
                ->join(array('sp' => 'shipment_participant_map'), 'sp.shipment_id=s.shipment_id')
                ->join(array('sl' => 'scheme_list'), 'sl.scheme_id=s.scheme_type')
                ->join(array('p' => 'participant'), 'p.participant_id=sp.participant_id')
                ->where("s.shipment_id = ?", $shipmentId);
        $shipmentResult = $db->fetchAll($sql);
        return $shipmentResult;
    }

    public function editResponse($shipmentId, $participantId, $scheme) {
        $participantService = new Application_Service_Participants();
        $schemeService = new Application_Service_Schemes();
        $participantData = $participantService->getParticipantDetails($participantId);
        $shipmentData = $schemeService->getShipmentData($shipmentId, $participantId);
        $possibleResults = $schemeService->getPossibleResults($scheme);
        if ($scheme == 'tb') {
            $results = $schemeService->getTbSamples($shipmentId, $participantId);
        }

        return array('participant' => $participantData,
            'shipment' => $shipmentData,
            'possibleResults' => $possibleResults,
            'results' => $results
        );
    }

    public function updateShipmentResults($params) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $authNameSpace = new Zend_Session_Namespace('administrators');
        $admin = $authNameSpace->primary_email;
        $size = count($params['sampleId']);

        if ($params['scheme'] == 'tb') {
            $attributes = array(
                "mtb_rif_kit_lot_no" => $params['mtbRifKitLotNo'],
                "expiry_date" => $params['expiryDate'],
                "assay" => $params['assay'],
                "count_tests_conducted_over_month" => $params['countTestsConductedOverMonth'],
                "count_errors_encountered_over_month" => $params['countErrorsEncounteredOverMonth'],
                "error_codes_encountered_over_month" => $params['errorCodesEncounteredOverMonth']
            );
            $attributes = json_encode($attributes);
            $mapData = array(
                "shipment_receipt_date" => Pt_Commons_General::dateFormat($params['receiptDate']),
                "attributes" => $attributes,
                "supervisor_approval" => $params['supervisorApproval'],
                "participant_supervisor" => $params['participantSupervisor'],
                "user_comment" => $params['userComments'],
                "updated_by_admin" => $admin,
                "updated_on_admin" => new Zend_Db_Expr('now()')
            );
            if (isset($params['testDate'])) {
                $mapData['shipment_test_date'] = Pt_Commons_General::dateFormat($params['testDate']);
            }
            if (isset($params['modeOfReceipt'])) {
                $mapData['mode_id'] = $params['modeOfReceipt'];
            }
            if (isset($params['testReceiptDate']) && trim($params['testReceiptDate'])!= '') {
                $mapData['shipment_test_report_date'] = Pt_Commons_General::dateFormat($params['testReceiptDate']);
            } else {
                $mapData['shipment_test_report_date'] = new Zend_Db_Expr('now()');
            }
            if (isset($authNameSpace->qc_access) && $authNameSpace->qc_access =='yes') {
                $mapData['qc_done'] = $params['qcDone'];
                if (isset($mapData['qc_done']) && trim($mapData['qc_done']) == "yes") {
                    $mapData['qc_date'] = Pt_Commons_General::dateFormat($params['qcDate']);
                    $mapData['qc_done_by'] = trim($params['qcDoneBy']);
                    $mapData['qc_created_on'] = new Zend_Db_Expr('now()');
                } else {
                    $mapData['qc_date'] = null;
                    $mapData['qc_done_by'] = null;
                    $mapData['qc_created_on'] = null;
                }
            }

            if (isset($params['customField1']) && trim($params['customField1']) != "") {
                $mapData['custom_field_1'] = $params['customField1'];
            }

            if (isset($params['customField2']) && trim($params['customField2']) != "") {
                $mapData['custom_field_2'] = $params['customField2'];
            }

            $shipmentParticipantDb = new Application_Model_DbTable_ShipmentParticipantMap();
            $shipmentParticipantDb->updateShipment($mapData, $params['smid'], $params['hdLastDate']);

            $instrumentsDb = new Application_Model_DbTable_Instruments();
            for ($i = 0; $i < $size; $i++) {
                $sql = $db->select()
                    ->from("response_result_tb")
                    ->where("shipment_map_id = " . $params['smid'] . " and sample_id = " . $params['sampleId'][$i]);
                $res = $db->fetchRow($sql);
                $dateTested = Pt_Commons_General::dateFormat($params['dateTested'][$i]);
                if ($dateTested == "" || $dateTested == "0000-00-00") {
                    $dateTested = null;
                }
                $instrumentInstalledOn = Pt_Commons_General::dateFormat($params['instrumentInstalledOn'][$i]);
                if ($instrumentInstalledOn == "" || $instrumentInstalledOn == "0000-00-00") {
                    $instrumentInstalledOn = null;
                }
                $instrumentLastCalibratedOn = Pt_Commons_General::dateFormat($params['instrumentLastCalibratedOn'][$i]);
                if ($instrumentLastCalibratedOn == "" || $instrumentLastCalibratedOn == "0000-00-00") {
                    $instrumentLastCalibratedOn = null;
                }
                $cartridgeExpirationDate = Pt_Commons_General::dateFormat($params['cartridgeExpirationDate'][$i]);
                if ($cartridgeExpirationDate == "" || $cartridgeExpirationDate == "0000-00-00") {
                    $cartridgeExpirationDate = null;
                }
                if ($res == null || count($res) == 0) {
                    $db->insert('response_result_tb', array(
                        'shipment_map_id' => $params['smid'],
                        'sample_id' => $params['sampleId'][$i],
                        'date_tested' => $dateTested,
                        'mtb_detected' => $params['mtbDetected'][$i],
                        'error_code' => $params['errorCode'][$i],
                        'rif_resistance' => $params['rifResistance'][$i],
                        'probe_d' => $params['probeD'][$i],
                        'probe_c' => $params['probeC'][$i],
                        'probe_e' => $params['probeE'][$i],
                        'probe_b' => $params['probeB'][$i],
                        'spc' => $params['spc'][$i],
                        'probe_a' => $params['probeA'][$i],
                        'instrument_serial' => $params['instrumentSerial'][$i],
                        'instrument_installed_on' => $instrumentInstalledOn,
                        'instrument_last_calibrated_on' => $instrumentLastCalibratedOn,
                        'module_name' => $params['moduleName'][$i],
                        'instrument_user' => $params['instrumentUser'][$i],
                        'cartridge_expiration_date' => $cartridgeExpirationDate,
                        'reagent_lot_id' => $params['reagentLotId'][$i],
                        'created_by' => $admin,
                        'created_on' => new Zend_Db_Expr('now()')
                    ));
                } else {
                    $db->update('response_result_tb', array(
                        'date_tested' => $dateTested,
                        'mtb_detected' => $params['mtbDetected'][$i],
                        'error_code' => $params['errorCode'][$i],
                        'rif_resistance' => $params['rifResistance'][$i],
                        'probe_d' => $params['probeD'][$i],
                        'probe_c' => $params['probeC'][$i],
                        'probe_e' => $params['probeE'][$i],
                        'probe_b' => $params['probeB'][$i],
                        'spc' => $params['spc'][$i],
                        'probe_a' => $params['probeA'][$i],
                        'instrument_serial' => $params['instrumentSerial'][$i],
                        'instrument_installed_on' => $instrumentInstalledOn,
                        'instrument_last_calibrated_on' => $instrumentLastCalibratedOn,
                        'module_name' => $params['moduleName'][$i],
                        'instrument_user' => $params['instrumentUser'][$i],
                        'cartridge_expiration_date' => $cartridgeExpirationDate,
                        'reagent_lot_id' => $params['reagentLotId'][$i],
                        'updated_by' => $admin,
                        'updated_on' => new Zend_Db_Expr('now()')
                    ), "shipment_map_id = " . $params['smid'] . " and sample_id = " . $params['sampleId'][$i]);
                }
                if (isset($params['instrumentSerial'][$i]) &&
                    $params['instrumentSerial'][$i] != "") {
                    $instrumentDetails = array(
                        'instrument_serial' => $params['instrumentSerial'][$i],
                        'instrument_installed_on' => $instrumentInstalledOn,
                        'instrument_last_calibrated_on' => $instrumentLastCalibratedOn
                    );
                    $instrumentsDb->upsertInstrument($params['participantId'], $instrumentDetails);
                }
            }
        }
    }
}
