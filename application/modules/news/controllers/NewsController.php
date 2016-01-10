<?php

require_once 'Zend/Controller/Action.php';

class News_NewsController extends Zend_Controller_Action {

	public function showAction() {
		$newsModel = new News_Model_News();
		$this->view->assign('channel', $newsModel->getChannelInfo($this->_request->channel, $this->view->docid));
	}

}