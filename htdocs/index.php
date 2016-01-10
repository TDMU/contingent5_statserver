<?php

define('BASE_PATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));

// Define path to application directory
define('APPLICATION_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    BASE_PATH . DIRECTORY_SEPARATOR . 'library',
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';  

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . 'configs' . DIRECTORY_SEPARATOR . 'application.ini'
);

$application->bootstrap()->run();