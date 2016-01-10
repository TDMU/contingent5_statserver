<?php

//require_once 'Zend/Controller/Action.php';

class Admin_QuestionsImportController extends Zend_Controller_Action {
	
//	private $_cfg;
	private $_db;
	private $_docsPath;
	
	public function init() {
		$this->getHelper('layout')->disableLayout();

		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$cfg = Zend_Registry::get ( 'cfg' );
		$this->_docsPath = $cfg['docs']['path'];
	}
	

private function parseIniFile(&$iniStr)
	{
	$aResult  =
	$aMatches = array();

	$a = &$aResult;
	$s = '\s*([[:alnum:]_\#\- \*]+?)\s*';	
	preg_match_all('#^\s*((\['.$s.'\])|(("?)'.$s.'\\5\s*=\s*("?)(.*?)\\7))\s*$#ms', $iniStr, $aMatches, PREG_SET_ORDER);

	foreach ($aMatches as $aMatch)
		{
		if (empty($aMatch[2]))
				$a [$aMatch[6]] = $aMatch[8];
		  else	$a = &$aResult [$aMatch[3]];
		}

	return $aResult;
	}
	
	
	public function showAction() {
//		set_time_limit ( 600 ); // 10 min

		$pid = $this->getRequest()->getParam('pid');
		
		if (!$pid || is_uploaded_file ( $_FILES ['questionsFile'] ['tmp_name'] ) && $_FILES ['questionsFile'] ['size'] > 0) {

//			file_put_contents('d:/temp/1.ts', str_replace("\r", '', file_get_contents($_FILES ['questionsFile'] ['tmp_name'])));
			$iniStr = preg_replace('#^\s*\#([Q9-9])\s*=\s*(.*?)\##ms', "#\$1=\"\$2\"\r\n#", iconv('cp866', 'utf-8', file_get_contents($_FILES ['questionsFile'] ['tmp_name'])));
			$iniStr = str_replace("\r\n\"", '"', $iniStr);
//			file_put_contents('d:/temp/1.ts', $iniStr);
			$questions = $this->parseIniFile($iniStr);

//			file_put_contents('d:/temp/q2a1.txt', $questions['2']['#1']);
//			print_r($questions['2']['#3']);
//			print_r($questions['2']['#1']);
//			exit;
			
			$xmlHeader = '<?xml version="1.0"?>
<!DOCTYPE content
[
<!ENTITY % ISOnum PUBLIC
"ISO 8879:1986//ENTITIES Numeric and Special Graphic//EN//XML"
"ISOnum.pen">
%ISOnum;
]>'; 
			
//			$db = Zend_Registry::get ( 'db' );
//			$cfg = Zend_Registry::get ( 'cfg' );
//			$docsPath = $cfg->docs->path;
			
			$this->_db->beginTransaction();
			
			$acl = Zend_Registry::get ( 'acl' );
			
			$sql = "select RDB\$SET_CONTEXT('USER_TRANSACTION', 'CURRENT_USERID', ?) from RDB\$DATABASE";
			$this->_db->fetchCol($sql, $acl->userid);
			
			
			try {
				foreach (array('test','import') as $mode) {
					foreach ($questions as $secname => $question) {
						if(!is_int($secname) || !isset($question['#Q']) || !isset($question['#Weight'])) continue;
						
						if($mode == 'test' && (!isset($question['#Q']) || !isset($question['#Weight'])))
							throw new Zend_Exception("Question: {$secname} #Q or #Weight not found"); 
							
						if($mode == 'import') {
							$xmlStr = $xmlHeader . '<content><question>' . $question['#Q'] . '</question>';
	
							$xmlStr .= '<answers>';
						}
							
						$AWeights = split(',', $question['#Weight']); 
			
						for($i = 1; $i <= count($AWeights); $i++) {
							if(!isset($question['#' . $i])) throw new Zend_Exception("Question: {$secname} answer {$i} not found"); 
							if($mode == 'import')	$xmlStr .= '<answer weight="' . $AWeights[$i - 1] . '">' . $question['#' . $i] . '</answer>';
						}
			
						if($mode == 'import') {
							$xmlStr .= '</answers></content>';
				
							$id = $this->_db->nextSequenceId('GEN_UID');
							$title = $id . ' ' . mb_substr($question['#Q'], 0, 30, 'UTF-8') . (mb_strlen($question['#Q'], 'UTF-8') > 30 ? ' ...' : '');
							$this->_db->query("insert into CONTENTTREE(NODEID, PARENTID, TITLE, NODETYPEID) values(?, {$pid}, ?, (select NODEID from CONTENTTREE where NODE_KEY = 'T_WTEST_Q1'))", array($id, $title));
				
							$this->_db->query('insert into INFO_QUESTION(NODEID, COMPLEXITY_LEVEL) values(?, 2)', array($id));
				
							file_put_contents($this->_docsPath . $id . '.xml', $xmlStr);
						}	
					}
				}	
			
				$this->_db->commit();

				echo 'true';
			} catch (Zend_Exception $e) {
				$this->_db->rollback();
				throw $e;
			}
			
		} else {
			echo 'Not uploaded';
		}
		
		exit;
	}

}
