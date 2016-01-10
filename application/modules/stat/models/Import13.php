<?php

class Stat_Model_Import13 extends Stat_Model_Import10 {

	private function getCompositeParamsSQLForValidation(&$compositeparams, $rowdata) {
		$sql = '';
		$i = 1000;
		foreach($compositeparams as &$compositeparam) {
		  $compositeparam['paramvalue'] = $this->getParamValue($this->key_guides[$compositeparam['param_name']], (string)$rowdata[$compositeparam['param_name']]);
      $sql .= " inner join STAT_PARAMGROUPS SPG$i
on (SPG$i.PARAMGROUPID = SPG.PARAMGROUPID)
and SPG$i.PARAMID = {$compositeparam['paramid']}
and SPG$i.PARAM_VALUE = '{$compositeparam['paramvalue']}'";
		  $i ++;
		}
		return $sql;
	}

	protected function processReport($report, $key_guides, $use_add_originid_guides) {
		$reportid = (string)$report->info->reportid;

		if ($report->addinfo) {
		  $sql = "update or insert into V_ADD_VALUES (NODEID, FIELDNAME, VAL)
 values (?, 'STAT2_' || upper(?), ?) matching(NODEID, FIELDNAME)";

		  foreach ($report->addinfo->children() as $info) {
		    $name = $info->getName();
		    if ($name == 'label') continue;
        $this->_db->query($sql, array($reportid, $name, $info));
		  }
		}


		$commonparams = array();
		$compositeparams = array();

		$this->prepareParams($report, $commonparams, $compositeparams);

		$paramscount = count($commonparams) + count($compositeparams);

		foreach ($report->data->DATAPACKET->ROWDATA->children() as $rowdata) {
      $this->getCommonParamsSQL($commonparams, $paramscount, $rowdata, $sql, $sql_last);
			$sql_tmp = $sql;

			foreach ($report->params->compositeparamvalue->children () as $compositeparamvalue) {
				if ((int) $rowdata[$compositeparamvalue->getName()] !== 0) {
					$sql = $sql_tmp .	$this->getCompositeParamsSQL($compositeparams, $compositeparamvalue) . $sql_last;
					$paramgroupid = $this->_db->fetchOne($sql);

					if(!$paramgroupid)
					  $paramgroupid = $this->getNewParamGroupID($commonparams, $compositeparams);

					if (!$compositeparamvalue['kind'])
					  throw new Zend_Exception('Attribute "kind" not found in statistics data.');

					$kind = $this->getKindValue((string)$compositeparamvalue['kind']);

					$sql = 'select DATAVALUE from STAT_DATA where DATAREPORTID = ? and PARAMGROUPID = ? and KINDID = ?';
					$existsVal = $this->_db->fetchOne($sql, array($reportid, $paramgroupid, $kind));
					if(!$existsVal) $existsVal = 0;

					$sql = 'update or insert into STAT_DATA (DATAREPORTID, PARAMGROUPID, KINDID, DATAVALUE) values (?, ?, ?, ?)';
					$this->_db->query($sql, array($reportid, $paramgroupid, $kind, $existsVal + (string)$rowdata[$compositeparamvalue->getName()]));
				}
			}
		}

		if (@$report->validation->solutions) {
		  foreach ($report->validation->solutions->children() as $solution) {
		    $sql = 'select NODEID from CONTENTTREE where NODE_KEY = ?';
		    $validation_key = 'VALIDATION_' . strtoupper($solution->getName());
		    $validationid = $this->_db->fetchOne($sql,  $validation_key);
				if (!$validationid)
				  throw new Zend_Exception("Validation with node key \"$validation_key\" not found in uman guides.");

		    foreach ($solution->children() as $rowdata) {
		      $sql = '';
		      $this->getCommonParamsSQL($commonparams, $paramscount, $rowdata, $sql, $sql_last);

		      $sql .= $this->getCompositeParamsSQLForValidation($compositeparams, $rowdata) . $sql_last;
		      $paramgroupid = $this->_db->fetchOne($sql);

//		    if(!$paramgroupid)
//		      file_put_contents('d:\\temp\\sql_import.txt', $sql);

		      if(!$paramgroupid)
		        $paramgroupid = $this->getNewParamGroupID($commonparams, $compositeparams);

		      $kindid = $this->getKindValue((string)$rowdata['kind']);

		      $sql = 'insert into STAT_VALIDATION (DATAREPORTID, PARAMGROUPID, KINDID, VALIDATIONID, ARG1, ARG2, NOTE) values (?, ?, ?, ?, ?, ?, ?)';
		      $this->_db->query($sql, array($reportid, $paramgroupid, $kindid, $validationid, $rowdata['arg1'], $rowdata['arg2'], (string)$rowdata));
		    }
		  }
		}

		$this->setImported($reportid);
	}

	protected function checkReportValid($report, &$log, $sql_guides) {
	  if (!(int)$report->info->validresult) {
	    $res = false;
	    $log .= ' Звіт не пройшов валідацію. Перед відправленням звіту на сервер треба виконати валідацію (Статистика2 меню Звіт|Валідація, детальніше дивись допомогу).';
	  } else {
	    $res = parent::checkReportValid($report, $log, $sql_guides);
	  }
	  return $res;
	}
}