<?php

class Application_Service_CountryShipmentMap {
    public function updateOrInsertCountryShipmetDueDate($countries,$shipment,$dates) {
        (new Application_Model_DbTable_CountrieShipmentMap())->insertOrUpdate($countries ,$shipment,$dates);   
    }
}

