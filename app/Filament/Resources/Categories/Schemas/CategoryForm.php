<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true) // 離開輸入框時觸發驗證與連動
                    ->unique(Category::class, 'name', ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'This category name has already been taken.',
                    ])
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, $context) {
                        if ($context === 'edit' && !blank($get('slug'))) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->label(fn ($context) => "URL Slug (" . ucfirst($context) . " Mode)")
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::lower($state)))
                    ->unique(Category::class, 'slug', ignoreRecord: true)
                    ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                    ->validationMessages([
                        'regex' => 'The slug must only contain lowercase letters, numbers, and hyphens.',
                        'unique' => 'This slug is already in use.',
                        'required' => 'The slug is required. If you cleared it, change the Name to regenerate.',
                    ])
                    ->helperText('Automatically generated from Name if empty.'),
            ]);
    }
}
