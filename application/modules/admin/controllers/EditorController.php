<?php

class Admin_EditorController extends Zend_Controller_Action {

	private $_cfg;

	public function init() {
		$this->getHelper('layout')->disableLayout();
		$this->_cfg = Zend_Registry::get('cfg');
	}

	public function showAction() {
		$ntid = $this->getRequest()->getParam('NODETYPEID');
		if(!$ntid) return;

		$model = new Admin_Model_Admin ( );
		$nodeeditorInfo = $model->getNodeEditorInfo($ntid);

		$mca = array_pad(array_reverse(explode('/', $nodeeditorInfo['EDITOR_FORM_URL'], 3)), 3, null);
		$this->_forward($mca[0], $mca[1], $mca[2]);
	}

	public function editAction() {
		$request = $this->getRequest();
		$id = $request->getParam ( 'NODEID', -1 );
		$ntid = $request->getParam ( 'NODETYPEID' );

		Zend_Dojo_View_Helper_Dojo::setUseDeclarative ();
		$this->view->addHelperPath('Uman/Dojo/View/Helper/', 'Uman_Dojo_View_Helper');
		$form = new Uman_Admin_Form();

		$form->setName ( 'nodeEditorForm' );
		$form->setAttrib( 'jsId', 'nodeEditorForm' );
		$form->setAction ( '/admin/editor/save' );
		$form->setMethod('post');
		$form->setEnctype('multipart/form-data');

		$dbModel = new Admin_Model_Admin ( );
		$nodeeditorInfo = $dbModel->getNodeEditorInfo($ntid);
		$fields = $dbModel->getEditorFields ( $nodeeditorInfo['DEFAULT_EDITOR_NODEID'] );

		$parentidExists = false;
		$nodetypeidExists = false;
		$js_oncreate = '';
		$js_beforesubmit = '';
		foreach($fields as $field) {
			if(!$field ['VISIBLE']) continue;
			if($field['FIELDNAME'] == 'PARENTID') $parentidExists = true;
			else if($field['FIELDNAME'] == 'NODETYPEID') $nodetypeidExists = true;
			$class = $field ['ELEMENT_CLASS'];
			$e = new $class ($field ['FIELDNAME']);
//			$e->setName($fieldName);
			if(!empty($field ['ELEMENT_CONFIG'])) {
/*
 * In php.ini must be:
 * allow_url_include = on;
 * allow_url_fopen = on;
*/
				$elem_cfg = new Zend_Config_Ini ('data://,' . $field ['ELEMENT_CONFIG'] );
				$elem_cfg = $elem_cfg->toArray ();
				$e->setOptions ( $elem_cfg );
			}
			$e->setLabel ( $field ['TITLE'] );
//			if (@$field['REQUIRED'])
		  $e->setRequired($field['REQUIRED']);
			switch ($class) {
				case 'Zend_Form_Element_Hidden':
					$decor = array ('ViewHelper' );
					break;
				case 'Zend_Form_Element_File':
//				$e->setMaxFileSize(1024);
					$decor = $form->elementFileDecorators;
					break;
				default:
					$decor = $form->elementDecorators;
			}
			$e->setDecorators($decor);
			$form->addElement($e);

			$js_oncreate .= isset($field['JS_ONCREATE'] ) ? $field['JS_ONCREATE'] . "\n" : '';
			$js_beforesubmit .= isset($field['JS_BEFORESUBMIT'] ) ? $field['JS_BEFORESUBMIT'] . "\n" : '';
		}

		$e = new Zend_Dojo_Form_Element_SubmitButton('save', array ('label' => 'Сохранить'));
		$e->setDecorators($form->buttonDecorators);
		$form->addElement($e);

/*		$e = new Zend_Dojo_Form_Element_Button('cancel', array('label' => 'Отмена', 'onClick' => "dijit.byId('editorDialog').onCancel()"));
		$e->setDecorators($form->buttonDecorators);
		$form->addElement($e);*/

		$form->addElements ( array (
			$form->createElement('hidden', 'NODEID')->setDecorators(array('ViewHelper')),
//			$form->createElement('hidden', 'NODETYPEID')->setDecorators(array('ViewHelper')),
			$form->createElement('hidden', 'MODE')->setDecorators(array('ViewHelper')))
		);

		if(!$nodetypeidExists)
			$form->addElement($form->createElement( 'hidden', 'NODETYPEID')->setDecorators (array('ViewHelper')));
		if(!$parentidExists)
			$form->addElement($form->createElement( 'hidden', 'PARENTID')->setDecorators (array('ViewHelper')));

		if($request->isPost()) {
			$res = $form->processAjax($request->getPost());
			if($res != 'true') {
				echo $res;
				exit;
			} else {
//			if($form->isValid ( $request->getPost () )) {
				$dbModel->saveFieldsValues ($fields, $request->getPost(), $nodeeditorInfo);
//				$dbModel->copyResources ( $id, false );
				echo $res;// "<textarea>$res</textarea"; //OK for AJAX request
				exit;
			}
		} else {
			if ($id == -1) { // New page
				$pid = $request->getParam ( 'PARENTID' );
				$dbModel->checkRight($pid);

				$id = $dbModel->getNextId('GEN_TEMP_UID');

				$values = array_merge($dbModel->getNewValues($fields, $nodeeditorInfo),
					array(
						'NODEID' => $id,
						'PARENTID' => $pid,
						'NODETYPEID' => $ntid,
						'MODE' => 'ADD',
						'SORTORDER' => $dbModel->getNextSortOrder($pid)
					)
				);
//				mkdir($this->_cfg['temp']['path'] . $id);
			} else {
				$dbModel->checkRight($id);
				$values = array_merge($dbModel->getFieldsValues($fields, $id, $nodeeditorInfo),
					array(
						'MODE' => 'EDIT'
					)
				);
				$dbModel->copyResources($id, $id, true);
			}

			$form->populate($values);


			$this->view->assign ( 'form', $form );
			$this->view->assign ( 'title', $nodeeditorInfo['TITLE'] );
			if (!empty($js_oncreate)) {
				$this->view->assign('js_oncreate', $js_oncreate);
				$this->view->assign('id', $id);
				$this->view->assign('baseUrl', $request->getBaseUrl());
			}
			if (!empty($js_beforesubmit)) {
				$this->view->assign('js_beforesubmit', $js_beforesubmit);
			}
		}
	}

	public function saveAction() {
		$this->_forward('edit');
	}

	public function rightsAction() {}

	public function changesAction() {}

}