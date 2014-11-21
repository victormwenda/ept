<?php
include("PHPExcel.php");
class Application_Service_Reports {
	
	public function getAllShipments($parameters)
	{
	    /* Array of database columns which should be read and sent back to DataTables. Use a space where
	     * you want to insert a non-database field (for example a counter or static image)
	     */
    
	    $aColumns = array('distribution_code', "DATE_FORMAT(distribution_date,'%d-%b-%Y')", 's.shipment_code',"DATE_FORMAT(s.lastdate_response,'%d-%b-%Y')",'sl.scheme_name' ,'s.number_of_samples' ,new Zend_Db_Expr('count("participant_id")'),new Zend_Db_Expr("SUM(shipment_test_date <> '')"),new Zend_Db_Expr("(SUM(shipment_test_date <> '')/count('participant_id'))*100"),new Zend_Db_Expr("SUM(final_result = 1)"),'s.status');
	    $searchColumns = array('distribution_code', "DATE_FORMAT(distribution_date,'%d-%b-%Y')", 's.shipment_code',"DATE_FORMAT(s.lastdate_response,'%d-%b-%Y')",'sl.scheme_name' ,'s.number_of_samples','participant_count','reported_count','reported_percentage','number_passed','s.status');
	    $havingColumns = array('participant_count','reported_count');
	    $orderColumns = array('distribution_code','distribution_date', 's.shipment_code','s.lastdate_response' ,'sl.scheme_name' ,'s.number_of_samples' ,new Zend_Db_Expr('count("participant_id")'),new Zend_Db_Expr("SUM(shipment_test_date <> '')"),new Zend_Db_Expr("(SUM(shipment_test_date <> '')/count('participant_id'))*100"),new Zend_Db_Expr("SUM(final_result = 1)"),'s.status');
    
	    /* Indexed column (used for fast and accurate table cardinality) */
	    $sIndexColumn = 'shipment_id';
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
		    $colSize = count($searchColumns);
		    
		    for ($i = 0; $i < $colSize; $i++) {
			if($searchColumns[$i] == "" || $searchColumns[$i] == null){
			    continue;
			}
			if ($i < $colSize - 1) {
			    $sWhereSub .= $searchColumns[$i] . " LIKE '%" . ($search) . "%' OR ";
			} else {
			    $sWhereSub .= $searchColumns[$i] . " LIKE '%" . ($search) . "%' ";
			}
		    }
		    $sWhereSub .= ")";
		}
		$sWhere .= $sWhereSub;
	    }
	    
	    //error_log($sHaving);
	    /* Individual column filtering */
	    for ($i = 0; $i < count($searchColumns); $i++) {
		if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
		    if ($sWhere == "") {
			$sWhere .= $searchColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
		    } else {
			$sWhere .= " AND " . $searchColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
		    }
		}
	    }
		
	    //
	    //
	    //$sHaving = "";
	    //if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
	    //    $searchArray = explode(" ", $parameters['sSearch']);
	    //    $sHavingSub = "";
	    //    foreach ($searchArray as $search) {
	    //        if ($sHavingSub == "") {
	    //            $sHavingSub .= "(";
	    //        } else {
	    //            $sHavingSub .= " AND (";
	    //        }
	    //        $colSize = count($havingColumns);
	    //
	    //        for ($i = 0; $i < $colSize; $i++) {
	    //            if($havingColumns[$i] == "" || $havingColumns[$i] == null){
	    //                continue;
	    //            }
	    //            if ($i < $colSize - 1) {
	    //                $sHavingSub .= $havingColumns[$i] . " LIKE '%" . ($search) . "%' OR ";
	    //            } else {
	    //                $sHavingSub .= $havingColumns[$i] . " LIKE '%" . ($search) . "%' ";
	    //            }
	    //        }
	    //        $sHavingSub .= ")";
	    //    }
	    //    $sHaving .= $sHavingSub;
	    //}			
		
	    /*
	     * SQL queries
	     * Get data to display
	     */
		    
		    $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		    $sQuery = $dbAdapter->select()->from(array('s'=>'shipment'))
				    ->join(array('sl'=>'scheme_list'),'s.scheme_type=sl.scheme_id')
				    ->join(array('d'=>'distributions'),'d.distribution_id=s.distribution_id')
				    ->joinLeft(array('sp'=>'shipment_participant_map'),'sp.shipment_id=s.shipment_id',array('report_generated','participant_count' => new Zend_Db_Expr('count("participant_id")'), 'reported_count'=> new Zend_Db_Expr("SUM(shipment_test_date <> '')"),'reported_percentage' => new Zend_Db_Expr("ROUND((SUM(shipment_test_date <> '')/count('participant_id'))*100,2)"), 'number_passed'=> new Zend_Db_Expr("SUM(final_result = 1)")))
				    ->joinLeft(array('p'=>'participant'),'p.participant_id=sp.participant_id')
				    //->joinLeft(array('pmm'=>'participant_manager_map'),'pmm.participant_id=p.participant_id')
				    ->joinLeft(array('rr'=>'r_results'),'sp.final_result=rr.result_id')
				    ->group(array('s.shipment_id'));
				  
				    
					
		    if(isset($parameters['scheme']) && $parameters['scheme'] !=""){
			    $sQuery = $sQuery->where("s.scheme_type = ?",$parameters['scheme']);
		    }
		    
		    if(isset($parameters['startDate']) && $parameters['startDate'] !="" && isset($parameters['endDate']) && $parameters['endDate'] !=""){
			    $sQuery = $sQuery->where("s.shipment_date >= ?",$parameters['startDate']);
			    $sQuery = $sQuery->where("s.shipment_date <= ?",$parameters['endDate']);
		    }
		    
		    if(isset($parameters['dataManager']) && $parameters['dataManager'] !=""){
				$sQuery=$sQuery->joinLeft(array('pmm'=>'participant_manager_map'),'pmm.participant_id=p.participant_id');
				$sQuery = $sQuery->where("pmm.dm_id = ?",$parameters['dataManager']);
		    }
		    
    
	    if (isset($sWhere) && $sWhere != "") {
		$sQuery = $sQuery->having($sWhere);
	    }
	    
	    //if (isset($sHaving) && $sHaving != "") {
	       // $sQuery = $sQuery->having($sHaving);
	   // }
			    
    
	    if (isset($sOrder) && $sOrder != "") {
		$sQuery = $sQuery->order($sOrder);
	    }
    
	    if (isset($sLimit) && isset($sOffset)) {
		$sQuery = $sQuery->limit($sLimit, $sOffset);
	    }
    
	    //error_log($sQuery);
    
	    $rResult = $dbAdapter->fetchAll($sQuery);
    
    
	    /* Data set length after filtering */
	    $sQuery = $sQuery->reset(Zend_Db_Select::LIMIT_COUNT);
	    $sQuery = $sQuery->reset(Zend_Db_Select::LIMIT_OFFSET);
	    $aResultFilterTotal = $dbAdapter->fetchAll($sQuery);
	    $iFilteredTotal = count($aResultFilterTotal);
    
	    /* Total data set length */
	    $sQuery = $dbAdapter->select()->from(array('s'=>'shipment'), new Zend_Db_Expr("COUNT('" . $sIndexColumn . "')"));
	    $aResultTotal = $dbAdapter->fetchCol($sQuery);
	    $iTotal = $aResultTotal[0];
    
	    /*
	     * Output
	     */
	    $output = array(
		"sEcho" => intval($parameters['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	    );
    
	    
	    $shipmentDb = new Application_Model_DbTable_Shipments();
		    //$aColumns = array('distribution_code', "DATE_FORMAT(distribution_date,'%d-%b-%Y')",
		    //'s.shipment_code' ,'sl.scheme_name' ,'s.number_of_samples' ,
		    //'sp.participant_count','sp.reported_count','sp.number_passed','s.status');
	    foreach ($rResult as $aRow) {
                $download=' No Download Available ';
		    if(isset($aRow['report_generated']) && $aRow['report_generated']=='yes'){
                        if (file_exists(UPLOAD_PATH. DIRECTORY_SEPARATOR."reports". DIRECTORY_SEPARATOR . $aRow['shipment_code']. DIRECTORY_SEPARATOR."summary.pdf")) {
                            $download='<a href="/uploads/reports/'. $aRow['shipment_code'].'/summary.pdf" class=\'btn btn-info btn-xs\'><i class=\'icon-download\'></i> Summary</a>';
                        }
                    }
		    $shipmentResults = $shipmentDb->getPendingShipmentsByDistribution($aRow['distribution_id']);
		    $responsePercentage=($aRow['reported_percentage'] != "") ? $aRow['reported_percentage'] : "0";
		    $row = array();
		    $row[] = $aRow['distribution_code'];
		    $row[] = Pt_Commons_General::humanDateFormat($aRow['distribution_date']);
		    $row[] = "<a href='javascript:void(0);' onclick='generateShipmentParticipantList(\"".base64_encode($aRow['shipment_id'])."\")'>".$aRow['shipment_code']."</a>";
		    $row[] = Pt_Commons_General::humanDateFormat($aRow['lastdate_response']);
		    $row[] = $aRow['scheme_name'];
		    $row[] = $aRow['number_of_samples'];
		    $row[] = $aRow['participant_count'];
		    $row[] = ($aRow['reported_count'] != "") ? $aRow['reported_count'] : 0;
		   // $row[] = ($aRow['reported_percentage'] != "") ? $aRow['reported_percentage'] : "0";
		    $row[] = '<a href="/reports/shipments/response-chart/id/'.base64_encode($aRow['shipment_id']).'/shipmentDate/'.base64_encode($aRow['distribution_date']).'/shipmentCode/'.base64_encode($aRow['distribution_code']).'" target="_blank" style="text-decoration:underline">'.$responsePercentage.' %</a>';
		    $row[] = $aRow['number_passed'];
		    $row[] = ucwords($aRow['status']);
		    $row[] = $download;
		
		
		    $output['aaData'][] = $row;
	    }
    
	    echo json_encode($output);
	}
    
    public function updateReportConfigs($params){
	$filterRules = array('*' => 'StripTags','*' => 'StringTrim');
        $filter = new Zend_Filter_Input($filterRules, null, $params);
        if ($filter->isValid()) {
            //$params = $filter->getEscaped();
            $db = new Application_Model_DbTable_ReportConfig();
            $db->getAdapter()->beginTransaction();
            try {
                $result=$db->updateReportDetails($params);
                //$alertMsg = new Zend_Session_Namespace('alert');
                //$alertMsg->msg=" documents submitted successfully.";
		
                $db->getAdapter()->commit();
                return $result;
            } catch (Exception $exc) {
                $db->getAdapter()->rollBack();
                error_log($exc->getMessage());
                error_log($exc->getTraceAsString());
            }
        }
    }
    
    public function getReportConfigValue($name){
	$db = new Application_Model_DbTable_ReportConfig();
	return $db->getValue($name);
    }
    public function getParticipantDetailedReport($params){
	 $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
			
	if(isset($params['reportType']) && $params['reportType']=="network"){
		$sQuery = $dbAdapter->select()->from(array('n'=>'r_network_tiers'))
			->joinLeft(array('p'=>'participant'),'p.network_tier=n.network_id',array())
			//->joinLeft(array('sp'=>'shipment_participant_map'),'sp.participant_id=p.participant_id',array('participant_count'=> new Zend_Db_Expr("SUM(shipment_test_date = '') + SUM(shipment_test_date <> '')"), 'reported_count'=> new Zend_Db_Expr("SUM(shipment_test_date <> '')"), 'number_passed'=> new Zend_Db_Expr("SUM(final_result = 1)")))
			->joinLeft(array('shp'=>'shipment_participant_map'),'shp.participant_id=p.participant_id',array())
			->joinLeft(array('s'=>'shipment'),'s.shipment_id=shp.shipment_id',array('lastdate_response'))
			->joinLeft(array('sp'=>'shipment_participant_map'),'sp.participant_id=p.participant_id',array('others'=> new Zend_Db_Expr("SUM(sp.shipment_test_date IS NULL)"), 'number_failed'=> new Zend_Db_Expr("SUM(sp.final_result = 2 AND sp.shipment_test_date <= s.lastdate_response)"), 'number_passed'=> new Zend_Db_Expr("SUM(sp.final_result = 1 AND sp.shipment_test_date <= s.lastdate_response)"),'number_late'=> new Zend_Db_Expr("SUM(sp.shipment_test_date > s.lastdate_response)"),'map_id'))
			->joinLeft(array('sl'=>'scheme_list'),'s.scheme_type=sl.scheme_id',array())
			->joinLeft(array('d'=>'distributions'),'d.distribution_id=s.distribution_id',array())			
			->joinLeft(array('rr'=>'r_results'),'sp.final_result=rr.result_id',array())
			->group('n.network_id')/*->where("p.status = 'active'")*/;
	}
	
	if(isset($params['reportType']) && $params['reportType']=="affiliation"){
		$sQuery = $dbAdapter->select()->from(array('pa'=>'r_participant_affiliates'))
			->joinLeft(array('p'=>'participant'),'p.affiliation=pa.affiliate',array())
			//->joinLeft(array('sp'=>'shipment_participant_map'),'sp.participant_id=p.participant_id',array('participant_count'=> new Zend_Db_Expr("SUM(shipment_test_date = '') + SUM(shipment_test_date <> '')"), 'reported_count'=> new Zend_Db_Expr("SUM(shipment_test_date <> '')"), 'number_passed'=> new Zend_Db_Expr("SUM(final_result = 1)")))
			->joinLeft(array('shp'=>'shipment_participant_map'),'shp.participant_id=p.participant_id',array())
			->joinLeft(array('s'=>'shipment'),'s.shipment_id=shp.shipment_id',array('lastdate_response'))
			->joinLeft(array('sp'=>'shipment_participant_map'),'sp.participant_id=p.participant_id',array('others'=> new Zend_Db_Expr("SUM(sp.shipment_test_date IS NULL)"), 'number_failed'=> new Zend_Db_Expr("SUM(sp.final_result = 2 AND sp.shipment_test_date <= s.lastdate_response)"), 'number_passed'=> new Zend_Db_Expr("SUM(sp.final_result = 1 AND sp.shipment_test_date <= s.lastdate_response)"),'number_late'=> new Zend_Db_Expr("SUM(sp.shipment_test_date > s.lastdate_response)")))
			->joinLeft(array('sl'=>'scheme_list'),'s.scheme_type=sl.scheme_id',array())
			->joinLeft(array('d'=>'distributions'),'d.distribution_id=s.distribution_id',array())			
			->joinLeft(array('rr'=>'r_results'),'sp.final_result=rr.result_id',array())
			->group('pa.aff_id')/*->where("p.status = 'active'")*/;
	}
	if(isset($params['reportType']) && $params['reportType']=="region"){
		$sQuery = $dbAdapter->select()->from(array('p'=>'participant'),array('p.region'))
			//->joinLeft(array('sp'=>'shipment_participant_map'),'sp.participant_id=p.participant_id',array('participant_count'=> new Zend_Db_Expr("SUM(shipment_test_date = '') + SUM(shipment_test_date <> '')"), 'reported_count'=> new Zend_Db_Expr("SUM(shipment_test_date <> '')"), 'number_passed'=> new Zend_Db_Expr("SUM(final_result = 1)")))
			->joinLeft(array('shp'=>'shipment_participant_map'),'shp.participant_id=p.participant_id',array())
			->joinLeft(array('s'=>'shipment'),'s.shipment_id=shp.shipment_id',array('lastdate_response'))			
			->joinLeft(array('sp'=>'shipment_participant_map'),'sp.participant_id=p.participant_id',array('others'=> new Zend_Db_Expr("SUM(sp.shipment_test_date IS NULL)"), 'number_failed'=> new Zend_Db_Expr("SUM(sp.final_result = 2 AND sp.shipment_test_date <= s.lastdate_response)"), 'number_passed'=> new Zend_Db_Expr("SUM(sp.final_result = 1 AND sp.shipment_test_date <= s.lastdate_response)"),'number_late'=> new Zend_Db_Expr("SUM(sp.shipment_test_date > s.lastdate_response)")))
		        ->joinLeft(array('sl'=>'scheme_list'),'s.scheme_type=sl.scheme_id',array())
			->joinLeft(array('d'=>'distributions'),'d.distribution_id=s.distribution_id',array())			
			->joinLeft(array('rr'=>'r_results'),'sp.final_result=rr.result_id',array())
			->group('p.region')->where("p.region IS NOT NULL")->where("p.region != ''")/*->where("p.status = 'active'")*/;
	}		    
	if(isset($params['scheme']) && $params['scheme'] !=""){
		$sQuery = $sQuery->where("s.scheme_type = ?",$params['scheme']);
	}
	
	if(isset($params['startDate']) && $params['startDate'] !="" && isset($params['endDate']) && $params['endDate'] !=""){
		$sQuery = $sQuery->where("s.shipment_date >= ?",$params['startDate']);
		$sQuery = $sQuery->where("s.shipment_date <= ?",$params['endDate']);
	}
	//echo $sQuery;die;
	return $dbAdapter->fetchAll($sQuery);
    }
    public function getAllParticipantDetailedReport($parameters)
	{
	    /* Array of database columns which should be read and sent back to DataTables. Use a space where
	     * you want to insert a non-database field (for example a counter or static image)
	     */
    
	    $aColumns = array('s.shipment_code','sl.scheme_name','distribution_code', "DATE_FORMAT(distribution_date,'%d-%b-%Y')");
	    
	    /*
	     * Paging
	     */
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
                    $sOrder .= $aColumns[intval($parameters['iSortCol_' . $i])] . "
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
		$sQuery = $dbAdapter->select()->from(array('s'=>'shipment'))
				->join(array('sl'=>'scheme_list'),'s.scheme_type=sl.scheme_id')
				->join(array('d'=>'distributions'),'d.distribution_id=s.distribution_id')
				->group('s.shipment_id');
		if(isset($parameters['startDate']) && $parameters['startDate'] !="" && isset($parameters['endDate']) && $parameters['endDate'] !=""){
			$sQuery = $sQuery->where("s.shipment_date >= ?",$parameters['startDate']);
			$sQuery = $sQuery->where("s.shipment_date <= ?",$parameters['endDate']);
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
	
		$rResult = $dbAdapter->fetchAll($sQuery);
    
    
	     /* Data set length after filtering */
        $sQuery = $sQuery->reset(Zend_Db_Select::LIMIT_COUNT);
        $sQuery = $sQuery->reset(Zend_Db_Select::LIMIT_OFFSET);
        $aResultFilterTotal = $dbAdapter->fetchAll($sQuery);
        $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
	
        $aResultTotal = $dbAdapter->fetchAll($sQuery);
        $iTotal = sizeof($aResultTotal);

        /*
         * Output
         */
        $output = array(
            "sEcho" => intval($parameters['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

	    foreach ($rResult as $aRow) {
		    $row = array();
		    $row[] = $aRow['shipment_code'];
		    $row[] = ucwords($aRow['scheme_name']);
		    $row[] = $aRow['distribution_code'];
		    $row[] = Pt_Commons_General::humanDateFormat($aRow['distribution_date']);
		    $output['aaData'][] = $row;
	    }
    
	    echo json_encode($output);
	}
	public function getTestKitReport($params){
		//Zend_Debug::dump($params);die;
		$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		  $sQuery = $dbAdapter->select()->from(array('res'=>'response_result_dts'),array('totalTest'=>new Zend_Db_Expr("CAST((COUNT('shipment_map_id')/s.number_of_samples) as UNSIGNED)")))
						->joinLeft(array('sp'=>'shipment_participant_map'),'sp.map_id=res.shipment_map_id',array())
						->joinLeft(array('p'=>'participant'),'sp.participant_id=p.participant_id',array())
						->joinLeft(array('s'=>'shipment'),'s.shipment_id=sp.shipment_id',array());
				
		if(isset($params['kitType']) && $params['kitType']=="testkit1"){
		$sQuery = $sQuery->joinLeft(array('tn'=>'r_testkitname_dts'),'tn.TestKitName_ID=res.test_kit_name_1',array('TestKit_Name'))
				->group('tn.TestKitName_ID');
		}
		if(isset($params['kitType']) && $params['kitType']=="testkit2"){
		$sQuery = $sQuery->joinLeft(array('tn'=>'r_testkitname_dts'),'tn.TestKitName_ID=res.test_kit_name_2',array('TestKit_Name'))
				->group('tn.TestKitName_ID');
		}
		if(isset($params['kitType']) && $params['kitType']=="testkit3"){
		$sQuery = $sQuery->joinLeft(array('tn'=>'r_testkitname_dts'),'tn.TestKitName_ID=res.test_kit_name_3',array('TestKit_Name'))
				->group('tn.TestKitName_ID');
		}
		if(isset($params['reportType']) && $params['reportType']=="network"){
			if(isset($params['networkValue']) && $params['networkValue']!=""){
				$sQuery = $sQuery->where("p.network_tier = ?",$params['networkValue']);
			}else{
			 $sQuery = $sQuery->joinLeft(array('n'=>'r_network_tiers'),'p.network_tier=n.network_id',array())->group('n.network_id');
			}
			
		}
		if(isset($params['reportType']) && $params['reportType']=="affiliation"){
			if(isset($params['affiliateValue']) && $params['affiliateValue']!=""){
				$iQuery= $dbAdapter->select()->from(array('rpa'=>'r_participant_affiliates'))
				                             ->where('rpa.aff_id=?',$params['affiliateValue']);
				$iResult=$dbAdapter->fetchRow($iQuery);
				$appliate=$iResult['affiliate'];
				$sQuery = $sQuery->where('p.affiliation="'.$appliate.'" OR p.affiliation='.$params['affiliateValue']);
			}else{
			 $sQuery = $sQuery->joinLeft(array('pa'=>'r_participant_affiliates'),'p.affiliation=pa.affiliate',array());
			}
			//echo $sQuery;die;
			
		}
		if(isset($params['reportType']) && $params['reportType']=="region"){
			if(isset($params['regionValue']) && $params['regionValue']!=""){
				$sQuery = $sQuery->where("p.region= ?",$params['regionValue']);
			}else{
			 $sQuery = $sQuery->group('p.region')->where("p.region IS NOT NULL")->where("p.region != ''");
			}
			
		}
		if(isset($params['startDate']) && $params['startDate'] !="" && isset($params['endDate']) && $params['endDate'] !=""){
			$sQuery = $sQuery->where("s.shipment_date >= ?",$params['startDate']);
			$sQuery = $sQuery->where("s.shipment_date <= ?",$params['endDate']);
		}
		$sQuery=$sQuery->where("tn.TestKit_Name IS NOT NULL");
		//echo $sQuery;die;
		return $dbAdapter->fetchAll($sQuery);
        }
	public function getTestKitDetailedReport($parameters)
	{
		//Zend_Debug::dump($parameters);die;
	    /* Array of database columns which should be read and sent back to DataTables. Use a space where
	     * you want to insert a non-database field (for example a counter or static image)
	     */
    
	    $aColumns = array('p.lab_name','tn.TestKit_Name');
	    
	    /*
	     * Paging
	     */
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
                    $sOrder .= $aColumns[intval($parameters['iSortCol_' . $i])] . "
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
		$sQuery = $dbAdapter->select()->from(array('res'=>'response_result_dts'),array())
			->joinLeft(array('sp'=>'shipment_participant_map'),'sp.map_id=res.shipment_map_id',array())
			->joinLeft(array('p'=>'participant'),'sp.participant_id=p.participant_id',array('p.lab_name','participantName' => new Zend_Db_Expr("GROUP_CONCAT(DISTINCT p.first_name,\" \",p.last_name ORDER BY p.first_name SEPARATOR ', ')")))
			->joinLeft(array('s'=>'shipment'),'s.shipment_id=sp.shipment_id',array())
                        ->group("p.participant_id");
				
		if(isset($parameters['kitType']) && $parameters['kitType']=="testkit1"){
		$sQuery = $sQuery->joinLeft(array('tn'=>'r_testkitname_dts'),'tn.TestKitName_ID=res.test_kit_name_1',array('tn.TestKit_Name'))
				->group('tn.TestKitName_ID');
		}
		if(isset($parameters['kitType']) && $parameters['kitType']=="testkit2"){
		$sQuery = $sQuery->joinLeft(array('tn'=>'r_testkitname_dts'),'tn.TestKitName_ID=res.test_kit_name_2',array('tn.TestKit_Name'))
				->group('tn.TestKitName_ID');
		}
		if(isset($parameters['kitType']) && $parameters['kitType']=="testkit3"){
		$sQuery = $sQuery->joinLeft(array('tn'=>'r_testkitname_dts'),'tn.TestKitName_ID=res.test_kit_name_3',array('tn.TestKit_Name'))
				->group('tn.TestKitName_ID');
		}
		if(isset($parameters['reportType']) && $parameters['reportType']=="network"){
			if(isset($parameters['networkValue']) && $parameters['networkValue']!=""){
				$sQuery = $sQuery->where("p.network_tier = ?",$parameters['networkValue']);
			}else{
			 $sQuery = $sQuery->joinLeft(array('n'=>'r_network_tiers'),'p.network_tier=n.network_id',array())->group('n.network_id');
			}
			
		}
		if(isset($parameters['reportType']) && $parameters['reportType']=="affiliation"){
			if(isset($parameters['affiliateValue']) && $parameters['affiliateValue']!=""){
				$iQuery= $dbAdapter->select()->from(array('rpa'=>'r_participant_affiliates'))
				                             ->where('rpa.aff_id=?',$parameters['affiliateValue']);
				$iResult=$dbAdapter->fetchRow($iQuery);
				$appliate=$iResult['affiliate'];
				$sQuery = $sQuery->where('p.affiliation="'.$appliate.'" OR p.affiliation='.$parameters['affiliateValue']);
			}else{
			 $sQuery = $sQuery->joinLeft(array('pa'=>'r_participant_affiliates'),'p.affiliation=pa.affiliate',array());
			}
			
		}
		if(isset($parameters['reportType']) && $parameters['reportType']=="region"){
			if(isset($parameters['regionValue']) && $parameters['regionValue']!=""){
				$sQuery = $sQuery->where("p.region= ?",$parameters['regionValue']);
			}else{
			 $sQuery = $sQuery->group('p.region')->where("p.region IS NOT NULL")->where("p.region != ''");
			}
			
		}
		if(isset($parameters['startDate']) && $parameters['startDate'] !="" && isset($parameters['endDate']) && $parameters['endDate'] !=""){
			$sQuery = $sQuery->where("s.shipment_date >= ?",$parameters['startDate']);
			$sQuery = $sQuery->where("s.shipment_date <= ?",$parameters['endDate']);
		}
		$sQuery=$sQuery->where("tn.TestKit_Name IS NOT NULL");
		if (isset($sWhere) && $sWhere != "") {
		    $sQuery = $sQuery->where($sWhere);
		}
		    
		if (isset($sOrder) && $sOrder != "") {
		    $sQuery = $sQuery->order($sOrder);
		}
	
		if (isset($sLimit) && isset($sOffset)) {
		    $sQuery = $sQuery->limit($sLimit, $sOffset);
		}
		$rResult = $dbAdapter->fetchAll($sQuery);
    
    
		/* Data set length after filtering */
		$sQuery = $sQuery->reset(Zend_Db_Select::LIMIT_COUNT);
		$sQuery = $sQuery->reset(Zend_Db_Select::LIMIT_OFFSET);
		$aResultFilterTotal = $dbAdapter->fetchAll($sQuery);
		$iFilteredTotal = count($aResultFilterTotal);

		/* Total data set length */
		
		$aResultTotal = $dbAdapter->fetchAll($sQuery);
		$iTotal = sizeof($aResultTotal);
	
		/*
		 * Output
		 */
		$output = array(
		    "sEcho" => intval($parameters['sEcho']),
		    "iTotalRecords" => $iTotal,
		    "iTotalDisplayRecords" => $iFilteredTotal,
		    "aaData" => array()
		);

		foreach ($rResult as $aRow) {
			$row = array();
			$row[] = $aRow['participantName'];
			$row[] = stripslashes($aRow['TestKit_Name']);
			$output['aaData'][] = $row;
		}
		
		echo json_encode($output);
	}
	
	
	public function getShipmentResponseCount($shipmentId, $date, $step=5,$maxDays = 60){
		$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		$responseResult=array();
		$responseDate=array();
		$initialStartDate=$date;
		for($i=$step; $i<=$maxDays;$i+=$step){
			
			$sQuery = $dbAdapter->select()->from(array('s'=>'shipment'),array(''))
					       ->joinLeft(array('sp'=>'shipment_participant_map'),'sp.shipment_id=s.shipment_id',array('reported_count'=> new Zend_Db_Expr("SUM(shipment_test_date <> '')")))
					      ->where("s.shipment_id = ?",$shipmentId)
					       ->group('s.shipment_id');
			$endDate = strftime("%Y-%m-%d", strtotime("$date + $i day"));
			
			if(isset($date) && $date !="" && $endDate!='' && $i<$maxDays){
				$sQuery = $sQuery->where("sp.shipment_test_date >= ?",$date);
				$sQuery = $sQuery->where("sp.shipment_test_date <= ?",$endDate);
				$result= $dbAdapter->fetchAll($sQuery);
				$count = (isset($result[0]['reported_count'])&& $result[0]['reported_count'] != "") ? $result[0]['reported_count'] : 0;
				$responseResult[] = (int)$count;
				$responseDate[] = Pt_Commons_General::humanDateFormat($date).' '.Pt_Commons_General::humanDateFormat($endDate);
			$date=strftime("%Y-%m-%d", strtotime("$endDate +1 day"));	
			}
			
			if($i==$maxDays){
				$sQuery = $sQuery->where("sp.shipment_test_date >= ?",$date);
				$result= $dbAdapter->fetchAll($sQuery);
				$count = (isset($result[0]['reported_count'])&& $result[0]['reported_count'] != "") ? $result[0]['reported_count'] : 0;
				$responseResult[] = (int)$count;
				$responseDate[] = Pt_Commons_General::humanDateFormat($date).'  and Above';
			}
			
		}
		return json_encode($responseResult).'#'.json_encode($responseDate);
	}
	
	public function getShipmentParticipant($shipmentId){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		$excel = new PHPExcel();
		//$sheet = $excel->getActiveSheet();
		
		
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize' => '80MB');
		
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
				'style' => PHPExcel_Style_Border::BORDER_THICK,
			    ),
			)
		);
		
		$borderStyle = array(
			'alignment' => array(
			    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
			    'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
			    ),
			)
		);
		
		$query=$db->select()->from('shipment',array('shipment_id','shipment_code','scheme_type','number_of_samples'))
					->where("shipment_id = ?", $shipmentId);
		$result=$db->fetchRow($query);
		
		if ($result['scheme_type'] == 'dts') {
			
			$refQuery=$db->select()->from(array('refRes'=>'reference_result_dts'),array('refRes.sample_label','sample_id','refRes.sample_score'))
						->joinLeft(array('r' => 'r_possibleresult'), 'r.id=refRes.reference_result', array('referenceResult'=>'r.response'))
						->where("refRes.shipment_id = ?", $shipmentId);
			$refResult=$db->fetchAll($refQuery);
			if(count($refResult)>0){
				foreach($refResult as $key=>$refRes){
					$refDtsQuery=$db->select()->from(array('refDts'=>'reference_dts_rapid_hiv'),array('refDts.lot_no','refDts.expiry_date','refDts.result'))
								->joinLeft(array('r' => 'r_possibleresult'), 'r.id=refDts.result', array('referenceKitResult'=>'r.response'))
								->joinLeft(array('tk' => 'r_testkitname_dts'), 'tk.TestKitName_ID=refDts.testkit', array('testKitName'=>'tk.TestKit_Name'))
								->where("refDts.shipment_id = ?", $shipmentId)
								->where("refDts.sample_id = ?", $refRes['sample_id']);
					$refResult[$key]['kitReference']=$db->fetchAll($refDtsQuery);
				}
			}
		}
		
		$firstSheet = new PHPExcel_Worksheet($excel, 'Instructions');
		$excel->addSheet($firstSheet, 0);
		$firstSheet->setTitle('Instructions');
		//$firstSheet->getDefaultColumnDimension()->setWidth(44);
		//$firstSheet->getDefaultRowDimension()->setRowHeight(45);
		$firstSheetHeading = array('Tab Name','Description');
		$firstSheetColNo = 0;
		$firstSheetRow = 1;
		
		$firstSheetStyle = array(
			'alignment' => array(
			    //'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
			    'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THICK,
			    ),
			)
		);
		
		foreach ($firstSheetHeading as $value) {
			$firstSheet->getCellByColumnAndRow($firstSheetColNo, $firstSheetRow)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$firstSheet->getStyleByColumnAndRow($firstSheetColNo,$firstSheetRow)->getFont()->setBold(true);
			$cellName = $firstSheet->getCellByColumnAndRow($firstSheetColNo,$firstSheetRow)->getColumn();
			$firstSheet->getStyle($cellName . $firstSheetRow)->applyFromArray($firstSheetStyle);
			$firstSheetColNo++;
		}
		
		$firstSheet->getCellByColumnAndRow(0,2)->setValueExplicit(html_entity_decode("Participant List", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		$firstSheet->getCellByColumnAndRow(1,2)->setValueExplicit(html_entity_decode("Includes dropdown lists for the following: region, department, position, RT, ELISA, received logbook", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
				
		$firstSheet->getDefaultRowDimension()->setRowHeight(10);
		$firstSheet->getColumnDimensionByColumn(0)->setWidth(20);
		$firstSheet->getDefaultRowDimension(1)->setRowHeight(70);
		$firstSheet->getColumnDimensionByColumn(1)->setWidth(100);
		
		$firstSheet->getCellByColumnAndRow(0,3)->setValueExplicit(html_entity_decode("Results Reported", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		$firstSheet->getCellByColumnAndRow(1,3)->setValueExplicit(html_entity_decode("This tab should include no commentary from NPHRL or GHSS staff.  All fields should only reflect results or comments reported on the results form.  If no report was submitted, highlight site data cells in red.  Explanation of missing results should only be comments that the site made, not PT staff.  All dates should be formatted as DD/MM/YY.  Dropdown menu legend is as followed: negative (NEG), positive (POS), invalid (INV), indeterminate (IND), not entered or reported (NE), not tested (NT) and should be used according to the way the site reported it.", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);		
		
		$firstSheet->getCellByColumnAndRow(0,4)->setValueExplicit(html_entity_decode("Panel Score", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		$firstSheet->getCellByColumnAndRow(1,4)->setValueExplicit(html_entity_decode("This tab is automatically populated.  Panel score calculated 6/6.  If a panel member must be omitted from the calculation (ie, loss of sample, etc) you must revise the equation manually by changing the number 6 to 5,4,etc. accordingly. Example seen for Akai House Clinic.", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		
		$firstSheet->getCellByColumnAndRow(0,5)->setValueExplicit(html_entity_decode("Documentation Score", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		$firstSheet->getCellByColumnAndRow(1,5)->setValueExplicit(html_entity_decode("The points breakdown for this tab are listed in the row above the sites for each column.  Data should be entered in manually by PT staff.  A site scores 1.5/3 if they used the wrong test kits got a 100% panel score.", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		
		$firstSheet->getCellByColumnAndRow(0,6)->setValueExplicit(html_entity_decode("Total Score", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		$firstSheet->getCellByColumnAndRow(1,6)->setValueExplicit(html_entity_decode("Columns C-F are populated automatically.  Columns G, H and I must be selected from the dropdown menu for each site based on the criteria listed in the 'Decision Tree' tab.", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		
		$firstSheet->getCellByColumnAndRow(0,7)->setValueExplicit(html_entity_decode("Follow-up Calls", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		$firstSheet->getCellByColumnAndRow(1,7)->setValueExplicit(html_entity_decode("Final comments or outcomes should be updated continuously with receipt dates included.", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);		
		
		$firstSheet->getCellByColumnAndRow(0,8)->setValueExplicit(html_entity_decode("Dropdown Lists", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		$firstSheet->getCellByColumnAndRow(1,8)->setValueExplicit(html_entity_decode("This tab contains all of the dropdown lists included in the rest of the database, any modifications should be performed with caution.", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		
		$firstSheet->getCellByColumnAndRow(0,9)->setValueExplicit(html_entity_decode("Decision Tree", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		$firstSheet->getCellByColumnAndRow(1,9)->setValueExplicit(html_entity_decode("Lists all of the appropriate corrective actions and scoring critieria.", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		
		$firstSheet->getCellByColumnAndRow(0,10)->setValueExplicit(html_entity_decode("Feedback Report", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		$firstSheet->getCellByColumnAndRow(1,10)->setValueExplicit(html_entity_decode("This tab is populated automatically and used to export data into the Feedback Reports generated in MS Word.", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		
		$firstSheet->getCellByColumnAndRow(0,11)->setValueExplicit(html_entity_decode("Comments", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		$firstSheet->getCellByColumnAndRow(1,11)->setValueExplicit(html_entity_decode("This tab lists all of the more detailed comments that will be given to the sites during site visits and phone calls.", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		
		
		for($counter=1;$counter<=11;$counter++){
			$firstSheet->getStyleByColumnAndRow(1,$counter)->getAlignment()->setWrapText(true);
			$firstSheet->getStyle("A$counter")->applyFromArray($firstSheetStyle);
			$firstSheet->getStyle("B$counter")->applyFromArray($firstSheetStyle);
		}
		//<------------ Participant List Details Start -----
		
		$headings = array('Facility Code', 'Facility Name', 'Region', 'Current Department','Site Type','Address','Facility Telephone','Email','Enroll Date');
		
		$sheet = new PHPExcel_Worksheet($excel, 'Participant List');
		$excel->addSheet($sheet, 1);
		$sheet->setTitle('Participant List');
		
		$sql = $db->select()->from(array('s' => 'shipment'), array('s.shipment_id', 's.shipment_code','s.number_of_samples'))
				->join(array('sp' => 'shipment_participant_map'), 'sp.shipment_id=s.shipment_id', array('sp.map_id', 'sp.participant_id','sp.attributes','sp.shipment_test_date','sp.shipment_receipt_date','sp.shipment_test_report_date','sp.supervisor_approval','sp.shipment_score','sp.documentation_score','sp.user_comment'))
				->join(array('p' => 'participant'), 'p.participant_id=sp.participant_id', array('p.unique_identifier','p.institute_name','p.department_name','p.region','p.first_name', 'p.last_name','p.address','p.city','p.mobile','p.email','p.status'))
				->joinLeft(array('pmp' => 'participant_manager_map'), 'pmp.participant_id=p.participant_id', array('pmp.dm_id'))
				->joinLeft(array('dm' => 'data_manager'), 'dm.dm_id=pmp.dm_id', array('dm.institute','dataManagerFirstName'=>'dm.first_name','dataManagerLastName'=>'dm.last_name'))
				->joinLeft(array('st' => 'r_site_type'), 'st.r_stid=p.site_type', array('st.site_type'))
				->joinLeft(array('en' => 'enrollments'), 'en.participant_id=p.participant_id', array('en.enrolled_on'))
				->where("s.shipment_id = ?", $shipmentId)
				->group(array('sp.map_id'));
		//echo $sql;die;
		$shipmentResult = $db->fetchAll($sql);
		//die;
		$colNo = 0;
		$currentRow = 1;
		$type = PHPExcel_Cell_DataType::TYPE_STRING;
		//$sheet->getCellByColumnAndRow(0, 1)->setValueExplicit(html_entity_decode("Participant List", ENT_QUOTES, 'UTF-8'), $type);
		//$sheet->getStyleByColumnAndRow(0,1)->getFont()->setBold(true);
		$sheet->getDefaultColumnDimension()->setWidth(24);
		$sheet->getDefaultRowDimension()->setRowHeight(18);
		
		foreach ($headings as $field => $value) {
			$sheet->getCellByColumnAndRow($colNo, $currentRow)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->getStyleByColumnAndRow($colNo,$currentRow)->getFont()->setBold(true);
			$cellName = $sheet->getCellByColumnAndRow($colNo,$currentRow)->getColumn();
			$sheet->getStyle($cellName . $currentRow)->applyFromArray($borderStyle);
			$colNo++;
		}
		
		if(isset($shipmentResult) && count($shipmentResult) > 0){
			$currentRow+=1;
			foreach($shipmentResult as $key=>$aRow){
				if ($result['scheme_type'] == 'dts') {
					$resQuery = $db->select()->from(array('rrdts'=>'response_result_dts'))
						->joinLeft(array('tk1' => 'r_testkitname_dts'), 'tk1.TestKitName_ID=rrdts.test_kit_name_1', array('testKitName1'=>'tk1.TestKit_Name'))
						->joinLeft(array('tk2' => 'r_testkitname_dts'), 'tk2.TestKitName_ID=rrdts.test_kit_name_2', array('testKitName2'=>'tk2.TestKit_Name'))
						->joinLeft(array('tk3' => 'r_testkitname_dts'), 'tk3.TestKitName_ID=rrdts.test_kit_name_3', array('testKitName3'=>'tk3.TestKit_Name'))
						->joinLeft(array('r' => 'r_possibleresult'), 'r.id=rrdts.test_result_1', array('testResult1'=>'r.response'))
						->joinLeft(array('rp' => 'r_possibleresult'), 'rp.id=rrdts.test_result_2', array('testResult2'=>'rp.response'))
						->joinLeft(array('rpr' => 'r_possibleresult'), 'rpr.id=rrdts.test_result_3', array('testResult3'=>'rpr.response'))
						->joinLeft(array('fr' => 'r_possibleresult'), 'fr.id=rrdts.reported_result', array('finalResult'=>'fr.response'))
						
						->where("rrdts.shipment_map_id = ?", $aRow['map_id']);
					$shipmentResult[$key]['response'] = $db->fetchAll($resQuery);
				}
			    
			    
				$sheet->getCellByColumnAndRow(0, $currentRow)->setValueExplicit(ucwords($aRow['unique_identifier']), PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow(1, $currentRow)->setValueExplicit($aRow['first_name'].$aRow['last_name'], PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow(2, $currentRow)->setValueExplicit($aRow['region'], PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow(3, $currentRow)->setValueExplicit($aRow['department_name'], PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow(4, $currentRow)->setValueExplicit($aRow['site_type'], PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow(5, $currentRow)->setValueExplicit($aRow['address'], PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow(6, $currentRow)->setValueExplicit($aRow['mobile'], PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow(7, $currentRow)->setValueExplicit($aRow['email'], PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow(8, $currentRow)->setValueExplicit($aRow['enrolled_on'], PHPExcel_Cell_DataType::TYPE_STRING);
				
				for($i=0;$i<=8;$i++){
					$cellName = $sheet->getCellByColumnAndRow($i,$currentRow)->getColumn();
					$sheet->getStyle($cellName . $currentRow)->applyFromArray($borderStyle);
				}
				
				$currentRow++;
				$shipmentCode=$aRow['shipment_code'];
			}
		}
		
		//------------- Participant List Details End ------>
		
		//<-------- Second sheet start
		$reportHeadings = array('Facility Code','Facility Name','Point of Contact','Region','Shipment Receipt Date','Sample Rehydration Date','Testing Date','Test#1 Name','Kit Lot #','Exp Date');
		
		if ($result['scheme_type'] == 'dts') {
			$reportHeadings=$this->addSampleNameInArray($shipmentId,$reportHeadings);
			array_push($reportHeadings,'Test#2 Name','Kit Lot #','Exp Date');
			$reportHeadings=$this->addSampleNameInArray($shipmentId,$reportHeadings);
			array_push($reportHeadings,'Test#3 Name','Kit Lot #','Exp Date');
			$reportHeadings=$this->addSampleNameInArray($shipmentId,$reportHeadings);
			$reportHeadings=$this->addSampleNameInArray($shipmentId,$reportHeadings);
			array_push($reportHeadings,'Comments');
		}
		
		$sheet = new PHPExcel_Worksheet($excel, 'Results Reported');
		$excel->addSheet($sheet, 2);
		$sheet->setTitle('Results Reported');
		$sheet->getDefaultColumnDimension()->setWidth(24);
		$sheet->getDefaultRowDimension()->setRowHeight(18);
		
		
		$colNo = 0;
		$currentRow = 2;
		$n=count($reportHeadings);
		$finalResColoumn=$n-($result['number_of_samples']+1);
		$c=1;
		$endMergeCell=($finalResColoumn+$result['number_of_samples'])-1;
		
		$firstCellName = $sheet->getCellByColumnAndRow($finalResColoumn,1)->getColumn();
		$secondCellName = $sheet->getCellByColumnAndRow($endMergeCell,1)->getColumn();
		$sheet->mergeCells($firstCellName."1:".$secondCellName."1");
		$sheet->getStyle($firstCellName."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('#00FF00');
		$sheet->getStyle($firstCellName."1")->applyFromArray($borderStyle);
		$sheet->getStyle($secondCellName."1")->applyFromArray($borderStyle);
		
		foreach ($reportHeadings as $field => $value) {
			
			$sheet->getCellByColumnAndRow($colNo, $currentRow)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->getStyleByColumnAndRow($colNo,$currentRow)->getFont()->setBold(true);
			$cellName = $sheet->getCellByColumnAndRow($colNo,$currentRow)->getColumn();
			$sheet->getStyle($cellName . $currentRow)->applyFromArray($borderStyle);
			
			$cellName = $sheet->getCellByColumnAndRow($colNo,3)->getColumn();
			$sheet->getStyle($cellName."3")->applyFromArray($borderStyle);
			
			if($colNo>=$finalResColoumn){
				if($c<=$result['number_of_samples']){
					
					$sheet->getCellByColumnAndRow($colNo,1)->setValueExplicit(html_entity_decode("Final Results", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
					$cellName = $sheet->getCellByColumnAndRow($colNo,$currentRow)->getColumn();
					$sheet->getStyle($cellName.$currentRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('#00FF00');
					$l=$c-1;
					$sheet->getCellByColumnAndRow($colNo,3)->setValueExplicit(html_entity_decode($refResult[$l]['referenceResult'], ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
					
					
				}
				$c++;
			}
			$sheet->getStyle($cellName.'3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFA0A0A0');
			$sheet->getStyle($cellName.'3')->getFont()->getColor()->setARGB('FFFFFF00');
					
			$colNo++;
		}
		
		$sheet->getStyle("A2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
		$sheet->getStyle("B2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
		$sheet->getStyle("C2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
		$sheet->getStyle("D2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
		
		//$sheet->getStyle("D2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('#A7A7A7');
		//$sheet->getStyle("E2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('#A7A7A7');
		//$sheet->getStyle("F2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('#A7A7A7');
		
		$cellName = $sheet->getCellByColumnAndRow($n,3)->getColumn();
		//$sheet->getStyle('A3:'.$cellName.'3')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('#969696');
		//$sheet->getStyle('A3:'.$cellName.'3')->applyFromArray($borderStyle);
		
		
		//<-------- Sheet three heading -------
		$sheetThree = new PHPExcel_Worksheet($excel, 'Panel Score');
		$excel->addSheet($sheetThree, 3);
		$sheetThree->setTitle('Panel Score');
		$sheetThree->getDefaultColumnDimension()->setWidth(20);
		$sheetThree->getDefaultRowDimension()->setRowHeight(18);
		$panelScoreHeadings = array('Facility Code','Facility Name');
		$panelScoreHeadings=$this->addSampleNameInArray($shipmentId,$panelScoreHeadings);
		array_push($panelScoreHeadings,'Test# Correct','% Correct');
		$sheetThreeColNo = 0;
		$sheetThreeRow = 1;
		$panelScoreHeadingCount=count($panelScoreHeadings);
		$sheetThreeColor=1+$result['number_of_samples'];
		foreach ($panelScoreHeadings as $sheetThreeHK => $value) {
			$sheetThree->getCellByColumnAndRow($sheetThreeColNo, $sheetThreeRow)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheetThree->getStyleByColumnAndRow($sheetThreeColNo,$sheetThreeRow)->getFont()->setBold(true);
			$cellName = $sheetThree->getCellByColumnAndRow($sheetThreeColNo,$sheetThreeRow)->getColumn();
			$sheetThree->getStyle($cellName . $sheetThreeRow)->applyFromArray($borderStyle);
			
			if($sheetThreeHK>1 && $sheetThreeHK<=$sheetThreeColor){
				$cellName = $sheetThree->getCellByColumnAndRow($sheetThreeColNo,$sheetThreeRow)->getColumn();
				$sheetThree->getStyle($cellName.$sheetThreeRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('#00FF00');
			}
			
			$sheetThreeColNo++;
		}
		//---------- Sheet Three heading ------->
		
		//<-------- Document Score Sheet Heading (Sheet Four)-------
		
		if ($result['scheme_type'] == 'dts') {
			$file = APPLICATION_PATH . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR . "config.ini";
			$config = new Zend_Config_Ini($file, APPLICATION_ENV);
			$documentationScorePerItem = ($config->evaluation->dts->documentationScore/4);
		}
		
		$docScoreSheet = new PHPExcel_Worksheet($excel, 'Documentation Score');
		$excel->addSheet($docScoreSheet, 4);
		$docScoreSheet->setTitle('Documentation Score');
		$docScoreSheet->getDefaultColumnDimension()->setWidth(20);
		//$docScoreSheet->getDefaultRowDimension()->setRowHeight(20);
		$docScoreSheet->getDefaultRowDimension('G')->setRowHeight(25);
		
		$docScoreHeadings = array('Facility Code','Facility Name','Supervisor signature','Rehydration Date','Tested Date','Rehydration Test In 24 Hrs','Documentation Score %');
		
		$docScoreSheetCol = 0;
		$docScoreRow = 1;
		$docScoreHeadingsCount=count($docScoreHeadings);
		foreach ($docScoreHeadings as $sheetThreeHK => $value) {
			$docScoreSheet->getCellByColumnAndRow($docScoreSheetCol, $docScoreRow)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$docScoreSheet->getStyleByColumnAndRow($docScoreSheetCol,$docScoreRow)->getFont()->setBold(true);
			$cellName = $docScoreSheet->getCellByColumnAndRow($docScoreSheetCol,$docScoreRow)->getColumn();
			$docScoreSheet->getStyle($cellName . $docScoreRow)->applyFromArray($borderStyle);
			$docScoreSheet->getStyleByColumnAndRow($docScoreSheetCol,$docScoreRow)->getAlignment()->setWrapText(true);
			$docScoreSheetCol++;
		}
		$docScoreRow=2;
		$secondRowcellName = $docScoreSheet->getCellByColumnAndRow(1,$docScoreRow);
		$secondRowcellName->setValueExplicit(html_entity_decode("Points Breakdown", ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		$docScoreSheet->getStyleByColumnAndRow(1,$docScoreRow)->getFont()->setBold(true);
		$cellName = $secondRowcellName->getColumn();
		$docScoreSheet->getStyle($cellName.$docScoreRow)->applyFromArray($borderStyle);
		
		for($r=2;$r<=6;$r++){
			
			$secondRowcellName = $docScoreSheet->getCellByColumnAndRow($r,$docScoreRow);
			if($r!=6){
				$secondRowcellName->setValueExplicit(html_entity_decode($documentationScorePerItem, ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			}
			$docScoreSheet->getStyleByColumnAndRow($r,$docScoreRow)->getFont()->setBold(true);
			$cellName = $secondRowcellName->getColumn();
			$docScoreSheet->getStyle($cellName.$docScoreRow)->applyFromArray($borderStyle);
		}
		
		//---------- Document Score Sheet Heading (Sheet Four)------->
		
		//<-------- Total Score Sheet Heading (Sheet Four)-------
		
		
		$totalScoreSheet = new PHPExcel_Worksheet($excel, 'Total Score');
		$excel->addSheet($totalScoreSheet,5);
		$totalScoreSheet->setTitle('Total Score');
		$totalScoreSheet->getDefaultColumnDimension()->setWidth(20);
		$totalScoreSheet->getDefaultRowDimension(1)->setRowHeight(30);
		$totalScoreHeadings = array('Facility Code','Facility Name','No.of Panels Correct(N='.$result['number_of_samples'].')','Panel Score(100% Conv.)','Panel Score(90% Conv.)','Documentation Score(100% Conv.)','Documentation Score(10% Conv.)','Total Score','Overall Performance','Comments','Comments2','Comments3','Corrective Action');
		
		$totScoreSheetCol = 0;
		$totScoreRow = 1;
		$totScoreHeadingsCount=count($totalScoreHeadings);
		foreach ($totalScoreHeadings as $sheetThreeHK => $value) {
			$totalScoreSheet->getCellByColumnAndRow($totScoreSheetCol, $totScoreRow)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
			$totalScoreSheet->getStyleByColumnAndRow($totScoreSheetCol,$totScoreRow)->getFont()->setBold(true);
			$cellName = $totalScoreSheet->getCellByColumnAndRow($totScoreSheetCol,$totScoreRow)->getColumn();
			$totalScoreSheet->getStyle($cellName . $totScoreRow)->applyFromArray($borderStyle);
			$totalScoreSheet->getStyleByColumnAndRow($totScoreSheetCol,$totScoreRow)->getAlignment()->setWrapText(true);
			$totScoreSheetCol++;
		}
		
		//---------- Document Score Sheet Heading (Sheet Four)------->
		
		$ktr=9;
		$kitId=7; //Test Kit coloumn count 
		if(isset($refResult) && count($refResult) > 0){
			foreach ($refResult as $keyv=>$row) {
				$keyv=$keyv+1;
				$ktr=$ktr+$keyv;
				if(count($row['kitReference'])>0){
					
					if($keyv==1){
						//In Excel Third row added the Test kit name1,kit lot,exp date
						if(trim($row['kitReference'][0]['expiry_date'])!=""){
							$row['kitReference'][0]['expiry_date']=Pt_Commons_General::excelDateFormat($row['kitReference'][0]['expiry_date']);
						}
						$sheet->getCellByColumnAndRow($kitId++,3)->setValueExplicit($row['kitReference'][0]['testKitName'], PHPExcel_Cell_DataType::TYPE_STRING);
						$sheet->getCellByColumnAndRow($kitId++,3)->setValueExplicit($row['kitReference'][0]['lot_no'], PHPExcel_Cell_DataType::TYPE_STRING);
						$sheet->getCellByColumnAndRow($kitId++,3)->setValueExplicit($row['kitReference'][0]['expiry_date'], PHPExcel_Cell_DataType::TYPE_STRING);
						
						$kitId=$kitId+$aRow['number_of_samples'];
						if(isset($row['kitReference'][1]['referenceKitResult'])){
							//In Excel Third row added the Test kit name2,kit lot,exp date
							if(trim($row['kitReference'][1]['expiry_date'])!=""){
								$row['kitReference'][1]['expiry_date']=Pt_Commons_General::excelDateFormat($row['kitReference'][1]['expiry_date']);
							}
							$sheet->getCellByColumnAndRow($kitId++,3)->setValueExplicit($row['kitReference'][1]['testKitName'], PHPExcel_Cell_DataType::TYPE_STRING);
							$sheet->getCellByColumnAndRow($kitId++,3)->setValueExplicit($row['kitReference'][1]['lot_no'], PHPExcel_Cell_DataType::TYPE_STRING);
							$sheet->getCellByColumnAndRow($kitId++,3)->setValueExplicit($row['kitReference'][1]['expiry_date'], PHPExcel_Cell_DataType::TYPE_STRING);
						}
						$kitId=$kitId+$aRow['number_of_samples'];
						if(isset($row['kitReference'][2]['referenceKitResult'])){
							//In Excel Third row added the Test kit name3,kit lot,exp date
							if(trim($row['kitReference'][2]['expiry_date'])!=""){
								$row['kitReference'][2]['expiry_date']=Pt_Commons_General::excelDateFormat($row['kitReference'][2]['expiry_date']);
							}
							$sheet->getCellByColumnAndRow($kitId++,3)->setValueExplicit($row['kitReference'][2]['testKitName'], PHPExcel_Cell_DataType::TYPE_STRING);
							$sheet->getCellByColumnAndRow($kitId++,3)->setValueExplicit($row['kitReference'][2]['lot_no'], PHPExcel_Cell_DataType::TYPE_STRING);
							$sheet->getCellByColumnAndRow($kitId++,3)->setValueExplicit($row['kitReference'][2]['expiry_date'], PHPExcel_Cell_DataType::TYPE_STRING);
						}
					}
					
					$sheet->getCellByColumnAndRow($ktr,3)->setValueExplicit($row['kitReference'][0]['referenceKitResult'], PHPExcel_Cell_DataType::TYPE_STRING);
					$ktr=($aRow['number_of_samples']-$keyv)+$ktr+3;
					
					if(isset($row['kitReference'][1]['referenceKitResult'])){
						$ktr=$ktr+$keyv;
						$sheet->getCellByColumnAndRow($ktr,3)->setValueExplicit($row['kitReference'][1]['referenceKitResult'], PHPExcel_Cell_DataType::TYPE_STRING);
						$ktr=($aRow['number_of_samples']-$keyv)+$ktr+3;
					
					}
					if(isset($row['kitReference'][2]['referenceKitResult'])){
					 	$ktr=$ktr+$keyv;
						$sheet->getCellByColumnAndRow($ktr,3)->setValueExplicit($row['kitReference'][2]['referenceKitResult'], PHPExcel_Cell_DataType::TYPE_STRING);
						
					}
					
				}
				$ktr=9;
			}
		}
		
		$currentRow = 4;
		$sheetThreeRow = 2;
		$docScoreRow = 3;
		$totScoreRow = 2;
		if(isset($shipmentResult) && count($shipmentResult) > 0){
			
			foreach ($shipmentResult as $aRow) {
				$r=0;
				$k=0;
				$rehydrationDate="";
				$shipmentTestDate="";
				$sheetThreeCol=0;
				$docScoreCol=0;
				$totScoreCol=0;
				$countCorrectResult=0;
				
				$colCellObj = $sheet->getCellByColumnAndRow($r++,$currentRow);
				$colCellObj->setValueExplicit(ucwords($aRow['unique_identifier']), PHPExcel_Cell_DataType::TYPE_STRING);
				$cellName = $colCellObj->getColumn();
				//$sheet->getStyle($cellName.$currentRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
				
					
				//$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit(ucwords($aRow['unique_identifier']), PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['first_name'].$aRow['last_name'], PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['dataManagerFirstName'].$aRow['dataManagerLastName'], PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['region'], PHPExcel_Cell_DataType::TYPE_STRING);
				
				if(isset($aRow['shipment_receipt_date']) && trim($aRow['shipment_receipt_date'])!=""){
					$aRow['shipment_receipt_date']=Pt_Commons_General::excelDateFormat($aRow['shipment_receipt_date']);
				}
				
				if(isset($aRow['shipment_test_date']) && trim($aRow['shipment_test_date'])!=""){
					$shipmentTestDate=Pt_Commons_General::excelDateFormat($aRow['shipment_test_date']);
				}
				
				if(trim($aRow['attributes'])!=""){
					$attributes=json_decode($aRow['attributes'],true);
					$sampleRehydrationDate = new Zend_Date($attributes['sample_rehydration_date'], Zend_Date::ISO_8601);
					$rehydrationDate=Pt_Commons_General::excelDateFormat($attributes["sample_rehydration_date"]);
				}
				
				$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['shipment_receipt_date'], PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($rehydrationDate, PHPExcel_Cell_DataType::TYPE_STRING);
				$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($shipmentTestDate, PHPExcel_Cell_DataType::TYPE_STRING);
				
				
				
				$sheetThree->getCellByColumnAndRow($sheetThreeCol++, $sheetThreeRow)->setValueExplicit(ucwords($aRow['unique_identifier']), PHPExcel_Cell_DataType::TYPE_STRING);
				$sheetThree->getCellByColumnAndRow($sheetThreeCol++, $sheetThreeRow)->setValueExplicit($aRow['first_name'].$aRow['last_name'], PHPExcel_Cell_DataType::TYPE_STRING);
				
				//<-------------Document score sheet------------
				
				$docScoreSheet->getCellByColumnAndRow($docScoreCol++, $docScoreRow)->setValueExplicit(ucwords($aRow['unique_identifier']), PHPExcel_Cell_DataType::TYPE_STRING);
				$docScoreSheet->getCellByColumnAndRow($docScoreCol++, $docScoreRow)->setValueExplicit($aRow['first_name'].$aRow['last_name'], PHPExcel_Cell_DataType::TYPE_STRING);
				
				if(strtolower($aRow['supervisor_approval']) == 'yes' && trim($aRow['participant_supervisor']) != ""){
					$docScoreSheet->getCellByColumnAndRow($docScoreCol++, $docScoreRow)->setValueExplicit($documentationScorePerItem, PHPExcel_Cell_DataType::TYPE_STRING);
				}else{
					$docScoreSheet->getCellByColumnAndRow($docScoreCol++, $docScoreRow)->setValueExplicit(0, PHPExcel_Cell_DataType::TYPE_STRING);
				}
				
				if(isset($rehydrationDate) && trim($rehydrationDate) != ""){
					$docScoreSheet->getCellByColumnAndRow($docScoreCol++, $docScoreRow)->setValueExplicit($documentationScorePerItem, PHPExcel_Cell_DataType::TYPE_STRING);
				}else{
					$docScoreSheet->getCellByColumnAndRow($docScoreCol++, $docScoreRow)->setValueExplicit(0, PHPExcel_Cell_DataType::TYPE_STRING);
				}
				
				if(isset($aRow['shipment_test_date']) && trim($aRow['shipment_test_date'])!=""){
					$docScoreSheet->getCellByColumnAndRow($docScoreCol++, $docScoreRow)->setValueExplicit($documentationScorePerItem, PHPExcel_Cell_DataType::TYPE_STRING);
				}else{
					$docScoreSheet->getCellByColumnAndRow($docScoreCol++, $docScoreRow)->setValueExplicit(0, PHPExcel_Cell_DataType::TYPE_STRING);
				}
				
				if(isset($sampleRehydrationDate) && trim($aRow['shipment_test_date'])!=""){
					$testedOn = new Zend_Date($aRow['shipment_test_date'], Zend_Date::ISO_8601);
					// Testing should be done within 24 hours of rehydration.
					$diff = $testedOn->sub($sampleRehydrationDate)->toValue();
					$days = ceil($diff / 60 / 60 / 24) + 1;
					if($days > 1) {
						$docScoreSheet->getCellByColumnAndRow($docScoreCol++, $docScoreRow)->setValueExplicit(0, PHPExcel_Cell_DataType::TYPE_STRING);
					}else{
						$docScoreSheet->getCellByColumnAndRow($docScoreCol++, $docScoreRow)->setValueExplicit($documentationScorePerItem, PHPExcel_Cell_DataType::TYPE_STRING);
					}
				}else{
					$docScoreSheet->getCellByColumnAndRow($docScoreCol++, $docScoreRow)->setValueExplicit(0, PHPExcel_Cell_DataType::TYPE_STRING);
				}
				
				$documentScore=(($aRow['documentation_score']/$config->evaluation->dts->documentationScore)*100);
				$docScoreSheet->getCellByColumnAndRow($docScoreCol++, $docScoreRow)->setValueExplicit($documentScore, PHPExcel_Cell_DataType::TYPE_STRING);
				
				//-------------Document score sheet------------>
				
				//<------------ Total score sheet ------------
				
				$totalScoreSheet->getCellByColumnAndRow($totScoreCol++, $totScoreRow)->setValueExplicit(ucwords($aRow['unique_identifier']), PHPExcel_Cell_DataType::TYPE_STRING);
				$totalScoreSheet->getCellByColumnAndRow($totScoreCol++, $totScoreRow)->setValueExplicit($aRow['first_name'].$aRow['last_name'], PHPExcel_Cell_DataType::TYPE_STRING);
				
				//------------ Total score sheet ------------>
				
				//Zend_Debug::dump($aRow['response']);
				if(count($aRow['response'])>0){
					
					if(isset($aRow['response'][0]['exp_date_1']) && trim($aRow['response'][0]['exp_date_1'])!=""){
						$aRow['response'][0]['exp_date_1']=Pt_Commons_General::excelDateFormat($aRow['response'][0]['exp_date_1']);
					}
					if(isset($aRow['response'][0]['exp_date_2']) && trim($aRow['response'][0]['exp_date_2'])!=""){
						$aRow['response'][0]['exp_date_2']=Pt_Commons_General::excelDateFormat($aRow['response'][0]['exp_date_2']);
					}
					if(isset($aRow['response'][0]['exp_date_3']) && trim($aRow['response'][0]['exp_date_3'])!=""){
						$aRow['response'][0]['exp_date_3']=Pt_Commons_General::excelDateFormat($aRow['response'][0]['exp_date_3']);
					}
					
					$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][0]['testKitName1'], PHPExcel_Cell_DataType::TYPE_STRING);
					$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][0]['lot_no_1'], PHPExcel_Cell_DataType::TYPE_STRING);
					$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][0]['exp_date_1'], PHPExcel_Cell_DataType::TYPE_STRING);
					
					for($k=0;$k<$aRow['number_of_samples'];$k++){
						//$row[] = $aRow[$k]['testResult1'];
						$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][$k]['testResult1'], PHPExcel_Cell_DataType::TYPE_STRING);
					}
					$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][0]['testKitName2'], PHPExcel_Cell_DataType::TYPE_STRING);
					$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][0]['lot_no_2'], PHPExcel_Cell_DataType::TYPE_STRING);
					$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][0]['exp_date_2'], PHPExcel_Cell_DataType::TYPE_STRING);
					
					for($k=0;$k<$aRow['number_of_samples'];$k++){
						//$row[] = $aRow[$k]['testResult2'];
						$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][$k]['testResult2'], PHPExcel_Cell_DataType::TYPE_STRING);
					}
					$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][0]['testKitName3'], PHPExcel_Cell_DataType::TYPE_STRING);
					$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][0]['lot_no_3'], PHPExcel_Cell_DataType::TYPE_STRING);
					$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][0]['exp_date_3'], PHPExcel_Cell_DataType::TYPE_STRING);
					
					for($k=0;$k<$aRow['number_of_samples'];$k++){
						//$row[] = $aRow[$k]['testResult3'];
						$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][$k]['testResult3'], PHPExcel_Cell_DataType::TYPE_STRING);
					}
					
					for($k=0;$k<$aRow['number_of_samples'];$k++){
						//$row[] = $aRow[$k]['finalResult'];
						$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['response'][$k]['finalResult'], PHPExcel_Cell_DataType::TYPE_STRING);
						
						$sheetThree->getCellByColumnAndRow($sheetThreeCol++, $sheetThreeRow)->setValueExplicit($aRow['response'][$k]['finalResult'], PHPExcel_Cell_DataType::TYPE_STRING);
						if(isset($aRow['response'][$k]['finalResult']) && $aRow['response'][$k]['finalResult']==$refResult[$k]['referenceResult'] && $aRow['response'][$k]['sample_id']==$refResult[$k]['sample_id']){
							$countCorrectResult++;
						}
						
					}
					$sheet->getCellByColumnAndRow($r++, $currentRow)->setValueExplicit($aRow['user_comment'], PHPExcel_Cell_DataType::TYPE_STRING);
					
					$sheetThree->getCellByColumnAndRow($sheetThreeCol++, $sheetThreeRow)->setValueExplicit($countCorrectResult, PHPExcel_Cell_DataType::TYPE_STRING);
					
					$totPer=round((($countCorrectResult/$aRow['number_of_samples'])*100),2);
					$sheetThree->getCellByColumnAndRow($sheetThreeCol++, $sheetThreeRow)->setValueExplicit($totPer, PHPExcel_Cell_DataType::TYPE_STRING);
					
					$totalScoreSheet->getCellByColumnAndRow($totScoreCol++, $totScoreRow)->setValueExplicit($countCorrectResult, PHPExcel_Cell_DataType::TYPE_STRING);
					$totalScoreSheet->getCellByColumnAndRow($totScoreCol++, $totScoreRow)->setValueExplicit($totPer, PHPExcel_Cell_DataType::TYPE_STRING);
					
					$totalScoreSheet->getCellByColumnAndRow($totScoreCol++, $totScoreRow)->setValueExplicit(($totPer*0.9), PHPExcel_Cell_DataType::TYPE_STRING);
					
				}
					$totalScoreSheet->getCellByColumnAndRow($totScoreCol++, $totScoreRow)->setValueExplicit($documentScore, PHPExcel_Cell_DataType::TYPE_STRING);
					$totalScoreSheet->getCellByColumnAndRow($totScoreCol++, $totScoreRow)->setValueExplicit($aRow['documentation_score'], PHPExcel_Cell_DataType::TYPE_STRING);
					$totalScoreSheet->getCellByColumnAndRow($totScoreCol++, $totScoreRow)->setValueExplicit(($aRow['shipment_score']+$aRow['documentation_score']), PHPExcel_Cell_DataType::TYPE_STRING);
				
				for($i=0;$i<$panelScoreHeadingCount;$i++){
					$cellName = $sheetThree->getCellByColumnAndRow($i,$sheetThreeRow)->getColumn();
					$sheetThree->getStyle($cellName . $sheetThreeRow)->applyFromArray($borderStyle);
				}
				
				for($i=0;$i<$n;$i++){
					$cellName = $sheet->getCellByColumnAndRow($i,$currentRow)->getColumn();
					$sheet->getStyle($cellName . $currentRow)->applyFromArray($borderStyle);
				}
				
				for($i=0;$i<$docScoreHeadingsCount;$i++){
					$cellName = $docScoreSheet->getCellByColumnAndRow($i,$docScoreRow)->getColumn();
					$docScoreSheet->getStyle($cellName.$docScoreRow)->applyFromArray($borderStyle);
				}
				
				for($i=0;$i<$totScoreHeadingsCount;$i++){
					$cellName = $totalScoreSheet->getCellByColumnAndRow($i,$totScoreRow)->getColumn();
					$totalScoreSheet->getStyle($cellName.$totScoreRow)->applyFromArray($borderStyle);
				}
				
				$currentRow++;
				
				$sheetThreeRow++;
				$docScoreRow++;
				$totScoreRow++;
				
				
			}
		}
		
		//----------- Second Sheet End----->
		
		
		
		
		
		$excel->setActiveSheetIndex(0);
		
		$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
		$filename = $shipmentCode.'-' . date('d-M-Y-H-i-s') . '.xls';
		$writer->save(TEMP_UPLOAD_PATH. DIRECTORY_SEPARATOR . $filename);
		return $filename;
		
	}
	
	
	public function addSampleNameInArray($shipmentId,$headings){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query=$db->select()->from('reference_result_dts',array('sample_label'))
				->where("shipment_id = ?",$shipmentId)->order("sample_id");
		$result = $db->fetchAll($query);
		foreach($result as $res){
			array_push($headings,$res['sample_label']);
		}
		return $headings;
	}
}

