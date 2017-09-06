<?php

class Admin_DistributionsController extends Zend_Controller_Action {
    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')
                    ->addActionContext('remove', 'html')
                    ->addActionContext('view-shipment', 'html')
                    ->addActionContext('ship-distribution', 'html')
                    ->initContext();
        $this->_helper->layout()->pageName = 'manageMenu';
    }

    public function indexAction() {
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();            
            $distributionService = new Application_Service_Distribution();
            $distributionService->echoAllDistributions($params);
        } else if ($this->_hasParam('searchString')) {
           $this->view->searchData = $this->_getParam('searchString');
        }
    }

    public function addAction() {
        $distributionService = new Application_Service_Distribution();
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();            
            $distributionService->addDistribution($params);
            $this->_redirect("/admin/distributions");
        }
        $this->view->distributionDates = $distributionService->getDistributionDates();
    }

    public function viewShipmentAction() {
        $this->_helper->layout()->disableLayout();
        if ($this->_hasParam('id')) {
            $id = (int)$this->_getParam('id');
            $distributionService = new Application_Service_Distribution();
            $this->view->shipments = $distributionService->getShipments($id);
        }
    }

    public function shipDistributionAction() {
        if ($this->_hasParam('did')) {
            $id = (int)base64_decode($this->_getParam('did'));
            $distributionService = new Application_Service_Distribution();
            $this->view->message = $distributionService->shipDistribution($id);
        } else {
            $this->view->message = "Unable to ship. Please try again later or contact system admin for help";
        }
    }

    public function editAction() {
        $distributionService = new Application_Service_Distribution();
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();
            $distributionService->updateDistribution($params);
            $this->_redirect("/admin/distributions");
        } else {
            $id = (int)base64_decode($this->_getParam('id'));
            $this->view->result = $distributionService->getDistribution($id);
            $this->view->distributionDates = $distributionService->getDistributionDates();
            if ($this->_hasParam('status')) {
                $this->view->fromStatus = 'shipped';
            }
        }
    }

    public function removeAction() {
        if ($this->_hasParam('did')) {
            $did = (int) base64_decode($this->_getParam('did'));
            $distributionService = new Application_Service_Distribution();
            $this->view->message = $distributionService->removeDistribution($did);
        } else {
            $this->view->message = "Unable to delete. Please try again later or contact system admin for help";
        }
    }
}









