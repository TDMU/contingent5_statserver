<?php

class Stat_Model_Import10 {
	protected $_db = null;
	protected $key_guides = array();
	protected $use_add_originid_guides = array();

	public function __construct() {
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter ();
	}

	public function getInfoForm() {
		$form = new Zend_Form ( );
		$form->setAction ( '' )->setMethod ( Zend_Form::METHOD_POST )->setAttrib ( 'enctype', Zend_Form::ENCTYPE_MULTIPART );

		$form->setDecorators ( array (array ('ViewScript', array ('viewScript' => 'import/infoForm10.phtml' ), 'Form' ) ) );

		$e = new Zend_Form_Element_Hidden ( 'step', array ('value' => 2 ) );
		$form->addElement ( $e );

		$e = new Zend_Form_Element_Hidden ( 'filename' );
		$form->addElement ( $e );

		$e = new Zend_Form_Element_Hidden ( 'validreports' );
		$form->addElement ( $e );

		$e = new Zend_Form_Element_Submit ( 'submit', array ('label' => 'Далі' ) );
		$form->addElement ( $e );

		$form->setElementDecorators ( array ('ViewHelper' ) );

		return $form;
	}

	public function checkPackage($zipFileName) {
		$zip = new ZipArchive ( );
		if ($zip->open ( $zipFileName ) !== TRUE)
			throw new Zend_Exception ( 'Error open ' . $zipFileName );

		$res = $zip->locateName ( 'data.xml', ZIPARCHIVE::FL_NOCASE );
		if ($res === FALSE)
			throw new Zend_Exception ( 'File data.xml not exists in this package' );

		$xml = simplexml_load_string ( $zip->getFromName ( 'data.xml' ) );
		$zip->close ();

		if (strcmp ( $xml->info->packagetype, 'Request (for UMAN Statistics)' ) !== 0)
			throw new Zend_Exception ( "packagetype not 'Request (for UMAN Statistics)' " );

		$info = array ();
		$info_reports = array ();

		foreach ( $xml->info->children () as $inf ) {
			$info [$inf->getName ()] = ( string ) $inf;
		}

		$validreports = '';
		foreach ( $xml->reports->children () as $report ) {
			$log = '';
			$isReportValid = 0;
			$ee = $this->checkReportExist ( ( string ) $report->info->reportid, $info ['orgcode'], $log );
			if ($ee != 1) {

				$sql_guides = array ();
				foreach ( $xml->guides->children () as $guide ) {
				    $sql_guides [( string ) $guide ['name']] = ( string ) $guide->sql->uman;
				}

				if ($this->checkReportValid ( $report, $log, $sql_guides )) {
					$log .= 'Даний звіт буде завантажено.';
					$isReportValid = 1;
					$validreports .= ($validreports ? ',' : '') . (string)$report->info->reportid;
				} else {
					$log .= ' Неможливо завантажити звіт.';
				}
			}

			$reportKey = 'rep_' . $report->info->reportid;
			foreach ( $report->info->children () as $key => $value )
				$info_reports [$reportKey] [$key] = $value;

			$info_reports [$reportKey] ['errors'] = $log;
			$info_reports [$reportKey] ['isReportValid'] = $isReportValid;
		}

		$info ['info_reports'] = $info_reports;
		$info ['validreports'] = $validreports;
		return $info;
	}

	private function checkPeriod($d1, $d2, $orgCode) {
		$sql = "select CT.NODEID as REQUESTID
				from  V_CONTENT_TYPE CT
				inner join V_ADD_VALUES AV_P
				on (AV_P.NODEID = CT.NODEID)
				and (AV_P.FIELDNAME = '_ADD_PERIODID')
				inner join V_ADD_VALUES AV_D1
				on (cast(AV_D1.NODEID as varchar(20)) = AV_P.VAL)
				and (AV_D1.FIELDNAME = '_ADD_STARTDATE')
				inner join V_ADD_VALUES AV_D2
				on (cast(AV_D2.NODEID as varchar(20)) = AV_P.VAL)
				and (AV_D2.FIELDNAME = '_ADD_ENDDATE')
				where CT.T_NODE_KEY = 'T_REQUEST'
				and AV_D1.VAL = ?
				and AV_D2.VAL = ?";

		$requestID = $this->_db->fetchOne ( $sql, array ($d1, $d2 ) );
		if (! $requestID)
			throw new Zend_Exception('Період, за який зроблено пакет, не відповідає звітним періодам.');

		$sql = "select CT.NODEID as RESPONSEID
				from  V_CONTENT_TYPE CT
				inner join V_ADD_VALUES AV_EST
				on (AV_EST.NODEID = CT.NODEID)
				and (AV_EST.FIELDNAME = '_ADD_ESTABLISHMENTID')
				inner join INFO_STRUCTURE_ITEM ISI
				on (cast(ISI.NODEID as varchar(500)) = AV_EST.VAL)
				where CT.T_NODE_KEY = 'T_RESPONSE'
				and ISI.CODE = ?
				and CT.PARENTID = ?";

		$rs = $this->_db->fetchRow ( $sql, array ($orgCode, $requestID ) );

		if ($rs)
			throw new Zend_Exception('Пакет за даний період вже завантажено.');

		return $requestID;

	}

