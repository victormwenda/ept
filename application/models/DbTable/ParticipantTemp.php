<?php

class Application_Model_DbTable_ParticipantTemp extends Zend_Db_Table_Abstract {
    protected $_name = 'participant_temp';
    protected $_primary = 'unique_identifier';

    public function getParticipantTempRecords() {
        return $this->getAdapter()->fetchAll(
            $this->getAdapter()
                ->select()
                ->from(array('pt' => $this->_name))
                ->joinLeft(array('p' => 'participant'), 'pt.participant_id = p.participant_id', array())
                ->joinLeft(array('c' => 'countries'), 'pt.country_id = c.id', array('c.iso_name as country_name'))
                ->joinLeft(array('dm' => 'data_manager'), 'pt.dm_id = dm.dm_id', array()));
    }

    public function clearParticipantTempRecords() {
        $db = Zend_Db_Table_Abstract::getAdapter();
        $db->delete('participant_temp');
    }

    public function addParticipantTempRecords($records) {
        $db = Zend_Db_Table_Abstract::getAdapter();
        foreach ($records as $record) {
            $db->insert('participant_temp', array(
                'unique_identifier' => $record["PT ID"],
                'lab_name' =>  $record["Lab Name"],
                'country' =>  $record["Country"],
                'region' =>  $record["Region"],
                'username' =>  $record["username"],
                'password' =>  $record["password"],
                'status' =>  $record["status"],
                'phone_number' =>  $record["Phone Number"],
                'participant_id' =>  $record["participant_id"],
                'dm_id' =>  $record["dm_id"],
                'country_id' =>  $record["country_id"]
            ));
        }
    }
}

