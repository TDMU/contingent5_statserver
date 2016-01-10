<?php

class Admin_Model_Guide {
	protected $_cfg = null;
	protected $_db = null; //Zend_Db_Adapter_Abstract
	protected $_acl = null;

	public function __construct() {
		$this->_cfg = Zend_Registry::get ( 'cfg' );
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$this->_acl = Zend_Registry::get ( 'acl' );
	}

	protected function filterByRights(&$val) {
		$allow_up = $this->_acl->isPathAllowed ( $val ['FULL_PATH'], 'MODIFY' );
		if($allow_up || $val ['ALLOW_DOWN'] || $val ['NODEID'] == 0) {
			unset($val ['FULL_PATH']);
			unset($val ['ALLOW_DOWN']);
			$val ['ALLOW_MOD'] = $allow_up;
			return true;
		}
		return false;
	}

	public function checkRight($nodeid, $rightKey = 'MODIFY') {
		if(!$this->_acl->isNodeByPathAllowed ( $nodeid, $rightKey ))
		  throw new Zend_Exception('You do not have right');
	}


	public function getGuide($params) {
		$type = @$params['type'];
		$startkey = @$params['startkey'];
		$add_tbl = @$params['add_tbl'];
		$add_condition = @$params['add_cond'];
		$add_parent = @$params['add_parent'];

		$sql = 'select ' .
		($add_parent ? "CT.TITLE || ' | ' ||" : '') .
		' VCT.TITLE as "label", VCT.NODEID || \'\' /* для нормальной работы CheckedMultiSelect */ as "value"
from V_CONTENT_TYPE VCT' .
($add_tbl ? "\ninner join {$add_tbl} ATB on (ATB.NODEID = VCT.NODEID)" : '') .
($add_parent ? "\ninner join CONTENTTREE CT on (CT.NODEID = VCT.PARENTID)" : '') .
' where ' .
($type ? "(VCT.T_NODE_KEY = '{$type}')" : "(VCT.NODE_KEY starting with '{$startkey}')") .
($add_condition ? "\nand {$add_condition}" : '') .
' order by 1';

// file_put_contents('d:\\temp\\sql.txt', $sql);
		return $this->_db->fetchAll($sql);//, $type);
	}


	public function getGuideTree($id, $deep = 0, $root = true, $level = 0) {
		if(is_numeric($id)) {
			$clause = 'coalesce(CT.PARENTID, -1) = ?';
		}	else {
			$clause = $root ? 'CT.NODE_KEY = ?' : 'PARENTID = (select CT1.NODEID from CONTENTTREE CT1 where CT1.NODE_KEY = ?)';
		}

		$sql = "select CT.NODEID, CT.TITLE, CT.HAS_CHILDS, CT.FULL_PATH,
(iif(exists(select *
  from CONTENTTREE CCT
  inner join USERSRIGHTS UR
  on (UR.CONTENT_NODEID = CCT.NODEID)
  and (UR.RIGHT_NODEID = (select RCT.NODEID from CONTENTTREE RCT where RCT.NODE_KEY = 'MODIFY'))
  where UR.USER_NODEID = ?
  and (
    CCT.NODEID = CT.NODEID
    or (CCT.PATH starting with CT.FULL_PATH)
  )
  and UR.ALLOW = 1
), 1, 0)) as ALLOW_DOWN

from V_CONTENT_TYPE CT
where {$clause}
order by CT.SORTORDER";

		$items = $this->_db->fetchAll ( $sql, array($this->_acl->userid, $id) );
//		$res = array_filter ( $res, array (&$this, 'filterByRights' ) );
//		$res = array_map ( array (&$this, 'arrayByRights' ), $res );
    $res = array();
		foreach ($items as &$val) {
			$allow_up = $this->_acl->isPathAllowed ( $val ['FULL_PATH'], 'MODIFY' );
			if($allow_up || $val ['ALLOW_DOWN'] || $val ['NODEID'] == 0) {
				unset($val ['FULL_PATH']);
				unset($val ['ALLOW_DOWN']);
				$val ['ALLOW_MOD'] = $allow_up;
				array_push($res, $val);
			}
		}

		if($deep && $level < $deep) {
			foreach ($res as &$row) {
				if($row['HAS_CHILDS'])
					$row['children'] = $this->getGuideTree($row['NODEID'], $deep, $root, $level + 1);
			}
		}

		return $res;
	}


	public function getGuideItems($name, $canAll = false, $canEmpty = false, $order = 'CT.SORTORDER') {
		$sql = 'select CT.NODEID, CT.TITLE
			from V_CONTENT_TYPE CT
			where CT.VISIBLE = 1
			and CT.T_NODE_KEY = ?
			order by ' . $order;

		$res = $this->_db->fetchPairs ( $sql, $name );


		if($canAll) $res = array_merge(array(0 => 'Усі'), $res);

		return $res;
	}

