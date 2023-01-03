<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Fauzannurhidayat\PhpMvc\Login\App\Router;
use Fauzannurhidayat\PhpMvc\Login\Config\Database;
use Fauzannurhidayat\PhpMvc\Login\Controller\HomeController;
use Fauzannurhidayat\PhpMvc\Login\Controller\UserController;
use Fauzannurhidayat\PhpMvc\Login\Middleware\MustLoginMiddleware;
use Fauzannurhidayat\PhpMvc\Login\Middleware\MustNotLoginMiddleware;

Database::getConnection('prod');

//Home controller
Router::add('GET', '/', HomeController::class, 'index', []);
//User controller
Router::add('GET', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]);
Router::add('GET', '/users/profile', UserController::class, 'updateProfile', [MustLoginMiddleware::class]);
Router::add('POST', '/users/profile', UserController::class, 'postUpdateProfile', [MustLoginMiddleware::class]);
Router::add('GET', '/users/password', UserController::class, 'updatePassword', [MustLoginMiddleware::class]);
Router::add('POST', '/users/password', UserController::class, 'postUpdatePassword', [MustLoginMiddleware::class]);
Router::run();
