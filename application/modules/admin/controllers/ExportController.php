<?php

require_once 'Zend/Controller/Action.php';

class Admin_ExportController extends Zend_Controller_Action {
	
	private $_cfg;
	private $_db;
//	private $_docsPath;
	private $_sql;
	private $_zip;
	private $_rootsql;
	private $_maxid;
	
	
	public function init() {
		$this->getHelper('layout')->disableLayout();
		
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$this->_cfg = Zend_Registry::get ( 'cfg' );
//		$this->_docsPath = $this->_cfg->docs->path;

		$basesql = "select VCT.*,
iif(VCT.T_NODE_KEY = 'T_FILE_LINK',
  (select IFL.FILENAME
  from INFO_FILELINK IFL
  where IFL.NODEID = VCT.NODEID), null) as FILENAME  
from V_CONTENT_TYPE VCT 
where ";
		
		$this->_rootsql = $basesql . "VCT.NODEID = ?";
		$this->_sql = $basesql . "VCT.PARENTID = ?";
		
	}

	private function addInfo($id, &$root, $packid) {
		$info = $root->appendChild(new DOMElement('packageinfo'));
		$info->appendChild(new DOMElement('packagetype', 'UMAN Content(for UPEditor)'));
		$info->appendChild(new DOMElement('version', '1'));
		$info->appendChild(new DOMElement('packid', $packid));
		$info->appendChild(new DOMElement('createdate', date('d.m.Y H:i:s')));
		$info->appendChild(new DOMElement('packagepath', implode('\\', $this->_db->fetchCol("select TITLE from GET_TREE_PARENTS(?) order by TREELEVEL desc", $id)).'\\'));
		$info->appendChild(new DOMElement('maxid', $this->_maxid));
	}
	
	private function addTypes(&$root) {
		$types = $root->appendChild(new DOMElement('types'));
		
		$sql = 'select NODETYPEID, TITLE, SORTORDER,
VISIBLE, NODE_KEY, ICONFILENAME, MIN_OCCURS, MAX_OCCURS
from V_TYPES';
		
		$res = $this->_db->fetchAll($sql);
		foreach ($res as &$row) {
			$node = new DOMElement('type');
			$node = $types->appendChild($node);
			foreach ($row as $name => $val) {
				$node->setAttribute($name, $val);
			}
		}
		
	}
	
	private function addLevel($rows, &$parentnode, $addFiles = true) {
//		$res = $this->_db->fetchAll($this->_sql, $id);
		foreach ($rows as &$row) {
			if($row['NODEID'] > $this->_maxid) $this->_maxid = $row['NODEID'];
			$node = $parentnode->appendChild(new DOMElement($row['T_IS_FOLDER'] ? 'folder' : 'file'));
			$node->setAttribute('id', $row['NODEID']);
			$node->setAttribute('ntkey', $row['T_NODE_KEY']);
			$node->setAttribute('title', $row['TITLE']);
			if(isset($row['FILENAME'])) {
				$node->setAttribute('fname', $row['FILENAME']);
				if($addFiles)
					$this->_zip->add_file("CONTENT/{$row['NODEID']}.bin", file_get_contents(DOCS_PATH . $row['NODEID'] . '.bin'));
//				flush();
			} elseif ($row['HAS_CHILDS'] > 0) {
				$this->addLevel($this->_db->fetchAll($this->_sql, $row['NODEID']), $node, $addFiles);
			}
		}
	}
	
	public function showAction() {
		set_time_limit(600); // 10 min
//		ini_set('output_buffering', '4096');
		$nodeid = $this->getRequest()->getParam ( 'id', -1 );
		$this->_zip = new Uman_ZipStream("pack_{$nodeid}.pkg");
		$xml = new DomDocument('1.0', 'utf-8');
		$root = $xml->appendChild(new DOMElement('package'));
 		
		$content = $xml->createElement('content');
		$content = $root->appendChild($content);
		$this->addLevel($this->_db->fetchAll($this->_rootsql, $nodeid), $content, $this->getRequest()->getParam('contentinc', false));

		$packid=$this->_db->nextSequenceId('GEN_UID');
		$this->addInfo($nodeid, $root, $packid);

		
		
		$this->addTypes($root);
		
		
		$this->_zip->add_file('info.xml', $xml->saveXML());
		$this->_zip->finish();
    
		$AdminDbModel = new Admin_Model_Admin();
       
		$AdminDbModel->exportPackage($nodeid, $packid);
		
		
/*		header("Content-type: application/x-zip");
		header("Content-Disposition: attachment; filename=test.zip");
				
		$zip = new ZipArchive();
		if ($zip->open('php://output', ZIPARCHIVE::CREATE)!==TRUE) { // Пока не работает запись в поток
    	echo "cannot open \n";
    	exit;
		}
		$zip->addFromString('test.txt', '11111111111');
		$zip->close();	
*/
		
		
		exit;
	}

}