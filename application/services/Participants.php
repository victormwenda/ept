<?php
include_once "PHPExcel.php";

class Application_Service_Participants {
	public function getUsersParticipants($userSystemId = null) {
		if ($userSystemId == null) {
			$authNameSpace = new Zend_Session_Namespace('datamanagers');
			$userSystemId = $authNameSpace->dm_id;
        }
		$participantDb = new Application_Model_DbTable_Participants();
		return $participantDb->getParticipantsByUserSystemId($userSystemId);
	}

	public function getParticipantDetails($partSysId) {
		$participantDb = new Application_Model_DbTable_Participants();
		return $participantDb->getParticipant($partSysId);
	}

    public function getParticipantDetailsByUniqueId($uniqueId) {
        $participantDb = new Application_Model_DbTable_Participants();
        return $participantDb->getParticipantByUniqueId($uniqueId);
    }

    public function addParticipant($params) {
		$participantDb = new Application_Model_DbTable_Participants();
		return $participantDb->addParticipant($params);
	}

	public function addParticipantForDataManager($params) {
		$participantDb = new Application_Model_DbTable_Participants();
		return $participantDb->addParticipantForDataManager($params);
	}

	public function updateParticipant($params) {
		$participantDb = new Application_Model_DbTable_Participants();
		return $participantDb->updateParticipant($params);
	}

	public function getAllParticipants($params) {
		$participantDb = new Application_Model_DbTable_Participants();
		return $participantDb->getAllParticipants($params);
	}

	public function getAllEnrollments($params) {
		$enrollments = new Application_Model_DbTable_Enrollments();
		return $enrollments->getAllEnrollments($params);
	}

	public function getEnrollmentDetails($pid,$sid) {
	    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
	    $sql = $db->select()->from(array('p'=>'participant'))
				  ->joinLeft(array('sp'=>'shipment_participant_map'),'p.participant_id=sp.participant_id')
				  ->joinLeft(array('s'=>'shipment'),'s.shipment_id=sp.shipment_id')
				  ->where("p.participant_id=".$pid);
	    return $db->fetchAll($sql);
	}

	public function getParticipantSchemes($dmId) {
	    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
	    $sql = $db->select()->from(array('p'=>'participant'))
				  ->joinLeft(array('pmm'=>'participant_manager_map'),'p.participant_id=pmm.participant_id')
				  ->joinLeft(array('sp'=>'shipment_participant_map'),'p.participant_id=sp.participant_id')
				  ->joinLeft(array('s'=>'shipment'),'s.shipment_id=sp.shipment_id')
				  ->joinLeft(array('sl'=>'scheme_list'),'sl.scheme_id=s.scheme_type')
				  ->where("pmm.dm_id= ?",$dmId)
				  ->group(array("sp.participant_id","s.scheme_type"))
				  ->order(new Zend_Db_Expr("CASE WHEN p.unique_identifier REGEXP '\d*' THEN CAST(CAST(p.unique_identifier AS DECIMAL) AS CHAR) ELSE TRIM(LEADING '0' FROM p.unique_identifier) END"));
	    return $db->fetchAll($sql);
	}

