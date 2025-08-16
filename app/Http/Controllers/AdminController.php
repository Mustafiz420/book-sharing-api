<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Services\ResponseService;
use App\Http\Resources\UserResource;
use App\Http\Resources\BookResource;

class AdminController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly BookRepositoryInterface $books,
        private readonly ResponseService $response
    ) {}

    public function users(): JsonResponse
    {
        $users = $this->users->allBasic();
        return $this->response->success(['users' => $users]);
    }

    public function books(): JsonResponse
    {
        $books = $this->books->allWithUser();
        return $this->response->success([
            'books' => BookResource::collection($books),
        ]);
    }

    public function deleteBook(int $id): JsonResponse
    {
        $this->books->deleteById($id);
        return $this->response->success([], 'Book deleted successfully');
    }
}
