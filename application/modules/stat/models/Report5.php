<?php

// Отчет по плану контингента за год по специальностям + план + перспективный план

class Stat_Model_Report5 extends Stat_Model_Reports {

  public $isExcelDifferentFromHTML = true;

  public function getForm() {
    $form = new Zend_Form();
    $form->setMethod('post');
    $form->setDecorators(array(array('ViewScript', array('viewScript' => 'reports/report5Form.phtml'))));

    $items = $this->getPeriodItems('REQ_MOZ_CONTMOVEMENT_YEAR_SPEC');
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

    $items = $this->getGuideItems ('T_COUNTRYTYPE', true);
    $e = new Zend_Form_Element_Select('countrytype',
    array('label' => 'Тип громадянства',
    'multiOptions' => $items,
    'style' => 'width: 200px'));
    $form->addElement($e);

		if (@!$this->params['establishment']) {
		  $e = new Zend_Form_Element_Checkbox('indpapers',
		  array('label' => 'Індивідуальні аркуші'));
		  $form->addElement($e);
		}

    $refreshAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'page', 'action' => 'show'));
    $e = new Zend_Form_Element_Submit('refresh', array('label'=>'Обновити', 'onclick'=>"document.forms[0].action='{$refreshAct}'"));
    $form->addElement($e);

    $excelAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'reports', 'action' => 'excel', 'report' => 5));
    $e = new Zend_Form_Element_Submit('excel', array('label'=>'Excel', 'onclick'=>"document.forms[0].action='{$excelAct}'"));
    $form->addElement($e);

    $form->setElementDecorators(array('ViewHelper', 'Errors'));

    return $form;
  }

  protected function getReportData() {
    $includeReportPlanKind = $this->params['report'] == 5;
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
/*
iif(GUIDE_EDULEVEL.SORTORDER <= 2,
  iif(INFO_ESTAB.CODE in (5, 14) and GUIDE_EDULEVEL.SORTORDER = 2, 2, 1), 2) as GRP, -- Мл. специалисты и бакалавры (кроме Запорожья и ХНМУ) не из Тернополя отдельно

iif(GUIDE_EDULEVEL.SORTORDER <= 2, -- разделяем вузы и колледжи
  iif(GUIDE_EDULEVEL.SORTORDER = 2 and INFO_ESTAB.CODE in (5, 14), GUIDE_ESTAB.SORTORDER,
    decode(INFO_ESTAB.CODE, 3, 17, 4, 18, 5, 19, 9, 20, 14, 21, GUIDE_ESTAB.SORTORDER)
  ),
  GUIDE_ESTAB.SORTORDER
) as ESTABLISHMENT_NUM,
*/
    $sql = "select first 10000
iif((GUIDE_EDULEVEL.SORTORDER = 1 or (GUIDE_EDULEVEL.SORTORDER = 2 and INFO_ESTAB.CODE in (54)) or (GUIDE_EDULEVEL.SORTORDER = 2 and INFO_ESTAB.CODE in (7))) and INFO_ESTAB.CODE <> 12, 1, 2) as GRP,

iif(GUIDE_EDULEVEL.SORTORDER <= 2, -- разделяем вузы и колледжи
  iif(GUIDE_EDULEVEL.SORTORDER = 2 and INFO_ESTAB.CODE in (5, 14), GUIDE_ESTAB.SORTORDER,
    decode(INFO_ESTAB.CODE, 3, 17, 4, 18, 5, 19, 9, 20, 14, 21, GUIDE_ESTAB.SORTORDER)
  ),
  GUIDE_ESTAB.SORTORDER
) as ESTABLISHMENT_NUM,

(select GUIDE_SPECIALITY.SORTORDER
from CONTENTTREE GUIDE_SPECIALITY
where (GUIDE_SPECIALITY.NODEID = cast(SPG_SPECIALITY.PARAMVALUE as bigint))) as SPECIALITY_NUM,

(select GUIDE_EDUDIRECTION.SORTORDER
from V_ADD_VALUES_REF VAV_EDUDIRECTION
inner join CONTENTTREE GUIDE_EDUDIRECTION
on (GUIDE_EDUDIRECTION.NODEID = cast(VAV_EDUDIRECTION.VAL as bigint))
where (VAV_EDUDIRECTION.NODEID = cast(SPG_SPECIALITY.PARAMVALUE as bigint))
and (VAV_EDUDIRECTION.FIELDNAME = '_ADD_EDUDIRECTION')) as EDUDIRECTION_NUM,

GUIDE_EDULEVEL.SORTORDER as EDULEVEL_NUM,

trim(leading 'STAT_KIND_' from GUIDE_KIND.NODE_KEY) as KIND,
cast(SD.DATAVALUE as integer) as VAL,

iif(exists(select * from STAT_VALIDATION SV
where SV.DATAREPORTID = SD.DATAREPORTID
and SV.PARAMGROUPID = SD.PARAMGROUPID
and SV.KINDID = SD.KINDID), 1, 0) as VALIDATION_ERRORS_EXISTS,

SD.DATAREPORTID, SD.PARAMGROUPID, SD.KINDID

from STAT_DATA SD

inner join TMP REQ
on (REQ.ID = SD.DATAREPORTID)

inner join V_ADD_VALUES_REF ADD_ESTABLISHMENTID
on (ADD_ESTABLISHMENTID.NODEID = cast(SD.DATAREPORTID as varchar(20)))
and (ADD_ESTABLISHMENTID.FIELDNAME = 'STRUCTUREID')
inner join CONTENTTREE GUIDE_ESTAB
on (GUIDE_ESTAB.NODEID = cast(ADD_ESTABLISHMENTID.VAL as bigint))
inner join INFO_STRUCTURE_ITEM INFO_ESTAB
on (INFO_ESTAB.NODEID = GUIDE_ESTAB.NODEID)

inner join V_STAT_PARAMGROUPS SPG_SPECIALITY
on (SPG_SPECIALITY.PARAMGROUPID = SD.PARAMGROUPID)
and (SPG_SPECIALITY.PARAMNAME = 'STAT_PARAM_SPECIALITYID')

inner join V_ADD_VALUES_REF VAV_EDULEVEL
on (VAV_EDULEVEL.NODEID = cast(SPG_SPECIALITY.PARAMVALUE as bigint))
and (VAV_EDULEVEL.FIELDNAME = '_ADD_EDULEVELID')
inner join CONTENTTREE GUIDE_EDULEVEL
on (GUIDE_EDULEVEL.NODEID = cast(VAV_EDULEVEL.VAL as bigint))

inner join CONTENTTREE GUIDE_KIND
on (GUIDE_KIND.NODEID = SD.KINDID)" .

($includeReportPlanKind ? $reportPlanKindJoin : "") .
($includeEdubasis ? $edubasisJoin : "") .
($includeEduform ? $eduformJoin : "") .
($includeCountryType ? $countryTypeJoin : "") .

"where 1=1 " .
(@$this->params['establishment'] ? " and ADD_ESTABLISHMENTID.VAL = " . $this->params['establishment'] : "") .

"order by GRP, ESTABLISHMENT_NUM, EDUDIRECTION_NUM, EDULEVEL_NUM, SPECIALITY_NUM desc";

    $res = $this->_db->fetchAll($sql);

    $this->_db->commit ();

    return $res;
  }


  private function showTotal($sheet, &$current_row, $eduDirections, $isGrandTotal = false, $isShowTotal = true, $isAddDelimiter = true) {
    if ($isShowTotal) {
      if (!$isGrandTotal) {
        $sheet->insertNewRowBefore($current_row, 1);
        $sheet->getRowDimension($current_row)->setRowHeight(0.75); // Разделительная линия перед ВСЕГО
        $current_row ++;
      }

      $rowAll = $current_row;

      $sheet->insertNewRowBefore($current_row + 1, count($eduDirections) + 1); // Уровни подготовки  + ВСЕГО

      $sheet->setCellValueByColumnAndRow(1, $current_row, $isGrandTotal ? 'ЗА ВСІМА ВНЗ' : 'РАЗОМ');

      $sheet->getStyle("A$current_row:T$current_row")->getFont()->setBold(true);
      $sheet->getStyle("A$current_row:T$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD8EEEA');
      $sheet->getStyle("B$current_row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

      $current_row++;

      $dirrows = array();

      foreach ($eduDirections as $dir => $edulevels) {
        $dirrow = $current_row;
        array_push($dirrows, $current_row);

        $sheet->setCellValueByColumnAndRow(1, $current_row, "напрям \"$dir\"");
        $sheet->getStyle("A$current_row:T$current_row")->getFont()->setBold(true);
        $sheet->getStyle("A$current_row:T$current_row")
          ->getFill()
          ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB('FFFDD9C9');

        $current_row ++;

        $sheet->insertNewRowBefore($current_row + 1, count($edulevels));

        $edulevelrows = array();

        foreach ($edulevels as $edulevel => $specialities) {
          array_push($edulevelrows, $current_row);

          $sheet->setCellValueByColumnAndRow(1, $current_row, "рівень \"$edulevel\"");
          $sheet->getStyle("A$current_row:T$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFDE9D9');

          $speciality_count = count($specialities);
          $sheet->insertNewRowBefore($current_row + 2, $speciality_count);

          $formula_row1 = $current_row + 1;
          $formula_row2 = $current_row + $speciality_count;

          for($col=2; $col<=19; $col++) {
            if($col == 15) continue;
            $colStr = PHPExcel_Cell::stringFromColumnIndex($col);
            $sheet->setCellValueByColumnAndRow($col, $current_row, "=SUM($colStr$formula_row1:$colStr$formula_row2)");
          }

          $current_row ++;

          foreach ($specialities as $specialityName => $specialityRows) {
            $sheet->setCellValueByColumnAndRow(1, $current_row, $specialityName);
            $sheet->getStyle("B$current_row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            for($col = 2; $col <= 19; $col++) {
              if($col == 15) continue;
              $colStr = PHPExcel_Cell::stringFromColumnIndex($col);
              $sheet->setCellValueByColumnAndRow($col, $current_row, "=$colStr" . implode("+$colStr", $specialityRows));
            }

            $current_row ++;
          }
        }

        for($col=2; $col<=19; $col++) {
          if($col == 15) continue;
          $colStr = PHPExcel_Cell::stringFromColumnIndex($col);
          $sheet->setCellValueByColumnAndRow($col, $dirrow, "=$colStr" . implode("+$colStr", $edulevelrows));
        }
      }

      for($col=2; $col<=19; $col++) {
        if($col == 15) continue;
        $colStr = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValueByColumnAndRow($col, $rowAll, "=$colStr" . implode("+$colStr", $dirrows));
      }
    }

    if (!$isGrandTotal && $isAddDelimiter) { // Разделительная строка между группами вузов
      $sheet->insertNewRowBefore($current_row + 1, 1);
      $sheet->mergeCellsByColumnAndRow(0, $current_row, 19, $current_row);
      $current_row ++;
    }
  }

  public function getWorkbook($isExcelFile = false) {
    $indpapers = @$this->params['indpapers'];
    $vuzes = $this->getReportData();
    $vuzes = $this->formtable($vuzes);

    $xls_reader = new PHPExcel_Reader_Excel2007();
    $workbook = $xls_reader->load(APPLICATION_PATH . 'modules/stat/templates/report5.xlsx');
    $sheet = $workbook->getActiveSheet();

    $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($indpapers ? 1 : 4, 8); // В шаблоне пока не работает

    $foreighnStr = $this->params['countrytype'] == 0 ? 'вітчизняних та іноземних ' :
      ($this->getAddValue($this->params['countrytype'], '_ADD_ORIGINID') == 'УКР' ? 'вітчизняних ' : 'іноземних ');
    $eduBaseStr = $this->getAddValue($this->params['edubase'], '_ADD_ORIGINID') == 'Б' ? 'держзамовленням' : 'контрактом';
    $eduForms = array('ДН' => 'що навчаються', 'ВЧ' => 'що навчаються вечірньо', 'ЗЧ' => 'що навчаються заочно', 'ДЦ' => 'що навчаються дистанційно', 'ДВ' => 'що отримують другу вищу освіту');
    $eduFormOriginID = $this->getAddValue($this->params['eduform'], '_ADD_ORIGINID');
    $eduFormStr = $eduForms[$eduFormOriginID] . ' ';
    switch ($eduFormOriginID) {
      case 'ЗЧ' :
      case 'ДВ' :
        $coefficient = 0.1; break;
      case 'ВЧ' : $coefficient = 0.25; break;
      default: $coefficient = 1;
    }

    $endDate = new DateTime($this->getAddValueDate($this->params['period'], '_ADD_ENDDATE'));
    if(@$this->params['reportplankind']) {
      $selectedReportPlan = $this->getGuideNodeTitle($this->params['reportplankind']);
      $planKindNum = $this->getAddValue($this->params['reportplankind'], '_ADD_ORIGINID') - 1;
      $endDate->modify("+$planKindNum year");
    }  else {
      $selectedReportPlan = 'Уточнений план';
    }
    $selectedYear = $endDate->format('Y');


    $sheet->setCellValue('A1', sprintf($sheet->getCell('A1')->getValue(), $selectedReportPlan, $foreighnStr, $eduFormStr, $eduBaseStr, $selectedYear));

    $current_row = 9;
//    $sheet->getStyle("B17")->getNumberFormat()->setFormatCode('dd.mm.yyyy'); // Если просто задано в шаблоне, то не работает в HTML

    $sheet->insertNewRowBefore($current_row + 1, count($vuzes) * 3); // Название вуза + всего + в том числе

    $allEduDirections = array();
    $groupEduDirections = array();
    $vuzezInGroupCount = 0;

    $lastVuzGroup = null;

    foreach ($vuzes as $vuznum => $vuz) {
      if ($indpapers) {
        if ($vuzezInGroupCount > 0) {
          $sheet->setBreak("A" . ($current_row - 1), PHPExcel_Worksheet::BREAK_ROW);
        }
      } else {
        if ($lastVuzGroup != $vuz['group']) {
          if ($lastVuzGroup)
            $this->showTotal($sheet, $current_row, $groupEduDirections, false, $vuzezInGroupCount > 1);

          $vuzezInGroupCount = 0;

          $lastVuzGroup = $vuz['group'];
          $groupEduDirections = array();

          if (!@$this->params['establishment']) {
            $sheet->insertNewRowBefore($current_row + 1, 1);

            $sheet->setCellValueByColumnAndRow(0, $current_row, $lastVuzGroup == 1 ? 'ВНЗ 1-2 рівня (2301120)' : 'ВНЗ 3-4 рівня (2301070)');
            $sheet->mergeCellsByColumnAndRow(0, $current_row, 19, $current_row);

            $sheet->getStyle("A$current_row:T$current_row")->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle("A$current_row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $current_row ++;
          }
        }

        if ($vuzezInGroupCount > 0) { // Разделительная линия между вузами внутри группы
          $sheet->insertNewRowBefore($current_row + 1, 1);
          $sheet->getRowDimension($current_row)->setRowHeight(0.75);
          $current_row ++;
        }
      }

      $vuzezInGroupCount ++;

      $vuzrow = $current_row + 1; // Запоминаем строку с суммами по вузу!!!

      $sheet->setCellValueByColumnAndRow(0, $current_row, $vuznum + 1);
      $sheet->setCellValueByColumnAndRow(1, $current_row, $vuz['title']);

      $sheet->mergeCellsByColumnAndRow(1, $current_row, 19, $current_row);

      $next_row = $current_row + 1;

      $sheet->getStyle("A$current_row:T$next_row")->getFont()->setBold(true);
      $sheet->getStyle("A$current_row:T$next_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($this->colorLevel1);
      $sheet->getStyle("B$current_row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


      $current_row ++;
      $sheet->setCellValueByColumnAndRow(1, $current_row, $selectedYear);

      $current_row ++;
      $sheet->setCellValueByColumnAndRow(1, $current_row, 'в тому числі');

      $current_row ++;

      $dir_count = count($vuz['DATA']);
      $sheet->insertNewRowBefore($current_row + 1, $dir_count); // Направления подготовки

      $dirrows = array();

      foreach ($vuz['DATA'] as $dirnum => $dir) {
        $dirrow = $current_row;

        if(!@array_key_exists($dir['title'], $groupEduDirections))
          $groupEduDirections[$dir['title']] = array();
        $currentGroupEduDirection = &$groupEduDirections[$dir['title']];

        if(!@array_key_exists($dir['title'], $allEduDirections))
          $allEduDirections[$dir['title']] = array();
        $currentAllEduDirection = &$allEduDirections[$dir['title']];

        $dirrows[$dirnum] = $current_row;

        $sheet->setCellValueByColumnAndRow(1, $current_row, 'напрям "' . $dir['title'] . '"');

        $sheet->getStyle("A$current_row:T$current_row")->getFont()->setBold(true);
        $sheet->getStyle("A$current_row:T$current_row")
          ->getFill()
          ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB($this->colorLevel2);

        $current_row ++;

        $levels_count = count($dir['DATA']);
        $sheet->insertNewRowBefore($current_row + 1, $levels_count); // Уровни подготовки

        $edulevelrows = array();

        foreach ($dir['DATA'] as $edulevelnum => $edulevel) {
          if(!@array_key_exists($edulevel['title'], $currentGroupEduDirection))
            $currentGroupEduDirection[$edulevel['title']] = array();
          $currentGroupEduLevel = &$currentGroupEduDirection[$edulevel['title']];

          if(!@array_key_exists($edulevel['title'], $currentAllEduDirection))
            $currentAllEduDirection[$edulevel['title']] = array();
          $currentAllEduLevel = &$currentAllEduDirection[$edulevel['title']];

          $edulevelrows[$edulevelnum] = $current_row;

          $speciality_count = count($edulevel['DATA'], 0);
          $sheet->insertNewRowBefore($current_row + 2, $speciality_count);

          $formula_row1 = $current_row + 1;
          $formula_row2 = $current_row + $speciality_count;


          // Всего по уровню подготовки
          for($col=2; $col<=19; $col++) {
            if($col == 15) continue;
            $colStr = PHPExcel_Cell::stringFromColumnIndex($col);
            $sheet->setCellValueByColumnAndRow($col, $current_row, "=SUM($colStr$formula_row1:$colStr$formula_row2)");
          }

          $sheet->setCellValueByColumnAndRow(1, $current_row, "рівень \"{$edulevel['title']}\"");
          $sheet->getStyle("A$current_row:T$current_row")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($this->colorLevel3);

          $current_row ++;

          foreach ($edulevel['DATA'] as $specialitynum => $speciality) {
            if(!@array_key_exists($speciality['title'], $currentGroupEduLevel))
              $currentGroupEduLevel[$speciality['title']] = array($current_row);
            else
              array_push($currentGroupEduLevel[$speciality['title']], $current_row);

            if(!@array_key_exists($speciality['title'], $currentAllEduLevel))
              $currentAllEduLevel[$speciality['title']] = array($current_row);
            else
              array_push($currentAllEduLevel[$speciality['title']], $current_row);

            $sheet->setCellValueByColumnAndRow(1, $current_row, $speciality['title']);
            $sheet->getStyle("B$current_row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            foreach (array('CS',null,'CEXIT4','CEXIT3','CEXIT2','CEXIT1',null,'CENTER4','CENTER3','CENTER2','CENTER1',null,'MOVEOUT',null,'MOVEIN') as $colnum => $column) {
              if($column) {
                $cell = $sheet->setCellValueByColumnAndRow($colnum + 2, $current_row, array_key_exists($column, $speciality) ? $speciality[$column]['val'] : 0, true);
                if (!$isExcelFile && @$speciality[$column]['kindid']) {
                  $ids = implode(',', $speciality[$column]['ids']);
//                  alert('datareportid = {$speciality[$column]['datareportid']}; kindid={$speciality[$column]['kindid']}; paramgroupids=$ids')
                  $cell->getHyperlink()->setUrl("javascript:showValidation({$speciality[$column]['datareportid']},{$speciality[$column]['kindid']},'$ids')");
                }
              }
            }

            $sheet->setCellValueByColumnAndRow(3, $current_row, "=SUM(E$current_row:H$current_row)");
            $sheet->setCellValueByColumnAndRow(8, $current_row, "=SUM(J$current_row:M$current_row)");
            $sheet->setCellValueByColumnAndRow(13, $current_row, "=C$current_row-D$current_row+I$current_row");
            $sheet->setCellValueByColumnAndRow(17, $current_row, "=N$current_row-O$current_row+Q$current_row");
            $sheet->setCellValueByColumnAndRow(18, $current_row, "=(C$current_row*8.5+R$current_row*3.5)/12");
            $sheet->setCellValueByColumnAndRow(19, $current_row, "=S$current_row*$coefficient");

            $current_row ++;
          }

        }

        for($col=2; $col<=19; $col++) {
          if($col == 15) continue;
          $colStr = PHPExcel_Cell::stringFromColumnIndex($col);
          $sheet->setCellValueByColumnAndRow($col, $dirrow, "=$colStr" . implode("+$colStr", $edulevelrows));
        }

      }

      for($col=2; $col<=19; $col++) {
        if($col == 15) continue;
        $colStr = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValueByColumnAndRow($col, $vuzrow, "=$colStr" . implode("+$colStr", $dirrows));
      }

      if ($indpapers) {
        $sheet->insertNewRowBefore($current_row, 4);
         $sheet->getStyle('A' . $current_row . ':T' . ($current_row + 3))
           ->applyFromArray(
               array(
                'font' => array (
                  'bold' => true,
                   'size' => 10
                ),
                 'alignment' => array(
//                   'wrap' => true,
                  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
//                   'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                 ),
                'borders' => array (
                    'allborders' => array (
                      'style' => PHPExcel_Style_Border::BORDER_NONE,
                   )
                 )
               )
            );

        $sheet->mergeCellsByColumnAndRow(1, $current_row + 1, 8, $current_row + 1);
        $sheet->setCellValueByColumnAndRow(1, $current_row + 1, @$vuz['STAT2_POST']);
        $sheet->mergeCellsByColumnAndRow(1, $current_row + 2, 8, $current_row + 2);
        $sheet->setCellValueByColumnAndRow(1, $current_row + 2, @$vuz['STAT2_DEGREE']);
        $sheet->mergeCellsByColumnAndRow(9, $current_row + 1, 13, $current_row + 1);
        $sheet->setCellValueByColumnAndRow(9, $current_row + 1, @$vuz['STAT2_FIO']);
        $current_row += 4;
      }
    }

    if (!$indpapers) {
      $this->showTotal($sheet, $current_row, @$groupEduDirections, false, $vuzezInGroupCount > 1, count($vuzes) > 1);

//-------------- ИТОГ по всем ВУЗам --------------------------------------------
      if(count($vuzes) > 1 && count($allEduDirections) > 0) {
        $this->showTotal($sheet, $current_row, $allEduDirections, true);
      }
//------------------------------------------------------------------------------
    }

    $sheet->removeRow($current_row, 1); // Удаляем лишнюю строку, которая была в шаблоне

    return $workbook;
  }

  protected function formtable($data) {
    $vuzes = array ();

    if (!empty($data)) {
      $this->guide_vuz = $this->getEstablishments('GUIDE_STRUCTURE', 'SORTORDER');
      $this->guide_edudir = $this->getGuideItems('T_EDUDIRECTION', false, false, 'CT.SORTORDER', 'SORTORDER');
      $this->guide_edulevel = $this->getGuideItems('T_EDULEVEL', false, false, 'CT.SORTORDER', 'SORTORDER');
      $this->guide_speciality = $this->getSpecialities(false, false, 'CT.SORTORDER', 'SORTORDER');


      $row = -1;
      $lastEstNum = -1;
      $lastDirNum = -1;
      $lastEdulevelNum = -1;
      $lastSpecialityNum = -1;
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
            $vuzes[$vuzNum] = array(
                'title' => $this->guide_vuz[$curEstNum],
                'group' => $datarow['GRP'],
                'datareportid'=> $datarow['DATAREPORTID'],
                'DATA' => array());
            $sql = "select VAV.FIELDNAME, VAV.VAL
from V_ADD_VALUES VAV
where VAV.NODEID = ?";
            foreach ($this->_db->fetchAll($sql, $datarow['DATAREPORTID']) as $info) {
              $vuzes[$vuzNum][$info['FIELDNAME']] = $info['VAL'];
            }
            $vuzData = &$vuzes[$vuzNum]['DATA'];
          }

          $dirNum = $datarow["EDUDIRECTION_NUM"];
          if ($lastDirNum != $dirNum) {
            $rowData ++;
            $lastDirNum = $dirNum;
            $lastEdulevelNum = -1;
            $rowEduLevel = -1;
            $rowSpeciality = -1;
            $vuzData[$rowData] =  array('id' => $lastDirNum, 'title' => $this->guide_edudir[$lastDirNum], 'DATA' => array());
            $dirData = &$vuzData[$rowData]['DATA'];
          }

          $edulevelNum = $datarow["EDULEVEL_NUM"];
          if ($lastEdulevelNum != $edulevelNum) {
            $rowEduLevel ++;
            $lastEdulevelNum = $edulevelNum;
            $lastSpecialityNum = -1;
            $rowSpeciality = -1;
            $dirData[$rowEduLevel] =  array('title' => $this->guide_edulevel[$lastEdulevelNum], 'DATA' => array());
            $edulevelData = &$dirData[$rowEduLevel]['DATA'];
          }

          $specialityNum = $datarow["SPECIALITY_NUM"];
          if ($lastSpecialityNum != $specialityNum) {
            $rowSpeciality ++;
            $lastSpecialityNum = $specialityNum;
            $edulevelData[$rowSpeciality] =  array('title' => $this->guide_speciality[$lastSpecialityNum]);
            $specialityData = &$edulevelData[$rowSpeciality];
          }

          $factor_name = $datarow["KIND"];
          if($factor_name == 'CENTER' || $factor_name == 'CEXIT') $factor_name .= $datarow["EDULEVEL_NUM"];
          $val = $datarow ["VAL"];
/*          echo '<br/><br/>factor_name:<br/>';
          var_dump($factor_name);
          echo '<br/><br/>specialityData:<br/>';
          var_dump($specialityData);*/
          $specialityData[$factor_name]['val'] = array_key_exists($factor_name, $specialityData) ? $specialityData[$factor_name]['val'] + $val : $val;
          if ($datarow["VALIDATION_ERRORS_EXISTS"]) {
            $specialityData[$factor_name]['kindid'] = $datarow["KINDID"];
            $specialityData[$factor_name]['datareportid'] = $vuzes[$vuzNum]["datareportid"];
            if (!array_key_exists('ids', $specialityData[$factor_name]))
              $specialityData[$factor_name]['ids'] = array($datarow["PARAMGROUPID"]);
            else
              array_push($specialityData[$factor_name]['ids'], $datarow["PARAMGROUPID"]);
          }
      }

    }

//    file_put_contents('d:\\temp\vuzes.txt', var_export($vuzes, true));
    return $vuzes;
  }

}