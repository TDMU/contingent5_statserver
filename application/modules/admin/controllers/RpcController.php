<?php

require_once 'Zend/Controller/Action.php';;

class Admin_RpcController extends Zend_Controller_Action {

	public function smdAction() {
		$class = 'Admin_Model_RPC';
		$server = new Zend_Json_Server();
		$server->getServiceMap()->setDojoCompatible(true);
		$server->getServiceMap()->setTransport('POST')
			->setTarget($this->getHelper('url')->url(array('action'=>'service')))
			->setId($this->getHelper('url')->url(array('action'=>'service')));
		$server->setClass($class);
		$smd = $server->getServiceMap();
		echo $smd;
		exit;
	}

	public function serviceAction() {
		$class = 'Admin_Model_RPC';
		$server = new Zend_Json_Server();
		$server->setClass($class);
		$server->setAutoEmitResponse(true);
		$server->handle();
		exit;
	}
	
}