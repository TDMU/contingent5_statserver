<?php

class Scheduler_Model_Scheduler {
	protected $_db = null;
	
	public function __construct() {
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
	}
	
	public function run() {
		$sql = 'select IT.NODEID, IT.TIME_ACTION, IT.SCHED_ACTION, PARAMS_ACTION,
(select first 1 JT.RUN_TIME
from JOURNAL_TASKS JT
where JT.TASK_NODEID = IT.NODEID
and JT.TASK_ERROR = 0
order by 1 desc) as LAST_RUN
from INFO_TASKS IT
inner join CONTENTTREE CT
on (CT.NODEID = IT.NODEID)
where CT.VISIBLE = 1
order by CT.SORTORDER';
		
		$actions = $this->_db->fetchAll($sql);
		$task_exec_count = 0;
//		$defaultTimeZone = date_default_timezone_get();
		$cron = new Uman_CronParser();
		foreach($actions as $action) {
//			date_default_timezone_set('UTC');
			$cron->calcLastRan($action ['TIME_ACTION']);
//			date_default_timezone_set($defaultTimeZone);
			$cronLastRan = $cron->getLastRanUnix();
			$lastRun = strtotime($action['LAST_RUN']);
			if ($lastRun < $cronLastRan) {
				if (isset ( $action ['SCHED_ACTION'] )) {
					$exec_error = false;
					$do_log = false;
					$taskClass = trim($action['SCHED_ACTION']);
					try {
						$taskObj = new $taskClass();
						ob_start();
						$params = new Zend_Config_Ini ('data://,' . $action['PARAMS_ACTION'] );
						$do_log = $taskObj->execute($params);
						if($do_log) {
							$log = ob_get_contents();
							$task_exec_count++;
						}
						ob_clean();
					} catch(Exception $e) {
						$exec_error = true;
						$log = $e->getMessage() . "\n\n" . $e->getTraceAsString();
					}
					if($do_log || $exec_error) {
						echo "<pre>$log</pre>";
						
						$sql = 'insert into JOURNAL_TASKS (TASK_NODEID, RUN_TIME, TASK_LOG, TASK_ERROR) values (?, ?, ?, ?)';
						$this->_db->query( $sql, array($action['NODEID'], date('Y/m/d H:i:s', $cronLastRan), $log, ($exec_error ? 1 : 0)));
					}
				}
			}
		}

		return $task_exec_count;
	}

}