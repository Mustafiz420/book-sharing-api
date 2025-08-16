<?php

namespace App\Repositories\Contracts;

use App\Models\Book;
use Illuminate\Support\Collection;

interface BookRepositoryInterface
{
    public function create(array $data): Book;
    public function allWithUser(): Collection;
    public function deleteById(int $id): void;
    public function nearbyForUser(int $authUserId, float $lat, float $lng, float $radiusKm = 10.0): Collection;
}
