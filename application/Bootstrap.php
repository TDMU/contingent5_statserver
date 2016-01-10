<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
    protected function _initAutoload() {
			$moduleLoader = new Zend_Application_Module_Autoloader(array(
					'namespace' => '', 
					'basePath'  => APPLICATION_PATH));
			return $moduleLoader;
    }

		protected function _initCfg() {
			$cfg = $this->getOptions();
			Zend_Registry::set('cfg', $cfg);
//			if(!defined('DOCS_PATH'))	
			define('DOCS_PATH', $cfg['docs']['path']);
			define('FS_CHARSET', $cfg['fs_charset']);
		}

		protected function _initView() {
        // Initialize view
        $view = new Zend_View();
        $view->doctype('XHTML1_STRICT');
//        $view->headTitle($view->translate('skin.oficial_site_info'));
        
//        $view->env = APPLICATION_ENV;

        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);

        // Return it, so that it can be stored by the bootstrap
        return $view;
    }

		protected function _initTranslate() {
			$frontendOptions = array ('lifetime' => 60 * 60 * 24, 'automatic_serialization' => true ); // время жизни кэша - 24 часа
		
			$cfg = $this->getOptions();
			$backendOptions = array ('cache_dir' => $cfg['temp']['path']); // директория, в которой размещаются файлы кэша
		

			$cache = Zend_Cache::factory ( 'Page', 'File', $frontendOptions, $backendOptions );
			Zend_Translate::setCache ( $cache );
		
			$translate = new Zend_Translate ( Zend_Translate::AN_GETTEXT, APPLICATION_PATH . 'locales', 'auto', 
					array('scan' => Zend_Translate::LOCALE_DIRECTORY, 'disableNotices' => true) );
		
			Zend_Registry::set ( 'Zend_Translate', $translate );
    }
}