	private function checkReportExist($reportid, $orgcode, &$log) {
		$err = 0;
		$sql = "select first 1 1 as REPORT_EXIST
from CONTENTTREE CT
inner join V_ADD_VALUES_REF IAV
on IAV.NODEID = CT.NODEID
and IAV.FIELDNAME = 'STRUCTUREID'
inner join INFO_STRUCTURE_ITEM ISI
on ISI.NODEID = IAV.VAL
where  CT.NODEID = ?  and
ISI.CODE = ?";

		$res = $this->_db->fetchOne ( $sql, array ($reportid, $orgcode ) );
		if ($res != 1) {
			$log .= " Даний звіт (id=$reportid, org=$orgcode) не очікується сервером.";
			$err = 1;
		}

		$sql = "select first 1 1 as REPORT_EXIST
from STAT_DATA ST
where ST.DATAREPORTID = ?";
		$res = $this->_db->fetchOne ( $sql, $reportid );
		if ($res == 1) {
			$log .= ' Даний звіт вже існує.';
			$err = 1;
		}

		return $err;
	}

	protected function checkReportValid($report, &$log, $sql_guides) {
		$i = 0;
		$res = true;

		if (count($report->params->children ()) > 0){
			foreach ( $report->params->common->children () as $common ) {
				if ($common ['nodekey'] !== NULL) {
					$guides [$i] ['nodekey'] = ( string ) $common ['nodekey'];
					$guides [$i] ['name'] = $common->getName ();
					$i ++;
				}
			}

			$i = 0;

			if (!(count($report->data->children ()) == 0 ||
			count($report->data->DATAPACKET->children ()) == 0 ||
			count($report->data->DATAPACKET->ROWDATA->children ()) == 0 )){
				foreach ( $report->data->DATAPACKET->ROWDATA->children () as $rowdata ) {
					foreach ( $guides as $guide ) {
						$guides_value [$guide ['name']] [$i] = ( string ) $rowdata [$guide ['name']];
					}
					$i ++;
				}

				foreach ( $guides as $guide ) {
					$guides_value [$guide ['name']] = array_unique ( $guides_value [$guide ['name']] );
					$sql = @$sql_guides [$guide ['name']];
					if ($sql) {
  					$items = $this->_db->fetchCol ( $sql );
  					$result = array_diff ( $guides_value [$guide ['name']], $items );
  					if (count ( $result ) > 0) {
  						$log .= ' Значення ';
  						foreach ( $result as $res ) {
  							$log .= $res . ' ';
  						}
  						$log .= 'не знайдені в довіднику ' . $guide ['name'] . '.';
  						$res = false;
  					}
					}
				}

				$guides = array ();
				$i = 0;
				foreach ( $report->params->composite->children () as $composite ) {
					if ($composite ['nodekey'] !== NULL) {
						$guides [$i] ['nodekey'] = ( string ) $composite ['nodekey'];
						$guides [$i] ['name'] = $composite->getName ();
						$i ++;
					}
				}

				$i = 0;
				foreach ( $report->params->compositeparamvalue->children () as $compositeparamvalue ) {
					foreach ( $guides as $guide ) {
						$guides_value [$guide ['name']] [$i] = ( string ) $compositeparamvalue [$guide ['name']];
					}
					$i ++;
				}

				foreach ( $guides as $guide ) {
					$guides_value [$guide ['name']] = array_unique ( $guides_value [$guide ['name']] );
					$items = $this->_db->fetchCol ( $sql_guides [$guide ['name']] );
					$result = array_diff ( $guides_value [$guide ['name']], $items );
					if (count ( $result ) > 0) {
						$log .= ' Значення ';
						foreach ( $result as $res ) {
							$log .= $res . ' ';
						}
						$log .= 'не знайдені в довіднику ' . $guide ['name'] . '.';
						$res = false;
					}
				}
			}
			else {
				$log .= 'Звіт пустий.';
				$res = false;
			}
		}
		else {
			$log .= 'Звіт пустий.';
			$res = false;
		}
		return $res;
	}

