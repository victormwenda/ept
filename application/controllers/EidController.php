<?php

class EidController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function indexAction()
    {
        // action body
    }

    public function responseAction()
    {
        $schemeService = new Application_Service_Schemes();
        $shipmentService = new Application_Service_Shipments();
        $this->view->extractionAssay = $schemeService->getEidExtractionAssay();
        $this->view->detectionAssay = $schemeService->getEidDetectionAssay();
    	if($this->getRequest()->isPost())
    	{
    		$data = $this->getRequest()->getPost();
			$data['uploadedFilePath'] = "";
			if ((!empty($_FILES["uploadedFile"])) && ($_FILES['uploadedFile']['error'] == 0)) {
				$filename = basename($_FILES['uploadedFile']['name']);
				$ext = substr($filename, strrpos($filename, '.') + 1);
				if (($_FILES["uploadedFile"]["size"] < 5000000)) {
					$dirpath = "dts-early-infant-diagnosis".DIRECTORY_SEPARATOR.$data['schemeCode'].DIRECTORY_SEPARATOR.$data['participantId'];
					$uploadDir = UPLOAD_PATH.DIRECTORY_SEPARATOR.$dirpath;
					if(!is_dir($uploadDir)){
						mkdir($uploadDir,0777,true);
					}
					$files = glob($uploadDir.'/*{,.}*', GLOB_BRACE); // get all file names
					foreach($files as $file){ // iterate files
					  if(is_file($file))
						unlink($file); // delete file
					}
					$data['uploadedFilePath'] = $dirpath.DIRECTORY_SEPARATOR.$filename;
					$newname = $uploadDir.DIRECTORY_SEPARATOR.$filename;
					move_uploaded_file($_FILES['uploadedFile']['tmp_name'],$newname);
				}
            }
            $shipmentService->updateEidResults($data);
    		$this->_redirect("/participant/current-schemes");
        } else {
            $sID= $this->getRequest()->getParam('sid');
            $pID= $this->getRequest()->getParam('pid');
            $eID =$this->getRequest()->getParam('eid');
            $participantService = new Application_Service_Participants();
            $this->view->participant = $participantService->getParticipantDetails($pID);
	        $this->view->eidPossibleResults = $schemeService->getPossibleResults('eid');
            $this->view->allSamples =$schemeService->getEidSamples($sID,$pID);
            $shipment = $schemeService->getShipmentData($sID,$pID);
	        $shipment['attributes'] = json_decode($shipment['attributes'],true);
            $this->view->shipment = $shipment;
            $this->view->shipId = $sID;
            $this->view->participantId = $pID;
            $this->view->eID = $eID;
            $authNameSpace = new Zend_Session_Namespace('administrators');
            $this->view->isEditable = $shipmentService->isShipmentEditable($sID, !$authNameSpace->is_ptcc_coordinator);
	        $commonService = new Application_Service_Common();
	        $this->view->modeOfReceipt=$commonService->getAllModeOfReceipt();
	        $this->view->globalQcAccess=$commonService->getConfig('qc_access');
    	}
    }

    public function downloadAction()
    {
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
		$this->view->referenceDetails = $schemeService->getEidReferenceData($sID);
	    
		$shipment = $schemeService->getShipmentData($sID,$pID);
	    $shipment['attributes'] = json_decode($shipment['attributes'],true);
	    $this->view->shipment = $shipment;
    }

    public function deleteAction()
    {
        
    }


}


