<?php

class Admin_Model_Rights {
	protected $_db = null; //Zend_Db_Adapter_Abstract
	
	public function __construct() {
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
	}
	
	public function getListAllowRightsWInherit($parentid) {
		$sql = "select VR.RIGHTID, VR.TITLE
from V_RIGHTS VR
where VR.VISIBLE = 1
and exists (
 select *
 from CONTENTTREE CT
 inner join ALLOW_NODES AN
 on AN.NODEID = CT.NODETYPEID
 and (AN.ALLOWNODEID = VR.RIGHTID)
 where CT.PATH starting with (
   select CT1.FULL_PATH  from CONTENTTREE CT1
   where CT1.NODEID = ?
 )
)";
		
		return $this->_db->fetchAll ( $sql, $parentid );
	}
	
	
	public function getRightsActions($userid, $id, $accessid, $fullRights = false) {
		if(!$fullRights) {
			$rsql = "coalesce((select list('r' || UR.RIGHT_NODEID || ':' || UR.ALLOW)
from USERSRIGHTS UR
where UR.CONTENT_NODEID = CT.NODEID
and UR.USER_NODEID = ?
and UR.ACCESS_NODEID = ?
and UR.INHERITED_RIGHT = 0
), '')";
		} else {
			$rsql = "coalesce((select list('r' || VR3.RIGHTID || ':' || (
  coalesce((select first 1 UR.ALLOW
    from  USERSRIGHTS UR
    inner join CONTENTTREE CTP
    on CTP.NODEID = UR.CONTENT_NODEID
    and ',' || CT.FULL_PATH || ',' containing ',' || CTP.NODEID || ','
    where UR.USER_NODEID = ?
    and UR.RIGHT_NODEID = VR3.RIGHTID
    and UR.ACCESS_NODEID = ?
    order by CTP.PATH desc
  ), 0))
)
from V_RIGHTS VR3
where exists (
 select *
 from CONTENTTREE CT3
 inner join ALLOW_NODES AN3
 on AN3.NODEID = CT3.NODETYPEID
 and (AN3.ALLOWNODEID = VR3.RIGHTID)
 where (CT3.NODEID = CT.NODEID)
 or (CT3.PATH starting with CT.FULL_PATH)
)
), '')";
		}
		
		$sql = "select CT.NODEID, CT.TITLE,
coalesce((select list(VR.RIGHTID)
from V_RIGHTS VR
where exists (
 select *
 from CONTENTTREE CT1
 inner join ALLOW_NODES AN
 on AN.NODEID = CT1.NODETYPEID
 and (AN.ALLOWNODEID = VR.RIGHTID)
 where (CT1.NODEID = CT.NODEID) or (CT1.PATH starting with CT.FULL_PATH)
)), '') as ALLOWRIGHTS, {$rsql} as RIGHTS
from CONTENTTREE CT
where coalesce(CT.PARENTID, 0) = ?
--CT.PARENTID = ?
order by CT.SORTORDER, CT.NODEID";
		
		return $this->_db->fetchAll ($sql, array($userid, $accessid, $id));
	}
	
	public function doRightsUpdate($id, $userid, $rightid, $accessid, $allow){
    $sql = " execute procedure SP_RIGHTSUPDATE (?, ?, ?, ?, ?)";

    $this->_db->query($sql, array($id, $userid, $rightid, $accessid, $allow));
	}
	
}