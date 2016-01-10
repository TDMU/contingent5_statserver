<?php

require_once 'Zend/Controller/Action.php';

class TypecontentController extends Zend_Controller_Action {

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
		$xsl = APPLICATION_PATH . 'modules/site/controllers/xml2html.xsl';
		$doc = new DOMDocument();
		$doc->substituteEntities = TRUE;
		$doc->load($xsl);
		$proc = new XsltProcessor();
		$proc->importStylesheet($doc);

		@$doc->load($xml);

		$proc->setParameter('', 'contextPath', '/');
		$proc->setParameter('', 'nodeResPath', '/res/' . $this->view->docid . '/');
		$proc->registerPHPFunctions('TypecontentController::widget');
		echo $proc->transformToXml($doc);
	}

	private function getFileSize($fileName) {
		$cfg = Zend_Registry::get('cfg');
		$res = $cfg['docs']['path'].$fileName;

		return Uman_Utils::getFileSize($res);
/*		$size = @filesize($res);
    if ($size > 104857.6) {
    	$size = round($size / 10485.76) / 100 . " Mb";
    } elseif ($size > 100) {
    	$size = round($size / 1024) . " Kb";
    } else $size = $size + " B";

		return $size;*/
	}

	public function contentAction() {
		$model = new Model_Common();
		$items = $model->getContentMenu($this->view->docid);

		foreach ($items as &$item) {
			if ($item ['IS_FOLDER']) {
				if ($item ['HAS_CHILDS']) {
					$img = 'f_full.png';
				} else {
					$img = 'f_empty.png';
				}
			} else {
				if ($item ['FILETYPE_ICONFILENAME'] != null) {
					$img = $item ['FILETYPE_ICONFILENAME'];
				} else {
					$img = 'unknown.png';
				}
			}

			$size = '';

			switch ($item ['T_NODE_KEY']) {
				case 'T_FILE_LINK' :
					$href = "/docs/{$item ['NODEID']}/{$item ['FILENAME']}";
					$size = '&#160;('.$this->getFileSize("{$item ['NODEID']}.bin").')';
					break;
				case 'T_REDIRECT' :
					$href = $item ['REDIRECT_URI'];
					break;
				default :
					$href = "{$item ['NODEID']}";
			}

			$item['img'] = $img;
			$item['href'] = $href;
			$item['size'] = $size;
		}

		$this->view->items = $items;
	}

	public function mapAction() {
		$model = new Model_Common();
		$nodeInfo = Zend_Registry::get('nodeInfo');
		$map = $model->getMap($nodeInfo['LANGUAGE_NODEID']);
		$this->view->assign ('map', $map);
	}

	public function searchAction() {
		Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive());

//		$request = Zend_Controller_Front::getInstance()->getRequest();
		$cfg = Zend_Registry::get('cfg');
		$indexPath = $cfg['search']['indexpath'];
//		$pagesize = $this->getRequest()->getParam('pagesize', $cfg['search']['pagesize']);

		$hits = array ();
		$queryStr = trim($this->getRequest()->getParam('query'));

		$index = Zend_Search_Lucene::open ($indexPath);
		if (strlen($queryStr) > 0) {
			$index = Zend_Search_Lucene::open ($indexPath);
			$userQuery = Zend_Search_Lucene_Search_QueryParser::parse($queryStr);
			$query = new Zend_Search_Lucene_Search_Query_Boolean();
			$query->addSubquery ($userQuery, true);

			try {
				$hits = $index->find ($query);
			} catch (Zend_Search_Lucene_Exception $ex) {
				$hits = array ();
			}
		}

		$view = $this->view;
//		$view->assign('hits', $hits);
		$view->assign('query', $queryStr);
//		$view->assign('pagesize', $pagesize);

		$paginator = Zend_Paginator::factory($hits);
		//new Zend_Paginator(new Zend_Paginator_Adapter_Array($hits));

		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 0));
		$paginator->setItemCountPerPage($this->getRequest()->getParam('pagesize', $cfg['search']['pagesize']));

		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination_search_control.phtml');


		$view->assign('paginator', $paginator);
		$view->queryParams = $this->getRequest()->getParams();

/*		$req = Zend_Controller_Front::getInstance()->getRequest();
		$paginator->setCurrentPageNumber($req->getParam('page', 0));
		$paginator->setItemCountPerPage($req->getParam('itemsperpage', $items_on_page));*/

//		return $paginator;

	}

	public function hotlineemailAction() {
//		$this->view->setScriptPath();
		$this->_helper->viewRenderer->setNoRender();
		echo $this->view->action('show', 'send', 'mail');//, $params);
	}

	public function forumsAction() {
//		echo $this->view->action('showforums', 'forum', 'forum', array('showTitle'=> true, 'showPath' => true));
	}

	public function forumAction() {
//		echo $this->view->action('showthemes', 'forum', 'forum', array('showTitle'=> true, 'showPath' => true));
	}

	public function forummessagesAction() {
//		echo $this->view->action('showmessages', 'forum', 'forum', array('showTitle'=> true, 'showPath' => true));
	}

}