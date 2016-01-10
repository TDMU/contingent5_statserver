<?php

/**
 * {0}
 *
 * @author alex
 * @version
 */

class Admin_Model_Admin {
	protected $_cfg = null;
	protected $_db = null; //Zend_Db_Adapter_Abstract
	protected $_acl = null;

	public function __construct() {
		$this->_cfg = Zend_Registry::get ( 'cfg' );
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$this->_acl = Zend_Registry::get ( 'acl' );
	}

	protected function filterByRights(&$val) {
		$allow_up = $this->_acl->isPathAllowed($val ['FULL_PATH']);
		if($allow_up || $val ['ALLOW_DOWN'] || $val ['NODEID'] == 0) {
			unset($val ['FULL_PATH']);
			unset($val ['ALLOW_DOWN']);
			$val ['ALLOW_MOD'] = $allow_up;
			return true;
		}
		return false;
	}

	public function checkRight($nodeid) { //, $rightKey) {
		if(!$this->_acl->isNodeByPathAllowed($nodeid))//, $rightKey))
		  throw new Zend_Exception('You do not have right');
	}

	public function getNodeinfo($id) {
		$sql = 'select VCT.NODEID, VCT.TITLE, VCT.T_TITLE
from V_CONTENT_TYPE VCT
where VCT.NODEID = ?';

		return $this->_db->fetchRow($sql, $id);
	}

	public function getAllowedTypes($id, $nodetypeid = null, $mode = 'ADD') {
		$ntclause = $nodetypeid ? "and AL.ALLOWNODEID = $nodetypeid" : '';
		$modeclause = $mode == 'ADD' ? '>' : '>=';

		$sql = "select
    AL.ALLOWNODEID as NTID,
    CT.TITLE
from ALLOW_NODES AL
inner join CONTENTTREE CT
on (CT.NODEID = AL.ALLOWNODEID)
inner join V_TYPES VT
on (VT.NODETYPEID = CT.NODEID)
where AL.NODEID = (
  select CT1.NODETYPEID
  from CONTENTTREE CT1
  where CT1.NODEID = ?
)
and (
    (VT.MAX_OCCURS is null)
    or
    (VT.MAX_OCCURS $modeclause (
        select count (VCT.NODEID)
        from V_CONTENT_TYPE VCT
        where VCT.PARENTID = ?
        and VCT.NODETYPEID = VT.NODETYPEID
      )
    )
  )
$ntclause
order by CT.SORTORDER";
//		file_put_contents('d:\\temp\\sql.txt', $sql);
		return $this->_db->fetchAll($sql, array($id, $id));
	}

