<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use GuzzleHttp\Handler\Proxy;
use PDO;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Total number of registered users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Posts', Post::where('is_published', true)->count())
                ->description('Total number of published posts')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart(
                    Post::where('is_published', true)
                        ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                        ->groupBy('date')
                        ->orderBy('date', 'asc')
                        ->pluck('count', 'date')
                        ->toArray()
                )
                ->color('info'),
            Stat::make('Total Products', Product::count())
                ->description('Total number of products')
                ->descriptionIcon(Product::count() == 0 ? 'heroicon-m-cube-transparent' : 'heroicon-m-arrow-trending-up')
                ->chart(
                    Product::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                        ->groupBy('date')
                        ->orderBy('date', 'asc')
                        ->pluck('count', 'date')
                        ->toArray()
                )
                ->color('warning'),
        ];
    }
}
