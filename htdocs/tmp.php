<?php
	define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
	define('APPLICATION_PATH', BASE_PATH . '/application/');
	defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
	
	set_include_path ( '.' . PATH_SEPARATOR . BASE_PATH . '/library' . PATH_SEPARATOR . get_include_path () );
	require_once 'Zend/Config/Ini.php';

	$cfg = new Zend_Config_Ini (APPLICATION_PATH . 'configs/application.ini', APPLICATION_ENV);
	
	$path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
//	$func = $GET['$func'];
	$func = array_shift($path);
	$docid = array_shift($path);
	$filename = iconv('utf-8', $cfg->fs_charset, urldecode(array_shift($path)));

//echo "hello di=".$docid." fn=".$filename;
  
	if ( (ereg("^[0-9]{1,7}$", $docid) === false ) ||
		( !(strpos($filename, "..") === false )) )  {
		exit();
	}
	header ( 'Content-Type: application/octet-stream' );
	$res = $cfg->temp->path . $docid . '/'.$filename;
	readfile($res);