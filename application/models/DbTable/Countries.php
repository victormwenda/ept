<?php

class Application_Model_DbTable_Countries extends Zend_Db_Table_Abstract {
    protected $_name = 'countries';

    public function getAllCountries() {
		$sql = $this->select();
        $authNameSpace = new Zend_Session_Namespace('administrators');
        if ($authNameSpace->is_ptcc_coordinator) {
            $sql = $sql->where("id IN (".implode(",",$authNameSpace->countries).")");
        }
		return $this->fetchAll($sql);
	}
    public function getCountryId($isoname) {
        return $this->fetchAll($this->select()->where('iso_name = ? ',$isoname))[0]->id;
    }
    public function updateCountry($countryId, $countryData) {
        return $this->update($countryData,"id=".$countryId);
    }
}

