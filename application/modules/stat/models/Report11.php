<?php

/*
*     ФПО План СЛУХАЧИ (отчет только по слушателям) 
*/



class Stat_Model_Report11 extends Stat_Model_Reports {

	public function getForm() {
		$form = new Zend_Form();
		$form->setMethod('post');
		$form->setDecorators(array(array('ViewScript', array('viewScript' => 'reports/report11Form.phtml'))));

		$items = $this->getPeriodItems('REQ_FPO_GR1'); // report NODE_KEY
		$e = new Zend_Form_Element_Select('period',
		array('label' => 'Період',
		'multiOptions' => $items,
		'required' => true,
		'style' => 'width: 300px'));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_EDUBASIS', false);
		$e = new Zend_Form_Element_Select('edubase',
		array('label' => 'Форма фінансування',
		'multiOptions' => $items,
		'style' => 'width: 300px'));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_COUNTRYTYPE', false);
		$e = new Zend_Form_Element_Select('countrytype',
		array('label' => 'Тип громадянства',
		'multiOptions' => $items,
		'style' => 'width: 300px'));
		$form->addElement($e);

		$refreshAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'page', 'action' => 'show'));
		$e = new Zend_Form_Element_Submit('refresh', array('label'=>'Обновити', 'onclick'=>"document.forms[0].action='{$refreshAct}'"));
		$form->addElement($e);

		$excelAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'reports', 'action' => 'excel', 'report' => 11));
		$e = new Zend_Form_Element_Submit('excel', array('label'=>'Excel', 'onclick'=>"document.forms[0].action='{$excelAct}'"));
		$form->addElement($e);

		$form->setElementDecorators(array('ViewHelper', 'Errors'));

		return $form;
	}

	protected function getReportData() {
		
		$includeEdubasis = $this->params['edubase'] != 0;
		if($includeEdubasis)
			$edubasisJoin = " inner join V_STAT_PARAMGROUPS SPG_EDUBASISID
on (SPG_EDUBASISID.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_EDUBASISID.PARAMNAME = 'STAT_PARAM_EDUBASISID')
and (SPG_EDUBASISID.PARAMVALUE = '{$this->params['edubase']}')";

		$includeCountryType = $this->params['countrytype'] != 0;
		if($includeCountryType)
			$countryTypeJoin = " inner join V_STAT_PARAMGROUPS SPG_COUNTRYTYPEID
on (SPG_COUNTRYTYPEID.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_COUNTRYTYPEID.PARAMNAME = 'STAT_PARAM_COUNTRYTYPEID')
and (SPG_COUNTRYTYPEID.PARAMVALUE = '{$this->params['countrytype']}')";


		$this->_db->beginTransaction ();

//		$sql = " insert into TMP
//select REQ.NODEID
//from CONTENTTREE REQ
//where REQ.PARENTID = ?";

		$sql = " insert into TMP
select CT_VUZREP.NODEID
from CONTENTTREE CT_VUZREP
inner join CONTENTTREE CT_PERIOD
  on CT_PERIOD.NODEID = CT_VUZREP.PARENTID
inner join CONTENTTREE CT_REPORTS
  on CT_REPORTS.NODEID = CT_PERIOD.PARENTID
inner join CONTENTTREE CT_REPORT
  on CT_REPORT.NODEID = CT_REPORTS.PARENTID
where
  CT_REPORT.NODE_KEY in ('REQ_FPO_GR1', 'REQ_FPO_GR2', 'REQ_FPO_GR3', 'REQ_FPO_GR4')
  and CT_PERIOD.TITLE = (select CT_P.TITLE from CONTENTTREE CT_P where CT_P.NODEID = ?)";

		$this->_db->query($sql, $this->params['period']);		
		
		$sql = "select
iif((cast(INFO_ESTAB.CODE as integer) in (2001,2002,2003,2004,2005,2006,2007,2008,2009,2010,2011,2012,2013,2014,2015,2031)), 0, 1) as GRP,

GUIDE_ESTAB.SORTORDER as ESTABLISHMENT_NUM,

CT_FPO_CATEGORY.SORTORDER as CATEGORY_SORTORDER,

trim(leading 'STAT_KIND_' from GUIDE_KIND.NODE_KEY) as KIND,

sum(cast(SD.DATAVALUE as integer)) as VAL

from STAT_DATA SD
inner join TMP REQ
on (REQ.ID = SD.DATAREPORTID)

inner join V_STAT_PARAMGROUPS SPG_FPO_CATEGORY
  on SPG_FPO_CATEGORY.PARAMGROUPID = SD.PARAMGROUPID
    and SPG_FPO_CATEGORY.PARAMNAME = 'STAT_PARAM_FPO_CATEGORYID'
inner join CONTENTTREE CT_FPO_CATEGORY
  on CT_FPO_CATEGORY.NODEID = SPG_FPO_CATEGORY.PARAMVALUE

inner join V_ADD_VALUES_REF ADD_ESTABLISHMENTID
  on (ADD_ESTABLISHMENTID.NODEID = cast(SD.DATAREPORTID as varchar(20)))
    and (ADD_ESTABLISHMENTID.FIELDNAME = 'STRUCTUREID')

inner join CONTENTTREE GUIDE_ESTAB
on (GUIDE_ESTAB.NODEID = cast(ADD_ESTABLISHMENTID.VAL as bigint))

inner join INFO_STRUCTURE_ITEM INFO_ESTAB
on (INFO_ESTAB.NODEID = GUIDE_ESTAB.NODEID)

inner join CONTENTTREE GUIDE_KIND
on (GUIDE_KIND.NODEID = SD.KINDID)" .

($includeEdubasis ? $edubasisJoin : "") .
($includeCountryType ? $countryTypeJoin : "") .

"where  CT_FPO_CATEGORY.NODE_KEY in ('FPO_CAT_PAC','FPO_CAT_TU','FPO_CAT_SPECIALIZATION','FPO_CAT_STAGUVANNYA','FPO_CAT_TEACHER','FPO_CAT_SEMINARU','FPO_CAT_MASTERCLASS','FPO_CAT_PFOFSCHOOL')" .

(@$this->params['establishment'] ? " and ADD_ESTABLISHMENTID.VAL = " . $this->params['establishment'] : "") .

"group by 1, 2, 3, 4
order by GRP, ESTABLISHMENT_NUM, CATEGORY_SORTORDER, KIND";

		$res = $this->_db->fetchAll($sql);

		$this->_db->commit ();
		
		return $res; 
	}
