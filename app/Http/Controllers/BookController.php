<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShareBookRequest;
use Illuminate\Http\JsonResponse;
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Services\ResponseService;
use App\Http\Resources\BookResource;

class BookController extends Controller
{
    public function __construct(
        private readonly BookRepositoryInterface $books,
        private readonly ResponseService $response
    ) {}

    public function store(ShareBookRequest $request): JsonResponse
    {
        $user = $request->user();
        $book = $this->books->create([
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'description' => $request->input('description'),
            'user_id' => $user->id,
        ]);

        return $this->response->created([
            'book' => (new BookResource($book))->toArray($request),
        ], 'Book shared successfully');
    }

    public function nearby(): JsonResponse
    {
        $user = auth()->user();
        $lat = (float) $user->latitude;
        $lng = (float) $user->longitude;
        $radius = 10; // km

        $books = $this->books->nearbyForUser($user->id, $lat, $lng, $radius);
        $resources = BookResource::collection($books);

        return $this->response->success([
            'books' => $resources,
        ]);
    }
}
