<?php

class Model_Common {

	protected $_db = null; //Zend_Db_Adapter_Abstract
	protected $_acl = null;

	public function __construct() {
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
	}

	protected function filterByRights($val) {
		if(!$this->_acl) $this->_acl = Zend_Registry::get ( 'acl' );
		return $this->_acl->isPathAllowed ( $val ['FULL_PATH']);
	}

	protected function filterLinksByRights($val) {
//		if(!$this->_acl) $this->_acl = Zend_Registry::get ( 'acl' );
		return $this->_acl->isNodeAllowed ( $val ['NODEID'])
			&& (!$val ['LINK_FULL_PATH'] || $this->_acl->isPathAllowed ($val ['LINK_FULL_PATH']));
	}

	public function getMenuItems($docid, $recursive = false) {
		$this->_acl = Zend_Registry::get('acl');
		if (!isset ( $docid ) || !$this->_acl->isNodeByPathAllowed($docid)) return array ();

		$sql = "select CT.NODEID, CT.TITLE, CT.LINK_FULL_PATH, CT.HREF, CT.DOC_TARGET
from V_CONTENT_TREE CT
where CT.PARENTID = ?
and CT.VISIBLE = 1
order by CT.SORTORDER";

		$res = $this->_db->fetchAll ( $sql, array($docid));//, $this->_acl->userid, $this->_acl->accessid) );

		$res = array_filter ( $res, array (&$this, 'filterLinksByRights' ) );

		foreach ( $res as &$val ) {
			if ($recursive) {
				$child_items = $this->getMenuItems($val['NODEID'], $recursive);
				if(count($child_items) > 0) $val['childs'] = $child_items;
			}
		}

		return $res;
	}

	public function getCurrentPath($docid, $fromType = 'T_MAIN_MENU') {
		$sql_path = 'select GTP.NODEID, GTP.TITLE, GTP.HREF
from GET_TREE_PARENTS(?, ?, 1, 1) GTP
--where GTP.VISIBLE = 1
order by TREELEVEL desc';

		return $this->_db->fetchAll ( $sql_path, array ($docid, $fromType) );
	}


	public function getNodeID($nodekey) {
		$sql = 'select CT.NODEID from CONTENTTREE CT where CT.NODE_KEY = upper(?)';

		return $this->_db->fetchOne ( $sql, $nodekey);
	}

	public function getNodeParentID($nodekey) {
		$sql = 'select CT.PARENTID from CONTENTTREE CT where CT.NODE_KEY = upper(?) ';

		return $this->_db->fetchOne ( $sql, $nodekey);
	}

	public function getNodeInfo($docid) {
		$sql = 'select SGN.* from SP_GET_NODEINFO(?) SGN ';

		return $this->_db->fetchRow ( $sql, $docid );
	}


	public function getMenuLanguages($siteid) {
		$sql_menu_languages = 'select CTMM.NODEID, CT.TITLE, CT.LOCALE_CODE
			from CONTENTTREE CT
			inner join CONTENTTREE NT
			on (NT.NODEID = CT.NODETYPEID)
			and NT.NODE_KEY = \'T_LANGUAGE\'
			inner join CONTENTTREE CTMM
			on (CTMM.PARENTID = CT.NODEID)
			inner join CONTENTTREE NTMM
			on (NTMM.NODEID = CTMM.NODETYPEID)
			and NTMM.NODE_KEY = \'T_MAIN_MENU\'
			where CT.PARENTID = ?
			order by CT.SORTORDER';

		$res = $this->_db->fetchAll ( $sql_menu_languages, $siteid );

		return $res;
	}

	public function getSite($language) {
		$cfg = Zend_Registry::get ( 'cfg' );
		$sql = 'select NODEID from SP_GET_SITE_ROOT_NODEID(?, ?)';

		return $this->_db->fetchRow ( $sql, array ($cfg['www']['defaultsite'], $language ) );
	}

	public function getMap($nodeid) {
		$this->_acl = Zend_Registry::get('acl');
		if (!isset ( $nodeid ) || !$this->_acl->isNodeByPathAllowed($nodeid)) return array ();
		$sql = 'select NODEID, TITLE, NODE_LEVEL, LINK_FULL_PATH, HREF, DOC_TARGET from SP_GET_MAP(?)';

		$res = $this->_db->fetchAll ( $sql, $nodeid );
		$res = array_filter ( $res, array (&$this, 'filterLinksByRights' ) );
		return $res;
	}


	public function getContentMenu($docid) {
		if (isset ( $docid )) {
			$sql = "select CT.NODEID, CT.FULL_PATH, CT.TITLE, CT.NODE_KEY,
          CT.DOC_TARGET, CT.T_OUTPUT_TARGET, CT.T_NODE_KEY, IFL.FILENAME, iif(IFL.NODEID is null, 0, 1) as IS_FILE_LINK,
          CT1.TITLE as FILETYPE_TITLE, iif(IFL.NODEID is null, 'html.png', CT1.ICONFILENAME) as FILETYPE_ICONFILENAME,
          (select first 1 1 from CONTENTTREE CT2 where CT2.PARENTID = CT.NODEID and CT2.VISIBLE = 1) as HAS_CHILDS,
          (select URI from INFO_REDIRECT IR where IR.NODEID = CT.NODEID) as REDIRECT_URI,
          coalesce ((select first 1 1
                  from ALLOW_NODES AN
                  inner join INFO_TYPES IT
                    on (IT.NODEID = AN.ALLOWNODEID)
                  where AN.NODEID = CT.NODETYPEID
                ), 0
              ) as IS_FOLDER
          from V_CONTENT_TYPE CT
          left join INFO_FILELINK IFL
          on (IFL.NODEID = CT.NODEID)
          left join CONTENTTREE CT1
          on (CT1.NODEID = IFL.FILETYPEID)
          where CT.PARENTID = ?
          and CT.VISIBLE = 1
          order by CT.SORTORDER";


			$res = $this->_db->fetchAll ( $sql, $docid );
//			var_dump($res);
		} else {
			$res = array ();
		}
		return $res;
	}

	public function getNewNode($oldnodeid) {
		$sql = 'select CT.NODEID, VCT.HREF
from CONTENTTREE CT
inner join V_CONTENT_TREE VCT
on (VCT.NODEID = CT.NODEID)
where CT.OLDID = ?';

		return $this->_db->fetchRow($sql, $oldnodeid);
	}


}