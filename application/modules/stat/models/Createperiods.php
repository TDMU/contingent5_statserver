<?php

class Stat_Model_Createperiods {
	protected $_acl = null;
	protected $_db = null;
	
	public function __construct() {
		$this->_acl = Zend_Registry::get('acl');
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter ();
	}
	

	public function execute($params = null) {
		$curdate = date ( 'd-m-Y' );
		$curdateArray = explode ( '-', $curdate );
		$requests = $this->getRequest ();
		$real_execute = false;
		foreach ( $requests as $request ) {
			
			$periods = $this->getPeriods ( $request ['NODEID'] );
			$lastpublicdate = $this->getLastPeriods ( $request ['NODEID'] );
			if (empty($lastpublicdate)){
				$lastpublicdate = date('d-m-Y', strtotime('-'.$request ['TIMELIMIT'].' day',strtotime($curdate)));
				$date = explode ( '-', $lastpublicdate );
			}else{
			$date = explode ( '-', $lastpublicdate );
			$i = $date [0];
			$date [0] = $date [2];
			$date [2] = $i;}
			
			foreach ( $periods as $period ) {
				
				$publicdate = $period ['PUBLICDATE'];
				$publicdateArray = explode ( '.', $publicdate );
				
				$pos = stripos ( $publicdateArray [2], 'yyyy' );
				if ($pos === false) {
					$date [2] = $publicdateArray [2];
					$publicyear = $publicdateArray [2];
				} else {
					$publicyear = $curdateArray [2];
				}
				;
				
				for($i = $date [2]; $i <= $publicyear; $i ++) {
					
					$publicdateArray1 [2] = $this->getYear ( $publicdateArray [2], $i );
					
					$pos = stripos ( $publicdateArray [1], 'mm' );
					if ($pos === false) {
						$firstmonth = ( integer ) $this->getMonth ( $publicdateArray [1], $curdateArray [1] );
						$lastmonth = $firstmonth;
					} else {
						$firstmonth = 1;
						$lastmonth = 12;
					
					}
					;
					
					for($j = $firstmonth; $j <= $lastmonth; $j ++) {
						
						$publicdateArray1 [1] = $this->getMonth ( $publicdateArray [1], $j );
						
						$pos = stripos ( $publicdateArray [0], 'dd' );
						if ($pos === false) {
							$firstday = ( integer ) $this->getDay ( $publicdateArray [0], $curdateArray [0], $j, $i );
							$lastday = $firstday;
						} else {
							$firstday = 1;
							$lastday = cal_days_in_month ( CAL_GREGORIAN, $j, $i );
						
						}
						
						for($k = $firstday; $k <= $lastday; $k ++) {
							
							$publicdateArray1 [0] = $this->getDay ( $publicdateArray [0], $k, $j, $i );
							
							$curdate = implode ( '-', $curdateArray );
							$publicdate = implode ( '.', $publicdateArray1 );
							$publicdate1 = implode ( '-', $publicdateArray1 );
							
							if ((strtotime ( $publicdate1 ) <= strtotime($curdate)) and
							(strtotime ( $publicdate1 ) >= strtotime('-'.$request ['TIMELIMIT'].' day',strtotime($curdate))) and 

							(strtotime ( $publicdate1 ) > strtotime ( $lastpublicdate ))) {
								
								$begindate = $this->getDate ( $period ['BEGINDATE'], array ($k, $j, $i ) );
								$enddate = $this->getDate ( $period ['ENDDATE'], array ($k, $j, $i ) );
								
								$nodeid = $this->_db->fetchOne ( 'select UID from SP_GEN_UID' );
								$sql = 'insert into CONTENTTREE (NODEID, PARENTID, NODETYPEID, TITLE, VISIBLE, CREATEUSERID)
values (?,?,(select CT.NODEID
from CONTENTTREE CT
where CT.NODE_KEY = ?), ?, 1, ?)';
								
								$titleperiod = $this->getPeriodTitle ( $begindate, $enddate, $i );
								$this->_db->query ( $sql, array ($nodeid, $period ['REPORTID'], 'T_STAT2_PERIOD', $titleperiod, $this->_acl->userid ) );
								echo "Создан отчет \"{$request ['TITLE']}\" за $titleperiod.\n";
								$this->insertDates ( $nodeid, '_ADD_STARTDATE', $begindate );
								$this->insertDates ( $nodeid, '_ADD_ENDDATE', $enddate );
								$this->insertDates ( $nodeid, '_ADD_PUBLICDATE', $publicdate );
								$this->insertDates ( $nodeid, '_ADD_PUBLICENDDATE', date('Y.m.d', strtotime('+'.$request ['TIMELIMIT'].' day',strtotime($publicdate1))) );
							    $real_execute = true;
							}
						}
					
					}
				
				}
			}
		}
	return $real_execute;	
	}
	

	private function getRequest() {
		$sql = "select ct.NODEID, CT.TITLE, (  select AV1.VAL
         from V_ADD_VALUES AV1
         where ((AV1.NODEID = CT.NODEID) and
         (AV1.FIELDNAME = '_ADD_TIMELIMIT'))
        )  as TIMELIMIT
from V_CONTENT_TYPE CT
where CT.T_NODE_KEY = 'T_STAT2_REQUEST'";
		
