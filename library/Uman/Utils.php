<?php


class Uman_Utils {

	// Есть в классе Zend_Validate_File_Size
	public static function getFileSize($fileName) {
		$size = @filesize($fileName);
    if ($size > 104857.6) {
    	$size = round($size / 10485.76) / 100 . " Mb";
    } elseif ($size > 100) {
    	$size = round($size / 1024) . " Kb";
    } else $size = $size + " B";
    
		return $size;
	}
	
	public static function shortenFileName($fileName, $maxLeng=17, $indicate = '...') {
		if(mb_strlen($fileName, 'utf-8') > $maxLeng)
			$fileName = mb_substr($fileName, 0, $maxLeng - strlen($indicate), 'utf8') . $indicate;

		return $fileName;
	}
	
	
	public static function getXhtml($docid) {
		$xml = DOCS_PATH . $docid . '.xml';
		$file = file_get_contents($xml);

		$begin = stripos($file, '<content>');
		$end = stripos($file, '</content>');
		if($begin && $end)
			return substr($file, $begin + 9, $end - $begin - 10);
		else
			return $file;
/*
 		$xsl = APPLICATION_PATH . 'modules/site/controllers/xml2html.xsl';
		$doc = new DOMDocument();
		$doc->substituteEntities = TRUE;
		$doc->load($xsl);
		$proc = new XsltProcessor();
		$proc->importStylesheet($doc);
		
		@$doc->load($xml);
		
		$proc->setParameter('', 'contextPath', '/');
		$proc->setParameter('', 'nodeResPath', '/res/' . $docid . '/');
//		$proc->registerPHPFunctions('TypecontentController::widget');
		return $proc->transformToXml($doc);*/
	}
	
	
}