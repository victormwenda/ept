<?php

class ParticipantController extends Zend_Controller_Action
{

    private $noOfItems = 10;

    public function init()
    {
    	//if(!Zend_Auth::getInstance()->hasIdentity()){
    	//	$this->_redirect('login/login');
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function dashboardAction()
    {
    	
        $authNameSpace = new Zend_Session_Namespace('Zend_Auth');
    	$this->view->authNameSpace = $authNameSpace;
    	//echo $authNameSpace->UserID; 
    	// get overview Info and pass to view 
    	$db = Zend_Db_Table_Abstract::getDefaultAdapter();
    	$stmt = $db->prepare("call SHIPMENT_OVERVIEW()");
    	$stmt->execute();
    	$this->view->rsOverview = $stmt->fetchAll();
    	
    	$stmt = $db->prepare("call SHIPMENT_CURRENT(?)");
    	$stmt->execute(array( $authNameSpace->UserID));
    	$this->view->rsShipCurr = array();// $stmt->fetchAll();
    	 
    	$stmt = $db->prepare("call SHIPMENT_DEFAULTED()");
    	$stmt->execute();
    	$this->view->rsShipDef = $stmt->fetchAll();
    	
    	$currentPage = $this->_getParam('page',1);
    	//$noOfItems = 4;
    	
    	$stmt = $db->prepare("call SHIPMENT_ALL(?,?)");
    	
    	$stmt->execute(array($this->noOfItems * $currentPage ,$this->noOfItems));
    	//`$this->view->rsShipAll = $stmt->fetchAll();

    	$pag = Zend_Paginator::factory($stmt->fetchAll());
    	$pag->setItemCountPerPage($this->noOfItems);
    	$pag->setCurrentPageNumber($currentPage);
    	$this->view->rsShipAll = $pag;

    	
    	//Zend_Debug::dump($this->view->rs);
    	//foreach($this->view->rs as $site){
    		//echo $site['SCHEME'];
    	//}
    	//echo $this->view->rs['SCHEME'];
    }

    public function reportAction()
    {
        // action body
    }

    public function userInfoAction()
    {
    	if(!$this->_request->isPost()){
        $authNameSpace = new Zend_Session_Namespace('Zend_Auth');
		$dbParticipant = new Application_Model_UsersProfile();
		$this->view->rsUser = $dbParticipant->getUserInfo($authNameSpace->UserID);
    	}
    	else{
    	//	$data = $this->_request->getParams();
    	//	 Zend_Debug::dump ($data);
    	}	  	
    }

    public function testersAction()
    {
        // action body
        // get all tester/participant for current user
    	$authNameSpace = new Zend_Session_Namespace('Zend_Auth');
    	$this->view->authNameSpace = $authNameSpace;
    	
    	$dbUsersProfile = new Application_Model_UsersProfile();
    	$this->view->rsUsersProfile = $dbUsersProfile->getUsersParticipant($authNameSpace->UserSystemID);
    	
    	
    	
    	// Zend_Debug::dump ($this->view->rsUsersProfile);
    	//die;
    }

    public function schemeAction()
    {
        // action body
    }

    public function passwordAction()
    {
        // action body
    }

    public function testereditAction()
    {
        // action body
        // Get
    	$dbParticipant = new Application_Model_UsersProfile();
    	if(!$this->_request->isPost())
    	{
    		// Display the data
    	$params = $this->getRequest()->getParams();
    	//Zend_Debug::dump($params);
    	
    	$this->view->rsParticipant = $dbParticipant->getParticipant($params['psid']);
    	//Zend_Debug::dump($this->view->rsParticipant);
    	}
    	else {
    		$data = $this->_request->getParams();
    		$dbParticipant->saveParticipant($data);
    		//Zend_Debug::dump($data);
    		//echo "data Saved"; 
    		//die;
    		$this->_forward('testers', 'Participant',null,array('msg'=>'Saved'));
    		
    	}
    	
    	
    }

    public function schemeinfoAction()
    {
        // action body
    }


}

















