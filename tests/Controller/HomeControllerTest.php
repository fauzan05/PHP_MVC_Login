<?php

namespace Fauzannurhidayat\PhpMvc\Login\Controller;

use Fauzannurhidayat\PhpMvc\Login\Config\Database;
use Fauzannurhidayat\PhpMvc\Login\Domain\Session;
use Fauzannurhidayat\PhpMvc\Login\Domain\User;
use Fauzannurhidayat\PhpMvc\Login\Repository\SessionRepository;
use Fauzannurhidayat\PhpMvc\Login\Repository\UserRepository;
use Fauzannurhidayat\PhpMvc\Login\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository($connection);
        $this->userRepository = new UserRepository($connection);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGuest()
    {
        $this->homeController->index();

        $this->expectOutputRegex("[Login Management]");
    }
    public function testUserLogin()
    {
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

        $this->homeController->index();

        $this->expectOutputRegex("[Hello Fauzan14]");   
    }
}