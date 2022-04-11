<?php

declare(strict_types=1);

namespace App\Services\Blog;

use Cycle\ORM\EntityManagerInterface;
use Spiral\RoadRunner\GRPC;
use Spiral\Shared\Attributes\Guarded;
use Spiral\Shared\Services\Blog\v1\BlogServiceInterface;
use Spiral\Shared\Services\Blog\v1\DTO;
use Spiral\Shared\Services\Common\v1\DTO\Pagination;
use Spiral\Shared\Services\Users\v1\DTO\UserGetRequest;
use Spiral\Shared\Services\Users\v1\DTO\UserListByIdsRequest;
use Spiral\Shared\Services\Users\v1\UserServiceInterface;

final class BlogService implements BlogServiceInterface
{
    public function __construct(
        private PostRepository $posts,
        private EntityManagerInterface $em,
        private UserServiceInterface $userService
    ) {
    }

    public function List(GRPC\ContextInterface $ctx, DTO\PostListRequest $in): DTO\PostListResponse
    {
        $posts = $this->posts->paginate($in->getPage(), $in->getPerPage());

        $userIds = \array_unique(\array_map(static fn(Post $post) => $post->getAuthorId(), $posts->items()));

        $users = [];
        foreach ($this->userService->ListByIds($ctx, new UserListByIdsRequest(['ids' => $userIds]))
                     ->getUsers()->getIterator() as $user) {
            $users[$user->getId()] = $user;
        }

        $response = new DTO\PostListResponse();
        $response->setPagination(new Pagination($posts->toArray()));

        return $response->setPosts(
            \array_map(
                static fn (Post $post) => PostDTOFactory::fromEntity($post, $users[$post->getAuthorId()] ?? null),
                $posts->items()
            )
        );
    }

    public function Get(GRPC\ContextInterface $ctx, DTO\PostGetRequest $in): DTO\PostGetResponse
    {
        $post = $this->posts->getByPK($in->getId());
        $user = $this->userService->Get($ctx, new UserGetRequest(['id' => $post->getAuthorId()]))->getUser();

        return new DTO\PostGetResponse([
            'post' => PostDTOFactory::fromEntity($post, $user)
        ]);
    }

    #[Guarded]
    public function Create(GRPC\ContextInterface $ctx, DTO\PostCreateRequest $in): DTO\PostGetResponse
    {
        $userId = $ctx->getToken()->get('token')->getPayload()['id'];
        $user = $this->userService->Get($ctx, new UserGetRequest(['id' => $userId]))->getUser();

        $post = new Post(
            $in->getTitle(),
            $in->getText(),
            $userId
        );

        $this->em->persist($post)->run();

        return new DTO\PostGetResponse([
            'post' => PostDTOFactory::fromEntity($post, $user)
        ]);
    }

    #[Guarded]
    public function Update(GRPC\ContextInterface $ctx, DTO\PostUpdateRequest $in): DTO\PostGetResponse
    {
        $post = $this->posts->getByPK($in->getId());
        $user = $this->userService->Get(
            $ctx,
            new UserGetRequest(['id' => $post->getAuthorId()])
        )->getUser();

        $post->setTitle($in->getTitle());
        $post->setText($in->getText());

        $this->em->persist($post)->run();

        return new DTO\PostGetResponse([
            'post' => PostDTOFactory::fromEntity($post, $user)
        ]);
    }

    #[Guarded]
    public function Delete(GRPC\ContextInterface $ctx, DTO\PostDeleteRequest $in): DTO\PostDeleteResponse
    {
        $post = $this->posts->getByPK($in->getId());

        $this->em->delete($post)->run();

        return new DTO\PostDeleteResponse();
    }
}