	//**************************************************************************************

	protected function prepareParams($report, &$commonparams, &$compositeparams) {
		foreach ($report->params->common->children() as $common) {
			if ($common['nodekey'] !== NULL) {
				$param['param_name'] = $common->getName ();
				$param['nodekey'] = (string)$common['nodekey'];
				$param['paramid'] = $this->getNodeKeyId((string)$common['nodekey']);
				$param['paramvalue'] = '';
				$commonparams[] = $param;
			}
		}

		foreach ($report->params->composite->children () as $composite) {
			if ($composite['nodekey'] !== NULL) {
				$param['param_name'] = $composite->getName();
				$param['nodekey'] = (string)$composite['nodekey'];
				$param['paramid'] = $this->getNodeKeyId((string)$composite['nodekey']);
				$param['paramvalue'] = '';
				$compositeparams[] = $param;
			}
		}
	}

	protected function getCommonParamsSQL(&$commonparams, $paramscount, $rowdata, &$sql, &$sql_last) {
		$sql = 'select SPG.PARAMGROUPID from STAT_PARAMGROUPS SPG ';
		$i = 0;
		foreach($commonparams as &$commonparam) {
		  if (array_key_exists($commonparam['param_name'], $this->key_guides) 
		  	&& (array_key_exists($commonparam['param_name'], $this->use_add_originid_guides))
		  )
		    $paramvalue = $this->getParamValue($this->key_guides[$commonparam['param_name']], (string)$rowdata[$commonparam['param_name']]);
		  else
		    $paramvalue = (string)$rowdata[$commonparam['param_name']];

		  $commonparam['paramvalue'] = str_replace("'", "''", $paramvalue);
		  if ($i == 0) {
		    $sql_last = " where SPG.PARAMID = {$commonparam['paramid']}
and SPG.PARAM_VALUE = '{$commonparam['paramvalue']}'
and (select count(*) from STAT_PARAMGROUPS SPG_COU
where SPG_COU.PARAMGROUPID = SPG.PARAMGROUPID) = $paramscount";
		  } else {
		    $sql .= " inner join STAT_PARAMGROUPS SPG$i
on (SPG$i.PARAMGROUPID = SPG.PARAMGROUPID)
and SPG$i.PARAMID = {$commonparam ['paramid']}
and SPG$i.PARAM_VALUE = '{$commonparam['paramvalue']}'";
		  }
		  $i ++;
		}
	}

	protected function getCompositeParamsSQL(&$compositeparams, $compositeparamvalue) {
	  $sql = '';
	  $i = 1000;

	  foreach ($compositeparams as &$compositeparam) {
			$compositeparam['paramvalue'] = $this->getParamValue($this->key_guides[$compositeparam['param_name']], (string)$compositeparamvalue[$compositeparam['param_name']]);

			$sql .= " inner join STAT_PARAMGROUPS SPG$i
on SPG$i.PARAMGROUPID = SPG.PARAMGROUPID
and SPG$i.PARAMID = {$compositeparam['paramid']}
and SPG$i.PARAM_VALUE = '{$compositeparam['paramvalue']}'";
			$i ++;
		}

		return $sql;
	}

	protected function getNewParamGroupID($commonparams, $compositeparams) {
		$paramgroupid = $this->_db->fetchOne('select UID from SP_GEN_UID');
		$sql = 'insert into STAT_PARAMGROUPS (PARAMGROUPID, PARAMID, PARAM_VALUE) values (?, ?, ?)';

		foreach(array_merge($commonparams, $compositeparams) as $param)
			$this->_db->query($sql, array($paramgroupid, $param['paramid'], $param['paramvalue']));

		return $paramgroupid;
	}

