<?php

class Application_Model_DbTable_CountrieShipmentMap extends Zend_Db_Table_Abstract {
    protected $_name = 'country_shipment_map';
    public function insertOrUpdate($countries,$shipment,$dates){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $countryShipmentMapValues =  $this->formatData($this->mappingCountryShipmentDate($countries,$shipment,$dates), $db);
        $db->beginTransaction(); 
        try {
            $sql='INSERT INTO `country_shipment_map` (`country_id`,`shipment_id`,`due_date_text`) 
                VALUES  '.implode (', ', $countryShipmentMapValues). ' ON DUPLICATE KEY UPDATE `due_date_text` = VALUES(due_date_text)';
            $db->query($sql);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            error_log($e, 0);
        }
   }

   function mappingCountryShipmentDate($countries,$shipment,$dates){
    $data=array();
        foreach($dates as $key => $date){
            $data[]=[$countries[$key]['id'],$shipment,$date];
        }
    return $data;
   }

   function formatData($data ,$db){
    $countryShipmentMapValues = array();
    foreach ($data as $rowValues) {
        foreach ($rowValues as $key => $rowValue) {
             $rowValues[$key] = $db->quote($rowValues[$key]);
        }
        $countryShipmentMapValues[] = "(" . implode(', ', $rowValues) . ")";
    }
    return $countryShipmentMapValues;
   }
}

