<?php

require_once 'Zend/Db/Table/Abstract.php';

class Stat_Model_Requests {
//	protected $_acl = null;
	protected $_db = null;

	public function __construct() {
//		$this->_acl = Zend_Registry::get ( 'acl' );
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter ();
	}

	public function getForm() {
		$form = new Zend_Form();
		$form->setMethod(Zend_Form::METHOD_POST);
		$form->setDecorators(array(array('ViewScript', array('viewScript' => 'requests/reqForm.phtml'))));

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


	public function getGuideYears() {
		$sql = 'select distinct extract(year from IAVD.VAL), extract(year from IAVD.VAL)
from V_CONTENT_TYPE VCT
inner join INFO_ADD_VALUES_DATE IAVD
on IAVD.NODEID = VCT.NODEID
inner join INFO_FIELDS I
on I.NODEID = IAVD.FIELDID
where VCT.T_NODE_KEY = ?
and I.FIELDNAME = ?
order by 1 desc';

		return $this->_db->fetchPairs($sql, array('T_STAT2_PERIOD', '_ADD_ENDDATE'));
	}


	public function getGuideItems($name, $canAll = false, $canEmpty = false, $order = 'CT.SORTORDER') {
		$sql = 'select CT.NODEID, CT.TITLE
			from V_CONTENT_TYPE CT
			where CT.VISIBLE = 1
			and CT.T_NODE_KEY = ?
			order by ' . $order;

		$items = $this->_db->fetchPairs ( $sql, $name );

		$res = $canAll? array(0 => 'Усі' ): array();

		return array_merge($res, $items);
	}



	public function getResponsesReports($year = 0, $establishmentid = 0, $periodid = 0) {
		$cond = "";
		$cond .= $year != 0 ? " and extract(year from AVD.VAL) = {$year}" : "";
		$cond .= $periodid != 0 ? " and CT1.NODEID = {$periodid}" : "";

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
and (AVD1.FIELDNAME = '_ADD_PUBLICDATE')
) as PUBLICDATE,
(
select AVD1.VAL
from V_ADD_VALUES_DATE AVD1
where (AVD1.NODEID = CT1.NODEID)
and (AVD1.FIELDNAME = '_ADD_PUBLICENDDATE')
) as PUBLICENDDATE,
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

from V_CONTENT_TYPE CT  -- отчеты
inner join ALLOW_NODES AN
on (AN.NODEID = CT.NODEID)
inner join V_CONTENT_TYPE CT1 -- периоды
on (CT1.PATH starting with CT.FULL_PATH)
and (CT1.T_NODE_KEY = 'T_STAT2_PERIOD')
inner join V_ADD_VALUES_DATE AVD
on (AVD.NODEID = CT1.NODEID)
and (AVD.FIELDNAME = '_ADD_ENDDATE')
where CT.T_NODE_KEY = 'T_STAT2_REQUEST'
and CT.VISIBLE = 1
and CT1.VISIBLE = 1
and AN.ALLOWNODEID = ?
$cond
order by AVD.VAL";

//		file_put_contents('d:\\temp\\sql.txt', $sql . "    --    " . $establishmentid);
		
		return $this->_db->fetchAll($sql, array ($establishmentid, $establishmentid, $establishmentid, $establishmentid));
	}


	public function reportRemove($reportid) {
		$this->_db->beginTransaction();

		$sql = 'delete from STAT_DATA where DATAREPORTID = ?';
		$this->_db->query($sql, $reportid);

		$sql = 'delete from STAT_VALIDATION where DATAREPORTID = ?';
		$this->_db->query($sql, $reportid);

		// delete add value
		$sql = "update V_ADD_VALUES_DATE AV
set AV.VAL = null
where AV.NODEID = ?
and AV.FIELDNAME = '_ADD_DISPATCHDATE'";

		$this->_db->query($sql, $reportid);

		$this->_db->commit();

/*		$sql = "execute block(REPORTID bigint = ?)
as
begin
  delete from STAT_DATA where DATAREPORTID = :REPORTID;

  update V_ADD_VALUES_DATE AV
  set AV.VAL = null
  where AV.NODEID = :REPORTID
  and AV.FIELDNAME = '_ADD_DISPATCHDATE';
end";

		$this->_db->query($sql, $reportid);*/
	}

}