    public function getParticipantMonthlyIndicators($dmId) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->select()->from(array('p'=>'participant'), array(
                'sorting_unique_identifier' => new Zend_Db_Expr("LPAD(p.unique_identifier, 10, '0')"),
                'p.participant_id',
                'p.unique_identifier',
                'p.lab_name'
            ))
            ->join(array('pmm' => 'participant_manager_map'),'p.participant_id = pmm.participant_id', array())
            ->join(array('c' => 'countries'),'p.country = c.id', array())
            ->joinLeft(array('pmi' => 'participant_monthly_indicators'),'p.participant_id = pmi.participant_id AND MONTH(pmi.created_on) = '.date('m').' AND YEAR(pmi.created_on) = '.date('Y'), array('submission_id', 'attributes'))
            ->where("pmm.dm_id = ?", $dmId)
            ->where("c.show_monthly_indicators = 1")
            ->where("p.status = 'active'")
            ->order("sorting_unique_identifier ASC");
        return $db->fetchAll($sql);
    }

    public function saveParticipantMonthlyIndicators($dmId, $participantId, $monthlyIndicators) {
        $data = array(
            'participant_id' => $participantId,
            'attributes' => json_encode($monthlyIndicators),
            'created_by' => $dmId,
            'created_on' => new Zend_Db_Expr('now()')
        );
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->select()->from(array('pmi'=>'participant_monthly_indicators'))
            ->where("pmi.participant_id = ?", $participantId)
            ->where("MONTH(pmi.created_on) = ?", date('m'))
            ->where("YEAR(pmi.created_on) = ?", date('Y'));
        $monthlyIndicatorsRecord = $db->fetchRow($sql);
        if ($monthlyIndicatorsRecord != "") {
            $db->update('participant_monthly_indicators', $data,'submission_id = '.$monthlyIndicatorsRecord['submission_id']);
        } else {
            $db->insert('participant_monthly_indicators', $data);
        }
    }

	public function getUnEnrolled($scheme) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$subSql = $db->select()
            ->from(array('e' => 'enrollments'), 'participant_id')
            ->where("e.scheme_id = ?", $scheme);
		$sql = $db->select()
            ->from(array('p' => 'participant'))
            ->where("p.participant_id NOT IN ?", $subSql)
            ->where("p.status = 'active'")
            ->order(new Zend_Db_Expr("CASE WHEN p.unique_identifier REGEXP '\d*' THEN CAST(CAST(p.unique_identifier AS DECIMAL) AS CHAR) ELSE TRIM(LEADING '0' FROM p.unique_identifier) END"));
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if($authNameSpace->is_ptcc_coordinator) {
            $sql = $sql->where("p.country IS NULL OR p.country IN (" . implode(",", $authNameSpace->countries) . ")");
        }
		return $db->fetchAll($sql);
	}

    public function getUnEnrolledCountriesBySchemeCode($scheme) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $subSql = $db->select()
            ->from(array('e' => 'enrollments'), 'participant_id')->where("scheme_id = ?", $scheme);
        $sql = $db->select()
            ->from(array('p' => 'participant'))
            ->join(array('c' => 'countries'), 'p.country = c.id', array('iso_name'))
            ->where("p.participant_id NOT IN ?", $subSql)
            ->where("p.status = 'active'")
            ->order(array('c.iso_name', new Zend_Db_Expr("CASE WHEN p.unique_identifier REGEXP '\d*' THEN CAST(CAST(p.unique_identifier AS DECIMAL) AS CHAR) ELSE TRIM(LEADING '0' FROM p.unique_identifier) END")));
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if($authNameSpace->is_ptcc_coordinator) {
            $sql = $sql->where("p.country IS NULL OR p.country IN (" . implode(",", $authNameSpace->countries) . ")");
        }
        $participants = $db->fetchAll($sql);
        $countries = array();
        foreach ($participants as $participant) {
            if (!array_key_exists($participant['iso_name'], $countries)) {
                $countries[$participant['iso_name']] = array(
                    "iso_name" => $participant['iso_name'],
                    "previously_selected" => array(),
                    "previously_unselected" => array(),
                    "enrolled_participants" => array(),
                    "unenrolled_participants" => array()
                );
            }
            $countries[$participant['iso_name']]["unenrolled_participants"][] = $participant;
        }
        return $countries;
    }

	public function getEnrolledBySchemeCode($scheme){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql = $db->select()->from(array('e' => 'enrollments'), array())
			->join(array('p' => 'participant'), "p.participant_id = e.participant_id")
            ->where("e.scheme_id = ?", $scheme)->where("p.status = 'active'")
            ->order(new Zend_Db_Expr("CASE WHEN p.unique_identifier REGEXP '\d*' THEN CAST(CAST(p.unique_identifier AS DECIMAL) AS CHAR) ELSE TRIM(LEADING '0' FROM p.unique_identifier) END"));
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if($authNameSpace->is_ptcc_coordinator) {
            $sql = $sql->where("p.country IS NULL OR p.country IN (" . implode(",", $authNameSpace->countries) . ")");
        }
		return $db->fetchAll($sql);
	}

    public function getEnrolledCountriesBySchemeCode($scheme){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->select()->from(array('e' => 'enrollments'), array())
            ->join(array('p' => 'participant'), "p.participant_id = e.participant_id")
            ->join(array('c' => 'countries'), 'p.country = c.id', array('iso_name'))
            ->where("e.scheme_id = ?", $scheme)->where("p.status = 'active'")
            ->order(new Zend_Db_Expr("CASE WHEN p.unique_identifier REGEXP '\d*' THEN CAST(CAST(p.unique_identifier AS DECIMAL) AS CHAR) ELSE TRIM(LEADING '0' FROM p.unique_identifier) END"));
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if($authNameSpace->is_ptcc_coordinator) {
            $sql = $sql->where("p.country IS NULL OR p.country IN (" . implode(",", $authNameSpace->countries) . ")");
        }
        $participants = $db->fetchAll($sql);
        $countries = array();
        foreach ($participants as $participant) {
            if (!array_key_exists($participant['iso_name'], $countries)) {
                $countries[$participant['iso_name']] = array(
                    "iso_name" => $participant['iso_name'],
                    "previously_selected" => array(),
                    "previously_unselected" => array(),
                    "enrolled_participants" => array(),
                    "unenrolled_participants" => array()
                );
            }
            $countries[$participant['iso_name']]["enrolled_participants"][] = $participant;
        }
        return $countries;
    }

    public function getEnrolledByShipmentId($shipmentId) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql = $db->select()
            ->from(array('p' => 'participant'))
			->join(array('spm' => 'shipment_participant_map'), 'spm.participant_id = p.participant_id', array())
			->join(array('s' => 'shipment'), 'spm.shipment_id = s.shipment_id', array())
			->where("s.shipment_id = ?", $shipmentId)
			->where("p.status = 'active'")
			->order(new Zend_Db_Expr("CASE WHEN p.unique_identifier REGEXP '\d*' THEN CAST(CAST(p.unique_identifier AS DECIMAL) AS CHAR) ELSE TRIM(LEADING '0' FROM p.unique_identifier) END"));
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if ($authNameSpace->is_ptcc_coordinator) {
            $sql = $sql->where("p.country IS NULL OR p.country IN (".implode(",",$authNameSpace->countries).")");
        }
		return $db->fetchAll($sql);
	}

    public function getEnrolledCountriesByShipmentId($shipmentId) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->select()
            ->from(array('p' => 'participant'))
            ->join(array('spm' => 'shipment_participant_map'), 'spm.participant_id = p.participant_id', array())
            ->join(array('s' => 'shipment'), 'spm.shipment_id = s.shipment_id', array())
            ->join(array('c' => 'countries'), 'p.country = c.id', array('iso_name'))
            ->where("s.shipment_id = ?", $shipmentId)
            ->where("p.status = 'active'")
            ->order(array('c.iso_name', new Zend_Db_Expr("CASE WHEN p.unique_identifier REGEXP '\d*' THEN CAST(CAST(p.unique_identifier AS DECIMAL) AS CHAR) ELSE TRIM(LEADING '0' FROM p.unique_identifier) END")));
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if ($authNameSpace->is_ptcc_coordinator) {
            $sql = $sql->where("p.country IS NULL OR p.country IN (".implode(",",$authNameSpace->countries).")");
        }
        $participants = $db->fetchAll($sql);
        $countries = array();
        foreach ($participants as $participant) {
            if (!array_key_exists($participant['iso_name'], $countries)) {
                $countries[$participant['iso_name']] = array(
                    "iso_name" => $participant['iso_name'],
                    "previously_selected" => array(),
                    "previously_unselected" => array(),
                    "enrolled_participants" => array(),
                    "unenrolled_participants" => array()
                );
            }
            $countries[$participant['iso_name']]["enrolled_participants"][] = $participant;
        }
        return $countries;
    }

	public function getSchemesByParticipantId($pid) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql = $db->select()->from(array('p'=>'participant'),array())
				       ->joinLeft(array('e'=>'enrollments'),'e.participant_id=p.participant_id',array())
				       ->joinLeft(array('sl'=>'scheme_list'),'sl.scheme_id=e.scheme_id',array('scheme_id'))
				       ->where("p.participant_id = ?", $pid)
				       ->order(new Zend_Db_Expr("CASE WHEN p.unique_identifier REGEXP '\d*' THEN CAST(CAST(p.unique_identifier AS DECIMAL) AS CHAR) ELSE TRIM(LEADING '0' FROM p.unique_identifier) END"));

		return $db->fetchCol($sql);
	}

	public function getUnEnrolledByShipmentId($shipmentId) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$subSql = $db->select()
            ->from(array('p' => 'participant'), array('participant_id'))
			->join(array('sp' => 'shipment_participant_map'),'sp.participant_id = p.participant_id', array())
			->join(array('s' => 'shipment'), 'sp.shipment_id = s.shipment_id', array())
			->where("s.shipment_id = ?", $shipmentId)
			->where("p.status = 'active'");
		$sql = $db->select()
            ->from(array('p' => 'participant'))
            ->where("p.participant_id NOT IN ?", $subSql)
			->order(new Zend_Db_Expr("CASE WHEN p.unique_identifier REGEXP '\d*' THEN CAST(CAST(p.unique_identifier AS DECIMAL) AS CHAR) ELSE TRIM(LEADING '0' FROM p.unique_identifier) END"));
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if ($authNameSpace->is_ptcc_coordinator) {
            $sql = $sql->where("p.country IS NULL OR p.country IN (".implode(",",$authNameSpace->countries).")");
        }
		return $db->fetchAll($sql);
	}

    public function getUnEnrolledCountriesByShipmentId($shipmentId) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $subSql = $db->select()
            ->from(array('p' => 'participant'), array('participant_id'))
            ->join(array('spm' => 'shipment_participant_map'), 'spm.participant_id = p.participant_id', array())
            ->join(array('s' => 'shipment'), 'spm.shipment_id = s.shipment_id', array())
            ->where("s.shipment_id = ?", $shipmentId)
            ->where("p.status = 'active'");
        $sql = $db->select()
            ->from(array('p' => 'participant'))
            ->join(array('c' => 'countries'), 'p.country = c.id', array('iso_name'))
            ->where("participant_id NOT IN ?", $subSql)
            ->order(array('c.iso_name', new Zend_Db_Expr("CASE WHEN p.unique_identifier REGEXP '\d*' THEN CAST(CAST(p.unique_identifier AS DECIMAL) AS CHAR) ELSE TRIM(LEADING '0' FROM p.unique_identifier) END")));
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if ($authNameSpace->is_ptcc_coordinator) {
            $sql = $sql->where("p.country IS NULL OR p.country IN (".implode(",",$authNameSpace->countries).")");
        }
        $participants = $db->fetchAll($sql);
        $countries = array();
        foreach ($participants as $participant) {
            if (!array_key_exists($participant['iso_name'], $countries)) {
                $countries[$participant['iso_name']] = array(
                    "iso_name" => $participant['iso_name'],
                    "previously_selected" => array(),
                    "previously_unselected" => array(),
                    "enrolled_participants" => array(),
                    "unenrolled_participants" => array()
                );
            }
            $countries[$participant['iso_name']]["unenrolled_participants"][] = $participant;
        }
        return $countries;
    }

    public function getEnrolledAndUnEnrolledParticipants($shipmentId)
    {
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $participantsSql =  $dbAdapter->select()
            ->from(array('p' => 'participant'), array(
                "participant_id",
                "id" => "participant_id",
                "lab_name",
                "email",
                "unique_identifier",
                "sorting_unique_identifier" => new Zend_Db_Expr("LPAD(p.unique_identifier, 10, '0')")
            ))
            ->join(array('c' => 'countries'), 'p.country = c.id', array('iso_name'))
            ->joinLeft(array('csm' => 'country_shipment_map'), 'csm.country_id=c.id AND  csm.shipment_id = '.(int)$shipmentId, array('due_date_text'))
            ->joinLeft(array('spm' => 'shipment_participant_map'), "p.participant_id = spm.participant_id AND spm.shipment_id = ".(int)$shipmentId, array('map_id'))
            ->where("p.status = 'active'")
            ->order(array("c.iso_name ASC", "sorting_unique_identifier ASC"));
        $participants = $dbAdapter->fetchAll($participantsSql);

        $countryNames = array_unique(array_column($participants, 'iso_name'));
        $participantsGroupedByCountry = array();

        
        foreach($countryNames as $countryName) {
            $participantsGroupedByCountry[$countryName] = array_filter($participants,
            function($participant) use ($countryName) {
                return $participant['iso_name'] == $countryName;
            });
        }
        return $participantsGroupedByCountry;
    }

	public function enrollParticipants($params) {
		$enrollments = new Application_Model_DbTable_Enrollments();
		return $enrollments->enrollParticipants($params);
	}

    public function addParticipantManagerMap($params) {
		$db = new Application_Model_DbTable_Participants();
		return $db->addParticipantManager($params);
	}

	public function getAffiliateList() {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		return $db->fetchAll($db->select()->from('r_participant_affiliates')->order('affiliate ASC'));
	}

	public function getEnrolledProgramsList() {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		return $db->fetchAll($db->select()->from('r_enrolled_programs')->order('enrolled_programs ASC'));
	}

	public function getSiteTypeList() {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		return $db->fetchAll($db->select()->from('r_site_type')->order('site_type ASC'));
	}

    public function getSiteType($siteTypeName) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        return $db->fetchRow($db->select()->from('r_site_type')->where('site_type = ?', $siteTypeName));
    }

	public function getNetworkTierList() {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		return $db->fetchAll($db->select()->from('r_network_tiers')->order('network_name ASC'));
	}

	public function getAllParticipantRegion() {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql = $db->select()->from(array('p'=>'participant'),array('p.region'))
            ->group('p.region')
            ->where("p.region IS NOT NULL")
            ->where("p.region != ''")
            ->order(array("p.region", new Zend_Db_Expr("CASE WHEN p.unique_identifier REGEXP '\d*' THEN CAST(CAST(p.unique_identifier AS DECIMAL) AS CHAR) ELSE TRIM(LEADING '0' FROM p.unique_identifier) END")));
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if($authNameSpace->is_ptcc_coordinator) {
            $sql = $sql->where("p.country IS NULL OR p.country IN (".implode(",",$authNameSpace->countries).")");
        }
		return $db->fetchAll($sql);
	}

	public function getAllParticipantDetails($dmId) {
	    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
	    $sql = $db->select()->from(array('p'=>'participant'))
	                          ->join(array('c'=>'countries'),'c.id=p.country')
				  ->joinLeft(array('pmm'=>'participant_manager_map'),'p.participant_id=pmm.participant_id')
				  ->where("pmm.dm_id= ?",$dmId)
				  ->group(array("p.participant_id"))
				  ->order(new Zend_Db_Expr("CASE WHEN p.unique_identifier REGEXP '\d*' THEN CAST(CAST(p.unique_identifier AS DECIMAL) AS CHAR) ELSE TRIM(LEADING '0' FROM p.unique_identifier) END"));
	    return $db->fetchAll($sql);
	}

	public function getAllActiveParticipants() {
		$participantDb = new Application_Model_DbTable_Participants();
		return $participantDb->fetchAllActiveParticipants();
	}

	public function getSchemeWiseParticipants($schemeType) {
		$participantDb = new Application_Model_DbTable_Participants();
		return $participantDb->getSchemeWiseParticipants($schemeType);
	}

	public function getShipmentEnrollement($parameters) {
		$db = new Application_Model_DbTable_Participants();
		$db->getEnrolledByShipmentDetails($parameters);
	}

	public function getShipmentUnEnrollements($parameters) {
		$db = new Application_Model_DbTable_Participants();
		$db->getUnEnrolledByShipments($parameters);
	}

    public function echoShipmentRespondedParticipants($params) {
		$participantDb = new Application_Model_DbTable_Participants();
		$participantDb->echoShipmentRespondedParticipants($params);
	}

	public function echoShipmentNotRespondedParticipants($params) {
		$participantDb = new Application_Model_DbTable_Participants();
		$participantDb->echoShipmentNotRespondedParticipants($params);
	}

    public function getShipmentNotEnrolledParticipants($params) {
		$participantDb = new Application_Model_DbTable_Participants();
		return $participantDb->getShipmentNotEnrolledParticipants($params);
	}

	public function getParticipantSchemesBySchemeId($parameters) {
		$shipmentDb = new Application_Model_DbTable_Shipments();
		return $shipmentDb->fetchParticipantSchemesBySchemeId($parameters);
	}

	public function exportShipmentRespondedParticipantsDetails($params) {
		try {
            $excel = new PHPExcel();
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array('memoryCacheSize' => '80MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $output = array();
            $sheet = $excel->getActiveSheet();
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                )
            );
            $styleInboldArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                )
            );
            $borderStyle = array(
                 'alignment' => array(
                     'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 ),
                 'borders' => array(
                     'outline' => array(
                         'style' => PHPExcel_Style_Border::BORDER_THIN,
                     ),
                 )
             );
            $sheet->mergeCells('A1:E1');
			$sheet->setCellValue('A1', html_entity_decode("Responded Shipment Participant List", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->getStyle('A1')->applyFromArray($styleInboldArray);
			if (isset($params['shipmentCode']) && trim($params['shipmentCode'])!="") {
				$sheet->setCellValue('A2', html_entity_decode("Shipment Code", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->setCellValue('B2', html_entity_decode($params['shipmentCode'], ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			}
			if (isset($params['shipmentCode']) && trim($params['shipmentCode'])!="") {
				$sheet->setCellValue('A3', html_entity_decode("Shipment Date", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->setCellValue('B3', html_entity_decode($params['shipmentDate'], ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			}
			$sheet->setCellValue('A4', html_entity_decode("Participant Id", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('B4', html_entity_decode("Lab Name/Participant Name", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('C4', html_entity_decode("Country", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('D4', html_entity_decode("Cell/Mobile", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('E4', html_entity_decode("Phone", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('F4', html_entity_decode("Affiliation", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('G4', html_entity_decode("Email", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('H4', html_entity_decode("Response Status", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);

			$sheet->getStyle('A4')->applyFromArray($styleArray);
			$sheet->getStyle('B4')->applyFromArray($styleArray);
			$sheet->getStyle('C4')->applyFromArray($styleArray);
			$sheet->getStyle('D4')->applyFromArray($styleArray);
			$sheet->getStyle('E4')->applyFromArray($styleArray);
			$sheet->getStyle('F4')->applyFromArray($styleArray);
			$sheet->getStyle('G4')->applyFromArray($styleArray);
			$sheet->getStyle('H4')->applyFromArray($styleArray);

            $sQuerySession = new Zend_Session_Namespace('respondedParticipantsExcel');
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $rResult = $db->fetchAll($sQuerySession->shipmentRespondedParticipantQuery);

            foreach ($rResult as $aRow) {
				$row = array();
				$row[] = $aRow['unique_identifier'];
				$row[] = $aRow['participantName'];
				$row[] = $aRow['iso_name'];
				$row[] = $aRow['mobile'];
				$row[] = $aRow['phone'];
				$row[] = $aRow['affiliation'];
				$row[] = $aRow['email'];
				$row[] = ucwords($aRow['RESPONSE']);

				$output[] = $row;
            }

            foreach ($output as $rowNo => $rowData) {
                $colNo = 0;
                foreach ($rowData as $field => $value) {
                    if (!isset($value)) {
                        $value = "";
                    }
                    $sheet->getCellByColumnAndRow($colNo, $rowNo + 5)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
                    $rRowCount = $rowNo + 5;
                    $cellName = $sheet->getCellByColumnAndRow($colNo, $rowNo + 5)->getColumn();
                    $sheet->getStyle($cellName . $rRowCount)->applyFromArray($borderStyle);
                    $sheet->getDefaultRowDimension()->setRowHeight(18);
                    $sheet->getColumnDimensionByColumn($colNo)->setWidth(22);
                    $sheet->getStyleByColumnAndRow($colNo, $rowNo + 5)->getAlignment()->setWrapText(true);
                    $colNo++;
                }
            }

            $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            $fileSafeShipmentCode = str_replace( ' ', '-', str_replace(array_merge(
                array_map('chr', range(0, 31)),
                array('<', '>', ':', '"', '/', '\\', '|', '?', '*')
            ), '', $params['shipmentCode']));
            $filename = $fileSafeShipmentCode.'-responded-participant-report-'.date('d-M-Y-H-i-s') . '.xls';
            $writer->save(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
            return $filename;
		} catch (Exception $exc) {
				return "";
				$sQuerySession->correctiveActionsQuery = '';
				error_log("GENERATE-SHIPMENT-RESPONDED-PARTICIPANT-REPORT-EXCEL--" . $exc->getMessage());
				error_log($exc->getTraceAsString());
		}
	}

	public function exportShipmentNotRespondedParticipantsDetails($params) {
		try {
            $excel = new PHPExcel();
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array('memoryCacheSize' => '80MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $output = array();
            $sheet = $excel->getActiveSheet();
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                )
            );
            $styleInboldArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                )
            );
            $borderStyle = array(
                 'alignment' => array(
                     'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                 ),
                 'borders' => array(
                     'outline' => array(
                         'style' => PHPExcel_Style_Border::BORDER_THIN,
                     ),
                 )
             );
            $sheet->mergeCells('A1:E1');
			$sheet->setCellValue('A1', html_entity_decode("Not Responded Shipment Participant List", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->getStyle('A1')->applyFromArray($styleInboldArray);

			if(isset($params['shipmentCode']) && trim($params['shipmentCode'])!=""){
				$sheet->setCellValue('A2', html_entity_decode("Shipment Code", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->setCellValue('B2', html_entity_decode($params['shipmentCode'], ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			}
			if(isset($params['shipmentCode']) && trim($params['shipmentCode'])!=""){
				$sheet->setCellValue('A3', html_entity_decode("Shipment Date", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->setCellValue('B3', html_entity_decode($params['shipmentDate'], ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			}

			$sheet->setCellValue('A4', html_entity_decode("Participant Id", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('B4', html_entity_decode("Lab Name/Participant Name", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('C4', html_entity_decode("Country", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('D4', html_entity_decode("Cell/Mobile", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('E4', html_entity_decode("Phone", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('F4', html_entity_decode("Affiliation", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('G4', html_entity_decode("Email", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue('H4', html_entity_decode("Response Status", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);

			$sheet->getStyle('A4')->applyFromArray($styleArray);
			$sheet->getStyle('B4')->applyFromArray($styleArray);
			$sheet->getStyle('C4')->applyFromArray($styleArray);
			$sheet->getStyle('D4')->applyFromArray($styleArray);
			$sheet->getStyle('E4')->applyFromArray($styleArray);
			$sheet->getStyle('F4')->applyFromArray($styleArray);
			$sheet->getStyle('G4')->applyFromArray($styleArray);
			$sheet->getStyle('H4')->applyFromArray($styleArray);

            $sQuerySession = new Zend_Session_Namespace('notRespondedParticipantsExcel');
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $rResult = $db->fetchAll($sQuerySession->shipmentRespondedParticipantQuery);

            foreach ($rResult as $aRow) {
				$row = array();
				$row[] = $aRow['unique_identifier'];
				$row[] = $aRow['participantName'];
				$row[] = $aRow['iso_name'];
				$row[] = $aRow['mobile'];
				$row[] = $aRow['phone'];
				$row[] = $aRow['affiliation'];
				$row[] = $aRow['email'];
				$row[] = ucwords($aRow['RESPONSE']);

				$output[] = $row;
            }

            foreach ($output as $rowNo => $rowData) {
                $colNo = 0;
                foreach ($rowData as $field => $value) {
                    if (!isset($value)) {
                        $value = "";
                    }
                    $sheet->getCellByColumnAndRow($colNo, $rowNo + 5)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
                    $rRowCount = $rowNo + 5;
                    $cellName = $sheet->getCellByColumnAndRow($colNo, $rowNo + 5)->getColumn();
                    $sheet->getStyle($cellName . $rRowCount)->applyFromArray($borderStyle);
                    $sheet->getDefaultRowDimension()->setRowHeight(18);
                    $sheet->getColumnDimensionByColumn($colNo)->setWidth(22);
                    $sheet->getStyleByColumnAndRow($colNo, $rowNo + 5)->getAlignment()->setWrapText(true);
                    $colNo++;
                }
            }

            $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            $fileSafeShipmentCode = str_replace( ' ', '-', str_replace(array_merge(
                array_map('chr', range(0, 31)),
                array('<', '>', ':', '"', '/', '\\', '|', '?', '*')
            ), '', $params['shipmentCode']));
            $filename = $fileSafeShipmentCode.'-not-responded-participant-report-' . date('d-M-Y-H-i-s') . '.xls';
            $writer->save(UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
            return $filename;
		} catch (Exception $exc) {
				return "";
				$sQuerySession->correctiveActionsQuery = '';
				error_log("GENERATE-SHIPMENT-NOT-RESPONDED-PARTICIPANT-REPORT-EXCEL--" . $exc->getMessage());
				error_log($exc->getTraceAsString());
		}
	}

    public function saveTempParticipants($tempParticipants) {
        $countriesDb = new Application_Model_DbTable_Countries();
        $countries = $countriesDb->getAllCountries();
        $countriesMap = array();
        $countriesMap = array_reduce($countries, function($accumulator, $country) {
            $accumulator[$country["iso_name"]] = $country;
            return $accumulator;
        }, $countriesMap);

        $emailAddressesInImport = array_column($tempParticipants, "Username");
        $dataManagerDb = new Application_Model_DbTable_DataManagers();
        $existingDataManagers = $dataManagerDb->getDataManagersByEmailAddresses($emailAddressesInImport);
        $existingDataManagersMap = array();
        $existingDataManagersMap = array_reduce($existingDataManagers, function($accumulator, $existingDataManager) {
            if (!isset($accumulator[$existingDataManager["primary_email"]])) {
                $accumulator[$existingDataManager["primary_email"]] = array();
            }
            $accumulator[$existingDataManager["primary_email"]] = $existingDataManager;
            return $accumulator;
        }, $existingDataManagersMap);

        $ptIdsInImport = array_column($tempParticipants, "PT ID");
        $participantDb = new Application_Model_DbTable_Participants();
        $existingParticipants = $participantDb->getParticipantsByUniqueIds($ptIdsInImport);
        $existingParticipantsMap = array();
        $existingParticipantsMap = array_reduce($existingParticipants, function($accumulator, $existingParticipant) {
            if (!isset($accumulator[$existingParticipant["unique_identifier"]])) {
                $accumulator[$existingParticipant["unique_identifier"]] = array();
            }
            if (isset($existingParticipant["username"]) && $existingParticipant["username"]) {
                $accumulator[$existingParticipant["unique_identifier"]][$existingParticipant["username"]] = $existingParticipant;
            } else {
                $accumulator[$existingParticipant["unique_identifier"]]["none"] = $existingParticipant;
            }
            return $accumulator;
        }, $existingParticipantsMap);
        $usernamePasswordMap = array();
        $tempParticipantsMap = array();
        for($i = 0; $i < count($tempParticipants); $i++) {
            if (!$tempParticipants[$i]["PT ID"]) {
                throw new Exception("The sheet contains a record with a blank PT ID which is a required field. Please check ".$tempParticipants[$i]["Lab Name"]."?");
            }
            if (!isset($tempParticipantsMap[$tempParticipants[$i]["PT ID"]])) {
                $tempParticipantsMap[$tempParticipants[$i]["PT ID"]] = array();
            }
            $username = "none";
            if (isset($tempParticipants[$i]["Username"]) && $tempParticipants[$i]["Username"]) {
                $username = $tempParticipants[$i]["Username"];
            }
            if (isset($tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username])) {
                throw new Exception("The sheet contains a duplicate records for ".$tempParticipants[$i]["PT ID"]." and ".$username.".");
            }
            if (!isset($tempParticipants[$i]["Country"]) || !$tempParticipants[$i]["Country"]) {
                throw new Exception("The sheet contains a record with a blank Country which is a required field. Please check ".$tempParticipants[$i]["PT ID"]."?");
            }
            if (!isset($tempParticipants[$i]["Lab Name"]) || !$tempParticipants[$i]["Lab Name"]) {
                throw new Exception("The sheet contains a record with a blank Lab Name which is a required field. Please check ".$tempParticipants[$i]["PT ID"]." in ".$tempParticipants[$i]["Country"]."?");
            }
            foreach (array_keys($tempParticipantsMap[$tempParticipants[$i]["PT ID"]]) as $participantUsername) {
                if ($tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$participantUsername]["Country"] != $tempParticipants[$i]["Country"]) {
                    throw new Exception("The sheet contains a more than one row with different countries for the same PT ID. Please check ".$tempParticipants[$i]["PT ID"]." with countries ".$tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$participantUsername]["Country"]." and ".$tempParticipants[$i]["Country"]."?");
                }
                if ($tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$participantUsername]["Region"] != $tempParticipants[$i]["Region"]) {
                    if (isset($tempParticipants[$i]["Region"]) &&
                        $tempParticipants[$i]["Region"] != null &&
                        $tempParticipants[$i]["Region"] != "" &&
                        (!isset($tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$participantUsername]["Region"]) ||
                            $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$participantUsername]["Region"] == null ||
                            $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$participantUsername]["Region"] == "")) {
                        $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$participantUsername]["Region"] = $tempParticipants[$i]["Region"];
                    } else if (isset($tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$participantUsername]["Region"]) &&
                        $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$participantUsername]["Region"] != null &&
                        $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$participantUsername]["Region"] != "" &&
                        (!isset($tempParticipants[$i]["Region"]) ||
                            $tempParticipants[$i]["Region"] == null ||
                            $tempParticipants[$i]["Region"] == "")) {
                        $tempParticipants[$i]["Region"] = $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$participantUsername]["Region"];
                    } else {
                        throw new Exception("The sheet contains a more than one row with different regions for the same PT ID. Please check ".$tempParticipants[$i]["PT ID"]." with regions ".$tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$participantUsername]["Region"]." and ".$tempParticipants[$i]["Region"]."?");
                    }
                }
            }
            $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username] = $tempParticipants[$i];
            $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]["country_id"] = null;
            $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]["dm_id"] = null;
            $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]["participant_id"] = null;
            if (isset($countriesMap[$tempParticipants[$i]["Country"]])) {
                $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]["country_id"] = $countriesMap[$tempParticipants[$i]["Country"]]["id"];
            } else {
                throw new Exception("The sheet contains a record where the country cannot be determined. Please check ".$tempParticipants[$i]["PT ID"]." in ".$tempParticipants[$i]["Country"]." to make sure that the country name is correctly spelled?");
            }
            if (isset($existingDataManagersMap[$tempParticipants[$i]["Username"]])) {
                $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]["dm_id"] = $existingDataManagersMap[$username]["dm_id"];
            }
            if (isset($existingParticipantsMap[$tempParticipants[$i]["PT ID"]])) {
                $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]["participant_id"] = array_values($existingParticipantsMap[$tempParticipants[$i]["PT ID"]])[0]["participant_id"];
                if (isset($existingParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]) && !isset($tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]["dm_id"])) {
                    $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]["dm_id"] = $existingParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]["dm_id"];
                } else if (!$tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]["dm_id"] &&
                    $username != "none" && count($existingParticipantsMap[$tempParticipants[$i]["PT ID"]]) == 1) {
                    $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]["dm_id"] =
                        array_values($existingParticipantsMap[$tempParticipants[$i]["PT ID"]])[0]["dm_id"];
                }
            }
            if ($tempParticipants[$i]["Password"]) {
                $tempParticipantsMap[$tempParticipants[$i]["PT ID"]][$username]["password"] = $tempParticipants[$i]["Password"];
                if (!isset($usernamePasswordMap[$username])) {
                    $usernamePasswordMap[$username] = $tempParticipants[$i]["Password"];
                } else if ($usernamePasswordMap[$username] != $tempParticipants[$i]["Password"]) {
                    throw new Exception("The sheet contains a different passwords for the same user. Please check passwords entered for ".$username."?");
                }
            }
        }

        $tempParticipantInserts = array();
        foreach (array_keys($tempParticipantsMap) as $ptId) {
            $participantLabName = null;
            $participantEmailAddress = null;
            $participantPhoneNumber = null;
            $participantActive = "No";
            if (isset($existingParticipantsMap[$ptId])) {
                $existingParticipant = $existingParticipantsMap[$ptId];
                foreach (array_keys($tempParticipantsMap[$ptId]) as $participantUsername) {
                    if (isset($existingParticipant) && isset($existingParticipant[$participantUsername])) {
                        $existingParticipantUser = $existingParticipant[$participantUsername];
                        if ($participantLabName == null && $existingParticipantUser["lab_name"] == $tempParticipantsMap[$ptId][$participantUsername]["Lab Name"]) {
                            $participantLabName = $existingParticipantUser["lab_name"];
                        }
                        if ($participantEmailAddress == null && $existingParticipantUser["email"] == $tempParticipantsMap[$ptId][$participantUsername]["Username"] &&
                            (strlen($tempParticipantsMap[$ptId][$participantUsername]["Username"]) < 12 ||
                                substr($tempParticipantsMap[$ptId][$participantUsername]["Username"], -12, 10) != "systemone.")) {
                            $participantEmailAddress = $existingParticipantUser["email"];
                        }
                        if ($participantPhoneNumber == null && $existingParticipantUser["phone"] == $tempParticipantsMap[$ptId][$participantUsername]["Phone Number"]) {
                            $participantPhoneNumber = $existingParticipantUser["phone"];
                        }
                    }
                }
            }
            if (isset($tempParticipantsMap[$ptId])) {
                foreach (array_keys($tempParticipantsMap[$ptId]) as $participantUsername) {
                    if ($participantActive == "No" && $tempParticipantsMap[$ptId][$participantUsername]["Active"] != "No") {
                        $participantActive = "Yes";
                    }
                }
            }
            foreach (array_keys($tempParticipantsMap[$ptId]) as $participantUsername) {
                if ($participantLabName == null && $tempParticipantsMap[$ptId][$participantUsername]["Lab Name"]) {
                    $participantLabName = $tempParticipantsMap[$ptId][$participantUsername]["Lab Name"];
                }
                if ($participantEmailAddress == null && $tempParticipantsMap[$ptId][$participantUsername]["Username"] &&
                    (strlen($tempParticipantsMap[$ptId][$participantUsername]["Username"]) < 12 ||
                        substr($tempParticipantsMap[$ptId][$participantUsername]["Username"], -12, 10) != "systemone.")) {
                    $participantEmailAddress = $tempParticipantsMap[$ptId][$participantUsername]["Username"];
                }
                if ($participantPhoneNumber == null && $tempParticipantsMap[$ptId][$participantUsername]["Phone Number"]) {
                    $participantPhoneNumber = $tempParticipantsMap[$ptId][$participantUsername]["Phone Number"];
                }
            }
            if (isset($existingParticipant) && count($existingParticipant) > 0) {
                $existingParticipantUser = array_values($existingParticipant)[0];
                if ($participantLabName == null) {
                    $participantLabName = $existingParticipantUser["lab_name"];
                }
                if ($participantEmailAddress == null) {
                    $participantEmailAddress = $existingParticipantUser["email"];
                }
                if ($participantPhoneNumber == null) {
                    $participantPhoneNumber = $existingParticipantUser["phone"];
                }
            }
            foreach (array_keys($tempParticipantsMap[$ptId]) as $participantUsername) {
                $tempParticipantsMap[$ptId][$participantUsername]["Lab Name"] = $participantLabName;
                $tempParticipantsMap[$ptId][$participantUsername]["Email"] = $participantEmailAddress;
                $tempParticipantsMap[$ptId][$participantUsername]["Phone Number"] = $participantPhoneNumber;
                if (count($tempParticipantsMap[$ptId]) == 1 &&
                    $participantEmailAddress &&
                    $tempParticipantsMap[$ptId][$participantUsername]["Username"] &&
                    (strlen($participantUsername) < 12 || substr($participantUsername, -12, 10) != "systemone.") &&
                    (strlen($participantEmailAddress) < 12 || substr($participantEmailAddress, -12, 10) != "systemone.")) {
                    $tempParticipantsMap[$ptId][$participantUsername]["Username"] = $participantEmailAddress;
                }
                if ($tempParticipantsMap[$ptId][$participantUsername]["Username"]) {
                    $tempParticipantsMap[$ptId][$participantUsername]["status"] = $tempParticipantsMap[$ptId][$participantUsername]["Active"] == "No" ? "inactive" : "active";
                }
                $tempParticipantsMap[$ptId][$participantUsername]["participant_status"] = $participantActive == "No" ? "inactive" : "active";
                $tempParticipantInserts[] = $tempParticipantsMap[$ptId][$participantUsername];
            }
        }
        $participantTempDb = new Application_Model_DbTable_ParticipantTemp();
        $participantTempDb->clearParticipantTempRecords();
        $participantTempDb->addParticipantTempRecords($tempParticipantInserts);

        return $participantTempDb->getParticipantTempRecords();
    }

    public function confirmImportTempParticipants() {
        $participantTempDb = new Application_Model_DbTable_ParticipantTemp();
        $participantTempRecords = $participantTempDb->getParticipantTempRecords();

        $participantDb = new Application_Model_DbTable_Participants();
        $siteType = $this->getSiteType("Laboratory");
        $userService = new Application_Service_DataManagers();
        $participantManagerMaps = array();
        foreach ($participantTempRecords as $participantTempRecord) {
            if ($participantTempRecord["insert"]) {
                $newParticipant = array(
                    "pid" => $participantTempRecord["unique_identifier"],
                    "country" => $participantTempRecord["country_id"],
                    "pname" => $participantTempRecord["lab_name"],
                    "pphone1" => $participantTempRecord["phone_number"],
                    "pemail" => $participantTempRecord["email"],
                    "siteType" => $siteType["r_stid"],
                    "region" => $participantTempRecord["region"],
                    "status" => $participantTempRecord["participant_status"],
                    "instituteName" => null,
                    "departmentName" => null,
                    "address" => null,
                    "city" => null,
                    "state" => null,
                    "zip" => null,
                    "long" => null,
                    "lat" => null,
                    "shippingAddress" => null,
                    "pphone2" => null,
                    "contactname" => null,
                    "partAff" => null,
                    "network" => null,
                    "testingVolume" => null,
                    "fundingSource" => null
                );
                $dataManagerIds = array();
                if (isset($participantTempRecord["dm_id"]) && $participantTempRecord["dm_id"]) {
                    $dataManagerIds[] = $participantTempRecord["dm_id"];
                } else {
                    $dataManager = $userService->getUserInfo($participantTempRecord["username"]);
                    if ($dataManager && isset($dataManager["dm_id"])) {
                        if ($participantTempRecord["update_username"] ||
                            $participantTempRecord["update_password"] ||
                            $participantTempRecord["update_phone_number"] ||
                            $participantTempRecord["update_dm_status"]) {
                            $updatedUser = array(
                                "userSystemId" => $dataManager["dm_id"],
                                "fname" => $dataManager["first_name"],
                                "lname" => $dataManager["last_name"],
                                "phone1" => $dataManager["mobile"],
                                "semail" => $dataManager["secondary_email"]
                            );
                            if (!isset($dataManager["last_name"]) || $dataManager["last_name"] == null || $dataManager["last_name"] == "") {
                                $updatedUser["fname"] = $participantTempRecord["lab_name"];
                            }
                            if ($participantTempRecord["update_username"]) {
                                $updatedUser["userId"] = $participantTempRecord['username'];
                            }
                            if ($participantTempRecord["update_password"]) {
                                $updatedUser["password"] = $participantTempRecord['password'];
                            }
                            if ($participantTempRecord["update_phone_number"]) {
                                $updatedUser["phone2"] = $participantTempRecord['phone_number'];
                            }
                            if ($participantTempRecord["update_dm_status"]) {
                                $updatedUser["status"] = $participantTempRecord['status'];
                            }
                            $userService->updateUser($updatedUser);
                        }
                        $dataManagerIds[] = $dataManager["dm_id"];
                    } else {
                        $newUser = array(
                            'fname' => $participantTempRecord["lab_name"],
                            'phone2' => $participantTempRecord['phone_number'],
                            'userId' => $participantTempRecord['username'],
                            'password' => $participantTempRecord['password'],
                            'force_password_reset' => 1,
                            'qcAccess' => "yes",
                            'receiptDateOption' => "yes",
                            'modeOfReceiptOption' => "yes",
                            'viewOnlyAccess' => "no",
                            'status' => $participantTempRecord["status"],
                            "lname" => null,
                            "institute" => null,
                            "phone1" => null,
                            "semail" => null
                        );
                        $dataManagerIds[] = $userService->addUser($newUser);
                    }
                }
                $participantId = $participantDb->addParticipant($newParticipant);
                foreach ($dataManagerIds as $dataManagerId) {
                    $participantManagerMaps[] = array(
                        "dm_id" => $dataManagerId,
                        "participant_id" => $participantId
                    );
                }
            } else if ($participantTempRecord["update"] || $participantTempRecord["insert_user"]) {
                $participant = $participantDb->getParticipant($participantTempRecord["participant_id"]);
                if ($participant &&
                    ($participantTempRecord["update_lab_name"] ||
                        $participantTempRecord["update_country"] ||
                        $participantTempRecord["update_region"] ||
                        $participantTempRecord["update_email"] ||
                        $participantTempRecord["update_participant_status"] ||
                        $participantTempRecord["update_phone_number"] ||
                        $participantTempRecord["update_username"] ||
                        $participantTempRecord["update_password"] ||
                        $participantTempRecord["update_dm_status"])) {
                    $updatedParticipant = array(
                        "participantId" => $participant["participant_id"],
                        "pid" => $participant["unique_identifier"],
                        "country" => $participant["country"],
                        "pname" => $participant["lab_name"],
                        "pphone1" => $participant["phone"],
                        "pemail" => $participant["email"],
                        "siteType" => $participant["site_type"],
                        "region" => $participant["region"],
                        "status" => $participant["status"],
                        "instituteName" => $participant["status"],
                        "departmentName" => $participant["department_name"],
                        "address" => $participant["address"],
                        "city" => $participant["city"],
                        "state" => $participant["state"],
                        "zip" => $participant["zip"],
                        "long" => $participant["long"],
                        "lat" => $participant["lat"],
                        "shippingAddress" => $participant["shipping_address"],
                        "pphone2" => $participant["mobile"],
                        "contactname" => $participant["contact_name"],
                        "partAff" => $participant["affiliation"],
                        "network" => $participant["network_tier"],
                        "testingVolume" => $participant["testing_volume"],
                        "fundingSource" => $participant["funding_source"]
                    );
                    $dataManagerToUpdate = null;
                    if ($dataManagerToUpdate["dm_id"] != null) {
                        $dataManagerToUpdate = $userService->getUserInfoBySystemId($dataManagerToUpdate["dm_id"]);
                    } else {
                        $dataManagerToUpdate = $userService->getUserInfo($participantTempRecord["username"]);
                    }
                    if ($dataManagerToUpdate && isset($dataManagerToUpdate["dm_id"])) {
                        $participantManagerMaps[] = array(
                            "dm_id" => $dataManagerToUpdate["dm_id"],
                            "participant_id" => $participant["participant_id"]
                        );
                        if ($participantTempRecord["update_username"] ||
                            $participantTempRecord["update_password"] ||
                            $participantTempRecord["update_phone_number"] ||
                            $participantTempRecord["update_dm_status"]) {
                            $updatedUser = array(
                                "userSystemId" => $dataManagerToUpdate["dm_id"],
                                "fname" => $dataManagerToUpdate["first_name"],
                                "lname" => $dataManagerToUpdate["last_name"],
                                "phone1" => $dataManagerToUpdate["mobile"],
                                "phone2" => $dataManagerToUpdate["phone"],
                                "semail" => $dataManagerToUpdate["secondary_email"]
                            );
                            if (!isset($dataManagerToUpdate["last_name"]) || $dataManagerToUpdate["last_name"] == null || $dataManagerToUpdate["last_name"] == "") {
                                $updatedUser["fname"] = substr($participantTempRecord["lab_name"], 0, 45);
                            }
                            if ($participantTempRecord["update_username"]) {
                                $updatedUser["userId"] = $participantTempRecord['username'];
                            }
                            if ($participantTempRecord["update_password"]) {
                                $updatedUser["password"] = $participantTempRecord['password'];
                            }
                            if ($participantTempRecord["update_phone_number"]) {
                                $updatedUser["phone2"] = $participantTempRecord['phone_number'];
                            }
                            if ($participantTempRecord["update_dm_status"]) {
                                $updatedUser["status"] = $participantTempRecord['status'];
                            }
                            $userService->updateUser($updatedUser);
                        }
                    } else if (isset($participantTempRecord['username']) && $participantTempRecord['username']) {
                        $newUser = array(
                            'fname' => substr($participantTempRecord["lab_name"], 0, 45),
                            'phone2' => $participantTempRecord['phone_number'],
                            'userId' => $participantTempRecord['username'],
                            'password' => $participantTempRecord['password'],
                            'force_password_reset' => 1,
                            'qcAccess' => "yes",
                            'receiptDateOption' => "yes",
                            'modeOfReceiptOption' => "yes",
                            'viewOnlyAccess' => "no",
                            'status' => $participantTempRecord["status"],
                            "lname" => null,
                            "institute" => null,
                            "phone1" => null,
                            "semail" => null
                        );
                        $newDataManagerId = $userService->addUser($newUser);
                        $participantManagerMaps[] = array(
                            "dm_id" => $newDataManagerId,
                            "participant_id" => $participant["participant_id"]
                        );
                    }
                    if ($participantTempRecord["update_lab_name"]) {
                        $updatedParticipant["pname"] = $participantTempRecord["lab_name"];
                    }
                    if ($participantTempRecord["update_country"]) {
                        $updatedParticipant["country"] = $participantTempRecord["country_id"];
                    }
                    if ($participantTempRecord["update_region"]) {
                        $updatedParticipant["region"] = $participantTempRecord["region"];
                    }
                    if ($participantTempRecord["update_email"]) {
                        $updatedParticipant["pemail"] = $participantTempRecord["email"];
                    }
                    if ($participantTempRecord["update_participant_status"]) {
                        $updatedParticipant["status"] = $participantTempRecord["participant_status"];
                    }
                    if ($participantTempRecord["update_phone_number"]) {
                        $updatedParticipant["pphone1"] = $participantTempRecord["phone_number"];
                    }
                    $participantDb->updateParticipant($updatedParticipant);
                }
            }
        }

        $participantIdsToLink = array();
        $participantIdsToLink = array_reduce($participantManagerMaps, function($accumulator, $participantManagerMap) {
            if (!isset($accumulator[$participantManagerMap["participant_id"]])) {
                $accumulator[$participantManagerMap["participant_id"]] = array();
            }
            $accumulator[$participantManagerMap["participant_id"]][] = $participantManagerMap["dm_id"];
            return $accumulator;
        }, $participantIdsToLink);
        foreach($participantIdsToLink as $participantId => $dataManagerIds) {
            $participantDb->addParticipantManager(array(
                "participantId" => $participantId,
                "datamanagers" => array_unique($dataManagerIds)
            ));
        }
    }

    public function generateParticipantDataForImportTemplate() {
        $participantDb = new Application_Model_DbTable_Participants();
        return $participantDb->generateParticipantDataForImportTemplate();
    }
}

