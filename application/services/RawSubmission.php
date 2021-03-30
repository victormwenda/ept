<?php
class Application_Service_RawSubmission {

    public function addRawSubmission($details)
    {
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers) && isset($details)) {
                // OLD APP: $headers['User-Agent'] == 'okhttp\\/3.4.1'
                // NEW APP: $headers['User-Agent'] == 'okhttp\\/3.12.1'
                $details['headers'] = $headers;
            }
        }

        $detailsString = json_encode($details);
        error_log($detailsString, 0);
        $rawSubmissionDb = new Application_Model_DbTable_RawSubmission();
        return $rawSubmissionDb->addRawSubmission($detailsString);
    }
}
