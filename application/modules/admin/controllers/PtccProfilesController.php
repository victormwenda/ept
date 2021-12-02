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
        $authNameSpace = new Zend_Session_Namespace('administrators');
        $ptccProfileService = new Application_Service_PtccProfile();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $ptccProfileService->savePtccProfile($params);
            if ($authNameSpace->is_ptcc_coordinator == 0) {
                $this->_redirect("/admin/ptcc-profiles");
            } else {
                $this->_redirect("/admin");
            }
        } elseif ($this->_hasParam('id')) {
            $idParamValue = $this->_getParam('id');
            $this->view->idParam = $idParamValue;
            if ($idParamValue == "me") {
                $this->view->ptccProfile = $ptccProfileService->getSystemPtccProfileDetails($authNameSpace->admin_id);
            } else {
                $adminId = (int)$this->_getParam('id');
                $this->view->ptccProfile = $ptccProfileService->getSystemPtccProfileDetails($adminId);
            }
        } else {
            $this->view->ptccProfile = $ptccProfileService->getSystemPtccProfileDetails();
        }
    }

    public function importAction() {
        if ($this->getRequest()->isPost()) {
            $upload = new Zend_File_Transfer_Adapter_Http();
            try {
                if (!$upload->receive()) {
                    $messages = $upload->getMessages();
                    error_log(implode("\n", $messages), 0);
                } else {
                    $location = $upload->getFileName('importPtccsExcelFile');
                    $excelReaderService = new Application_Service_ExcelProcessor();
                    $importDataOnFirstSheet = $excelReaderService->readPtccImport($location);

                    $ptccProfileService = new Application_Service_PtccProfile();
                    $this->view->tempPtccs = $ptccProfileService->saveTempPtccs($importDataOnFirstSheet);
                    // Load excel file into temp table and render temp details in import.phtml
                    // test in edge
                }
            }
            catch(Zend_File_Transfer_Exception $e){
                error_log($e->getMessage(), 0);
            }
        }
    }
}





