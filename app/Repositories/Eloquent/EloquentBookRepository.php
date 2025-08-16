<?php

namespace App\Repositories\Eloquent;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentBookRepository implements BookRepositoryInterface
{
    public function create(array $data): Book
    {
        return Book::create($data);
    }

    public function allWithUser(): Collection
    {
        return Book::with(['user:id,name'])->get();
    }

    public function deleteById(int $id): void
    {
        Book::findOrFail($id)->delete();
    }

    public function nearbyForUser(int $authUserId, float $lat, float $lng, float $radiusKm = 10.0): Collection
    {
        return Book::query()
            ->select('books.*')
            ->selectRaw('users.id as user_id, users.name as user_name')
            ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(users.latitude)) * cos(radians(users.longitude) - radians(?)) + sin(radians(?)) * sin(radians(users.latitude)))) as distance_km', [$lat, $lng, $lat])
            ->join('users', 'users.id', '=', 'books.user_id')
            ->where('books.user_id', '!=', $authUserId)
            ->having('distance_km', '<=', $radiusKm)
            ->orderBy('distance_km')
            ->get();
    }
}
