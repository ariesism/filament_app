<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Tag;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fields')
                    ->description('Fill all the fields')
                    ->icon(Heroicon::PencilSquare)
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
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
                                    ->unique(ignoreRecord: true)
                                    ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                                    ->validationMessages([
                                        'unique' => '這個網址別名 (Slug) 已經被使用了，請更換內容。',
                                        'regex' => 'Slug 只能包含小寫英文、數字與連字號 (-)。',
                                    ])
                                    ->helperText('這會作為文章的網址。若清空並修改標題，系統會重新生成。'),
                                Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->required(),
                                ColorPicker::make('color')
                                    ->label('Post Color')
                                    ->required(),
                            ])->columns(2),
                        MarkdownEditor::make('content')
                            ->label('Content')
                            ->required(),
                    ])->columnSpan(2),
                Group::make([
                    Section::make('Media')
                        ->description('Upload images')
                        ->icon(Heroicon::Photo)
                        ->schema([
                            FileUpload::make('image')
                            ->label('Featured Image')
                            ->image()
                            ->disk('public')
                            ->directory('posts'),
                    ]),
                    Section::make('Metadata')
                        ->description('Additional information about the post')
                        ->icon(Heroicon::InformationCircle)
                        ->schema([
                            Select::make('tags')
                                ->label('Tags')
                                ->relationship('tags', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->maxItems(5)
                                ->rules(['array'])
                                ->helperText('Select up to 5 tags.')
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(50),
                                ])
                                ->noSearchResultsMessage('No tags found. You can create one.')
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(50),
                                ])
                                ->createOptionUsing(function (array $data) {
                                    return Tag::firstOrCreate([
                                        'name' => $data['name'],
                                    ])->id;
                                }),
                            Checkbox::make('is_published')
                                ->label('Published'),
                            DateTimePicker::make('published_at')
                                ->label('Published At')
                                ->native(false)
                                ->seconds(false)
                                ->minDate(fn ($context, $record) => 
                                    ($context === 'edit' && $record?->published_at?->isPast()) 
                                        ? $record->published_at 
                                        : now()
                                )
                                ->rules([
                                    fn ($get, $context, $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $context, $record) {
                                        $inputTime = strtotime($value);
                                        $originalTime = $record?->published_at ? strtotime($record->published_at) : null;

                                        // 只有當「時間被改動了」才需要檢查是不是選了過去的時間
                                        if ($inputTime !== $originalTime) {
                                            // 允許 1 分鐘誤差，避免填表太久
                                            if ($inputTime < (time() - 60)) {
                                                $fail('如果要修改發布時間，請選擇一個未來的時間點。');
                                            }
                                        }
                                    },
                                ])
                                ->helperText(fn ($record) => $record?->published_at?->isPast() 
                                    ? '這篇文章已經發布。修改時間將會改變它在前端的排序。' 
                                    : '設定預約發布的時間。'
                                )
                                ->helperText('Select the date and time when the post should be published.'),
                        ])
                ])->columnSpan(1),
            ])->columns(3);
    }
}
