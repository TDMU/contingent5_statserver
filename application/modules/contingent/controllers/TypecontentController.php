<?php

require_once 'Zend/Controller/Action.php';

class Contingent_TypecontentController extends Zend_Controller_Action {

	public function init() {
//		$this->getHelper('layout')->disableLayout();
//		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}	
	
	public function xhtmlAction() {
		$xml = DOCS_PATH . $this->view->docid . '.xml';
		$xsl = APPLICATION_PATH . 'modules/contingent/controllers/xml2html.xsl';
		$doc = new DOMDocument ( );
		$doc->substituteEntities = TRUE;
		$doc->load ( $xsl );
		$proc = new XsltProcessor ( );
		$proc->importStylesheet ( $doc );
		
		$doc->load ( $xml );
		
		$proc->setParameter ( '', 'contextPath', '/' );
		$proc->setParameter ( '', 'nodeResPath', '/res/' . $this->view->docid . '/' );
		echo $proc->transformToXml ( $doc );
	}
	
	public function forumsAction() {
		echo $this->view->action('showforums', 'forum', 'forum', array('showTitle'=> true, 'showPath' => true));
	}

	public function forumAction() {
		echo $this->view->action('showthemes', 'forum', 'forum', array('showTitle'=> true, 'showPath' => true));
	}
	
	public function forummessagesAction() {
		echo $this->view->action('showmessages', 'forum', 'forum', array('showTitle'=> true, 'showPath' => true));
	}
	
}