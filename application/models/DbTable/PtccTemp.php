<?php

class Application_Model_DbTable_PtccTemp extends Zend_Db_Table_Abstract {
    protected $_name = 'ptcc_temp';
    protected $_primary = 'primary_email';

    public function getPtccTempRecords() {
        $ptccTempRecords = $this->getAdapter()->fetchAll(
            $this->getAdapter()
                ->select()
                ->from(array('pt' => $this->_name), array(
                    "pt.country",
                    "pt.first_name",
                    "pt.last_name",
                    "pt.phone_number",
                    "pt.primary_email",
                    "pt.password",
                    "pt.status",
                    "pt.admin_id",
                    "pt.country_id"
                ))
                ->joinLeft(array('sa' => 'system_admin'), 'pt.admin_id = sa.admin_id', array(
                    "old_first_name" => "sa.first_name",
                    "old_last_name" => "sa.last_name",
                    "old_phone_number" => "sa.phone",
                    "old_primary_email" => "sa.primary_email",
                    "old_password" => "sa.password",
                    "old_status" => "sa.status",
                    "old_force_password_reset" => "sa.force_password_reset",
                ))
                ->joinLeft(array('c' => 'countries'), 'pt.country_id = c.id', array('c.iso_name as old_country'))
                ->order(array("pt.country ASC", "pt.first_name ASC", "pt.last_name ASC")));
        for ($i = 0; $i < count($ptccTempRecords); $i++) {
            $ptccTempRecords[$i]["import_action"] = "None";
            $ptccTempRecords[$i]["insert"] = !$ptccTempRecords[$i]["admin_id"];
            $ptccTempRecords[$i]["update"] = false;
            $ptccTempRecords[$i]["update_country"] = false;
            $ptccTempRecords[$i]["update_first_name"] = false;
            $ptccTempRecords[$i]["update_last_name"] = false;
            $ptccTempRecords[$i]["update_phone_number"] = false;
            $ptccTempRecords[$i]["update_primary_email"] = false;
            $ptccTempRecords[$i]["update_password"] = false;
            $ptccTempRecords[$i]["update_status"] = false;
            if (!$ptccTempRecords[$i]["insert"]) {
                $ptccTempRecords[$i]["update_country"] = $ptccTempRecords[$i]["country"] !== $ptccTempRecords[$i]["old_country"];
                $ptccTempRecords[$i]["update_first_name"] = $ptccTempRecords[$i]["first_name"] !== $ptccTempRecords[$i]["old_first_name"];
                $ptccTempRecords[$i]["update_last_name"] = $ptccTempRecords[$i]["last_name"] !== $ptccTempRecords[$i]["old_last_name"];
                $ptccTempRecords[$i]["update_phone_number"] = $ptccTempRecords[$i]["phone_number"] !== $ptccTempRecords[$i]["old_phone_number"];
                $ptccTempRecords[$i]["update_primary_email"] = $ptccTempRecords[$i]["primary_email"] !== $ptccTempRecords[$i]["old_primary_email"];
                $ptccTempRecords[$i]["update_password"] = $ptccTempRecords[$i]["password"] !== $ptccTempRecords[$i]["old_password"] && $ptccTempRecords[$i]["old_force_password_reset"];
                $ptccTempRecords[$i]["update_status"] = $ptccTempRecords[$i]["status"] !== $ptccTempRecords[$i]["old_status"];
                $ptccTempRecords[$i]["update"] = $ptccTempRecords[$i]["update_country"] || $ptccTempRecords[$i]["update_first_name"] || $ptccTempRecords[$i]["update_last_name"] || $ptccTempRecords[$i]["update_phone_number"] || $ptccTempRecords[$i]["update_primary_email"] || $ptccTempRecords[$i]["update_password"] || $ptccTempRecords[$i]["update_status"];
                if ($ptccTempRecords[$i]["update"]) {
                    $ptccTempRecords[$i]["import_action"] = "Change";
                }
            } else {
                $ptccTempRecords[$i]["import_action"] = "New";
            }
        }
        return $ptccTempRecords;
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

