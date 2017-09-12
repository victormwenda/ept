<?php

class TbController extends Zend_Controller_Action
{
    public function init() { }

    public function indexAction() { }

    public function responseAction() {
        $schemeService = new Application_Service_Schemes();
        $shipmentService = new Application_Service_Shipments();
        
    	if ($this->getRequest()->isPost()) {
    	    $data = $this->getRequest()->getPost();
            $shipmentService->updateTbResults($data);
            $this->_redirect("/participant/dashboard");
        } else {
            $sID= $this->getRequest()->getParam('sid');
            $pID= $this->getRequest()->getParam('pid');
            $eID =$this->getRequest()->getParam('eid');
        
            $participantService = new Application_Service_Participants();
            $this->view->participant = $participantService->getParticipantDetails($pID);
            $this->view->allSamples = $schemeService->getTbSamples($sID,$pID);
            $shipment = $schemeService->getShipmentData($sID,$pID);
	        $shipment['attributes'] = json_decode($shipment['attributes'],true);
            $this->view->assays = $schemeService->getTbAssayReferenceMap();
            $instrumentDb = new Application_Model_DbTable_Instruments();
            $this->view->instruments = $instrumentDb->getInstruments($pID);
            $this->view->shipment = $shipment;
            $this->view->shipId = $sID;
            $this->view->participantId = $pID;
            $this->view->eID = $eID;
    
            $this->view->isEditable = $shipmentService->isShipmentEditable($sID,$pID);
	    
            $commonService = new Application_Service_Common();
            $this->view->globalQcAccess = $commonService->getConfig('qc_access');
            $this->view->allNotTestedReason = $schemeService->getNotTestedReasons('tb');
    	}
    }

    public function downloadAction() {
        $this->_helper->layout()->disableLayout();
        $sID= $this->getRequest()->getParam('sid');
        $pID= $this->getRequest()->getParam('pid');
        $eID =$this->getRequest()->getParam('eid');

        $reportService = new Application_Service_Reports();
        $this->view->header=$reportService->getReportConfigValue('report-header');
        $this->view->logo=$reportService->getReportConfigValue('logo');
        $this->view->logoRight=$reportService->getReportConfigValue('logo-right');

        $participantService = new Application_Service_Participants();
        $this->view->participant = $participantService->getParticipantDetails($pID);
        $schemeService = new Application_Service_Schemes();
        $this->view->referenceDetails = $schemeService->getTbReferenceData($sID);
        $shipment = $schemeService->getShipmentData($sID,$pID);
        $shipment['attributes'] = json_decode($shipment['attributes'],true);
        $this->view->shipment = $shipment;
    }
}



