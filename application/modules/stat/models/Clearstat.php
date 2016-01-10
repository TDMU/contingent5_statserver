<?php

class Stat_Model_Clearstat {
	protected $_db = null;
	protected $_cfg = null;

	public function __construct() {
		$this->_cfg = Zend_Registry::get ( 'cfg' );
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter ();
	}

	public function execute($params) {
		$requestkey= $params->request_key;
		$delete_reports= $params->delete_reports;
		$real_execute = false;
		if ($requestkey !== null) {
			$this->clear($requestkey, $delete_reports);
			if ($delete_reports) echo "Удалены все отчеты требования $requestkey";
			else echo "Удалены все данные отчетов требования $requestkey";
			$real_execute = true;
		}
		return $real_execute;
	}

	private function clear($requestkey, $delete_reports) {
		$this->_db->beginTransaction();

		$sql = 'delete from STAT_DATA
where (DATAREPORTID in (select SD.DATAREPORTID
                        from STAT_DATA SD
                        inner join CONTENTTREE CT
                        on CT.NODEID = SD.DATAREPORTID
                        where CT.FULL_PATH starting with
                             (select CT1.PATH from CONTENTTREE CT1 where CT1.NODE_KEY = ?)))';
		$this->_db->query($sql, $requestkey);

		$sql = 'delete from STAT_VALIDATION
where (DATAREPORTID in (select SD.DATAREPORTID
                        from STAT_DATA SD
                        inner join CONTENTTREE CT
                        on CT.NODEID = SD.DATAREPORTID
                        where CT.FULL_PATH starting with
                             (select CT1.PATH from CONTENTTREE CT1 where CT1.NODE_KEY = ?)))';
		$this->_db->query($sql, $requestkey);

		if ($delete_reports){
			$sql = "delete from CONTENTTREE CT1
where (CT1.NODEID in (select CT.NODEID
                        from V_CONTENT_TYPE CT
                        where CT.FULL_PATH starting with
                             (select CT1.PATH from CONTENTTREE CT1 where CT1.NODE_KEY = ?)
                             and CT.T_NODE_KEY = 'T_STAT2_REPORT'))";

			$this->_db->query($sql, $requestkey);
		}

		$this->_db->commit();
	}
}