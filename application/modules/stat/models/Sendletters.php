<?php

class Stat_Model_Sendletters {
	protected $_db = null;
	protected $_cfg = null;
	
	public function __construct() {
		$this->_cfg = Zend_Registry::get ( 'cfg' );
		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter ();
	}
	
	public function execute($params) {
		$letterkeys = $params->letter_keys;
		$real_execute = false;
		if ($letterkeys !== null) {
//			$tr = new Zend_Mail_Transport_Smtp ( $this->_cfg ['post'] ['smtp'] );
//			Zend_Mail::setDefaultTransport ( $tr );
			$lettertemplates = $this->getLetterTemplates ( $letterkeys );
			foreach ( $lettertemplates as $lettertemplate ) {
				if (! ($xmlfile = file_get_contents ( DOCS_PATH . $lettertemplate ['NODEID'] . '.xml' ))) {
					exit ();
				} else {
					$xml = new SimpleXMLElement ( $xmlfile );
				}
				$sql = ( string ) $xml->sql;
				$subject = ( string ) $xml->subject;
				$from_email = ( string ) $xml->from_email;
				$from_name = ( string ) $xml->from_name;
				$userlist = $this->getUserlist ( $lettertemplate ['NODEID'] );
				$sql = str_replace ( ':userlist', $userlist, $sql );
				$letters = $this->_db->fetchAll ( $sql, array (date ( 'd.m.Y' ) ) );
				if (! empty ( $letters ))
					echo "Письмо: \"$subject\" отправлено следующим адресатам:\n";
				foreach ( $letters as $letter ) {
					$reports = explode ( ',', $letter ['MESSAGE'] );
					$text = ( string ) $xml->text;
					$text = '';
					foreach ( $reports as $report ) {
						$text .= ($text ? "\n" : '') . $report;
					}
					$text = str_replace ( 'message', $text, $xml->text );
					$emails = $this->getEmails ( $letter ['USERID'] );
					if ($emails !== false) {
						$emails = explode ( ',', $emails );
						foreach ( $emails as $email ) {
							
							$mail = new Zend_Mail ( 'UTF-8' );
							$mail->setBodyText ( $text );
							$mail->setFrom ( $from_email, $from_name );
							$mail->addTo ( $email, '' );
							$mail->setSubject ( $subject );
							$mail->send ();
							echo $email . "\n";
							$real_execute = true;
						}
					}
				}
			}
		}
		return $real_execute;
	}
	
	private function getUserlist($lettertemplateid) {
		$sql = 'select AV.VAL
from V_ADD_VALUES AV
where AV.NODEID =  ?
     and AV.FIELDNAME = ?';
		
		return $this->_db->fetchOne ( $sql, array ($lettertemplateid, '_USER_TO_SEND' ) );
	}
	
	private function getEmails($userid) {
		$sql = 'select AV1.VAL
         from V_ADD_VALUES AV1
         where AV1.NODEID = ?
         and AV1.FIELDNAME = ?';
		
		return $this->_db->fetchOne ( $sql, array ($userid, '_ADD_EMAIL' ) );
	}
	
	private function getLetterTemplates($letterkeys) {
		$letterkeys = explode ( ',', $letterkeys );
		$letterkeys = "'" . implode ( "','", $letterkeys ) . "'";
		$sql = "select CT.NODEID
from V_CONTENT_TYPE CT
where CT.T_NODE_KEY = ?
  and CT.NODE_KEY in ({$letterkeys})
  and CT.VISIBLE = 1";
		
		return $this->_db->fetchAll ( $sql, 'T_LETTER_TEMPLATE' );
	}

}