<?php

class Admin_ShipmentController extends Zend_Controller_Action {
    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')
                ->addActionContext('get-sample-form', 'html')
                ->addActionContext('get-shipment-code', 'html')
                ->addActionContext('remove', 'html')
                ->addActionContext('view-enrollments', 'html')
                ->addActionContext('delete-shipment-participant', 'html')
                ->addActionContext('new-shipment-mail', 'html')
                ->addActionContext('unenrollments', 'html')
                ->addActionContext('response-switch', 'html')
                ->addActionContext('shipment-responded-participants', 'html')
                ->addActionContext('shipment-not-responded-participants', 'html')
                ->addActionContext('shipment-not-enrolled-participants', 'html')
                ->addActionContext('export-shipment-responded-participants', 'html')
                ->addActionContext('export-shipment-not-responded-participants', 'html')
                ->initContext();
        $this->_helper->layout()->pageName = 'manageMenu';
    }

    public function indexAction() {
        $shipmentService = new Application_Service_Shipments();
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();
            $shipmentService->echoAllShipments($params);
        } else if ($this->_hasParam('searchString')) {
            $this->view->searchData = $this->_getParam('searchString');
        }
        if ($this->_hasParam('did')) {
            $this->view->selectedDistribution = (int) base64_decode($this->_getParam('did'));
        } else {
            $this->view->selectedDistribution = "";
        }
        $distro = new Application_Service_Distribution();
        $unshippedDistributions = $distro->getUnshippedDistributions();
        $tbShipments = $shipmentService->getShipmentsForScheme('tb');
        $unshippedDistributionsArray = array();
        foreach ($unshippedDistributions as $dist) {
            array_push($unshippedDistributionsArray, iterator_to_array($dist));
        }
        for ($i = 0; $i < count($unshippedDistributionsArray); $i++) {
            $distributionShipmentCodes = array();
            foreach ($tbShipments as $tbShipment) {
                if ($tbShipment['distribution_id'] == $unshippedDistributionsArray[$i]['distribution_id']) {
                    array_push($distributionShipmentCodes, $tbShipment['shipment_code']);
                }
            }
            $unshippedDistributionsArray[$i]['shipment_codes'] = $distributionShipmentCodes;
        }
        $this->view->tbShipments = $tbShipments;
        $this->view->unshippedDistro = $unshippedDistributionsArray;
    }

    public function addAction() {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $distributionService = new Application_Service_Distribution();
            $distribution=$distributionService->addDistributionasshipmentcode($params);

            $shipmentService = new Application_Service_Shipments();
            $shipmentService->addShipmentagainstditribution($params,$distribution);
            $this->view->tbShipments = $shipmentService->getShipmentsForScheme('tb');
            if (isset($params['selectedDistribution']) && $params['selectedDistribution'] != "" && $params['selectedDistribution'] != null) {
                $this->_redirect("/admin/shipment/index/did/" . base64_encode($params['selectedDistribution']));
            } else {
                $this->_redirect("/admin/shipment");
            }
        }
    }

    public function getSampleFormAction() {
        if ($this->_hasParam('did')) {
            $this->view->selectedDistribution = (int) base64_decode($this->_getParam('did'));
        }
        $distro = new Application_Service_Distribution();
        $unshippedDistributions = $distro->getUnshippedDistributions();
        $unshippedDistributionsArray = array();
        foreach ($unshippedDistributions as $dist) {
            array_push($unshippedDistributionsArray, iterator_to_array($dist));
        }
        $this->view->unshippedDistro = $unshippedDistributionsArray;
        if ($this->getRequest()->isPost()) {
            $this->view->scheme = $sid = strtolower('tb');
        }
    }

    public function shipItAction() {
        $shipmentService = new Application_Service_Shipments();
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getPost();
            $shipmentService->shipItNow($params);
            $this->_redirect("/admin/shipment");
        } else {
            if ($this->_hasParam('sid')) {
                $participantService = new Application_Service_Participants();
                $sid = (int)base64_decode($this->_getParam('sid'));
                $this->view->shipment = $shipmentDetails = $shipmentService->getShipment($sid);
                $this->view->countries = $countries = $participantService->getEnrolledAndUnEnrolledParticipants($sid);
            }
        }
    }

    public function removeAction() {
        if ($this->_hasParam('sid')) {
            $sid = (int) base64_decode($this->_getParam('sid'));
            $shipmentService = new Application_Service_Shipments();
            $this->view->message = $shipmentService->removeShipment($sid);
        } else {
            $this->view->message = "Unable to delete. Please try again later or contact system admin for help";
        }
    }

    public function editAction() {
        if ($this->getRequest()->isPost()) {
            $shipmentService = new Application_Service_Shipments();
            $params = $this->getRequest()->getPost();
            $shipmentService->updateShipment($params);
            $this->_redirect("/admin/shipment");
        } else {
            if ($this->_hasParam('sid')) {
                $sid = (int) base64_decode($this->_getParam('sid'));
                $shipmentService = new Application_Service_Shipments();
                $this->view->shipmentData = $response = $shipmentService->getShipmentForEdit($sid);
                if ($response == null || $response == "" || $response === false) {
                    $this->_redirect("/admin/shipment");
                }
            } else {
                $this->_redirect("/admin/shipment");
            }
        }
    }

    public function viewEnrollmentsAction() {
        $participantService = new Application_Service_Participants();
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();
            $participantService->getShipmentEnrollement($params);
        }
        if ($this->_hasParam('id')) {
            $shipmentId = (int) base64_decode($this->_getParam('id'));
            $shipmentService = new Application_Service_Shipments();
            $this->view->shipment = $shipmentService->getShipment($shipmentId);
            $this->view->shipmentCode = $this->_getParam('shipmentCode');
        } else {
            $this->_redirect("/admin/index");
        }
    }

    public function deleteShipmentParticipantAction() {
        if ($this->_hasParam('mid')) {
            if ($this->getRequest()->isPost()) {
                $mapId = (int) base64_decode($this->_getParam('mid'));
                $shipmentService = new Application_Service_Shipments();
                $this->view->result = $shipmentService->removeShipmentParticipant($mapId);
            }
        } else {
            $this->view->message = "Unable to delete. Please try again later or contact system admin for help";
        }
    }

    public function unenrollmentsAction() {
        $participantService = new Application_Service_Participants();
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();
            $participantService->getShipmentUnEnrollements($params);
        }
    }

    public function addEnrollmentsAction() {
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();
            $shipmentService = new Application_Service_Shipments();
            $shipmentService->addEnrollements($params);
            $this->_redirect("/admin/shipment/view-enrollments/id/" . $params['shipmentId']);
        }
    }

    public function getShipmentCodeAction() {
        if ($this->getRequest()->isPost()) {
            $sid = strtolower($this->_getParam('sid'));
            $shipmentService = new Application_Service_Shipments();
            $this->view->code = $shipmentService->getShipmentCode($sid);
        }
    }

    public function newShipmentMailAction() {
        if ($this->getRequest()->isPost()) {
            $sid = strtolower(base64_decode($this->_getParam('sid')));
            $shipmentService = new Application_Service_Shipments();
            $this->view->pcount = $shipmentService->getShipmentParticipants($sid);
        }
    }

    public function notParticipatedMailAction() {
        if ($this->getRequest()->isPost()) {
            $sid = strtolower(base64_decode($this->_getParam('sid')));
            $shipmentService = new Application_Service_Shipments();
            $this->view->pcount = $shipmentService->getShipmentNotParticipated($sid);
        }
    }

    public function manageResponsesAction() {
         if ($this->_hasParam('sid')) {
            $shipmentId = (int) base64_decode($this->_getParam('sid'));
            $schemeType = base64_decode($this->_getParam('sctype'));
            $shipmentService = new Application_Service_Shipments();
            $this->view->shipment = $shipmentService->getShipmentForEdit($shipmentId);
            $this->view->shipmentId = $shipmentId;
            $this->view->schemeType = $schemeType;
        }
    }

    public function shipmentRespondedParticipantsAction() {
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();
            $clientsServices = new Application_Service_Participants();
            $clientsServices->echoShipmentRespondedParticipants($params);
        }
    }

    public function shipmentNotRespondedParticipantsAction() {
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();
            $clientsServices = new Application_Service_Participants();
            $clientsServices->echoShipmentNotRespondedParticipants($params);
        }
    }

    public function shipmentNotEnrolledParticipantsAction() {
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();
            $clientsServices = new Application_Service_Participants();
            $clientsServices->getShipmentNotEnrolledParticipants($params);
        }
    }

    public function enrollShipmentParticipantAction() {
        if ($this->_hasParam('sid') && $this->_hasParam('pid')) {
            if ($this->getRequest()->isPost()) {
                $shipmentId = (int) base64_decode($this->_getParam('sid'));
                $participantId = $this->_getParam('pid');
                $shipmentService = new Application_Service_Shipments();
                $this->view->result = $shipmentService->enrollShipmentParticipant($shipmentId,$participantId);
            }
        } else {
            $this->view->message = "Unable to delete. Please try again later or contact system admin for help";
        }
    }

    public function responseSwitchAction() {
        if ($this->_hasParam('sid') && $this->_hasParam('switchStatus')) {
            if ($this->getRequest()->isPost()) {
                $shipmentId = (int) ($this->_getParam('sid'));
                $switchStatus = strtolower($this->_getParam('switchStatus'));
                $shipmentService = new Application_Service_Shipments();
                $this->view->message = $shipmentService->responseSwitch($shipmentId,$switchStatus);
            }
        } else {
            $this->view->message = "Unable to update status. Please try again later or contact system admin for help";
        }
    }

    public function exportShipmentRespondedParticipantsAction() {
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();
            $clientsServices = new Application_Service_Participants();
            $this->view->result=$clientsServices->exportShipmentRespondedParticipantsDetails($params);
        }
    }

    public function exportShipmentNotRespondedParticipantsAction() {
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();
            $clientsServices = new Application_Service_Participants();
            $this->view->result=$clientsServices->exportShipmentNotRespondedParticipantsDetails($params);
        }
    }

    public function editShipmentEmailAction() {
        $this->_helper->layout()->setLayout('adminmodal');
        $params = $this->_getAllParams();
        if ($this->getRequest()->isPost()) {
            $shipmentService = new Application_Service_Shipments();
            $shipmentService->sendEmailToParticipants($params);
        } else {
            $this->view->shipmentId = base64_decode($this->_getParam('id'));
            if ($this->_hasParam('mail_purpose')) {
                $this->view->mail_purpose = $this->_getParam('mail_purpose');
            } else {
                $this->view->mail_purpose = 'new_shipment';
            }
            $commonServices = new Application_Service_Common();
            $newShipmentMailContent = $commonServices->getEmailTemplate($this->view->mail_purpose);
            $this->view->emailSubject = $newShipmentMailContent['mail_subject'];
            $this->view->emailBody = str_replace('<p>', '',
                str_replace('</p>', '',
                    str_replace('</p><p>', "\n\n", $newShipmentMailContent['mail_content'])));
        }
    }
}

