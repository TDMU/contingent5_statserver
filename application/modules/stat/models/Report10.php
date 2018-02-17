<?php

/*
*     ФПО План контингента  
*/



class Stat_Model_Report10 extends Stat_Model_Reports {

	public function getForm() {
		$form = new Zend_Form();
		$form->setMethod('post');
		$form->setDecorators(array(array('ViewScript', array('viewScript' => 'reports/report10Form.phtml'))));

		$items = $this->getPeriodItems('REQ_FPO_GR1'); // report NODE_KEY
		$e = new Zend_Form_Element_Select('period',
		array('label' => 'Період',
		'multiOptions' => $items,
		'required' => true,
		'style' => 'width: 300px'));
		$form->addElement($e);

		$items = $this->getGuideItems ('T_FPO_CATEGORY');
		$e = new Zend_Form_Element_Select('reportfpocategory',
		array('label' => 'Категорія',
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

		$excelAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'reports', 'action' => 'excel', 'report' => 10));
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
		
		$sql = "select first 10000
iif((cast(INFO_ESTAB.CODE as integer) in (2001,2002,2003,2004,2005,2006,2007,2008,2009,2010,2011,2012,2013,2014,2031)), 0, 1) as GRP,		
GUIDE_ESTAB.SORTORDER as ESTABLISHMENT_NUM,

SPG_SPECIALITY.PARAMVALUE as L2_SPECIALITY_NODEID,

(select SPECCOD.VAL
 from V_ADD_VALUES SPECCOD
 where SPECCOD.NODEID = cast(SPG_SPECIALITY.PARAMVALUE as bigint)
   and SPECCOD.FIELDNAME = '_ADD_CODE'
) as L2_SPECIALITY_CODE_STR,

iif( CT_SPEC1.T_NODE_KEY = 'T_FPO_SPECIALITY_L0', SPECCOD1.VAL,
  iif(CT_SPEC0.T_NODE_KEY = 'T_FPO_SPECIALITY_L0', SPECCOD0.VAL, -1)
  ) as L0_SPECIALITY_CODE_STR,

iif( CT_SPEC1.T_NODE_KEY = 'T_FPO_SPECIALITY_L0', SPECCOD1.NODEID,
  iif(CT_SPEC0.T_NODE_KEY = 'T_FPO_SPECIALITY_L0', SPECCOD0.NODEID, -1)
  ) as L0_SPECIALITY_NODEID,

iif( CT_SPEC1.T_NODE_KEY = 'T_FPO_SPECIALITY_L1', SPECCOD1.VAL,
  iif(CT_SPEC0.T_NODE_KEY = 'T_FPO_SPECIALITY_L1', SPECCOD0.VAL, -1)
  ) as L1_SPECIALITY_CODE_STR,

iif( CT_SPEC1.T_NODE_KEY = 'T_FPO_SPECIALITY_L1', SPECCOD1.NODEID,
  iif(CT_SPEC0.T_NODE_KEY = 'T_FPO_SPECIALITY_L1', SPECCOD0.NODEID, -1)
  ) as L1_SPECIALITY_NODEID,

trim(leading 'STAT_KIND_' from GUIDE_KIND.NODE_KEY) as KIND,
cast(SD.DATAVALUE as integer) as VAL

--SD.DATAREPORTID, SD.PARAMGROUPID, SD.KINDID

from STAT_DATA SD
inner join TMP REQ
on (REQ.ID = SD.DATAREPORTID)

inner join V_STAT_PARAMGROUPS SPG_FPO_CATEGORY
  on (SPG_FPO_CATEGORY.PARAMGROUPID = SD.PARAMGROUPID)
    and (SPG_FPO_CATEGORY.PARAMNAME = 'STAT_PARAM_FPO_CATEGORYID')
    and (SPG_FPO_CATEGORY.PARAMVALUE = '{$this->params['reportfpocategory']}')

inner join V_ADD_VALUES_REF ADD_ESTABLISHMENTID
  on (ADD_ESTABLISHMENTID.NODEID = cast(SD.DATAREPORTID as varchar(20)))
    and (ADD_ESTABLISHMENTID.FIELDNAME = 'STRUCTUREID')

inner join CONTENTTREE GUIDE_ESTAB
on (GUIDE_ESTAB.NODEID = cast(ADD_ESTABLISHMENTID.VAL as bigint))

inner join INFO_STRUCTURE_ITEM INFO_ESTAB
on (INFO_ESTAB.NODEID = GUIDE_ESTAB.NODEID)

inner join V_STAT_PARAMGROUPS SPG_SPECIALITY
  on (SPG_SPECIALITY.PARAMGROUPID = SD.PARAMGROUPID)
    and (SPG_SPECIALITY.PARAMNAME = 'STAT_PARAM_FPO_SPECIALITYID')

inner join V_CONTENT_TYPE CT_SPEC
  on CT_SPEC.NODEID = cast(SPG_SPECIALITY.PARAMVALUE as bigint)
inner join V_CONTENT_TYPE CT_SPEC1
  on CT_SPEC1.NODEID = CT_SPEC.PARENTID
inner join V_CONTENT_TYPE CT_SPEC0
  on CT_SPEC0.NODEID = CT_SPEC1.PARENTID

left join V_ADD_VALUES SPECCOD1
   on SPECCOD1.NODEID = CT_SPEC1.NODEID
     and SPECCOD1.FIELDNAME = '_ADD_CODE'
left join V_ADD_VALUES SPECCOD0
   on SPECCOD0.NODEID = CT_SPEC0.NODEID
     and SPECCOD0.FIELDNAME = '_ADD_CODE'    
    
inner join CONTENTTREE GUIDE_KIND
on (GUIDE_KIND.NODEID = SD.KINDID)" .

($includeEdubasis ? $edubasisJoin : "") .
($includeCountryType ? $countryTypeJoin : "") .

" where 1=1 " .
(@$this->params['establishment'] ? " and ADD_ESTABLISHMENTID.VAL = " . $this->params['establishment'] : "") .

" order by GRP, ESTABLISHMENT_NUM, L0_SPECIALITY_CODE_STR, L1_SPECIALITY_CODE_STR, L2_SPECIALITY_CODE_STR";

		$res = $this->_db->fetchAll($sql);

		$this->_db->commit ();
		
		return $res; 
	}
	
