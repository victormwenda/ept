<?php

class Application_Service_SystemAdmin {
	public function getAllAdmin($params){
		$adminDb = new Application_Model_DbTable_SystemAdmin();
		return $adminDb->getAllAdmin($params);
	}
	public function addSystemAdmin($params){
		$adminDb = new Application_Model_DbTable_SystemAdmin();
		return $adminDb->addSystemAdmin($params);		
	}
	public function updateSystemAdmin($params){
		$adminDb = new Application_Model_DbTable_SystemAdmin();
		return $adminDb->updateSystemAdmin($params);		
	}
	public function getSystemAdminDetails($adminId){
		$adminDb = new Application_Model_DbTable_SystemAdmin();
		return $adminDb->getSystemAdminDetails($adminId);		
	}
    public function saveTempPtccs($tempPtccs) {
        $usernamesInImport = array_column($tempPtccs, "Email Address");
        $systemAdminsDb = new Application_Model_DbTable_SystemAdmin();
        $existingSystemAdmins = $systemAdminsDb->getPtccsByEmailAddresses($usernamesInImport);
        $existingSystemAdminsMap = array();
        $existingSystemAdminsMap = array_reduce($existingSystemAdmins->toArray(), function($accumulator, $existingSystemAdmin) {
            if (!isset($accumulator[$existingSystemAdmins["country_id"]])) {
                $accumulator[$existingSystemAdmin["country_id"]] = array();
            }
            $accumulator[$existingSystemAdmin["country_id"]][$existingSystemAdmin["primary_email"]] = $existingSystemAdmin;
            return $accumulator;
        }, $existingSystemAdminsMap);

        $countriesDb = new Application_Model_DbTable_Countries();
        $countries = $countriesDb->getAllCountries();
        $countriesMap = array();
        $countriesMap = array_reduce($countries, function($accumulator, $country) {
            $accumulator[$country["iso_name"]] = $country;
            return $accumulator;
        }, $countriesMap);
        foreach ($tempPtccs as $tempPtcc) {
            $existingSystemAdmin = array(
                "admin_id" => null,
                "country_id" => null,
                "status" => null
            );
            if (isset($countriesMap[$tempPtcc["Country"]])) {
                $tempPtcc["country_id"] = $countriesMap[$tempPtcc["Country"]]["id"];
            }
            if (isset($existingSystemAdminsMap[$tempPtcc["country_id"]]) && isset($existingSystemAdminsMap[$tempPtcc["country_id"]][$tempPtcc["Email Address"]])) {
                $existingSystemAdmin = $existingSystemAdminsMap[$tempPtcc["country_id"]][$tempPtcc["Email Address"]];
            }
            $tempPtcc["admin_id"] = $existingSystemAdmin["admin_id"];
            $tempPtcc["country_id"] = $existingSystemAdmin["country_id"];
            $tempPtcc["status"] = "active";
            if (isset($countriesMap[$tempPtcc["Country"]]) && !$tempPtcc["country_id"]) {
                $tempPtcc["country_id"] = $countriesMap[$tempPtcc["Country"]]["id"];
            }
            if ($tempPtcc["Active"] === "No") {
                $tempPtcc["status"] = "inactive";
            }
        }
        $ptccTempDb = new Application_Model_DbTable_PtccTemp();
        $ptccTempDb->clearPtccTempRecords();
        $ptccTempDb->addPtccTempRecords($tempPtccs);

        return $ptccTempDb->getPtccTempRecords();
    }
}

