<?php

require_once 'Zend/Controller/Action.php';

class Stat_TypecontentController extends Zend_Controller_Action {

	public function init() {
//		$this->getHelper('layout')->disableLayout();
//		$this->_helper->layout->disableLayout();
	}

	public static function widget($paramsStr) {
		$view = Zend_Layout::getMvcInstance()->getView();
		$params = array();
		foreach(explode(';', $paramsStr) as $param) {
  		list($key, $value) = explode('=', $param, 2);
  		$params[trim($key)] = trim($value);
		}
		$mca = explode('/', $params['widget']);
		$mca = array_pad($mca, 3, null);
		return $view->action($mca[2], $mca[1], $mca[0], $params);
	}

	public function xhtmlAction() {
		$this->_helper->viewRenderer->setNoRender();

		$xml = DOCS_PATH . $this->view->docid . '.xml';
		$xsl = APPLICATION_PATH . 'modules/stat/controllers/xml2html.xsl';
		$doc = new DOMDocument();
		$doc->substituteEntities = TRUE;
		$doc->load($xsl);
		$proc = new XsltProcessor();
		$proc->importStylesheet($doc);

		@$doc->load($xml);

		$proc->setParameter('', 'contextPath', '/');
		$proc->setParameter('', 'nodeResPath', '/res/' . $this->view->docid . '/');
		$proc->registerPHPFunctions('Stat_TypecontentController::widget');
		echo $proc->transformToXml($doc);
	}

}