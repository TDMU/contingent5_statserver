<?php

require_once 'Zend/Db/Table/Abstract.php';

class Stat_Model_Reports {
	protected $_db = null;

	protected $colorLevel1 = 'FFD8EEEA';
	protected $colorLevel2 = 'FFFDD9C9';
	protected $colorLevel3 = 'FFFDE9D9';
  public $isExcelDifferentFromHTML = false;
  public $params;

	public function __construct() {
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter ();
	}

	public function getGuideItems($name, $canAll = false, $canEmpty = false, $order = 'CT.SORTORDER', $keyField = 'NODEID') {
		$sql = "select CT.$keyField, CT.TITLE
from V_CONTENT_TYPE CT
where CT.VISIBLE = 1
and CT.T_NODE_KEY = ?
order by $order";

		$items = $this->_db->fetchAll($sql, $name);

		if ($canAll)
			$res = array (0 => 'Усі' );
		else
			$res = array ();

		foreach ( $items as &$item ) {
			$res [$item [$keyField]] = $item ['TITLE'];
		}

		return $res;
	}
	
	public function getTitleOfParamGroupId($paramGroupId){
		$sql = "select
				  case CT_T.NODE_KEY
				    when 'STAT_PARAM_SPECIALITYID' then 1
				    when 'STAT_PARAM_EDUBASISID' then 3
				    when 'STAT_PARAM_EDUFORMID' then 2
				    when 'STAT_PARAM_COUNTRYTYPEID' then 4
				    else 5
				  end,
				
				  PG.PARAM_VALUE, CT_T.NODE_KEY, CT_V.TITLE,
				  coalesce((select VAV.VAL from V_ADD_VALUES VAV where VAV.NODEID = CT_V.NODEID and VAV.FIELDNAME = '_ADD_CODE'), '-') as CODE
				from STAT_PARAMGROUPS PG
				inner join CONTENTTREE CT_T
				  on (CT_T.NODEID = PG.PARAMID)
				inner join CONTENTTREE CT_V
				  on (CT_V.NODEID = cast(PG.PARAM_VALUE as integer))
				where PG.PARAMGROUPID = ?
				order by 1";
		$items = $this->_db->fetchAll($sql, $paramGroupId);
		
		$res = '';
		foreach ( $items as &$item ) {
			$last=isset($res[0])?$res[strlen($res)-1]:'';
			if (strlen($res) > 0 && $last != '[') $res = $res . ', ';
			if ($item['NODE_KEY'] == 'STAT_PARAM_SPECIALITYID')
				$res = $res . $item ['CODE'] . ' - ' . $item ['TITLE'] . ' [';
			else 
				$res = $res . $item ['TITLE'];
		}		
		$res = $res . ']';
		
		return $res;
	}

	public function getSpecialities($canAll = false, $canEmpty = false, $order = 'CT.SORTORDER', $keyField = 'NODEID') {
		$sql = "select CT.$keyField,
		CT.TITLE as TITLE
from V_CONTENT_TYPE CT
where CT.VISIBLE = 1
and CT.T_NODE_KEY = 'T_SPECIALITY'
order by $order";

		$items = $this->_db->fetchAll($sql);

		if ($canAll)
			$res = array (0 => 'Усі' );
		else
			$res = array ();

		foreach ( $items as &$item ) {
			$res [$item [$keyField]] = $item ['TITLE'];
		}

		return $res;
	}
	
	public function getFpoSpecialities($fpocategoryid) {
		$sql = "select 
(select VAV.VAL from V_ADD_VALUES VAV where VAV.NODEID = VCT.NODEID and VAV.FIELDNAME = '_ADD_CODE') as CODE,
VCT.TITLE_WO_CODE as TITLE, VCT.NODEID as SPECID
from CONTENTTREE CT  -- Это справочник
inner join V_CONTENT_TYPE VCT -- это элементы справочника
on VCT.PATH starting with CT.FULL_PATH
where VCT.VISIBLE = 1
and CT.NODE_KEY = (select VAV.VAL from V_ADD_VALUES VAV where VAV.NODEID = $fpocategoryid and VAV.FIELDNAME = 'SPECIALITY_GUIDE_NODEKEY')
-- and VCT.T_NODE_KEY = 'T_FPO_SPECIALITY'
order by 1";		
	
		$items = $this->_db->fetchAll($sql);
		
		foreach ( $items as &$item ) {
			$res [$item ['CODE']] = $item ['TITLE'];
		}
		
		return $res;
	}	

