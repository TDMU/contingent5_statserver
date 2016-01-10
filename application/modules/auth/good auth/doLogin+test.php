protected function _doLogin($realm, $login, $password)
{
    $authAdapter = new Zend_Auth_Adapter_Digest(APPLICATION_PATH . '/configs/auth', $realm, $login, $password);

    $result = $authAdapter->authenticate();

    if ($result->isValid()) {
        // success: сохраняем роль пользователя в Zend_Auth
        Zend_Auth::getInstance()->getStorage()->write($authAdapter->getRealm());
    }
}

class LoginControllerTest extends ControllerTestCase
{
    // логинимся "правильным" данными
    public function testTrueUserLoginAction()
    {
        // эмулируем отправку формы
        $this->getRequest()
        	 ->setMethod('POST')
        	 ->setPost(array(  "realm" => "user",
        	                   "username" => "user",
        	                   "password" => "123456",
        	                   "rememberMe"=>1));

        $this->dispatch('/login/');

        // аутентификация должна пройти успешно, идентифицироваться мы должны как user
        $this->assertEquals(Zend_Auth::getInstance()->getIdentity(), 'user');

        // мы должны быть перенаправлены на главную страницу
        $this->assertRedirectTo('/');
    }

    // логинимся "неправильным" данными
    public function testFalseUserLoginAction()
    {
        $this->getRequest()
        	 ->setMethod('POST')
        	 ->setPost(array(  "realm" => "user",
        	                   "username" => "user",
        	                   "password" => "654321",
        	                   "rememberMe"=>0));

        $this->dispatch('/');

        // ищем в доме элемент с ID="error" и контентом 'Authorization error. Please check login or/and password'
        // лучше использовать assertQueryCount
        $this->assertQueryContentContains('#error', 'Authorization error. Please check login or/and password');
    }

    public function testLogoutAction()
    {
        // логинимся
        $this->_doLogin('admin', 'admin', '123456');

        // вызываем логаут
        $this->dispatch('/login/logout/');

        // теперь мы должны быть "забыты" Zend_Auth'ом
        $this->assertNull(Zend_Auth::getInstance()->getIdentity());
    }
}
