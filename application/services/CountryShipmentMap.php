<?php

class Application_Service_CountryShipmentMap {


    public function updateOrInsertCountryShipmetDueDate($date,$country,$shipment) {
    	$CountryShipmentMapDB = new Application_Model_DbTable_CountrieShipmentMap();
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        try {
            $db->beginTransaction(); 
            $CountryShipmentMapDB->isShipmentExist($country ,$shipment)?$CountryShipmentMapDB->updateCountryShipmentMap($country ,$shipment,$date) : $CountryShipmentMapDB->insertCountryShipmentMap($country ,$shipment,$date);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            error_log($e, 0);
        }
    }


}