	public function getEstablishments($parentKey = 'GUIDE_STRUCTURE', $keyField = 'NODEID', $canAll = false, $canEmpty = false) {
		$sql = "select VCT.$keyField, VCT.TITLE as TITLE
from V_CONTENT_TYPE VCT
where VCT.VISIBLE = 1
and VCT.T_NODE_KEY = 'T_STRUCTURE_ITEM'
and VCT.PARENTID = (
  select CT.NODEID
  from CONTENTTREE CT
  where CT.NODE_KEY = '$parentKey'
)
order by VCT.SORTORDER";

		$items = $this->_db->fetchAll($sql);

		if ($canAll)
			$res = array (0 => 'Усі' );
		else
			$res = array ();

		foreach ( $items as &$item ) {
			$res [$item [$keyField]] = $item ['TITLE'];
		}

		return $res;
	}
	
	public function getFPOListeners() {
		$sql = "select CT.SORTORDER, CT.TITLE as TITLE
  from CONTENTTREE CT
  where CT.VISIBLE = 1
  and  CT.NODE_KEY in ('FPO_CAT_PAC','FPO_CAT_TU','FPO_CAT_SPECIALIZATION','FPO_CAT_STAGUVANNYA','FPO_CAT_TEACHER')
  order by CT.SORTORDER";
	
		$items = $this->_db->fetchAll($sql);
			
		$res = array ();
	
		foreach ( $items as &$item ) {
			$res [$item ['SORTORDER']] = $item ['TITLE'];
		}
	
		return $res;
	}

	public function getPeriodItems($request_key, $addCondition = null) {
		$sql = "select CT.NODEID, CT.TITLE
from V_CONTENT_TYPE CT
where CT.VISIBLE = 1
and CT.PATH starting with (select CTP.FULL_PATH from CONTENTTREE CTP where CTP.NODE_KEY = ?)
and CT.T_NODE_KEY = 'T_STAT2_PERIOD'" .
($addCondition ? " and {$addCondition} " : " ") .
"order by CT.SORTORDER desc";

		return $this->_db->fetchPairs($sql, $request_key);
	}

	public function getGuideNodeTitle($nodeid) {
		if($nodeid == 0) return 'Усі';

		$sql = "select CT.TITLE
from CONTENTTREE CT
where CT.NODEID = ?";

		return $this->_db->fetchOne($sql, array($nodeid));
	}

	public function getAddValue($nodeid, $fieldname) {
		$sql = "select AV.VAL
from V_ADD_VALUES AV
where AV.NODEID = ?
and AV.FIELDNAME = ?";

		return $this->_db->fetchOne($sql, array($nodeid, $fieldname));
	}

	public function getAddValueDate($nodeid, $fieldname) {
		$sql = "select AV.VAL
from V_ADD_VALUES_DATE AV
where AV.NODEID = ?
and AV.FIELDNAME = ?";

		return $this->_db->fetchOne($sql, array($nodeid, $fieldname));
	}

	public function getValidations() {
		$sql = "select CT.TITLE as VALIDATION, SV.ARG1, SV.ARG2, SV.NOTE
from STAT_VALIDATION SV
inner join CONTENTTREE CT
on (CT.NODEID = SV.VALIDATIONID)
where SV.DATAREPORTID = ?
and SV.PARAMGROUPID in ({$this->params['paramgroupids']})
and SV.KINDID = ?";

		return $this->_db->fetchAll($sql, array($this->params['datareportid'], $this->params['kindid']));
	}


	public function getReportsHash() {
		$sql = "select list(VAVD.VAL)
from CONTENTTREE REQ
inner join V_ADD_VALUES_DATE VAVD
on (VAVD.NODEID = REQ.NODEID)
and (VAVD.FIELDNAME = '_ADD_DISPATCHDATE')
where REQ.PARENTID = ?";

		return md5($this->_db->fetchOne($sql, $this->params['period']));
	}

}