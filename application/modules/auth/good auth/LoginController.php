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

        // �������� �� ��������� �� ������������
        if (!Zend_Auth::getInstance()->getIdentity()) {

            // ����� ����� ������ � ���������� �� view
            $form = $this->_getLoginForm();
            $this->view->form = $form;

            // ���� ����� ���� ����������, �� ������ � ���������
            if ($this->_request->isPost()) {
                $formData = $this->_request->getPost();

                // ��������� �����
                if ($form->isValid($formData)) {

                    // ��������� ������� ������
                    $result = $this->_authenticate($form->getValue('realm'), $form->getValue('username'), $form->getValue('password'));

                    if ($result->isValid()) {
                        // �������� ������������ �� 2 ������
                        if ($form->getValue('rememberMe')) {
                            Zend_Session::rememberMe(60*60*24*14);
                        }
                        // ���������� �� �������
                        $this->_redirect('/');
                    } else {
                        // failure: ������� ��������� � ������
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

    // ��������� ����� ������
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

    // �������������� - ����� ������� - ��������� Digest Adapter
    protected function _authenticate($realm, $login, $password)
    {
        $authAdapter = new Zend_Auth_Adapter_Digest(APPLICATION_PATH . '/configs/auth', $realm, $login, $password);

        $result = $authAdapter->authenticate();

        if ($result->isValid()) {
            // success: ��������� ���� ������������ � Zend_Auth
            Zend_Auth::getInstance()->getStorage()->write($authAdapter->getRealm());
        }
        return $result;
    }

    // ���������������
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }
}