		return $this->_db->fetchAll ( $sql );
	}
	
	private function checkExistPeriod($reportid, $begindate, $enddate) {
		$sql = 'select first 1 1 as REPORT_EXIST

from V_CONTENT_TYPE CT

where CT.T_NODE_KEY = ?
 and CT.PARENTID = ?
 and ((  select AV1.VAL
         from V_ADD_VALUES_DATE AV1
         where ((AV1.NODEID = CT.NODEID) and
         (AV1.FIELDNAME = ?))
        ) = ? )
 and ((  select AV1.VAL
         from V_ADD_VALUES_DATE AV1
         where ((AV1.NODEID = CT.NODEID) and
         (AV1.FIELDNAME = ?))
        ) = ? )';
		
		$res = $this->_db->fetchOne ( $sql, array ('T_STAT2_PERIOD', $reportid, '_ADD_STARTDATE', $begindate, '_ADD_ENDDATE', $enddate ) );
		if ($res != 1) {
			return 0;
		} else {
			return 1;
		}
	}
	
	private function getPeriods($requestid) {
		$sql = "select CT.NODEID,
		 (     select AV1.VAL
         from V_ADD_VALUES AV1
         where ((AV1.NODEID = CT.NODEID) and
         (AV1.FIELDNAME = '_ADD_BEGINDATEMASK'))
        ) as BEGINDATE,

    (     select AV1.VAL
         from V_ADD_VALUES AV1
         where ((AV1.NODEID = CT.NODEID) and
         (AV1.FIELDNAME = '_ADD_ENDDATEMASK'))
        ) as ENDDATE,

    (     select AV1.VAL
         from V_ADD_VALUES AV1
         where ((AV1.NODEID = CT.NODEID) and
         (AV1.FIELDNAME = '_ADD_PUBLICDATEMASK'))
        ) as PUBLICDATE,
        
        (     select VCT1.NODEID
         from V_CONTENT_TYPE VCT1
         where ((VCT1.PARENTID = CT2.NODEID) and
         (VCT1.T_NODE_KEY = 'T_STAT2_REPORTS'))
        ) as REPORTID

     
from V_CONTENT_TYPE CT
inner join CONTENTTREE CT1
on CT1.NODEID = CT.PARENTID
inner join CONTENTTREE CT2
on CT2.NODEID = CT1.PARENTID
where CT.T_NODE_KEY = 'T_STAT2_PERIODTEMPLATE'
and CT2.NODEID = ?
and CT.VISIBLE = 1";
		
		return $this->_db->fetchAll ( $sql, array ($requestid ) );
	}
	
	private function getLastPeriods($requestid) {
		$sql = 'select first 1 AV1.VAL as PUBLICDATE
from CONTENTTREE CT
inner join CONTENTTREE CT1
on CT1.NODEID = CT.PARENTID
inner join CONTENTTREE CT2
on CT2.NODEID = CT1.PARENTID
inner join V_ADD_VALUES_DATE AV1
on ((AV1.NODEID = CT.NODEID) and
         (AV1.FIELDNAME = ?))
where CT2.NODEID = ?
order by PUBLICDATE desc';
		
		return $this->_db->fetchOne ( $sql, array ('_ADD_PUBLICDATE', $requestid ) );
	}
	
	private function insertDates($nodeid, $nodetypedate, $date) {
		$sql = 'insert into INFO_ADD_VALUES_DATE (NODEID, FIELDID, VAL)
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
and FF.FIELDNAME = ?), ?)';
		
		$this->_db->query ( $sql, array ($nodeid, 'T_STAT2_PERIOD', $nodetypedate, $date ) );
	}
	
	private function getPeriodTitle($begindate, $enddate, $year) {
		
		if ($begindate == $enddate) {
			$title = $begindate;
		} elseif ((strtotime ( $begindate ) == strtotime ( '01-01-' . $year )) and (strtotime ( $enddate ) == strtotime ( '31-12-' . $year ))) {
			$title = $year;
		} else {
			$title = $begindate . ' - ' . $enddate;
		}
		;
		
		return $title;
	}
	
	private function getDate($Date, $curdate) {
		
		$Date = explode ( '.', $Date );
		$Date [2] = $this->getYear ( $Date [2], $curdate [2] );
		
		$Date [1] = $this->getMonth ( $Date [1], $curdate [1] );
		
		$Date [0] = $this->getDay ( $Date [0], $curdate [0], $Date [1], $Date [2] );
		
		$Date = implode ( '.', $Date );
		return $Date;
	}
	
	private function getYear($year, $curyear) {
		$year = $this->getRealDate ( $year, $curyear );
		$year = str_replace ( 'yyyy', $curyear, strtolower ( $year ) );
		
		return trim ( $year );
	}
	
	private function getMonth($month, $curmonth) {
		$month = $this->getRealDate ( $month, $curmonth );
		if ($curmonth < 10) {
			$curmonth = '0' . $curmonth;
		}
		;
		$month = str_replace ( 'mm', $curmonth, strtolower ( $month ) );
		$month = str_replace ( 'fm', '01', strtolower ( $month ) );
		$month = str_replace ( 'lm', '12', strtolower ( $month ) );
		
		return trim ( $month );
	}
	
	private function getDay($day, $curday, $curmonth, $curyear) {
		$day = $this->getRealDate ( $day, $curday );
		if ($curday < 10) {
			$curday = '0' . $curday;
		}
		;
		$day = str_replace ( 'dd', $curday, strtolower ( $day ) );
		$day = str_replace ( 'fd', '01', strtolower ( $day ) );
		$day = str_replace ( 'ld', cal_days_in_month ( CAL_GREGORIAN, $curmonth, $curyear ), strtolower ( $day ) );
		
		return trim ( $day );
	}
	
	private function getRealDate($date, $curdate) {
		
		$date_p = explode ( '+', $date );
		if (count ( $date_p ) > 1) {
			
			$date = $curdate + $date_p [1];
		}
		;
		$date_m = explode ( '-', $date );
		if (count ( $date_m ) > 1) {
			$date = $curdate - $date_m [1];
		
		}
		;
		
		return $date;
	}

}