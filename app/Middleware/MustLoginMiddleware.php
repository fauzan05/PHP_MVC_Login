<?php

namespace Fauzannurhidayat\PhpMvc\Login\Middleware;

use Fauzannurhidayat\PhpMvc\Login\App\View;
use Fauzannurhidayat\PhpMvc\Login\Config\Database;
use Fauzannurhidayat\PhpMvc\Login\Repository\SessionRepository;
use Fauzannurhidayat\PhpMvc\Login\Repository\UserRepository;
use Fauzannurhidayat\PhpMvc\Login\Service\SessionService;

class MustLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }
    public function before():void
    {
        $user = $this->sessionService->current(); //ada session usernya
        if($user == null)
        {
            View::Redirect('/PHP_MVC_LOGIN/public/users/login');
        }
    }
}