	public function getAllowUpdateActions($id) {
		$sql = ' select distinct VT.NODETYPEID, VA.TITLE, VT.TITLE as TYPETITLE,
coalesce(VA.ICONFILENAME, VT.ICONFILENAME) as ICONFILENAME, VT.OUTPUT_TARGET, VA.FUNC, VA.DOC_TARGET
from CONTENTTREE CT
inner join ALLOW_NODES AN
  on (AN.NODEID = CT.NODETYPEID)
inner join V_TYPES VT
  on (VT.NODETYPEID = AN.ALLOWNODEID)
inner join ALLOW_NODES AN1
  on (AN1.NODEID = VT.NODETYPEID)
inner join V_ACTIONS VA
  on (VA.NODEID = AN1.ALLOWNODEID)
where
  CT.NODEID = ?
and (VA.ACTION_TYPE = \'I\')
and (
    (VT.MAX_OCCURS is null)
    or
    (VT.MAX_OCCURS > (
        select count (VCT.NODEID)
        from V_CONTENT_TYPE VCT
        where VCT.PARENTID = CT.NODEID
        and VCT.NODETYPEID = VT.NODETYPEID
      )
    )
  )
order by VA.SORTORDER';

		$res = $this->_db->fetchAll($sql, $id);

		foreach ($res as &$row) { // Temporary
			$row['FUNC'] = str_replace('#NODEID#', $id, $row['FUNC']);
			$row['FUNC'] = str_replace('#NODETYPEID#', $row['NODETYPEID'], $row['FUNC']);
		}

		return $res;
	}

	public function getNodeEditorInfo($ntid) {
		$sql = "select CT.TITLE, IT.DEFAULT_EDITOR_NODEID, IT.ISHISTORY,
(select VAL from V_ADD_VALUES VAD where VAD.NODEID = IT.DEFAULT_EDITOR_NODEID and VAD.FIELDNAME = '_ADD_VALUESFILTER') as VALUES_FILTER_CLASS,
coalesce((select VAL from V_ADD_VALUES VAD where VAD.NODEID = IT.DEFAULT_EDITOR_NODEID and VAD.FIELDNAME = '_ADD_EDITOR_FORM_URL'), 'admin/editor/edit') as EDITOR_FORM_URL
from INFO_TYPES IT
inner join CONTENTTREE CT
on (CT.NODEID = IT.DEFAULT_EDITOR_NODEID)
where IT.NODEID = ?";

		return $this->_db->fetchRow($sql, array($ntid));
	}

	public function getEditorFields($ntid) {
		$sql = "select
coalesce(IFLD.FIELDNAME, IFLD1.FIELDNAME) as FIELDNAME,
CT.NODEID, CT.TITLE, CT.VISIBLE,
coalesce(IFLD.TABLENAME, IFLD1.TABLENAME) as TABLENAME,
coalesce(IFLD.ELEMENT_CLASS, IFLD1.ELEMENT_CLASS) as ELEMENT_CLASS,
coalesce(IFLD.ELEMENT_CONFIG, IFLD1.ELEMENT_CONFIG) as ELEMENT_CONFIG,
coalesce(IFLD.REQUIRED, IFLD1.REQUIRED) as REQUIRED,
coalesce(IFLD.JS_ONCREATE, IFLD1.JS_ONCREATE) as JS_ONCREATE,
coalesce(IFLD.JS_BEFORESUBMIT, IFLD1.JS_BEFORESUBMIT) as JS_BEFORESUBMIT,
coalesce(IFLD.DEFAULT_VALUE, IFLD1.DEFAULT_VALUE) as DEFAULT_VALUE
from CONTENTTREE CT
left join INFO_FIELDS IFLD
on (IFLD.NODEID = CT.NODEID)
left join CONTENTTREE CT1
on (CT1.NODEID = IFLD.LINKED_NODEID)
left join INFO_FIELDS IFLD1
on (IFLD1.NODEID = IFLD.LINKED_NODEID)
where CT.PARENTID = ?
and CT.VISIBLE = 1
order by CT.SORTORDER";

		return $this->_db->fetchAssoc($sql, $ntid);
	}


	protected function getFieldsByTables(&$fields) {
		$tables = array('CONTENTTREE' => array());
		foreach ($fields as $fieldname => &$field) {
			$tblname = $field['TABLENAME'];
//			$fieldname = $field['FIELDNAME'];
			if(!$tblname) $tblname = '_NODB_';
			if(!isset($tables[$tblname])) $tables[$tblname] = array($fieldname);
			else array_push($tables[$tblname], $fieldname);
		}
		return $tables;
	}


	private function loadXmlFileContent($id, $fieldname, &$res) {
		$file = @file_get_contents($this->_cfg['docs']['path'] . $id . '.xml');
		$res[$fieldname] = $file;
	}

	public function getNewValues($fields, $nodeeditorInfo) {
		$res = array();

		foreach ($fields as $field) {
			if(isset($field['DEFAULT_VALUE']))
				$res[$field['FIELDNAME']] = $field['DEFAULT_VALUE'];
		}


		if(isset($nodeeditorInfo['VALUES_FILTER_CLASS'])) {
			$prepClass = 'Admin_Model_' . trim($nodeeditorInfo['VALUES_FILTER_CLASS']);
			$prepObj = new $prepClass();
			if(method_exists($prepObj, 'newValues'))
				$prepObj->newValues($res);
		}
		return $res;
	}

	public function getFieldsValues($fields, $id, $nodeeditorInfo) {
		$tables = $this->getFieldsByTables($fields);

		$res = array();

		foreach ($tables as $table => &$fields) {
			if($table == 'CONTENTTREE') $fields = array_merge(array('NODEID', 'PARENTID', 'NODETYPEID'), $fields);
			elseif(strpos($table, 'V_ADD_') === 0) $fields = array('FIELDNAME', 'VAL');
			elseif($table == '_UPLOAD_') continue;
			elseif($table == '_NODB_') {
				foreach ($fields as &$fieldname) {
					if(strpos($fieldname, '_FILE_') === 0) { // начинается с _FILE_
						$this->loadXmlFileContent($id, $fieldname, $res);
					}
				}
				continue;
			}
			$fieldsStr = implode(",", $fields);

			$sql = 'select ' . $fieldsStr . ' from ' . $table . ' where NODEID = ?';

			if(strpos($table, 'V_ADD_') === 0)
				$tblValues = $this->_db->fetchPairs($sql, $id);
			else
				$tblValues = $this->_db->fetchRow($sql, $id);

			if($tblValues) $res = array_merge($res, $tblValues);
		}

		if(isset($nodeeditorInfo['VALUES_FILTER_CLASS'])) {
			$prepClass = 'Admin_Model_' . trim($nodeeditorInfo['VALUES_FILTER_CLASS']);
			$prepObj = new $prepClass();
			if(method_exists($prepObj, 'afterLoadValues'))
				$prepObj->afterLoadValues($res);
		}

		return $res;
	}

	protected function checkAllowedNode($parentid, $nodetypeid, $mode = 'ADD') {
		$allowedTypes = $this->getAllowedTypes($parentid, $nodetypeid, $mode);
		if(!$allowedTypes)
			throw new Zend_Exception("Type {$nodetypeid} is not allowed in {$parentid}");
	}

	public function getNextId($gen = 'GEN_UID') {
		return $this->_db->nextSequenceId($gen);
	}

	public function getNextSortOrder($parentid) {
		$sql = 'select coalesce((select first 1 SORTORDER
from CONTENTTREE
where PARENTID = ?
order by SORTORDER desc), 0) + 1 from RDB$DATABASE';

