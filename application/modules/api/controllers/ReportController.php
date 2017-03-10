<?php

class Api_ReportController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->layout()->setLayout('api');
    }

    public function indexAction() {
        // http://ept/api/report?sid=4&pid=2
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        if (!isset($authNameSpace->dm_id)) {
            $this->getResponse()->setHttpResponseCode(401);
            Zend_Session::namespaceUnset('datamanagers');
        } else {
            $sID = intval($this->getRequest()->getParam('sid'));
            $pID = intval($this->getRequest()->getParam('pid'));
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $this->view->result = $db->fetchRow($db->select()
                ->from(array('spm' => 'shipment_participant_map'), array('spm.map_id'))
                ->join(array('s' => 'shipment'), 's.shipment_id=spm.shipment_id', array('s.shipment_code'))
                ->join(array('p' => 'participant'), 'p.participant_id=spm.participant_id', array('p.first_name', 'p.last_name'))
                ->where("spm.shipment_id = ?", $sID)
                ->where("spm.participant_id = ?", $pID));
        }
    }

    public function viewAction() {
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        if (!isset($authNameSpace->dm_id)) {
            $this->getResponse()->setHttpResponseCode(401);
            Zend_Session::namespaceUnset('datamanagers');
        } else {
            $sID = intval($this->getRequest()->getParam('sid'));
            $pID = intval($this->getRequest()->getParam('pid'));

            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $result = $db->fetchRow($db->select()
                ->from(array('spm' => 'shipment_participant_map'), array('spm.map_id'))
                ->join(array('s' => 'shipment'), 's.shipment_id=spm.shipment_id', array('s.shipment_code'))
                ->join(array('p' => 'participant'), 'p.participant_id=spm.participant_id', array('p.first_name', 'p.last_name'))
                ->where("spm.shipment_id = ?", $sID)
                ->where("spm.participant_id = ?", $pID));

            if(isset($result['last_name']) && trim($result['last_name'])!=""){
                $result['last_name']="_".$result['last_name'];
            }
            $fileName=$result['first_name'].$result['last_name']."-".$result['map_id'].".pdf";
            $fileName = preg_replace('/[^A-Za-z0-9.]/', '-', $fileName);
            $fileName = str_replace(" ", "-", $fileName);
            $this->view->url = '../../uploads/reports/'.$result['shipment_code'].'/'.$fileName;
        }
    }
}



