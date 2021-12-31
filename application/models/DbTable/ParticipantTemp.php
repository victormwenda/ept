<?php

class Application_Model_DbTable_ParticipantTemp extends Zend_Db_Table_Abstract {
    protected $_name = 'participant_temp';
    protected $_primary = 'unique_identifier';

    public function getParticipantTempRecords() {
        $participantTempRecords = $this->getAdapter()->fetchAll(
            $this->getAdapter()
                ->select()
                ->from(array('pt' => $this->_name), array(
                    "sorting_unique_identifier" => new Zend_Db_Expr("LPAD(pt.unique_identifier, 10, '0')"),
                    "unique_identifier" => "pt.unique_identifier",
                    "lab_name" => "pt.lab_name",
                    "country" => "pt.country",
                    "region" => "pt.region",
                    "username" => "pt.username",
                    "password" => "pt.password",
                    "status" => "pt.status",
                    "participant_status" => "pt.participant_status",
                    "email" => "pt.email",
                    "phone_number" => "pt.phone_number",
                    "participant_id" => "pt.participant_id",
                    "dm_id" => "pt.dm_id",
                    "country_id" => "pt.country_id"
                ))
                ->joinLeft(array('p' => 'participant'), 'pt.participant_id = p.participant_id', array(
                    "old_lab_name" => "p.lab_name",
                    "old_email" => "p.email",
                    "old_phone" => "p.phone",
                    "old_mobile" => "p.mobile",
                    "old_region" => "p.region",
                    "old_participant_status" => "p.status"
                ))
                ->joinLeft(array('c' => 'countries'), 'p.country = c.id', array("old_country" => "c.iso_name"))
                ->joinLeft(array('dm' => 'data_manager'), 'pt.dm_id = dm.dm_id', array(
                    "old_username" => "dm.primary_email",
                    "old_password" => "dm.password",
                    "old_force_password_reset" => "dm.force_password_reset",
                    "old_dm_status" => "dm.status"
                ))
                ->joinLeft(array('pmm' => 'participant_manager_map'), 'pt.participant_id = pmm.participant_id AND pt.dm_id = pmm.dm_id', array(
                    "insert_user_link" => new Zend_Db_Expr("pmm.dm_id IS NULL AND pt.dm_id IS NOT NULL AND pt.participant_id IS NOT NULL")
                ))
                ->order("sorting_unique_identifier ASC"));
        $usernamesInImport = array_unique(array_column($participantTempRecords, "username"));
        $ptIdsInImport = array_unique(array_column($participantTempRecords, "unique_identifier"));
        $participantActiveUsernamesNotInImport = $this->getAdapter()->fetchAll(
            $this->getAdapter()
                ->select()
                ->from(array('p' => 'participant'), array())
                ->join(array('pmm' => 'participant_manager_map'), 'p.participant_id = pmm.participant_id', array())
                ->join(array('dm' => 'data_manager'), 'pmm.dm_id = dm.dm_id', array("primary_email" => "dm.primary_email"))
                ->where("dm.primary_email IN (?)", $usernamesInImport)
                ->where("p.unique_identifier NOT IN (?)", $ptIdsInImport)
                ->where("dm.status = 'active'")
                ->distinct());
        for ($i = 0; $i < count($participantTempRecords); $i++) {
            $participantTempRecords[$i]["import_action"] = "None";
            $participantTempRecords[$i]["insert"] = !$participantTempRecords[$i]["participant_id"];
            $participantTempRecords[$i]["insert_user"] = $participantTempRecords[$i]["username"] && !$participantTempRecords[$i]["dm_id"];
            $participantTempRecords[$i]["update"] = false;
            $participantTempRecords[$i]["update_lab_name"] = false;
            $participantTempRecords[$i]["update_country"] = false;
            $participantTempRecords[$i]["update_region"] = false;
            $participantTempRecords[$i]["update_username"] = false;
            $participantTempRecords[$i]["update_password"] = false;
            $participantTempRecords[$i]["update_dm_status"] = false;
            $participantTempRecords[$i]["update_participant_status"] = false;
            $participantTempRecords[$i]["update_email"] = false;
            $participantTempRecords[$i]["update_phone_number"] = false;
            if ($participantTempRecords[$i]["insert"]) {
                $participantTempRecords[$i]["import_action"] = "New";
            } else {
                $participantTempRecords[$i]["update_lab_name"] = $participantTempRecords[$i]["lab_name"] != $participantTempRecords[$i]["old_lab_name"];
                $participantTempRecords[$i]["update_country"] = $participantTempRecords[$i]["country"] != $participantTempRecords[$i]["old_country"];
                $participantTempRecords[$i]["update_region"] = $participantTempRecords[$i]["region"] != $participantTempRecords[$i]["old_region"];
                $participantTempRecords[$i]["update_username"] = $participantTempRecords[$i]["old_username"] &&
                    $participantTempRecords[$i]["username"] != $participantTempRecords[$i]["old_username"];
                $participantTempRecords[$i]["update_email"] = $participantTempRecords[$i]["email"] != $participantTempRecords[$i]["old_email"];
                $participantTempRecords[$i]["update_password"] = isset($participantTempRecords[$i]["password"]) &&
                    !!$participantTempRecords[$i]["password"] &&
                    $participantTempRecords[$i]["password"] != $participantTempRecords[$i]["old_password"] &&
                    $participantTempRecords[$i]["old_force_password_reset"];
                $participantTempRecords[$i]["update_dm_status"] = $participantTempRecords[$i]["old_dm_status"] &&
                    $participantTempRecords[$i]["status"] != $participantTempRecords[$i]["old_dm_status"];
                if ($participantTempRecords[$i]["update_dm_status"] && $participantTempRecords[$i]["status"] == "inactive") {
                    if (in_array($participantTempRecords[$i]["username"], $participantActiveUsernamesNotInImport)) {
                        $participantTempRecords[$i]["update_dm_status"] = false;
                        $participantTempRecords[$i]["status"] = "active";
                    } else {
                        $ptId = $participantTempRecords[$i]["unique_identifier"];
                        $username = $participantTempRecords[$i]["username"];
                        $otherParticipantsInWorksheetLinkedToUserWithActiveStatus = array_filter($participantTempRecords, function ($participantTempRecord) use ($username, $ptId) {
                            return $participantTempRecord["username"] == $username &&
                                $participantTempRecord["unique_identifier"] != $ptId &&
                                $participantTempRecord["status"] == 'active';
                        });
                        if (count($otherParticipantsInWorksheetLinkedToUserWithActiveStatus)) {
                            $participantTempRecords[$i]["update_dm_status"] = false;
                            $participantTempRecords[$i]["status"] = "active";
                        }
                    }
                }
                $participantTempRecords[$i]["update_participant_status"] = $participantTempRecords[$i]["participant_status"] != $participantTempRecords[$i]["old_participant_status"];
                $participantTempRecords[$i]["update_phone_number"] = $participantTempRecords[$i]["phone_number"] != $participantTempRecords[$i]["old_phone"];
                $participantTempRecords[$i]["update"] = $participantTempRecords[$i]["update_lab_name"] ||
                    $participantTempRecords[$i]["update_country"] ||
                    $participantTempRecords[$i]["update_region"] ||
                    $participantTempRecords[$i]["update_email"] ||
                    $participantTempRecords[$i]["update_username"] ||
                    $participantTempRecords[$i]["update_password"] ||
                    $participantTempRecords[$i]["update_dm_status"] ||
                    $participantTempRecords[$i]["update_participant_status"] ||
                    $participantTempRecords[$i]["update_phone_number"];
                if ($participantTempRecords[$i]["insert_user"]) {
                    $participantTempRecords[$i]["import_action"] = "New User";
                } else if ($participantTempRecords[$i]["insert_user_link"]) {
                    $participantTempRecords[$i]["import_action"] = "Link User";
                } else if ($participantTempRecords[$i]["update"]) {
                    $participantTempRecords[$i]["import_action"] = "Change";
                }
            }
        }
        return $participantTempRecords;
    }

    public function clearParticipantTempRecords() {
        $db = Zend_Db_Table_Abstract::getAdapter();
        $db->delete('participant_temp');
    }

    public function addParticipantTempRecords($records) {
        $db = Zend_Db_Table_Abstract::getAdapter();
        foreach ($records as $record) {
            if (!isset($record["password"])) {
                $record["password"] = null;
            }
            if (!isset($record["status"])) {
                $record["status"] = null;
            }
            $db->insert('participant_temp', array(
                'unique_identifier' => $record["PT ID"],
                'lab_name' =>  $record["Lab Name"],
                'country' =>  $record["Country"],
                'region' =>  $record["Region"],
                'username' =>  $record["Username"],
                'password' =>  $record["password"],
                'status' =>  $record["status"],
                'participant_status' => $record["participant_status"],
                'email' =>  $record["Email"],
                'phone_number' =>  $record["Phone Number"],
                'participant_id' =>  $record["participant_id"],
                'dm_id' =>  $record["dm_id"],
                'country_id' =>  $record["country_id"]
            ));
        }
    }
}

