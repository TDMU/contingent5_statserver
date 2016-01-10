<?php

class Zend_View_Helper_AuthInfoHelper {
	
	public function authInfoHelper() {
		return Zend_Registry::get('auth_ident');
	}
	
}