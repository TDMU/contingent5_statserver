<?php

class Stat_Model_Report8 extends Stat_Model_Reports {

	public function getForm() {
		$form = new Zend_Form();
		$form->setMethod('post');
		$form->setDecorators(array(array('ViewScript', array('viewScript' => 'reports/report8Form.phtml'))));

		$items = $this->getPeriodItems('REQ_EDUSTAT_FAIL');
		$e = new Zend_Form_Element_Select('period',
		array('label' => 'На дату',
		'multiOptions' => $items,
		'required' => true));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_DEPARTMENT', true);
		$e = new Zend_Form_Element_Select('department',
		array('label' => 'Факультет',
		'multiOptions' => $items,
		'style' => 'width: 200px'));
		$form->addElement($e);

		$refreshAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'page', 'action' => 'show'));
		$e = new Zend_Form_Element_Submit('refresh', array('label'=>'Обновити', 'onclick'=>"document.forms[0].action='{$refreshAct}'"));
		$form->addElement($e);

		$excelAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'reports', 'action' => 'excel', 'report' => 8));
		$e = new Zend_Form_Element_Submit('excel', array('label'=>'Excel', 'onclick'=>"document.forms[0].action='{$excelAct}'"));
		$form->addElement($e);

		$form->setElementDecorators(array('ViewHelper', 'Errors'));

		return $form;
	}

	protected function getReportData() {
	  if (@$this->params['department']) {
	    $departmentClause = "inner join V_STAT_PARAMGROUPS SPG_DEPARTMENT
on (SPG_DEPARTMENT.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_DEPARTMENT.PARAMNAME = 'STAT_PARAM_DEPARTMENT')
and (SPG_DEPARTMENT.PARAMVALUE = '{$this->params['department']}')";
	  } else {
	    $departmentClause = '';
	  }


		$this->_db->beginTransaction ();

		$sql = " insert into TMP
select REQ.NODEID
from CONTENTTREE REQ
where REQ.PARENTID = ?";

		$this->_db->query($sql, $this->params['period']);

		$sql = "select first 10000
ADD_COURSE_ORIGIN.VAL as COURSE,
SPG_STUDENT.PARAMVALUE as FIO,
SPG_DISCIPLINE.PARAMVALUE as DISCIPLINE,
sum(iif(GUIDE_KIND.NODE_KEY = 'EDU_KIND_MISS_LECT', cast(SD.DATAVALUE as integer), 0)) as MISS_LECT,
sum(iif(GUIDE_KIND.NODE_KEY = 'EDU_KIND_MISS_PRACT', cast(SD.DATAVALUE as integer), 0)) as MISS_PRACT,
sum(iif(GUIDE_KIND.NODE_KEY = 'EDU_KIND_MISS_PRACT_HOURS', cast(SD.DATAVALUE as integer), 0)) as MISS_PRACT_HOURS,
sum(iif(GUIDE_KIND.NODE_KEY = 'EDU_KIND_FAIL', cast(SD.DATAVALUE as integer), 0)) as FAIL,
sum(iif(GUIDE_KIND.NODE_KEY = 'EDU_KIND_MODULE_UNACCESS', cast(SD.DATAVALUE as integer), 0)) as MODULE_UNACCESS,
sum(iif(GUIDE_KIND.NODE_KEY = 'EDU_KIND_MODULE_FAIL', cast(SD.DATAVALUE as integer), 0)) as MODULE_FAIL

from STAT_DATA SD

inner join TMP REQ
on (REQ.ID = SD.DATAREPORTID)

inner join V_ADD_VALUES_REF ADD_ESTABLISHMENTID
on (ADD_ESTABLISHMENTID.NODEID = cast(SD.DATAREPORTID as varchar(150)))
and (ADD_ESTABLISHMENTID.FIELDNAME = 'STRUCTUREID')
inner join CONTENTTREE GUIDE_ESTAB
on (GUIDE_ESTAB.NODEID = cast(ADD_ESTABLISHMENTID.VAL as bigint))

inner join V_STAT_PARAMGROUPS SPG_STUDENT
on (SPG_STUDENT.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_STUDENT.PARAMNAME = 'STAT_PARAM_STUDENT')

inner join V_STAT_PARAMGROUPS SPG_DISCIPLINE
on (SPG_DISCIPLINE.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_DISCIPLINE.PARAMNAME = 'STAT_PARAM_DISCIPLINE')

inner join V_STAT_PARAMGROUPS SPG_COURSE
on (SPG_COURSE.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_COURSE.PARAMNAME = 'STAT_PARAM_COURSE')
inner join V_ADD_VALUES ADD_COURSE_ORIGIN
on ('' || ADD_COURSE_ORIGIN.NODEID = SPG_COURSE.PARAMVALUE)
and (ADD_COURSE_ORIGIN.FIELDNAME = '_ADD_ORIGINID')

$departmentClause

inner join CONTENTTREE GUIDE_KIND
on (GUIDE_KIND.NODEID = SD.KINDID)

where 1=1 " .
(@$this->params['establishment'] ? " and ADD_ESTABLISHMENTID.VAL = " . $this->params['establishment'] : "") .
"group by 1, 2, 3
order by 1, 2, 3";

		$res = $this->_db->fetchAll($sql);

		$this->_db->commit ();

		return $res;
	}



	private function addDiscipline($sheet, $col, $row, $name) {
	  $sheet->mergeCellsByColumnAndRow($col, $row, $col + 5, $row);
	  $sheet->setCellValueByColumnAndRow($col, $row, $name);
	  $row ++;
	  $sheet->mergeCellsByColumnAndRow($col, $row, $col + 2, $row);
	  $sheet->setCellValueByColumnAndRow($col, $row, 'Пропуски');
	  $sheet->mergeCellsByColumnAndRow($col + 3, $row, $col + 3, $row + 2);
	  $sheet->setCellValueByColumnAndRow($col + 3, $row, 'Кол-во неуд. оценок');
	  $sheet->mergeCellsByColumnAndRow($col + 4, $row, $col + 5, $row + 1);
	  $sheet->setCellValueByColumnAndRow($col + 4, $row, 'ИМК');
	  $row ++;
	  $sheet->mergeCellsByColumnAndRow($col, $row, $col, $row + 1);
	  $sheet->setCellValueByColumnAndRow($col, $row, 'лекц.');
	  $sheet->mergeCellsByColumnAndRow($col + 1, $row, $col + 2, $row);
	  $sheet->setCellValueByColumnAndRow($col + 1, $row, 'практ. занятия');
	  $row ++;
	  $sheet->setCellValueByColumnAndRow($col + 1, $row, 'кол-во занятий');
	  $sheet->setCellValueByColumnAndRow($col + 2, $row, 'кол-во акад. часов');
	  $sheet->setCellValueByColumnAndRow($col + 4, $row, 'не доп.');
	  $sheet->setCellValueByColumnAndRow($col + 5, $row, 'не сдал');

	}

	private function finalizeTable($sheet, $startRow, $endRow, $discCount) {
	  $endCol = $discCount * 6 + 2;
	  if ($discCount > 1) {
  	  $this->addDiscipline($sheet, $endCol + 1, $startRow, 'По всем дисциплинам');


    	for ($col = 1; $col <= 6; $col++) {
  	    $sumCols = array();
  	    for ($i = 0; $i < $discCount; $i ++)
    	    $sumCols[] = PHPExcel_Cell::stringFromColumnIndex($i * 6 + $col + 2);

    	  for ($row = $startRow + 4; $row <= $endRow; $row++) {
    	    $formula = 'sum(' . implode("$row,", $sumCols) . $row . ')';
          $sheet->setCellValueByColumnAndRow($endCol + $col, $row, "=$formula");//"=if($formula=0;\"\";$formula)");
   	    }
   	  }

  	  $endCol += 6;
	  }

//    for ($i = 0; $i <= $endCol; $i++)
//      $sheet->setCellValueByColumnAndRow($i, $startRow + 4, $i + 1);

   	  $sheet->getStyle('A' . $startRow . ':' . PHPExcel_Cell::stringFromColumnIndex($endCol) . ($startRow + 3))
   	      ->applyFromArray(
   	        array(
        			'font' => array(
        				'bold' => true,
   	            'size' => 10
        			)
        		)
   	      );

   	  $sheet->getStyle('A' . $startRow . ':' . PHPExcel_Cell::stringFromColumnIndex($endCol) . $endRow)
     	    ->applyFromArray(
           		array(
           			'alignment' => array(
           		    'wrap' => true,
          				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
           				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
           		  ),
          		  	'borders' => array(
               	  	'allborders' => array(
               		  	'style' => PHPExcel_Style_Border::BORDER_THIN,
           				)
           			)
         	    )
        	  );

   	  $sheet
   	    ->getStyle('B' . ($startRow - 1))
   	    ->getAlignment()
   	    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

   	  $sheet->getStyle('B' . ($startRow + 4) . ':B' . $endRow)
   	    ->getAlignment()
   	    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	}

	public function getWorkbook($isExcelFile = false) {
		$data = $this->getReportData();

		$xls_reader = new PHPExcel_Reader_Excel2007();
		$workbook = $xls_reader->load(APPLICATION_PATH . 'modules/stat/templates/report8.xlsx');
		$sheet = $workbook->getActiveSheet();

		$onDate = new DateTime($this->getAddValueDate($this->params['period'], '_ADD_ENDDATE'));
		$onDateStr = $onDate->format('d.m.Y');
		$sheet->setCellValue('A1', sprintf($sheet->getCell('A1')->getValue(), $onDateStr));
		$departmentStr = $this->getGuideNodeTitle($this->params['department']);

		$lastCourse = -1;
		$lastStudent = -1;
		$lastDisc = -1;

		$current_row = 1;

		$maxCol = 2;

		if (count($data) > 0) {
		  foreach ($data as $datarow) {
		    $curCourse = $datarow['COURSE'];
		    if ($lastCourse != $curCourse) {
		      if ($lastCourse != -1) {
		        $this->finalizeTable($sheet, $titleRow, $current_row, count($disciplines));
		      }

		      $lastCourse = $curCourse;
		      $disciplines = array();

		      $current_row += 2;

		      $sheet->mergeCellsByColumnAndRow(1, $current_row, 7, $current_row );
		      $sheet->setCellValueByColumnAndRow(1, $current_row, "$departmentStr;  курс: $curCourse");
		      $current_row ++;

		      $titleRow = $current_row;

		      $sheet->insertNewRowBefore($current_row + 1, 4);
		      $sheet->setCellValueByColumnAndRow(0, $current_row, '№');
		      $sheet->mergeCellsByColumnAndRow(0, $current_row, 0, $current_row + 3);
		      $sheet->setCellValueByColumnAndRow(1, $current_row, 'ФИО студента');
		      $sheet->mergeCellsByColumnAndRow(1, $current_row, 1, $current_row + 3);
		      $sheet->setCellValueByColumnAndRow(2, $current_row, '№ группы');
		      $sheet->mergeCellsByColumnAndRow(2, $current_row, 2, $current_row + 3);

		      $current_row += 3;
		    }

		    $discipline = $datarow['DISCIPLINE'];
		    $discIndex = array_search($discipline, $disciplines);
		    if ($discIndex === FALSE) {
		      $this->addDiscipline($sheet, count($disciplines) * 6 + 3, $titleRow, $discipline);
		      array_push($disciplines, $discipline);
		      $discIndex = count($disciplines) - 1;
		      $maxCol = max($maxCol, ($discIndex + 1) * 6);
		    }

		    $curStudent = $datarow['FIO'];
		    if ($curStudent != $lastStudent) {
		      $current_row ++;
		      $sheet->insertNewRowBefore($current_row, 1);
		      $sheet->setCellValueByColumnAndRow(0, $current_row, ($current_row - $titleRow - 3));
		      $sheet->setCellValueByColumnAndRow(1, $current_row, preg_replace("/\(.*\)$/", "", $curStudent));
          $sheet->setCellValueByColumnAndRow(2, $current_row,
		        strpos($curStudent, '(') === false ? '' : preg_replace("/.*\((.*)\)$/", "$1", $curStudent));
		      $lastStudent = $curStudent;
		    }

		    $sheet->setCellValueByColumnAndRow($discIndex * 6 + 3, $current_row, $datarow['MISS_LECT'] == 0 ? '' : $datarow['MISS_LECT']);
		    $sheet->setCellValueByColumnAndRow($discIndex * 6 + 4, $current_row, $datarow['MISS_PRACT'] == 0 ? '' : $datarow['MISS_PRACT']);
		    $sheet->setCellValueByColumnAndRow($discIndex * 6 + 5, $current_row, $datarow['MISS_PRACT_HOURS'] == 0 ? '' : $datarow['MISS_PRACT_HOURS']);
		    $sheet->setCellValueByColumnAndRow($discIndex * 6 + 6, $current_row, $datarow['FAIL'] == 0 ? '' : $datarow['FAIL']);
		    $sheet->setCellValueByColumnAndRow($discIndex * 6 + 7, $current_row, $datarow['MODULE_UNACCESS'] == 0 ? '' : $datarow['MODULE_UNACCESS']);
		    $sheet->setCellValueByColumnAndRow($discIndex * 6 + 8, $current_row, $datarow['MODULE_FAIL'] == 0 ? '' : $datarow['MODULE_FAIL']);
		  }

		  $this->finalizeTable($sheet, $titleRow, $current_row, count($disciplines));
		  $maxCol += 6;
		  $sheet->mergeCellsByColumnAndRow(0, 1, $maxCol + 1, 1);

		  for ($i = 2; $i <= $maxCol; $i++) {
		    $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth(7);
		  }

		}
		return $workbook;
	}

}