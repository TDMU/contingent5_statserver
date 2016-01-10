<?php

require_once 'Zend/Controller/Action.php';

class Auth_PageController extends Zend_Controller_Action {

	public function loginAction() {
		$this->view->translate()->setLocale(isset($_GET['locale']) ? $_GET['locale'] : 'ru');
		$this->view->resource = $this->_request->getParam('resource');
		
		$this->view->headTitle($this->view->translate('Login page'));
		$this->view->headLink()->appendStylesheet(array('rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => '/img/favicon.ico'));
		$this->view->headLink()->appendStylesheet ('/modules/auth/css/login.css');

		if($this->_request->isPost()) {
//			file_put_contents('d:\\temp\\auth.txt', var_export($this->_request->getParams(), true));
			$filter = new Zend_Filter_StripTags();
			$username = $filter->filter($this->_request->getParam('username'));
			$password = $filter->filter($this->_request->getParam('password'));
			$woredir = $this->_request->getParam('woredir');
			if($woredir) {
				$this->getHelper('viewRenderer')->setNoRender();
				$this->getHelper('layout')->disableLayout();
			} 
			if (empty($username)) {
				$this->_response->setHttpResponseCode(401); // Unauthorized
				if($woredir) echo 'Please, provide a username.';
				else 	$this->view->message = 'Please, provide a username.';//$this->view->translate('Please provide a username.');
			} else {
				Zend_Session::start();
				if(Uman_Auth::login($username, $password)) {
					Zend_Session::rememberMe();
					$auth = Zend_Auth::getInstance();
					$identity = $auth->getIdentity();
					
					$ns = new Zend_Session_Namespace('acl');
					$ns->acl = new Uman_Acl($identity->NODEID, $identity->PATH);;
					
					if($woredir) echo 'OK';
					else $this->_redirect($this->_request->getParam('resource', '/'));
				} else {
					$this->_response->setHttpResponseCode(401); // Unauthorized
					Zend_Session::destroy();
					if($woredir) echo 'Authorization error. Please, try again.';
					else $this->view->message = $this->view->translate('Authorization error. Please, try again.');
				}
			}	
		}	else {
			if(Zend_Session::sessionExists()) {
				Zend_Session::start();
				Zend_Session::destroy();
			}
		}
	}

/*    function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }*/
	
    function showAction() {
        $this->_forward('login');
    }
	
}