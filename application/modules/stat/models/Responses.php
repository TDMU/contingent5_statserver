<?php

require_once 'Zend/Db/Table/Abstract.php';

class Stat_Model_Responses {
	protected $_db = null;

	public function __construct() {
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter ();
	}

	public function getForm($request_key) {
		$form = new Zend_Form();
		$form->setMethod(Zend_Form::METHOD_POST);
		$form->setDecorators(array(array('ViewScript', array('viewScript' => 'responses/respForm.phtml'))));

		$items = $this->getRequests($request_key);
		$e = new Zend_Form_Element_Select('report',
			array('label' => 'Звіт',
				'multiOptions' => $items,
				'required' => true));
		$form->addElement($e);

		$items = $this->getGuideYears ();
		$e = new Zend_Form_Element_Select('year',
			array('label' => 'Рік',
				'multiOptions' => $items,
				'required' => true,
				'value' => reset($items)));
		$form->addElement($e);

		$e = new Zend_Form_Element_Submit('refresh', array('label'=>'Обновити'));
		$form->addElement($e);

		$form->setElementDecorators(array('ViewHelper', 'Errors'));

		return $form;
	}


	private function getGuideYears() {
		$sql = 'select distinct extract(year from IAVD.VAL), extract(year from IAVD.VAL)
from V_CONTENT_TYPE VCT
inner join INFO_ADD_VALUES_DATE IAVD
on IAVD.NODEID = VCT.NODEID
inner join INFO_FIELDS I
on I.NODEID = IAVD.FIELDID
where VCT.T_NODE_KEY = ?
and I.FIELDNAME = ?
and VCT.VISIBLE = 1
order by 1 desc';

		return $this->_db->fetchPairs( $sql, array ('T_STAT2_PERIOD', '_ADD_ENDDATE' ) );
	}


	private function getRequests($key_starting_with) {
		$sql = 'select CT.NODEID, CT.TITLE
from V_CONTENT_TYPE CT
where CT.T_NODE_KEY = \'T_STAT2_REQUEST\'
and CT.NODE_KEY starting with ?
and CT.VISIBLE = 1
order by 2';

		$items = $this->_db->fetchAll($sql, $key_starting_with);

		$res = array(0 => 'Усі');

		foreach($items as $item) {
			$res[$item['NODEID']] = $item['TITLE'];
		}

		return $res;
	}


	private function getGuideItems($name, $canAll = false, $canEmpty = false, $order = 'CT.SORTORDER') {
		$sql = 'select CT.NODEID, CT.TITLE
from V_CONTENT_TYPE CT
where CT.T_NODE_KEY = ?
and CT.VISIBLE = 1
order by ' . $order;

		$items = $this->_db->fetchAll($sql, $name);

		$res = $canAll ? array(0 => 'Усі') : array ();

//		array_merge($res, $items); Не работает как надо
		foreach($items as $item) {
			$res[$item['NODEID']] = $item['TITLE'];
		}

		return $res;
	}


