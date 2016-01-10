<?php

require_once 'Zend/Controller/Action.php';

class Admin_ImportController extends Zend_Controller_Action {
	
	private $_acl;
	private $_db;
	private $_docsPath;
	private $_sql;
	private $_zip;
	private $_rootsql;
	private $_maxid;
	private $db_status_import;
	private $infoxmlfile;
	private $packagetype;
	private $version;
	private $log;
	
	public function init() {
		$this->getHelper('layout')->disableLayout();
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$this->_acl = Zend_Registry::get ( 'acl' );
		$this->_docsPath = DOCS_PATH;
		$this->db_status_import = 'I';
		$this->infoxmlfile = 'info.xml';
		$this->packagetype = 'UMAN Content(for UPEditor)';
		$this->version = '1';
	}
	
	private function importLevel($zip, $children, $parentid = null) {
		foreach ( $children as $child ) {
			$rule = $child ['rule'];
			switch ($rule) {
				case 'mod' :
					if ($child ['rule'] != '') {
						$this->log .= "act: $rule node: " . $child ['title'] . "\n";
					}
					
					$ntid = $this->_db->fetchOne ( 'select NODEID from CONTENTTREE where NODE_KEY = ?', $child ['ntkey'] );
					if (strcmp ( $child->getName (), 'file' ) === 0) {
						$fp = $zip->getStream ( "CONTENT/" . $child ['id'] . ".bin" );
						if (! $fp) {
							
							$this->log .= "File '" . $child ['fname'] . "' not mod \n";
							break;
						}

						$this->log .= "Mod file '" . DOCS_PATH . $child ['id'] . ".bin' \n";
						$handle = fopen ( DOCS_PATH . $child ['id'] . ".bin", "wb" );
						while ( ! feof ( $fp ) ) {
							fwrite ( $handle, fread ( $fp, 2 ) );
						}
						fclose ( $handle );
						
						$sql = ' execute procedure SP_MODIFY_FILELINK (?,?,?,null,1,?,null,null,?)';
						$this->_db->query ( $sql, array ($child ['id'], $parentid, $child ['title'], $ntid, $child ['fname'] ) );
						break;
					} else {
						$this->log .= "Mod folder '" . $this->_docsPath . $child ['id'] . "' \n";
						$sql = 'execute procedure SP_MODIFY_FOLDER (?,?,?,null,1,?,null,null,null)';
						$this->_db->query ( $sql, array ($child ['id'], $parentid, $child ['title'], $ntid, $child ['fname'] ) );
						break;
					}
				
				case 'add' :
					if ($child ['rule'] != '') {
						$this->log .= "act: $rule node: " . $child ['title'] . "\n";
					}
					
					if (! $parentid)
						throw new Zend_Exception ( 'Error adding node.' );
						
					$id = $this->_db->nextSequenceId ( 'GEN_UID' );
					$ntid = $this->_db->fetchOne ( 'select NODEID from CONTENTTREE where NODE_KEY = ?', $child ['ntkey'] );
					if (strcmp ( $child->getName (), 'file' ) === 0) {
						$fp = $zip->getStream ( "CONTENT/" . $child ['id'] . ".bin" );
						if (! $fp) {
							$this->log .= "File '" . $child ['fname'] . "' not add \n";
							break;
						}
						
						$this->log .= "Add file '" . $this->_docsPath . $id . ".bin' \n";
						$handle = fopen ( $this->_docsPath . $id . ".bin", "wb" );
						while ( ! feof ( $fp ) ) {
							fwrite ( $handle, fread ( $fp, 2 ) );
						}
						fclose ( $handle );
					
						$sql = ' execute procedure SP_MODIFY_FILELINK (?,?,?,null,1,?,null,null,?)';
						$this->_db->query ( $sql, array ($id, $parentid, $child ['title'], $ntid, $child ['fname'] ) );
						$child ['id'] = $id;
						break;
					
					} else {
						$this->log .= "Add folder '" . $this->_docsPath . $id . "' \n";
						$sql = 'execute procedure SP_MODIFY_FOLDER (?,?,?,null,1,?,null,null,null)';
						$this->_db->query ( $sql, array ($id, $parentid, $child ['title'], $ntid, $child ['fname'] ) );
						$child ['id'] = $id;
						break;
					}
			}
			
			if (strcmp ( $child->getName (), 'folder' ) === 0)
				$this->importLevel ( $zip, $child->children (), $child ['id'] );
		}
	
	}
	
	private function ImportLevelDel($zip, $children, $parentid = null) {
		
		foreach ( $children as $child ) {
			$rule = $child ['rule'];
			switch ($rule) {
				case 'del' :
					if ($child ['rule'] != '') {
						$this->log .= "act: $rule node: " . $child ['title'] . "\n";
					}
					
					$sql = 'select C.NODEID from CONTENTTREE C
					       where C.FULL_PATH starting with (select CN.FULL_PATH
					        from CONTENTTREE CN where CN.NODEID = ?)
					         order by C.PATH desc';
					$nodes_for_del = $this->_db->fetchAll ( $sql, array ($child ['id'] ) );
					foreach ( $nodes_for_del as $row ) {
						$sql = 'delete from CONTENTTREE where NODEID = ?';
						$this->_db->query ( $sql, array ($row ['NODEID'] ) );
						if (file_exists ( $this->_docsPath . $row ['NODEID'] . ".bin" )) {
							if (unlink ( $this->_docsPath . $row ['NODEID'] . ".bin" )) {
								$this->log .= "Del file '" . $this->_docsPath . $row ['NODEID'] . ".bin' \n";
							} else {
								$this->log .= "File '" . $child ['fname'] . "' not del \n";
							}
						}

						if (is_dir ( $this->_docsPath . $row ['NODEID'] )) {
							
							if ($this->dir_erase ( $this->_docsPath . $row ['NODEID'] )) {
								$this->log .= "Del folder '" . $this->_docsPath . $row ['NODEID'] . "' \n";
							} else {
								$this->log .= "Folder '" . $child ['title'] . "' not del \n";
							}
						}
					}
					
					break;
			}
			
			if (strcmp ( $child->getName (), 'folder' ) === 0)
				$this->ImportLevelDel ( $zip, $child->children (), $child ['id'] );
		}
	
	}
	
