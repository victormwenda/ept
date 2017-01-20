<?php

class Application_Service_PtccProfile {
	public function getAllPtccProfiles($params) {
		$ptccProfileDb = new Application_Model_DbTable_PtccProfile();
		return $ptccProfileDb->getAllPtccProfiles($params);
	}

	public function savePtccProfile($params) {
        $ptccProfileDb = new Application_Model_DbTable_PtccProfile();
		return $ptccProfileDb->upsertPtccProfile($params);
	}

	public function getSystemPtccProfileDetails($ptccProfileId = null) {
        $ptccProfileDb = new Application_Model_DbTable_PtccProfile();
		return $ptccProfileDb->getPtccProfileDetails($ptccProfileId);
	}
}