	public function getGuideStructure($year, $requestid, $request_key) {
		$sql = "select CT_STRUCT.NODEID, CT_STRUCT.TITLE
from V_CONTENT_TYPE CT_STRUCT
where CT_STRUCT.T_NODE_KEY = 'T_STRUCTURE_ITEM'
and exists (
  select *
  from V_CONTENT_TYPE VCT_REQ
  inner join ALLOW_NODES AN
    on (AN.NODEID = VCT_REQ.NODEID)
      and (AN.ALLOWNODEID = CT_STRUCT.NODEID)
  inner join V_CONTENT_TYPE CT_PERIOD
    on (CT_PERIOD.PATH starting with VCT_REQ.FULL_PATH)
      and (CT_PERIOD.T_NODE_KEY = 'T_STAT2_PERIOD')
  inner join V_ADD_VALUES_DATE VAVD_REQ_ENDDATE
    on (VAVD_REQ_ENDDATE.NODEID = CT_PERIOD.NODEID)
      and (VAVD_REQ_ENDDATE.FIELDNAME = '_ADD_ENDDATE')
      and (VAVD_REQ_ENDDATE.VAL >= '01.01.$year' and VAVD_REQ_ENDDATE.VAL <= '31.12.$year')
  where VCT_REQ.T_NODE_KEY = 'T_STAT2_REQUEST'
    and VCT_REQ.NODE_KEY starting with '$request_key' " .
	($requestid ? "and VCT_REQ.NODEID = {$requestid} " : "") .
")
order by CT_STRUCT.SORTORDER";

		return $this->_db->fetchAll ( $sql);
	}


	public function getRequestsByYear($year, $requestid, $request_key) {
		$sql = "select CT_PERIOD.NODEID as REQUEST_PERIODID, CT_PERIOD.TITLE as REQUEST_PERIOD,
CT_REQUEST.TITLE as REQUEST_TITLE, AV_PUBLICENDDATE.VAL as REQUEST_PUBLIC_ENDDATE
from V_CONTENT_TYPE CT_PERIOD
inner join CONTENTTREE CT_REPORTS
on CT_REPORTS.NODEID = CT_PERIOD.PARENTID
inner join CONTENTTREE CT_REQUEST
on CT_REQUEST.NODEID = CT_REPORTS.PARENTID " .
($requestid ? "and CT_REQUEST.NODEID = $requestid " : "and CT_REQUEST.NODE_KEY starting with '$request_key' ") .
"inner join V_ADD_VALUES_DATE AV_ENDDATE
on (AV_ENDDATE.NODEID = CT_PERIOD.NODEID)
and (AV_ENDDATE.FIELDNAME = '_ADD_ENDDATE')
inner join V_ADD_VALUES_DATE AV_PUBLICENDDATE
on (AV_PUBLICENDDATE.NODEID = CT_PERIOD.NODEID)
and (AV_PUBLICENDDATE.FIELDNAME = '_ADD_PUBLICENDDATE')
where CT_PERIOD.T_NODE_KEY = 'T_STAT2_PERIOD'
and CT_PERIOD.VISIBLE = 1
and CT_REPORTS.VISIBLE = 1
and CT_REQUEST.VISIBLE = 1
and extract(year from AV_ENDDATE.VAL) = ?
order by AV_ENDDATE.VAL";

		return $this->_db->fetchAll($sql, $year);
	}


	public function getResponsesReports($year, $establishmentid, $requestid) {
		$cond = $year != 0 ? " and extract(year from AVD.VAL) = {$year}" : "";

		$sql = "select CT1.TITLE as PERIOD, CT1.NODEID as PERIODID,
CT.TITLE as REPORT, AVD.VAL as ENDDATE,
(
select AVD1.VAL
from V_ADD_VALUES_DATE AVD1
where (AVD1.NODEID = CT1.NODEID)
and (AVD1.FIELDNAME = '_ADD_STARTDATE')
) as STARTDATE,

(
select AVD1.VAL
from V_ADD_VALUES_DATE AVD1
where (AVD1.NODEID = CT1.NODEID)
and (AVD1.FIELDNAME = '_ADD_PUBLICENDDATE')
) as MAXDATE,
(
select CT.NODEID
from CONTENTTREE CT
inner join V_ADD_VALUES_REF IAV
on IAV.NODEID = CT.NODEID
and IAV.FIELDNAME = 'STRUCTUREID'
where CT.PARENTID = CT1.NODEID
and IAV.VAL = ?
)as REPORTID,
(
select CT.CREATEDATE
from CONTENTTREE CT
inner join V_ADD_VALUES_REF IAV
on IAV.NODEID = CT.NODEID
and IAV.FIELDNAME = 'STRUCTUREID'
where CT.PARENTID = CT1.NODEID
and IAV.VAL = ?
)as CREATEDATE,
(
select IAV.VAL
from CONTENTTREE CT
inner join V_ADD_VALUES_DATE IAV
on IAV.NODEID = CT.NODEID
and IAV.FIELDNAME = '_ADD_DISPATCHDATE'
inner join V_ADD_VALUES_REF IAV1
on IAV1.NODEID = CT.NODEID
and IAV1.FIELDNAME = 'STRUCTUREID'
where CT.PARENTID = CT1.NODEID
and IAV1.VAL = ?
)as UPLOADDATE

from V_CONTENT_TYPE CT  -- отчеті
inner join ALLOW_NODES AN
on (AN.NODEID = CT.NODEID)
inner join V_CONTENT_TYPE CT1 -- периоді
on (CT1.PATH starting with CT.FULL_PATH)
and (CT1.T_NODE_KEY = 'T_STAT2_PERIOD')
inner join V_ADD_VALUES_DATE AVD
on (AVD.NODEID = CT1.NODEID)
and (AVD.FIELDNAME = '_ADD_ENDDATE')
where CT.T_NODE_KEY = 'T_STAT2_REQUEST'" .
($requestid ? "and CT.NODEID = {$requestid} " : "") .
"and CT.VISIBLE = 1
and CT1.VISIBLE = 1
and AN.ALLOWNODEID = ? {$cond}
order by AVD.VAL";

		return $this->_db->fetchAll($sql, array ($establishmentid, $establishmentid, $establishmentid, $establishmentid));
	}

	public function getEstablishments2Reports($year, $requestid, $request_key) {
		$res = array();
		foreach ($this->getGuideStructure($year, $requestid, $request_key) as $establishment) {
			$reports = $this->getResponsesReports($year, $establishment['NODEID'], $requestid);
		  foreach ($reports as $report) {
			  $establishment['reports'][$report['PERIODID']] = $report;
		  }
			array_push($res, $establishment);
		}
		return $res;
	}

}