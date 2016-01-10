<?php

require_once 'Zend/Controller/Action.php';

class PageController extends Zend_Controller_Action {

	public function showAction() {
		$nodeInfo = Zend_Registry::get('nodeInfo');
		$request = $this->getRequest();

		$lang = $nodeInfo['LOCALE_CODE'];
		$view = $this->view;
		$view->translate()->setLocale($lang);
		$view->assign('docid', $request->docid); // For content rendering
//		$view->assign ('ctxid', $request->ctxid);
//		$view->assign ('dbModel', new Model_Common());
		$view->assign('node_info', $nodeInfo);
		$view->assign('docsPath', $view->cfg['docs']['path']);
		$view->headTitle($view->translate('skin.oficial_site_info').' '.$nodeInfo['TITLE']);
		$view->headLink()->appendStylesheet(array(
			'rel' => 'shortcut icon',
			'type' => 'image/x-icon',
			'href' => '/img/favicon.ico'
		));
		$view->headLink()->appendStylesheet ($view->baseUrl . '/modules/site/css/cont.css');
		//$view->headScript()->appendFile($view->baseUrl . '/modules/site/js/script.js');

		$view->headMeta()->appendName('content-language', $nodeInfo['DOC_LOCALE_CODE']);
		$keywords = $nodeInfo['KEYWORDS'];
		if(isset($keywords)) $view->headMeta()->appendName('keywords', $keywords);
		$description = $nodeInfo['DESCRIPTION'];
		if(isset($description)) $view->headMeta()->appendName('description', $description);

		$model = new Model_Common();
		$view->link_items = $model->getMenuItems($nodeInfo['MAINLINKS_NODEID']);
		$view->path_items = $model->getCurrentPath($request->ctxid);
		$view->menu_languages = $model->getMenuLanguages($nodeInfo['SITE_NODEID']);
		$view->menu_items = $model->getMenuItems($nodeInfo['MENU_NODEID']);

		$view->content_gen = $request->getParam('content_gen', $nodeInfo['CONTENT_GEN']);
		$view->queryParams = $this->getRequest()->getParams();
	}

	public function searchAction() {
		$this->_forward('show', 'page', null, array('content_gen' => 'search'));
	}

}