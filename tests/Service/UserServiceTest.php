<?php

namespace Fauzannurhidayat\PhpMvc\Login\Service;

use Fauzannurhidayat\PhpMvc\Login\Domain\User;
use Fauzannurhidayat\PhpMvc\Login\Repository\UserRepository;
use Fauzannurhidayat\PhpMvc\Login\Config\Database;
use Fauzannurhidayat\PhpMvc\Login\Exception\ValidationException;
use Fauzannurhidayat\PhpMvc\Login\Model\UserLoginRequest;
use Fauzannurhidayat\PhpMvc\Login\Model\UserPasswordUpdateRequest;
use Fauzannurhidayat\PhpMvc\Login\Model\UserProfileUpdateRequest;
use Fauzannurhidayat\PhpMvc\Login\Model\UserRegisterRequest;
use Fauzannurhidayat\PhpMvc\Login\Repository\SessionRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected  function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }
    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = "1";
        $request->name = "Nama1";
        $request->password = "Password1";

        $response = $this->userService->register($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);
        self::assertNotEquals($request->password, $response->user->password);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }
    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = "";
        $request->password = "";

        $this->userService->register($request);
    }

    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->id = "2";
        $user->name = "Fauzan";
        $user->password = "yaya";

        $this->userRepository->save($user);
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "2";
        $request->name = "Fauzan";
        $request->password = "yaya";
        $this->userService->register($request);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "fauzan";
        $request->password = "fauzan";
        /*
        ini adalah password yang sudah dienkripsi, sedangkan di database 'test' si password tidak dienkripsi
        */
        $this->userService->login($request);
    }
    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = "1";
        $user->name = "Nama1";
        $user->password = password_hash("Password1", PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "1";
        $request->password = "Password1";

        $this->userService->login($request);
    }
    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = "1";
        $user->name = "Nama1";
        $user->password = password_hash("Password1", PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "fauzan";
        $request->password = "fauzan";

        $response = $this->userService->login($request);
        self::assertEquals($request->id, $response->user->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }
    public function testUpdateSuccess()
    {
        $user = new User();
        $user->id = "1";
        $user->name = "Nama1";
        $user->password = password_hash("Password1", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest();
        $request->id = "1";
        $request->name = "Fauzan";
        $this->userService->updateProfile($request);

        $result = $this->userRepository->findById($user->id);
        self::assertEquals($result->name, $request->name);
    }
    public function testUpdateValidationError()
    {
        $this->expectException(ValidationException::class);
        $request = new UserProfileUpdateRequest();
        $request->id = "";
        $request->name = "";
        $this->userService->updateProfile($request);
    }
    public function testUpdateNotFound()
    {
        $this->expectException(ValidationException::class);
        $request = new UserProfileUpdateRequest();
        $request->id = "1";
        $request->name = "Fauzan";
        $this->userService->updateProfile($request);
    }
    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->id = "1";
        $user->name = "Nama1";
        $user->password = password_hash("Password1", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = "Password1";
        $request->newPassword = "yaya";
        $this->userService->updatePassword($request);

        $result = $this->userRepository->findById($user->id);

        self::assertTrue(password_verify($request->newPassword, $result->password));
    }
    public function testUpdatePasswordValidationError()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->id = "";
        $request->oldPassword = "";
        $request->newPassword = "";
        $this->userService->updatePassword($request);
    }
    public function testUpdatePasswordWrongOldPassword()
    {
        $this->expectException(ValidationException::class);
        $user = new User();
        $user->id = "1";
        $user->name = "Nama1";
        $user->password = password_hash("Password1", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = "Gatau";
        $request->newPassword = "yaya";
        $this->userService->updatePassword($request);
    }
    public function testUpdatePasswordNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->id = "";
        $request->oldPassword = "Gatau";
        $request->newPassword = "yaya";
        $this->userService->updatePassword($request);
    }
}
