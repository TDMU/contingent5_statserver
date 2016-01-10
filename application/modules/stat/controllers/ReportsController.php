<?php

require_once 'Zend/Controller/Action.php';

class Stat_ReportsController extends Zend_Controller_Action {

  public function init() {
		set_time_limit(1200); // 20 min
		$this->_cfg = Zend_Registry::get('cfg');
  }

	public function showreportAction($isExcel = false) {
		$reportNum = $this->getRequest()->getParam('report', 0);
		$modelClass = 'Stat_Model_Report'.$reportNum;
		$model = new $modelClass();
	  $model->params = $this->getRequest()->getParams();

		$auth = Zend_Auth::getInstance();
		$ident = $auth->getIdentity();
		if($ident->STRUCTURE_CODE != 0)
    	$model->params['establishment'] = $ident->STRUCTUREID;

		$form = $model->getForm();

    $form->populate($model->params);
		$this->view->form = $form;

		$validForm = $this->getRequest()->isPost() && $form->isValid($model->params);

		if ($validForm) {
		  $frontendOptions = array ('lifetime' => null, 'automatic_serialization' => true ); // время жизни кэша - бесконечность
		  $backendOptions = array('cache_dir' => $this->_cfg['temp']['path']); // директория, в которой размещаются файлы кэша
		  $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

		  $reportsHash = (string)$model->getReportsHash();

		  $tag = get_class($this) . '_' . $reportNum . '_'
		      . md5(var_export($form->getValues(true), true))
		      . ($model->isExcelDifferentFromHTML && $isExcel ? '_xls' : ''). '_'
		      . $ident->STRUCTUREID;
		  $id = $tag . '_' . $reportsHash;

		  if (!$workbook = $cache->load($id)) {
		    $startTime = time();
		    $workbook = $model->getWorkbook($isExcel);
		    $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($tag));
		    if (time() - $startTime > 20)
		      $cache->save($workbook, $id, array($tag));
		  }

		  if ($isExcel) {
	  	  header('Content-Type: application/vnd.ms-excel');
  	  	header('Content-Disposition: attachment;filename="report'.$reportNum.'.xls"');
	  	  header('Cache-Control: max-age=0');

    		$objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
		    $objWriter->setPreCalculateFormulas(false);
		    $objWriter->save('php://output');
		  } else {
	  		$objWriter = PHPExcel_IOFactory::createWriter($workbook, 'HTML');
	  		$this->view->report = $objWriter;
		  }
		}
	}

	public function excelAction() {
//		$this->getRequest()->setParam('excel', true);
	  $this->showreportAction(true);
	  exit;

/*	  $auth = Zend_Auth::getInstance();
		$ident = $auth->getIdentity();
		if($ident->STRUCTURE_CODE != 0) $this->getRequest()->setParam('establishment', $ident->STRUCTUREID);

		$reportNum = $this->getRequest()->getParam('report', 0);
		$modelClass = 'Stat_Model_Report'.$reportNum;
		$model = new $modelClass();
		$workbook = $model->getWorkbook($this->getRequest()->getParams(), true);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="report'.$reportNum.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
		$objWriter->setPreCalculateFormulas(false);
		$objWriter->save('php://output');
		exit;*/
	}


	public function validationAction() {
	  $model = new Stat_Model_Reports();
	  $model->params = $this->getRequest()->getParams();
	  $this->view->validations = $model->getValidations();
	}
}