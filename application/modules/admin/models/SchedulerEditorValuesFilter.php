<?php

class Admin_Model_SchedulerEditorValuesFilter {
	protected $_cfg = null;
	protected $_db = null;

	public function __construct() {
		$this->_cfg = Zend_Registry::get('cfg');
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
	}

	public function afterSaveValues($values) {
		$sql = 'select (select AV1.VAL
from V_ADD_VALUES AV1
where AV1.NODEID = CT.NODEID
and AV1.FIELDNAME = ?
) as SCHED_ACTION
from V_CONTENT_TYPE CT
where CT.T_NODE_KEY = ?
and CT.VISIBLE = 1';
		
		$this->_db->beginTransaction();
		$actions = $this->_db->fetchAll($sql, array ('_ADD_ACTION', 'T_SHEDULER_TASK'));
		$this->_db->commit();
		$sched_action = '';
		foreach($actions as $action)
				$sched_action .= $action['SCHED_ACTION'] . "\n";
		
		file_put_contents($this->_cfg['crontab']['path'].'uman', $sched_action);
	}
	
	
}