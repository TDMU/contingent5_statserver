<?php

require_once 'Zend/Controller/Action.php';

class Admin_FilemanagerController extends Zend_Controller_Action {
	private $_cfg;
	private $docid;

	private $imgExts = array('jpg', 'png', 'gif');
	private $imgValidators = array(
		array('Size', false, '500kB'),
		array('ImageSize', false, array('maxwidth' => 1024, 'maxheight' => 768))
	);
	
	private $fileExts = array('zip', 'exe', 'doc', 'xls', 'pps', 'pdf');
	private $fileValidators = array(
		array('Size', false, '30MB'),
	);
	
	public function init() {
		$this->_cfg = Zend_Registry::get('cfg');
		$this->docid = $this->getRequest()->getParam('docid', 'unknown_doc');
	}

	public function showAction() {
		$this->view->headScript()
			->appendFile('/modules/admin/js/dojo/dojo.js');
		$this->view->headLink()
			->appendStylesheet('/modules/admin/css/filemanager.css');
			
		$this->view->type = $this->getRequest()->getParam('type');
//		$this->view->view = $this->getRequest()->getParam('view', 'thumbs');
	}
	
	private function upload() {
		$todir = $this->_cfg['temp']['path'] . $this->getRequest()->getParam('docid', 'unknown_doc');
		if(!file_exists($todir)) mkdir($todir);
		
		$adapter = new Zend_File_Transfer_Adapter_Http(array('ignoreNoFile' => true));
		$filename = $adapter->getFileName('upload', false);
		$adapter
			->addValidator('Extension', false, $this->getRequest()->getParam('type') == 'images' ? $this->imgExts : $this->fileExts)
			->addValidators($this->getRequest()->getParam('type') == 'images' ? $this->imgValidators : $this->fileValidators)
			->addFilter('Rename', array('target' => $todir.DIRECTORY_SEPARATOR.iconv('utf-8', FS_CHARSET, $filename), 'overwrite' => true));
		

//		$adapter->setDestination($todir);

		$result = new stdClass();
		$result->messages = array();
		$result->uploadedUrl = '';

		if(!$adapter->isValid()) {
			$result->messages = $adapter->getMessages();
		} else {
			if($adapter->receive() && $adapter->isUploaded()) {
				$result->uploadedUrl = ($this->getRequest()->getParam('type') == 'images' ? '' : 'downloads/') . $filename;
			}
		}

		$result->CKEditorFuncNum = $this->getRequest()->getParam('CKEditorFuncNum');
		return $result; 
	}

	public function quickuploadAction() {
		$this->getHelper('layout')->disableLayout();
		$this->view->uploadInfo = $this->upload();
	}
	
	public function thumbAction() {
		$imageFile = $this->_cfg['temp']['path']
				. $this->getRequest()->getParam('docid')
				. DIRECTORY_SEPARATOR 
				. iconv('utf-8', FS_CHARSET, $this->getRequest()->getParam('img'));
				
		$thumb = Uman_Thumb_Factory::create($imageFile);
		$thumb->resize(120, 110);
		$thumb->show();
		exit;
	}

	public function filesAction() {
		$this->getHelper('layout')->disableLayout();
		$dir = $this->_cfg['temp']['path'] . $this->getRequest()->getParam('docid', 'unknown_doc');
		
		$files = array();
		if(file_exists($dir)) {
			$fileNames = @scandir($dir);
			foreach($fileNames as $file) {
				if($file != '.' && $file != '..') {
					$file = iconv(FS_CHARSET, 'utf-8', $file);
					$filename = $dir . DIRECTORY_SEPARATOR . $file;
					if(array_search(pathinfo($filename, PATHINFO_EXTENSION),
						$this->getRequest()->getParam('type') == 'images' ? $this->imgExts : $this->fileExts) === FALSE) continue;

					$fileInfo = array();
					$fileInfo['fullname'] = $filename;
					$fileStat = @stat($filename);
					$fileInfo['name'] = $file;
					$fileInfo['size'] = $fileStat[7];
					$fileInfo['mtime'] = $fileStat[9];
					array_push($files, $fileInfo);
				}
			}
		}

		$this->view->files = $files;
	}
	

	public function executeAction() {
		$this->_forward($this->getRequest()->getParam('act', 'unknown_action'));
	}

	public function refreshAction() {
		$this->_forward($this->getRequest()->getParam('view', 'files'));
	}
	
	public function thumbsAction() {
		$this->filesAction();
	}
	
	public function delAction() {
		$files = $this->getRequest()->getParam('files');
		$res_path = $this->_cfg['temp']['path'] . $this->docid . DIRECTORY_SEPARATOR;
		foreach (explode(',', $files) as $file) {
			unlink($res_path . iconv('utf-8', FS_CHARSET, $file));
		}
		
		$this->_forward('refresh');
	}

	public function uploadrefAction() {
		$uploadInfo = $this->upload();
		if(count($uploadInfo->messages) > 0) {
			throw new Zend_Controller_Action_Exception(implode('<br/>', $uploadInfo->messages));
		} else {
			$this->_forward('refresh');
		}
	}
	
	
	
}