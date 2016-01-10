<?php

require_once 'Zend/Controller/Action.php';

class Stat_RequestsController extends Zend_Controller_Action {

	
/*	public function init() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}*/
	
	
	public function showAction() {
		$model = new Stat_Model_Requests();

		$form = $model->getForm();

		$form->populate($this->getRequest()->getParams());

		$this->view->form = $form; 

		$auth = Zend_Auth::getInstance();
		$ident = $auth->getIdentity();

		$this->view->reports = $model->getResponsesReports($form->getValue('year'), $ident->STRUCTURE_CODE == 0 ? 0 : $ident->STRUCTUREID);
	}
	
	public function removeAction() {
		$model = new Stat_Model_Requests();
		
		$model->reportRemove($this->getRequest()->getParam('reportid', -1));
		
//		$this->_forward('show', 'page');
		$this->_redirect($this->getHelper('url')->url(array('module' => 'stat', 'controller' => 'page', 'action' => 'show', 'docid' => $this->getRequest()->getParam('docid')), null, true));
	}
	
	public function xmlAction() {
		$this->getHelper('layout')->disableLayout();
		$this->getHelper('viewRenderer')->setNoRender();
		
		$model = new Stat_Model_Requests();

		$auth = Zend_Auth::getInstance();
		$ident = $auth->getIdentity();
		
		$requests = $model->getResponsesReports(0, $ident->STRUCTURE_CODE == 0 ? 0 : $ident->STRUCTUREID);
		
		$dom = new DOMDocument('1.0', 'utf-8');
		$root = $dom->createElement('requestlist');
		foreach ($requests as $request) {
			if($request['UPLOADDATE']) continue; // Пропустить, если отчёт уже подан
			$publicDate = strtotime($request['PUBLICDATE']);
			if($publicDate < strtotime('-30 day')) continue; // Пропустить, если от даты публикации прошло больше 30 дней 
			$req = $dom->createElement('request');
			$req->setAttribute("id", $request['PERIODID']);
			$req->setAttribute("title", $request['REPORT'] . ' (' . $request['PERIOD'] . ')');
			$req->setAttribute("publicdate", $request['PUBLICDATE']);
			$root->appendChild($req);
		}

		$dom->appendChild($root);
		
//		$dom->formatOutput = TRUE;
		header ("Content-type: text/xml");
		echo $dom->saveXML();
	}
	
}