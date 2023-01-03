<?php

namespace Fauzannurhidayat\PhpMvc\Login\Middleware {

    require_once __DIR__ . "/../Helper/Helper.php";

    use Fauzannurhidayat\PhpMvc\Login\Config\Database;
    use Fauzannurhidayat\PhpMvc\Login\Domain\Session;
    use Fauzannurhidayat\PhpMvc\Login\Domain\User;
    use Fauzannurhidayat\PhpMvc\Login\Repository\SessionRepository;
    use Fauzannurhidayat\PhpMvc\Login\Repository\UserRepository;
    use Fauzannurhidayat\PhpMvc\Login\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class MustNotLoginMiddlewareTest extends TestCase
    {
        private MustNotLoginMiddleware $mustLoginMiddleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        public function setUp(): void
        {
            $this->mustLoginMiddleware = new MustNotLoginMiddleware();
            putenv("mode=test");

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->mustLoginMiddleware->before();
            $this->expectOutputString("");
        }

        public function testBeforeLoginUser()
        {
            //if user login
            $user = new User();
            $user->id = "14";
            $user->name = "Fauzan14";
            $user->password = "Fauzan14";
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->mustLoginMiddleware->before();
            $this->expectOutputRegex("[Location: /PHP_MVC_LOGIN/public]");
        }
    }
}
