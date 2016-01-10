<?php

class Mail_Model_Mail {

	private static function getRecipients($docid, $rec_num = -1) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		$sql = "select AV.VAL
from V_ADD_VALUES AV
where AV.NODEID = ?
and AV.FIELDNAME = '_ADD_RECIPIENTS'";
		
		$rec = $db->fetchOne($sql, $docid);
		$rec = new Zend_Config_Ini('data://,' . $rec);
		return $rec->toArray();
	}
	
	
	public static function getMailForm($docid) {
		$form = new Zend_Form();
		$form->setMethod('post')
				->setAttrib('onsubmit', 'return checkReply(this);')
				->setDecorators(array(
					'FormElements',
					array('HtmlTag', array('tag' => 'table', 'class' => 'zend_form')),
					'Form'
				));
		
		$form->addElements(array(
			array('select', 'to', array(
				'label' => 'To',
				'required' => true,
				'multiOptions' => array_merge(array('' => ''), array_keys(self::getRecipients($docid)))
			)),
			array('text', 'replyto', array(
				'label' => 'Your e-mail address',
				'validators' => array(
					array('EmailAddress')
				),
				)),
			array('textarea', 'body', array(
				'label' => 'Message body',
				'required' => true,
				'rows' => '7',
				'cols' => '70',
				'validators' => array(
					array('StringLength', true, array(0, 5000))
				),
				'filters' => array('StringTrim')
			)),
		));

		$form->setElementDecorators(array(
			'ViewHelper',
			'Errors',
			array(array('data'=>'HtmlTag'), array('tag' => 'td')),
			array('Label', array('tag' => 'td')),
			array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
		));		
		
		$form->addElements(array(
			array('captcha', 'captcha', array(
				'label' => 'Enter safety code',
				'required' => true,
				'ignore'  => true,
/*				'captcha' => array( 
					'captcha' => 'Image',  //!!! Проблема. Не всегда отображаются символы на картинке
					'alt' => 'captcha',
					'wordLen' => 5,  
					'timeout' => 600,
					'gcFreq' => 1,
					'font' => BASE_PATH . '/library/Uman/font/Vera.ttf',  
					'imgDir' =>  BASE_PATH . '/public/captcha/',  
					'imgUrl' => '/captcha/',
					'dotNoiseLevel' => 60,  
					'lineNoiseLevel' => 5  
				),*/
				'captcha' => array( 
					'captcha' => 'Figlet',
					'wordLen' => 5,  
					'timeout' => 600
				),
				'decorators' =>  array(
					'Captcha',
					'Errors',
					array(array('data'=>'HtmlTag'), array('tag' => 'td')),
					array('Label', array('tag' => 'td')),
				//array(array('row'=>'HtmlTag'),array('tag'=>'tr'))  !!! Ошибка в Zend http://framework.zend.com/issues/browse/ZF-9168
				)			
			)),
			array('submit', 'submit', array(
				'label' => 'Send',
				'decorators' =>  array(
					'ViewHelper',
					array(array('data'=>'HtmlTag'), array('tag' => 'td')),
					array(array('Label' => 'HtmlTag'), array('tag' => 'td',  'placement' => 'prepend')),
					array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
				)
			))
		));

		return $form;
	}
	
	public static function sendMail($docid, $params) {
//		$cfg = Zend_Registry::get('cfg');
//		$tr = new Zend_Mail_Transport_Smtp($cfg['post']['smtp']);
//		Zend_Mail::setDefaultTransport($tr);
		$mail = new Zend_Mail('UTF-8');
//		$mail->setFrom('site@csmu.strace.net', 'CSMU site');
		$recipients = self::getRecipients($docid);
		$toNames = array_keys($recipients);
		$toEmails = array_values($recipients);
		$toEmails = explode(',', $toEmails[$params['to']]);
		$mail->addTo(array_shift($toEmails), $toNames[$params['to']]);
		if($params['replyto']) $mail->setFrom($params['replyto']);
//		else $mail->setFrom('www@csmu.strace.net', 'Не отвечать!');
//		else $mail->clearReplyTo();
		$mail->addBcc($toEmails);
		$mail->setSubject($toNames[$params['to']]);
		$mail->setBodyText($params['body']);
		$mail->send();
	}
	
}