	protected function getFooterStructure($fpocategoryid) {
		$sql = "select 
(select VAV.VAL from V_ADD_VALUES VAV where VAV.NODEID = VCT.NODEID and VAV.FIELDNAME = '_ADD_CODE') as CODE,
VCT.T_NODE_KEY as TYPE_NODE_KEY, -- info about level
VCT.TITLE_WO_CODE as TITLE, VCT.NODEID as SPECID 
from CONTENTTREE CT  -- Это справочник
inner join V_CONTENT_TYPE VCT -- это элементы справочника
on VCT.PATH starting with CT.FULL_PATH
where VCT.VISIBLE = 1
and CT.NODE_KEY = (select VAV.VAL from V_ADD_VALUES VAV where VAV.NODEID = $fpocategoryid and VAV.FIELDNAME = 'SPECIALITY_GUIDE_NODEKEY')
-- and VCT.T_NODE_KEY = 'T_FPO_SPECIALITY'
order by 1";
		
		return $this->_db->fetchAll($sql);
		
	}
	
	
	// recursion function
	private function showFooterLine($sheet, $leveldata, &$current_row, &$sumRows, &$sumLevelUp, $isGrandTotal){		
		
		if($leveldata['level'] == 0){			
			$sheet->mergeCellsByColumnAndRow(1, $current_row, 2, $current_row);			
			$sheet->setCellValueByColumnAndRow(1, $current_row, $isGrandTotal ? 'ЗА ВСІМА ВНЗ' : 'РАЗОМ');						
			$sheet->getStyleByColumnAndRow(1, $current_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sheet->getStyle("B$current_row:J$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8EEEA');
		} else if($leveldata['level'] == 1){
			$sheet->mergeCellsByColumnAndRow(1, $current_row, 2, $current_row);
			$sheet->setCellValueByColumnAndRow(1, $current_row, $leveldata['code'] . " - " . $leveldata['title']);
			$sheet->getStyleByColumnAndRow(1, $current_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle("B$current_row:J$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8EEEA');
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
			/*
			echo '<br/><br/>';
			var_dump($leveldata['code']); echo '<br/>';
			var_dump($sumRows); echo '<br/>';
			var_dump($v);
			*/
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
	
	private function showFooter($sheet, $footer_Structure, &$current_row, &$sumRows, $isGrandTotal = false, $isAddDelimiter = true){		
		if (!$isGrandTotal) {
			$sheet->insertNewRowBefore($current_row, 1);
			$sheet->getRowDimension($current_row)->setRowHeight(0.75); // Разделительная линия перед ВСЕГО
			$current_row ++;
		}
						
		//$sheet->insertNewRowBefore($current_row+1, count($footer_Structure) + 1);
										
		$sumLevelUp = array();	
		$sheet->insertNewRowBefore($current_row+1, 1);
		
		$this->showFooterLine($sheet, $footer_Structure, $current_row, $sumRows, $sumLevelUp, $isGrandTotal);
		
		//$current_row ++;
					
		if (!$isGrandTotal && $isAddDelimiter) {
			// Разделительная строка между группами вузов
			$sheet->insertNewRowBefore($current_row + 1, 1);
			$sheet->mergeCellsByColumnAndRow(0, $current_row, 9, $current_row);
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
					$sheet->setCellValueByColumnAndRow($colnum + 3, $current_row, array_key_exists($column, $leveldata) ? $leveldata[$column] : 0);
			}
			$sheet->setCellValueByColumnAndRow(8, $current_row, "=D$current_row-E$current_row+F$current_row-G$current_row+H$current_row"); // на конец года
			$sheet->setCellValueByColumnAndRow(9, $current_row, "=((D$current_row*11)+I$current_row)/12"); // Середньорічна чисельність
			
			
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
			
			// заполнение формул с 3 по 9 колонку
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
		$workbook = $xls_reader->load(APPLICATION_PATH . 'modules/stat/templates/report10.xlsx');
		$sheet = $workbook->getActiveSheet();

		$foreighnStr = $this->getAddValue($this->params['countrytype'], '_ADD_ORIGINID') == 'УКР' ? '(громадяни України)' : '(іноземні громадяни) ';
		
		$eduBaseStr = $this->getAddValue($this->params['edubase'], '_ADD_ORIGINID') == 'Б' ? 'держзамовленням' : 'контрактом ' . $foreighnStr;				
		
		$footerStructure = $this->getFooterStructure($this->params['reportfpocategory']);
		$footerStructure = $this->formtableFooter($footerStructure);
		
		$fpoCategory = array(
		  '200606' => 'аспірантів без відриву від виробництва', 
		  '200607' => 'аспірантів з відривом від виробництва',
		  '200608' => 'докторантів',
		  '200609' => 'інтернів',
		  '200610' => 'магістрів',
		  '200611' => 'клінічних ординаторів',
		  '200612' => 'слухачів передатестаційних циклів',
		  '200613' => 'слухачів циклів тематичного удосконалення',
		  '200614' => 'слухачів циклів спеціалізації',
		  '200615' => 'слухачів циклів стажування',
          '207181' => 'аспірантів очної денної форми навчання',
          '207182' => 'аспірантів очної вечірньої форми навчання',
          '207183' => 'аспірантів заочної форми навчання');
		$fpoCategoryStr = $fpoCategory[$this->params['reportfpocategory']] . ' ';
		
		$endDate = new DateTime($this->getAddValueDate($this->params['period'], '_ADD_ENDDATE'));		
		$selectedYear = $endDate->format('Y');


		$sheet->setCellValue('A6', sprintf($sheet->getCell('A6')->getValue(), $fpoCategoryStr, $eduBaseStr, $selectedYear));

				
		$current_row = 13;
//		$sheet->getStyle("B17")->getNumberFormat()->setFormatCode('dd.mm.yyyy'); // Если просто задано в шаблоне, то не работает в HTML

		$sheet->insertNewRowBefore($current_row+1, count($data) * 2  - 1);

		$sum1 = array();     // [Код специальности] => [строка, строка, ...]
		$sum2 = array();     // [Код специальности] => [строка, строка, ...]
		$sumItog = array();  // [Код специальности] => [строка, строка, ...]		
		
		$group = null; //set default 2017 to prevent php notice
        $lastGroup = -1;
		foreach ($data as $vuznum => $vuz) {
			$group = $vuz['group'];		

			// Footer группы вузов 0
			if ($group == 1 && $lastGroup == -1){
				$lastGroup = $group;
				$this->showFooter($sheet, $footerStructure[0], $current_row, $sum1, false, true);
			}

			$sheet->setCellValueByColumnAndRow(0, $current_row, $vuznum + 1);
			$sheet->setCellValueByColumnAndRow(1, $current_row, $vuz['title']);

			// Декорирование строки с названием вуза
			$workbook->getActiveSheet()->mergeCellsByColumnAndRow(1, $current_row, 9, $current_row);
			$next_row = $current_row + 1;
			$sheet->insertNewRowBefore($next_row + 1, 2);
			$sheet->getStyle("A$current_row:J$next_row")->getFont()->setBold(true);
			$sheet->getStyle("A$current_row:J$next_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8EEEA');
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
			for ($col=3; $col<=9; $col++){
				$colStr = PHPExcel_Cell::stringFromColumnIndex($col);
				$sheet->setCellValueByColumnAndRow($col, $formularow, "=$colStr" . implode("+$colStr", $sumLevel));
			} 
			$current_row ++;
						
		}  // $vuz
		
		// Footer группы вузов 0 (если второй группы вузов нет, то сработат здесь "всего" (по группе))
		if ($group == 0 && $lastGroup == -1){ 
			$lastGroup = $group;
				$this->showFooter($sheet, $footerStructure[0], $current_row, $sum1);
		}else
		// Footer группы вузов 1
		if ($group == 1 && $lastGroup == 1){			
			$this->showFooter($sheet, $footerStructure[0], $current_row, $sum2);
		}
				
		// Общий итог:
		if (!@$this->params['establishment']){
			$this->showFooter($sheet, $footerStructure[0], $current_row, $sumItog, true);
		}

		$sheet->removeRow($current_row, 1); // Удаляем лишнюю строку, которая была в шаблоне
		
		//$sheet->insertNewRowBefore($current_row + 1, 6);
		$sheet->mergeCellsByColumnAndRow(1, $current_row, 9, $current_row);
		$sheet->setCellValueByColumnAndRow(1, $current_row, "Примітка:"); 
		$current_row ++;
		$sheet->mergeCellsByColumnAndRow(1, $current_row, 9, $current_row);
		$sheet->setCellValueByColumnAndRow(1, $current_row, "* - Показники з 01.01. по 31.12. календарний рік"); 
		$current_row ++;
		$sheet->mergeCellsByColumnAndRow(1, $current_row, 9, $current_row);
		$sheet->setCellValueByColumnAndRow(1, $current_row, "** - Особи, які подали документи до спеціалізованої вченої ради (що підтверджено довідкою)"); 
		$current_row ++;
		$sheet->mergeCellsByColumnAndRow(1, $current_row, 9, $current_row);
		$sheet->setCellValueByColumnAndRow(1, $current_row, "*** - Особи, які зараховані відповідно з наказом ВНЗ та погодженням МОЗ України"); 
		$current_row ++;
		$sheet->mergeCellsByColumnAndRow(1, $current_row, 9, $current_row);
		$sheet->setCellValueByColumnAndRow(1, $current_row, "**** - Особи, які відраховані відповідно з наказом ВНЗ та погодженням МОЗ України"); 
		$current_row ++;
		$sheet->mergeCellsByColumnAndRow(1, $current_row, 9, $current_row);
		$sheet->setCellValueByColumnAndRow(1, $current_row, "Позиції 3,4,5,6,7 заповнюються ВНЗ"); 
		$current_row ++;

		return $workbook;
				
	}

	protected function formtable($data) {
		$vuzes = array ();
		
		if (!empty($data)) {

			$this->guide_vuz = $this->getEstablishments('GUIDE_STRUCTURE_FPO', 'SORTORDER');
//			$this->guide_edudir = $this->getGuideItems('T_EDUDIRECTION', false, false, 'CT.SORTORDER', 'SORTORDER');
//			$this->guide_edulevel = $this->getGuideItems('T_EDULEVEL', false, false, 'CT.SORTORDER', 'SORTORDER');
			$this->guide_fpospeciality = $this->getFpoSpecialities($this->params['reportfpocategory']);			//	'STAT2_GUIDE_FPO_SPECIALITY_1'		
			
			$lastEstNum = -1;
			$lastL0SpecialityCode = -1;
			$lastL1SpecialityCode = -1;
			$lastL2SpecialityCode = -1; // speciality
			
			$rowEst = -1;
			$rowL0 = -1;
			$rowL1 = -1;
			$rowL2 = -1; // speciality	

			foreach($data as &$datarow) {
					$curEstNum = $datarow['ESTABLISHMENT_NUM'];
					if ($lastEstNum != $curEstNum) {
						$rowEst ++;						
						$rowL0 = -1;
						$rowL1 = -1;
						$rowL2 = -1; // speciality
						unset($L0Data);
						unset($L1Data);
						$lastEstNum = $curEstNum;
						$lastL0SpecialityCode = -1;
						$lastL1SpecialityCode = -1;
						$lastL2SpecialityCode = -1; // speciality
						
						$vuzes[$rowEst] = array(
                			'title' => $this->guide_vuz[$curEstNum],
                			'group' => $datarow['GRP'],
                			//'datareportid'=> $datarow['DATAREPORTID'],
                			'DATA' => array());
						$vuzData = &$vuzes[$rowEst]['DATA'];
					}
					
					// L0
					$curL0SpecialityCode = $datarow['L0_SPECIALITY_CODE_STR'];
					if ($lastL0SpecialityCode != $curL0SpecialityCode) {												
						$rowL0 ++;
						$rowL1 = -1;
						$rowL2 = -1; // speciality
						unset($L1Data);
						$lastL1SpecialityCode = -1;
						$lastL2SpecialityCode = -1; // speciality
						$lastL0SpecialityCode = $curL0SpecialityCode;						
						$vuzData[$rowL0] = array(
							'code' => $curL0SpecialityCode,
							'title' => $this->guide_fpospeciality[$curL0SpecialityCode], // Title L0
							'level' => 0,
							'DATA' => array()
						);
						$L0Data = &$vuzData[$rowL0]['DATA'];
					}					
					
					// L1
					$curL1SpecialityCode = $datarow['L1_SPECIALITY_CODE_STR'];
					if ($lastL1SpecialityCode != $curL1SpecialityCode) {
						$rowL1 ++;
						$rowL2 = -1; // speciality
						$lastL2SpecialityCode = -1; // speciality
						$lastL1SpecialityCode = $curL1SpecialityCode;
						$L0Data[$rowL1] = array(
							'code' => $curL1SpecialityCode,
							'title' => $this->guide_fpospeciality[$curL1SpecialityCode], // Title L1
							'level' => 1,
							'DATA' => array()
						);
						$L1Data = &$L0Data[$rowL1]['DATA'];
					}
					
					// Speciality
					$curL2SpecialityCode = $datarow["L2_SPECIALITY_CODE_STR"];
					if ($lastL2SpecialityCode != $curL2SpecialityCode) {
						$rowL2 ++;
						$lastL2SpecialityCode = $curL2SpecialityCode;
						
						// parent selector 
						if (isset($L1Data) && (!is_null($L1Data))) $parentdata = &$L1Data; 
						else if (isset($L0Data) && (!is_null($L0Data)) ) $parentdata = &$L0Data;
						else $parentdata = &$vuzData;
												
						$parentdata[$rowL2] = array(						
							'code' => $curL2SpecialityCode,
							'title' => $this->guide_fpospeciality[$curL2SpecialityCode], // Title L2 Speciality
							'level' => 2
						);
																		
						$specialityData = &$parentdata[$rowL2];
					}
					
					
					$factor_name = $datarow["KIND"];
//					if($factor_name == 'CENTER' || $factor_name == 'CEXIT') $factor_name .= $datarow["EDULEVEL_NUM"];
					$val = $datarow ["VAL"];
					$specialityData[$factor_name] = array_key_exists($factor_name, $specialityData) ? $specialityData[$factor_name] + $val : $val;
					
					/*
					echo '<br/><br/>factor_name:<br/>';
					var_dump($factor_name);
					echo '<br/><br/>specialityData:<br/>';
					var_dump($specialityData);
					*/
			}

			//echo '<br/><br/>================ array vuzes:<br/>';
			//var_dump($vuzes);
			
		}

		return $vuzes;
	}

	
	
	/*
	 * (select VAV.VAL from V_ADD_VALUES VAV where VAV.NODEID = VCT.NODEID and VAV.FIELDNAME = '_ADD_CODE') as CODE,
	 *  VCT.T_NODE_KEY as TYPE_NODE_KEY, -- info about level
	 *  VCT.TITLE_WO_CODE as TITLE
	 */
		
	protected function formtableFooter($data) {
//		$footdata = array ();
		
		$rowL1 = -1;
		$rowL2 = -1;
		$rowL3 = -1; // speciality
			
		$footdata[0] = array(
			'code' => "",
			'title' => "Итог",
			'specid' => "",
			'level' => 0,
			'DATA' => array()
		);
		$L0Data =& $footdata[0]['DATA']; // ROOT
			
		if (!empty($data)) {
						
			foreach($data as &$datarow) {				
				
				$typeNodeKey = $datarow['TYPE_NODE_KEY'];
				
				// L1
				if ($typeNodeKey == "T_FPO_SPECIALITY_L0") {
					$rowL1 ++;
					unset($L2Data);
					$rowL2 = -1;
					$rowL3 = -1;
					$L0Data[$rowL1] = array(
											'code' => $datarow['CODE'],
											'title' => $datarow['TITLE'], // Title L0
											'specid' => $datarow['SPECID'],
											'level' => 1,
											'DATA' => array()
					);
					$L1Data =& $L0Data[$rowL1]['DATA'];
				}
					
				// L2
				if ($typeNodeKey == "T_FPO_SPECIALITY_L1") {
					$rowL2++;
					$rowL3 = -1;
					$L1Data[$rowL2] = array(
											'code' => $datarow['CODE'],
											'title' => $datarow['TITLE'], // Title L0
											'specid' => $datarow['SPECID'],
											'level' => 2,
											'DATA' => array()
					);
					$L2Data =& $L1Data[$rowL2]['DATA'];
				}
					
				// L3 Speciality
				if ($typeNodeKey == "T_FPO_SPECIALITY") {
					
					// parent selector
					if (isset($L2Data) && (!is_null($L2Data))) $parentdata =& $L2Data;
					else if (isset($L1Data) && (!is_null($L1Data)) ) $parentdata =& $L1Data;
					else $parentdata =& $L0Data;
					
					$rowL3++;
					$parentdata[$rowL3] = array(
												'code' => $datarow['CODE'],
												'title' => $datarow['TITLE'], // Title L0
												'specid' => $datarow['SPECID'],
												'level' => 3												
					);					
				}				
			}
			
		//	echo '<br/><br/>================ array $footdata:<br/>';
		//	var_dump($footdata);
		}
		return $footdata;
	}
}