	private function dir_erase($dirname) {
		$list = scandir ( $dirname );
		foreach ( $list as $item => $name ) {
			if ($name !== "." and $name !== "..") {
				if (is_dir ( "$dirname/$name" ))
					dir_erase ( "$dirname/$name" );
				else
					unlink ( "$dirname/$name" );
			}
		}
		rmdir ( $dirname );
	}
	
	private function import($zip) {
		if (! ($xmlfile = $zip->getFromName ( $this->infoxmlfile ))) {
			echo "file '" . $this->infoxmlfile . "' in the package not found";
			exit;
		} else {
			$xml = new SimpleXMLElement ( $xmlfile );
		}

		$time_start = microtime ( 1 );
		$this->log = "Start log....\n";
		
		if (strcmp ( $this->packagetype, ( string ) $xml->packageinfo->packagetype ) != 0) {
			echo "Package type error.\n Expected: '" . $this->packagetype . "', found: '" . ( string ) $xml->packageinfo->packagetype . "'.";
			exit;
		} else {
			$this->log .= "Package type: " . ( string ) $xml->packageinfo->packagetype . "\n";
		}
		
		if (strcmp ( $this->version, ( string ) $xml->packageinfo->version ) != 0) {
			echo "Package version error.\n Expected: '" . $this->version . "', found: '" . ( string ) $xml->packageinfo->version . "'.";
			exit;
		} else {
			$this->log .= "Package version: " . ( string ) $xml->packageinfo->version . "\n";
		}
		
		if (strcmp ( "", ( string ) $xml->packageinfo->finalization ) === 0) {
			echo "Package is not finalized";
			exit;
		}
		
		$packid = ( string ) $xml->packageinfo->packid;
		
		$sql = "select P.STATUS, P.PACKAGEID, CT.TITLE, P.EXPORTDATE, 
		P.IMPORTDATE, IIU.USERNAME as IMPORTUSERNAME, 
		EIU.USERNAME as EXPORTUSERNAME from PACKAGES P 
		left join CONTENTTREE CT   
		on (CT.NODEID = P.NODEID) 
		left join INFO_USERS IIU   
		on (IIU.NODEID = P.IMPORTUSERID) 
		left join INFO_USERS EIU   
		on (EIU.NODEID = P.EXPORTUSERID) 
		where P.PACKAGEID = ?";
		$res = $this->_db->fetchRow($sql, $packid);
		if(!$res) {
			echo "Export of package not found. Package id = " . $packid;
			exit;
		}

		if(trim($res['STATUS']) == $this->db_status_import) {
			echo "Package was imported. 
			Information: \n 
			export from: " . $res['EXPORTDATE'] . " By: '" . $res['EXPORTUSERNAME'] . "'\n. 
			Import from: " . $res['IMPORTDATE'] . " 
			By: '" . $res['IMPORTUSERNAME'] . "'\n for node title: " . $res['TITLE'] . "\n. Package ID: " . $res['PACKAGEID'];
			exit;
		} else {
			
			$this->log .= " Information: \n export from: " . $res['EXPORTDATE'] . " By: '" . $res['EXPORTUSERNAME'] . "'.\n for node title: " . $res['TITLE'] . "\n";
		
		}

		$this->_db->beginTransaction ();
		
		$sql = "select RDB\$SET_CONTEXT('USER_TRANSACTION', 'CURRENT_USERID', ?) from RDB\$DATABASE";
		$this->_db->fetchCol($sql, $this->_acl->userid);
		
		
		$this->log .= "------------------------- begin\n";
		
		$this->importLevel ( $zip, $xml->content->children () );
		$this->ImportLevelDel ( $zip, $xml->content->children () );
		

		$this->log .= "------------------------- end\n";
		
		$time_end = microtime ( 1 );
		$time = $time_end - $time_start;
		$this->log .= "-> Package load: " . $time . " sec";
		
		//		
		$AdminDbModel = new Admin_Model_Admin ( );
		$AdminDbModel->importPackage($packid, (string)$xml->packageinfo->guid, $this->log, $this->db_status_import);
		$this->_db->commit();
		echo $this->log;
	}
	
	public function showAction() {
		set_time_limit ( 20000 ); // 333 min

		if (is_uploaded_file ( $_FILES ['packFile'] ['tmp_name'] ) && $_FILES ['packFile'] ['size'] > 0) {
			$zip = new ZipArchive ( );
			if ($zip->open ( $_FILES ['packFile'] ['tmp_name'] ) !== TRUE) {
				echo "cannot open \n";
				exit;
			}
			$this->import ( $zip );
			//			unlink($_FILES['packFile']['tmp_name']); //denied
			$zip->close ();
		} else {
			echo 'Not uploaded. tmp="' . $_FILES ['packFile'] ['tmp_name'] . '"';
		}
		
		//		echo 'true';
		exit;
	}

}