	public function getAdminTreeLevel($parentid = 0, $deep = 0, $level = 0) {
		if(is_numeric($parentid)) {
			$sortField = $this->_db->fetchOne('select T_SORTFIELD from V_CONTENT_TYPE where NODEID = ? ', $parentid);
			$clause = 'CT.PARENTID = ?';
		}	else {
			$clause = 'CT.NODE_KEY = ?';
		}

		if(!$sortField) $sortField = 'SORTORDER';

if($parentid != -1) {
		$sql = "select CT.NODEID, CT.PARENTID, CT.TITLE, CT.VISIBLE, CT.NODETYPEID,
CT.T_IS_FOLDER, CT.HAS_CHILDS, CT.T_TITLE, CT.NODE_KEY, CT.T_NODE_KEY, CT.FULL_PATH,
(iif(exists(select *
  from CONTENTTREE CCT
  inner join USERSRIGHTS UR
  on (UR.CONTENT_NODEID = CCT.NODEID)
  and (UR.USER_NODEID = ?)
  and (UR.RIGHT_NODEID = (select RCT.NODEID from CONTENTTREE RCT where RCT.NODE_KEY = 'MODIFY'))
  and (UR.ACCESS_NODEID = ?)
  and (
    (UR.CONTENT_NODEID = CT.NODEID)
--  CCT.NODEID = CT.NODEID
    or (CCT.PATH starting with CT.FULL_PATH)
  )
  and UR.ALLOW = 1
), 1, 0)) as ALLOW_DOWN

from V_CONTENT_TYPE CT
where {$clause}
order by CT.{$sortField}";


		$res = $this->_db->fetchAll ( $sql, array($this->_acl->userid, $this->_acl->accessid, $parentid) );
} else {
		$sql = "select CT.NODEID, coalesce(CT.PARENTID, -1) as PARENTID, CT.TITLE, CT.VISIBLE, CT.NODETYPEID,
CT.T_IS_FOLDER, CT.HAS_CHILDS, CT.T_TITLE, CT.NODE_KEY, CT.T_NODE_KEY, CT.FULL_PATH,
(iif(exists(select *
  from CONTENTTREE CCT
  inner join USERSRIGHTS UR1
  on (UR1.CONTENT_NODEID = CCT.NODEID)
  and UR1.USER_NODEID = UR.USER_NODEID
  and (UR1.RIGHT_NODEID = UR.RIGHT_NODEID)
  and UR1.ACCESS_NODEID = UR.ACCESS_NODEID
  and UR1.ALLOW = 1
  where
    UR1.CONTENT_NODEID = CT.NODEID
    or (CCT.PATH starting with CT.FULL_PATH)
), 1, 0)) as ALLOW_DOWN

from V_CONTENT_TYPE CT
inner join USERSRIGHTS UR
on (UR.CONTENT_NODEID = CT.NODEID)
and UR.USER_NODEID = ?
and (UR.RIGHT_NODEID = (select RCT.NODEID from CONTENTTREE RCT where RCT.NODE_KEY = 'MODIFY'))
and UR.ACCESS_NODEID = ?
and UR.ALLOW = 1";

		$res = $this->_db->fetchAll ( $sql, array($this->_acl->userid, $this->_acl->accessid) );
}

		$res = array_filter ( $res, array (&$this, 'filterByRights' ) );

//		if($deep && $level < $deep) {
//			foreach ($res as &$row)
//				$row['children'] = $this->getAdminTreeLevel($row['NODEID'], $deep, $level + 1);
//		}
		return $res;
	}



/*
	private function full_del_dir ($directory) {
		if(!file_exists($directory)) return;
		$dir = opendir($directory);
		while(($file = readdir($dir))) {
			if ( is_file ($directory."/".$file)) {
				unlink ($directory."/".$file);
			} else if ( is_dir ($directory."/".$file) &&
       	     ($file != ".") && ($file != "..")) {
				full_del_dir ($directory."/".$file);
			}
		}

		closedir ($dir);
		rmdir ($directory);
	}


	public function delNode($id) {
		$this->checkRight($id);

		$this->_db->beginTransaction();
		try {
			if($this->_db->fetchOne('select count(*) from CONTENTTREE where PARENTID = ?', $id) > 0)
				throw new Zend_Exception('This node has childs and can not be deleted');
			$this->_db->query('delete from CONTENTTREE where NODEID = ?', $id);
			$this->full_del_dir($this->_cfg->docs->path . $id);
			@unlink ($this->_cfg->docs->path ."/".$id.'.xml');
			@unlink ($this->_cfg->docs->path ."/".$id.'.bin');
			$this->_db->commit();
		} catch (Zend_Exception $e) {
			$this->_db->rollback();
			throw $e;
		}
	}
*/


}