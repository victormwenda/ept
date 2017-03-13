<?php

class PrivacyPolicyController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_helper->layout()->activeMenu = 'home';
        $commonServices = new Application_Service_Common();
        $this->view->banner = $commonServices->getHomeBanner();
    }


}

