<?php

class Admin_ParticipantsController extends Zend_Controller_Action {
    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')
	            ->addActionContext('view-participants', 'html')
	            ->addActionContext('get-datamanager', 'html')
	            ->addActionContext('get-participant', 'html')
                ->initContext();
        $this->_helper->layout()->pageName = 'configMenu';
    }

    public function indexAction() {
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();            
            $clientsServices = new Application_Service_Participants();
            $clientsServices->getAllParticipants($params);
        }
    }

    public function addAction() {
        $participantService = new Application_Service_Participants();
        $commonService = new Application_Service_Common();
        $dataManagerService = new Application_Service_DataManagers();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
	        $participantService->addParticipant($params);
            $this->_redirect("/admin/participants");
        }
        $this->view->affiliates = $participantService->getAffiliateList();
        $this->view->networks = $participantService->getNetworkTierList();
        $this->view->dataManagers = $dataManagerService->getDataManagerList();
        $this->view->countriesList = $commonService->getcountriesList();
        $this->view->enrolledPrograms = $participantService->getEnrolledProgramsList();
        $this->view->siteType = $participantService->getSiteTypeList();
    }

    public function editAction() {
        $participantService = new Application_Service_Participants();
		$commonService = new Application_Service_Common();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $participantService->updateParticipant($params);
            $this->_redirect("/admin/participants");
        } else {
            if ($this->_hasParam('id')) {
                $userId = (int)$this->_getParam('id');
                $this->view->participant = $participantService->getParticipantDetails($userId);
            }
            $this->view->affiliates = $participantService->getAffiliateList();
            $dataManagerService = new Application_Service_DataManagers();
            $this->view->networks = $participantService->getNetworkTierList();
            $this->view->enrolledPrograms = $participantService->getEnrolledProgramsList();
			$this->view->siteType = $participantService->getSiteTypeList();
            $this->view->dataManagers = $dataManagerService->getDataManagerList();
			$this->view->countriesList = $commonService->getcountriesList();
        }
		$scheme = new Application_Service_Schemes();
        $this->view->schemes = $scheme->getAllSchemes();
        $this->view->participantSchemes = $participantService->getSchemesByParticipantId($userId);
    }

    public function pendingAction() {
        // action body
    }

    public function viewParticipantsAction() {
	    $this->_helper->layout()->setLayout('modal');
	    $participantService = new Application_Service_Participants();
	    if ($this->_hasParam('id')) {
            $dmId = (int)$this->_getParam('id');
            $this->view->participant = $participantService->getAllParticipantDetails($dmId);
        }
    }

    public function participantManagerMapAction() {
       	$participantService = new Application_Service_Participants();
    	$dataManagerService = new Application_Service_DataManagers();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $participantService->addParticipantManagerMap($params);
            $this->_redirect("/admin/participants/participant-manager-map");
        }
        $this->view->participants = $participantService->getAllActiveParticipants();
        $this->view->dataManagers = $dataManagerService->getDataManagerList();
    }

    public function getDatamanagerAction() {
        $dataManagerService = new Application_Service_DataManagers();
        if ($this->_hasParam('participantId')) {
            $participantId = $this->_getParam('participantId');
            $this->view->paticipantManagers = $dataManagerService->getParticipantDatamanagerList($participantId);
        }
        $this->view->dataManagers = $dataManagerService->getDataManagerList();
    }

    public function getParticipantAction() {
        $participantService = new Application_Service_Participants();
        $dataManagerService = new Application_Service_DataManagers();
        if ($this->_hasParam('datamanagerId')) {
            $datamanagerId = $this->_getParam('datamanagerId');
            $this->view->mappedParticipant = $dataManagerService->getDatamanagerParticipantList($datamanagerId);
        }
        $this->view->participants = $participantService->getAllActiveParticipants();
    }

    public function importAction() {
        if ($this->getRequest()->isPost()) {
            $upload = new Zend_File_Transfer_Adapter_Http();
            try {
                if (!$upload->receive()) {
                    $messages = $upload->getMessages();
                    error_log(implode("\n", $messages), 0);
                } else {
                    $location = $upload->getFileName('importParticipantsExcelFile');
                    $excelReaderService = new Application_Service_ExcelProcessor();
                    $importDataOnFirstSheet = $excelReaderService->readParticipantImport($location);

                    $participantService = new Application_Service_Participants();
                    $tempParticipants = $participantService->saveTempParticipants($importDataOnFirstSheet);
                    $this->view->tempParticipants = $tempParticipants;
                    $this->view->numberOfUnchanged = count(array_filter($tempParticipants, function($tempParticipant) {
                        return !$tempParticipant["insert"] && !$tempParticipant["update"];
                    }));
                    $this->view->numberOfUpdates = count(array_filter($tempParticipants, function($tempParticipant) {
                        return $tempParticipant["update"];
                    }));
                    $this->view->numberOfInserts = count(array_filter($tempParticipants, function($tempParticipant) {
                        return $tempParticipant["insert"];
                    }));
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




