<?php

declare(strict_types=1);

namespace App\Services\Users;

use Cycle\ORM\EntityManagerInterface;
use Google\Protobuf\Timestamp;
use Spiral\Auth\TokenInterface;
use Spiral\Auth\TokenStorageInterface;
use Spiral\RoadRunner\GRPC;
use Spiral\Shared\Attributes\Guarded;
use Spiral\Shared\Attributes\InjectInterceptor;
use Spiral\Shared\GRPC\RequestContext;
use Spiral\Shared\Services\Common\v1\DTO\Pagination;
use Spiral\Shared\Services\Common\v1\DTO\Token;
use Spiral\Shared\Services\Users\v1\DTO;
use Spiral\Shared\Services\Users\v1\UserServiceInterface;

final class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepository $users,
        private EntityManagerInterface $em,
        private TokenStorageInterface $tokens
    ) {
    }

    public function List(GRPC\ContextInterface $ctx, DTO\UserListRequest $in): DTO\UserListResponse
    {
        $users = $this->users->paginate($in->getPage(), $in->getPerPage());

        $response = new DTO\UserListResponse();
        $response->setPagination(new Pagination($users->toArray()));
        $response->setUsers(
            \array_map(
                static fn (User $user) => UserDTOFactory::fromEntity($user),
                $users->items()
            )
        );

        return $response;
    }

    public function ListByIds(GRPC\ContextInterface $ctx, DTO\UserListByIdsRequest $in): DTO\UserListResponse
    {
        $users = $this->users->findAllInPKs(\iterator_to_array($in->getIds()->getIterator()));

        return new DTO\UserListResponse([
            'users' => \array_map(
                static fn (User $user) => UserDTOFactory::fromEntity($user),
                $users
            )
        ]);
    }

    public function Get(GRPC\ContextInterface $ctx, DTO\UserGetRequest $in): DTO\UserGetResponse
    {
        $user = $this->users->getByPK($in->getId());

        return new DTO\UserGetResponse([
            'user' => UserDTOFactory::fromEntity($user)
        ]);
    }

    public function Register(GRPC\ContextInterface $ctx, DTO\UserRegisterRequest $in): DTO\UserGetResponse
    {
        $user = new User(
            $in->getUsername(),
            $in->getEmail(),
            $in->getPassword(),
        );

        if ($in->getIsAdmin()) {
            $user->grantAdminPrivileges();
        }

        $this->em->persist($user)->run();

        return new DTO\UserGetResponse([
            'user' => UserDTOFactory::fromEntity($user)
        ]);
    }

    /**
     * @param RequestContext $ctx
     */
    public function Auth(GRPC\ContextInterface $ctx, DTO\UserAuthRequest $in): DTO\UserAuthResponse
    {
        $user = $this->users->getByUsername($in->getUsername());

        $response = new DTO\UserAuthResponse();

        if (!$user->verifyPassword($in->getPassword())) {
            throw new GRPC\Exception\GRPCException(
                'Invalid username or password',
                GRPC\StatusCode::PERMISSION_DENIED
            );
        }

        $userToken = $this->tokens->create([
            'id' => $user->getId(),
        ]);

        $token = new Token();
        $token->setToken($userToken->getID());
        $token->setExpiresAt(new Timestamp(['seconds' => $userToken->getExpiresAt()->getTimestamp()]));
        $response->setToken($token);
        $response->setUser(UserDTOFactory::fromEntity($user));

        $ctx->withToken($userToken->getID());

        return $response;
    }

    #[Guarded]
    public function Update(GRPC\ContextInterface $ctx, DTO\UserUpdateRequest $in): DTO\UserGetResponse
    {
        $user = $this->users->getByPK($in->getId());

        $user->setUsername($in->getUsername());
        $user->setEmail($in->getEmail());
        $user->setPassword($in->getPassword());
        $this->em->persist($user)->run();

        return new DTO\UserGetResponse([
            'user' => UserDTOFactory::fromEntity($user)
        ]);
    }

    #[Guarded]
    public function Delete(GRPC\ContextInterface $ctx, DTO\UserDeleteRequest $in): DTO\UserDeleteResponse
    {
        $userId = (int) $ctx->getValue(TokenInterface::class)->getPayload()['id'];
        if (!$this->users->getByPK($userId)->isAdmin()) {
            throw new GRPC\Exception\GRPCException(
                'Only admins can delete users',
                GRPC\StatusCode::PERMISSION_DENIED
            );
        }

        $this->em->delete($this->users->getByPK($in->getId()))->run();

        return new DTO\UserDeleteResponse();
    }
}