	protected function setImported($reportid) {
		$sql = "insert into INFO_ADD_VALUES_DATE (NODEID, FIELDID, VAL)
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
and FF.FIELDNAME = '_ADD_DISPATCHDATE'), current_date)";

		$this->_db->query ( $sql, $reportid );
	}

	protected function processReport($report, $key_guides, $use_add_originid_guides) {
		$reportid = (string) $report->info->reportid;
		$commonparams = array();
		$compositeparams = array();

		$this->prepareParams($report, $commonparams, $compositeparams);

		$paramscount = count($commonparams) + count($compositeparams);

		foreach ($report->data->DATAPACKET->ROWDATA->children() as $rowdata) {
      $this->getCommonParamsSQL($commonparams, $paramscount, $rowdata, $sql, $sql_last);
		  $sql_tmp = $sql;

			foreach ($report->params->compositeparamvalue->children() as $compositeparamvalue) {
				if ((int) $rowdata[$compositeparamvalue->getName()] !== 0) {
					$sql = $sql_tmp .	$this->getCompositeParamsSQL($compositeparams, $compositeparamvalue) . $sql_last;

					$paramgroupid = $this->_db->fetchOne($sql);

					if(!$paramgroupid)
					  $paramgroupid = $this->getNewParamGroupID($commonparams, $compositeparams);

					$kindid = $this->getKindValue((string)$compositeparamvalue['kind']);

					$sql = 'select DATAVALUE from STAT_DATA where DATAREPORTID = ? and PARAMGROUPID = ? and KINDID = ?';
					$existsVal = $this->_db->fetchOne($sql, array($reportid, $paramgroupid, $kindid));
					if(!$existsVal) $existsVal = 0;

					$sql = 'update or insert into STAT_DATA (DATAREPORTID, PARAMGROUPID, KINDID, DATAVALUE) values (?, ?, ?, ?)';
					$this->_db->query ( $sql, array ($reportid, $paramgroupid, $kindid, $existsVal + (string)$rowdata[$compositeparamvalue->getName()]));
				}
			}
		}

		$this->setImported($reportid);
	}

	private function getNodeKeyId($nodekey) {
		$sql = "select C.NODEID
from CONTENTTREE C
where C.NODE_KEY = ?";

		return $this->_db->fetchOne ( $sql, $nodekey );
	}

	protected function getParamValue($nodekey, $origin_value) {
		$sql = "select CT.NODEID
  from V_CONTENT_TYPE CT
  inner join V_ADD_VALUES AV
  on (AV.NODEID = CT.NODEID)
  and (AV.FIELDNAME = '_ADD_ORIGINID')
  inner join V_CONTENT_TYPE C
  on c.NODEID = ct.PARENTID
  where C.T_NODE_KEY = ?
  and (AV.VAL = ?)";

		return $this->_db->fetchOne ( $sql, array ($nodekey, $origin_value ) );
	}

	private function getGuidesValue($nodekey, $origin_value) {
		$sql = "select CT.NODEID
  from V_CONTENT_TYPE CT
  inner join V_ADD_VALUES AV
  on (AV.NODEID = CT.NODEID)
  and (AV.FIELDNAME = '_ADD_ORIGINID')
  inner join V_CONTENT_TYPE C
  on C.NODEID = CT.PARENTID
  where C.T_NODE_KEY = ?
  and (AV.VAL = ?)";

		return $this->_db->fetchOne($sql, array($nodekey, $origin_value));
	}

	protected function getKindValue($nodekey) {
		$sql = "select CT.NODEID
  from V_CONTENT_TYPE CT
  where CT.NODE_KEY = ?";

		return $this->_db->fetchOne($sql, $nodekey);
	}

	public function doImport($zipFileName, $ValidReports) {
		set_time_limit(36000);

	  $zip = new ZipArchive();
		$zip->open($zipFileName);
		$xml = simplexml_load_string($zip->getFromName('data.xml'));
		//		 $xml =  new SimpleXMLElement($zip->getFromName ( 'data.xml' ));
		$zip->close();
//		$key_guides = array();
		$info = $xml->info;
		foreach ($xml->guides->children() as $guide) {
			$this->key_guides[(string)$guide['name']] = (string)$guide['key'];
			if (!isset($guide['donot_use_add_originid'])) 
				$this->use_add_originid_guides[(string)$guide['name']] = '1';
		}

		$this->_db->beginTransaction();

		foreach ($xml->reports->children() as $report)
			if (substr_count($ValidReports, (string) $report->info->reportid) >= 1) {
				try {
					$this->processReport($report, $this->key_guides, $this->use_add_originid_guides);
				} catch (Exception $e) {
		      $this->_db->rollback();
				  return $e->getMessage();
				}
			}

		$this->_db->commit();
		return null;
	}
}