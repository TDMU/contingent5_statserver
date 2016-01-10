<?php

class Admin_Model_Changes {
	protected $_db = null; //Zend_Db_Adapter_Abstract
	
	public function __construct() {
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
	}
	
	public function getChanges($id, $fromDate, $toDate) {
		$sql = "select first 200 CT.NODEID, CT.NODETYPEID, CT.TITLE, 
(select VCT.HREF from V_CONTENT_TREE VCT where VCT.NODEID = CT.NODEID) as HREF, 
CT.CREATEDATE, CT.MODIFYDATE,
(select CIU.USERNAME from INFO_USERS CIU where CIU.NODEID = CT.CREATEUSERID) as CREATEUSER,
(select MIU.USERNAME from INFO_USERS MIU where MIU.NODEID = CT.MODIFYUSERID) as MODIFYUSER
from CONTENTTREE CT
where CT.PATH starting with (select CTP.FULL_PATH from CONTENTTREE CTP where CTP.NODEID = ?)
and cast(CT.MODIFYDATE as date) >= ?
and cast(CT.MODIFYDATE as date) <= ?
order by CT.MODIFYDATE desc";
		
		$data = $this->_db->fetchAll($sql, array($id, $fromDate, $toDate));
		
		foreach ($data as &$row) {
			$row['CREATEDATE'] = date('d.m.Y H:i', strtotime($row['CREATEDATE']));
			$row['MODIFYDATE'] = date('d.m.Y H:i', strtotime($row['MODIFYDATE']));
		}
		
		return $data;
	}
	
	public static function getForm() {
		Zend_Dojo_View_Helper_Dojo::setUseDeclarative ();
		
		$form = new Zend_Form();
		$form->setDecorators(array(array('ViewScript', array('viewScript' => 'editor/changesForm.phtml'))));
		
		$zdate = new Zend_Date();
		
		$e = new Zend_Dojo_Form_Element_DateTextBox('fromDate', array('label' => 'From', 'required' => true));
		$e->setAttrib('onchange', 'admin.currentManObj.refresh()');
		$form->addElement($e);

		$e = new Zend_Dojo_Form_Element_DateTextBox('toDate', array('label' => 'To', 'required' => true));
		$e->setAttrib('onchange', 'admin.currentManObj.refresh()');
		$form->addElement($e);
		
//		$e = new Zend_Dojo_Form_Element_Button( 'refresh', array ('label' => 'Refresh' ) );
//		$e->setAttrib('onchange', 'admin.currentManObj.refresh()');
//		$form->addElement($e);
		
		Zend_Dojo::enableForm($form);

		$form->setDefaults(array('toDate' => $zdate->getIso(), 'fromDate' => $zdate->/*addDay(-1)->*/getIso()));
		
		return $form;
	}
	
}