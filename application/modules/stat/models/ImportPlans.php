<?php

class Stat_Model_ImportPlans {

	protected $_acl = null;
	protected $_db = null;

	public function __construct() {
		$this->_acl = Zend_Registry::get ( 'acl' );
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter ();
	}


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
		//		$f->addValidator ( 'Size', false, 204800 );
		$f->addValidator ( 'Extension', false, 'xls' );
		$cfg = Zend_Registry::get ( 'cfg' );
		$target = $cfg['temp']['path'] . Zend_Auth::getInstance()->getIdentity()->NODEID . '.xls';
		$f->addFilter ( 'Rename', array ('target' => $target, 'overwrite' => 'true' ) );
		$form->addElement ( $f );

		$e = new Zend_Form_Element_Submit ( 'submit', array ('label' => 'Завантажити' ) );
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

	public static function doImportPlans($xlsFileName) {
		$acl = Zend_Registry::get ( 'acl' );
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$xls_reader = new PHPExcel_Reader_Excel5();
		$workSheet = $xls_reader->load($xlsFileName)->getActiveSheet();
		$structures = array();

		$kindid = $workSheet->getCellByColumnAndRow(5, 2)->getValue();
		$eduformparamid = $workSheet->getCellByColumnAndRow(6, 2)->getValue();
		$eduformvalueid = $workSheet->getCellByColumnAndRow(7, 2)->getValue();
		$edubaseparamid = $workSheet->getCellByColumnAndRow(8, 2)->getValue();
		$edubasevalueid = $workSheet->getCellByColumnAndRow(9, 2)->getValue();
		$countrytypeparamid = $workSheet->getCellByColumnAndRow(10, 2)->getValue();
		$countrytypevalueid = $workSheet->getCellByColumnAndRow(11, 2)->getValue();
		$reportkindparamid = $workSheet->getCellByColumnAndRow(12, 2)->getValue();
		$reportkindvalueid = $workSheet->getCellByColumnAndRow(13, 2)->getValue();
		$specialitycodeparamid = $workSheet->getCellByColumnAndRow(14, 2)->getValue();

		for ($i=$workSheet->getCellByColumnAndRow(0, 2)->getValue();$i<=$workSheet->getCellByColumnAndRow(1, 2)->getValue(); $i++)
		{

			$stuctureid = (integer)$workSheet->getCellByColumnAndRow(0, $i)->getValue();
			if ($stuctureid > 0)
			{
				if (!in_array($stuctureid, array_keys($structures)))
				{

					$nodeid = $db->fetchOne ( 'select UID from SP_GEN_UID' );
					$sql = "insert into CONTENTTREE (NODEID, PARENTID, NODETYPEID, TITLE, CREATEUSERID)
            values(?,
           ?,
           (select CT.NODEID
            from CONTENTTREE CT
            where CT.NODE_KEY = 'T_STAT2_REPORT'),
           (select CT.TITLE
            from CONTENTTREE CT
            where CT.NODEID = ?),
            ?)
            ";
					$db->query($sql,
					array($nodeid,
					$workSheet->getCellByColumnAndRow(2, 2)->getValue(),
					$stuctureid,
					$acl->userid
					) );


					$sql = "insert into INFO_ADD_VALUES_REF (NODEID, FIELDID, VAL)
values (?, (select FF.NODEID
from INFO_FIELDS FF
  inner join CONTENTTREE CT_F
    on (CT_F.NODEID = FF.NODEID)
  inner join CONTENTTREE CT_E
    on (CT_E.NODEID = CT_F.PARENTID)
  inner join INFO_TYPES IT
    on(IT.DEFAULT_EDITOR_NODEID = CT_E.NODEID)
  inner join CONTENTTREE CT_T
    on (CT_T.NODEID = IT.NODEID)
where CT_T.NODE_KEY = 'T_STAT2_REPORT'
  and FF.FIELDNAME = 'STRUCTUREID'
                  ), ?)";
					$db->query($sql,array($nodeid, $stuctureid));

					$structures[$stuctureid] = $nodeid;

					for ($j=$workSheet->getCellByColumnAndRow(3, 2)->getValue();
					$j<=$workSheet->getCellByColumnAndRow(4, 2)->getValue(); $j++)
					{
						$cell = (integer)$workSheet->getCellByColumnAndRow($j, $i)->getValue();

						if ($cell >0)
						{
							$sql = "select CT.NODEID
from V_CONTENT_TYPE CT
inner join V_ADD_VALUES VAV
on VAV.NODEID = CT.NODEID
   and VAV.FIELDNAME = '_ADD_CODE'
where CT.T_NODE_KEY = 'T_SPECIALITY'
      and VAV.VAL = ?";
							$specialitycodevalueid =	$db->fetchOne($sql,$workSheet->getCellByColumnAndRow($j, 6)->getValue());


							$sql = "select first 1 SPG.PARAMGROUPID
from STAT_PARAMGROUPS SPG
inner join STAT_PARAMGROUPS SPG2
on (SPG2.PARAMGROUPID = SPG.PARAMGROUPID)
and SPG2.PARAMID = ?
and SPG2.PARAM_VALUE = ?
inner join STAT_PARAMGROUPS SPG3
on (SPG3.PARAMGROUPID = SPG.PARAMGROUPID)
and SPG3.PARAMID =  ?
and SPG3.PARAM_VALUE = ?
inner join STAT_PARAMGROUPS SPG4
on (SPG4.PARAMGROUPID = SPG.PARAMGROUPID)
and SPG4.PARAMID = ?
and SPG4.PARAM_VALUE = ?
inner join STAT_PARAMGROUPS SPG5
on (SPG5.PARAMGROUPID = SPG.PARAMGROUPID)
and SPG5.PARAMID = ?
and SPG5.PARAM_VALUE = ?
where SPG.PARAMID = ?
and SPG.PARAM_VALUE = ?
and (select count(*) from STAT_PARAMGROUPS SPG_COU
where SPG_COU.PARAMGROUPID = SPG.PARAMGROUPID) = 5";

							$paramgroupid = $db->fetchOne($sql,array($edubaseparamid,$edubasevalueid,$countrytypeparamid,$countrytypevalueid,
							$reportkindparamid,$reportkindvalueid, $specialitycodeparamid, $specialitycodevalueid,$eduformparamid,$eduformvalueid));

							if (is_null($paramgroupid)|empty($paramgroupid))
							{
								$paramgroupid = $db->fetchOne ( 'select UID from SP_GEN_UID' );

								$sql = "insert into STAT_PARAMGROUPS (PARAMGROUPID, PARAMID, PARAM_VALUE)
values (?, ?, ?)";
								$db->query($sql,array($paramgroupid,$eduformparamid,$eduformvalueid));
								$db->query($sql,array($paramgroupid,$edubaseparamid,$edubasevalueid));
								$db->query($sql,array($paramgroupid,$countrytypeparamid,$countrytypevalueid));
								$db->query($sql,array($paramgroupid,$reportkindparamid,$reportkindvalueid));
								$db->query($sql,array($paramgroupid,$specialitycodeparamid,$specialitycodevalueid));
							
}
							$sql = "insert into STAT_DATA (DATAREPORTID, PARAMGROUPID, KINDID, DATAVALUE)
values (?, ?, ?, ?)";
							$db->query($sql,array($nodeid,$paramgroupid,$kindid,$cell));

						}
					}
				}
				else
				{
					$nodeid = $structures[$stuctureid];
					for ($j=$workSheet->getCellByColumnAndRow(3, 2)->getValue();
					$j<=$workSheet->getCellByColumnAndRow(4, 2)->getValue(); $j++)
					{
						$cell = (integer)$workSheet->getCellByColumnAndRow($j, $i)->getValue();
							

							
						if ($cell >0)
						{

							$sql = "select CT.NODEID
from V_CONTENT_TYPE CT
inner join V_ADD_VALUES VAV
on VAV.NODEID = CT.NODEID
   and VAV.FIELDNAME = '_ADD_CODE'
where CT.T_NODE_KEY = 'T_SPECIALITY'
      and VAV.VAL = ?";
							$specialitycodevalueid =	$db->fetchOne($sql,$workSheet->getCellByColumnAndRow($j, 6)->getValue());


							$sql = "select first 1 SPG.PARAMGROUPID
from STAT_PARAMGROUPS SPG
inner join STAT_PARAMGROUPS SPG2
on (SPG2.PARAMGROUPID = SPG.PARAMGROUPID)
and SPG2.PARAMID = ?
and SPG2.PARAM_VALUE = ?
inner join STAT_PARAMGROUPS SPG3
on (SPG3.PARAMGROUPID = SPG.PARAMGROUPID)
and SPG3.PARAMID =  ?
and SPG3.PARAM_VALUE = ?
inner join STAT_PARAMGROUPS SPG4
on (SPG4.PARAMGROUPID = SPG.PARAMGROUPID)
and SPG4.PARAMID = ?
and SPG4.PARAM_VALUE = ?
inner join STAT_PARAMGROUPS SPG5
on (SPG5.PARAMGROUPID = SPG.PARAMGROUPID)
and SPG5.PARAMID = ?
and SPG5.PARAM_VALUE = ?
where SPG.PARAMID = ?
and SPG.PARAM_VALUE = ?
and (select count(*) from STAT_PARAMGROUPS SPG_COU
where SPG_COU.PARAMGROUPID = SPG.PARAMGROUPID) = 5";

							$paramgroupid = $db->fetchOne($sql,array($edubaseparamid,$edubasevalueid,$countrytypeparamid,$countrytypevalueid,
							$reportkindparamid,$reportkindvalueid, $specialitycodeparamid, $specialitycodevalueid,$eduformparamid,$eduformvalueid));



							if (!(is_null($paramgroupid)|empty($paramgroupid)))
							{
								$sql = "select ST.DATAVALUE
from STAT_DATA ST
where ST.DATAREPORTID = ?
   and ST.PARAMGROUPID = ?
   and ST.KINDID = ?
                  ";
								$datavalue = $db->fetchOne($sql,array($paramgroupid,$eduformparamid,$eduformvalueid));


								if (!(is_null($datavalue)|empty($datavalue))) $cell = $cell + $datavalue;

							}
							else
							{
								$paramgroupid = $db->fetchOne ( 'select UID from SP_GEN_UID' );

								$sql = "insert into STAT_PARAMGROUPS (PARAMGROUPID, PARAMID, PARAM_VALUE)
values (?, ?, ?)";
								$db->query($sql,array($paramgroupid,$eduformparamid,$eduformvalueid));
								$db->query($sql,array($paramgroupid,$edubaseparamid,$edubasevalueid));
								$db->query($sql,array($paramgroupid,$countrytypeparamid,$countrytypevalueid));
								$db->query($sql,array($paramgroupid,$reportkindparamid,$reportkindvalueid));
								$db->query($sql,array($paramgroupid,$specialitycodeparamid,$specialitycodevalueid));
							}

							$sql = "insert into STAT_DATA (DATAREPORTID, PARAMGROUPID, KINDID, DATAVALUE)
values (?, ?, ?, ?)";
							$db->query($sql,array($nodeid,$paramgroupid,$kindid,$cell));

						}
					}
				}
			}
		}



		return	'Відправлення планів завершено успішно';

	}

}