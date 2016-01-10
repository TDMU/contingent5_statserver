<?php

class Admin_GuideController extends Zend_Controller_Action {
	
	public function init() {
		$this->getHelper('layout')->disableLayout();
	}
	
	//Для отображения справочников в редакторе
	public function showAction() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		$dbModel = new Admin_Model_Guide ( );
		$data = $dbModel->getGuide($this->getRequest()->getParams());
		$data = new Zend_Dojo_Data('value', $data, 'label');//, 'TITLE');
		$this->_helper->autoCompleteDojo($data); 
	}

	//Для отображения дерева юзеров в настройке прав
	public function guidetreeAction() { 
		$dbModel = new Admin_Model_Guide ( );
		$id = $this->getRequest()->getParam('id', -1);
		$deep = $this->getRequest()->getParam('deep', 0);
		$root = $this->getRequest()->getParam('root', true);
		$items = $dbModel->getGuideTree($id, $deep, $root);
		$data = new Zend_Dojo_Data('NODEID', $items, 'TITLE');
		$this->_helper->autoCompleteDojo($data);
	}
	
	//Для отображения уровня в дереве контента
	public function treelevelAction() {
		$dbModel = new Admin_Model_Guide ( );
//		$action = $this->getRequest()->getParam('act');
		$id = $this->getRequest()->getParam('id', -1);
		$deep = $this->getRequest()->getParam('deep', 0);
		$items = $dbModel->getAdminTreeLevel($id, $deep);
		echo $this->_helper->json($items);
		exit;
	}
	
}