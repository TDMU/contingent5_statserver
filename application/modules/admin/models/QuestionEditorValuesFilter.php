<?php

class Admin_Model_QuestionEditorValuesFilter {

	public function afterLoadValues(&$values) {
		$values['_FILE_XML_EDITOR'] = $this->xsltTransform($values['_FILE_XML_EDITOR'], '../application/modules/admin/models/xml2q.xsl');
	}


	public function beforeSaveValues(&$values) {
		$xmlHeader = '<?xml version="1.0"?>
<!DOCTYPE content
[
<!ENTITY % ISOnum PUBLIC
"ISO 8879:1986//ENTITIES Numeric and Special Graphic//EN//XML"
"ISOnum.pen">
%ISOnum;
]>';

		$xmlStr = $xmlHeader . '<content>' . $values['_FILE_XML_EDITOR'] . '</content>';
		
		$doc = $this->xsltTransform($xmlStr, '../application/modules/admin/models/q2xml.xsl', true);
		$values['_FILE_XML_EDITOR'] = $doc->saveXml();
		
		$q = $doc->getElementsByTagName('question')->item(0)->nodeValue;
		$values['TITLE'] = $values['NODEID'] . ' ' . mb_substr($q, 0, 30, 'UTF-8') . (mb_strlen($q, 'UTF-8') > 30 ? ' ...' : '');
		if(strlen($values['TITLE']) == 0) throw new Zend_Exception('Please enter text of the question.');
	}


	private function xsltTransform($xmlStr, $xslFile, $toDom = false) {
		$doc = new DOMDocument();
		$doc->substituteEntities = TRUE;
//		$doc->resolveExternals = TRUE;
		
		$doc->load ( $xslFile );
		$proc = new XsltProcessor ( );
		$proc->importStylesheet ( $doc );
		
		$doc->loadXML($xmlStr);
		
		if($toDom) return $proc->transformToDoc( $doc );
		else return $proc->transformToXml ( $doc );
	}
	
	public function newValues(&$values) {
		if(!isset($values['_FILE_XML_EDITOR'])) {
			$values['_FILE_XML_EDITOR'] = file_get_contents('../application/modules/admin/models/q1template.html');
		}
	}

}