<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Enums\RoleEnum;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): void {
                $user = Auth::user();

                $query->forUser($user);

                $query->with(['category', 'tags', 'user']);
            })
            ->columns([
                TextColumn::make('id')
                    ->label('Article ID')
                    ->searchable()
                    ->sortable()
                    ->icon(Heroicon::ClipboardDocument)
                    ->iconPosition('after')
                    ->copyable()
                    ->copyMessage('文章 ID 已複製')
                    ->copyMessageDuration(1500),
                ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public'),
                TextColumn::make('user.name')
                    ->label('User Name')
                    ->searchable()
                    ->sortable()
                    ->visible(fn (): bool => Auth::user()?->hasAnyRole([
                        RoleEnum::Admin->value,
                        RoleEnum::Super_Admin->value,
                    ]) ?? false),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                ColorColumn::make('color')
                    ->label('Color')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tags')
                    ->label('Tags')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Published At')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->searchPlaceholder(fn (): string => Auth::user()?->hasAnyRole([
                RoleEnum::Admin->value,
                RoleEnum::Super_Admin->value,
            ])
                ? '搜尋文章 ID、標題、分類或作者名稱'
                : '搜尋文章 ID、標題或分類')
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                Filter::make('published_at')
                    ->label('Published At')
                    ->schema([
                        DatePicker::make('published_from')
                            ->label('Published From'),
                        DatePicker::make('published_until')
                            ->label('Published Until'),
                    ])
                    ->query(function ($query, $data) {
                        if ($data['published_from']) {
                            $query->whereDate('published_at', '>=', $data['published_from']);
                        }
                        if ($data['published_until']) {
                            $query->whereDate('published_at', '<=', $data['published_until']);
                        }
                    }),
                Filter::make('is_published')
                    ->label('Published')
                    ->query(fn ($query) => $query->where('is_published', true)),
            ])
            ->recordActions([
                Action::make('status')
                    ->label('Status')
                    ->icon('heroicon-o-document-check')
                    ->authorize(fn (Post $record): bool => Auth::user()?->can('update', $record) ?? false)
                    ->mountUsing(function ($form, Post $record) {
                        $form->fill([
                            'is_published' => $record->is_published,
                        ]);
                    })
                    ->schema([
                        Checkbox::make('is_published')
                            ->label('Published'),
                    ])
                    ->action(function (array $data, $record) {
                        $record->is_published = $data['is_published'];
                        $record->save();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
