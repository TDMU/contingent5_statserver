<?php

require_once 'Zend/Controller/Action.php';

class Stat_ImportController extends Zend_Controller_Action {

	private $_model;
	private $xmlHeader = '<?xml version="1.0" encoding="utf-8"?>';

	private function getModel($packageFile) {
		if(!$this->_model) {
			$version = Stat_Model_Import::getPackageVersion($packageFile);
			$className = 'Stat_Model_Import'.$version;
			$this->_model = new $className();
		}
		return $this->_model;
	}

	private function sendArrayAsXml($info) {
		$xml = new Uman_Array2Xml('package');
		$xml->createNode($info);
		header ("Content-type: text/xml");
		echo $xml;
	}

	public function showAction() {
		$this->getHelper('viewRenderer')->setNoRender();

		$request = $this->getRequest();

		if ($request->isPost()) {
			$stat_app = $request->getParam('stat_app', 0);
			if($this->getRequest()->getParam('step', 1) == 1) {
				$form = Stat_Model_Import::getUploadForm();
				if(!$stat_app && !$form->isValid($request->getPost())
						|| !$form->file->receive()) {
					echo $form;
				} else {
					$packageFile = $form->file->getFileName ();
					try {
						$info = $this->getModel($packageFile)->checkPackage($packageFile);
						if(is_array($info)) {
							if($stat_app) {
								$info['filename'] = $packageFile;
								$this->sendArrayAsXml($info);
							} else {
								$form = $this->getModel($packageFile)->getInfoForm();
								$form->populate(array('filename' => $packageFile, 'validreports' => $info['validreports']));
								echo $this->view->partial($form->getDecorator('ViewScript')->getOption('viewScript'),
									array('element' => $form, 'info' => $info));
							}
						} else {
							echo $info;
						}
					} catch (Zend_Exception $e) {
						$form->addError($e->getMessage());
						echo $form;
					}
				}
			} elseif($request->getParam('step') == 2) {
				try {
					$packageFile = $request->getParam('filename');
					$validreports = $request->getParam('validreports');
					$importErrorMessages = $this->getModel($packageFile)->doImport($packageFile, $validreports);
					unlink($packageFile);
					if(!$importErrorMessages) {
					  if($stat_app) {
					    header ("Content-type: text/xml");
					    echo "$this->xmlHeader<result>Імпорт завершено успішно</result>";
					  } else {
					    echo '<h2 style="text-align: center">Імпорт завершено успішно</h2>';
					  }
					} else {
					    echo '<h2 style="text-align: center">Під час імпорту були наступні помилки:</h2>';
					    echo $importErrorMessages;
					}
				} catch (Exception $e) {
					header ("Content-type: text/xml");
					echo "$this->xmlHeader<result>{$e->getMessage()}</result>";
				}
			}
		} else {
			$form = Stat_Model_Import::getUploadForm();
			echo $form;
		}
	}


	public function uploadAction() {
		$this->getHelper('layout')->disableLayout();
		$this->getRequest()->setParam('stat_app', 1);
		$this->showAction();
	}

	public function plansAction() {
		$this->getHelper('viewRenderer')->setNoRender();
		$request = $this->getRequest();

		if ($request->isPost()) {
			$form = Stat_Model_ImportPlans::getUploadForm();
			if(!$form->isValid($request->getPost())
						|| !$form->file->receive()) {
					echo $form;
				} else {
					$xlsFile = $form->file->getFileName ();
					try {
						$info = Stat_Model_ImportPlans::doImportPlans($xlsFile);
            echo $info;
					} catch (Zend_Exception $e) {
						$form->addError($e->getMessage());
						echo $form;
					}
				}

		}	else {
			$form = Stat_Model_ImportPlans::getUploadForm();
			echo $form;
		}
	}
}