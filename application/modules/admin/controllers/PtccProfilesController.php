<?php

class Admin_PtccProfilesController extends Zend_Controller_Action {
    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')
                ->initContext();
        $this->_helper->layout()->pageName = 'configMenu';
    }

    public function indexAction() {
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();            
            $clientsServices = new Application_Service_PtccProfile();
            $clientsServices->getAllPtccProfiles($params);
        }
    }

    public function saveAction() {
        $ptccProfileService = new Application_Service_PtccProfile();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $ptccProfileService->savePtccProfile($params);
            $this->_redirect("/admin/ptcc-profiles");
        }
    }

    public function editAction() {
        $ptccProfileService = new Application_Service_PtccProfile();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $ptccProfileService->savePtccProfile($params);
            $this->_redirect("/admin/ptcc-profiles");
        } elseif ($this->_hasParam('id')) {
            $adminId = (int)$this->_getParam('id');
            $this->view->ptccProfile = $ptccProfileService->getSystemPtccProfileDetails($adminId);
        } else {
            $this->view->ptccProfile = $ptccProfileService->getSystemPtccProfileDetails();
        }
    }
}





