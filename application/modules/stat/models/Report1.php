<?php

// Квартальное движение контингента

class Stat_Model_Report1 extends Stat_Model_Reports{

	public function getForm() {
		$form = new Zend_Form();
		$form->setMethod('post');
		$form->setDecorators(array(array('ViewScript', array('viewScript' => 'reports/report1Form.phtml'))));

		$items = $this->getPeriodItems('REQ_MOZ_CONTMOVEMENT');
		$e = new Zend_Form_Element_Select('period',
		array('label' => 'Період',
		'multiOptions' => $items,
		'required' => true));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_STRUCTURE_ITEM', true, false, 'CT.TITLE');
		$e = new Zend_Form_Element_Select('establishment',
		array('label' => 'Установа',
		'multiOptions' => $items));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_EDUFORM', true);
		$e = new Zend_Form_Element_Select('eduform',
		array('label' => 'Форма навчання',
		'multiOptions' => $items));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_EDUBASIS', true);
		$e = new Zend_Form_Element_Select('edubase',
		array('label' => 'Форма фінансування',
		'multiOptions' => $items));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_COUNTRY', true);
		$e = new Zend_Form_Element_Select('country',
		array('label' => 'Громадянство (країна)',
		'multiOptions' => $items));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_COUNTRYTYPE', true);
		$e = new Zend_Form_Element_Select('countrytype',
		array('label' => 'Тип громадянства',
		'multiOptions' => $items));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_EDULEVEL');
		$e = new Zend_Form_Element_MultiCheckbox('edulevel',
		array('label' => 'Рівень підготовки',
		'multiOptions' => $items));
		$e->setValue(array_keys($items));
		$form->addElement($e);

		$refreshAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'page', 'action' => 'show'));
		$e = new Zend_Form_Element_Submit('refresh', array('label'=>'Обновити', 'onclick'=>"document.forms[0].action='{$refreshAct}'"));
		$form->addElement($e);

		$excelAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'reports', 'action' => 'excel', 'report' => 1));
		$e = new Zend_Form_Element_Submit('excel', array('label'=>'Excel', 'onclick'=>"document.forms[0].action='{$excelAct}'"));
		$form->addElement($e);

		$form->setElementDecorators(array('ViewHelper', 'Errors'));

		$auth = Zend_Auth::getInstance ();
		$ident = $auth->getIdentity ();
		if($ident->STRUCTURE_CODE != 0) $form->getElement('establishment')->setValue($ident->STRUCTUREID);

		return $form;
	}


	protected function getReportData() {
		$reportNum = $this->params['report'];

		$this->_db->beginTransaction ();

		$sql = " insert into TMP
select REQ.NODEID
from CONTENTTREE REQ
where REQ.PARENTID = ?";

		$this->_db->query($sql, $this->params['period']);

		$includeEstablishment = $reportNum != 1 || $this->params['establishment'] != 0;
		$establishmentJoin = " inner join V_ADD_VALUES_REF ADD_ESTABLISHMENTID
on (ADD_ESTABLISHMENTID.NODEID = cast(SD.DATAREPORTID as varchar(20)))
and (ADD_ESTABLISHMENTID.FIELDNAME = 'STRUCTUREID')";

		$includeEdubasis = $this->params['edubase'] != 0;
		$edubasisJoin = " inner join V_STAT_PARAMGROUPS SPG_EDUBASISID
on (SPG_EDUBASISID.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_EDUBASISID.PARAMNAME = 'STAT_PARAM_EDUBASISID')";

		$includeEduform = $this->params['eduform'] != 0;
		$eduformJoin = " inner join V_STAT_PARAMGROUPS SPG_EDUFORMID
on (SPG_EDUFORMID.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_EDUFORMID.PARAMNAME = 'STAT_PARAM_EDUFORMID')";

		$includeCountry = $this->params['country'] != 0 || $this->params['countrytype'] != 0;
		$countryJoin = " inner join V_STAT_PARAMGROUPS SPG_COUNTRYID
on (SPG_COUNTRYID.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_COUNTRYID.PARAMNAME = 'STAT_PARAM_COUNTRYID')
inner join V_ADD_VALUES_REF ADD_COUNTRYTYPEID
on (ADD_COUNTRYTYPEID.NODEID = cast(SPG_COUNTRYID.PARAMVALUE as bigint))
and (ADD_COUNTRYTYPEID.FIELDNAME = '_ADD_COUNTRYTYPEID')";

		$sql = "select first 10000 " . ($includeEstablishment ? "ADD_ESTABLISHMENTID.VAL as ESTABLISHMENTID, " : "") . "GUIDE_SPEC.NODEID as SPECIALITYID,
GUIDE_SPEC.TITLE as SPECIALITY,
ADD_CODE.VAL as CODE,
ADD_EDULEVELID.VAL as EDULEVELID," . ($includeEdubasis ? " SPG_EDUBASISID.PARAMVALUE as EDUBASISID," : "") . ($includeEduform ? " SPG_EDUFORMID.PARAMVALUE as EDUFORMID," : "") . ($includeCountry ? " SPG_COUNTRYID.PARAMVALUE as COUNTRYID, ADD_COUNTRYTYPEID.VAL as COUNTRYTYPEID," : "") . "ADD_COURSE_ORIGIN.VAL as COURSE,
trim(leading 'STAT_KIND_' from GUIDE_KIND.NODE_KEY) as KIND,
cast(SD.DATAVALUE as integer) as VAL

from STAT_DATA SD

inner join TMP REQ
on (REQ.ID = SD.DATAREPORTID)

inner join V_STAT_PARAMGROUPS SPG_SPEC
on (SPG_SPEC.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_SPEC.PARAMNAME = 'STAT_PARAM_SPECIALITYID')
inner join CONTENTTREE GUIDE_SPEC
on (GUIDE_SPEC.NODEID = cast(SPG_SPEC.PARAMVALUE as bigint))
inner join V_ADD_VALUES ADD_CODE
on (ADD_CODE.NODEID = cast(SPG_SPEC.PARAMVALUE as bigint))
and (ADD_CODE.FIELDNAME = '_ADD_CODE')
inner join V_ADD_VALUES_REF ADD_EDULEVELID
on (ADD_EDULEVELID.NODEID = cast(SPG_SPEC.PARAMVALUE as bigint))
and (ADD_EDULEVELID.FIELDNAME = '_ADD_EDULEVELID')" .

		($includeEdubasis ? $edubasisJoin : "") . ($includeEduform ? $eduformJoin : "") . ($includeCountry ? $countryJoin : "") . ($includeEstablishment ? $establishmentJoin : "") .

		"inner join V_STAT_PARAMGROUPS SPG_COURSE
on (SPG_COURSE.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_COURSE.PARAMNAME = 'STAT_PARAM_COURSE')
inner join V_ADD_VALUES ADD_COURSE_ORIGIN
on (ADD_COURSE_ORIGIN.NODEID = cast(SPG_COURSE.PARAMVALUE as bigint))
and (ADD_COURSE_ORIGIN.FIELDNAME = '_ADD_ORIGINID')

inner join CONTENTTREE GUIDE_KIND
on (GUIDE_KIND.NODEID = SD.KINDID)
where 1 = 1
and SD.DATAVALUE <> '0' " . (@$this->params['establishment'] != 0 ? " and ADD_ESTABLISHMENTID.VAL = " . $this->params['establishment'] : "") . ($includeEdubasis ? " and SPG_EDUBASISID.PARAMVALUE = '" . $this->params['edubase'] . "'" : "") . ($includeEduform ? " and SPG_EDUFORMID.PARAMVALUE = '" . $this->params['eduform'] . "'" : "") . //group by 1,2,3,4,5,6"
" order by " . ($reportNum != 1 ? " ADD_ESTABLISHMENTID.VAL," : "") . "ADD_CODE.VAL";

		$res = $this->_db->fetchAll ( $sql );

		$this->_db->commit ();

		return $res;
	}

	public function getWorkbook() {
		$data = $this->getReportData();
		$data = $this->formtable($data);

		$xls_reader = new PHPExcel_Reader_Excel2007();
		$workbook = $xls_reader->load(APPLICATION_PATH . 'modules/stat/templates/report1.xlsx');

		$workbook->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $this->getGuideNodeTitle($this->params['establishment']));

		if(count($data) > 2) $workbook->getActiveSheet()->insertNewRowBefore(5, count($data) - 2);
		foreach ($data as $rownum => $row) {
			$workbook->getActiveSheet()->setCellValueByColumnAndRow(0, $rownum + 4, $row['title']);
			foreach (array('CS','CS1','CS2','CS3','CS4','CS5','CS6','CS7','CIN','CIN1','CIN2','CIN3','CIN4','CIN5','CIN6','CIN7','COUT','COUT1','COUT2','COUT3','COUT4','COUT5','COUT6','COUT7') as $colnum => $column) {
				$workbook->getActiveSheet()->setCellValueByColumnAndRow($colnum + 1, $rownum + 4, array_key_exists($column, $row) ? $row[$column] : 0);
			}
		}

		return $workbook;
	}

	private function formtable($data) {
		$list = array ();

		if (! empty ( $data )) {
			$sum = array ('title' => 'Усього:' );

			$row = - 1;
			$lastspecid = - 1;

			foreach ( $data as &$datarow ) {
				if (($this->params['country'] == 0 || $this->params['country'] == $datarow ['COUNTRYID'])
						&& ($this->params['countrytype'] == 0 || $this->params['countrytype'] == $datarow ['COUNTRYTYPEID'])
						&& (is_array ($this->params['edulevel'])
						&& (array_search ( $datarow ['EDULEVELID'], $this->params['edulevel']) !== false))) {
					$specid = $datarow ["SPECIALITYID"];
					if ($lastspecid != $specid) {
						$row ++;
						$lastspecid = $specid;
						$list [$row] = array ('title' => $datarow ["CODE"] . " - " . $datarow ["SPECIALITY"] );
						$line = &$list [$row];
					}

					$factor_name = $datarow ["KIND"];
					$full_factor_name = $factor_name . $datarow ["COURSE"];
					$val = $datarow ["VAL"];
					$line [$full_factor_name] = array_key_exists ( $full_factor_name, $line ) ? $line [$full_factor_name] + $val : $val;
					$line [$factor_name] = array_key_exists ( $factor_name, $line ) ? $line [$factor_name] + $val : $val;

					$sum [$full_factor_name] = array_key_exists ( $full_factor_name, $sum ) ? $sum [$full_factor_name] + $val : $val;
					$sum [$factor_name] = array_key_exists ( $factor_name, $sum ) ? $sum [$factor_name] + $val : $val;
				}
			}

			$list [$row + 1] = &$sum;
		}

		return $list;
	}

}