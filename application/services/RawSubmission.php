<?php
class Application_Service_RawSubmission {

    public function addRawSubmission($details)
    {
        $detailsString = json_encode($details);
        error_log($detailsString, 0);
        $rawSubmissionDb = new Application_Model_DbTable_RawSubmission();
        return $rawSubmissionDb->addRawSubmission($detailsString);
    }
}
