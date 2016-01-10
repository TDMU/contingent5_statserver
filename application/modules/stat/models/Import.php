<?php

class Stat_Model_Import {
	
	public static function getUploadForm() {
		$form = new Zend_Form ( );
		$form->setAction('')
				->setMethod(Zend_Form::METHOD_POST)
				->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);

		$form->setDecorators ( array (array ('ViewScript', array ('viewScript' => 'import/uploadForm.phtml' )), 'Errors' ) );
             
		$f = new Zend_Form_Element_File ( 'file');//, array ('label' => 'Веберіть файл пакету') );
//		$f->setAttrib ( 'style', 'width: 600px' );
		$f->setRequired();
		$f->addValidator ( 'Count', false, 1 );
		$f->addValidator ( 'Size', false, 204800 );
		$f->addValidator ( 'Extension', false, 'zip,pkg' );
		$cfg = Zend_Registry::get ( 'cfg' );
		$target = $cfg['temp']['path'] . Zend_Auth::getInstance()->getIdentity()->NODEID . '.zip';
		$f->addFilter ( 'Rename', array ('target' => $target, 'overwrite' => 'true' ) );
		$form->addElement ( $f );
		
		$e = new Zend_Form_Element_Submit ( 'submit', array ('label' => 'Завантажити' ) );
		$form->addElement ( $e );
		
		$e = new Zend_Form_Element_Hidden( 'step', array('value' => 1) );
		$form->addElement ( $e );
		
		return $form;
	}
	
	
	
	public static function getPackageVersion($zipFileName) {
		$zip = new ZipArchive ( );
		if ($zip->open ( $zipFileName ) !== TRUE)
			throw new Zend_Exception ( 'Error opening ' . $zipFileName );
			
		if(!($zip->locateName('info.xml', ZIPARCHIVE::FL_NOCASE) === FALSE)) return '09';
		else {
			$xml = simplexml_load_string($zip->getFromName('data.xml'));
			$zip->close ();
			return str_replace('.', '', $xml->info->version);
		}
	}
	
}