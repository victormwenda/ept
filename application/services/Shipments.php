<?php

class Application_Service_Shipments {
    public function echoAllShipments($parameters) {
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
           you want to insert a non-database field (for example a counter or static image)
        */
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $configFile = APPLICATION_PATH . '/configs/config.local.ini';
        if (!is_file($configFile)) {
            $configFile = APPLICATION_PATH . '/configs/config.ini';
        }
        $config = new Zend_Config_Ini($configFile, APPLICATION_ENV);

        $aColumns = array("sl.scheme_name", "shipment_code", 'distribution_code', "DATE_FORMAT(distribution_date,'%d-%b-%Y')", 'number_of_samples', 's.status');
        $orderColumns = array("sl.scheme_name", "shipment_code", 'distribution_code', 'distribution_date', 'number_of_samples', 's.status');

        // Paging
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }

        // Ordering
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
        Filtering
        NOTE this does not match the built-in DataTables filtering which does it
        word by word on any field. It's possible to do here, but concerned about efficiency
        on very large tables, and MySQL's regex functionality is very limited
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
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        // Individual column filtering
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
        SQL queries
        Get data to display
        */
        $sQuery = $db->select()->from(array('s' => 'shipment'))
            ->join(array('d' => 'distributions'), 'd.distribution_id = s.distribution_id', array('distribution_code', 'distribution_date','distribution_id'))
            ->joinLeft(array('spm' => 'shipment_participant_map'), 's.shipment_id = spm.shipment_id', array(
                "total_participants" => new Zend_Db_Expr("count(map_id)"),
                "last_new_shipment_mailed_on",
                "new_shipment_mail_count",
                "last_submission_updated_on" => new Zend_Db_Expr("GREATEST(MAX(spm.updated_on_admin), MAX(spm.updated_on_user))")
            ))
            ->joinLeft(array('p' => 'participant'), 'spm.participant_id = p.participant_id', array())
            ->join(array('sl' => 'scheme_list'), 'sl.scheme_id=s.scheme_type', array('SCHEME' => 'sl.scheme_name'))
            ->group('s.shipment_id');

        $authNameSpace = new Zend_Session_Namespace('administrators');
        if ($authNameSpace->is_ptcc_coordinator) {
            $sQuery = $sQuery->where("p.country IN (".implode(",",$authNameSpace->countries).")");
            $sQuery = $sQuery->where("s.is_official = 1");
        }

        if (isset($parameters['scheme']) && $parameters['scheme'] != "") {
            $sQuery = $sQuery->where("s.scheme_type = ?", $parameters['scheme']);
        }

        if (isset($parameters['distribution']) && $parameters['distribution'] != "" && $parameters['distribution'] != 0) {
            $sQuery = $sQuery->where("s.distribution_id = ?", $parameters['distribution']);
        }

        if (isset($sWhere) && $sWhere != "") {
            $sQuery = $sQuery->where($sWhere);
        }

        if (isset($sOrder) && $sOrder != "") {
            $sQuery = $sQuery->order($sOrder);
        }

        if (isset($sLimit) && isset($sOffset)) {
            $sQuery = $sQuery->limit($sLimit, $sOffset);
        }

        $rResult = $db->fetchAll($sQuery);

        // Data set length after filtering
        $sQuery = $sQuery->reset(Zend_Db_Select::LIMIT_COUNT);
        $sQuery = $sQuery->reset(Zend_Db_Select::LIMIT_OFFSET);
        $aResultFilterTotal = $db->fetchAll($sQuery);
        $iFilteredTotal = count($aResultFilterTotal);

        // Total data set length
        $sQuery = $db->select()->from('shipment', new Zend_Db_Expr("COUNT('shipment_id')"));
        $aResultTotal = $db->fetchCol($sQuery);
        $iTotal = $aResultTotal[0];

