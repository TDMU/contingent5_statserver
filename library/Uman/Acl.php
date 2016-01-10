<?php

class Uman_Acl extends Zend_Acl {
	
	private $db;
	
	public $userid = null;
	public $userpath = null;
	public $remoteIP = null;
	
	public $accessid = 0;

	private $rights = null;
	private $mca = null;
	
	public function __construct($userid, $userpath) {
		$this->userid = $userid;
		$this->userpath = $userpath == '' ? $userid : $userpath . ',' . $userid;
		$this->remoteIP = $_SERVER['REMOTE_ADDR'];
				
		$this->db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		$this->initAccess();
		$this->roleResource();
		$this->readRightsTable();
		$this->readMCATable();
	}
	
	private function initAccess() {
		$sql = 'select NODEID, PHP_CONDITION from INFO_ACCESS';
		$accs = $this->db->fetchAll($sql);
		
		foreach ($accs as &$acc) {
			if(preg_match($acc['PHP_CONDITION'], $this->remoteIP)) {
				$this->accessid = $acc['NODEID'];
				return;
			}
		}
	}
	
	private function roleResource() {
		$this->addRole(new Zend_Acl_Role($this->userid));
		
		$sql_allow = 'select UR.CONTENT_NODEID, UR.USER_NODEID, UR.RIGHT_NODEID, UR.ACCESS_NODEID, UR.ALLOW
from USERSRIGHTS UR
where UR.USER_NODEID = ?
and UR.ACCESS_NODEID = ? ';
		
		$result = $this->db->fetchAll($sql_allow, array($this->userid, $this->accessid));
		
		foreach($result as &$val) {
			$res = $val ['CONTENT_NODEID'].'|'.$val ['ACCESS_NODEID'].'|'.$val ['RIGHT_NODEID'];
			if(!$this->has($res)) $this->add(new Zend_Acl_Resource ($res));
			if($val['ALLOW'] != 0) {
				$this->allow($val['USER_NODEID'], $res, $val['RIGHT_NODEID']);
			} else {
				$this->deny($val['USER_NODEID'], $res, $val['RIGHT_NODEID']);
			}
		}
	}

	private function readRightsTable() {
		$sql = "select CT.NODE_KEY, iif(CT.NODE_KEY starting with 'RIGHT_', CT.PARENTID, CT.NODEID) as NODEID
from CONTENTTREE CT
where CT.PATH starting with
(
  select CTR.FULL_PATH
  from CONTENTTREE CTR
  where CTR.NODE_KEY = 'RIGHTS'
)
and CT.VISIBLE = 1";
		
		$this->rights = $this->db->fetchPairs($sql);
	}
	
	
	private function readMCATable() {
		$sql = "select CT.NODE_KEY, CT.FULL_PATH
from CONTENTTREE CT
where CT.PATH starting with
(
  select CTR.FULL_PATH
  from CONTENTTREE CTR
  where CTR.NODE_KEY = 'ALLOW_MCA'
)
and CT.VISIBLE = 1";
		
		$this->mca = $this->db->fetchPairs($sql);
	}


	public function isNodeAllowed($nodeid, $permission = RIGHT) {
		$permission = @$this->rights[$permission];
		$res = $nodeid.'|'.$this->accessid .'|'.$permission;
		return !$this->has($res) || $this->isAllowed($this->userid, $res, $permission);
	}

	public function isNodeByPathAllowed($nodeid, $permission = RIGHT) {
		$path = $this->db->fetchOne('select FULL_PATH from CONTENTTREE where NODEID = ?', $nodeid);
		return is_string($path) && $this->isPathAllowed($path, $permission);
	}

	public function isPathAllowed($resource, $permission = RIGHT) {
		$permission = @$this->rights[$permission];
		$path =  array_reverse(explode(",", $resource));
		foreach($path as &$nodeid) {
			$res = "$nodeid|{$this->accessid}|$permission";
			if($this->has($res)) 
				return $this->isAllowed($this->userid, $res, $permission);
		}
		return false;
	}

	public function isMCAExecuteAllowed($mca) {
		$path = @$this->mca['MCA_' . strtoupper($mca)];
		return !$path || $this->isPathAllowed($path, 'EXECUTE');
	}

	public function isMCAExists($mca) {
		return array_key_exists('MCA_' . strtoupper($mca), $this->mca);
	}
	
}