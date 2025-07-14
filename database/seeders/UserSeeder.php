<?php // database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        $passwordEnc = Hash::make('password'); // Use a single hashed password for speed
        for ($i = 0; $i < 1_000; $i++) {
            $data[] = [
                'name' => fake()->name(),
                'email' => fake()->unique()->email(),
                'password' => $passwordEnc,
            ];
        }

        // Insert data in chunks to improve performance
        foreach (array_chunk($data, 100) as $chunk) {
            User::insert($chunk);
        }
    }
}
