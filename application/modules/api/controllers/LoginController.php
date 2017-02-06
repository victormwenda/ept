<?php

class Api_LoginController extends Zend_Controller_Action {
    public function init() {
        $this->_helper->layout()->setLayout('api');
    }

    public function indexAction() {
        if ($this->getRequest()->isPost()) {
            $params = Zend_Json::decode($this->getRequest()->getRawBody());
            error_log(serialize($params), 0);
            $params['username'] = trim($params['username']);
            $params['password'] = trim($params['password']);
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $adapter = new Zend_Auth_Adapter_DbTable($db, "data_manager", "primary_email", "password");
            $adapter->setIdentity($params['username']);
            $adapter->setCredential($params['password']);
            $select = $adapter->getDbSelect();
            $select->where('status = "active"');

            // STEP 2 : Let's Authenticate
            $auth = Zend_Auth::getInstance();
            $res = $auth->authenticate($adapter); // -- METHOD 2 to authenticate , seems to work fine for me

            if ($res->isValid()){
                Zend_Session::rememberMe(60 * 60 * 5); // asking the session to be active for 5 hours
                $rs = $adapter->getResultRowObject();
                $authNameSpace = new Zend_Session_Namespace('datamanagers');
                $authNameSpace->UserID = $params['username'];
                $authNameSpace->dm_id = $rs->dm_id;
                $authNameSpace->first_name = $rs->first_name;
                $authNameSpace->last_name = $rs->last_name;
                $authNameSpace->phone = $rs->phone;
                $authNameSpace->email = $rs->primary_email;
                $authNameSpace->qc_access = $rs->qc_access;
                $authNameSpace->view_only_access = $rs->view_only_access;
                $authNameSpace->enable_adding_test_response_date = $rs->enable_adding_test_response_date;
                $authNameSpace->enable_choosing_mode_of_receipt = $rs->enable_choosing_mode_of_receipt;
                $authNameSpace->force_password_reset = $rs->force_password_reset;
                $userService = new Application_Service_DataManagers();
                $userService->updateLastLogin($rs->dm_id);

                $this->getResponse()->setBody('Signed in as '.$authNameSpace->first_name.' '.$authNameSpace->last_name);
                $this->getResponse()->setHttpResponseCode(200);
            } else {
                Zend_Auth::getInstance()->clearIdentity();
                $this->getResponse()->setBody('Sorry. Unable to sign you in. Please check your credentials');
                $this->getResponse()->setHttpResponseCode(401);
                Zend_Session::namespaceUnset('datamanagers');
            }
        }
    }
}



