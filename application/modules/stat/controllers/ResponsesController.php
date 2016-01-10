<?php

require_once 'Zend/Controller/Action.php';

class Stat_ResponsesController extends Zend_Controller_Action {

	public function showAction() {
		$model = new Stat_Model_Responses();
		$params = $this->getRequest()->getParams();

		$form = $model->getForm($params['request_key']);

		$form->populate($params);

		$this->view->form = $form;

		$this->view->requests = $model->getRequestsByYear($form->getValue('year'), $form->getValue('report'), $params['request_key']);
		$this->view->est2reports = $model->getEstablishments2Reports($form->getValue('year'), $form->getValue('report'), $params['request_key']);

//		file_put_contents('d:\\temp\\requests.txt', var_export($this->view->requests, true));
//		file_put_contents('d:\\temp\\est2reports.txt', var_export($this->view->est2reports, true));
	}
}