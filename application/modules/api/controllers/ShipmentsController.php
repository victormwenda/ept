<?php

class Api_ShipmentsController extends Zend_Controller_Action {
    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')
            ->initContext();
        $this->_helper->layout()->setLayout('api');
    }

    public function indexAction() {
        $authNameSpace = new Zend_Session_Namespace('datamanagers');
        if (!isset($authNameSpace->dm_id)) {
            $this->getResponse()->setHttpResponseCode(401);
            Zend_Session::namespaceUnset('datamanagers');
        } else {
            $this->getResponse()->setHeader("Content-Type", "application/json");
            $shipmentService = new Application_Service_Shipments();
            $shipmentService->getShipmentCurrent(array(
                "currentType" => "active",
                "forMobileApp" => true
            ));
        }
    }
}



