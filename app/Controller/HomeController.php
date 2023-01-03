<?php

namespace Fauzannurhidayat\PhpMvc\Login\Controller;

use Fauzannurhidayat\PhpMvc\Login\App\View;
use Fauzannurhidayat\PhpMvc\Login\Config\Database;
use Fauzannurhidayat\PhpMvc\Login\Repository\SessionRepository;
use Fauzannurhidayat\PhpMvc\Login\Repository\UserRepository;
use Fauzannurhidayat\PhpMvc\Login\Service\SessionService;

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }
    function index()
    {
        $user = $this->sessionService->current();
        if($user == null)
        {
            View::Render('Home/Index', [
                "title" => "PHP Login Management"
            ]);
        }else{
            View::Render('Home/Dashboard', [
                "title" => "Dashboard",
                "user" => $user->name
            ]);
        }
        
    }
}