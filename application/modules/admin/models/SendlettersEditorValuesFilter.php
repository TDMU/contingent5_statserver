<?php

class Admin_Model_SendlettersEditorValuesFilter {
	
	protected $_db = null;
	
	public function __construct() {
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter ();
	}
	
	public function afterSaveValues(&$values) {
		if($values['SENDNOW']) {
			$this->_db->beginTransaction ();
			$letter_keys = new Zend_Config_Ini ( 'data://,' . 'letter_keys=' . $values ['NODE_KEY'] );
			ob_start ();
			$model = new Stat_Model_Sendletters ( );
			$result = $model->execute ( $letter_keys );
			ob_clean ();
		}
	}
}