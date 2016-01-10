<?php

require_once 'Zend/Controller/Action.php';

class SearchController extends Zend_Controller_Action {

	public function showAction() {
		$this->_forward('show', 'page', null, array('content_gen' => 'search'));
	}

}