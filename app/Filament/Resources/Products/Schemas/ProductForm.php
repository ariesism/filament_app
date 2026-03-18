<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('General Information')
                        ->completedIcon(Heroicon::HandThumbUp)
                        ->schema([
                            Group::make()
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Product Name')
                                        ->required(),
                                    TextInput::make('sku')
                                        ->label('SKU')
                                        ->required()
                                        ->unique(ignoreRecord: true),
                                ])->columns(2),
                            TextInput::make('category')
                                ->label('Category'),
                            MarkdownEditor::make('description')
                                ->label('Description'),
                        ]),
                    Step::make('Pricing & Stock')
                        ->completedIcon(Heroicon::HandThumbUp)
                        ->schema([
                            Group::make()
                                ->schema([
                                    TextInput::make('price')
                                        ->label('Price')
                                        ->required()
                                        ->numeric(),
                                    TextInput::make('stock_quantity')
                                        ->label('Stock Quantity')
                                        ->required()
                                        ->integer(),
                                ])->columns(2),
                        ]),
                    Step::make('Additional Details')
                        ->completedIcon(Heroicon::HandThumbUp)
                        ->schema([
                            FileUpload::make('image_url')
                                ->label('Image')
                                ->disk('public')
                                ->directory('product-images'),
                            Checkbox::make('is_active')
                                ->label('Is Active'),
                        ]),
                ])
                ->columnSpanFull()
                ->skippable()
                ->submitAction(
                    Action::make('save')
                        ->label('Save Product')
                        ->submit('save'),
                )
            ]);
    }
}
