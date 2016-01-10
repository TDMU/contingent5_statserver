<?php
class Uman_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract {
 	protected $cache;
	
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$module = $request->getModuleName();
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		
		if($module == 'auth' || $controller == 'error') return;
		
		$cache = $this->getCoreCache(60*60*24); // 24 часа сохраняются права для гостя 
				
		$se_mask = "(Yandex|Googlebot|StackRambler|Yahoo\! Slurp|WebAlta|msnbot|Baiduspider\+|Speedy Spider|Sogou web spider|Subscribe|Twiceler|GingerCrawler)";
		$isBot = ereg($se_mask, $_SERVER['HTTP_USER_AGENT']);

		if(!$isBot) {
			Zend_Session::start();
			$ns = new Zend_Session_Namespace('tmp');
			$ns->remote_addr = $_SERVER['REMOTE_ADDR'];
			$ns->http_user_agent = $_SERVER['HTTP_USER_AGENT'];
		}
		
		$auth = Zend_Auth::getInstance();
		$accessID = Uman_Auth::getAccessID();
			
		if(!$isBot && $auth->hasIdentity()) {
			$identity = $auth->getIdentity();
		} else {
			if(!$identity = $cache->load("guest_ident_$accessID")) {
				if(!Uman_Auth::login('guest', 'guest', false))
					throw new Zend_Exception('The trouble is that guest login fault :-)');

				$auth = Zend_Auth::getInstance();
				$identity = $auth->getIdentity();
				$cache->save($identity, "guest_ident_$accessID");
			}
		}

		if($identity->USERNAME != 'guest') {
			$ns = new Zend_Session_Namespace('acl');
			$acl = $ns->acl;
		} else {	
			if(!$acl = $cache->load("guest_acl_$accessID")) {
				$acl = new Uman_Acl($identity->NODEID, $identity->PATH);
				$cache->save($acl, "guest_acl_$accessID");
			}
		}

		Zend_Registry::set('auth_ident', $identity);
		Zend_Registry::set('acl', $acl);
		
		if($acl->isMCAExists("{$module}_{$controller}_{$action}")) {
			if(!$acl->isMCAExecuteAllowed("{$module}_{$controller}_{$action}")) {
				$this->authRedirect($request);
			}
		} else {	
			$right = 'RIGHT_' . strtoupper($controller . '_' . $action);
			if(!defined('RIGHT')) define('RIGHT', $right);

			if($module == 'admin' || $controller == 'admin') {
				$request->setParam('show_error', 1);  // Для админки отображать полные сообщения об ошибках
				return;
			}

			$dbModel = new Model_Common();

			if(@!is_numeric($request->docid) && !$request->ctxid) {// Если в url нет параметра docid
				$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					
				$href = null;
				$uri = $request->getRequestUri();
				if(strpos($uri, '.htm')) {
					$docid = substr($uri, strrpos($uri, '/') + 1, strrpos($uri, '.htm') - strrpos($uri, '/') - 1);
					if(@is_numeric($docid))
					$newNode = $dbModel->getNewNode($docid);
					if(@$newNode) {
						$href = $newNode['HREF'];
						$redirector->setCode(301);
					} else {
						throw new Zend_Controller_Dispatcher_Exception("Resource '$docid' not found.");
					}
				}
					
				if(!$href) {
					Zend_Locale::setDefault('en');
					$locale = new Zend_Locale();// (Zend_Locale::BROWSER);
					$language = $locale->getLanguage ();
					$siteID = $dbModel->getSite($language);
					if (!$siteID)
					throw new Zend_Exception('Main site not found');
					$docid = $siteID['NODEID'];
					$href = "page/show/docid/{$docid}";
				}

				$redirector->gotoUrlAndExit($href);
			}

			$docid = $request->docid;//substr($request->docid, 0, strlen($request->docid)-5); // Remove .html
			if(@!is_numeric($docid)) $docid = $dbModel->getNodeID($docid);

			$ctxid = $request->getParam('ctxid', $docid);
			$nodeInfo = $dbModel->getNodeInfo($ctxid);
			Zend_Registry::set('nodeInfo', $nodeInfo);

			$request->setParam('docid', $docid);
			$request->setParam('ctxid', $ctxid);


			if(!$acl->isPathAllowed($nodeInfo['FULL_PATH'])
				|| ($ctxid != $docid && !$acl->isNodeByPathAllowed($ctxid))) {
				$this->authRedirect($request);
			}
		}
	}
	
	private function authRedirect(Zend_Controller_Request_Abstract $request) {
		$request->setModuleName('auth')
			->setControllerName('page')
			->setActionName('login')
			->setParam('resource', $_SERVER['REQUEST_URI'])
			->setParam('message', 'Access denied.');
	}

  public function getCoreCache($life_time = null)
    {
        //Если $life_time не передана, то берем из конфига
//        if(!$life_time) $life_time = $this->config->life_time;

        /* Если объект кэша еще не вызывался или нужно создать аналогичный объект, но с другими настройками
             создаем! */
        if (!$this->cache) {// || $life_time != $this->cache->getOption('lifetime')) {
            require_once 'Zend/Cache.php';
            $front_opt = array(
               // Вообще кэшировать ли? Полезно для отладки добавлять такую опцию в настройки
               'caching' => true, //$this->config->caching,
               'lifetime' => $life_time,
               /* Сериализует автоматически объекты и масивы перед записью в файл
                    вообще это работает медленней, но так гораздо удобнее */
               'automatic_serialization' => true
            );
            $back_opt = array(
                'cache_dir' => BASE_PATH . '/tmp/',
                // Вот тут нужно поэкспериментировать...
                /** 'read_control_type' => 'adler32', @todo */
                // Тут тоже
//                'hashed_directory_level' => '1',
                // Права доступа к создаваемым файлам
//                'hashed_directory_umask' => '0766'
            );
            $this->cache = Zend_Cache::factory('Core', 'File', $front_opt, $back_opt);
        }

        return $this->cache;
    }
	
}