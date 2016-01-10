<?php

class Scheduler_SchedulerController extends Zend_Controller_Action {

	public function init() {
		$this->getHelper('layout')->disableLayout();
		$this->getHelper('viewRenderer')->setNoRender();
	}	
	
	public function runAction() {
		set_time_limit(1200);
		$model = new Scheduler_Model_Scheduler();
		$tasks = $model->run();
		echo "Executed $tasks tasks";
	}

}