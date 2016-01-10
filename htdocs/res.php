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
	
	if ( (ereg("^[0-9]{1,7}$", $docid) === false ) ||
		(!(strpos($filename, "..") === false )) )  {
		exit();
	}
	
	if ($func == 'docs') {
		$res = $cfg->docs->path.$docid.'.bin';
	} else {
		$res = $cfg->docs->path.$docid.'/'.$filename;
	}
  
	$last_modified_time = filemtime($res);
	$etag = md5_file($res);

	header("Last-Modified: ".gmdate('r', $last_modified_time)." GMT");
	header("Etag: $etag");

	if ((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time) ||
    (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag)) {
    header("{$_SERVER['SERVER_PROTOCOL']} 304 Not Modified");
    exit;
	} 

	header("Content-Type: application/octet-stream");
	header("content-disposition: attachment; filename=\"{$filename}\"");   
	header("Content-Length: " . filesize($res));
        
	readfile($res);