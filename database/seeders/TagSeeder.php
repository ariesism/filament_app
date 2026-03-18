<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'travel', 'tourism', 'guide', 'itinerary', 'tips',
            'food', 'street-food', 'local-cuisine', 'culture', 'festival',
            'city', 'landmarks', 'attractions', 'architecture', 'museums',
            'nature', 'mountains', 'beaches', 'national-parks', 'hiking',
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate([
                'name' => $tag,
            ]);
        }

    }
}
