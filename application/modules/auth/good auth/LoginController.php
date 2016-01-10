<?php
/**
 * PagesController for default module
 *
 * @category Application
 * @package Default
 */
class LoginController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->login = false;

        // проверка не залогинен ли пользователь
        if (!Zend_Auth::getInstance()->getIdentity()) {

            // берем форму логина и закидываем во view
            $form = $this->_getLoginForm();
            $this->view->form = $form;

            // если форма была отправлена, мы должны её проверить
            if ($this->_request->isPost()) {
                $formData = $this->_request->getPost();

                // проверяем форму
                if ($form->isValid($formData)) {

                    // проверяем входные данные
                    $result = $this->_authenticate($form->getValue('realm'), $form->getValue('username'), $form->getValue('password'));

                    if ($result->isValid()) {
                        // запомним пользователя на 2 недели
                        if ($form->getValue('rememberMe')) {
                            Zend_Session::rememberMe(60*60*24*14);
                        }
                        // отправляем на главную
                        $this->_redirect('/');
                    } else {
                        // failure: выводим сообщение о ошибке
                        $this->view->error = 'Authorization error. Please check login or/and password';
                    }
                } else {
                    $form->populate($formData);
                }
            }
        } else {
            $this->view->login = true;
            $this->view->username = Zend_Auth::getInstance()->getIdentity();
        }
    }

    // генерация формы логина
    private function _getLoginForm()
    {
        $form = new Zend_Form();
        $form->setMethod('POST');
        $form->setName('userLoginForm');

        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('User name')
                 ->setRequired(true)
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->addValidator('Alnum')
                 ->addValidator('StringLength', false,
                                array(3,
                                      24));

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
                 ->setRequired(true)
                 ->setValue(null)
                 ->addValidator('StringLength', false,
                                array(6));
        $realm = new Zend_Form_Element_Select('realm');
        $realm->setLabel('Role')
              ->addMultiOptions(array('user'=>'User', 'admin'=>'Admin'))
              ->setRequired(true)
              ->setValue('user');

        $rememberMe = new Zend_Form_Element_Checkbox('rememberMe');
        $rememberMe->setLabel('Remember Me');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Login');

        $form->addElements(array($realm, $username, $password, $rememberMe, $submit));

        return $form;
    }

    // аутентификация - самая простая - используя Digest Adapter
    protected function _authenticate($realm, $login, $password)
    {
        $authAdapter = new Zend_Auth_Adapter_Digest(APPLICATION_PATH . '/configs/auth', $realm, $login, $password);

        $result = $authAdapter->authenticate();

        if ($result->isValid()) {
            // success: сохраняем роль пользователя в Zend_Auth
            Zend_Auth::getInstance()->getStorage()->write($authAdapter->getRealm());
        }
        return $result;
    }

    // разлогиниваемся
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }
}
