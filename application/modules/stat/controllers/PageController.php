<?php

/**
 * IndexController - The default controller class
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class Stat_PageController extends Zend_Controller_Action {

	public function showAction() {
		set_time_limit(120);

		$nodeInfo = Zend_Registry::get('nodeInfo');

		$view = $this->view;

		Zend_Registry::set ( 'view', $view );


		$view->assign ('docid', $this->getRequest()->docid);
		$view->assign ('ctxid', $this->getRequest()->ctxid);

		$cfg = Zend_Registry::get('cfg');
//		$view->docsPath = $cfg['docs']['path'];

		$view->assign ('request', $this->_request);
		$view->assign ( 'dbModel', new Model_Common() );
		$view->assign ('node_info', $nodeInfo);
		$view->translate()->setLocale($nodeInfo['LOCALE_CODE']);

		$view->baseUrl = $this->_request->getBaseUrl ();
		$view->headTitle($view->translate('skin.oficial_site_info').' '.$nodeInfo['TITLE']);
		$view->headLink()->appendStylesheet(array('rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => '/img/favicon.ico'));
		$view->headLink ()->appendStylesheet ('/modules/stat/css/css.css');
		$view->headLink ()->appendStylesheet ('/modules/stat/css/reports.css');
		$view->headScript()->appendFile('/modules/stat/scripts/script.js');
		$keywords = $view->node_info['KEYWORDS'];
		if(isset($keywords)) $view->headMeta()->appendName('keywords', $keywords);
		
		$view->content_gen = $this->getRequest()->getParam('content_gen', $nodeInfo['CONTENT_GEN']);
		$view->queryParams = $this->getRequest()->getParams();
		
	}

}