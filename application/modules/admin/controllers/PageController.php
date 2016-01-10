<?php

require_once 'Zend/Controller/Action.php';

class Admin_PageController extends Zend_Controller_Action {

	public function init() {
    //активируем хэлпер AjaxContext
    //и передаём туда имя action'а, который необходимо интегрировать с AJAX'ом
//		$this->_helper->AjaxContext()->addActionContext('index', 'json')->initContext('json');
	}

	public function showAction() {
		$view = $this->view;

		$view->headTitle('Administration');//$view->translate('skin.oficial_site_info').' '.$nodeInfo['TITLE']);
		$view->headLink()->appendStylesheet(array('rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => '/img/favicon.ico'));
		$view->headScript()
			->setAllowArbitraryAttributes(true)
//			->appendFile('/modules/admin/js/dojo/dojo.js', 'text/javascript', array('data-dojo-config1' => 'isDebug: true, parseOnLoad: true, extraLocale: [\'ru\']'))
//			->appendFile('/modules/admin/js/dojo/uman_dojo.js')
			->appendFile('/js/ckeditor/ckeditor.js')
//			->appendFile('/js/AjexFileManager/ajex.js');
			;

/*		$view->dojo()->setLocalPath('/js/dojo/dojo.js')
             ->addStyleSheetModule('dijit.themes.tundra')
             ->requireModule('dojo.rpc.JsonService');*/
	}
}