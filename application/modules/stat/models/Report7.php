<?php

// Уточненный план контингента студентов по специальностям

class Stat_Model_Report7 extends Stat_Model_Report5 {

	public function getForm() {
		$form = new Zend_Form();
		$form->setMethod('post');
		$form->setDecorators(array(array('ViewScript', array('viewScript' => 'reports/report7Form.phtml'))));

		$items = $this->getPeriodItems('REQ_MOZ_CONTMOVEMENT_PLAN_SPEC');
		$e = new Zend_Form_Element_Select('period',
		array('label' => 'Період',
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

		$excelAct = Zend_Controller_Action_HelperBroker::getStaticHelper('url')->url(array('controller' => 'reports', 'action' => 'excel', 'report' => 7));
		$e = new Zend_Form_Element_Submit('excel', array('label'=>'Excel', 'onclick'=>"document.forms[0].action='{$excelAct}'"));
		$form->addElement($e);

		$form->setElementDecorators(array('ViewHelper', 'Errors'));

		return $form;
	}

}