<?php

class Application_Model_DbTable_CountrieShipmentMap extends Zend_Db_Table_Abstract {
    protected $_name = 'country_shipment_map';

    public function isShipmentExist($country ,$shipment){
         $sql = $this->select()->where('country_id = ?',$country)->where('shipment_id = ?',$shipment);
         print_r($this->fetchAll($sql));
        return count($this->fetchAll($sql)) > 0;
    }
    public function updateCountryShipmentMap($country ,$shipment,$date){
         return $this->update(array('due_date_text'=>$date),array('country_id = ?'=>$country,'shipment_id = ?'=>$shipment));
    }
    public function insertCountryShipmentMap($country ,$shipment,$date){
         $data = array(
                'country_id'=>$country,
                'shipment_id'=>$shipment,
                'due_date_text'=>$date
            );
        return $this->insert($data);
    }
}

