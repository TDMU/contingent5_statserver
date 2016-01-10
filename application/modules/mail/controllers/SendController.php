<?php

class Mail_SendController extends Zend_Controller_Action {

	public function showAction() {
		$form = Mail_Model_Mail::getMailForm($this->view->docid);
		
		if($this->getRequest()->isPost()
				&& $form->isValid($this->getRequest()->getParams())) {
				try {
					Mail_Model_Mail::sendMail($this->view->docid, $this->getRequest()->getParams());
					$this->view->message = 'Sent successfully';
				} catch(Zend_Exception $e) {
					$this->view->message = 'Error: ' . $e->getMessage();
				}	
		} else {
			$this->view->form = $form;
			$this->view->content = Uman_Utils::getXhtml($this->view->docid);
		}
	}

}