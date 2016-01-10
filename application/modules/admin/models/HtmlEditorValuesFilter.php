<?php

class Admin_Model_HtmlEditorValuesFilter {

	public function afterLoadValues(&$values) {
		$file = $values['_FILE_EDITOR'];
		$begin = stripos($file, '<content>');
		$end = stripos($file, '</content>');
		if($begin && $end)
			$values['_FILE_EDITOR'] =  substr($file, $begin + 9, $end - $begin - 9);
		else
			$values['_FILE_EDITOR'] = $file;
	}


	public function beforeSaveValues(&$values) {
		if(strlen($values['_FILE_EDITOR']) == 0) return;

		$values['_FILE_EDITOR'] = '<?xml version="1.0"?>
<!DOCTYPE content
[
<!ENTITY % ISOnum PUBLIC
"ISO 8879:1986//ENTITIES Numeric and Special Graphic//EN//XML"
"ISOnum.pen">
%ISOnum;
]>
<content>' . $values['_FILE_EDITOR'] . '</content>';
	}

	public function newValues(&$values) {
	}

}