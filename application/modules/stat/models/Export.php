<?php

require_once 'Zend/Db/Table/Abstract.php';

class Stat_Model_Export {
	protected $_acl = null;
	protected $_db = null;

	public function __construct() {
		$this->_acl = Zend_Registry::get ( 'acl' );
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter ();
	}

	public function checkReport($periodid, $establishmentid, $title) {
		$this->_db->beginTransaction ();

		try {

			$sql = "select RDB\$SET_CONTEXT('USER_TRANSACTION', 'CURRENT_USERID', ?) from RDB\$DATABASE";
			$this->_db->fetchCol($sql, $this->_acl->userid);

			$sql = "select CT.NODEID
  from CONTENTTREE CT
    inner join INFO_ADD_VALUES_REF VAN
      on (VAN.NODEID = CT.NODEID)
  where CT.PARENTID = ?
  and
    VAN.FIELDID = (select FF.NODEID
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
                  )
     and VAN.VAL = ?";

			$report = $this->_db->fetchRow ( $sql, array ($periodid, $establishmentid ) );
			$reportid = $report ['NODEID'];
			if ($reportid === NULL) {
				$reportid = $this->_db->nextSequenceId('GEN_UID');
				$sql = 'insert into CONTENTTREE (NODEID, PARENTID, NODETYPEID, TITLE)
    values(?,
           ?,
           (select CT.NODEID
            from CONTENTTREE CT
            where CT.NODE_KEY = ?),
           ?)';

				$this->_db->query ( $sql, array ($reportid, $periodid, 'T_STAT2_REPORT', $title ) );

				$sql = 'insert into INFO_ADD_VALUES_REF (NODEID, FIELDID, VAL)
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
where CT_T.NODE_KEY = ?
  and FF.FIELDNAME = ?
                  ), ?)';

				$this->_db->query ( $sql, array ($reportid, 'T_STAT2_REPORT', 'STRUCTUREID', $establishmentid ) );

			} else {

				$sql = 'update CONTENTTREE
    set MODIFYDATE = current_date
    where (NODEID = ?)';

				$this->_db->query ( $sql, array ($reportid ) );

			}
			;
			$this->_db->commit ();

		} catch ( Zend_Exception $e ) {
			$this->_db->rollback ();
			throw $e;
		}
		return $reportid;
	}


	public function getInfoStructure($userid) {
		$sql = 'select CT.TITLE, ISI.CODE, CT.NODEID
from INFO_USERS IU
inner join CONTENTTREE CT
on CT.NODEID = IU.STRUCTUREID
inner join INFO_STRUCTURE_ITEM ISI
on ISI.NODEID = CT.NODEID
where IU.NODEID = ?';

		$res = $this->_db->fetchRow ( $sql, $userid );

		return $res;
	}


	public function getStatRequest($periodid) {
		$sql = 'select CT_R.NODEID, CT_R.TITLE, VAV.VAL
from CONTENTTREE CT_R
  inner join CONTENTTREE CT_REPORTS
    on(CT_REPORTS.PARENTID = CT_R.NODEID)
  inner join CONTENTTREE CT_PERIOD
    on (CT_PERIOD.PARENTID = CT_REPORTS.NODEID)
  inner join V_ADD_VALUES VAV
    on (VAV.NODEID = CT_R.NODEID) and (VAV.FIELDNAME = ?)
where CT_PERIOD.NODEID = ?';

		$res = $this->_db->fetchRow ( $sql, array ('_ADD_USEGUIDES', $periodid ) );

		return $res;
	}


	public function getResponsesReports($year = 0, $establishmentid = 0, $periodid = 0) {
		$cond = "";
		$cond .= $year != 0 ? " and extract(year from AVD.VAL) = {$year}" : "";
		$cond .= $periodid != 0 ? " and CT1.NODEID = {$periodid}" : "";

		$sql = "select CT1.TITLE as PERIOD, CT1.NODEID as PERIODID,
       CT.TITLE as REPORT,
       AVD.VAL as ENDDATE,
       (
         select AVD1.VAL
         from V_ADD_VALUES_DATE AVD1
         where ((AVD1.NODEID = CT1.NODEID) and
         (AVD1.FIELDNAME = '_ADD_STARTDATE'))
        ) as STARTDATE,

        (
         select AVD1.VAL
         from V_ADD_VALUES_DATE AVD1
         where ((AVD1.NODEID = CT1.NODEID) and
         (AVD1.FIELDNAME = '_ADD_PUBLICDATE'))
        ) as PUBLICDATE,

        (
         select AVD1.VAL
         from V_ADD_VALUES AVD1
         where ((AVD1.NODEID = CT.NODEID) and
         (AVD1.FIELDNAME = '_ADD_TIMELIMIT'))
        ) as TIMELIMIT,
        (
        select CT.NODEID
from CONTENTTREE CT
inner join V_ADD_VALUES_REF IAV
on IAV.NODEID = CT.NODEID
   and IAV.FIELDNAME = 'STRUCTUREID'

where CT.PARENTID = CT1.NODEID
      and IAV.VAL = ?
      )as REPORTID,
      (
        select CT.CREATEDATE
from CONTENTTREE CT
inner join V_ADD_VALUES_REF IAV
on IAV.NODEID = CT.NODEID
   and IAV.FIELDNAME = 'STRUCTUREID'

where CT.PARENTID = CT1.NODEID
      and IAV.VAL = ?
      )as CREATEDATE,
      (
        select IAV.VAL
from CONTENTTREE CT
inner join V_ADD_VALUES_DATE IAV
on IAV.NODEID = CT.NODEID
   and IAV.FIELDNAME = '_ADD_DISPATCHDATE'
inner join V_ADD_VALUES_REF IAV1
on IAV1.NODEID = CT.NODEID
   and IAV1.FIELDNAME = 'STRUCTUREID'

where CT.PARENTID = CT1.NODEID
      and IAV1.VAL = ?
      )as UPLOADDATE

from V_CONTENT_TYPE CT  -- отчеті
inner join ALLOW_NODES AN
  on (AN.NODEID = CT.NODEID)
inner join V_CONTENT_TYPE CT1 -- периоді
  on ((CT1.PATH starting with CT.FULL_PATH) and
      (CT1.T_NODE_KEY = 'T_STAT2_PERIOD'))
inner join V_ADD_VALUES_DATE AVD
on ((AVD.NODEID = CT1.NODEID) and
      (AVD.FIELDNAME = '_ADD_ENDDATE'))
where CT.T_NODE_KEY = 'T_STAT2_REQUEST' and
  AN.ALLOWNODEID = ? {$cond} order by AVD.VAL";

		//	file_put_contents('d:\temp\1.sql', $sql);
		$reports = $this->_db->fetchAll($sql, array ($establishmentid, $establishmentid, $establishmentid, $establishmentid));
		return $reports;
	}


	public function export($params) {
		$auth = Zend_Auth::getInstance ();
		$ident = $auth->getIdentity ();

		$sql = "select CT.NODEID
from V_CONTENT_TYPE CT
where CT.T_NODE_KEY = 'T_STAT2_DATA'
and exists (
  select *
  from CONTENTTREE CT_PERIOD
  where CT_PERIOD.PATH starting with CT.FULL_PATH
  and CT_PERIOD.NODEID = ?
)";

		$periodids = explode(',', $params['id']);
		if (count($periodids) == 0) {
			echo "Request count = 0";
			exit;
		}
		$reportsnodeid = $this->_db->fetchOne($sql, $periodids[0]);

		$dt = date('Ymd');
		$zip = new Uman_ZipStream("pack_{$dt}.pkg");

		if (!($xmlfile = file_get_contents(DOCS_PATH . $reportsnodeid . '.xml'))) {
			echo "Data_info error. " . DOCS_PATH . $reportsnodeid . ".xml not found";
			exit;
		}

		$xml = new SimpleXMLElement($xmlfile);
		$structure = $this->getInfoStructure($ident->NODEID);
		$xml->info->orgcode = $structure['CODE'];
		$xml->info->orgname = $structure['TITLE'];

		$dom_xml = dom_import_simplexml($xml);
		$dom = new DOMDocument('1.0', 'utf-8');
		$dom_xml = $dom->importNode($dom_xml, true);
		$dom_xml = $dom->appendChild($dom_xml);

		$guides = array();
		foreach($periodids as $periodid) {
			$request = $this->getStatRequest($periodid);
			foreach (explode(',', $request['VAL']) as $guide)
			  if(array_search($guide, $guides) === FALSE) $guides[] = $guide;

			if (!($xmlfile = file_get_contents(DOCS_PATH . $request['NODEID'] . '.xml'))) {
				echo "Report error. " . DOCS_PATH . $periodid . ".xml not found";
				exit;
			} else {
				$xml = new SimpleXMLElement($xmlfile);
			}

			$reportinfo = $this->getResponsesReports (0, $ident->STRUCTURE_CODE == 0 ? 0 : $ident->STRUCTUREID, $periodid);
			$reportid = $this->checkReport($periodid, $ident->STRUCTURE_CODE == 0 ? 0 : $ident->STRUCTUREID, $structure['TITLE']);

			$xml->info->reportid = $reportid;
			$xml->info->title = (string) $reportinfo[0]['REPORT'];
			$xml->info->begindate = $this->dateFormat($reportinfo[0]['STARTDATE']);
			$xml->info->enddate = $this->dateFormat($reportinfo[0]['ENDDATE']);
			$xml->info->publicdate = $this->dateFormat($reportinfo[0]['PUBLICDATE']);
			$xml->info->timelimit = $reportinfo[0]['TIMELIMIT'];

			$fieldsdata = @$xml->validation->conditions->fieldsdata;
			if (@$fieldsdata->rules) {
				foreach ($fieldsdata->rules->children() as $rule) {
			    $sql = (string)$rule;

          $reportvalues = $this->_db->fetchAll($sql, array($structure['NODEID'], $this->dateFormat($reportinfo[0]['STARTDATE']), $this->dateFormat($reportinfo[0]['ENDDATE'])));

			    $sql = "select CTP.NODE_KEY, VAV.VAL
from STAT_PARAMGROUPS SP
inner join CONTENTTREE CTP
on CTP.NODEID = SP.PARAMID
inner join CONTENTTREE CTV
on CTV.NODEID = SP.PARAM_VALUE
inner join V_ADD_VALUES VAV
on VAV.NODEID = CTV.NODEID
and VAV.FIELDNAME = '_ADD_ORIGINID'
where SP.PARAMGROUPID = ?";

			    $fields = $fieldsdata->addChild('fields');
			    $fields->addAttribute('title', $rule['title']);

          foreach ($reportvalues as $reportvalue) {
			      $params = $this->_db->fetchAll($sql, $reportvalue['PARAMGROUPID']);

			      $field = $fields->addChild('field', $reportvalue['DATAVALUE']);
			      $field->addAttribute('fieldname', (string)$rule['fieldname']);
			      foreach ($params as $param)
			        $field->addAttribute($param['NODE_KEY'], $param['VAL']);
			    }
			  }
			  unset($fieldsdata->rules);
			}

			$dom_xml = dom_import_simplexml($xml);
			$element = $dom->getElementsByTagName('reports')->item(0);
			$dom_xml = $dom->importNode($dom_xml, true);
			$dom_xml = $element->appendChild($dom_xml);
		}

		$sql = 'select CT.NODEID
from CONTENTTREE CT
where CT.NODE_KEY = ?';

		foreach ($guides as $guide) {
			$guideid = $this->_db->fetchOne($sql, $guide);
			if (!($xmlfile = file_get_contents(DOCS_PATH . $guideid . '.xml'))) {
				echo "Guide $guide error. " . DOCS_PATH . $guideid . ".xml not found";
				exit;
			} else {
				$xml = new SimpleXMLElement($xmlfile);
			}

		  if ($this->string_starts_with($guide, "CONT_GUIDE_")) {
        $guideItems = $this->getContingentGuide((string)$xml->sql->uman);
		  } else {
  			$guideItems = $this->_db->fetchAll((string)$xml->sql->uman);
		  }

			foreach ($guideItems as $guideItem) {
			  $item = $xml->items->addChild('item');
				foreach ($xml->columns->children() as $column)
					$item->addAttribute($column['attr'], $guideItem[strtoupper($column['attr'])]);
			}

			$dom_xml = dom_import_simplexml($xml);
			$element = $dom->getElementsByTagName('guides')->item(0);
			$dom_xml = $dom->importNode($dom_xml, true);
			$dom_xml = $element->appendChild($dom_xml);
		}

		$zip->add_file('data.xml', $dom->saveXML());
		$zip->finish();
	}

	private function dateFormat($date) {
		if ($date !== null) {
			$date = explode('-', $date);
			return $date[2] . '.' . $date[1] . '.' .$date[0];
		};
	}


	private function string_starts_with($string, $search) {
    return (strncmp($string, $search, strlen($search)) == 0);
  }


	private function getContingentGuide($sql) {
	  $frontController = Zend_Controller_Front::getInstance();
    $bootstrap = $frontController->getParam('bootstrap');
	  $resource = $bootstrap->getPluginResource('multidb');
    $db = $resource->getDb('cont5db');

	  return $db->fetchAll($sql);
	}
}