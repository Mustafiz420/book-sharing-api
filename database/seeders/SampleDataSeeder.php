<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Book;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $u1 = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('secret123'),
            'latitude' => 40.7130,
            'longitude' => -74.0059,
            'role' => 'user',
        ]);

        $u2 = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('secret123'),
            'latitude' => 40.7220,
            'longitude' => -74.0000,
            'role' => 'user',
        ]);

        Book::create(['title' => '1984', 'author' => 'George Orwell', 'description' => 'Dystopian novel', 'user_id' => $u2->id]);
        Book::create(['title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'description' => 'Classic novel', 'user_id' => $u1->id]);
    }
}
