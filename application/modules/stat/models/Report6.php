<?php

class Stat_Model_Report6 extends Stat_Model_Reports {

	public function getForm() {
		$form = new Zend_Form();
		$form->setMethod('post');
		$form->setDecorators(array(array('ViewScript', array('viewScript' => 'reports/report6Form.phtml'))));

		$items = $this->getPeriodItems('REQ_MOZ_VACANCY');
		$e = new Zend_Form_Element_Select('period',
		array('label' => 'На дату',
		'multiOptions' => $items,
		'required' => true));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_EDUFORM', false);
		$e = new Zend_Form_Element_Select('eduform',
		array('label' => 'Форма навчання',
		'multiOptions' => $items,
		'style' => 'width: 200px'));
		$form->addElement($e);

		$refreshAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'page', 'action' => 'show'));
		$e = new Zend_Form_Element_Submit('refresh', array('label'=>'Обновити', 'onclick'=>"document.forms[0].action='{$refreshAct}'"));
		$form->addElement($e);

		$excelAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'reports', 'action' => 'excel', 'report' => 6));
		$e = new Zend_Form_Element_Submit('excel', array('label'=>'Excel', 'onclick'=>"document.forms[0].action='{$excelAct}'"));
		$form->addElement($e);

		$form->setElementDecorators(array('ViewHelper', 'Errors'));

		return $form;
	}

	protected function getReportData() {
		$this->_db->beginTransaction ();

		$sql = " insert into TMP
select REQ.NODEID
from CONTENTTREE REQ
where REQ.PARENTID = ?";

		$this->_db->query($sql, $this->params['period']);

		$sql = "select GUIDE_ESTAB.SORTORDER as ESTABLISHMENT_NUM,
null as SPECIALITY_NUM, null as SPECIALITY_CODE,
null as C1, null as C2, null as C3, null as C4, null as C5, null as C6, null as C7
from TMP REQ
inner join V_ADD_VALUES_REF ADD_ESTABLISHMENTID
on (ADD_ESTABLISHMENTID.NODEID = REQ.ID)
and (ADD_ESTABLISHMENTID.FIELDNAME = 'STRUCTUREID')
inner join CONTENTTREE GUIDE_ESTAB
on (GUIDE_ESTAB.NODEID = cast(ADD_ESTABLISHMENTID.VAL as bigint))
inner join V_ADD_VALUES_DATE ADD_DISPATCH
on ADD_DISPATCH.NODEID = REQ.ID
and ADD_DISPATCH.FIELDNAME = '_ADD_DISPATCHDATE'
where not exists (
  select *
  from STAT_DATA SD
  where SD.DATAREPORTID = REQ.ID
) ".
(@$this->params['establishment'] ? " and ADD_ESTABLISHMENTID.VAL = " . $this->params['establishment'] : "") .

"union

select first 10000 GUIDE_ESTAB.SORTORDER as ESTABLISHMENT_NUM,
GUIDE_SPEC.SORTORDER as SPECIALITY_NUM, ADD_CODE.VAL as SPECIALITY_CODE,
sum(iif(ADD_COURSE_ORIGIN.VAL = 1, cast(SD.DATAVALUE as integer), 0)) as C1,
sum(iif(ADD_COURSE_ORIGIN.VAL = 2, cast(SD.DATAVALUE as integer), 0)) as C2,
sum(iif(ADD_COURSE_ORIGIN.VAL = 3, cast(SD.DATAVALUE as integer), 0)) as C3,
sum(iif(ADD_COURSE_ORIGIN.VAL = 4, cast(SD.DATAVALUE as integer), 0)) as C4,
sum(iif(ADD_COURSE_ORIGIN.VAL = 5, cast(SD.DATAVALUE as integer), 0)) as C5,
sum(iif(ADD_COURSE_ORIGIN.VAL = 6, cast(SD.DATAVALUE as integer), 0)) as C6,
sum(iif(ADD_COURSE_ORIGIN.VAL = 7, cast(SD.DATAVALUE as integer), 0)) as C7

from STAT_DATA SD

inner join TMP REQ
on (REQ.ID = SD.DATAREPORTID)

inner join V_ADD_VALUES_REF ADD_ESTABLISHMENTID
on (ADD_ESTABLISHMENTID.NODEID = cast(SD.DATAREPORTID as varchar(20)))
and (ADD_ESTABLISHMENTID.FIELDNAME = 'STRUCTUREID')
inner join CONTENTTREE GUIDE_ESTAB
on (GUIDE_ESTAB.NODEID = cast(ADD_ESTABLISHMENTID.VAL as bigint))

inner join V_STAT_PARAMGROUPS SPG_SPECIALITY
on (SPG_SPECIALITY.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_SPECIALITY.PARAMNAME = 'STAT_PARAM_SPECIALITYID')
inner join CONTENTTREE GUIDE_SPEC
on (GUIDE_SPEC.NODEID = cast(SPG_SPECIALITY.PARAMVALUE as bigint))
inner join V_ADD_VALUES ADD_CODE
on (ADD_CODE.NODEID = cast(SPG_SPECIALITY.PARAMVALUE as bigint))
and (ADD_CODE.FIELDNAME = '_ADD_CODE')

inner join V_STAT_PARAMGROUPS SPG_COURSE
on (SPG_COURSE.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_COURSE.PARAMNAME = 'STAT_PARAM_COURSE')
inner join V_ADD_VALUES ADD_COURSE_ORIGIN
on (ADD_COURSE_ORIGIN.NODEID = cast(SPG_COURSE.PARAMVALUE as bigint))
and (ADD_COURSE_ORIGIN.FIELDNAME = '_ADD_ORIGINID')

inner join V_STAT_PARAMGROUPS SPG_EDUFORMID
on (SPG_EDUFORMID.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_EDUFORMID.PARAMNAME = 'STAT_PARAM_EDUFORMID')
and (SPG_EDUFORMID.PARAMVALUE = '{$this->params['eduform']}')

where 1=1 " .
(@$this->params['establishment'] ? " and ADD_ESTABLISHMENTID.VAL = " . $this->params['establishment'] : "") .
"group by 1, 2, 3
order by 1, 3";

		$res = $this->_db->fetchAll($sql);

		$this->_db->commit ();

		return $res;
	}



	public function getWorkbook( $isExcelFile = false) {
		$data = $this->getReportData();

		$xls_reader = new PHPExcel_Reader_Excel2007();
		$workbook = $xls_reader->load(APPLICATION_PATH . 'modules/stat/templates/report6.xlsx');
		$sheet = $workbook->getActiveSheet();

    $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(2, 4); // В шаблоне пока не работает
//    $sheet->getSheetView()->setZoomScale(120);

		$guide_vuz = $this->getEstablishments('GUIDE_STRUCTURE', 'SORTORDER');
		$guide_eduform = $this->getGuideItems('T_EDUFORM');
		$guide_speciality = $this->getSpecialities(false, false, 'CT.SORTORDER', 'SORTORDER');

		$eduFormStr = mb_strtolower($guide_eduform[$this->params['eduform']], 'utf8');
		$endDate = new DateTime($this->getAddValueDate($this->params['period'], '_ADD_ENDDATE'));
		$endDateStr = $endDate->format('d.m.Y');

		$sheet->setCellValue('A1', sprintf($sheet->getCell('A1')->getValue(), $endDateStr, $eduFormStr));

		$lastEstNum = -1;
		$vuznum = 1;

		$current_row = 5;

		$sheet->insertNewRowBefore($current_row + 1, count($data));

		foreach ($data as $datarow) {
			$curEstNum = $datarow['ESTABLISHMENT_NUM'];
			if ($lastEstNum != $curEstNum) {
			  $lastEstNum = $curEstNum;

  			if ($vuznum > 1) { // Разделительная линия между вузами
		      $sheet->insertNewRowBefore($current_row + 1, 1);
  			  $sheet->getRowDimension($current_row)->setRowHeight(0.75);
		  	  $current_row ++;
			  }

		    $sheet->insertNewRowBefore($current_row + 1, 1);

			  $sheet->setCellValueByColumnAndRow(0, $current_row, $vuznum);
	  		$sheet->setCellValueByColumnAndRow(1, $current_row, $guide_vuz[$curEstNum]);
	  		$sheet->mergeCellsByColumnAndRow(1, $current_row, 9, $current_row);
			  $sheet->getStyle("B$current_row:J$current_row")->getFont()->setBold(true);
			  $sheet->getStyle("A$current_row:J$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($this->colorLevel1);

	  		$current_row ++;
	  		$vuznum ++;
			}

//      $sheet->getRowDimension($current_row)->setOutlineLevel(1);//->setVisible(false);

			if (!$datarow['SPECIALITY_NUM']) {
			  $sheet->setCellValueByColumnAndRow(1, $current_row, 'Вакантних місць немає');
	  		$sheet->mergeCellsByColumnAndRow(1, $current_row, 9, $current_row);
	  		$sheet->getStyle("B$current_row:J$current_row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			  $current_row ++;
			  continue;
			}

			$sheet->setCellValueByColumnAndRow(1, $current_row, $guide_speciality[$datarow['SPECIALITY_NUM']]);
			$sheet->setCellValueByColumnAndRow(2, $current_row, "=SUM(D$current_row:J$current_row)");
			for($col = 1; $col <= 7; $col++)
			  $sheet->setCellValueByColumnAndRow($col+2, $current_row, $datarow["C$col"]);

			$current_row ++;
		}

		$sheet->removeRow($current_row, 1);

		return $workbook;
	}

}