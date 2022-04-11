<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use Spiral\Http\Request\InputManager;
use Spiral\Router\Annotation\Route;
use Spiral\Shared\GRPC\RequestContext;
use Spiral\Shared\Services\Users\v1\DTO\User;
use Spiral\Shared\Services\Users\v1\DTO\UserAuthRequest;
use Spiral\Shared\Services\Users\v1\DTO\UserDeleteRequest;
use Spiral\Shared\Services\Users\v1\DTO\UserGetRequest;
use Spiral\Shared\Services\Users\v1\DTO\UserListRequest;
use Spiral\Shared\Services\Users\v1\DTO\UserRegisterRequest;
use Spiral\Shared\Services\Users\v1\UserServiceInterface;

class UsersController
{
    public function __construct(
        private UserServiceInterface $userService
    ) {
    }

    #[Route(route: 'users', name: 'user.list', methods: ['GET'])]
    public function index(InputManager $input): array
    {
        $response = $this->userService->List(
            new RequestContext(),
            new UserListRequest(['page' => (int) ($input->query('page') ?? 1), 'per_page' => 10])
        );

        return [
            'data' => \array_map(fn(User $user) => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'is_admin' => $user->getIsAdmin(),
                'created_at' => Carbon::createFromTimestamp($user->getCreatedAt()->getSeconds())->toDateTimeString(),
            ], \iterator_to_array(
                $response->getUsers()->getIterator()
            )),
            'current_page' => $response->getPagination()->getCurrentPage(),
            'per_page' => $response->getPagination()->getPerPage(),
            'last_page' => $response->getPagination()->getLastPage(),
            'total_pages' => $response->getPagination()->getTotal()
        ];
    }

    #[Route(route: 'user/<id:\d+>', name: 'user.show', methods: ['GET'])]
    public function get(int $id): array
    {
        $user = $this->userService->Get(
            new RequestContext(),
            new UserGetRequest(['id' => $id])
        )->getUser();

        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'is_admin' => $user->getIsAdmin(),
            'created_at' => Carbon::createFromTimestamp($user->getCreatedAt()->getSeconds())->toDateTimeString(),
        ];
    }

    #[Route(route: 'user/auth', name: 'user.auth', methods: ['POST'])]
    public function auth(InputManager $input): array
    {
        $response = $this->userService->Auth(
            new RequestContext(),
            new UserAuthRequest([
                'username' => $input->post('username'),
                'password' => $input->post('password')
            ])
        );

        $user = $response->getUser();
        $toke = $response->getToken();

        return [
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'is_admin' => $user->getIsAdmin(),
                'created_at' => Carbon::createFromTimestamp($user->getCreatedAt()->getSeconds())->toDateTimeString(),
            ],
            'token' => [
                'token' => $toke->getToken(),
                'expires_at' => Carbon::createFromTimestamp($toke->getExpiresAt()->getSeconds())->toDateTimeString(),
            ]
        ];
    }

    #[Route(route: 'user', name: 'user.register', methods: ['POST'])]
    public function register(InputManager $input): array
    {
        // Validate input data
        // ...

        $user = $this->userService->Register(
            (new RequestContext())->withToken($input->header('Authorization')),
            new UserRegisterRequest([
                'username' => $input->input('username'),
                'email' => $input->input('email'),
                'password' => $input->input('password'),
                'is_admin' => (bool) $input->input('is_admin'),
            ])
        )->getUser();

        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'is_admin' => $user->getIsAdmin(),
            'created_at' => Carbon::createFromTimestamp($user->getCreatedAt()->getSeconds())->toDateTimeString(),
        ];
    }

    #[Route(route: 'user/<id:\d+>', name: 'user.delete', methods: ['DELETE'])]
    public function delete(InputManager $input, int $id): string
    {
        $this->userService->Delete(
            (new RequestContext())->withToken($input->header('Authorization')),
            new UserDeleteRequest(['id' => $id])
        );

        return 'OK';
    }
}
