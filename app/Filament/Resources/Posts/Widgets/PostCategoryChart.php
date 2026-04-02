<?php

namespace App\Filament\Resources\Posts\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class PostCategoryChart extends ChartWidget
{
    protected ?string $heading = '各類別文章分佈';
    protected int|string|array $columnSpan = 'full';
    protected ?string $maxHeight = '250px';
    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $categories = Category::withCount('posts')
            ->orderBy('posts_count', 'desc')    
            ->get();
        $backgroundColors = $categories->map(function ($category) {
            return '#' . substr(md5($category->id), 0, 6);
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => '文章數量',
                    'data' => $categories->pluck('posts_count')->toArray(),
                    'backgroundColor' => $backgroundColors,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $categories->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