		return $this->_db->fetchOne($sql, $parentid);
	}

	private function saveXmlFileContent($id, &$val) {
		$filename = $this->_cfg['docs']['path'] . $id . '.xml';
		if(strlen($val) > 0) {
			file_put_contents($filename, $val);
		} else {
			if(file_exists($filename)) unlink($filename);
		}
	}

/*	private function isChangedForHistory($fields, $values, $nodeeditorInfo) {
		$oldvalues = $this->getFieldsValues($fields, $values['NODEID'], $nodeeditorInfo);
//		file_put_contents('d:\\temp\\1old.xml', $oldvalues['_FILE_EDITOR']);
//		file_put_contents('d:\\temp\\2new.xml', $values['_FILE_EDITOR']);
		return $oldvalues['TITLE'] != $values['TITLE'] || $oldvalues['_FILE_EDITOR'] != $values['_FILE_EDITOR'];
	}*/

	private function regMod($values) {

	}

	public function saveFieldsValues($fields, $values, $nodeeditorInfo) {
		if($values['MODE'] == 'ADD') {
			$oldvalues = array();
			$oldid = $values['NODEID'];
			$id = $this->getNextId();
			$values['NODEID'] = $id;
		} else {
//			$oldvalues = $this->getFieldsValues($fields, $values['NODEID'], $nodeeditorInfo);
			$oldid = $id = $values['NODEID'];
		}

		if(array_key_exists('_FILE_EDITOR', $values))
			$values['_FILE_EDITOR'] = preg_replace("' ?ilo-full-src=\".*\"'Ui", '', $values['_FILE_EDITOR']);


//			file_put_contents("t:\\values.txt", serialize($values));

/*		$createHistoryRecord = $nodeeditorInfo['ISHISTORY'];

		if($createHistoryRecord) {
			$this->regMod($values);
			file_put_contents($this->_cfg['docs_mod']['path'] . "$id.ser", serialize($values));
			$this->copyRes($this->_cfg['temp']['path'] . $id, $this->_cfg['docs_mod']['path'] . $id);
			$this->full_del_dir($this->_cfg['temp']['path'] . $id);
			return;
		}*/




		if(isset($nodeeditorInfo['VALUES_FILTER_CLASS'])) {
			$prepClass = 'Admin_Model_' . trim($nodeeditorInfo['VALUES_FILTER_CLASS']);
			$prepObj = new $prepClass();
			if(method_exists($prepObj, 'beforeSaveValues'))
				$prepObj->beforeSaveValues($values);
		}

		$tables = $this->getFieldsByTables($fields);

		$this->_db->beginTransaction();

		$sql = "select RDB\$SET_CONTEXT('USER_TRANSACTION', 'CURRENT_USERID', ?) from RDB\$DATABASE";
		$this->_db->fetchCol($sql, $this->_acl->userid);

		try {
			foreach ($tables as $table => &$table_fields) {
				if($table == '_UPLOAD_') {
					$upload = new Zend_File_Transfer_Adapter_Http(array('ignoreNoFile' => true));
//					$upload->addValidator('Size', true, '20M');
					if(!$upload->isValid()) throw new Zend_Exception('Upload not valid<br>' .  implode('<br>', $upload->getMessages()));
					if($upload->receive() && $upload->isUploaded()) {
						$tmpFile = $upload->getFileName();
						$docFile = $this->_cfg['docs']['path'] . $values['NODEID'] . '.bin';
						if(file_exists($docFile)) unlink($docFile);
						if(!rename($tmpFile, $docFile))
							throw new Zend_Exception("File not renamed from \"{$tmpFile}\" to \" {$docFile}\"");
					} //else throw new Zend_Exception('Upload not valid<br>' .  implode('<br>', $upload->getMessages()));
				} elseif(strpos($table, 'V_ADD_') === 0) {
					$sql = 'update or insert into ' . $table . '(NODEID, FIELDNAME, VAL) values(?,?,?) matching(NODEID, FIELDNAME)';
					$table_fields_values[0] = $values['NODEID'];
					foreach ($table_fields as &$fieldname) {
						$table_fields_values[1] = $fieldname;
						$table_fields_values[2] = !empty($values[$fieldname]) ? (is_array($values[$fieldname]) ? implode(",", $values[$fieldname]) : $values[$fieldname]) : null;
						$this->_db->query($sql, $table_fields_values);
					}
				} else {
					array_push($table_fields, 'NODEID');
					if($table == 'CONTENTTREE') {
						if(in_array('PARENTID', $table_fields)) {
							if($values['MODE'] == 'ADD')// || ($oldvalues['PARENTID'] != $values['PARENTID']))
								$this->checkAllowedNode($values['PARENTID'], $values['NODETYPEID'], 'ADD'); //problem with OCCUR_MIN=1 and edit
						} else {
							$table_fields = array_merge(array('PARENTID'), $table_fields);
						}
						if(!in_array('NODETYPEID', $table_fields)) {
						  $table_fields = array_merge(array('NODETYPEID'), $table_fields);
						}
					} elseif($table == '_NODB_') {
						foreach ($table_fields as &$fieldname) {
							if(strpos($fieldname, '_FILE_') === 0) { // начинается с _FILE_
								$this->saveXmlFileContent($values['NODEID'], $values[$fieldname]);
							}
						}
						continue;
					}

					$fieldsStr = implode(",", $table_fields);
					$paramsStr = implode(",", array_fill(0, count($table_fields), '?'));

					$sql = 'update or insert into ' . $table . '(' . $fieldsStr . ') values(' . $paramsStr . ') matching(NODEID)';

					$table_fields_values = array();
					foreach ($table_fields as &$fieldname) {
						array_push($table_fields_values, $values[$fieldname] !== '' ? (is_array($values[$fieldname]) ? implode(",", $values[$fieldname]) : $values[$fieldname]) : null);
					}


//					try {
						$this->_db->query($sql, $table_fields_values);
//					} catch (Zend_Exception $e) {
//						throw new Zend_Exception($sql . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
//					}
				}
			}
			$this->_db->commit();

			if(isset($nodeeditorInfo['VALUES_FILTER_CLASS'])) {
				$prepClass = 'Admin_Model_' . trim($nodeeditorInfo['VALUES_FILTER_CLASS']);
				$prepObj = new $prepClass();
				if(method_exists($prepObj, 'afterSaveValues'))
					$prepObj->afterSaveValues($values);
			}

			$this->copyResources($oldid, $id, false);

		} catch (Zend_Exception $e) {
			$this->_db->rollback();
			throw $e;
		}
	}

	public function changeParent($id, $pid) {
		$this->checkRight($id);

		$this->_db->query('update CONTENTTREE set PARENTID = ? where NODEID = ?', array($pid, $id));
	}


	private function delNode($id) {
		$this->checkRight($id);

		$this->_db->query('delete from CONTENTTREE where NODEID = ?', $id);
		$this->full_del_dir($this->_cfg['docs']['path'] . $id);
		@unlink($this->_cfg['docs']['path'] ."/".$id.'.xml');
		@unlink($this->_cfg['docs']['path'] ."/".$id.'.bin');
	}

	public function delNodes($id, $level = 0) {
		$this->checkRight($id);

		if($level == 0) $this->_db->beginTransaction();
		try {
//			if($this->_db->fetchOne('select count(*) from CONTENTTREE where PARENTID = ?', $id) > 0)
//				throw new Zend_Exception('This node has childs and can not be deleted');
			foreach ($this->_db->fetchAll('select NODEID from CONTENTTREE where PARENTID = ? order by SORTORDER desc', $id) as $row) {
				$this->delNodes($row['NODEID'], $level + 1);
				$this->delNode($row['NODEID']);
			}
			$this->delNode($id);
			if($level == 0) $this->_db->commit();
		} catch (Zend_Exception $e) {
			if($level == 0) $this->_db->rollback();
			throw $e;
		}
	}

	public function clearNodeTemp($id) {
		$nodeTempDir = $this->_cfg['temp']['path'] . $id;
		$this->full_del_dir($nodeTempDir);
	}

	private function full_del_dir ($directory) {
		if(!file_exists($directory)) return;
		$dir = opendir($directory);
		while(($file = readdir($dir))) {
			if ( is_file ($directory."/".$file)) {
				unlink ($directory."/".$file);
			} else if ( is_dir ($directory."/".$file) &&
       	     ($file != ".") && ($file != "..")) {
				$this->full_del_dir ($directory."/".$file);
			}
		}

		closedir ($dir);
		rmdir ($directory);
	}

	private function sync_folder($srcdir, $dstdir, $forced = false) {
		if(!is_dir($dstdir)) mkdir($dstdir);

		$srcfiles = scandir($srcdir);

		foreach ($srcfiles as $file) {
			if($file == '.' || $file == '..') continue;

			$srcfile = $srcdir . '/' . $file;
			$dstfile = $dstdir . '/' . $file;

			if(!is_file($srcfile)) continue;

			if(is_file($dstfile) && filemtime($srcfile) <= filemtime($dstfile)) continue;

			if(!copy($srcfile, $dstfile))
				throw new Zend_Exception("Ошибка: Не могу скопировать файл '$srcfile'!");

			touch($dstfile, filemtime($srcfile));
			chmod($dstfile, 0770);
		}

		$dstfiles = scandir($dstdir);

		$diff = array_diff($dstfiles, $srcfiles);
		foreach ($diff as $file) {
			unlink($dstdir . '/' . $file);
		}

		return true;
	}

	public function copyRes($fromDir, $toDir) {
		if(!file_exists($fromDir)) return;
		$this->sync_folder($fromDir, $toDir);
	}

	public function copyResources($fromid, $toid, $toTemp = true) {
		if ($toTemp) {
			$docdir = $this->_cfg['docs']['path'] . $fromid;
			if (file_exists ( $docdir )) {
				$tmpdir = $this->_cfg['temp']['path'] . $toid;
				$this->sync_folder ( $docdir, $tmpdir );
			}
		} else {
			$tmpdir = $this->_cfg['temp']['path'] . $fromid;
			if (file_exists ( $tmpdir )) {
				$docdir = $this->_cfg['docs']['path'] . $toid;
				$this->sync_folder ( $tmpdir, $docdir );
				$this->full_del_dir( $tmpdir );
			}
		}
	}


	public function exportPackage($id, $packageid) {
		$sql = 'insert into PACKAGES (PACKAGEID, NODEID, EXPORTUSERID, STATUS)
values (?, ?, ?, ?)';

		$this->_db->query($sql, array($packageid, $id, $this->_acl->userid ,'E'));
	}

	public function importPackage($packageid, $guid, $log, $db_status_import) {
		$sql = 'update PACKAGES
set GUID = ?, IMPORTUSERID = ?, "LOG" = ?, STATUS = ?
where (PACKAGEID = ?)';

		$this->_db->query($sql, array($guid, $this->_acl->userid, $log, $db_status_import, $packageid));
	}

}