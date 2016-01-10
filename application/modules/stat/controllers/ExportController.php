<?php

require_once 'Zend/Controller/Action.php';

class Stat_ExportController extends Zend_Controller_Action {
	
	public function init() {
		$this->getHelper('layout')->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function exportAction() {
		set_time_limit ( 120 );
		$model = new Stat_Model_Export();
		$model->export($this->getRequest()->getParams());
	}
	
}