<?php
include_once 'CronInit.php';

$conf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

try {
    $db = Zend_Db::factory($conf->resources->db);
    Zend_Db_Table::setDefaultAdapter($db);

    $limit = '10';
    $sQuery = $db->select()
        ->from(array('tpn' => 'temp_push_notification'))
        ->where("tpn.status = ?",'pending')
        ->limit($limit);
    $pushNotificationResults = $db->fetchAll($sQuery);

    if (count($pushNotificationResults) > 0) {
        foreach ($pushNotificationResults as $pushNotificationResult) {
           /*$db->update('temp_push_notification', array('status' => 'not-sent'),
                'temp_id = ' . $pushNotificationResult['temp_id']);*/

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://exp.host/--/api/v2/push/send',
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json',
                    'accept-encoding: gzip, deflate',
                    'accept: application/json'
                ),
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query(array(array(
                    'to' => $pushNotificationResult['to'],
                    'sound' => $pushNotificationResult['sound'],
                    'title' => $pushNotificationResult['title'],
                    'body' => $pushNotificationResult['body'],
                    'data' => json_decode($pushNotificationResult['data'])
                ))),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HEADER => 1
            ));

            error_log(serialize($curl),0);

            $result = curl_exec($curl);

            curl_close($curl);

            /*if ($sendResult == true){
              $db->delete('temp_push_notification', $id);
            }*/
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage(), 0);
    error_log($e->getTraceAsString(), 0);
    error_log('whoops! Something went wrong in cron/SendMobilePushNotifications.php', 0);
}
