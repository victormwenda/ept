<?php

class Application_Service_Distribution {
	public function echoAllDistributions($params) {
		$distributionDb = new Application_Model_DbTable_Distribution();
		$distributionDb->echoAllDistributions($params);
	}

	public function addDistribution($params) {
        $distributionDb = new Application_Model_DbTable_Distribution();
		return $distributionDb->addDistribution($params);
	}
	public function AddDistributionAsShipmentCode($params) {
        $distributionDb = new Application_Model_DbTable_Distribution();
		return $distributionDb->AddDistributionAsShipmentCode($params);
	}
	public function getDistribution($did) {
        $distributionDb = new Application_Model_DbTable_Distribution();
		return $distributionDb->getDistribution($did);
	}

	public function updateDistribution($params) {
        $distributionDb = new Application_Model_DbTable_Distribution();
		return $distributionDb->updateDistribution($params);
	}

	public function getDistributionDates() {
        $distributionDb = new Application_Model_DbTable_Distribution();
		return $distributionDb->getDistributionDates();
	}

	public function getShipments($distroId) {
	    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql = $db->select()->from(array('s'=>'shipment'))
				->where("distribution_id = ?",$distroId);
										  
	    return $db->fetchAll($sql);
	}
	
	public function getUnshippedDistributions() {
        $distributionDb = new Application_Model_DbTable_Distribution();
		return $distributionDb->getUnshippedDistributions();
	}
	
	public function updateDistributionStatus($distributionId,$status) {
        $distributionDb = new Application_Model_DbTable_Distribution();
		return $distributionDb->updateDistributionStatus($distributionId,$status);
	}
	
	public function shipDistribution($distributionId) {
        $shipmentDb = new Application_Model_DbTable_Shipments();
        $tbShipmentData = $shipmentDb->getTbShipmentRowInfo($distributionId);

		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$db->beginTransaction();
		try {
			$shipmentDb->updateShipmentStatusByDistribution($distributionId, "shipped");
			
			$distributionDb = new Application_Model_DbTable_Distribution();
			$distributionDb->updateDistributionStatus($distributionId,"shipped");

			if ($tbShipmentData != "" && $tbShipmentData['scheme_type'] == 'tb') {
                $tempPushNotificationsDb = new Application_Model_DbTable_TempPushNotification();
                $pushNotifications = $shipmentDb->getShipmentShippedPushNotifications($distributionId);
                foreach ($pushNotifications as $pushNotificationData) {
                    $tempPushNotificationsDb->insertTempPushNotificationDetails(
                        $pushNotificationData['push_notification_token'],
                        'default', 'ePT ' . $pushNotificationData['shipment_code'] . ' Shipped',
                        'ePT panel ' . $pushNotificationData['shipment_code'] . ' has been shipped to ' . $pushNotificationData['lab_name'] . '. Did you receive it?',
                        '{"title": "ePT ' . $pushNotificationData['shipment_code'] . ' Shipped", "body": "ePT panel ' . $pushNotificationData['shipment_code'] . ' has been shipped to ' . $pushNotificationData['lab_name'] . '. Did you receive it?", "dismissText": "Close", "actionText": "Confirm", "shipmentId": ' . $pushNotificationData['shipment_id'] . ', "participantId": ' . $pushNotificationData['participant_id'] . ', "action": "receive_shipment"}');
                }
            }
			$db->commit();
			return "PT Event shipped!";
		} catch (Exception $e) {
			$db->rollBack();
			error_log($e->getMessage());
			error_log($e->getTraceAsString());
			return "Unable to ship. Please try again later or contact system admin for help";		
		}
	}
	
	public function getAllDistributionReports($parameters) {
        $distributionDb = new Application_Model_DbTable_Distribution();
		return $distributionDb->getAllDistributionReports($parameters);
	}

	public function getAllDistributionStatus() {
        $distributionDb = new Application_Model_DbTable_Distribution();
		return $distributionDb->getAllDistributionStatusDetails();
	}

    public function removeDistribution($did) {
        try {
            $distributionDb = new Application_Model_DbTable_Distribution();
            $distributionDb->delete('distribution_id=' . $did);
            return "";
        } catch (Exception $e) {
            return($e->getMessage());
        }
    }
}

