<?php

class Application_Model_DbTable_PtccTemp extends Zend_Db_Table_Abstract {
    protected $_name = 'ptcc_temp';
    protected $_primary = 'primary_email';

    public function getPtccTempRecords() {
        return $this->getAdapter()->fetchAll(
            $this->getAdapter()
                ->select()
                ->from(array('pt' => $this->_name))
                ->joinLeft(array('sa' => 'system_admin'), 'pt.admin_id = sa.admin_id', array())
                ->joinLeft(array('c' => 'countries'), 'pt.country_id = c.id', array('c.iso_name as country_name')));
    }

    public function clearPtccTempRecords() {
        $db = Zend_Db_Table_Abstract::getAdapter();
        $db->delete('ptcc_temp');
    }

    public function addPtccTempRecords($records) {
        $db = Zend_Db_Table_Abstract::getAdapter();
        foreach ($records as $record) {
            $db->insert('ptcc_temp', array(
                'country' => $record["Country"],
                'first_name' =>  $record["First Name"],
                'last_name' =>  $record["Last Name"],
                'phone_number' =>  $record["Phone Number"],
                'primary_email' =>  $record["Email Address"],
                'password' =>  $record["Password"],
                'status' =>  $record["status"],
                'admin_id' =>  $record["admin_id"],
                'country_id' =>  $record["country_id"]
            ));
        }
    }
}

