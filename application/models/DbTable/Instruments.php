<?php

class Application_Model_DbTable_Instruments extends Zend_Db_Table_Abstract {
    protected $_name = 'instrument';
    protected $_primary = 'instrument_id';

    public function getInstruments($pid, $insertBlankRowIfEmpty) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = null;
        if ($pid == null) {
            $authNameSpace = new Zend_Session_Namespace('datamanagers');
            $userSystemId = $authNameSpace->dm_id;
            $sql = $db->select()->from(array('i'=>'instrument'))
                ->join(array('pmm'=>'participant_manager_map'),'i.participant_id=pmm.participant_id', array())
                ->where("pmm.dm_id=".$userSystemId);
        } else {
            $sql = $db->select()->from(array('i'=>'instrument'))
                ->where("i.participant_id=".$pid);
        }
        $res = $db->fetchAll($sql);
        $response = array();
        foreach ($res as $row) {
            $response[$row['instrument_id']] = array(
                'participant_id' => $row['participant_id'],
                'instrument_serial' => $row['instrument_serial'],
                'instrument_installed_on' => $row['instrument_installed_on'],
                'instrument_last_calibrated_on' => $row['instrument_last_calibrated_on']
            );
        }
        if(count($response) == 0 && $insertBlankRowIfEmpty) {
            $response["-1"] = array(
                'participant_id' => $pid,
                'instrument_serial' => "",
                'instrument_installed_on' => "",
                'instrument_last_calibrated_on' => ""
            );
        }
        return $response;
    }

    public function getInstrumentsReferenceMap($pid, $insertBlankRowIfEmpty) {
        $instruments = $this->getInstruments($pid, $insertBlankRowIfEmpty);
        $response = array();
        foreach ($instruments as $instrumentId => $instrumentDetails) {
            $response[$instrumentId] = array(
                'participantId' => $instrumentDetails['participant_id'],
                'instrumentSerial' => $instrumentDetails['instrument_serial'],
                'instrumentInstalledOn' => Pt_Commons_General::dbDateToString($instrumentDetails['instrument_installed_on']),
                'instrumentLastCalibratedOn' => Pt_Commons_General::dbDateToString($instrumentDetails['instrument_last_calibrated_on'])
            );
        }
        return $response;
    }

    public function upsertInstrument($pid, $params) {
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        $dataManagerId = $authNameSpace->dm_id;
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = null;
        if (isset($params['instrument_id'])) {
            $sql = $db->select()->from(array('i'=>'instrument'))
                ->where("i.instrument_id = ?", $params['instrument_id']);
        } else {
            $sql = $db->select()->from(array('i'=>'instrument'))
                ->where("i.participant_id = ?", $pid)
                ->where("i.instrument_serial = ?", $params['instrument_serial']);
        }
        $instruments = $db->fetchAll($sql);
        $noOfRows = 0;
        $instrumentInstalledOn = Application_Service_Common::ParseDate($params['instrument_installed_on']);
        $instrumentLastCalibratedOn = Application_Service_Common::ParseDate($params['instrument_last_calibrated_on']);
        if (count($instruments) == 0) {
            if (isset($params['instrument_serial']) && $params['instrument_serial'] != "") {
                $data = array(
                    'participant_id' => $pid,
                    'instrument_serial' => $params['instrument_serial'],
                    'created_by' => $dataManagerId,
                    'created_on' => new Zend_Db_Expr('now()')
                );
                if (isset($instrumentInstalledOn)) {
                    $data['instrument_installed_on'] = Pt_Commons_General::stringToDbDate($instrumentInstalledOn);
                }
                if (isset($instrumentLastCalibratedOn)) {
                    $data['instrument_last_calibrated_on'] = Pt_Commons_General::stringToDbDate($instrumentLastCalibratedOn);
                }
                $db->insert('instrument', $data);
                $noOfRows = 1;
            }
        } else {
            $data = array(
                'instrument_serial' => $params['instrument_serial'],
                'participant_id' => $pid
            );
            if (isset($instrumentInstalledOn) &&
                $instrumentInstalledOn != $instruments[0]['instrument_installed_on']) {
                $data['instrument_installed_on'] = Pt_Commons_General::stringToDbDate($instrumentInstalledOn);
            }
            if (isset($instrumentLastCalibratedOn) &&
                $instrumentLastCalibratedOn != $instruments[0]['instrument_last_calibrated_on']) {
                $data['instrument_last_calibrated_on'] = Pt_Commons_General::stringToDbDate($instrumentLastCalibratedOn);
            }
            if (isset($data['instrument_installed_on']) ||
                isset($data['instrument_last_calibrated_on'])) {
                $data['updated_by'] = $dataManagerId;
                $data['updated_on'] = new Zend_Db_Expr('now()');
                $noOfRows = $this->update($data, "instrument_id = " . $instruments[0]['instrument_id']);
            }
        }
        $this->updateUnfinalizedResponsesWithNewDates($pid, $params['instrument_serial']);
        return $noOfRows;
    }

    public function deleteInstrument($instrumentId) {
        $noOfRows = 0;
        if (isset($instrumentId) && $instrumentId != null && intval($instrumentId) > 0) {
            $where = $this->getAdapter()->quoteInto("instrument_id = ?", $instrumentId);
            $noOfRows = $this->delete($where);
        }
        return $noOfRows;
    }

    public function updateUnfinalizedResponsesWithNewDates($participantId, $instrumentSerial) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->select()->from(array('res'=>'response_result_tb'), array(
            "shipment_map_id" => "res.shipment_map_id",
            "sample_id" => "res.sample_id",
            "instrument_installed_on" => "res.instrument_installed_on",
            "instrument_last_calibrated_on" => "res.instrument_last_calibrated_on"
        ))
            ->join(array('spm' => 'shipment_participant_map'), 'res.shipment_map_id = spm.map_id', array())
            ->join(array('s' => 'shipment'), 'spm.shipment_id = s.shipment_id', array())
            ->join(array('i' => 'instrument'), 'res.instrument_serial = spm.map_id', array())
            ->where("res.instrument_serial = ?", $instrumentSerial)
            ->where("spm.participant_id = ?", $participantId)
            ->where("s.status <> 'finalized'")
            ->where("res.instrument_installed_on <> i.instrument_installed_on OR res.instrument_last_calibrated_on <> i.instrument_last_calibrated_on");
        $unfinalizedResponses = $db->fetchAll($sql);
        foreach ($unfinalizedResponses as $unfinalizedResponse) {
            $data = array();
            if (isset($instrumentInstalledOn)) {
                $data['instrument_installed_on'] = $instrumentInstalledOn;
            }
            if (isset($instrumentLastCalibratedOn)) {
                $data['instrument_last_calibrated_on'] = $instrumentLastCalibratedOn;
            }
            $db->update('response_result_tb', $data,
                "shipment_map_id = " . $unfinalizedResponse['shipment_map_id'] . " AND sample_id = " . $unfinalizedResponse['sample_id']
            );
        }
    }
}

