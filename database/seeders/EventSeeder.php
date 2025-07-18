<?php

// database/seeders/EventSeeder.php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        for ($i = 0; $i < 100_000; $i++) {
            $data[] = [
                'user_id' => random_int(1, 1_000), // Assuming we have 1_000 users with auto-incrementing IDs
                'description' => fake()->realText(),
                'value' => random_int(1, 10),
                'date' => fake()->dateTimeThisYear(),
                'type' => array_rand(['ALERT', 'WARNING', 'INFO']),
            ];
        }

        // Chunking is essential for large datasets to avoid memory issues
        foreach (array_chunk($data, 500) as $chunk) {
            Event::insert($chunk);
        }
    }
}
