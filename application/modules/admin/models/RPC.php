<?php

class Admin_Model_RPC {

	public function delNode($id) {
		$dbModel = new Admin_Model_Admin();
		$dbModel->delNodes($id);
		return true;
	}

/*	public function changeParent($id, $pid) {
		$dbModel = new Admin_Model_Admin();
		$dbModel->changeParent($id, $pid);
		return true;
	}*/
	
	public function clearNodeTemp($id) {
		$dbModel = new Admin_Model_Admin();
		$dbModel->clearNodeTemp($id);
		return true;
	}

	public function getAllowedTypes($id){
		$dbModel = new Admin_Model_Admin();
		return $dbModel->getAllowedTypes($id); 
	}
	
	
	
	public static function getRightsContent($userid, $id, $accessid, $fullRights = 0) {
		$dbModel = new Admin_Model_Rights();
		$data = array();
		$data['rightlist'] = $dbModel->getListAllowRightsWInherit($id);
		$data['content'] = $dbModel->getRightsActions($userid, $id, $accessid, $fullRights);
		return $data;
	}

	public function setRight($id, $userid, $rightid, $accessid, $allow){
		$dbModel = new Admin_Model_Rights();
		$dbModel->doRightsUpdate($id, $userid, $rightid, $accessid, $allow);
		return true; 
	}

	
	public static function getChangesContent($pid, $fromDate, $toDate) {
		$dbModel = new Admin_Model_Changes();
		return $dbModel->getChanges($pid, $fromDate, $toDate);
	}
	
}