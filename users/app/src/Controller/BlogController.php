<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use Spiral\Http\Request\InputManager;
use Spiral\Router\Annotation\Route;
use Spiral\Shared\GRPC\RequestContext;
use Spiral\Shared\Services\Blog\v1\BlogServiceInterface;
use Spiral\Shared\Services\Blog\v1\DTO\Post;
use Spiral\Shared\Services\Blog\v1\DTO\PostCreateRequest;
use Spiral\Shared\Services\Blog\v1\DTO\PostDeleteRequest;
use Spiral\Shared\Services\Blog\v1\DTO\PostGetRequest;
use Spiral\Shared\Services\Blog\v1\DTO\PostListRequest;
use Spiral\Shared\Services\Blog\v1\DTO\PostUpdateRequest;

class BlogController
{
    public function __construct(
        private BlogServiceInterface $blogService
    ) {
    }

    #[Route(route: 'blog', name: 'blog.list', methods: ['GET'])]
    public function index(InputManager $input): array
    {
        $response = $this->blogService->List(
            new RequestContext(),
            new PostListRequest(['page' => (int) ($input->query('page') ?? 1), 'per_page' => 10])
        );

        return [
            'data' => \array_map(fn(Post $post) => [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'text' => $post->getText(),
                'author' => $post->getAuthor() ? [
                    'id' => $post->getAuthor()->getId(),
                    'username' => $post->getAuthor()->getUsername()
                ] : null,
                'created_at' => Carbon::createFromTimestamp($post->getCreatedAt()->getSeconds())->toDateTimeString(),
            ], \iterator_to_array(
                $response->getPosts()->getIterator()
            )),
            'current_page' => $response->getPagination()->getCurrentPage(),
            'per_page' => $response->getPagination()->getPerPage(),
            'last_page' => $response->getPagination()->getLastPage(),
            'total_pages' => $response->getPagination()->getTotal()
        ];
    }

    #[Route(route: 'blog/post/<id:\d+>', name: 'blog.post.show', methods: ['GET'])]
    public function get(int $id): array
    {
        $post = $this->blogService->Get(
            new RequestContext(),
            new PostGetRequest(['id' => $id])
        )->getPost();

        return [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'text' => $post->getText(),
            'author' => $post->getAuthor() ? [
                'id' => $post->getAuthor()->getId(),
                'username' => $post->getAuthor()->getUsername()
            ] : null,
            'created_at' => Carbon::createFromTimestamp($post->getCreatedAt()->getSeconds())->toDateTimeString(),
        ];
    }

    #[Route(route: 'blog/post', name: 'blog.post.create', methods: ['POST'])]
    public function create(InputManager $input): string
    {
        // Validate input data
        // ...

        $this->blogService->Create(
            (new RequestContext())->withToken($input->header('Authorization')),
            new PostCreateRequest([
                'title' => $input->post('title'),
                'text' => $input->post('text'),
            ])
        );

        return 'OK';
    }

    #[Route(route: 'blog/post/<id:\d+>', name: 'blog.post.update', methods: ['POST'])]
    public function update(InputManager $input, int $id): string
    {
        // Validate input data
        // ...

        $this->blogService->Update(
            (new RequestContext())->withToken($input->header('Authorization')),
            new PostUpdateRequest([
                'id' => $id,
                'title' => $input->post('title'),
                'text' => $input->post('text'),
            ])
        );

        return 'OK';
    }

    #[Route(route: 'blog/post/<id:\d+>', name: 'blog.post.delete', methods: ['DELETE'])]
    public function delete(InputManager $input, int $id): string
    {
        $this->blogService->Delete(
            (new RequestContext())->withToken($input->header('Authorization')),
            new PostDeleteRequest(['id' => $id])
        );

        return 'OK';
    }
}
