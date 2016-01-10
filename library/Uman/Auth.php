<?php

class Uman_Auth {

	public static function login($username, $password, $useSession = true) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		$authAdapter = new Zend_Auth_Adapter_DbTable($db);
		
		$authAdapter->setTableName('V_USERS');
		$authAdapter->setIdentityColumn('USERNAME');
		$authAdapter->setCredentialColumn('PASSWD');
		
		$authAdapter->setIdentity($username);
		$authAdapter->setCredential($password);
				
		$auth = Zend_Auth::getInstance();
		if(!$useSession) $auth->setStorage(new Zend_Auth_Storage_NonPersistent());
		$result = $auth->authenticate($authAdapter);
		
		if ($result->isValid()) {
			$data = $authAdapter->getResultRowObject(array('NODEID', 'USERNAME', 'TITLE', 'PATH', 'STRUCTUREID', 'STRUCTURE_CODE'));

			$auth->getStorage()->write($data);

			return true;
		} else {
			return false;
		}
	}
	
	public static function getAccessID() {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		$sql = 'select NODEID, PHP_CONDITION from INFO_ACCESS';
		$accs = $db->fetchAll($sql);
		
		foreach ($accs as $acc) {
			if(preg_match($acc['PHP_CONDITION'], $_SERVER['REMOTE_ADDR']))
				return $acc['NODEID'];
		}
		return null;
	}
	
}