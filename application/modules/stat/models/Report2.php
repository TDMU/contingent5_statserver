<?php

class Stat_Model_Report2 extends Stat_Model_Report1{

	public function getForm() {
		$form = new Zend_Form();
		$form->setMethod('post');
		$form->setDecorators(array(array('ViewScript', array('viewScript' => 'reports/report2Form.phtml'))));

		$items = $this->getPeriodItems('REQ_MOZ_CONTMOVEMENT');
		$e = new Zend_Form_Element_Select('period',
		array('label' => 'Період',
		'multiOptions' => $items,
		'required' => true));
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

		$excelAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'reports', 'action' => 'excel', 'report' => 2));
		$e = new Zend_Form_Element_Submit('excel', array('label'=>'Excel', 'onclick'=>"document.forms[0].action='{$excelAct}'"));
		$form->addElement($e);

		$form->setElementDecorators(array('ViewHelper', 'Errors'));

/*		$auth = Zend_Auth::getInstance ();
		$ident = $auth->getIdentity ();
		if($ident->STRUCTURE_CODE != 0) $form->getElement('establishment')->setValue($ident->STRUCTUREID);*/

		return $form;
	}


	public function getWorkbook() {
		$data = $this->getReportData();
		$data = $this->formtable($data);

		$xls_reader = new PHPExcel_Reader_Excel2007();
		$workbook = $xls_reader->load(APPLICATION_PATH . 'modules/stat/templates/report2.xlsx');
		$sheet = $workbook->getActiveSheet();

		$startDateStr = date('d.m.Y', strtotime($this->getAddValueDate($this->params['period'], '_ADD_STARTDATE')));
		$endDateStr = date('d.m.Y', strtotime($this->getAddValueDate($this->params['period'], '_ADD_ENDDATE')));

		$sheet->setCellValue('A1', sprintf($sheet->getCell('A1')->getValue(), $startDateStr, $endDateStr));

		$current_row = 5;

		$sheet->insertNewRowBefore($current_row + 1, count($data));

		foreach ($data as $vuznum => $vuz) {
			$sheet->mergeCellsByColumnAndRow(1, $current_row, 25, $current_row);

			$sheet->getStyle("A$current_row:Z$current_row")->getFont()->setBold(true);
			$sheet->getStyle("A$current_row:Z$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($this->colorLevel1);
			$sheet->getStyle("B$current_row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			$sheet->setCellValueByColumnAndRow(0, $current_row, $vuznum + 1);
			$sheet->setCellValueByColumnAndRow(1, $current_row, $vuz['title']);
			$current_row ++;

			$sheet->insertNewRowBefore($current_row + 1, count($vuz['DATA']) + 1);

			$startRow = $current_row;
			foreach ($vuz['DATA'] as $row) {
				$sheet->setCellValueByColumnAndRow(1, $current_row, $row['title']);
				foreach (array(null,'CS1','CS2','CS3','CS4','CS5','CS6','CS7',null,'CIN1','CIN2','CIN3','CIN4','CIN5','CIN6','CIN7',null,'COUT1','COUT2','COUT3','COUT4','COUT5','COUT6','COUT7') as $colnum => $column) {
				  if ($column)
					  $sheet->setCellValueByColumnAndRow($colnum + 2, $current_row, array_key_exists($column, $row) ? $row[$column] : 0);
				}

				$sheet->setCellValueByColumnAndRow(2, $current_row, "=SUM(D$current_row:J$current_row)");
				$sheet->setCellValueByColumnAndRow(10, $current_row, "=SUM(L$current_row:R$current_row)");
				$sheet->setCellValueByColumnAndRow(18, $current_row, "=SUM(T$current_row:Z$current_row)");

				$current_row ++;
			}

  		$sheet->setCellValueByColumnAndRow(1, $current_row, 'Усього: ');
			$sheet->getStyle("B$current_row:Z$current_row")->getFont()->setBold(true);
  		$sheet->getStyle("B$current_row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
  		$prev_row = $current_row - 1;

  		for($col=2; $col<=25; $col++) {
			  $colStr = PHPExcel_Cell::stringFromColumnIndex($col);
			  $sheet->setCellValueByColumnAndRow($col, $current_row, "=SUM($colStr$startRow:$colStr$prev_row)");
			}

			$current_row ++;
		}

//		$sheet->unmergeCellsByColumnAndRow(0, $current_row, 25, $sheet->getHighestRow());

		$sheet->removeRow($current_row, 1);

		return $workbook;
	}

	private function formtable($data) {
		$vuzes = array ();
		$lastEstID = - 1;
		$lastSpecID = - 1;
		$vuzNum = - 1;
		$rowData = - 1;
		$vuzData; // = [{"CS1": 10}, {"CS2": 10}];
		$line;
//		$sum = null;

		if (! empty ( $data )) {
			foreach ( $data as &$datarow ) {
				if (($this->params['country'] == 0 || $this->params['country'] == $datarow ['COUNTRYID'])
						&& ($this->params['countrytype'] == 0 || $this->params['countrytype'] == $datarow ['COUNTRYTYPEID'])
						&& (is_array ( $this->params['edulevel'])
						&& (array_search ( $datarow ['EDULEVELID'], $this->params['edulevel']) !== false))) {
					$curEstID = $datarow ['ESTABLISHMENTID'];
					if ($lastEstID != $curEstID) {
//						if ($vuzNum >= 0) {
//							$vuzData [$rowData + 1] = $sum;
//						}
//						$sum = array ('title' => 'Усього:' );
						$vuzNum ++;
						$rowData = - 1;
						$lastEstID = $curEstID;
						$lastSpecID = - 1;
						$vuzes [$vuzNum] = array ('title' => $this->getGuideNodeTitle($lastEstID), 'DATA' => array () );
						$vuzData = &$vuzes [$vuzNum] ['DATA'];
					}

					$curSpecID = $datarow ['SPECIALITYID'];
					if ($lastSpecID != $curSpecID) {
						$rowData ++;
						$lastSpecID = $curSpecID;
						$vuzData [$rowData] = array ('title' => $datarow ['CODE'] . " - " . $datarow ['SPECIALITY'] );
						$line = &$vuzData [$rowData];
					}

					$factor_name = $datarow ["KIND"];
					$full_factor_name = $factor_name . $datarow ["COURSE"];
					$val = $datarow ["VAL"];
					$line [$full_factor_name] = array_key_exists ( $full_factor_name, $line ) ? $line [$full_factor_name] + $val : $val;
					$line [$factor_name] = array_key_exists ( $factor_name, $line ) ? $line [$factor_name] + $val : $val;

//					$sum [$full_factor_name] = array_key_exists ( $full_factor_name, $sum ) ? $sum [$full_factor_name] + $val : $val;
//					$sum [$factor_name] = array_key_exists ( $factor_name, $sum ) ? $sum [$factor_name] + $val : $val;
				}
			}

//			if ($vuzNum >= 0) {
//				$vuzData [$rowData + 1] = &$sum;
//			}
		}

		sort ( $vuzes );

		return $vuzes;
	}

}