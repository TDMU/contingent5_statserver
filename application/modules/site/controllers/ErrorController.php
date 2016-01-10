<?php

/**
 * ErrorController - The default error controller class
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class ErrorController extends Zend_Controller_Action
{

    /**
     * This action handles  
     *    - Application errors
     *    - Errors in the controller chain arising from missing 
     *      controller classes and/or action methods
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found                
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'HTTP/1.1 404 Not Found';
                break;
            default:
                // application error; display error page, but don't change                
                // status code
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application Error';
                break;
        }
        
/*        
$body = 'HTTP/1.1 404 Not Found' . "<br>";
$body .= $errors->exception . "<br>";
$body .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "<br>";
$body .= "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "<br>";
$body .= "Lang: " . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . "<br>";
$body .= "Referer Link: " . $_SERVER['HTTP_REFERER'] . "<br>";
$body .= "Requested URL" . $_SERVER['REQUEST_URI'] . "<br>";

$mail = new Zend_Mail();
$mail->setBodyHtml($body);
$mail->setFrom('support@somesite.com', 'Website Support');
$mail->addTo("ahmed.abdelaliem@mysite.com.com", "Ahmed Abdel-ALiem");
$mail->setSubject('Sitename Error Occurred');
$mail->send();*/
        
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
        
    }
}
