<?php

class Reports_ShipmentController extends Zend_Controller_Action {
    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('generate-forms', 'html')
                    ->initContext();
        $this->_helper->layout()->pageName = 'Generate Forms';
    }

    public function generateFormsAction() {
        if ($this->_hasParam('sid')) {
            $shipmentId = (int) base64_decode($this->_getParam('sid'));
            $shipmentService = new Application_Service_Shipments();
            $this->view->shipment = $shipmentService->getShipmentForEdit($shipmentId);
        }
    }
}





