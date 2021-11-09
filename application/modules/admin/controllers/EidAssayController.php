<?php
class Admin_EidAssayController extends Zend_Controller_Action{
    public function init(){
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')
            ->addActionContext('change-status', 'html')
            ->initContext();
        $this->_helper->layout()->pageName = 'configMenu';
    }
    public function indexAction(){
        if (!$this->getRequest()->isPost()) {
            $this->view->source = "";
            if($this->_hasParam('fromSource')){
                $this->view->source = $this->_getParam('fromSource');
            }
        }
    }

    public function addAction(){
        if (!$this->getRequest()->isPost()) {
            $this->view->source = "";
            if($this->_hasParam('source')){
                $this->view->source = $this->_getParam('source');
            }
        }
    }

    public function editAction(){
        $this->_redirect("/admin/eid-assay");
    }

    public function changeStatusAction(){
    }
}