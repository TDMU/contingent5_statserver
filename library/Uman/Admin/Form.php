<?php

class Uman_Admin_Form extends Zend_Dojo_Form {
	
	public $elementDecorators = array (
		'DijitElement',
		'Errors',
		array(array('data'  => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
		array('Label', array('tag' => 'td')),
		array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);

	public $elementFileDecorators = array (
		'File',
		'Errors',
		array(array('data'  => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
		array('Label', array('tag' => 'td')),
		array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);

	public $buttonDecorators = array(
		'DijitElement',
		array(array('data' => 'HtmlTag'),  array('tag' => 'td', 'class' => 'element')),
		array(array('Label' => 'HtmlTag'), array('tag' => 'td',  'placement' => 'prepend')),
		array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);
        
	public function init() {
/*		$this->setDecorators(array(
            'FormElements',
			array('HtmlTag', array('tag' => 'table', 'class' => 'form')),
		array('TabContainer', array(
                'id' => 'tabContainer',
                'style' => 'width: 600px; height: 500px;',
                'dijitParams' => array(
                    'tabPosition' => 'top'
                ),
            )),
			'FormErrors',
            'DijitForm',
        ));
*/		
	}
        
	public function loadDefaultDecorators() {
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'table', 'class' => 'form')),
			'FormErrors',
			'DijitForm',
		));
	}
}