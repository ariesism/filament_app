<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            'USA', 'Japan', 'Taiwan', 'France', 'Italy',
            'Australia', 'Canada', 'Germany', 'Thailand', 'Korea'
        ];

        $topics = [
            'Travel Guide', 'Food Guide', 'Top Attractions',
            'Hidden Gems', 'Cultural Experience',
            'Best Places to Visit', 'Itinerary Plan',
            'Street Food Tour', 'Nature Exploration', 'City Walk'
        ];

        $colors = [
            '#3b82f6', '#22c55e', '#ef4444', '#f59e0b',
            '#6366f1', '#10b981', '#ec4899', '#f97316',
            '#0ea5e9', '#84cc16'
        ];

        $allTags = Tag::all();

        for ($i = 0; $i < 50; $i++) {
            $country = $countries[array_rand($countries)];
            $topic = $topics[array_rand($topics)];
            $title = trim("{$country} {$topic}");

            $category = Category::firstOrCreate([
                'name' => $country,
                'slug' => Str::slug($country),
            ]);

            $createdAt = now()->subDays(rand(0, 365));
            $publishedAt = (clone $createdAt)->addDays(rand(0, 15));

            $post = Post::create([
                'title' => $title,
                'slug' => Post::generateUniqueSlug($title),
                'category_id' => $category->id,
                'color' => $colors[array_rand($colors)],
                'image' => 'https://picsum.photos/seed/' . Str::slug($title) . '/800/600',
                'content' => fake()->paragraphs(5, true),
                'is_published' => true,
                'published_at' => $publishedAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $weightedTags = $allTags->merge(
                $allTags->whereIn('name', ['travel', 'guide', 'food'])
            );

            $randomTags = $weightedTags
                ->unique('id')
                ->random(rand(2, 5))
                ->pluck('id');

            $post->tags()->syncWithoutDetaching($randomTags);
        }
    }
}