        // Output
        $output = array(
            "sEcho" => intval($parameters['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        for ($i = 0; $i < count($rResult); $i++) {
            $row = array();
            if ($rResult[$i]['status'] == 'ready' || $rResult[$i]['status'] == 'shipped') {
                $btn = "btn-success";
            } else if ($rResult[$i]['status'] == 'pending') {
                $btn = "btn-danger";
            } else {
                $btn = "btn-primary";
            }
            if ($rResult[$i]['status'] != 'finalized' && $rResult[$i]['status'] != 'ready' && $rResult[$i]['status'] != 'pending') {
                $responseSwitch = "<select onchange='responseSwitch(this.value,".$rResult[$i]['shipment_id'].")'>";
                $responseSwitch .= "<option value='on'".(isset($rResult[$i]['response_switch']) && $rResult[$i]['response_switch'] =="on" ? " selected='selected' " : "").">On</option>";
                $responseSwitch .= "<option value='off'".(isset($rResult[$i]['response_switch']) && $rResult[$i]['response_switch'] =="off" ? " selected='selected' " : "").">Off</option>";
                $responseSwitch .= "</select>";
            } else {
                $responseSwitch = '-';
            }
            if ($rResult[$i]['status'] == 'evaluated' && $rResult[$i]['last_submission_updated_on'] > $rResult[$i]['updated_on_admin']) {
                $rResult[$i]['status'] = "Should be Re-Evaluated";
            }

            $row[] = $rResult[$i]['shipment_code'];
            $row[] = $rResult[$i]['SCHEME'];
            $row[] = $rResult[$i]['distribution_code'];
            $row[] = Application_Service_Common::ParseDateHumanFormat($rResult[$i]['distribution_date']);
            $row[] = Application_Service_Common::ParseDateHumanFormat($rResult[$i]['lastdate_response']);
            $row[] = $rResult[$i]['number_of_samples'];
            if ($authNameSpace->is_ptcc_coordinator == 0) {
                $row[] = $rResult[$i]['total_participants'];
                $row[] = $responseSwitch;
                $row[] = ucfirst($rResult[$i]['status']);
            }
            $enrolled = '';
            $shipped = '';
            $announcementMail = '';
            $manageResponses = '';

            if ($rResult[$i]['status'] != 'finalized') {
                $edit = '&nbsp;<a class="btn btn-primary btn-xs" href="/admin/shipment/edit/sid/' . base64_encode($rResult[$i]['shipment_id']) . '"><span><i class="icon-edit"></i> Edit</span></a>';
                $shipped = '&nbsp;<a class="btn ' . $btn . ' btn-xs" href="/admin/shipment/ship-it/sid/' . base64_encode($rResult[$i]['shipment_id']) . '"><span><i class="icon-user"></i> Enroll</span></a>';
            } else {
                $edit = '&nbsp;<a class="btn btn-danger btn-xs disabled" href="javascript:void(0);"><span><i class="icon-check"></i> Finalized</span></a>';
            }

            // If shipment is finalized and has a CS survey
            $surveryReminders = '';
            if ($authNameSpace->is_ptcc_coordinator == 0) {
                if ($rResult[$i]['status'] == 'finalized' && null !== $rResult[$i]['cs_survey']) {
                    $days_since_finalized = (new DateTime($rResult[$i]['finalized_date']))->diff(new DateTime())->days;
                    // If currently within the submission window
                    if ($days_since_finalized <= (int) $config->customerSatisfactionSurvey->submissionWindow) {
                        $surveryReminders = '&nbsp;<a class="btn btn-warning btn-xs" href="javascript:void(0);" onclick="mailSurveyReminder(\'' . base64_encode($rResult[$i]['shipment_id']) . '\')"><span><i class="icon-bullhorn"></i> Survey Reminder Mail</span></a>';
                    }
                }
            }

            if($rResult[$i]['status'] == 'shipped') {
                $enrolled = '&nbsp;<a class="btn btn-primary btn-xs disabled" href="javascript:void(0);"><span><i class="icon-ambulance"></i> Shipped</span></a>';
                $announcementMail = '&nbsp;<a class="btn btn-warning btn-xs" href="javascript:void(0);" onclick="mailShipment(\'' . base64_encode($rResult[$i]['shipment_id']) . '\')"><span><i class="icon-bullhorn"></i> New Shipment Mail</span></a>';
            }
            if (in_array(ucfirst($rResult[$i]['status']), array("Shipped", "Evaluated", "Should be Re-Evaluated"))) {
                $manageResponses='&nbsp;<a class="btn btn-info btn-xs" href="/admin/shipment/manage-responses/sid/' . base64_encode($rResult[$i]['shipment_id']) . '/sctype/'. base64_encode($rResult[$i]['scheme_type']) . '"><span><i class="icon-gear"></i> Responses</span></a>';
            }
            if ($rResult[$i]['status'] == 'configured') {
                $manageResponses .= '&nbsp;<a class="btn btn-primary btn-xs" href="javascript:void(0);" onclick="shipDistribution(\'' . base64_encode($rResult[$i]['distribution_id']) . '\')"><span><i class="icon-ambulance"></i> Ship Now</span></a>';
            }
            $delete = '&nbsp;<a class="btn btn-primary btn-xs" href="javascript:void(0);" onclick="removeShipment(\'' . base64_encode($rResult[$i]['shipment_id']) . '\', \'' . $rResult[$i]['shipment_id'] . '\')"><span><i class="icon-remove"></i> Delete</span></a>';
            $generateForms = '';
            if (!$authNameSpace->is_ptcc_coordinator) {
                $generateForms = '&nbsp;<a class="btn btn-success btn-xs" href="javascript:void(0);" onclick="generateForms(\'' . base64_encode($rResult[$i]['shipment_id']) . '\')"><span><i class="icon-file"></i> Generate Forms</span></a>';
            }

            // Disable specific actions if PTCC coordinator
            if ($authNameSpace->is_ptcc_coordinator == 1) {
                $edit = $delete = $manageResponses = $shipped = '';
            }

            $row[] = $edit . $shipped . $enrolled . $delete . $announcementMail . $surveryReminders . $manageResponses . $generateForms;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function UpdateDistributionStatusByShipmentId($sid,$status){
        $shipment=new Application_Model_DbTable_Shipments();
        $dist_id=$shipment->fetchRow('shipment_id='.$sid)['distribution_id'];
        $shipment->updateShipmentStatusByDistribution($dist_id,$status);
        $distributionService = new Application_Service_Distribution();
        $shipmentsForThisDistribution = $distributionService->getShipments($dist_id);
        if (count($shipmentsForThisDistribution) > 1) {
            foreach ($shipmentsForThisDistribution as $shipmentForThisDistribution) {
                if (!isset($shipmentForThisDistribution['status']) || $shipmentForThisDistribution['status'] != $status) {
                    return;
                }
            }
        }
        $distributionService->updateDistributionStatus($dist_id,$status);
    }

    public function getShipmentsForScheme($scheme) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sQuery = $db->select()->from(array('s' => 'shipment'))
            ->join(array('d' => 'distributions'), 'd.distribution_id = s.distribution_id', array('distribution_code', 'distribution_date'))
            ->joinLeft(array("spm" => "shipment_participant_map"), "s.shipment_id = spm.shipment_id", array(
                "total_participants" => new Zend_Db_Expr("COUNT(map_id)"),
                "last_new_shipment_mailed_on",
                "new_shipment_mail_count",
                "last_submission_updated_on" => new Zend_Db_Expr("GREATEST(MAX(spm.updated_on_admin), MAX(spm.updated_on_user))")
            ))
            ->joinLeft(array('p' => 'participant'), 'spm.participant_id = p.participant_id', array())
            ->join(array('sl' => 'scheme_list'), 'sl.scheme_id=s.scheme_type', array('SCHEME' => 'sl.scheme_name'))
            ->where('s.scheme_type = ?', $scheme)
            ->group('s.shipment_id')
            ->order('distribution_date desc');
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if($authNameSpace->is_ptcc_coordinator) {
            $sQuery = $sQuery->where("p.country IS NULL OR p.country IN (".implode(",",$authNameSpace->countries).")");
        }
        $rResult = $db->fetchAll($sQuery);
        for ($i = 0; $i < count($rResult); $i++) {
            if ($rResult[$i]['status'] == 'evaluated' && $rResult[$i]['last_submission_updated_on'] > $rResult[$i]['updated_on_admin']) {
                $rResult[$i]['status'] = "Should be Re-Evaluated";
            }
        }
        return $rResult;
    }

    public function updateEidResults($params) {
        if (!$this->isShipmentEditableToDataManager($params['shipmentId'])) {
            return false;
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $db->beginTransaction();
        try {
            $shipmentParticipantDb = new Application_Model_DbTable_ShipmentParticipantMap();
            $authNameSpace = new Zend_Session_Namespace('datamanagers');
			if (isset($params['sampleRehydrationDate']) && trim($params['sampleRehydrationDate'])!="") {
				$params['sampleRehydrationDate']=Application_Service_Common::ParseDate($params['sampleRehydrationDate']);
			} else {
				$params['sampleRehydrationDate'] = '';
			}
			if (isset($params['extractionAssayExpiryDate']) && trim($params['extractionAssayExpiryDate'])!="") {
				$params['extractionAssayExpiryDate']=Application_Service_Common::ParseDate($params['extractionAssayExpiryDate']);
			} else {
				$params['extractionAssayExpiryDate'] = '';
			}
			if (isset($params['detectionAssayExpiryDate']) && trim($params['detectionAssayExpiryDate'])!="") {
				$params['detectionAssayExpiryDate']=Application_Service_Common::ParseDate($params['detectionAssayExpiryDate']);
			} else {
				$params['detectionAssayExpiryDate'] = '';
			}
			if (!isset($params['modeOfReceipt']) || trim($params['modeOfReceipt'])=="") {
				$params['modeOfReceipt']= NULL;
			}
            $attributes = array("sample_rehydration_date" => $params['sampleRehydrationDate'],
                "extraction_assay" => $params['extractionAssay'],
                "detection_assay" => $params['detectionAssay'],
                "extraction_assay_expiry_date" => Application_Service_Common::ParseDate($params['extractionAssayExpiryDate']),
                "detection_assay_expiry_date" => Application_Service_Common::ParseDate($params['detectionAssayExpiryDate']),
                "extraction_assay_lot_no" => $params['extractionAssayLotNo'],
                "detection_assay_lot_no" => $params['detectionAssayLotNo'],
		        "uploaded_file" => $params['uploadedFilePath']);

            $data = array(
                "shipment_receipt_date" => Application_Service_Common::ParseDate($params['receiptDate']),
                "shipment_test_date" => Application_Service_Common::ParseDate($params['testDate']),
                "attributes" => json_encode($attributes),
                "supervisor_approval" => $params['supervisorApproval'],
                "participant_supervisor" => $params['participantSupervisor'],
                "user_comment" => $params['userComments'],
		        "mode_id" => $params['modeOfReceipt'],
                "updated_by_user" => $authNameSpace->dm_id,
                "updated_on_user" => new Zend_Db_Expr('now()')
            );

            if (isset($params['testReceiptDate']) && trim($params['testReceiptDate'])!= '') {
                $data['shipment_test_report_date'] = Application_Service_Common::ParseDate($params['testReceiptDate']);
            } else {
                $data['shipment_test_report_date'] = new Zend_Db_Expr('now()');
            }

            if (isset($authNameSpace->qc_access) && $authNameSpace->qc_access =='yes') {
                $data['qc_done'] = $params['qcDone'];
                if (isset($data['qc_done']) && trim($data['qc_done'])=="yes") {
                    $data['qc_date'] = Application_Service_Common::ParseDate($params['qcDate']);
                    $data['qc_done_by'] = trim($params['qcDoneBy']);
                    $data['qc_created_on'] = new Zend_Db_Expr('now()');
                } else {
                    $data['qc_date']=NULL;
                    $data['qc_done_by'] = NULL;
                    $data['qc_created_on'] = NULL;
                }
            }
            $noOfRowsAffected = $shipmentParticipantDb->updateShipment($data, $params['smid'], $params['hdLastDate'], null);

            $eidResponseDb = new Application_Model_DbTable_ResponseEid();
            $eidResponseDb->updateResults($params);
            $db->commit();
        } catch (Exception $e) {
            // If any of the queries failed and threw an exception,
            // we want to roll back the whole transaction, reversing
            // changes made in the transaction, even those that succeeded.
            // Thus all changes are committed together, or none are.
            $db->rollBack();
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }
    }

    public function updateDtsResults($params) {
        if (!$this->isShipmentEditableToDataManager($params['shipmentId'])) {
            return false;
        }
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $db->beginTransaction();
        try {

            $shipmentParticipantDb = new Application_Model_DbTable_ShipmentParticipantMap();
            $authNameSpace = new Zend_Session_Namespace('datamanagers');
            $attributes["sample_rehydration_date"] = Application_Service_Common::ParseDate($params['sampleRehydrationDate']);
            $attributes["algorithm"] = $params['algorithm'];

            $data = array(
                "shipment_receipt_date" => Application_Service_Common::ParseDate($params['receiptDate']),
                "shipment_test_date" => Application_Service_Common::ParseDate($params['testDate']),
                "attributes" => json_encode($attributes),
                "supervisor_approval" => $params['supervisorApproval'],
                "participant_supervisor" => $params['participantSupervisor'],
                "user_comment" => $params['userComments'],
                "updated_by_user" => $authNameSpace->dm_id,
                "mode_id" => $params['modeOfReceipt'],
                "updated_on_user" => new Zend_Db_Expr('now()')
            );

            if (isset($params['testReceiptDate']) && trim($params['testReceiptDate'])!= '') {
                $data['shipment_test_report_date'] = Application_Service_Common::ParseDate($params['testReceiptDate']);
            } else {
                $data['shipment_test_report_date'] = new Zend_Db_Expr('now()');
            }

            if (isset($authNameSpace->qc_access) && $authNameSpace->qc_access =='yes') {
                $data['qc_done'] = $params['qcDone'];
                if (isset($data['qc_done']) && trim($data['qc_done'])=="yes") {
                    $data['qc_date'] = Application_Service_Common::ParseDate($params['qcDate']);
                    $data['qc_done_by'] = trim($params['qcDoneBy']);
                    $data['qc_created_on'] = new Zend_Db_Expr('now()');
                } else {
                    $data['qc_date']=NULL;
                    $data['qc_done_by'] = NULL;
                    $data['qc_created_on'] = NULL;
                }
            }
            if (isset($params['customField1']) && trim($params['customField1']) != "") {
                $data['custom_field_1'] = $params['customField1'];
            }

            if (isset($params['customField2']) && trim($params['customField2']) != "") {
                $data['custom_field_2'] = $params['customField2'];
            }

            $noOfRowsAffected = $shipmentParticipantDb->updateShipment($data, $params['smid'], $params['hdLastDate'], null);

            $dtsResponseDb = new Application_Model_DbTable_ResponseDts();
            $dtsResponseDb->updateResults($params);
            $db->commit();
        } catch (Exception $e) {
            // If any of the queries failed and threw an exception,
            // we want to roll back the whole transaction, reversing
            // changes made in the transaction, even those that succeeded.
            // Thus all changes are committed together, or none are.
            $db->rollBack();
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }
    }

    public function removeDtsResults($mapId) {
        try {
            $shipmentParticipantDb = new Application_Model_DbTable_ShipmentParticipantMap();
            $authNameSpace = new Zend_Session_Namespace('datamanagers');
            $data = array(
                "shipment_receipt_date" =>'',
                "shipment_test_date" =>'',
                "attributes" => '{}',
                "shipment_test_report_date" => '',
                "supervisor_approval" =>'',
                "participant_supervisor" =>'',
                "user_comment" => '',
                "final_result" => '',
		        "updated_on_user" => new Zend_Db_Expr('now()'),
                "updated_by_user" => $authNameSpace->dm_id,
                "qc_date"=>'',
                "qc_done_by" => '',
                "qc_created_on" => '',
                "mode_id" => ''
            );
            $noOfRowsAffected = $shipmentParticipantDb->removeShipmentMapDetails($data, $mapId);

            $dtsResponseDb = new Application_Model_DbTable_ResponseDts();
            $dtsResponseDb->removeShipmentResults($mapId);
        } catch (Exception $e) {
            return "Unable to delete. Please try again later or contact system admin for help";
        }
    }

    public function removeDtsEidResults($mapId) {
        try {
            $shipmentParticipantDb = new Application_Model_DbTable_ShipmentParticipantMap();
            $authNameSpace = new Zend_Session_Namespace('datamanagers');
            $data = array(
                "shipment_receipt_date" =>'',
                "shipment_test_date" =>'',
                "attributes" => '{}',
                "shipment_test_report_date" => '',
                "supervisor_approval" =>'',
                "participant_supervisor" =>'',
                "user_comment" => '',
                "final_result" => '',
                "updated_on_user" => new Zend_Db_Expr('now()'),
                "updated_by_user" => $authNameSpace->dm_id,
                "qc_date"=>'',
                "qc_done_by" => '',
                "qc_created_on" => '',
                "mode_id" => ''
            );
            $noOfRowsAffected = $shipmentParticipantDb->removeShipmentMapDetails($data, $mapId);

            $responseDb = new Application_Model_DbTable_ResponseEid();
            $responseDb->delete("shipment_map_id=$mapId");
        } catch (Exception $e) {
            return($e->getMessage());
            return "Unable to delete. Please try again later or contact system admin for help";
        }
    }

    public function removeDtsTbResults($mapId) {
        try {
            $shipmentParticipantDb = new Application_Model_DbTable_ShipmentParticipantMap();
            $authNameSpace = new Zend_Session_Namespace('datamanagers');
            $data = array(
                "shipment_receipt_date" => null,
                "shipment_test_date" => null,
                "attributes" => null,
                "shipment_test_report_date" => null,
                "supervisor_approval" => '',
                "participant_supervisor" => '',
                "user_comment" => '',
                "final_result" => '0',
                "updated_on_user" => new Zend_Db_Expr('now()'),
                "updated_by_user" => $authNameSpace->dm_id,
                "qc_date" => null,
                "qc_done_by" => null,
                "qc_created_on" => null,
                "mode_id" => null,
                "not_tested_reason" => null,
                "optional_eval_comment" => null,
                "evaluation_comment" => '0',
                "failure_reason" => '0',
                "pt_support_comments" => '',
                "pt_test_not_performed_comments" => null,
                "is_pt_test_not_performed" => null,
                "shipment_score" => null,
                "documentation_score" => null,

            );
            $noOfRowsAffected = $shipmentParticipantDb->removeShipmentMapDetails($data, $mapId);

            $responseDb = new Application_Model_DbTable_ResponseTb();
            $responseDb->delete("shipment_map_id=$mapId");
        } catch (Exception $e) {
            return($e->getMessage());
            return "Unable to delete. Please try again later or contact system admin for help";
        }
    }

    public function updateDbsResults($params) {
        if (!$this->isShipmentEditableToDataManager($params['shipmentId'])) {
            return false;
        }
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $db->beginTransaction();
        try {
            $shipmentParticipantDb = new Application_Model_DbTable_ShipmentParticipantMap();
            $authNameSpace = new Zend_Session_Namespace('datamanagers');
            $attributes["sample_rehydration_date"] = Application_Service_Common::ParseDate($params['sampleRehydrationDate']);
            $data = array(
                "shipment_receipt_date" => Application_Service_Common::ParseDate($params['receiptDate']),
                "shipment_test_date" => Application_Service_Common::ParseDate($params['testDate']),
                "attributes" => json_encode($attributes),
                "supervisor_approval" => $params['supervisorApproval'],
                "participant_supervisor" => $params['participantSupervisor'],
                "user_comment" => $params['userComments'],
		        "mode_id" => $params['modeOfReceipt'],
                "updated_by_user" => $authNameSpace->dm_id,
                "updated_on_user" => new Zend_Db_Expr('now()')
            );
            if (isset($params['testReceiptDate']) && trim($params['testReceiptDate'])!= '') {
               $data['shipment_test_report_date'] = Application_Service_Common::ParseDate($params['testReceiptDate']);
            } else {
                $data['shipment_test_report_date'] = new Zend_Db_Expr('now()');
            }

            if (isset($authNameSpace->qc_access) && $authNameSpace->qc_access =='yes') {
                $data['qc_done'] = $params['qcDone'];
                if (isset($data['qc_done']) && trim($data['qc_done'])=="yes") {
                    $data['qc_date'] = Application_Service_Common::ParseDate($params['qcDate']);
                    $data['qc_done_by'] = trim($params['qcDoneBy']);
                    $data['qc_created_on'] = new Zend_Db_Expr('now()');
                 } else {
                    $data['qc_date']=NULL;
                    $data['qc_done_by'] = NULL;
                    $data['qc_created_on'] = NULL;
                 }
            }
            $noOfRowsAffected = $shipmentParticipantDb->updateShipment($data, $params['smid'], $params['hdLastDate'], null);

            $dbsResponseDb = new Application_Model_DbTable_ResponseDbs();
            $dbsResponseDb->updateResults($params);
            $db->commit();
        } catch (Exception $e) {
            // If any of the queries failed and threw an exception,
            // we want to roll back the whole transaction, reversing
            // changes made in the transaction, even those that succeeded.
            // Thus all changes are committed together, or none are.
            $db->rollBack();
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }
    }

    private function getShipmentParticipantMap($shipmentId, $participantId) {
        $schemeService = new Application_Service_Schemes();
        $shipmentData = $schemeService->getShipmentData($shipmentId, $participantId);
        if (!isset($shipmentData['attributes']) || $shipmentData['attributes'] == "") {
            $shipmentData['attributes'] = "{}";
        }
        $shipmentData['attributes'] = json_decode($shipmentData['attributes'], true);
        return $shipmentData;
    }

    public function validateUpdateTbResultHeader($params, $shipmentMapFromDatabase) {
        $validationErrors = array();
        $evaluationStatus = $shipmentMapFromDatabase['evaluation_status'];
        $submitted = $evaluationStatus[2] == '1';
        if (!$submitted) {
            return "";
        }
        if ($params['unableToSubmit'] == "yes") {
            if (!isset($params["unableToSubmitReason"]) || $params["unableToSubmitReason"] == "") {
                array_push($validationErrors,"Reason for not testing the panel is a required field.");
            } else if ($params["unableToSubmitReason"] == "other" &&
                (!isset($params['unableToSubmitComment']) || $params['unableToSubmitComment'] == "")) {
                array_push($validationErrors,"Please specify a reason for not testing the panel?");
            }
        } else {
            if (!isset($params['assay']) || $params["assay"] == "") {
                array_push($validationErrors,"Assay is a required field.");
            }
            $cartridgeLotNo = isset($params['cartridgeLotNo']) ? $params['cartridgeLotNo'] : $params['mtbRifKitLotNo'];
            if (!isset($cartridgeLotNo) || $cartridgeLotNo == "") {
                array_push($validationErrors,"Cartridge Lot No is a required field.");
            }
            if (!isset($params['expiryDate']) || $params["expiryDate"] == "") {
                array_push($validationErrors,"Expiration date of Cartridge is a required field.");
            }
            if (isset($params['qcDone']) && $params["qcDone"] == "yes") {
                if (!isset($params['qcDate']) || $params["qcDate"] == "") {
                    array_push($validationErrors,"Maintenance Date is a required field.");
                }
                if (!isset($params['qcDoneBy']) || $params["qcDoneBy"] == "") {
                    array_push($validationErrors,"Maintenance Done By is a required field.");
                }
            }
        }
        $uniqueValidationErrors = array_unique($validationErrors);
        return implode("\n", $uniqueValidationErrors);
    }

    public function updateTbResultHeader($params) {
        if (!$this->isShipmentEditableToDataManager($params['shipmentId'])) {
            return "The submission cannot be edited at this point in time.";
        }
        $shipmentData = $this->getShipmentParticipantMap($params['shipmentId'], $params['participantId']);
        $validationErrorMessages = $this->validateUpdateTbResultHeader($params, $shipmentData);
        if ($validationErrorMessages != "") {
            return $validationErrorMessages;
        }
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        $attributes = array(
            "cartridge_lot_no" => isset($params['cartridgeLotNo']) ? $params['cartridgeLotNo'] : $params['mtbRifKitLotNo'],
            "expiry_date" => Application_Service_Common::ParseDate($params['expiryDate']),
            "assay" => $params['assay'],
            "count_tests_conducted_over_month" => $params['countTestsConductedOverMonth'],
            "count_errors_encountered_over_month" => $params['countErrorsEncounteredOverMonth'],
            "error_codes_encountered_over_month" => $params['errorCodesEncounteredOverMonth']
        );
        $data = array(
            "shipment_receipt_date" => Application_Service_Common::ParseDbDate($params['dateReceived']),
            "attributes" => json_encode($attributes),
            "updated_by_user" => $authNameSpace->dm_id,
            "updated_on_user" => new Zend_Db_Expr('now()')
        );

        if ($params['unableToSubmit'] == "yes") {
            $data['is_pt_test_not_performed'] = "yes";
            if ($params["unableToSubmitReason"] == "other") {
                $data['not_tested_reason'] = null;
                $data['pt_test_not_performed_comments'] = $params["unableToSubmitComment"];
            } else if (isset($params["unableToSubmitReason"]) && trim($params["unableToSubmitReason"]) != "") {
                $data['not_tested_reason'] = $params["unableToSubmitReason"];
                $data['pt_test_not_performed_comments'] = null;
            }
        } else {
            $data['is_pt_test_not_performed'] = "no";
            $data['not_tested_reason'] = null;
            $data['pt_test_not_performed_comments'] = null;
        }
        if (isset($params['testReceiptDate']) && trim($params['testReceiptDate'])!= '') {
            $data['shipment_test_report_date'] = Application_Service_Common::ParseDbDate($params['testReceiptDate']);
        } else {
            $data['shipment_test_report_date'] = new Zend_Db_Expr('now()');
        }
        if (isset($authNameSpace->qc_access) && $authNameSpace->qc_access =='yes') {
            $data['qc_done'] = $params['qcDone'];
            if (isset($data['qc_done']) && trim($data['qc_done'])=="yes") {
                $data['qc_date'] = Application_Service_Common::ParseDbDate($params['qcDate']);
                $data['qc_done_by'] = trim($params['qcDoneBy']);
                $data['qc_created_on'] = new Zend_Db_Expr('now()');
            } else {
                $data['qc_date']=NULL;
                $data['qc_done_by'] = NULL;
                $data['qc_created_on'] = NULL;
            }
        }
        $shipmentParticipantDb = new Application_Model_DbTable_ShipmentParticipantMap();
        $shipmentParticipantDb->updateShipmentValues($data, $params['smid']);
        return "";
    }

    public function validateUpdateTbResult($params, $shipmentMapFromDatabase) {
        $validationErrors = array();
        $evaluationStatus = $shipmentMapFromDatabase['evaluation_status'];
        $submitted = $evaluationStatus[2] == '1';
        if (!$submitted) {
            return "";
        }

        if (!isset($params['dateTested']) || $params['dateTested'] == "") {
            array_push($validationErrors,"Date Tested is a required field.");
        }
        if (!isset($params['mtbDetected']) || $params['mtbDetected'] == "") {
            array_push($validationErrors,"MTB Detected is a required field.");
        } else if ($params['mtbDetected'] == "error") {
            if (!isset($params['errorCode']) || $params['errorCode'] == "") {
                array_push($validationErrors,"Error Code is a required field when MTB Detected is Error.");
            }
        } else if (isset($shipmentMapFromDatabase["attributes"]["assay"]) && $shipmentMapFromDatabase["attributes"]["assay"] != "" &&
            in_array($params['mtbDetected'], array("detected", "high", "medium", "low", "veryLow", "trace", "notDetected"))) {
            if (in_array($params['mtbDetected'], array("detected", "high", "medium", "low", "veryLow"))) {
                if (!isset($params['rifResistance']) || $params['rifResistance'] == "" || $params['rifResistance'] == "na") {
                    array_push($validationErrors,"Rif Resistance is a required field when MTB Detected is one of Detected, High, Medium, Low or Very Low.");
                }
            }
            if (!isset($params['probe1']) || $params['probe1'] == "" || !is_numeric($params['rifResistance'])) {
                $probe1Name = $shipmentMapFromDatabase["attributes"]["assay"] == "2" ? "SPC" : "Probe D";
                array_push($validationErrors,$probe1Name." is a required, numeric field when MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
            }
            if (!isset($params['probe2']) || $params['probe2'] == "" || !is_numeric($params['probe2'])) {
                $probe2Name = $shipmentMapFromDatabase["attributes"]["assay"] == "2" ? "IS1081-IS6110" : "Probe C";
                array_push($validationErrors,$probe2Name." is a required, numeric field when MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
            }
            if (!isset($params['probe3']) || $params['probe3'] == "" || !is_numeric($params['probe3'])) {
                $probe3Name = $shipmentMapFromDatabase["attributes"]["assay"] == "2" ? "rpoB1" : "Probe E";
                array_push($validationErrors,$probe3Name." is a required, numeric field when MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
            }
            if (!isset($params['probe4']) || $params['probe4'] == "" || !is_numeric($params['probe4'])) {
                $probe4Name = $shipmentMapFromDatabase["attributes"]["assay"] == "2" ? "rpoB2" : "Probe B";
                array_push($validationErrors,$probe4Name." is a required, numeric field when MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
            }
            if (!isset($params['probe5']) || $params['probe5'] == "" || !is_numeric($params['probe5'])) {
                $probe5Name = $shipmentMapFromDatabase["attributes"]["assay"] == "2" ? "rpoB3" : "SPC";
                array_push($validationErrors,$probe5Name." is a required, numeric field when MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
            }
            if (!isset($params['probe6']) || $params['probe6'] == "" || !is_numeric($params['probe6'])) {
                $probe6Name = $shipmentMapFromDatabase["attributes"]["assay"] == "2" ? "rpoB4" : "Probe A";
                array_push($validationErrors,$probe6Name." is a required, numeric field when MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
            }
        }

        $uniqueValidationErrors = array_unique($validationErrors);
        return implode("\n", $uniqueValidationErrors);
    }

    public function updateTbResult($params, $cartridgeExpirationDate, $cartridgeLotNo) {
        if (!$this->isShipmentEditableToDataManager($params['shipmentId'])) {
            return "The submission cannot be edited at this point in time.";
        }
        $shipmentData = $this->getShipmentParticipantMap($params['shipmentId'], $params['participantId']);
        $validationErrorMessages = $this->validateUpdateTbResult($params, $shipmentData);
        if ($validationErrorMessages != "") {
            return $validationErrorMessages;
        }
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $mtbDetected = $params['mtbDetected'];
            $rifResistance = $params['rifResistance'];
            $errorCode = $params['errorCode'];
            if ($mtbDetected != "error") {
                $errorCode = null;
                if(!in_array($mtbDetected, array("detected", "high", "medium", "low", "veryLow")) && ($rifResistance == null || $rifResistance == "")) {
                    if ($mtbDetected=="trace") {
                        $rifResistance = "indeterminate";
                    } else {
                        $rifResistance = "na";
                    }
                }
            } else {
                $rifResistance = "na";
            }
            $params['rifResistance'] = $rifResistance;
            $params['errorCode'] = $errorCode;

            $tbResponseDb = new Application_Model_DbTable_ResponseTb();
            $tbResponseDb->updateResult($params, $cartridgeExpirationDate, $cartridgeLotNo);
            $instrumentsDb = new Application_Model_DbTable_Instruments();
            if (isset($params['instrumentSerial']) &&
                $params['instrumentSerial'] != "") {
                $instrumentDetails = array(
                    'instrument_serial' => $params['instrumentSerial'],
                    'instrument_installed_on' => Application_Service_Common::ParseDate($params['instrumentInstalledOn']),
                    'instrument_last_calibrated_on' => Application_Service_Common::ParseDate($params['instrumentLastCalibratedOn'])
                );
                $instrumentsDb->upsertInstrument($params['participantId'], $instrumentDetails);
            }

            $db->commit();
            return "";
        } catch (Exception $e) {
            $db->rollBack();
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            return "An unexpected error occurred when trying to submit these results.";
        }
    }

    public function validateUpdateTbResultFooter($params, $shipmentMapFromDatabase) {
        $validationErrors = array();
        $evaluationStatus = $shipmentMapFromDatabase['evaluation_status'];
        $submitted = $evaluationStatus[2] == '1' ||
            (isset($params['submitAction']) && $params['submitAction'] == 'submit');
        if (!$submitted && isset($params['submitResponse']) && trim($params['submitResponse']) != 'yes') {
            return "";
        }
        if (isset($params['unableToSubmit']) && $params['unableToSubmit'] == "yes") {
            return "";
        }
        if (isset($shipmentMapFromDatabase['is_pt_test_not_performed']) && $shipmentMapFromDatabase['is_pt_test_not_performed'] == "yes") {
            return "";
        }
        if (!isset($shipmentMapFromDatabase['shipment_receipt_date']) || $shipmentMapFromDatabase["shipment_receipt_date"] == "") {
            array_push($validationErrors,"Shipment Received on is a required field.");
        }
        if (!isset($shipmentMapFromDatabase["attributes"]["assay"]) || $shipmentMapFromDatabase["attributes"]["assay"] == "") {
            array_push($validationErrors,"Assay is a required field.");
        }
        if (!isset($shipmentMapFromDatabase["attributes"]["cartridgeLotNo"]) || $shipmentMapFromDatabase["attributes"]["cartridgeLotNo"] == "") {
            array_push($validationErrors,"Cartridge Lot No is a required field.");
        }
        if (!isset($shipmentMapFromDatabase["attributes"]['expiryDate']) || $params["expiryDate"] == "") {
            array_push($validationErrors,"Expiration date of Cartridge is a required field.");
        }
        if (!isset($shipmentMapFromDatabase['shipment_test_report_date']) || $shipmentMapFromDatabase["shipment_test_report_date"] == "") {
            array_push($validationErrors,"Response Date is a required field.");
        }
        if (isset($shipmentMapFromDatabase['qc_done']) && $params["qcDone"] == "yes") {
            if (!isset($shipmentMapFromDatabase['qc_date']) || $shipmentMapFromDatabase['qc_date'] == "") {
                array_push($validationErrors,"Maintenance Date is a required field.");
            }
            if (!isset($shipmentMapFromDatabase['qc_done_by']) || $shipmentMapFromDatabase['qc_done_by'] == "") {
                array_push($validationErrors,"Maintenance Done By is a required field.");
            }
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->select()->from(array('res' => 'response_result_tb'))
            ->where("res.shipment_map_id = ?", $shipmentMapFromDatabase["map_id"]);
        $sampleResults = $db->fetchAll($sql);
        $size = count($sampleResults);
        if ($size != 5) {
            array_push($validationErrors,"All 5 samples need to be tested and there values entered.");
        }
        for ($i = 0; $i < $size; $i++) {
            if (!isset($sampleResults[$i]['date_tested']) || $sampleResults[$i]['date_tested'] == "") {
                array_push($validationErrors,"Date Tested is a required field for each sample.");
            }
            if (!isset($sampleResults[$i]['mtb_detected']) || $sampleResults[$i]['mtb_detected'] == "") {
                array_push($validationErrors,"MTB Detected is a required field for each sample.");
            } else if ($sampleResults[$i]['mtb_detected'] == "error") {
                if (!isset($sampleResults[$i]['error_code']) || $sampleResults[$i]['error_code'] == "") {
                    array_push($validationErrors,"Error Code is a required field for a sample when MTB Detected is Error.");
                }
            } else if (in_array($sampleResults[$i]['mtb_detected'], array("detected", "high", "medium", "low", "veryLow"))) {
                if (!isset($sampleResults[$i]['rif_resistance']) || $sampleResults[$i]['rif_resistance'] == "" || $sampleResults[$i]['rif_resistance'] == "na") {
                    array_push($validationErrors,"Rif Resistance is a required field for each sample where MTB Detected is one of Detected, High, Medium, Low or Very Low.");
                }
            } else if (isset($shipmentMapFromDatabase["attributes"]["assay"]) && $shipmentMapFromDatabase["attributes"]["assay"] != "" &&
                in_array($sampleResults[$i]['mtb_detected'], array("detected", "high", "medium", "low", "veryLow", "trace", "notDetected"))) {
                if (!isset($sampleResults[$i]['probe_1']) || $sampleResults[$i]['probe_1'] == "" || !is_numeric($sampleResults[$i]['probe_1'])) {
                    $probe1Name = $shipmentMapFromDatabase["attributes"]["assay"] == "2" ? "SPC" : "Probe D";
                    array_push($validationErrors,$probe1Name." is a required, numeric field for each sample where MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
                }
                if (!isset($sampleResults[$i]['probe_2']) || $sampleResults[$i]['probe_2'] == "" || !is_numeric($sampleResults[$i]['probe_2'])) {
                    $probe2Name = $shipmentMapFromDatabase["attributes"]["assay"] == "2" ? "IS1081-IS6110" : "Probe C";
                    array_push($validationErrors,$probe2Name." is a required, numeric field for each sample where MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
                }
                if (!isset($sampleResults[$i]['probe_3']) || $sampleResults[$i]['probe_3'] == "" || !is_numeric($sampleResults[$i]['probe_3'])) {
                    $probe3Name = $shipmentMapFromDatabase["attributes"]["assay"] == "2" ? "rpoB1" : "Probe E";
                    array_push($validationErrors,$probe3Name." is a required, numeric field for each sample where MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
                }
                if (!isset($sampleResults[$i]['probe_4']) || $sampleResults[$i]['probe_4'] == "" || !is_numeric($sampleResults[$i]['probe_4'])) {
                    $probe4Name = $shipmentMapFromDatabase["attributes"]["assay"] == "2" ? "rpoB2" : "Probe B";
                    array_push($validationErrors,$probe4Name." is a required, numeric field for each sample where MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
                }
                if (!isset($sampleResults[$i]['probe_5']) || $sampleResults[$i]['probe_5'] == "" || !is_numeric($sampleResults[$i]['probe_5'])) {
                    $probe5Name = $shipmentMapFromDatabase["attributes"]["assay"] == "2" ? "rpoB3" : "SPC";
                    array_push($validationErrors,$probe5Name." is a required, numeric field for each sample where MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
                }
                if (!isset($sampleResults[$i]['probe_6']) || $sampleResults[$i]['probe_6'] == "" || !is_numeric($sampleResults[$i]['probe_6'])) {
                    $probe6Name = $shipmentMapFromDatabase["attributes"]["assay"] == "2" ? "rpoB4" : "Probe A";
                    array_push($validationErrors,$probe6Name." is a required, numeric field for each sample where MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
                }
            }
        }
        if (!isset($params['supervisorApproval']) || $params["supervisorApproval"] == "") {
            array_push($validationErrors,"Supervisor Review is a required field.");
        } else if ($params["supervisorApproval"] == "yes" && (!isset($params['participantSupervisor']) || $params["participantSupervisor"] == "")) {
            array_push($validationErrors,"Supervisor Name is a required field.");
        }
        $uniqueValidationErrors = array_unique($validationErrors);
        return implode("\n", $uniqueValidationErrors);
    }

    public function updateTbResultFooter($params) {
        if (!$this->isShipmentEditableToDataManager($params["shipmentId"])) {
            return "The submission cannot be edited at this point in time.";
        }
        $shipmentData = $this->getShipmentParticipantMap($params["shipmentId"], $params["participantId"]);
        $validationErrorMessages = $this->validateUpdateTbResultFooter($params, $shipmentData);
        if ($validationErrorMessages != "") {
            return $validationErrorMessages;
        }
        $shipmentParticipantDb = new Application_Model_DbTable_ShipmentParticipantMap();
        $authNameSpace = new Zend_Session_Namespace("datamanagers");
        $attributes = $shipmentData["attributes"];
        if (isset($params["cartridgeLotNo"]) && $params["cartridgeLotNo"] != "") {
            $attributes["cartridge_lot_no"] = $params["cartridgeLotNo"];
        } else if (isset($params["mtbRifKitLotNo"]) && $params["mtbRifKitLotNo"] != "") {
            $attributes["cartridge_lot_no"] = $params["mtbRifKitLotNo"];
        }
        if (isset($params["expiryDate"]) && $params["expiryDate"] != "") {
            $attributes["expiry_date"] = $params["expiryDate"];
        }
        if (isset($params["assay"]) && $params["assay"] != "") {
            $attributes["assay"] = $params["assay"];
        }
        if (isset($params["countTestsConductedOverMonth"]) && $params["countTestsConductedOverMonth"] != "") {
            $attributes["count_tests_conducted_over_month"] = $params["countTestsConductedOverMonth"];
        }
        if (isset($params["countErrorsEncounteredOverMonth"]) && $params["countErrorsEncounteredOverMonth"] != "") {
            $attributes["count_errors_encountered_over_month"] = $params["countErrorsEncounteredOverMonth"];
        }
        if (isset($params["errorCodesEncounteredOverMonth"]) && $params["errorCodesEncounteredOverMonth"] != "") {
            $attributes["error_codes_encountered_over_month"] = $params["errorCodesEncounteredOverMonth"];
        }
        $data = array(
            "supervisor_approval" => $params['supervisorApproval'],
            "participant_supervisor" => $params['participantSupervisor'],
            "user_comment" => $params['userComments'],
            "updated_by_user" => $authNameSpace->dm_id,
            "updated_on_user" => new Zend_Db_Expr('now()'),
            "attributes" => json_encode($attributes)
        );
        if ($params['unableToSubmit'] == "yes") {
            $data['is_pt_test_not_performed'] = "yes";
            if ($params["unableToSubmitReason"] == "other") {
                $data['not_tested_reason'] = null;
                $data['pt_test_not_performed_comments'] = $params["unableToSubmitComment"];
            } else if (isset($params["unableToSubmitReason"]) && trim($params["unableToSubmitReason"]) != "") {
                $data['not_tested_reason'] = $params["unableToSubmitReason"];
                $data['pt_test_not_performed_comments'] = null;
            }
        } else {
            $data['is_pt_test_not_performed'] = "no";
            $data['not_tested_reason'] = null;
            $data['pt_test_not_performed_comments'] = null;
        }
        if (isset($params['testReceiptDate']) && trim($params['testReceiptDate'])!= '') {
            $data['shipment_test_report_date'] = Application_Service_Common::ParseDbDate($params['testReceiptDate']);
        } else {
            $data['shipment_test_report_date'] = new Zend_Db_Expr('now()');
        }
        if (isset($params['submitResponse']) &&
            isset($params['deadlineDate']) &&
            trim($params['submitResponse']) == 'yes') {
            $shipmentParticipantDb->updateShipment($data, $params['smid'], $params['deadlineDate'], 'submit');
        } else {
            $shipmentParticipantDb->updateShipmentValues($data, $params['smid']);
        }
        return true;
    }

    public function validateUpdateTbResults($params, $shipmentMapFromDatabase) {
        $validationErrors = array();
        $evaluationStatus = $shipmentMapFromDatabase['evaluation_status'];
        $submitted = $evaluationStatus[2] == '1';
        if (!$submitted) {
            return "";
        }
        if ($params['ableToEnterResults'] == "no") {
            if (!isset($params["notTestedReason"]) || $params["notTestedReason"] == "") {
                array_push($validationErrors,"Reason for not testing the panel is a required field.");
            } else if ($params["notTestedReason"] == "other" &&
                (!isset($params['notTestedOtherReason']) || $params['notTestedOtherReason'] == "")) {
                array_push($validationErrors,"Please specify a reason for not testing the panel?");
            }
        } else {
            if (!isset($params['assay']) || $params["assay"] == "") {
                array_push($validationErrors,"Assay is a required field.");
            }
            $cartridgeLotNo = isset($params['cartridgeLotNo']) ? $params['cartridgeLotNo'] : $params['mtbRifKitLotNo'];
            if (!isset($cartridgeLotNo) || $cartridgeLotNo == "") {
                array_push($validationErrors,"Cartridge Lot No is a required field.");
            }
            if (!isset($params['expiryDate']) || $params["expiryDate"] == "") {
                array_push($validationErrors,"Expiration date of Cartridge is a required field.");
            }
            if (isset($params['qcDone']) && $params["qcDone"] == "yes") {
                if (!isset($params['qcDate']) || $params["qcDate"] == "") {
                    array_push($validationErrors,"Maintenance Date is a required field.");
                }
                if (!isset($params['qcDoneBy']) || $params["qcDoneBy"] == "") {
                    array_push($validationErrors,"Maintenance Done By is a required field.");
                }
            }
            if (!isset($params['receiptDate']) || $params["receiptDate"] == "") {
                array_push($validationErrors,"Shipment Received on is a required field.");
            }
            $size = count($params['sampleId']);
            if ($size < 5) {
                array_push($validationErrors,"All 5 samples need to be tested and there values entered.");
            }
            for ($i = 0; $i < $size; $i++) {
                if (!isset($params['dateTested'][$i]) || $params['dateTested'][$i] == "") {
                    array_push($validationErrors,"Date Tested is a required field.");
                }
                if (!isset($params['mtbDetected'][$i]) || $params['mtbDetected'][$i] == "") {
                    array_push($validationErrors,"MTB Detected is a required field.");
                } else if ($params['mtbDetected'][$i] == "error") {
                    if (!isset($params['errorCode'][$i]) || $params['errorCode'][$i] == "") {
                        array_push($validationErrors,"Error Code is a required field when MTB Detected is Error.");
                    }
                } else if (isset($params["assay"]) && $params["assay"] != "" && isset($params['mtbDetected'][$i]) &&
                    in_array($params['mtbDetected'][$i], array("detected", "high", "medium", "low", "veryLow", "trace", "notDetected"))) {
                    if (isset($params['mtbDetected'][$i]) && in_array($params['mtbDetected'][$i], array("detected", "high", "medium", "low", "veryLow"))) {
                        if (!isset($params['rifResistance'][$i]) || $params['rifResistance'][$i] == "" || $params['rifResistance'][$i] == "na") {
                            array_push($validationErrors,"Rif Resistance is a required field when MTB Detected is one of Detected, High, Medium, Low or Very Low.");
                        }
                    }
                    if (!isset($params['probe1'][$i]) || $params['probe1'][$i] == "" || !is_numeric($params['probe1'][$i])) {
                        $probe1Name = $params["assay"] == "2" ? "SPC" : "Probe D";
                        array_push($validationErrors,$probe1Name." is a required, numeric field when MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
                    }
                    if (!isset($params['probe2'][$i]) || $params['probe2'][$i] == "" || !is_numeric($params['probe2'][$i])) {
                        $probe2Name = $params["assay"] == "2" ? "IS1081-IS6110" : "Probe C";
                        array_push($validationErrors,$probe2Name." is a required, numeric field when MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
                    }
                    if (!isset($params['probe3'][$i]) || $params['probe3'][$i] == "" || !is_numeric($params['probe3'][$i])) {
                        $probe3Name = $params["assay"] == "2" ? "rpoB1" : "Probe E";
                        array_push($validationErrors,$probe3Name." is a required, numeric field when MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
                    }
                    if (!isset($params['probe4'][$i]) || $params['probe4'][$i] == "" || !is_numeric($params['probe4'][$i])) {
                        $probe4Name = $params["assay"] == "2" ? "rpoB2" : "Probe B";
                        array_push($validationErrors,$probe4Name." is a required, numeric field when MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
                    }
                    if (!isset($params['probe5'][$i]) || $params['probe5'][$i] == "" || !is_numeric($params['probe5'][$i])) {
                        $probe5Name = $params["assay"] == "2" ? "rpoB3" : "SPC";
                        array_push($validationErrors,$probe5Name." is a required, numeric field when MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
                    }
                    if (!isset($params['probe6'][$i]) || $params['probe6'][$i] == "" || !is_numeric($params['probe6'][$i])) {
                        $probe6Name = $params["assay"] == "2" ? "rpoB4" : "Probe A";
                        array_push($validationErrors,$probe6Name." is a required, numeric field when MTB Detected is one of Detected, High, Medium, Low, Very Low, Trace or Not Detected.");
                    }
                }
            }
            if (!isset($params['supervisorApproval']) || $params["supervisorApproval"] == "") {
                array_push($validationErrors,"Supervisor Review is a required field.");
            } else if ($params["supervisorApproval"] == "yes" && (!isset($params['participantSupervisor']) || $params["participantSupervisor"] == "")) {
                array_push($validationErrors,"Supervisor Name is a required field.");
            }
        }
        $uniqueValidationErrors = array_unique($validationErrors);
        return implode("\n", $uniqueValidationErrors);
    }

    public function updateTbResults($params) {
        if (!$this->isShipmentEditableToDataManager($params['shipmentId'])) {
            return "The submission cannot be edited at this point in time.";
        }
        $shipmentData = $this->getShipmentParticipantMap($params['shipmentId'], $params['participantId']);
        $validationErrorMessages = $this->validateUpdateTbResults($params, $shipmentData);
        if ($validationErrorMessages != "") {
            return $validationErrorMessages;
        }
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $shipmentParticipantDb = new Application_Model_DbTable_ShipmentParticipantMap();
            $authNameSpace = new Zend_Session_Namespace('datamanagers');
            $attributes = array(
                "cartridge_lot_no" => isset($params['cartridgeLotNo']) ? $params['cartridgeLotNo'] : $params['mtbRifKitLotNo'],
                "expiry_date" => Application_Service_Common::ParseDate($params['expiryDate']),
                "assay" => $params['assay'],
                "count_tests_conducted_over_month" => $params['countTestsConductedOverMonth'],
                "count_errors_encountered_over_month" => $params['countErrorsEncounteredOverMonth'],
                "error_codes_encountered_over_month" => $params['errorCodesEncounteredOverMonth']
            );
            $data = array(
                "shipment_receipt_date" => Application_Service_Common::ParseDate($params['receiptDate']),
                "attributes" => json_encode($attributes),
                "supervisor_approval" => $params['supervisorApproval'],
                "participant_supervisor" => $params['participantSupervisor'],
                "user_comment" => $params['userComments'],
		        "updated_by_user" => $authNameSpace->dm_id,
                "updated_on_user" => new Zend_Db_Expr('now()')
            );
            if ($params['ableToEnterResults'] == "no") {
                $data['is_pt_test_not_performed'] = "yes";
                if ($params["notTestedReason"] == "other") {
                    $data['not_tested_reason'] = null;
                    $data['pt_test_not_performed_comments'] = $params["notTestedOtherReason"];
                } else if (isset($params["notTestedReason"]) && trim($params["notTestedReason"]) != "") {
                    $data['not_tested_reason'] = $params["notTestedReason"];
                    $data['pt_test_not_performed_comments'] = null;
                }
            } else {
                $data['is_pt_test_not_performed'] = "no";
                $data['not_tested_reason'] = null;
                $data['pt_test_not_performed_comments'] = null;
            }
            if (isset($params['testReceiptDate']) && trim($params['testReceiptDate'])!= '') {
                $data['shipment_test_report_date'] = Application_Service_Common::ParseDate($params['testReceiptDate']);
            } else {
                $data['shipment_test_report_date'] = new Zend_Db_Expr('now()');
            }
            if (isset($authNameSpace->qc_access) && $authNameSpace->qc_access =='yes') {
                $data['qc_done'] = isset($params['qcDone']) ? $params['qcDone'] : 'no';
                if (isset($data['qc_done']) && trim($data['qc_done'])=="yes") {
                    $data['qc_date'] = Application_Service_Common::ParseDate($params['qcDate']);
                    $data['qc_done_by'] = trim($params['qcDoneBy']);
                    $data['qc_created_on'] = new Zend_Db_Expr('now()');
                } else {
                    $data['qc_date'] = null;
                    $data['qc_done_by'] = null;
                    $data['qc_created_on'] = null;
                }
            }
            $shipmentParticipantDb->updateShipment($data, $params['smid'], $params['hdLastDate'], $params['submitAction']);

            $tbResponseDb = new Application_Model_DbTable_ResponseTb();
            $shipmentMap = $db->fetchRow($db->select()->from(array('spm' => 'shipment_participant_map'))
                ->where("spm.map_id = ?", $params['smid']));
            $evaluationStatus = $shipmentMap['evaluation_status'];
            $submitted = $evaluationStatus[2] == '1' ||
                (isset($params['submitAction']) && $params['submitAction'] == 'submit');
            $sampleIds = $params['sampleId'];
            foreach ($sampleIds as $key => $sampleId) {
                $mtbDetected = $params['mtbDetected'][$key];
                $rifResistance = isset($params['rifResistance'][$key]) ? $params['rifResistance'][$key] : null;
                $errorCode = isset($params['errorCode'][$key]) ? $params['errorCode'][$key] : null;
                if ($mtbDetected != "error") {
                    $errorCode = null;
                    if(!in_array($mtbDetected, array("detected", "high", "medium", "low", "veryLow")) && ($rifResistance == null || $rifResistance == "")) {
                        if($mtbDetected=="trace") {
                            $rifResistance = "indeterminate";
                        }else{
                            $rifResistance = "na";
                        }
                    }
                } else {
                    $rifResistance = "na";
                }
                $params['rifResistance'][$key] = $rifResistance;
                $params['errorCode'][$key] = $errorCode;
            }
            $tbResponseDb->updateResults($params, $submitted);

            $instrumentsDb = new Application_Model_DbTable_Instruments();
            $headerInstrumentSerials = $params['headerInstrumentSerial'];
            foreach ($headerInstrumentSerials as $key => $headerInstrumentSerial) {
                if (isset($headerInstrumentSerial) &&
                    $headerInstrumentSerial != "") {
                    $headerInstrumentDetails = array(
                        'instrument_serial' => $headerInstrumentSerial,
                        'instrument_installed_on' => Application_Service_Common::ParseDate($params['headerInstrumentInstalledOn'][$key]),
                        'instrument_last_calibrated_on' => Application_Service_Common::ParseDate($params['headerInstrumentLastCalibratedOn'][$key])
                    );
                    $instrumentsDb->upsertInstrument($params['participantId'], $headerInstrumentDetails);
                }
            }
            $db->commit();
            return "";
        } catch (Exception $e) {
            $db->rollBack();
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            return "An unexpected error occurred when trying to submit these results.";
        }
    }

    public function addShipment($params) {
        $authNameSpace = new Zend_Session_Namespace('administrators');
        $db = new Application_Model_DbTable_Shipments();
        $distroService = new Application_Service_Distribution();
        $distro = $distroService->getDistribution($params['distribution']);
		$controlCount = 0;
		if (isset($params['control'])) {
            foreach ($params['control'] as $control) {
                if ($control == 1) {
                    $controlCount+=1;
                }
            }
        }
        $data = array(
            'shipment_code' => $params['shipmentCode'],
            'distribution_id' => $params['distribution'],
            'scheme_type' => 'tb',
            'shipment_date' => $distro['distribution_date'],
            'number_of_samples' => count($params['sampleName']) - $controlCount,
			'number_of_controls' => $controlCount,
            'lastdate_response' => Application_Service_Common::ParseDate($params['lastDate']),
            'created_on_admin' => new Zend_Db_Expr('now()'),
            'created_by_admin' => $authNameSpace->primary_email
        );
        if (isset($params['isFollowUp'])) {
            $data['follows_up_from'] = $params['followsUpFrom'];
        }
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $lastId = $db->insert($data);
        if (isset($params['autoEnroll'])) {
            $participantService = new Application_Service_Participants();
            $previouslyEnrolledParticipants = $participantService->getEnrolledByShipmentId($params['followsUpFrom']);
            $enrollmentTable = new Application_Model_DbTable_ShipmentParticipantMap();
            $enrollmentTable->shipItNow(array(
                'shipmentId' => $lastId,
                'participants' => array_map(create_function('$p', 'return $p["participant_id"];'), $previouslyEnrolledParticipants)
            ));
        }
        $size = count($params['sampleName']);
            for ($i = 0; $i < $size; $i++) {
                $dbAdapter->insert('reference_result_tb', array(
                        'shipment_id' => $lastId,
                        'sample_id' => ($i + 1),
                        'sample_label' => $params['sampleName'][$i],
                        'sample_content' => $params['sampleContent'][$i],
                        'mtb_rif_mtb_detected' => $params['mtbDetectedMtbRif'][$i],
                        'mtb_rif_rif_resistance' => $params['rifResistanceMtbRif'][$i],
                        'mtb_rif_probe_d' => $params['probeMtbRifD'][$i],
                        'mtb_rif_probe_c' => $params['probeMtbRifC'][$i],
                        'mtb_rif_probe_e' => $params['probeMtbRifE'][$i],
                        'mtb_rif_probe_b' => $params['probeMtbRifB'][$i],
                        'mtb_rif_probe_spc' => $params['probeMtbRifSpc'][$i],
                        'mtb_rif_probe_a' => $params['probeMtbRifA'][$i],
                        'ultra_mtb_detected' => $params['mtbDetectedUltra'][$i],
                        'ultra_rif_resistance' => $params['rifResistanceUltra'][$i],
                        'ultra_probe_spc' => $params['probeUltraSpc'][$i],
                        'ultra_probe_is1081_is6110' => $params['probeUltraIS1081IS6110'][$i],
                        'ultra_probe_rpo_b1' => $params['probeUltraRpoB1'][$i],
                        'ultra_probe_rpo_b2' => $params['probeUltraRpoB2'][$i],
                        'ultra_probe_rpo_b3' => $params['probeUltraRpoB3'][$i],
                        'ultra_probe_rpo_b4' => $params['probeUltraRpoB4'][$i],
                        'control' => 0,
                        'mandatory' => 1,
                        'sample_score' => Application_Service_EvaluationScoring::SAMPLE_MAX_SCORE
                    )
                );
            }
        if (!isset($params['autoEnroll'])) {
            $distroService->updateDistributionStatus($params['distribution'], 'pending');
        }
    }

    public function addShipmentAgainstDitribution($params,$distribution) {
        $authNameSpace = new Zend_Session_Namespace('administrators');
        $db = new Application_Model_DbTable_Shipments();

        $distroService = new Application_Service_Distribution();
        $distro = $distroService->getDistribution($distribution);

        $controlCount = 0;
        if (isset($params['control'])) {
            foreach ($params['control'] as $control) {
                if ($control == 1) {
                    $controlCount+=1;
                }
            }
        }
        $data = array(
            'shipment_code' => $params['shipmentCode'],
            'distribution_id' => $distro['distribution_id'],
            "cs_survey" => empty(trim($params["cs_survey"])) ? null : trim($params["cs_survey"]),
            'scheme_type' => 'tb',
            'shipment_date' => $distro['distribution_date'],
            'number_of_samples' => count($params['sampleName']) - $controlCount,
            'number_of_controls' => $controlCount,
            'lastdate_response' => Application_Service_Common::ParseDate($params['lastDate']),
            'is_official' => $params['isOfficial'] == 'yes' ? 1 : 0,
            'created_on_admin' => new Zend_Db_Expr('now()'),
            'created_by_admin' => $authNameSpace->primary_email
        );
        if (isset($params['isFollowUp'])) {
            $data['follows_up_from'] = $params['followsUpFrom'];
        }
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $lastId = $db->insert($data);
        if (isset($params['autoEnroll'])) {
            $participantService = new Application_Service_Participants();
            $previouslyEnrolledParticipants = $participantService->getEnrolledByShipmentId($params['followsUpFrom']);
            $enrollmentTable = new Application_Model_DbTable_ShipmentParticipantMap();
            $enrollmentTable->shipItNow(array(
                'shipmentId' => $lastId,
                'participants' => array_map(create_function('$p', 'return $p["participant_id"];'), $previouslyEnrolledParticipants)
            ));
        }
        $size = count($params['sampleName']);
        for ($i = 0; $i < $size; $i++) {
            $dbAdapter->insert('reference_result_tb', array(
                    'shipment_id' => $lastId,
                    'sample_id' => ($i + 1),
                    'sample_label' => $params['sampleName'][$i],
                    'sample_content' => $params['sampleContent'][$i],
                    'mtb_rif_mtb_detected' => $params['mtbDetectedMtbRif'][$i],
                    'mtb_rif_rif_resistance' => $params['rifResistanceMtbRif'][$i],
                    'mtb_rif_probe_d' => $params['probeMtbRifD'][$i],
                    'mtb_rif_probe_c' => $params['probeMtbRifC'][$i],
                    'mtb_rif_probe_e' => $params['probeMtbRifE'][$i],
                    'mtb_rif_probe_b' => $params['probeMtbRifB'][$i],
                    'mtb_rif_probe_spc' => $params['probeMtbRifSpc'][$i],
                    'mtb_rif_probe_a' => $params['probeMtbRifA'][$i],
                    'ultra_mtb_detected' => $params['mtbDetectedUltra'][$i],
                    'ultra_rif_resistance' => $params['rifResistanceUltra'][$i],
                    'ultra_probe_spc' => $params['probeUltraSpc'][$i],
                    'ultra_probe_is1081_is6110' => $params['probeUltraIS1081IS6110'][$i],
                    'ultra_probe_rpo_b1' => $params['probeUltraRpoB1'][$i],
                    'ultra_probe_rpo_b2' => $params['probeUltraRpoB2'][$i],
                    'ultra_probe_rpo_b3' => $params['probeUltraRpoB3'][$i],
                    'ultra_probe_rpo_b4' => $params['probeUltraRpoB4'][$i],
                    'control' => 0,
                    'mandatory' => 1,
                    'sample_score' => Application_Service_EvaluationScoring::SAMPLE_MAX_SCORE
                )
            );
        }
        if (!isset($params['autoEnroll'])) {
            $distroService->updateDistributionStatus($distro['distribution_id'], 'pending');
        }
    }

    public function getShipment($sid) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $shipment = $db->fetchRow($db->select()->from(array('s' => 'shipment'))
            ->joinLeft(array("sp" => "shipment_participant_map"), "sp.shipment_id=s.shipment_id", array(
                "last_submission_updated_on" => new Zend_Db_Expr("GREATEST(MAX(sp.updated_on_admin), MAX(sp.updated_on_user))")
            ))
            ->where("s.shipment_id = ?", $sid)
            ->group("s.shipment_id"));
        if ($shipment['status'] == 'evaluated' && $shipment['last_submission_updated_on'] > $shipment['updated_on_admin']) {
            $shipment['status'] = "Should be Re-Evaluated";
        }
        return $shipment;
    }

    public function shipItNow($params) {
        $db = new Application_Model_DbTable_ShipmentParticipantMap();
        return $db->shipItNow($params);
    }

    public function removeShipment($sid) {
        try {
            $shipmentDb = new Application_Model_DbTable_Shipments();
            $relatedDistribution=new Application_Model_DbTable_Distribution();
            $row = $shipmentDb->fetchRow('shipment_id=' . $sid);
            $did=$row['distribution_id'];
            $distribution=$relatedDistribution->delete('distribution_id='.$did);

            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            if ($row['scheme_type'] == 'dts') {
                $db->delete('reference_dts_eia', 'shipment_id=' . $sid);
                $db->delete('reference_dts_wb', 'shipment_id=' . $sid);
                $db->delete("reference_result_dts", 'shipment_id=' . $sid);
            } else if ($row['scheme_type'] == 'dbs') {
                $db->delete('reference_dbs_eia', 'shipment_id=' . $sid);
                $db->delete('reference_dbs_wb', 'shipment_id=' . $sid);
                $db->delete("reference_result_dbs", 'shipment_id=' . $sid);
            } else if ($row['scheme_type'] == 'eid') {
                $db->delete("reference_result_eid", 'shipment_id=' . $sid);
            } else if ($row['scheme_type'] == 'tb') {
                $db->delete("reference_result_tb", 'shipment_id=' . $sid);
            }

            $shipmentParticipantMap = new Application_Model_DbTable_ShipmentParticipantMap();
            $shipmentParticipantMap->delete('shipment_id=' . $sid);
            $shipmentDb->delete('shipment_id=' . $sid);
            return "";
        } catch (Exception $e) {
            return($e->getMessage());
            return "c Unable to delete. Please try again later or contact system admin for help";
        }
    }

    public function isShipmentEditableToDataManager($shipmentId=NULL) {
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        if ($authNameSpace->view_only_access=='yes') {
            return false;
        }

        $spMap = new Application_Model_DbTable_ShipmentParticipantMap();
        return $spMap->isShipmentEditable($shipmentId, false);
    }

    public function isShipmentEditable($shipmentId,$isAdministrator) {
		$spMap = new Application_Model_DbTable_ShipmentParticipantMap();
        return $spMap->isShipmentEditable($shipmentId, $isAdministrator);
    }

    public function checkParticipantAccess($participantId) {
        $participantDb = new Application_Model_DbTable_Participants();
        return $participantDb->checkParticipantAccess($participantId);
    }

    public function getShipmentForEdit($sid) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $shipment = $db->fetchRow($db->select()->from(array('s' => 'shipment'))
            ->join(array('d' => 'distributions'), 'd.distribution_id = s.distribution_id', array('distribution_code', 'distribution_date'))
            ->where("s.shipment_id = ?", $sid));

		$returnArray = array();

        $reference = $db->fetchAll($db->select()->from(array('s' => 'shipment'))
            ->join(array('ref' => 'reference_result_tb'), 'ref.shipment_id=s.shipment_id')
            ->where("s.shipment_id = ?", $sid));
        $possibleResults = "";

		$returnArray['shipment'] = $shipment;
		$returnArray['reference'] = $reference;
		$returnArray['possibleResults'] = $possibleResults;

        return $returnArray;
    }

    public function getDetailsForSubmissionForms($sid) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $shipmentData = $db->fetchRow(
            $db->select()
                ->from(array('s' => 'shipment'), array('s.shipment_code', 's.lastdate_response'))
                ->where("s.shipment_id = ?", $sid)
        );
        $participantDataQuery = $db->select()
            ->from(array('spm' => 'shipment_participant_map'), array())
            ->join(array('s' => 'shipment'), 's.shipment_id = spm.shipment_id', array())
            ->join(array('p' => 'participant'), 'p.participant_id = spm.participant_id',
                array(
                    "country" => "p.country",
                    "participant_name" => "p.lab_name",
                    "pt_id" => "p.unique_identifier",
                    'sorting_unique_identifier' => new Zend_Db_Expr("LPAD(p.unique_identifier, 10, '0')")
                ))
            ->joinLeft(array('pmm' => 'participant_manager_map'), 'pmm.participant_id = spm.participant_id', array())
            ->joinLeft(array('dm' => 'data_manager'), 'dm.dm_id = pmm.dm_id', array(
                "username" => "dm.primary_email",
                "dm.password"
            ))
            ->joinLeft(array('csm' => 'country_shipment_map'), 'csm.country_id = p.country AND csm.shipment_id = spm.shipment_id', array('due_date' => new Zend_Db_Expr('IFNULL(csm.due_date_text, CAST(s.lastdate_response AS CHAR))')))
            ->where("spm.shipment_id = ?", $sid)
            ->group('p.participant_id')
            ->order('sorting_unique_identifier');
        $participantData = $db->fetchAll($participantDataQuery);

        $sampleData = $db->fetchAll(
            $db->select()
                ->from(array('ref' => 'reference_result_tb'), array('ref.sample_id', 'ref.sample_label'))
                ->where("ref.shipment_id = ?", $sid)
                ->order("ref.sample_id ASC")
        );

        $countryData = $db->fetchAll(
            $db->select()
                ->from(array('c' => 'countries'), array(
                    "c.id",
                    "country_name" => "c.iso_name"
                ))
                ->joinLeft('ptcc_country_map', 'ptcc_country_map.country_id = c.id AND ptcc_country_map.show_details_on_report = 1', array())
                ->joinLeft(array('admin' => 'system_admin'), "admin.admin_id = ptcc_country_map.admin_id AND admin.include_as_pecc_in_reports = 1 AND admin.status = 'active'", array(
                    "pecc_details" => new Zend_Db_Expr("GROUP_CONCAT(CONCAT(COALESCE(CONCAT(admin.first_name,' ',admin.last_name),admin.first_name,admin.last_name),IFNULL(CONCAT(' (',admin.primary_email, ')'),'')))")
                ))
                ->group('c.id')
                ->order("c.id ASC")
        );
        $countryMap = array();
        foreach ($countryData as $countryRecord) {
            $countryMap[$countryRecord['id']] = $countryRecord;
        }

        return array(
            "shipment" => $shipmentData,
            "country" => $countryMap,
            "participant" => $participantData,
            "sample" => $sampleData
        );
    }

    public function updateShipment($params) {

        if (!empty(trim($params["cs_survey"]))) {
            $json_decoded = json_decode($params["cs_survey"]);
            if (0 !== json_last_error()) {
                $sessionAlert = new Zend_Session_Namespace('alertSpace');
                $sessionAlert->message = "The survey data is malformed";
                $sessionAlert->status = "failure";
                return false;
            }
        }

        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $shipmentRow = $dbAdapter->fetchRow($dbAdapter->select()
            ->from(array("s" => "shipment"))
            ->where("shipment_id = " . $params["shipmentId"]));
        $size = count($params["sampleName"]);
		$controlCount = 0;
		if (isset($params["control"])) {
            foreach ($params["control"] as $control) {
                if ($control == 1) {
                    $controlCount += 1;
                }
            }
        }

        $existingSamples = $dbAdapter->fetchAll($dbAdapter->select()
            ->from("reference_result_tb")
            ->where("shipment_id = " . $params["shipmentId"]));
        $existingSampleMap = array();
        foreach ($existingSamples as $existingResult) {
            $existingSampleMap[$existingResult["sample_id"]] = array(
                "mtb_rif_mtb_detected" => $existingResult["mtb_rif_mtb_detected"],
                "mtb_rif_rif_resistance" => $existingResult["mtb_rif_rif_resistance"],
                "ultra_mtb_detected" => $existingResult["ultra_mtb_detected"],
                "ultra_rif_resistance" => $existingResult["ultra_rif_resistance"],
                "mtb_rif_is_excluded" => $existingResult["mtb_rif_is_excluded"],
                "mtb_rif_is_exempt" => $existingResult["mtb_rif_is_exempt"],
                "ultra_is_excluded" => $existingResult["ultra_is_excluded"],
                "ultra_is_exempt" => $existingResult["ultra_is_exempt"],
                "excluded_reason" => $existingResult["excluded_reason"]
            );
        }
        $dbAdapter->delete("reference_result_tb", "shipment_id = " . $params["shipmentId"]);
        $rescoringNecessary = false;
        $maxShipmentScore = 0;
        $newSampleMap = array();
        for ($i = 0; $i < $size; $i++) {
            $sampleId = strval($i + 1);
            $newSampleMap[$sampleId] = array(
                "mtb_rif_mtb_detected" => $params["mtbDetectedMtbRif"][$i],
                "mtb_rif_rif_resistance" => $params["rifResistanceMtbRif"][$i],
                "ultra_mtb_detected" => $params["mtbDetectedUltra"][$i],
                "ultra_rif_resistance" => $params["rifResistanceUltra"][$i],
                "mtb_rif_is_excluded" => $params["mtb_rif_excluded"][$i] == "yes_not_exempt" || $params["mtb_rif_excluded"][$i] == "yes_exempt" ? "yes" : "no",
                "mtb_rif_is_exempt" => $params["mtb_rif_excluded"][$i] == "yes_exempt" ? "yes" : "no",
                "ultra_is_excluded" => $params["ultra_excluded"][$i] == "yes_not_exempt" || $params["ultra_excluded"][$i] == "yes_exempt" ? "yes" : "no",
                "ultra_is_exempt" => $params["ultra_excluded"][$i] == "yes_exempt" ? "yes" : "no",
                "excluded_reason" => $params["excludedReason"][$i]
            );
            if (!isset($existingSampleMap[$sampleId]) ||
                $existingSampleMap[$sampleId]["mtb_rif_mtb_detected"] != $newSampleMap[$sampleId]["mtb_rif_mtb_detected"] ||
                $existingSampleMap[$sampleId]["mtb_rif_rif_resistance"] != $newSampleMap[$sampleId]["mtb_rif_rif_resistance"] ||
                $existingSampleMap[$sampleId]["ultra_mtb_detected"] != $newSampleMap[$sampleId]["ultra_mtb_detected"] ||
                $existingSampleMap[$sampleId]["ultra_rif_resistance"] != $newSampleMap[$sampleId]["ultra_rif_resistance"] ||
                $existingSampleMap[$sampleId]["mtb_rif_is_excluded"] != $newSampleMap[$sampleId]["mtb_rif_is_excluded"] ||
                $existingSampleMap[$sampleId]["mtb_rif_is_exempt"] != $newSampleMap[$sampleId]["mtb_rif_is_exempt"] ||
                $existingSampleMap[$sampleId]["ultra_is_excluded"] != $newSampleMap[$sampleId]["ultra_is_excluded"] ||
                $existingSampleMap[$sampleId]["ultra_is_exempt"] != $newSampleMap[$sampleId]["ultra_is_exempt"] ||
                $existingSampleMap[$sampleId]["excluded_reason"] != $newSampleMap[$sampleId]["excluded_reason"]) {
                $rescoringNecessary = true;
            }
            $dbAdapter->insert('reference_result_tb', array(
                "shipment_id" => $params["shipmentId"],
                "sample_id" => $sampleId,
                "sample_label" => $params["sampleName"][$i],
                "sample_content" => $params["sampleContent"][$i],
                "mtb_rif_mtb_detected" => $newSampleMap[$sampleId]["mtb_rif_mtb_detected"],
                "mtb_rif_rif_resistance" => $newSampleMap[$sampleId]["mtb_rif_rif_resistance"],
                "mtb_rif_probe_d" => $params["probeMtbRifD"][$i],
                "mtb_rif_probe_c" => $params["probeMtbRifC"][$i],
                "mtb_rif_probe_e" => $params["probeMtbRifE"][$i],
                "mtb_rif_probe_b" => $params["probeMtbRifB"][$i],
                "mtb_rif_probe_spc" => $params["probeMtbRifSpc"][$i],
                "mtb_rif_probe_a" => $params["probeMtbRifA"][$i],
                "ultra_mtb_detected" => $newSampleMap[$sampleId]["ultra_mtb_detected"],
                "ultra_rif_resistance" => $newSampleMap[$sampleId]["ultra_rif_resistance"],
                "ultra_probe_spc" => $params["probeUltraSpc"][$i],
                "ultra_probe_is1081_is6110" => $params["probeUltraIS1081IS6110"][$i],
                "ultra_probe_rpo_b1" => $params["probeUltraRpoB1"][$i],
                "ultra_probe_rpo_b2" => $params["probeUltraRpoB2"][$i],
                "ultra_probe_rpo_b3" => $params["probeUltraRpoB3"][$i],
                "ultra_probe_rpo_b4" => $params["probeUltraRpoB4"][$i],
                "control" => 0,
                "mandatory" => 1,
                "sample_score" => Application_Service_EvaluationScoring::SAMPLE_MAX_SCORE,
                "mtb_rif_is_excluded" => $newSampleMap[$sampleId]["mtb_rif_is_excluded"],
                "mtb_rif_is_exempt" => $newSampleMap[$sampleId]["mtb_rif_is_exempt"],
                "ultra_is_excluded" => $newSampleMap[$sampleId]["ultra_is_excluded"],
                "ultra_is_exempt" => $newSampleMap[$sampleId]["ultra_is_exempt"],
                "excluded_reason" => $newSampleMap[$sampleId]["excluded_reason"]
            ));
            if ($newSampleMap[$sampleId]["mtb_rif_is_excluded"] == "no" ||
                $newSampleMap[$sampleId]["mtb_rif_is_exempt"] == "yes" ||
                $newSampleMap[$sampleId]["ultra_is_excluded"] == "no" ||
                $newSampleMap[$sampleId]["ultra_is_exempt"] == "yes") {
                $maxShipmentScore += Application_Service_EvaluationScoring::SAMPLE_MAX_SCORE;
            }
        }
        $authNameSpace = new Zend_Session_Namespace('administrators');
        $admin = $authNameSpace->primary_email;
        if ($rescoringNecessary) {
            $assayRecords = $dbAdapter->fetchAll($dbAdapter->select()->from("r_tb_assay"));
            $assays = array();
            foreach ($assayRecords as $assayRecord) {
                $assays[$assayRecord["id"]] = $assayRecord["short_name"];
            }
            $scoredSubmissions = $dbAdapter->fetchAll($dbAdapter->select()
                ->from("shipment_participant_map")
                ->where("shipment_score is not null")
                ->where("shipment_id = ".$params["shipmentId"]));
            $schemeService = new Application_Service_Schemes();
            $scoringService = new Application_Service_EvaluationScoring();
            $samplePassStatuses = array();
            foreach ($scoredSubmissions as $scoredSubmission) {
                $finalResult = $scoredSubmission["final_result"];
                $sampleRes = $schemeService->getTbSamples($params["shipmentId"],
                    $scoredSubmission["participant_id"]);
                $submissionShipmentScore = 0;
                $failureReason = array();
                $hasBlankResult = false;
                $assayName = "Unspecified";
                if (isset($attributes["assay"]) && $attributes["assay"] != '' && array_key_exists($attributes["assay"], $assays)) {
                    $assayName = $assays[$attributes["assay"]];
                }
                for ($i = 0; $i < count($sampleRes); $i++) {
                    $sampleId = $sampleRes[$i]["sample_id"];
                    $samplePassStatus = $scoringService->calculateTbSamplePassStatus(
                        $newSampleMap[$sampleId][$assayName == "MTB Ultra" ? 'ultra_mtb_detected' : "mtb_rif_mtb_detected"], $sampleRes[$i]["res_mtb_detected"],
                        $newSampleMap[$sampleId][$assayName == "MTB Ultra" ? "ultra_rif_resistance" : "mtb_rif_rif_resistance"], $sampleRes[$i]["res_rif_resistance"],
                        $sampleRes[$i]["res_probe_1"], $sampleRes[$i]["res_probe_2"],
                        $sampleRes[$i]["res_probe_3"], $sampleRes[$i]["res_probe_4"], $sampleRes[$i]["res_probe_5"],
                        $sampleRes[$i]["res_probe_6"], $newSampleMap[$sampleId][$assayName == "MTB Ultra" ? "ultra_is_excluded" : "mtb_rif_is_excluded"],
                        $newSampleMap[$sampleId][$assayName == "MTB Ultra" ? "ultra_is_exempt" : "mtb_rif_is_exempt"]);
                    $submissionShipmentScore += $scoringService->calculateTbSampleScore(
                        $samplePassStatus,
                        $sampleRes[$i]["ref_sample_score"]);
                    array_push($samplePassStatuses, $samplePassStatus);
                    $hasBlankResult = $hasBlankResult || !isset($sampleRes[$i]["res_mtb_detected"]);
                }
                if(!isset($scoredSubmission["attributes"]) || $scoredSubmission["attributes"] == "") {
                    $scoredSubmission["attributes"] = "{}";
                }
                $attributes = json_decode($scoredSubmission["attributes"],true);
                $shipmentData = array(
                    "updated_by_admin" => $admin,
                    "updated_on_admin" => new Zend_Db_Expr("now()")
                );
                $shipmentData["shipment_score"] = $submissionShipmentScore;
                $shipmentData["documentation_score"] = $scoringService->calculateTbDocumentationScore($shipmentRow["shipment_date"],
                    $attributes["expiry_date"], $scoredSubmission["shipment_receipt_date"], $scoredSubmission["supervisor_approval"],
                    $scoredSubmission["participant_supervisor"], $shipmentRow["lastdate_response"]);
                $submissionPassStatus = $scoringService->calculateSubmissionPassStatus(
                    $submissionShipmentScore, $shipmentData["documentation_score"], $maxShipmentScore,
                    $samplePassStatuses);
                if ($scoredSubmission["is_excluded"] == "yes") {
                    $finalResult = 3;
                    $failureReason[] = array("warning" => "Excluded from Evaluation");
                } else if ($hasBlankResult) {
                    $failureReason[]["warning"] = "Could not determine score. Not enough responses found in the submission.";
                    $finalResult = 4;
                } else if ($submissionPassStatus == "fail") {
                    $totalScore = $shipmentData['shipment_score'] + $shipmentData['documentation_score'];
                    $maxTotalScore = $maxShipmentScore + Application_Service_EvaluationScoring::MAX_DOCUMENTATION_SCORE;
                    $failureReason[]["warning"] = "Participant did not meet the score criteria (Participant Score - <strong>$totalScore</strong> out of <strong>$maxTotalScore</strong>)";
                    $finalResult = 2;
                } else if ($submissionPassStatus == "pass") {
                    $finalResult = 1;
                }
                $shipmentData["failure_reason"] = json_encode($failureReason);
                $shipmentData["final_result"] = $finalResult;
                $dbAdapter->update(
                    "shipment_participant_map",
                    $shipmentData,
                    "map_id = " . $scoredSubmission["map_id"]
                );
            }
        }

        $dbAdapter->update("shipment", array(
            "number_of_samples" => $size - $controlCount,
            "number_of_controls" => $controlCount,
            "cs_survey" => empty(trim($params["cs_survey"])) ? null : trim($params["cs_survey"]),
			"shipment_code" => $params["shipmentCode"],
			"lastdate_response" => Application_Service_Common::ParseDate($params["lastDate"]),
            "is_official" => $params["isOfficial"] == "yes" ? 1 : 0,
            "updated_by_admin" => $admin,
            "updated_on_admin" => new Zend_Db_Expr("now()")
        ), "shipment_id = " . $params["shipmentId"]);
        return true;
    }

    public function receiveShipment($params) {
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        $evaluationStatusRow = $dbAdapter->fetchRow(
            $dbAdapter->select()
                ->from(array('spm' => 'shipment_participant_map'), array('evaluation_status'))
                ->where('shipment_id = ' . $params['shipment_id'] . ' AND participant_id = '. $params['participant_id']));
        $evaluationStatus = $evaluationStatusRow['evaluation_status'];
        $evaluationStatus[1] = '1';
        $dbAdapter->update("shipment_participant_map",
            array(
                "shipment_receipt_date" => $params["shipment_receipt_date"],
                "evaluation_status" => $evaluationStatus,
                "updated_by_user" => $authNameSpace->dm_id,
                "updated_on_user" => new Zend_Db_Expr('now()')
            ),
            "shipment_id = " . $params["shipment_id"] . " AND participant_id = ". $params["participant_id"]);
    }

    public function getShipmentOverview($parameters) {
        $shipmentDb = new Application_Model_DbTable_Shipments();
        return $shipmentDb->getShipmentOverviewDetails($parameters);
    }

    public function getShipmentCurrent($parameters) {
        $shipmentDb = new Application_Model_DbTable_Shipments();
        return $shipmentDb->getShipmentCurrentDetails($parameters);
    }

    public function getShipmentDefault($parameters) {
        $shipmentDb = new Application_Model_DbTable_Shipments();
        return $shipmentDb->getShipmentDefaultDetails($parameters);
    }

    public function getShipmentAll($parameters) {
        $shipmentDb = new Application_Model_DbTable_Shipments();
        return $shipmentDb->getShipmentAllDetails($parameters);
    }

    public function getindividualReport($parameters) {
        $shipmentDb = new Application_Model_DbTable_Shipments();
        return $shipmentDb->getindividualReportDetails($parameters);
    }

    public function getSummaryReport($parameters) {
        $shipmentDb = new Application_Model_DbTable_Shipments();
        return $shipmentDb->getSummaryReportDetails($parameters);
    }

    public function getShipmentInReports($distributionId) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->select()->from(array("s" => "shipment", array("shipment_id", "shipment_code", "status", "number_of_samples", "s.updated_on_admin")))
            ->join(array("d" => "distributions"), "d.distribution_id=s.distribution_id", array(
                "distribution_code", "distribution_date"))
            ->joinLeft(array("sp" => "shipment_participant_map"), "sp.shipment_id=s.shipment_id", array(
                "report_generated",
                "participant_count" => new Zend_Db_Expr("count(sp.participant_id)"),
                "reported_count" => new Zend_Db_Expr("COUNT(CASE substr(sp.evaluation_status,4,1) WHEN '1' THEN 1 WHEN '2' THEN 1 END)"),
                "number_passed" => new Zend_Db_Expr("SUM(final_result = 1)"),
                "last_submission_updated_on" => new Zend_Db_Expr("GREATEST(MAX(sp.updated_on_admin), MAX(sp.updated_on_user))")
            ))
            ->join(array("sl" => "scheme_list"), "sl.scheme_id=s.scheme_type", array("scheme_name"))
            ->joinLeft(array("rr" => "r_results"), "sp.final_result=rr.result_id")
            ->where("s.distribution_id = ?", $distributionId)
            ->group("s.shipment_id");
        $rResult = $db->fetchAll($sql);
        for ($i = 0; $i < count($rResult); $i++) {
            if ($rResult[$i]['status'] == 'evaluated' && $rResult[$i]['last_submission_updated_on'] > $rResult[$i]['updated_on_admin']) {
                $rResult[$i]['status'] = "Should be Re-Evaluated";
            }
        }
        return $rResult;
    }

    public function getParticipantCountBasedOnScheme() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $sQuery = $db->select()->from(array('s' => 'shipment'), array())
            ->join(array('sp' => 'shipment_participant_map'), 'sp.shipment_id=s.shipment_id', array('participantCount' => new Zend_Db_Expr("count(sp.participant_id)")))
            ->join(array('sl' => 'scheme_list'), 'sl.scheme_id=s.scheme_type', array('SCHEME' => 'sl.scheme_id'))
            ->where("s.scheme_type = sl.scheme_id")
            ->where("s.status!='pending'")
            ->group('s.scheme_type')
            ->order("sl.scheme_id");
        $resultArray = $db->fetchAll($sQuery);
        return $resultArray;
    }

    public function getParticipantCountBasedOnShipment() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $sQuery = $db->select()
            ->from(array('s' => 'shipment'), array('s.shipment_code', 's.scheme_type', 's.lastdate_response'))
            ->join(array('spm' => 'shipment_participant_map'),'spm.shipment_id = s.shipment_id',
                array(
                    'participantCount' => new Zend_Db_Expr("COUNT(spm.participant_id)"),
                    'receivedCount' => new Zend_Db_Expr("SUM(spm.shipment_receipt_date IS NOT NULL)"),
                    'respondedCount' => new Zend_Db_Expr("SUM(SUBSTR(spm.evaluation_status, 3, 1) = '1')")
                ))
            ->join(array('p' => 'participant'),'spm.participant_id = p.participant_id', array())
            ->where("s.status IN ('shipped', 'evaluated')")
            ->where("s.shipment_date > DATE_SUB(now(), INTERVAL 24 MONTH)");
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if ($authNameSpace->is_ptcc_coordinator) {
            $sQuery = $sQuery->where("p.country IN (".implode(",", $authNameSpace->countries).")");
        }
        $sQuery = $sQuery->group('s.shipment_id')
            ->order("s.shipment_id DESC");
        return $db->fetchAll($sQuery);
    }

    public function removeShipmentParticipant($mapId) {
        try {
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            return  $db->delete('shipment_participant_map', "map_id = " . $mapId);
        } catch (Exception $e) {
            return($e->getMessage());
            return "Unable to delete. Please try again later or contact system admin for help";
        }
    }

    public function addEnrollements($params) {
        $db = new Application_Model_DbTable_ShipmentParticipantMap();
        return $db->addEnrollementDetails($params);
    }

    public function getShipmentCode($sid) {
        $code = '';
        $month = date("m");
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sQuery = $db->select()->from('shipment')->where("scheme_type = ?", $sid)->where("MONTH(DATE(created_on_admin))= ?", $month);
        $resultArray = $db->fetchAll($sQuery);
        $year = date("y");
        $count = count($resultArray) + 1;
        if ($sid == 'dts') {
            $code = 'DTS' . $month . $year . '-' . $count;
        }
        else if ($sid == 'eid') {
            $code = 'EID' . $month . $year . '-' . $count;
        } else if ($sid == 'dbs') {
            $code = 'DBS' . $month . $year . '-' . $count;
        }
        return $this->checkShipmentCode($month, $year, $count, $sid);
    }

    public function checkShipmentCode($month, $year, $count, $sid) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $code = '';
        if ($sid == 'dts') {
            $code = 'DTS' . $month . $year . '-' . $count;
        } else if ($sid == 'eid') {
            $code = 'EID' . $month . $year . '-' . $count;
        } else if ($sid == 'dbs') {
            $code = 'DBS' . $month . $year . '-' . $count;
        }
        $sQuery = $db->select()->from('shipment')->where("shipment_code = ?", $code);
        $resultArray = $db->fetchAll($sQuery);
        if (count($resultArray) > 0) {
            $count++;
            if ($sid == 'dts') {
                $code = 'DTS' . $month . $year . '-' . $count;
            } else if ($sid == 'eid') {
                $code = 'EID' . $month . $year . '-' . $count;
            } else if ($sid == 'dbs') {
                $code = 'DBS' . $month . $year . '-' . $count;
            } else {
                $code = '';
            }
            $this->checkShipmentCode($month, $year, $count, $sid);
        }
        return $code;
    }

    public function getShipmentReport($parameters) {
        $shipmentDb = new Application_Model_DbTable_Shipments();
        return $shipmentDb->getShipmentReportDetails($parameters);
    }

    public function getShipmentParticipants($sid) {
        $commonServices = new Application_Service_Common();
        $general = new Pt_Commons_General();
        $newShipmentMailContent = $commonServices->getEmailTemplate('new_shipment');
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $return = 0;
        $sQuery = $db->select()->from(array('sp' => 'shipment_participant_map'), array('sp.participant_id','sp.map_id','sp.new_shipment_mail_count'))
            ->join(array('s' => 'shipment'), 's.shipment_id=sp.shipment_id', array('s.shipment_code','s.shipment_code'))
            ->join(array('d' => 'distributions'), 'd.distribution_id = s.distribution_id', array('distribution_code', 'distribution_date'))
            ->join(array('p' => 'participant'), 'p.participant_id=sp.participant_id', array('p.email','participantName' => new Zend_Db_Expr("GROUP_CONCAT(DISTINCT p.unique_identifier,\" - \",p.lab_name ORDER BY p.lab_name SEPARATOR ', ')")))
            ->join(array('sl' => 'scheme_list'), 'sl.scheme_id=s.scheme_type', array('SCHEME' => 'sl.scheme_name'))
            ->where("sp.shipment_id = ?", $sid)
            ->group("p.participant_id");
        $participantEmails=$db->fetchAll($sQuery);
        foreach($participantEmails as $participantDetails){
            if ($participantDetails['email']!='') {
                $surveyDate=$general->humanDateFormat($participantDetails['distribution_date']);
                $search = array('##NAME##','##SHIPCODE##','##SHIPTYPE##','##SURVEYCODE##','##SURVEYDATE##',);
                $replace = array($participantDetails['participantName'],$participantDetails['shipment_code'],$participantDetails['SCHEME'],$participantDetails['distribution_code'],$surveyDate);
                $content = $newShipmentMailContent['mail_content'];
                $message = str_replace($search, $replace, $content);
                $subject = $newShipmentMailContent['mail_subject'];
                $fromEmail =$newShipmentMailContent['mail_from'];
                $fromFullName = $newShipmentMailContent['from_name'];
                $toEmail =$participantDetails['email'];
                $cc=$newShipmentMailContent['mail_cc'];
                $bcc=$newShipmentMailContent['mail_bcc'];
                $commonServices->insertTempMail($toEmail,$cc,$bcc, $subject, $message, $fromEmail, $fromFullName);
                $count=$participantDetails['new_shipment_mail_count']+1;
                $return=$db->update("shipment_participant_map", array(
                        "last_new_shipment_mailed_on" => new Zend_Db_Expr("now()"),
                        "new_shipment_mail_count" => $count
                ), "map_id = " . $participantDetails['map_id']);
            }
        }
        return $return;
    }

    public function getShipmentCountries($sid) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sQuery = $db->select()->from(array('s' => 'shipment'), array('s.shipment_id'))
            ->join(array('spm' => 'shipment_participant_map'), 'spm.shipment_id = s.shipment_id', array())
            ->join(array('p' => 'participant'), 'p.participant_id = spm.participant_id', array())
            ->join(array('c' => 'countries'), 'c.id = p.country', array('country_id' => 'c.id', 'country_name' => 'c.iso_name'))
            ->joinLeft(
                array('csm' => 'country_shipment_map'),
                'csm.country_id = c.id AND csm.shipment_id = s.shipment_id',
                array('due_date' => new Zend_Db_Expr('IFNULL(csm.due_date_text, CAST(s.lastdate_response AS CHAR))'))
            )
            ->where("s.shipment_id = ?", $sid)
            ->group("c.id")
            ->order('c.iso_name ASC');
        return $db->fetchAll($sQuery);
    }

    public function updateShipmentCountry($sid, $countryId, $dueDateText) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sQuery = $db->select()
            ->from(array('s' => 'shipment'), array(
                's.shipment_id',
                'lastdate_response' => new Zend_Db_Expr('CAST(s.lastdate_response AS CHAR)')
            ))
            ->join(array('spm' => 'shipment_participant_map'), 'spm.shipment_id = s.shipment_id', array())
            ->join(array('p' => 'participant'), 'p.participant_id = spm.participant_id', array())
            ->join(array('c' => 'countries'), 'c.id = p.country', array('country_id' => 'c.id', 'country_name' => 'c.iso_name'))
            ->joinLeft(
                array('csm' => 'country_shipment_map'),
                'csm.country_id = c.id AND csm.shipment_id = s.shipment_id',
                array('due_date' => new Zend_Db_Expr('IFNULL(csm.due_date_text, CAST(s.lastdate_response AS CHAR))'))
            )
            ->where("s.shipment_id = ?", $sid)
            ->where("p.country = ?", $countryId)
            ->group("c.id");
        $existingDetails  = $db->fetchRow($sQuery);
        if ($existingDetails) {
            if (!isset($dueDateText) || $dueDateText == "" || $dueDateText == $existingDetails["lastdate_response"]) {
                $db->delete('country_shipment_map', 'shipment_id = ' . $sid . ' AND country_id = ' . $countryId);
            } else if ($dueDateText != $existingDetails["due_date"]) {

                if ($existingDetails["lastdate_response"] != $existingDetails["due_date"]) {
                    $db->update('country_shipment_map',
                        array(
                            'due_date_text' => $dueDateText
                        ),
                        'shipment_id = ' . $sid . ' AND country_id = ' . $countryId
                    );
                } else {
                    $db->insert('country_shipment_map',
                        array(
                            'shipment_id' => $sid,
                            'country_id' => $countryId,
                            'due_date_text' => $dueDateText
                        )
                    );
                }
            }

        }
    }

    public function getShipmentNotParticipated($sid) {
        $commonServices = new Application_Service_Common();
        $general = new Pt_Commons_General();
        $notParticipatedMailContent = $commonServices->getEmailTemplate('not_participated');
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $return=0;
        $sQuery = $db->select()->from(array('sp' => 'shipment_participant_map'), array('sp.participant_id','sp.map_id','sp.last_not_participated_mail_count','sp.final_result'))
            ->joinLeft(array('s' => 'shipment'), 's.shipment_id=sp.shipment_id', array('s.shipment_code','s.shipment_code'))
            ->joinLeft(array('d' => 'distributions'), 'd.distribution_id = s.distribution_id', array('distribution_code', 'distribution_date'))
            ->joinLeft(array('p' => 'participant'), 'p.participant_id=sp.participant_id', array('p.email','participantName' => new Zend_Db_Expr("GROUP_CONCAT(DISTINCT p.unique_identifier,\" - \",p.lab_name ORDER BY p.lab_name SEPARATOR ', ')")))
            ->joinLeft(array('sl' => 'scheme_list'), 'sl.scheme_id=s.scheme_type', array('SCHEME' => 'sl.scheme_name'))
            ->where("(sp.shipment_receipt_date = '0000-00-00' OR sp.shipment_receipt_date IS NULL)")
            ->where("sp.shipment_id = ?", $sid)
            ->group("sp.participant_id");
        $participantEmails=$db->fetchAll($sQuery);
        foreach ($participantEmails as $participantDetails){
            if ($participantDetails['email']!='') {
                $surveyDate=$general->humanDateFormat($participantDetails['distribution_date']);
                $search = array('##NAME##','##SHIPCODE##','##SHIPTYPE##','##SURVEYCODE##','##SURVEYDATE##',);
                $replace = array($participantDetails['participantName'],$participantDetails['shipment_code'],$participantDetails['SCHEME'],$participantDetails['distribution_code'],$surveyDate);
                $content = $notParticipatedMailContent['mail_content'];
                $message = str_replace($search, $replace, $content);
                $subject = $notParticipatedMailContent['mail_subject'];
                $fromEmail =$notParticipatedMailContent['mail_from'];
                $fromFullName = $notParticipatedMailContent['from_name'];
                $toEmail =$participantDetails['email'];
                $cc=$notParticipatedMailContent['mail_cc'];
                $bcc=$notParticipatedMailContent['mail_bcc'];
                $commonServices->insertTempMail($toEmail,$cc,$bcc, $subject, $message, $fromEmail, $fromFullName);
                $count=$participantDetails['last_not_participated_mail_count']+1;
                $return=$db->update("shipment_participant_map", array(
                    "last_not_participated_mailed_on" => new Zend_Db_Expr("now()"),
                    "last_not_participated_mail_count" => $count
                ), "map_id = " . $participantDetails["map_id"]);
            }
        }
        return $return;
    }

    public function enrollShipmentParticipant($shipmentId,$participantId) {
        $db = new Application_Model_DbTable_ShipmentParticipantMap();
        return $db->enrollShipmentParticipant($shipmentId,$participantId);
    }

    public function getShipmentRowData($shipmentId) {
        $shipmentDb = new Application_Model_DbTable_Shipments();
        return $shipmentDb->getShipmentRowInfo($shipmentId);
    }

    public function getTbShipmentRowInfoByShipmentCode($shipmentCode) {
        $shipmentDb = new Application_Model_DbTable_Shipments();
        return $shipmentDb->getTbShipmentRowInfoByShipmentCode($shipmentCode);
    }

    public function getAllShipmentForm($parameters) {
        $shipmentDb = new Application_Model_DbTable_Shipments();
        return $shipmentDb->getAllShipmentFormDetails($parameters);
    }

	public function getAllFinalizedShipments($parameters){
		$shipmentDb = new Application_Model_DbTable_Shipments();
		return $shipmentDb->fetchAllFinalizedShipments($parameters);
	}

	public function responseSwitch($shipmentId,$switchStatus){
		$shipmentDb = new Application_Model_DbTable_Shipments();
		return $shipmentDb->responseSwitch($shipmentId,$switchStatus);
	}

	public function getFinalizedShipmentInReports($distributionId) {
        $authNameSpace = new Zend_Session_Namespace('administrators');
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->select()->from(array('s' => 'shipment',
            array('shipment_id','shipment_code','status','number_of_samples')))
            ->join(array('d' => 'distributions'), 'd.distribution_id=s.distribution_id',array(
                'distribution_code','distribution_date'))
            ->join(array('sp' => 'shipment_participant_map'), 'sp.shipment_id=s.shipment_id', array(
                'participant_count' => new Zend_Db_Expr('count("participant_id")'),
                'reported_count' => new Zend_Db_Expr("COUNT(CASE substr(sp.evaluation_status,4,1) WHEN '1' THEN 1 WHEN '2' THEN 1 END)"),
                'number_passed' => new Zend_Db_Expr("SUM(final_result = 1)")))
            ->join(array('sl' => 'scheme_list'), 'sl.scheme_id=s.scheme_type',array('scheme_name'))
            ->join(array('p' => 'participant'), 'p.participant_id=sp.participant_id', array())
            ->joinLeft(array('rr' => 'r_results'), 'sp.final_result=rr.result_id')
			->where("s.status='finalized'")
            ->where("s.distribution_id = ?", $distributionId)   ;
        if($authNameSpace->is_ptcc_coordinator) {
            $sql = $sql->where("p.country IS NULL OR p.country IN (".implode(",",$authNameSpace->countries).")");
        }
        $sql = $sql->group('s.shipment_id');

        return $db->fetchAll($sql);
    }

	public function addQcDetails($params) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $shipmentParticipantDb = new Application_Model_DbTable_ShipmentParticipantMap();
            $noOfRowsAffected = $shipmentParticipantDb->addQcInfo($params);
			if($noOfRowsAffected>0){
				$db->commit();
				return $noOfRowsAffected;
			}
        } catch (Exception $e) {
            $db->rollBack();
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }
    }

    public function sendSurveyReminderEmail($params) {

        $configFile = APPLICATION_PATH . '/configs/config.local.ini';
        if (!is_file($configFile)) {
            $configFile = APPLICATION_PATH . '/configs/config.ini';
        }
        $config = new Zend_Config_Ini($configFile, APPLICATION_ENV);

        $authNameSpace = new Zend_Session_Namespace('administrators');
        $commonServices = new Application_Service_Common();
        $general = new Pt_Commons_General();
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sQuery = $db->select()->from(array('sp' => 'shipment_participant_map'), array('sp.participant_id','sp.map_id','sp.new_shipment_mail_count'))
            ->join(array('s' => 'shipment'), 's.shipment_id=sp.shipment_id', array('s.shipment_code','s.shipment_code'))
            ->join(array('d' => 'distributions'), 'd.distribution_id = s.distribution_id', array('distribution_code', 'distribution_date'))
            ->join(array('p' => 'participant'), 'p.participant_id=sp.participant_id', array('p.email','participantName' => new Zend_Db_Expr("GROUP_CONCAT(DISTINCT p.lab_name ORDER BY p.lab_name SEPARATOR ', ')")))
            ->where("sp.shipment_id = ?", $params["shipmentId"]);

        if (1 == $authNameSpace->is_ptcc_coordinator) {
            $sQuery = $sQuery->where(new Zend_Db_Expr("p.country IN (".implode(',', $authNameSpace->countries).")"));
        }

        /**
         * PE Participant Conditions:
         * 1. The shipment is finalized
         * 2. The shipment finalized date is known
         * 3. The shipment contains a survey
         * 4. The survey submission window is open
         * 5. The participant has responded and has submitted PE results
         */
        $sQuery = $sQuery->where(new Zend_Db_Expr('s.status=\'finalized\''));
        $sQuery = $sQuery->where(new Zend_Db_Expr('s.finalized_date IS NOT NULL'));
        $sQuery = $sQuery->where(new Zend_Db_Expr('s.cs_survey IS NOT NULL'));
        $sQuery = $sQuery->where(new Zend_Db_Expr(sprintf(
            'DATE_ADD(CAST(s.finalized_date AS DATE), INTERVAL +%u DAY) >= NOW()',
            $config->customerSatisfactionSurvey->submissionWindow
        )));
        $sQuery = $sQuery->where(new Zend_Db_Expr('sp.report_generated=\'yes\' AND sp.shipment_test_report_date IS NOT NULL'));

        switch ($params['sendTo']) {
            case 'notResponded' :
                // The participant has not responded to the survey
                $sQuery = $sQuery->where(new Zend_Db_Expr('sp.cs_survey_response IS NULL'));
            break;
        }

        $sQuery = $sQuery->group("p.participant_id");

        $participantEmails = $db->fetchAll($sQuery);
        $mailContent = $commonServices->getEmailTemplate('cs_survey_reminder');
        foreach($participantEmails as $participantDetails){
            if ($participantDetails['email']!='') {
                $surveyDate = $general->humanDateFormat($participantDetails['distribution_date']);
                $search = array('##NAME##','##SHIPCODE##','##SHIPTYPE##','##SURVEYCODE##','##SURVEYDATE##',);
                $replace = array($participantDetails['participantName'],$participantDetails['shipment_code'],$participantDetails['SCHEME'],$participantDetails['distribution_code'],$surveyDate);
                $content = "<p>" . implode( "</p>\n\n<p>", preg_split( '/\n(?:\s*\n)+/', $params["emailBody"] ) ) . "</p>";;
                $message = str_replace($search, $replace, $content);
                $subject = $params["emailSubject"];
                $fromEmail = $mailContent['mail_from'];
                $fromFullName = $mailContent['from_name'];
                $toEmail = $participantDetails['email'];
                $cc = $mailContent['mail_cc'];
                $bcc = $mailContent['mail_bcc'];
                $commonServices->insertTempMail($toEmail,$cc,$bcc, $subject, $message, $fromEmail, $fromFullName);
            }
        }

    }

    public function sendEmailToParticipants($params) {
        $authNameSpace = new Zend_Session_Namespace('administrators');
        $commonServices = new Application_Service_Common();
        $general = new Pt_Commons_General();
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sQuery = $db->select()->from(array('sp' => 'shipment_participant_map'), array('sp.participant_id','sp.map_id','sp.new_shipment_mail_count'))
            ->join(array('s' => 'shipment'), 's.shipment_id=sp.shipment_id', array('s.shipment_code','s.shipment_code'))
            ->join(array('d' => 'distributions'), 'd.distribution_id = s.distribution_id', array('distribution_code', 'distribution_date'))
            ->join(array('p' => 'participant'), 'p.participant_id=sp.participant_id', array('p.email','participantName' => new Zend_Db_Expr("GROUP_CONCAT(DISTINCT p.lab_name ORDER BY p.lab_name SEPARATOR ', ')")))
            ->join(array('sl' => 'scheme_list'), 'sl.scheme_id=s.scheme_type', array('SCHEME' => 'sl.scheme_name'))
            ->where("sp.shipment_id = ?", $params["shipmentId"]);

        if (1 == $authNameSpace->is_ptcc_coordinator) {
            $sQuery = $sQuery->where(new Zend_Db_Expr("p.country IN (".implode(',', $authNameSpace->countries).")"));
        }

        if ($params["sendTo"] == "notSubmitted") {
            $sQuery = $sQuery->where(new Zend_Db_Expr("substr(sp.evaluation_status, 3, 1) = '9'"));
        }
        if ($params["sendTo"] == "submitted") {
            $sQuery = $sQuery->where(new Zend_Db_Expr("substr(sp.evaluation_status, 3, 1) = '1'"));
        }
        if ($params["sendTo"] == "saved") {
            $sQuery = $sQuery->where("sp.shipment_receipt_date IS NOT NULL")
                ->where(new Zend_Db_Expr("substr(sp.evaluation_status, 3, 1) = '9'"));
        }
        if ($params["sendTo"] == "neither") {
            $sQuery = $sQuery->where("sp.shipment_receipt_date IS NULL")
                ->where(new Zend_Db_Expr("substr(sp.evaluation_status, 3, 1) = '9'"));
        }
        $sQuery = $sQuery->group("p.participant_id");
        $participantEmails = $db->fetchAll($sQuery);
        $newShipmentMailContent = $commonServices->getEmailTemplate('new_shipment');
        foreach($participantEmails as $participantDetails){
            if ($participantDetails['email']!='') {
                $surveyDate = $general->humanDateFormat($participantDetails['distribution_date']);
                $search = array('##NAME##','##SHIPCODE##','##SHIPTYPE##','##SURVEYCODE##','##SURVEYDATE##',);
                $replace = array($participantDetails['participantName'],$participantDetails['shipment_code'],$participantDetails['SCHEME'],$participantDetails['distribution_code'],$surveyDate);
                $content = "<p>" . implode( "</p>\n\n<p>", preg_split( '/\n(?:\s*\n)+/', $params["emailBody"] ) ) . "</p>";;
                $message = str_replace($search, $replace, $content);
                $subject = $params["emailSubject"];
                $fromEmail = $newShipmentMailContent['mail_from'];
                $fromFullName = $newShipmentMailContent['from_name'];
                $toEmail = $participantDetails['email'];
                $cc = $newShipmentMailContent['mail_cc'];
                $bcc = $newShipmentMailContent['mail_bcc'];
                $commonServices->insertTempMail($toEmail,$cc,$bcc, $subject, $message, $fromEmail, $fromFullName);
                $count = $participantDetails['new_shipment_mail_count']+1;
                $db->update("shipment_participant_map", array(
                    "last_new_shipment_mailed_on" => new Zend_Db_Expr("now()"),
                    "new_shipment_mail_count" => $count
                ), "map_id = " . $participantDetails["map_id"]);
            }
        }
    }

    public function sendShipmentSavedEmailToParticipantsAndPTCC($pid, $sid) {
        $commonServices = new Application_Service_Common();
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $resultsSql = $db->select()
            ->from(array('spm' => 'shipment_participant_map'), array(
                'participant_id' => 'spm.participant_id',
                'submission_status' => new Zend_Db_Expr("CASE WHEN SUBSTR(spm.evaluation_status, 3, 1) = '9' THEN 'Saved' ELSE 'Submitted' END"),
                'is_pt_test_not_performed' => 'spm.is_pt_test_not_performed'
            ))
            ->join(array('ref' => 'reference_result_tb'), 'ref.shipment_id = spm.shipment_id', array('ref.sample_label', 'ref.sample_id'))
            ->joinLeft(array('res' => 'response_result_tb'), 'res.shipment_map_id = spm.map_id AND res.sample_id = ref.sample_id', array(
                'mtb_detected' => new Zend_Db_Expr(
                    "CASE
                                WHEN res.error_code = 'error' THEN 'Error'
                                WHEN IFNULL(res.error_code, '') != '' THEN CONCAT('Error ', res.error_code)
                                WHEN res.mtb_detected = 'notDetected' THEN 'MTB Not Detected '
                                WHEN res.mtb_detected = 'noResult' THEN 'No Result'
                                WHEN res.mtb_detected = 'veryLow' THEN 'MTB Very Low '
                                WHEN res.mtb_detected = 'trace' THEN 'MTB Trace '
                                WHEN res.mtb_detected = 'na' THEN 'N/A'
                                WHEN IFNULL(res.mtb_detected, '') = '' THEN ''
                                ELSE CONCAT('MTB ', UPPER(SUBSTRING(res.mtb_detected, 1, 1)), SUBSTRING(res.mtb_detected, 2, 254), ' ')
                              END"),
                'rif_resistance' => new Zend_Db_Expr(
                    "CASE
                                WHEN res.error_code = 'error'
                                  OR IFNULL(res.error_code, '') != ''
                                  OR res.mtb_detected IN ('notDetected', 'noResult', 'na')
                                  OR IFNULL(res.mtb_detected, '') = ''
                                  OR IFNULL(res.rif_resistance, '') = ''
                                  OR res.rif_resistance = 'na'
                                  THEN ''
                                WHEN res.rif_resistance = 'notDetected' THEN 'RIF Resistance Not Detected'
                                WHEN res.rif_resistance = 'veryLow' THEN 'RIF Resistance Very Low'
                                ELSE CONCAT('RIF Resistance ', UPPER(SUBSTRING(res.rif_resistance, 1, 1)), SUBSTRING(res.rif_resistance, 2, 254))
                              END"),
                'date_tested' => 'res.date_tested'))
            ->join(array('p' => 'participant'), 'p.participant_id = spm.participant_id', array(
                'country_id' => 'p.country',
                'pt_id' => 'p.unique_identifier',
                'participant_name' => 'p.lab_name'
            ))
            ->join(array('s' => 'shipment'), 's.shipment_id = spm.shipment_id', array('s.shipment_code'))
            ->joinLeft(array('rntr' => 'response_not_tested_reason'), 'rntr.not_tested_reason_id = spm.not_tested_reason', array('rntr.not_tested_reason'))
            ->where("spm.shipment_id = ?", $sid)
            ->where("spm.participant_id = ?", $pid)
            ->order('ref.sample_id ASC');
        $resultsSaved = $db->fetchAll($resultsSql);
        if(count($resultsSaved) > 0) {

            $recipientsSql = $db->select()
                ->from(array('spm' => 'shipment_participant_map'), array())
                ->join(array('p' => 'participant'), 'p.participant_id = spm.participant_id', array(
                    'participant_name' => 'p.lab_name',
                    'participant_email' => 'p.email'
                ))
                ->join(array('pmm' => 'participant_manager_map'),'pmm.participant_id = p.participant_id', array())
                ->join(array('dm' => 'data_manager'),'dm.dm_id = pmm.dm_id', array(
                    'data_manager_email' => 'dm.primary_email'
                ))
                ->joinLeft(array('pcm' => 'ptcc_country_map'), 'pcm.country_id = p.country', array())
                ->joinLeft(array('admin' => 'system_admin'), 'admin.admin_id = pcm.admin_id', array(
                    'pecc_name' => new Zend_Db_Expr("CONCAT(IFNULL(CONCAT(admin.first_name, ' '), ''), IFNULL(admin.last_name, ''))"),
                    'pecc_email' => 'admin.primary_email'
                ))
                ->where("spm.shipment_id = ?", $sid)
                ->where("spm.participant_id = ?", $pid);
            $recipients = $db->fetchAll($recipientsSql);
            $recipientAddresses = array();
            foreach($recipients as $recipient) {
                if (isset($recipient["participant_email"]) &&
                    $recipient["participant_email"] != "" &&
                    !array_key_exists($recipient["participant_email"], $recipientAddresses)) {
                    $recipientAddresses = array_merge(
                        $recipientAddresses,
                        array(
                            $recipient["participant_email"] => $recipient["participant_name"]
                        )
                    );
                }
                if (isset($recipient["data_manager_email"]) &&
                    $recipient["data_manager_email"] != "" &&
                    !array_key_exists($recipient["data_manager_email"], $recipientAddresses)) {
                    $recipientAddresses = array_merge(
                        $recipientAddresses,
                        array(
                            $recipient["data_manager_email"] => $recipient["participant_name"]
                        )
                    );
                }
                if (isset($recipient["pecc_email"]) &&
                    $recipient["pecc_email"] != "" &&
                    !array_key_exists($recipient["pecc_email"], $recipientAddresses)) {
                    $recipientAddresses = array_merge(
                        $recipientAddresses,
                        array(
                            $recipient["pecc_email"] => $recipient["pecc_name"]
                        )
                    );
                }
            }
            foreach($recipientAddresses as $emailAddress => $name) {
                $participantConfirmationEmailBody = "<p>Dear ".$name.= ",</p>".
                        "<p>This is a confirmation that we have received the following results on the ePT platform from ".$resultsSaved[0]["participant_name"]." (".$resultsSaved[0]["pt_id"].") for panel ".$resultsSaved[0]["shipment_code"]."</p>";
                if ($resultsSaved[0]["submission_status"] == "Saved") {
                    $participantConfirmationEmailBody .= "<p><strong><span style=\"color: red;\">IMPORTANT:</span> While the following data has been saved on the system, it will not be evaluated until someone actually submits these results. When the results are ready to be submitted, please ensure that the 'Submit' button at the bottom of the form is clicked.</strong></p>";
                }
                if (isset($resultsSaved[0]["is_pt_test_not_performed"]) && $resultsSaved[0]["is_pt_test_not_performed"] == "yes") {
                    $participantConfirmationEmailBody .= "<p>Participant was unable to test the proficiency testing panel";
                    if (isset($resultsSaved[0]["not_tested_reason"]) && $resultsSaved[0]["not_tested_reason"] != "") {
                        $participantConfirmationEmailBody .= "due to ".$resultsSaved[0]["not_tested_reason"];
                    }
                    $participantConfirmationEmailBody .= "</p>";
                } else {
                    $participantConfirmationEmailBody .= "<table>";
                    $participantConfirmationEmailBody .= "<tr><td><strong>Sample</strong></td><td><strong>Date Tested</strong></td><td><strong>Result</strong></td></tr>";
                    foreach ($resultsSaved as $resultSaved) {
                        if ($resultSaved["mtb_detected"] != "" || $resultSaved["rif_resistance"] != "") {
                            $participantConfirmationEmailBody .= "<tr><td>" . $resultSaved["sample_label"] . "</td><td>" .
                                (isset($resultSaved["date_tested"]) ?
                                    Pt_Commons_General::dbDateToString($resultSaved["date_tested"]) :
                                    "") . "</td><td>" .
                                $resultSaved["mtb_detected"] . $resultSaved["rif_resistance"] . "</td></tr>";
                        }
                    }
                    $participantConfirmationEmailBody .= "</table>";
                }
                $commonServices->insertTempMail($emailAddress,'','', "Receipt of ePT results for ".$resultsSaved[0]["shipment_code"]." from ".$resultsSaved[0]["participant_name"]." (".$resultsSaved[0]["pt_id"].")", $participantConfirmationEmailBody, "tbeptmanager@gmail.com", "ePT");
            }
        }
    }

    public function getShipments($distributionId) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->select()->from(array("s" => "shipment"))
            ->join(array("d" => "distributions"), "d.distribution_id=s.distribution_id")
            ->joinLeft(array("sp" => "shipment_participant_map"), "sp.shipment_id=s.shipment_id", array(
                "map_id",
                "responseDate" => "shipment_test_report_date",
                "participant_count" => new Zend_Db_Expr("count(sp.participant_id)"),
                "reported_count" => new Zend_Db_Expr("SUM(substr(sp.evaluation_status,3,1) = 1 AND (substr(sp.evaluation_status,4,1) = '1' OR substr(sp.evaluation_status,4,1) = '2'))"),
                "number_passed" => new Zend_Db_Expr("SUM(final_result = 1)"),
                "last_not_participated_mailed_on",
                "last_not_participated_mail_count",
                "shipment_status" => "s.status",
                "last_submission_updated_on" => new Zend_Db_Expr("GREATEST(MAX(sp.updated_on_admin), MAX(sp.updated_on_user))"),
                "shipment_updated_on_admin" => new Zend_Db_Expr("MAX(s.updated_on_admin)")
            ))
            ->join(array('p' => 'participant'), 'p.participant_id=sp.participant_id')
            ->join(array('sl' => 'scheme_list'), 'sl.scheme_id=s.scheme_type')
            ->joinLeft(array('rr' => 'r_results'), 'sp.final_result=rr.result_id')
            ->where("s.distribution_id = ?", $distributionId);
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if(isset($authNameSpace) && $authNameSpace->is_ptcc_coordinator) {
            $sql = $sql->where("p.country IN (".implode(",",$authNameSpace->countries).")");
        }
        $sql = $sql->group('s.shipment_id');
        $rResult = $db->fetchAll($sql);
        for ($i = 0; $i < count($rResult); $i++) {
            if ($rResult[$i]['shipment_status'] == 'evaluated' && $rResult[$i]['last_submission_updated_on'] > $rResult[$i]['shipment_updated_on_admin']) {
                $rResult[$i]['shipment_status'] = "Should be Re-Evaluated";
            }
        }
        return $rResult;
    }
}
