<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('General Information')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Product Name')
                                    ->weight('bold')
                                    ->color('info'),
                                TextEntry::make('sku')
                                    ->label('SKU')
                                    ->weight('bold')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('category')
                                    ->label('Category')
                                    ->color('info'),
                                TextEntry::make('description')
                                    ->label('Description')
                                    ->weight('bold')
                                    ->markdown(),
                            ]),
                        Tab::make('Pricing & Stock')
                            ->schema([
                                TextEntry::make('price')
                                    ->label('Price')
                                    ->weight('bold')
                                    ->numeric()
                                    ->color('info'),
                                TextEntry::make('stock_quantity')
                                    ->label('Stock Quantity')
                                    ->weight('bold')
                                    ->color('info'),
                            ]),
                        Tab::make('Additional Details')
                            ->schema([
                                ImageEntry::make('image_url')
                                    ->label('Image')
                                    ->disk('public'),
                                IconEntry::make('is_active')
                                    ->label('Is Active')
                                    ->boolean(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->vertical(),
            ]);
    }
}
