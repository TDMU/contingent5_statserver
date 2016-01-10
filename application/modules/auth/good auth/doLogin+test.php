protected function _doLogin($realm, $login, $password)
{
    $authAdapter = new Zend_Auth_Adapter_Digest(APPLICATION_PATH . '/configs/auth', $realm, $login, $password);

    $result = $authAdapter->authenticate();

    if ($result->isValid()) {
        // success: ��������� ���� ������������ � Zend_Auth
        Zend_Auth::getInstance()->getStorage()->write($authAdapter->getRealm());
    }
}

class LoginControllerTest extends ControllerTestCase
{
    // ��������� "����������" �������
    public function testTrueUserLoginAction()
    {
        // ��������� �������� �����
        $this->getRequest()
        	 ->setMethod('POST')
        	 ->setPost(array(  "realm" => "user",
        	                   "username" => "user",
        	                   "password" => "123456",
        	                   "rememberMe"=>1));

        $this->dispatch('/login/');

        // �������������� ������ ������ �������, ������������������ �� ������ ��� user
        $this->assertEquals(Zend_Auth::getInstance()->getIdentity(), 'user');

        // �� ������ ���� �������������� �� ������� ��������
        $this->assertRedirectTo('/');
    }

    // ��������� "������������" �������
    public function testFalseUserLoginAction()
    {
        $this->getRequest()
        	 ->setMethod('POST')
        	 ->setPost(array(  "realm" => "user",
        	                   "username" => "user",
        	                   "password" => "654321",
        	                   "rememberMe"=>0));

        $this->dispatch('/');

        // ���� � ���� ������� � ID="error" � ��������� 'Authorization error. Please check login or/and password'
        // ����� ������������ assertQueryCount
        $this->assertQueryContentContains('#error', 'Authorization error. Please check login or/and password');
    }

    public function testLogoutAction()
    {
        // ���������
        $this->_doLogin('admin', 'admin', '123456');

        // �������� ������
        $this->dispatch('/login/logout/');

        // ������ �� ������ ���� "������" Zend_Auth'��
        $this->assertNull(Zend_Auth::getInstance()->getIdentity());
    }
}
