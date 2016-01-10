<?php

// Движение контингента за год по направлениям и уровням подготовки + план + перспективный план

class Stat_Model_Report3 extends Stat_Model_Reports {

	public function getForm() {
		$form = new Zend_Form();
		$form->setMethod('post');
		$form->setDecorators(array(array('ViewScript', array('viewScript' => 'reports/report3Form.phtml'))));

		$items = $this->getPeriodItems('REQ_MOZ_CONTMOVEMENT_YEAR');
		$e = new Zend_Form_Element_Select('period',
		array('label' => 'Рік',
		'multiOptions' => $items,
		'required' => true,
		'style' => 'width: 80px'));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_REPORTPLANKIND');
		$e = new Zend_Form_Element_Select('reportplankind',
		array('label' => 'Звіт/план',
		'multiOptions' => $items,
		'required' => true,
		'style' => 'width: 200px'));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_EDUFORM', false);
		$e = new Zend_Form_Element_Select('eduform',
		array('label' => 'Форма навчання',
		'multiOptions' => $items,
		'style' => 'width: 200px'));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_EDUBASIS', false);
		$e = new Zend_Form_Element_Select('edubase',
		array('label' => 'Форма фінансування',
		'multiOptions' => $items,
		'style' => 'width: 200px'));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_COUNTRYTYPE', false);
		$e = new Zend_Form_Element_Select('countrytype',
		array('label' => 'Тип громадянства',
		'multiOptions' => $items,
		'style' => 'width: 200px'));
		$form->addElement($e);

		$refreshAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'page', 'action' => 'show'));
		$e = new Zend_Form_Element_Submit('refresh', array('label'=>'Обновити', 'onclick'=>"document.forms[0].action='{$refreshAct}'"));
		$form->addElement($e);

		$excelAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'reports', 'action' => 'excel', 'report' => 3));
		$e = new Zend_Form_Element_Submit('excel', array('label'=>'Excel', 'onclick'=>"document.forms[0].action='{$excelAct}'"));
		$form->addElement($e);

		$form->setElementDecorators(array('ViewHelper', 'Errors'));

		return $form;
	}

	protected function getReportData() {
		$includeReportPlanKind = $this->params['report'] == 3;
		if($includeReportPlanKind)
			$reportPlanKindJoin = " inner join V_STAT_PARAMGROUPS SPG_REPORTKIND
on (SPG_REPORTKIND.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_REPORTKIND.PARAMNAME = 'STAT_PARAM_REPORTPLANKIND')
and (SPG_REPORTKIND.PARAMVALUE = {$this->params['reportplankind']})";

		$includeEdubasis = $this->params['edubase'] != 0;
		if($includeEdubasis)
			$edubasisJoin = " inner join V_STAT_PARAMGROUPS SPG_EDUBASISID
on (SPG_EDUBASISID.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_EDUBASISID.PARAMNAME = 'STAT_PARAM_EDUBASISID')
and (SPG_EDUBASISID.PARAMVALUE = '{$this->params['edubase']}')";

		$includeEduform = $this->params['eduform'] != 0;
		if($includeEduform)
			$eduformJoin = " inner join V_STAT_PARAMGROUPS SPG_EDUFORMID
on (SPG_EDUFORMID.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_EDUFORMID.PARAMNAME = 'STAT_PARAM_EDUFORMID')
and (SPG_EDUFORMID.PARAMVALUE = '{$this->params['eduform']}')";

		$includeCountryType = $this->params['countrytype'] != 0;
		if($includeCountryType)
			$countryTypeJoin = " inner join V_STAT_PARAMGROUPS SPG_COUNTRYTYPEID
on (SPG_COUNTRYTYPEID.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_COUNTRYTYPEID.PARAMNAME = 'STAT_PARAM_COUNTRYTYPEID')
and (SPG_COUNTRYTYPEID.PARAMVALUE = '{$this->params['countrytype']}')";


		$this->_db->beginTransaction ();

		$sql = " insert into TMP
select REQ.NODEID
from CONTENTTREE REQ
where REQ.PARENTID = ?";

		$this->_db->query($sql, $this->params['period']);

		$sql = "select first 10000 GUIDE_ESTAB.SORTORDER as ESTABLISHMENT_NUM,

(select GUIDE_EDUDIRECTION.SORTORDER
from V_STAT_PARAMGROUPS SPG_EDUDIRECTION
inner join CONTENTTREE GUIDE_EDUDIRECTION
on (GUIDE_EDUDIRECTION.NODEID = cast(SPG_EDUDIRECTION.PARAMVALUE as bigint))
where (SPG_EDUDIRECTION.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_EDUDIRECTION.PARAMNAME = 'STAT_PARAM_EDUDIRECTIONID')) as EDUDIRECTION_NUM,

(select GUIDE_EDULEVEL.SORTORDER
from V_STAT_PARAMGROUPS SPG_EDULEVEL
inner join CONTENTTREE GUIDE_EDULEVEL
on (GUIDE_EDULEVEL.NODEID = cast(SPG_EDULEVEL.PARAMVALUE as bigint))
where (SPG_EDULEVEL.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_EDULEVEL.PARAMNAME = 'STAT_PARAM_EDULEVELID')) as EDULEVEL_NUM,

trim(leading 'STAT_KIND_' from GUIDE_KIND.NODE_KEY) as KIND,
cast(SD.DATAVALUE as integer) as VAL

from STAT_DATA SD

inner join TMP REQ
on (REQ.ID = SD.DATAREPORTID)

inner join V_ADD_VALUES_REF ADD_ESTABLISHMENTID
on (ADD_ESTABLISHMENTID.NODEID = cast(SD.DATAREPORTID as varchar(20)))
and (ADD_ESTABLISHMENTID.FIELDNAME = 'STRUCTUREID')
inner join CONTENTTREE GUIDE_ESTAB
on (GUIDE_ESTAB.NODEID = cast(ADD_ESTABLISHMENTID.VAL as bigint))

inner join CONTENTTREE GUIDE_KIND
on (GUIDE_KIND.NODEID = SD.KINDID)" .

($includeReportPlanKind ? $reportPlanKindJoin : "") .
($includeEdubasis ? $edubasisJoin : "") .
($includeEduform ? $eduformJoin : "") .
($includeCountryType ? $countryTypeJoin : "") .

"where 1=1 " .
(@$this->params['establishment'] ? " and ADD_ESTABLISHMENTID.VAL = " . $this->params['establishment'] : "") .
//($includeReportPlanKind ? " and SPG_REPORTKIND.PARAMVALUE = '" . @$this->params['reportplankind'] . "'" : "") .
//($includeEdubasis ? " and SPG_EDUBASISID.PARAMVALUE = '{$this->params['edubase']}'" : "") .
//($includeEduform ? " and SPG_EDUFORMID.PARAMVALUE = '{$this->params['eduform']}'" : "") .
//($includeCountryType ? " and SPG_COUNTRYTYPEID.PARAMVALUE = '{$this->params['countrytype']}'" : "") .

"order by ESTABLISHMENT_NUM, EDUDIRECTION_NUM, EDULEVEL_NUM desc";

		$res = $this->_db->fetchAll($sql);

		$this->_db->commit ();

		return $res;
	}



	public function getWorkbook() {
		$data = $this->getReportData();
		$data = $this->formtable($data);

		$eduDirections = array();

		$xls_reader = new PHPExcel_Reader_Excel2007();
		$workbook = $xls_reader->load(APPLICATION_PATH . 'modules/stat/templates/report3.xlsx');
		$sheet = $workbook->getActiveSheet();

		$foreighnStr = $this->getAddValue($this->params['countrytype'], '_ADD_ORIGINID') == 'УКР' ? '' : 'іноземних ';
		$eduBaseStr = $this->getAddValue($this->params['edubase'], '_ADD_ORIGINID') == 'Б' ? 'держзамовленням' : 'контрактом';
		$eduForms = array('ДН' => 'що навчаються', 'ВЧ' => 'що навчаються вечірньо', 'ЗЧ' => 'що навчаються заочно', 'ДЦ' => 'що навчаються дистанційно', 'ДВ' => 'що отримують другу вищу освіту');
		$eduFormStr = $eduForms[$this->getAddValue($this->params['eduform'], '_ADD_ORIGINID')] . ' ';

		$endDate = new DateTime($this->getAddValueDate($this->params['period'], '_ADD_ENDDATE'));
		if(@$this->params['reportplankind']) {
			$selectedReportPlan = $this->getGuideNodeTitle($this->params['reportplankind']);
			$planKindNum = $this->getAddValue($this->params['reportplankind'], '_ADD_ORIGINID') - 1;
			$endDate->modify("+$planKindNum year");
		}	else {
			$selectedReportPlan = 'Уточнений план';
		}
		$selectedYear = $endDate->format('Y');


		$sheet->setCellValue('A1', sprintf($sheet->getCell('A1')->getValue(), $selectedReportPlan, $foreighnStr, $eduFormStr, $eduBaseStr, $selectedYear));

		$current_row = 9;
//		$sheet->getStyle("B17")->getNumberFormat()->setFormatCode('dd.mm.yyyy'); // Если просто задано в шаблоне, то не работает в HTML

		$sheet->insertNewRowBefore($current_row+1, count($data) * 2  - 1);

		foreach ($data as $vuznum => $vuz) {
			$vuzrow = $current_row + 1;

			$sheet->setCellValueByColumnAndRow(0, $current_row, $vuznum + 1);
			$sheet->setCellValueByColumnAndRow(1, $current_row, $vuz['title']);

			$workbook->getActiveSheet()->mergeCellsByColumnAndRow(1, $current_row, 19, $current_row);

			$next_row = $current_row + 1;

			$sheet->insertNewRowBefore($next_row + 1, 2);

			$sheet->getStyle("A$current_row:T$next_row")->getFont()->setBold(true);
			$sheet->getStyle("A$current_row:T$next_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8EEEA');
			$sheet->getStyle("B$current_row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


			$current_row ++;
			$sheet->setCellValueByColumnAndRow(1, $current_row, $selectedYear);

			$current_row ++;
			$sheet->setCellValueByColumnAndRow(1, $current_row, 'в тому числі');

			$current_row ++;
			$sheet->insertNewRowBefore($current_row+1, count($vuz['DATA']));

			$dirrows = array();

			foreach ($vuz['DATA'] as $dirnum => $dir) {
				if(!@array_key_exists($dir['title'], $eduDirections))
					$eduDirections[$dir['title']] = array();
				$currentEduDirection = &$eduDirections[$dir['title']];

				$dirrows[$dirnum] = $current_row;

				$sheet->setCellValueByColumnAndRow(1, $current_row, 'напрям "' . $dir['title'] . '"');

				$levels_count = count($dir['DATA']);
				$formula_row1 = $current_row + 1;
				$formula_row2 = $current_row + $levels_count;

				for($col=2; $col<=19; $col++) {
					if($col == 15) continue;
					$colStr = PHPExcel_Cell::stringFromColumnIndex($col);
					$sheet->setCellValueByColumnAndRow($col, $current_row, "=SUM($colStr$formula_row1:$colStr$formula_row2)");
				}

				$sheet->getStyle("A$current_row:T$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFDE9D9');

				$current_row ++;

				$sheet->insertNewRowBefore($current_row+1, $levels_count);

				foreach ($dir['DATA'] as $edulevelnum => $edulevel) {
					if(!@array_key_exists($edulevel['title'], $currentEduDirection))
						$currentEduDirection[$edulevel['title']] = array($current_row);
					else
						array_push($currentEduDirection[$edulevel['title']], $current_row);

					$sheet->setCellValueByColumnAndRow(1, $current_row, $edulevel['title']);

					foreach (array('CS',null,'CEXIT4','CEXIT3','CEXIT2','CEXIT1',null,'CENTER4','CENTER3','CENTER2','CENTER1',null,'MOVEOUT',null,'MOVEIN') as $colnum => $column) {
						if($column)
							$sheet->setCellValueByColumnAndRow($colnum + 2, $current_row, array_key_exists($column, $edulevel) ? $edulevel[$column] : 0);
					}

					$sheet->setCellValueByColumnAndRow(3, $current_row, "=SUM(E$current_row:H$current_row)");
					$sheet->setCellValueByColumnAndRow(8, $current_row, "=SUM(J$current_row:M$current_row)");
					$sheet->setCellValueByColumnAndRow(13, $current_row, "=C$current_row-D$current_row+I$current_row");
					$sheet->setCellValueByColumnAndRow(17, $current_row, "=N$current_row-O$current_row+Q$current_row");
					$sheet->setCellValueByColumnAndRow(18, $current_row, "=(C$current_row*8.5+R$current_row*3.5)/12");
					$sheet->setCellValueByColumnAndRow(19, $current_row, "=S$current_row");

					$current_row ++;
				}

			}

			for($col=2; $col<=19; $col++) {
				if($col == 15) continue;
				$colStr = PHPExcel_Cell::stringFromColumnIndex($col);
				$sheet->setCellValueByColumnAndRow($col, $vuzrow, "=$colStr" . implode("+$colStr", $dirrows));
			}

			$sheet->getRowDimension($current_row)->setRowHeight(0.75);

			$current_row ++;
		}

//------------------------------------------------------------------------------
		if(count($eduDirections) > 0) {
			$rowAll = $current_row;
			$sheet->setCellValueByColumnAndRow(1, $current_row, 'РАЗОМ');

			$sheet->insertNewRowBefore($current_row+1, count($eduDirections) + 1);

			$sheet->getStyle("A$current_row:T$current_row")->getFont()->setBold(true);
			$sheet->getStyle("A$current_row:T$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8EEEA');
			$sheet->getStyle("B$current_row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			$current_row++;

			$dirrows = array();

			foreach ($eduDirections as $dir => $edulevels) {
				array_push($dirrows, $current_row);

				$sheet->setCellValueByColumnAndRow(1, $current_row, 'напрям "' . $dir . '"');
				$sheet->getStyle("A$current_row:T$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFDE9D9');

				$levels_count = count($edulevels);
				$formula_row1 = $current_row + 1;
				$formula_row2 = $current_row + $levels_count;

				for($col=2; $col<=19; $col++) {
					if($col == 15) continue;
					$colStr = PHPExcel_Cell::stringFromColumnIndex($col);
					$sheet->setCellValueByColumnAndRow($col, $current_row, "=SUM($colStr$formula_row1:$colStr$formula_row2)");
				}

				$current_row ++;

				$sheet->insertNewRowBefore($current_row+1, $levels_count);

				foreach ($edulevels as $edulevel => $edulevelRows) {
					$sheet->setCellValueByColumnAndRow(1, $current_row, $edulevel);

					for($col = 2; $col <= 19; $col++) {
						if($col == 15) continue;
						$colStr = PHPExcel_Cell::stringFromColumnIndex($col);
						$sheet->setCellValueByColumnAndRow($col, $current_row, "=$colStr" . implode("+$colStr", $edulevelRows));
					}

					$current_row ++;
				}
			}

			for($col=2; $col<=19; $col++) {
				if($col == 15) continue;
				$colStr = PHPExcel_Cell::stringFromColumnIndex($col);
				$sheet->setCellValueByColumnAndRow($col, $rowAll, "=$colStr" . implode("+$colStr", $dirrows));
			}
		}
		//------------------------------------------------------------------------------

		$sheet->removeRow($current_row);//, 1);

		return $workbook;
	}

	protected function formtable($data) {
		$vuzes = array ();

		if (!empty($data)) {

			$this->guide_vuz = $this->getEstablishments('GUIDE_STRUCTURE', 'SORTORDER');
			$this->guide_edudir = $this->getGuideItems('T_EDUDIRECTION', false, false, 'CT.SORTORDER', 'SORTORDER');
			$this->guide_edulevel = $this->getGuideItems('T_EDULEVEL', false, false, 'CT.SORTORDER', 'SORTORDER');


			$row = -1;
			$lastEstNum = -1;
			$lastDirNum = -1;
			$lastEdulevelNum = -1;
			$vuzNum = -1;
			$rowData = -1;
			$vuzData;

			foreach($data as &$datarow) {
					$curEstNum = $datarow['ESTABLISHMENT_NUM'];
					if ($lastEstNum != $curEstNum) {
						$vuzNum ++;
						$rowData = -1;
						$lastEstNum = $curEstNum;
						$lastDirNum = -1;
						$vuzes[$vuzNum] = array('title' => $this->guide_vuz[$lastEstNum], 'DATA' => array());
						$vuzData = &$vuzes[$vuzNum]['DATA'];
					}

					$dirNum = $datarow["EDUDIRECTION_NUM"];
					if ($lastDirNum != $dirNum) {
						$rowData ++;
						$lastDirNum = $dirNum;
						$lastEdulevelNum = -1;
						$rowEduLevel = -1;
						$vuzData[$rowData] =  array('id' => $lastDirNum, 'title' => $this->guide_edudir[$lastDirNum], 'DATA' => array());
						$dirData = &$vuzData[$rowData]['DATA'];
					}

					$edulevelNum = $datarow["EDULEVEL_NUM"];
					if ($lastEdulevelNum != $edulevelNum) {
						$rowEduLevel ++;
						$lastEdulevelNum = $edulevelNum;
						$dirData[$rowEduLevel] =  array('title' => $this->guide_edulevel[$lastEdulevelNum]);//, 'DATA' => array());
						$edulevelData = &$dirData[$rowEduLevel];
					}

					$factor_name = $datarow["KIND"];
					if($factor_name == 'CENTER' || $factor_name == 'CEXIT') $factor_name .= $datarow["EDULEVEL_NUM"];
					$val = $datarow ["VAL"];
					$edulevelData[$factor_name] = array_key_exists($factor_name, $edulevelData) ? $edulevelData[$factor_name] + $val : $val;
			}

		}

		return $vuzes;
	}

}