/*	
	protected function getFooterStructure($fpocategoryid) {
		$sql = "select 
(select VAV.VAL from V_ADD_VALUES VAV where VAV.NODEID = VCT.NODEID and VAV.FIELDNAME = '_ADD_CODE') as CODE,
VCT.T_NODE_KEY as TYPE_NODE_KEY, -- info about level
VCT.TITLE_WO_CODE as TITLE 
from CONTENTTREE CT  -- Это справочник
inner join V_CONTENT_TYPE VCT -- это элементы справочника
on VCT.PATH starting with CT.FULL_PATH
where VCT.VISIBLE = 1
and CT.NODE_KEY = (select VAV.VAL from V_ADD_VALUES VAV where VAV.NODEID = $fpocategoryid and VAV.FIELDNAME = 'SPECIALITY_GUIDE_NODEKEY')
-- and VCT.T_NODE_KEY = 'T_FPO_SPECIALITY'
order by 1";
		
		return $this->_db->fetchAll($sql);
		
	}
	
*/	
	// recursion function
/*	
	private function showFooterLine($sheet, $leveldata, &$current_row, &$sumRows, &$sumLevelUp, $isGrandTotal){		
		
		if($leveldata['level'] == 0){			
			$sheet->mergeCellsByColumnAndRow(1, $current_row, 2, $current_row);			
			$sheet->setCellValueByColumnAndRow(1, $current_row, $isGrandTotal ? 'ЗА ВСІМА ВНЗ' : 'РАЗОМ');						
			$sheet->getStyleByColumnAndRow(1, $current_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sheet->getStyle("B$current_row:I$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8EEEA');
		} else if($leveldata['level'] == 1){
			$sheet->mergeCellsByColumnAndRow(1, $current_row, 2, $current_row);
			$sheet->setCellValueByColumnAndRow(1, $current_row, $leveldata['code'] . " - " . $leveldata['title']);
			$sheet->getStyleByColumnAndRow(1, $current_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle("B$current_row:I$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8EEEA');
		} else if($leveldata['level'] == 2){
			$sheet->mergeCellsByColumnAndRow(1, $current_row, 2, $current_row);
			$sheet->setCellValueByColumnAndRow(1, $current_row, $leveldata['code'] . " - " . $leveldata['title']);
			$sheet->getStyleByColumnAndRow(1, $current_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyleByColumnAndRow(1, $current_row)->getFont()->setBold(true);
		} else if($leveldata['level'] == 3){
			// Speciality
			$sheet->setCellValueByColumnAndRow(1, $current_row, $leveldata['title']);
			$sheet->setCellValueByColumnAndRow(2, $current_row, $leveldata['code']);
			$sheet->getStyleByColumnAndRow(1, $current_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			//var_dump($sumRows);
			
			$v = array_key_exists($leveldata['code'], $sumRows);
			
			//echo '<br/><br/>';
			//var_dump($leveldata['code']); echo '<br/>';
			//var_dump($sumRows); echo '<br/>';
			//var_dump($v);
			
			for ($col=3; $col<=9; $col++){
				$colStr = PHPExcel_Cell::stringFromColumnIndex($col);
				$sheet->setCellValueByColumnAndRow($col, $current_row, ($v ? "=$colStr" . implode("+$colStr", $sumRows[$leveldata['code']]) : "0") );
			}
										
		}
		
		// Номер строки вносим в сумму по разделу
		if (isset($sumLevelUp))
			$sumLevelUp[] = $current_row;
		
		$formularow = $current_row;
		 
		$current_row ++;		
		
		if (isset($leveldata['DATA'])){
			
			$levelSize = count($leveldata['DATA']);
			$sheet->insertNewRowBefore($current_row + 1, $levelSize);
			$sumLevel = array();
			foreach ($leveldata['DATA'] as $lnum => $levelnext) {
				$this->showFooterLine($sheet, $levelnext, $current_row, $sumRows, $sumLevel, $isGrandTotal);  // recursion
			}
			// заполнение формул с 4 по 10 колонку
			$rCnt = count($sumLevel);
			if ($rCnt > 0){			 
			$rFirst = $sumLevel[0];
			$rLast = $sumLevel[$rCnt - 1];
			$rSum = ($rLast-$rFirst == $rCnt - 1) && ($rCnt > 1);  									
				for ($col=3; $col<=9; $col++){
					$colStr = PHPExcel_Cell::stringFromColumnIndex($col);
					$sheet->setCellValueByColumnAndRow($col, $formularow, 
						$rSum ? "=SUM(" . $colStr . $rFirst . ":" . $colStr . $rLast . ")" : "=$colStr" . implode("+$colStr", $sumLevel));
				}
			}
		}
	}
	*/
	private function showFooter($sheet, $footer_Structure, &$current_row, &$sumRows, $isGrandTotal = false, $isAddDelimiter = true){		
		if (!$isGrandTotal) {
			$sheet->insertNewRowBefore($current_row, 1);
			$sheet->getRowDimension($current_row)->setRowHeight(0.75); // Разделительная линия перед ВСЕГО
			$current_row ++;
		}
						
		//$sheet->insertNewRowBefore($current_row+1, count($footer_Structure) + 1);
										
		$sumLevelUp = array();	
		$cnt = count($footer_Structure);
		$sheet->insertNewRowBefore($current_row+1, $cnt + 1);
		
		// Заголовок
		//$sheet->mergeCellsByColumnAndRow(1, $current_row, 2, $current_row);
		$sheet->setCellValueByColumnAndRow(1, $current_row, $isGrandTotal ? 'ЗА ВСІМА ВНЗ' : 'РАЗОМ');
		$sheet->getStyleByColumnAndRow(1, $current_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("B$current_row:I$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8EEEA');
		$formularow = $current_row;
		$current_row ++;
		
		// Categories
		foreach ($footer_Structure as $code => $category) {
			$sheet->setCellValueByColumnAndRow(1, $current_row, $category);			
			$sheet->getStyleByColumnAndRow(1, $current_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$v = array_key_exists($code, $sumRows);
			for ($col=2; $col<=8; $col++){
				$colStr = PHPExcel_Cell::stringFromColumnIndex($col);
				$sheet->setCellValueByColumnAndRow($col, $current_row, ($v ? "=$colStr" . implode("+$colStr", $sumRows[$code]) : "0") );
			}
			$current_row ++;
		}
		
		// Суммы
		// заполнение формул с 2 по 8 колонку		
		$rFirst = $formularow + 1;
		$rLast = $formularow + $cnt;		
		for ($col=2; $col<=8; $col++){
			$colStr = PHPExcel_Cell::stringFromColumnIndex($col);
			$sheet->setCellValueByColumnAndRow($col, $formularow,
			"=SUM(" . $colStr . $rFirst . ":" . $colStr . $rLast . ")");
		}		
					
		if (!$isGrandTotal && $isAddDelimiter) {
			// Разделительная строка между группами вузов
			$sheet->insertNewRowBefore($current_row + 1, 1);
			$sheet->mergeCellsByColumnAndRow(0, $current_row, 8, $current_row);
			$current_row ++;
		}
	}
	
	
	// recursion function
	private function showLine($sheet, $leveldata, &$current_row, &$sum, &$sumItog, &$sumLevelUp){		
		
		if($leveldata['level'] == 0){
			$sheet->mergeCellsByColumnAndRow(1, $current_row, 2, $current_row);
			$sheet->setCellValueByColumnAndRow(1, $current_row, $leveldata['code'] . " - " . $leveldata['title']);
			$sheet->getStyleByColumnAndRow(1, $current_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle("B$current_row:J$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8EEEA');													
		} else if($leveldata['level'] == 1){
			$sheet->setCellValueByColumnAndRow(1, $current_row, $leveldata['code'] . " - " . $leveldata['title']);
			$sheet->getStyleByColumnAndRow(1, $current_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyleByColumnAndRow(1, $current_row)->getFont()->setBold(true);
		} else if($leveldata['level'] == 2){ 
			// Speciality
			$sheet->setCellValueByColumnAndRow(1, $current_row, $leveldata['title']);
			$sheet->setCellValueByColumnAndRow(2, $current_row, $leveldata['code']);
			$sheet->getStyleByColumnAndRow(1, $current_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			foreach (array('CS','CEXIT','CENTER','MOVEOUT','MOVEIN', null) as $colnum => $column) {
				if($column)
					$sheet->setCellValueByColumnAndRow($colnum + 2, $current_row, array_key_exists($column, $leveldata) ? $leveldata[$column] : 0);
			}
			$sheet->setCellValueByColumnAndRow(7, $current_row, "=C$current_row-D$current_row+E$current_row-F$current_row+G$current_row"); // на конец года
			$sheet->setCellValueByColumnAndRow(8, $current_row, "=((C$current_row*11)+H$current_row)/12"); // Середньорічна чисельність
			
			
			// Номер строки вносим в итого по вузам
			if(array_key_exists($leveldata['code'], $sumItog))
				array_push($sumItog[$leveldata['code']], $current_row);
            else
				$sumItog[$leveldata['code']] = array($current_row);
            
            // Номер строки вносим в итого по группе                      
            if(array_key_exists($leveldata['code'], $sum))
            	array_push($sum[$leveldata['code']], $current_row);
            else
            	$sum[$leveldata['code']] = array($current_row);            
		}		
		
		// Номер строки вносим в сумму по разделу
		$sumLevelUp[] = $current_row;		
		
		$formularow = $current_row;
		 		
		$current_row ++;
		
		if (isset($leveldata['DATA'])){
			$levelSize = count($leveldata['DATA']);
			$sheet->insertNewRowBefore($current_row + 1, $levelSize);
			$sumLevel = array();
			foreach ($leveldata['DATA'] as $lnum => $levelnext) {
				$this->showLine($sheet, $levelnext, $current_row, $sum, $sumItog, $sumLevel);  // recursion
			}
			
			// заполнение формул с 4 по 10 колонку
			$rCnt = count($sumLevel);
			if ($rCnt > 0){
				$rFirst = $sumLevel[0];
				$rLast = $sumLevel[$rCnt - 1];
				$rSum = ($rLast-$rFirst == $rCnt - 1) && ($rCnt > 1);				
				for ($col=3; $col<=9; $col++){
					$colStr = PHPExcel_Cell::stringFromColumnIndex($col);				     
					$sheet->setCellValueByColumnAndRow($col, $formularow, 
						$rSum ? "=SUM(" . $colStr . $rFirst . ":" . $colStr . $rLast . ")" : "=$colStr" . implode("+$colStr", $sumLevel));				
				}
			}	
		}
					
	}

	public function getWorkbook() {
				
		$data = $this->getReportData();						
		$data = $this->formtable($data);
				

		$xls_reader = new PHPExcel_Reader_Excel2007();
		$workbook = $xls_reader->load(APPLICATION_PATH . 'modules/stat/templates/report11.xlsx');
		$sheet = $workbook->getActiveSheet();
		
		$foreighnStr = $this->getAddValue($this->params['countrytype'], '_ADD_ORIGINID') == 'УКР' ? '(громадяни України)' : '(іноземні громадяни) ';
		
		$eduBaseStr = $this->getAddValue($this->params['edubase'], '_ADD_ORIGINID') == 'Б' ? 'держзамовленням' : 'контрактом ' . $foreighnStr;				
		
		$footerStructure = $this->getFPOListeners(); // Справочник категорий - слушатели	;				
		
		$endDate = new DateTime($this->getAddValueDate($this->params['period'], '_ADD_ENDDATE'));		
		$selectedYear = $endDate->format('Y');


		$sheet->setCellValue('A6', sprintf($sheet->getCell('A6')->getValue(), $eduBaseStr, $selectedYear));

				
		$current_row = 13;
//		$sheet->getStyle("B17")->getNumberFormat()->setFormatCode('dd.mm.yyyy'); // Если просто задано в шаблоне, то не работает в HTML

		$sheet->insertNewRowBefore($current_row+1, count($data) * 2  - 1);

		$sum1 = array();     // [Код специальности] => [строка, строка, ...]
		$sum2 = array();     // [Код специальности] => [строка, строка, ...]
		$sumItog = array();  // [Код специальности] => [строка, строка, ...]		
		
		$lastGroup = -1;
		foreach ($data as $vuznum => $vuz) {
			$group = $vuz['group'];		

			// Footer группы вузов 0
			
			if ($group == 1 && $lastGroup == -1){
				$lastGroup = $group;
				$this->showFooter($sheet, $footerStructure, $current_row, $sum1, false, true);
			}

			$sheet->setCellValueByColumnAndRow(0, $current_row, $vuznum + 1);
			$sheet->setCellValueByColumnAndRow(1, $current_row, $vuz['title']);

			// Декорирование строки с названием вуза
			$workbook->getActiveSheet()->mergeCellsByColumnAndRow(1, $current_row, 8, $current_row);
			$next_row = $current_row + 1;
			$sheet->insertNewRowBefore($next_row + 1, 2);
			$sheet->getStyle("A$current_row:I$next_row")->getFont()->setBold(true);
			$sheet->getStyle("A$current_row:I$next_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8EEEA');
			$sheet->getStyle("B$current_row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			// Строка с годом и суммами по столбцам. Ее номер - $vuzrow
			$current_row ++;
			$sheet->setCellValueByColumnAndRow(1, $current_row, $selectedYear);
			$formularow = $current_row;  // Запоминаем строку с суммами по вузу!!!

			// Строка "в тому числі" и далее пустая
			$current_row ++;
			$sheet->setCellValueByColumnAndRow(1, $current_row, 'в тому числі');

			$current_row ++;
			$sheet->insertNewRowBefore($current_row+1, count($vuz['DATA']));
			
			$sumLevel = array();
			
			foreach ($vuz['DATA'] as $numL0 => $dataL0) {

				if ($group == 0) 				
					$this->showLine($sheet, $dataL0, $current_row, $sum1, $sumItog, $sumLevel); 
				else
					$this->showLine($sheet, $dataL0, $current_row, $sum2, $sumItog, $sumLevel);
				
			}
			// заполнение формул с 4 по 10 колонку
			for ($col=2; $col<=8; $col++){
				$colStr = PHPExcel_Cell::stringFromColumnIndex($col);
				$sheet->setCellValueByColumnAndRow($col, $formularow, "=$colStr" . implode("+$colStr", $sumLevel));
			}
			 
			$current_row ++;
						
		}  // $vuz
		
		// Footer группы вузов 0 (если второй группы вузов нет, то сработат здесь "всего" (по группе))
		if ($group == 0 && $lastGroup == -1){ 
			$lastGroup = $group;
				$this->showFooter($sheet, $footerStructure, $current_row, $sum1);
		}else
		// Footer группы вузов 1
		if ($group == 1 && $lastGroup == 1){			
			$this->showFooter($sheet, $footerStructure, $current_row, $sum2);
		}
				
		// Общий итог:
		if (!@$this->params['establishment']){
			$this->showFooter($sheet, $footerStructure, $current_row, $sumItog, true);
		}

		$sheet->removeRow($current_row, 1); // Удаляем лишнюю строку, которая была в шаблоне

		return $workbook;
				
	}

	protected function formtable($data) {
		$vuzes = array ();
		
		if (!empty($data)) {

			$this->guide_vuz = $this->getEstablishments('GUIDE_STRUCTURE_FPO', 'SORTORDER');
//			$this->guide_edudir = $this->getGuideItems('T_EDUDIRECTION', false, false, 'CT.SORTORDER', 'SORTORDER');
//			$this->guide_edulevel = $this->getGuideItems('T_EDULEVEL', false, false, 'CT.SORTORDER', 'SORTORDER');
			$this->guide_category = $this->getFPOListeners(); // Справочник категорий - слушатели						
			
			$lastEstNum = -1;			
			$lastCategory = -1; // Категория слушателей	
			
			$rowEst = -1;
			$rowCat = -1; // Категория слушателей	

			foreach($data as &$datarow) {
					$curEstNum = $datarow['ESTABLISHMENT_NUM'];
					if ($lastEstNum != $curEstNum) {
						$rowEst ++;												
						$rowCat = -1; // Категория слушателей						
						$lastEstNum = $curEstNum;						
						$lastCategory = -1; // Категория слушателей
						
						$vuzes[$rowEst] = array(
                			'title' => $this->guide_vuz[$curEstNum],
                			'group' => $datarow['GRP'],
                			//'datareportid'=> $datarow['DATAREPORTID'],
                			'DATA' => array());
						$vuzData = &$vuzes[$rowEst]['DATA'];
					}

					// Категория слушателей
					$curCategory = $datarow["CATEGORY_SORTORDER"];
					if ($lastCategory != $curCategory) {
						$rowCat ++;
						$lastCategory = $curCategory;
												
						$vuzData[$rowCat] = array(						
							'code' => $curCategory,
							'title' => $this->guide_category[$curCategory], // Title L2 Speciality
							'level' => 2
						);
																		
						$categoryData = &$vuzData[$rowCat];
					}
					
					
					$factor_name = $datarow["KIND"];
//					if($factor_name == 'CENTER' || $factor_name == 'CEXIT') $factor_name .= $datarow["EDULEVEL_NUM"];
					$val = $datarow ["VAL"];
					$categoryData[$factor_name] =  array_key_exists($factor_name, $categoryData) ? $categoryData[$factor_name] + $val : $val;
					
					
					//echo '<br/><br/>factor_name:<br/>';
					//var_dump($factor_name);
					//echo '<br/><br/>categoryData:<br/>';
					//var_dump($categoryData);
					
			}

			//echo '<br/><br/>================ array vuzes:<br/>';
			//var_dump($vuzes);
			
		}

		return $vuzes;
	}
	
}