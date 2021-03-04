<?php

class Application_Service_CountryShipmentMap {
    public function updateOrInsertCountryShipmetDueDate($countries,$shipment,$dates) {
        (new Application_Model_DbTable_CountryShipmentMap())->insertOrUpdate($countries ,$shipment,$dates);   
    }
}

