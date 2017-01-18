<?php

class Application_Model_DbTable_Instruments extends Zend_Db_Table_Abstract {
    protected $_name = 'instrument';
    protected $_primary = 'instrument_id';

    public function getInstruments($pid = null) {
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
        if (count($instruments) == 0) {
            if (isset($params['instrument_serial']) && $params['instrument_serial'] != "") {
                $data = array(
                    'participant_id' => $pid,
                    'instrument_serial' => $params['instrument_serial'],
                    'created_by' => $dataManagerId,
                    'created_on' => new Zend_Db_Expr('now()')
                );
                if (isset($params['instrument_installed_on']) &&
                    $params['instrument_installed_on'] != "") {
                    $data['instrument_installed_on'] = Pt_Commons_General::dateFormat($params['instrument_installed_on']);
                }
                if (isset($params['instrument_last_calibrated_on']) &&
                    $params['instrument_last_calibrated_on'] != "") {
                    $data['instrument_last_calibrated_on'] = Pt_Commons_General::dateFormat($params['instrument_last_calibrated_on']);
                }
                $db->insert('instrument', $data);
                $noOfRows = 1;
            }
        } else {
            $data = array(
                'instrument_serial' => $params['instrument_serial'],
                'participant_id' => $pid
            );
            if (isset($params['instrument_installed_on']) &&
                $params['instrument_installed_on'] != $instruments[0]['instrument_installed_on']) {
                $data['instrument_installed_on'] = Pt_Commons_General::dateFormat($params['instrument_installed_on']);
            }
            if (isset($params['instrument_last_calibrated_on']) &&
                $params['instrument_last_calibrated_on'] != $instruments[0]['instrument_last_calibrated_on']) {
                $data['instrument_last_calibrated_on'] = Pt_Commons_General::dateFormat($params['instrument_last_calibrated_on']);
            }
            if (isset($data['instrument_installed_on']) ||
                isset($data['instrument_last_calibrated_on'])) {
                $data['updated_by'] = $dataManagerId;
                $data['updated_on'] = new Zend_Db_Expr('now()');
                $noOfRows = $this->update($data, "instrument_id = " . $instruments[0]['instrument_id']);
            }
        }
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
}

