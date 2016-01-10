<?php
//
// Отчет выводит данные, поданные вузами в том виде, как они были поданы. Отчет для контроля поданных данных
//  

class Stat_Model_Report9 extends Stat_Model_Reports {
	
	public $isExcelDifferentFromHTML = true;

	public function getForm() {
		$form = new Zend_Form();
		$form->setMethod('post');
		$form->setDecorators(array(array('ViewScript', array('viewScript' => 'reports/report9Form.phtml'))));

		$items = $this->getPeriodItems('REQ_MOZ_CONTMOVEMENT_YEAR_SPEC');
		$e = new Zend_Form_Element_Select('period',
		array('label' => 'Рік',
		'multiOptions' => $items,
		'required' => true,
		'style' => 'width: 80px'));
		$form->addElement($e);

		$refreshAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'page', 'action' => 'show'));
		$e = new Zend_Form_Element_Submit('refresh', array('label'=>'Обновити', 'onclick'=>"document.forms[0].action='{$refreshAct}'"));
		$form->addElement($e);

		$excelAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'reports', 'action' => 'excel', 'report' => 9));
		$e = new Zend_Form_Element_Submit('excel', array('label'=>'Excel', 'onclick'=>"document.forms[0].action='{$excelAct}'"));
		$form->addElement($e);

		$form->setElementDecorators(array('ViewHelper', 'Errors'));

		return $form;
	}

	protected function getReportData() {

		$this->_db->beginTransaction ();

		$sql = "insert into TMP
select REQ.NODEID
from CONTENTTREE REQ
where REQ.PARENTID = ?";

		$this->_db->query($sql, $this->params['period']);

		$sql = "select first 10000 
				SD.PARAMGROUPID as PARAMGROUPID,
  				GUIDE_ESTAB.NODEID as ESTABLISHMENTID,								
								
				GUIDE_ESTAB.SORTORDER as ESTABLISHMENT_NUM,
				trim(leading 'STAT_KIND_' from GUIDE_KIND.NODE_KEY) as KIND,
				
				(select GUIDE_SPECIALITY.SORTORDER
				from CONTENTTREE GUIDE_SPECIALITY
				where (GUIDE_SPECIALITY.NODEID = cast(SPG_SPECIALITY.PARAMVALUE as bigint))) as SPECIALITY_NUM,
				
				CT_EDUFORM.TITLE as EDUFORMTITLE,
				CT_EDUBASIS.TITLE as EDUBASISTITLE,
				CT_COUNTRYTYPE.TITLE as COUNTRYTYPETITLE, 
				
				CT_PLANKIND.SORTORDER as REPORTKIND_NUM,
				
				cast(SD.DATAVALUE as integer) as VAL
				
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
				
				inner join V_STAT_PARAMGROUPS SPG_REPORTKIND
				  on (SPG_REPORTKIND.PARAMGROUPID = SD.PARAMGROUPID)
				    and (SPG_REPORTKIND.PARAMNAME = 'STAT_PARAM_REPORTPLANKIND')
				inner join CONTENTTREE CT_PLANKIND
				  on (CT_PLANKIND.NODEID = SPG_REPORTKIND.PARAMVALUE)    
				    
				inner join V_STAT_PARAMGROUPS SPG_EDUFORMID
				  on (SPG_EDUFORMID.PARAMGROUPID = SD.PARAMGROUPID)
				    and (SPG_EDUFORMID.PARAMNAME = 'STAT_PARAM_EDUFORMID')
				inner join CONTENTTREE CT_EDUFORM
				  on (CT_EDUFORM.NODEID = SPG_EDUFORMID.PARAMVALUE)    
				  
				inner join V_STAT_PARAMGROUPS SPG_EDUBASISID
				  on (SPG_EDUBASISID.PARAMGROUPID = SD.PARAMGROUPID)
				    and (SPG_EDUBASISID.PARAMNAME = 'STAT_PARAM_EDUBASISID')
				inner join CONTENTTREE CT_EDUBASIS
				  on (CT_EDUBASIS.NODEID = SPG_EDUBASISID.PARAMVALUE)
				
				inner join V_STAT_PARAMGROUPS SPG_COUNTRYTYPEID
				  on (SPG_COUNTRYTYPEID.PARAMGROUPID = SD.PARAMGROUPID)
				    and (SPG_COUNTRYTYPEID.PARAMNAME = 'STAT_PARAM_COUNTRYTYPEID')
				inner join CONTENTTREE CT_COUNTRYTYPE
				  on (CT_COUNTRYTYPE.NODEID = SPG_COUNTRYTYPEID.PARAMVALUE)   
				  
				inner join CONTENTTREE GUIDE_KIND
				  on (GUIDE_KIND.NODEID = SD.KINDID)" .  
				"where 1=1 " .
		(@$this->params['establishment'] ? " and ADD_ESTABLISHMENTID.VAL = " . $this->params['establishment'] : "") .
				"order by ESTABLISHMENT_NUM, SPECIALITY_NUM, EDUFORMTITLE, EDUBASISTITLE, COUNTRYTYPETITLE";

		$res = $this->_db->fetchAll($sql);

		$this->_db->commit ();

//		echo '<br/><br/>data:<br/>';
//		var_dump($res);
//		echo '<br/><br/>period:<br/>';
//		var_dump($this->params['period']);
		
		return $res;
	}


	// Доп. данные, поданные вузом, на конец предыдущего года для сравнение на начала отчетного года
	protected function getReportDataAdd(){
		$sql = "select
        AV_STRUCTUREID.VAL as STRUCTUREID,
        SD.PARAMGROUPID as PARAMGROUPID,  

        sum(decode(
            CT_KIND.NODE_KEY,
            'STAT_KIND_CS', cast(SD.DATAVALUE as integer),
            'STAT_KIND_CENTER', cast(SD.DATAVALUE as integer),
            'STAT_KIND_CEXIT', 0 - cast(SD.DATAVALUE as integer),
            'STAT_KIND_MOVEIN', cast(SD.DATAVALUE as integer),
            'STAT_KIND_MOVEOUT', 0 - cast(SD.DATAVALUE as integer),
            0
          )
        ) as DATAVALUE
        from CONTENTTREE CT_REPORT
        inner join CONTENTTREE CT_PERIOD
          on CT_PERIOD.NODEID = CT_REPORT.PARENTID
        inner join CONTENTTREE CT_REPORTS
          on CT_REPORTS.NODEID = CT_PERIOD.PARENTID
        inner join CONTENTTREE CT_REQUEST
          on CT_REQUEST.NODEID = CT_REPORTS.PARENTID
        inner join V_ADD_VALUES_REF AV_STRUCTUREID
          on AV_STRUCTUREID.NODEID = CT_REPORT.NODEID
            and AV_STRUCTUREID.FIELDNAME = 'STRUCTUREID'
        inner join STAT_DATA SD
          on SD.DATAREPORTID = CT_REPORT.NODEID
        inner join CONTENTTREE CT_KIND
          on CT_KIND.NODEID = SD.KINDID

        inner join V_ADD_VALUES_REF ADD_ESTABLISHMENTID
				  on (ADD_ESTABLISHMENTID.NODEID = cast(SD.DATAREPORTID as varchar(20)))
				    and (ADD_ESTABLISHMENTID.FIELDNAME = 'STRUCTUREID')  
          
        where CT_REQUEST.NODE_KEY = 'REQ_MOZ_CONTMOVEMENT_YEAR_SPEC'" .
        
		(@$this->params['establishment'] ? " and ADD_ESTABLISHMENTID.VAL = " . $this->params['establishment'] : "") .
		
        " --and AV_STRUCTUREID.VAL = ?  -- NODEID OF STRUCTURE
        and ( 
          exists (
            select *
            from V_STAT_PARAMGROUPS VSP
            inner join V_ADD_VALUES VA_ORIGIN
              on (VA_ORIGIN.NODEID = VSP.PARAMVALUE)
                and (VA_ORIGIN.FIELDNAME = '_ADD_ORIGINID')
                and (VA_ORIGIN.VAL = '1')
            where VSP.PARAMGROUPID = SD.PARAMGROUPID
              and VSP.PARAMNAME = 'STAT_PARAM_REPORTPLANKIND'
          )
        )
        and (
          (
            select AVD.VAL
            from V_ADD_VALUES_DATE AVD
            where (AVD.NODEID = CT_PERIOD.NODEID)
            and (AVD.FIELDNAME = '_ADD_ENDDATE')
          ) = cast(? as date) - 1)
        group by 1, 2"; 
		
		$d = new DateTime($this->getAddValueDate($this->params['period'], '_ADD_STARTDATE'));
						
		$this->_db->beginTransaction ();
		$res = $this->_db->fetchAll($sql, $d->format('d.m.Y'));
		$this->_db->commit ();		
		
		// добавляем ключ GR_KEY
		foreach ($res as &$resstr){
			$resstr['GR_KEY'] = $resstr['STRUCTUREID'] . '_' . $resstr['PARAMGROUPID'];			
		}
		
		return $res;
	}
	

	public function getWorkbook() {
		$data = $this->getReportData();
		$data_add = $this->getReportDataAdd(); // Доп. данные, поданные вузом, на конец предыдущего года для сравнение на начала отчетного года		
		
		$data = $this->formtable($data, $data_add);			
		
		//$eduDirections = array();
		
		
		$xls_reader = new PHPExcel_Reader_Excel2007();		
		$workbook = $xls_reader->load(APPLICATION_PATH . 'modules/stat/templates/report9.xlsx');
		$sheet = $workbook->getActiveSheet();
		
		$current_row = 7;
		$firstData_col = 3;
		
		// Заголовок отчета
		$endDate = new DateTime($this->getAddValueDate($this->params['period'], '_ADD_ENDDATE'));		
		$reportYear = $endDate->format('Y');		
		$sheet->setCellValue('D5', sprintf($sheet->getCell('D5')->getValue(), $reportYear));		
		$endDate->modify("+1 year");
		$reportYear = $endDate->format('Y');
		$sheet->setCellValue('J5', sprintf($sheet->getCell('J5')->getValue(), $reportYear));
		$endDate->modify("+1 year");
		$reportYear = $endDate->format('Y');
		$sheet->setCellValue('P5', sprintf($sheet->getCell('P5')->getValue(), $reportYear));

		$endDate->modify("-3 year");
		$reportYear = $endDate->format('Y');
		$sheet->setCellValue('C5', sprintf($sheet->getCell('C5')->getValue(), $reportYear));
				
//		$sheet->getStyle("B17")->getNumberFormat()->setFormatCode('dd.mm.yyyy'); // Если просто задано в шаблоне, то не работает в HTML

		$sheet->insertNewRowBefore($current_row+1, count($data) - 1);

		foreach ($data as $vuznum => $vuz) {
			//$vuzrow = $current_row + 1;			

			$sheet->setCellValueByColumnAndRow(0, $current_row, $vuznum + 1);
			$sheet->setCellValueByColumnAndRow(1, $current_row, $vuz['title'] . " (" . count($vuz['DATA']) . " стр.)");

			$workbook->getActiveSheet()->mergeCellsByColumnAndRow(1, $current_row, 20, $current_row); 

			$sheet->getStyle("B$current_row:B$current_row")->getFont()->setBold(true);
			$sheet->getStyle("B$current_row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			

			$sheet->insertNewRowBefore($current_row+1, count($vuz['DATA']));

			foreach ($vuz['DATA'] as $specnum => $spec) {
				$current_row ++;
				
				$specColor = $spec['speccolor'];
				if ($specColor){
				  $sheet->getStyleByColumnAndRow(1, $current_row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCDCDC1');
				}				
				
				$sheet->getStyle("A$current_row:U$current_row")->getFont()->setBold(false);
				$title = $spec['title'] . " (" . substr($spec['id'], 0, 2) . ")";				
				
				$cell = $sheet->setCellValueByColumnAndRow(1, $current_row, $title, true);								
				
				$s = $spec['endlastyear'];				
								
				$cell = $sheet->setCellValueByColumnAndRow(
							  $firstData_col-1, $current_row, 
							  $s, true);
				
				// валидация: на конец прошлого года есть данные а в отчетном году их нет на начало года
				$id = $spec['id'];
				$beginletter = (isset($id[0]) && strlen($id)>2)?$id[0].$id[1]:''; 
				   
				if ($beginletter == '0_' && $s > 0) {
					$sheet->getStyleByColumnAndRow($firstData_col-1, $current_row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFfb8883');
				}
				
				$spec_data = $spec['DATA'];
				
				for($rc=1; $rc<=3; $rc++){ // Report Kind (num)
					if(array_key_exists($rc, $spec_data)){						
											
						// валидация
						if (array_key_exists('CS', $spec_data[$rc]) && $s != $spec_data[$rc]['CS']){
							$col = $firstData_col + (($rc-1) * 6);
							$sheet->getStyleByColumnAndRow($col, $current_row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFfb8883');
						} 
						
						foreach (array('CS', 'CENTER', 'CEXIT', 'MOVEIN', 'MOVEOUT') as $colnum => $column) {
							$cell = $sheet->setCellValueByColumnAndRow(
							  $colnum + $firstData_col + (($rc-1) * 6), 
							  $current_row, 
							  array_key_exists($column, $spec_data[$rc]) ? $spec_data[$rc][$column] : ' ', true);										
						}
						$s = (array_key_exists('CS', $spec_data[$rc]) ? $spec_data[$rc]['CS'] : 0) + 
							 (array_key_exists('CENTER', $spec_data[$rc]) ? $spec_data[$rc]['CENTER'] : 0) - 
							 (array_key_exists('CEXIT', $spec_data[$rc]) ? $spec_data[$rc]['CEXIT'] : 0) + 
							 (array_key_exists('MOVEIN', $spec_data[$rc]) ? $spec_data[$rc]['MOVEIN'] : 0) - 
							 (array_key_exists('MOVEOUT', $spec_data[$rc]) ? $spec_data[$rc]['MOVEOUT'] : 0);
						$sheet->setCellValueByColumnAndRow($firstData_col + 5 + (($rc-1) * 6),  $current_row, $s);
					}
				}
			}
			$current_row ++;
		} 

//------------------------------------------------------------------------------

		return $workbook;
	}

	function search($array, $key, $value)
	{
		$results = array();
	
		if (is_array($array))
		{
			if (isset($array[$key]) && $array[$key] == $value)
			$results[] = $array;
	
			foreach ($array as $subarray)
			$results = array_merge($results, $this->search($subarray, $key, $value));
		}
	
		return $results;
	}
	
	function addAddData(&$vuz, &$dataAdd, $lastSpecColor){
		// Добавляет в отчет строки, если по ним были данные в предыдущем году (а в этом нет)		
		$structureId = $vuz['structureid'];
		$rowData = count($vuz['DATA']); // Число групп в вузе, начинается с 0, но так как нужна след., то -1 нет
		$lastSpecColor = !$lastSpecColor;
		//echo '<br/><br/>vuz:<br/>';
		//var_dump($vuz);
		
		
		$vuzAdd = $this->search($dataAdd, 'STRUCTUREID', $structureId);		
		
		foreach ($vuzAdd as &$specAddGr){
			$paramGroupId = $specAddGr['PARAMGROUPID'];
			$endLastYear = $specAddGr['DATAVALUE'];
//			if ($endLastYear != 0){						
				$invuz = $this->search($vuz, 'paramgroupid', $paramGroupId);
				if (count($invuz) == 0){									
					$Title = $this->getTitleOfParamGroupId($paramGroupId);
					$curSpecialityKey = '0_'.$rowData;				
									
					//echo '<br/><br/>till:<br/>';
					//var_dump($vuz);								
					
					$vuz['DATA'][$rowData] =  array('id' => $curSpecialityKey, 'title' => $Title, 'speccolor' => $lastSpecColor,
										  'endlastyear' => $endLastYear, 'paramgroupid' => $paramGroupId, 'DATA' => array());
					
					$rowData ++;					
					//echo '<br/><br/>after:<br/>';
					//var_dump($vuz);					
				}
//			}		
		} 		
		
	}
	
	protected function formtable($data, $data_add) {	
		
		$vuzes = array ();

		if (!empty($data)) {

			$this->guide_vuz = $this->getEstablishments('GUIDE_STRUCTURE', 'SORTORDER');
			$this->guide_speciality = $this->getSpecialities(false, false, 'CT.SORTORDER', 'SORTORDER');

			$vuzNum = -1;
			$lastEstNum = -1;
			$rowData = -1;
			$lastSpeciality = -1;
			$structureId = -1;
			$lastSpecialityKey = '';
			$curColor = false;   // fasle - светлый / true - темный - подсветка строк одной специальности 
			$vuzData;
			
			foreach($data as &$datarow) {
				
				// смена вуза
				$curEstNum = $datarow['ESTABLISHMENT_NUM'];
				if ($lastEstNum != $curEstNum){					
					if ($lastEstNum > -1){	// Первый раз пропускаем
						$this->addAddData($vuzes[$vuzNum], $data_add, $curColor);
					}
					$lastEstNum = $curEstNum;
					$structureId = $datarow['ESTABLISHMENTID'];
					$lastSpeciality = -1;
					$lastSpecialityKey = '';
					$rowData = -1;
					$vuzNum ++;
					$vuzes[$vuzNum] = array('title' => $this->guide_vuz[$lastEstNum], 'structureid' => $structureId, 'DATA' => array());
					$vuzData = &$vuzes[$vuzNum]['DATA'];					
				}
				
				// смена специальности или любого из параметров: EDUFORMTITLE, EDUBASISTITLE, COUNTRYTYPETITLE				
				$fo = $datarow["EDUFORMTITLE"];
				$ff = $datarow["EDUBASISTITLE"];
				$gr = $datarow["COUNTRYTYPETITLE"];
				$curSpeciality = $datarow['SPECIALITY_NUM'];
				$curSpecialityKey = $datarow['SPECIALITY_NUM'].'_'.$fo.'_'.$ff.'_'.$gr;
				
				if ($curSpeciality != $lastSpeciality) {
					$lastSpeciality = $curSpeciality;
					$curColor = !$curColor;
				}
				 
				if ($lastSpecialityKey != $curSpecialityKey){					
					$lastSpecialityKey = $curSpecialityKey;
					// Получить строковое выражение для группы (перечисленные через запятую значения параметров группы)
										
					$Title =  $this->guide_speciality[$curSpeciality] . ' [' . $fo . ', ' . $ff . ', ' . $gr . ']'; 					
											
					$endLastYear = 0; // ???
					$paramGroupId = -1;
					
					$rowData ++;
					$vuzData[$rowData] =  array('id' => $curSpecialityKey, 'title' => $Title, 'speccolor' => $curColor,
					  'endlastyear' => $endLastYear, 'paramgroupid' => 0, 'DATA' => array());
					$specialityData = &$vuzData[$rowData]['DATA'];					
				}				

				$factor_name = $datarow["KIND"];																 
				$val = $datarow ["VAL"];
				$rep_kind_num = $datarow ["REPORTKIND_NUM"];
				
				// 1 - только для "отчета по плану" 
				// -1 - чтобы устанавливалось значение только один раз для $curSpecialityKey
				if ($rep_kind_num == 1 && $paramGroupId = -1) { 
					$paramGroupId = $datarow["PARAMGROUPID"]; // для отчет, план и перспективный план разные PARAMGROUPID
					$vuzData[$rowData]['paramgroupid'] = $paramGroupId;
					// задача: $structureId, $paramGroupId -> $endLastYear
					$res = $this->search($data_add, 'GR_KEY', $structureId . '_' . $paramGroupId);
					if (is_array($res) && array_key_exists('0', $res) && is_array($res[0]) && isset($res[0]['DATAVALUE'])){
						$vuzData[$rowData]['endlastyear'] = $res[0]['DATAVALUE'];
					}
				}
				 
/*				echo '<br/><br/>vuz:<br/>';
				var_dump($curEstNum);				
				echo '<br/><br/>factor_name:<br/>';
				var_dump($factor_name);
				echo '<br/><br/>specialityData:<br/>';
				var_dump($specialityData); */
				
				
				$specialityData[$rep_kind_num][$factor_name] = 
					array_key_exists($rep_kind_num, $specialityData) &&
				    array_key_exists($factor_name, $specialityData[$rep_kind_num]) ? $specialityData[$rep_kind_num][$factor_name] + $val : $val;				
			}
			$this->addAddData($vuzes[$vuzNum], $data_add, $curColor); // Следует выполнить и для последнего вуза
		}

		return $vuzes;
	}
	

}