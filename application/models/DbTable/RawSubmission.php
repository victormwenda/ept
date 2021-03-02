<?php

class Application_Model_DbTable_RawSubmission extends Zend_Db_Table_Abstract
{
    protected $_name = 'raw_submission';
    protected $_primary = 'id';

    public function addRawSubmission($detailsString) {
        $id = 0;
        if (isset($detailsString)) {
            $data = array(
                'details'=>$detailsString,
                'created_on' => new Zend_Db_Expr('now()')
            );
            $id = $this->insert($data);
        }
        return $id;
    }
}

