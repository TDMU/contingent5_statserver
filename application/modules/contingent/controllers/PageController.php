<?php

require_once 'Zend/Controller/Action.php';

class Contingent_PageController extends Zend_Controller_Action {

	public function showAction() {
//		$view->layout()->setLayout('contingent');
//		Zend_Layout::setLayout('contingent');
		
		$nodeInfo = Zend_Registry::get('nodeInfo');
		$request = $this->getRequest();
		
		$view = $this->view;
		$view->translate()->setLocale($nodeInfo['LOCALE_CODE']);
		$view->assign ('docid', $request->docid);
//		$view->assign ('ctxid', $request->ctxid);
//		$view->assign ('dbModel', new Model_Common());
		$view->assign ('node_info', $nodeInfo);
		$view->assign('docsPath', $view->cfg['docs']['path']);
		$view->headTitle($view->translate('skin.oficial_site_info').' '.$nodeInfo['TITLE']);
		
		$keywords = $nodeInfo['KEYWORDS'];
		if(isset($keywords)) $view->headMeta()->appendName('keywords', $keywords);
		
		$view->headLink()->appendStylesheet('/modules/contingent/css/cont.css');
		
		$model = new Model_Common();
		$view->menu_items = $model->getMenuItems($nodeInfo['MENU_NODEID']);
		
	}
}