<?php

class Api_ReportController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->layout()->setLayout('api');
    }

    public function indexAction() {
        // http://ept/api/report/mid=MTM=
        $id = (int) base64_decode($this->getRequest()->getParam('mid'));
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $this->view->result = $db->fetchRow($db->select()
            ->from(array('spm' => 'shipment_participant_map'), array('spm.map_id'))
            ->join(array('s' => 'shipment'), 's.shipment_id=spm.shipment_id', array('s.shipment_code'))
            ->join(array('p' => 'participant'), 'p.participant_id=spm.participant_id', array('p.first_name', 'p.last_name'))
            ->where("spm.map_id = ?", $id));
    }
}



