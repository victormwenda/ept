<?php

class Application_Service_PtccProfile {
	public function getAllPtccProfiles($params) {
		$ptccProfileDb = new Application_Model_DbTable_SystemAdmin();
		return $ptccProfileDb->getAllPtccProfiles($params);
	}

	public function savePtccProfile($params) {
        $ptccProfileDb = new Application_Model_DbTable_SystemAdmin();
		return $ptccProfileDb->upsertPtccProfile($params);
	}

	public function getSystemPtccProfileDetails($adminId = null) {
        $ptccProfileDb = new Application_Model_DbTable_SystemAdmin();
		return $ptccProfileDb->getPtccProfileDetails($adminId);
	}
    public function saveTempPtccs($tempPtccs) {
        $usernamesInImport = array_column($tempPtccs, "Email Address");
        $systemAdminsDb = new Application_Model_DbTable_SystemAdmin();
        $existingSystemAdmins = $systemAdminsDb->getPtccsByEmailAddresses($usernamesInImport);
        $existingPtccsMap = array();
        $existingPtccsMap = array_reduce($existingSystemAdmins, function($ptccAccumulator, $existingPtcc) {
            if (!isset($ptccAccumulator[$existingPtcc["country_id"]])) {
                $ptccAccumulator[$existingPtcc["country_id"]] = array();
            }
            $ptccAccumulator[$existingPtcc["country_id"]][$existingPtcc["primary_email"]] = $existingPtcc;
            return $ptccAccumulator;
        }, $existingPtccsMap);
        $existingSystemAdminsMap = array();
        $existingSystemAdminsMap = array_reduce($existingSystemAdmins, function($saAccumulator, $existingSystemAdmin) {
            $saAccumulator[$existingSystemAdmin["primary_email"]] = $existingSystemAdmin;
            return $saAccumulator;
        }, $existingSystemAdminsMap);
        $countriesDb = new Application_Model_DbTable_Countries();
        $countries = $countriesDb->getAllCountries();
        $countriesMap = array();
        $countriesMap = array_reduce($countries, function($accumulator, $country) {
            $accumulator[$country["iso_name"]] = $country;
            return $accumulator;
        }, $countriesMap);
        for ($i = 0; $i < count($tempPtccs); $i++) {
            $existingSystemAdmin = array(
                "admin_id" => null,
                "country_id" => null,
                "status" => null
            );
            if (isset($countriesMap[$tempPtccs[$i]["Country"]])) {
                $tempPtccs[$i]["country_id"] = $countriesMap[$tempPtccs[$i]["Country"]]["id"];
            } else {
                throw new Exception("The sheet contains a country name of \"".$tempPtccs[$i]["Country"]."\" which is not recognised by ePT. Please edit the sheet using the following country names only: ".implode(", ", array_keys($countriesMap))."?");
            }
            if (isset($existingPtccsMap[$tempPtccs[$i]["country_id"]]) && isset($existingPtccsMap[$tempPtccs[$i]["country_id"]][$tempPtccs[$i]["Email Address"]])) {
                $existingSystemAdmin = $existingPtccsMap[$tempPtccs[$i]["country_id"]][$tempPtccs[$i]["Email Address"]];
            }
            $tempPtccs[$i]["admin_id"] = $existingSystemAdmin["admin_id"];
            $tempPtccs[$i]["country_id"] = $existingSystemAdmin["country_id"];
            $tempPtccs[$i]["status"] = "active";
            if ($tempPtccs[$i]["admin_id"] === null && isset($existingSystemAdminsMap[$tempPtccs[$i]["Email Address"]])) {
                $tempPtccs[$i]["admin_id"] = $existingSystemAdminsMap[$tempPtccs[$i]["Email Address"]]["admin_id"];
            }
            if (isset($countriesMap[$tempPtccs[$i]["Country"]]) && !$tempPtccs[$i]["country_id"]) {
                $tempPtccs[$i]["country_id"] = $countriesMap[$tempPtccs[$i]["Country"]]["id"];
            }
            if ($tempPtccs[$i]["Active"] === "No") {
                $tempPtccs[$i]["status"] = "inactive";
            }
            if (!$tempPtccs[$i]["country_id"]) {
                throw new Exception("The sheet contains a record where the country cannot be determined. Please check ".$tempPtccs[$i]["Email Address"]." in ".$tempPtccs[$i]["Country"]." to make sure that the country is correctly specified?");
            }
            if (!$tempPtccs[$i]["First Name"]) {
                throw new Exception("The sheet contains a record with a blank First Name which is a required field. Please check ".$tempPtccs[$i]["Email Address"]." in ".$tempPtccs[$i]["Country"]."?");
            }
            if (!$tempPtccs[$i]["Last Name"]) {
                throw new Exception("The sheet contains a record with a blank Last Name which is a required field. Please check ".$tempPtccs[$i]["Email Address"]." in ".$tempPtccs[$i]["Country"]."?");
            }
            if (!$tempPtccs[$i]["Email Address"]) {
                throw new Exception("The sheet contains a record with a blank First Name which is a required field. Please check ".$tempPtccs[$i]["First Name"]." ".$tempPtccs[$i]["Last Name"]." in ".$tempPtccs[$i]["Country"]."?");
            }
            if (!$tempPtccs[$i]["admin_id"] && !$tempPtccs[$i]["Password"]) {
                throw new Exception("The sheet contains a record with a blank Password which is a required field when the user does not yet exist in ePT. Please check ".$tempPtccs[$i]["Email Address"]." in ".$tempPtccs[$i]["Country"]."?");
            }
        }
        $ptccTempDb = new Application_Model_DbTable_PtccTemp();
        $ptccTempDb->clearPtccTempRecords();
        $ptccTempDb->addPtccTempRecords($tempPtccs);

        return $ptccTempDb->getPtccTempRecords();
    }
}

