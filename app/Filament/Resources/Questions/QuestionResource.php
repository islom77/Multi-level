<?php

namespace App\Filament\Resources\Questions;

use App\Filament\Resources\Questions\Pages\CreateQuestion;
use App\Filament\Resources\Questions\Pages\EditQuestion;
use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Models\Question;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Models\QuestionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Savollar';

    protected static ?string $pluralLabel = 'Savollar';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Savol Ma\'lumotlari')
                    ->schema([
                        Select::make('question_type_id')
                            ->label('Savol turi')
                            ->options(QuestionType::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $set('children', []);
                            }),

                        TextInput::make('name')
                            ->label('Savol nomi')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        RichEditor::make('text')
                            ->label('Savol matni (Rich Editor)')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                                'codeBlock',
                            ])
                            ->columnSpanFull(),

                        TextInput::make('order')
                            ->label('Tartib raqami (Mock ichida)')
                            ->numeric()
                            ->minValue(1),

                        Select::make('parent_id')
                            ->label('Ota savol')
                            ->options(Question::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])
                    ->columns(2),

                // Fill in the Blank uchun question_data va child questions
                Section::make('Bo\'sh joylarni to\'ldirish')
                    ->schema([
                        Placeholder::make('blank_info')
                            ->label('')
                            ->content('QuestionData ichida matn yozing va [blank] belgisini ishlating. Masalan: "The [blank] is shining."'),

                        // QuestionData uchun RichEditor va Audio
                        Repeater::make('questionData')
                            ->label('Savol ma\'lumotlari')
                            ->relationship('questionData')
                            ->schema([
                                RichEditor::make('text')
                                    ->label('Matn (Rich Editor) - [blank] dan foydalaning')
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ])
                                    ->columnSpanFull()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // [blank] larni sanash
                                        $blankCount = substr_count(strtolower($state ?? ''), '[blank]');

                                        // Hozirgi child questions
                                        $currentChildren = $get('../../children') ?? [];
                                        $currentCount = count($currentChildren);

                                        // Agar [blank] lar ko'p bo'lsa, yangilarini qo'shamiz
                                        if ($blankCount > $currentCount) {
                                            for ($i = $currentCount; $i < $blankCount; $i++) {
                                                $currentChildren[] = [
                                                    'name' => 'Blank ' . ($i + 1),
                                                    'text' => 'Blank ' . ($i + 1),
                                                    'order' => $i + 1,
                                                    'keyWords' => [
                                                        ['word' => '']
                                                    ]
                                                ];
                                            }
                                            $set('../../children', $currentChildren);
                                        }
                                        // Agar [blank] lar kam bo'lsa, ortiqchalarini o'chiramiz
                                        elseif ($blankCount < $currentCount) {
                                            $currentChildren = array_slice($currentChildren, 0, $blankCount);
                                            $set('../../children', $currentChildren);
                                        }
                                    }),

                                FileUpload::make('audio')
                                    ->label('Audio fayl')
                                    ->disk('public')
                                    ->directory('question-audios')
                                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'])
                                    ->maxSize(10240) // 10MB
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->defaultItems(1)
                            ->collapsible(),

                        // Child questions va key_words
                        Repeater::make('children')
                            ->label('Bo\'sh joylar va kalit so\'zlar (avtomatik)')
                            ->relationship('children')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nomi')
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('text')
                                    ->label('Bo\'sh joy matni')
                                    ->columnSpanFull(),

                                TextInput::make('order')
                                    ->label('Tartib')
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpanFull(),

                                Repeater::make('keyWords')
                                    ->label('Kalit so\'zlar (to\'g\'ri javoblar)')
                                    ->relationship('keyWords')
                                    ->schema([
                                        TextInput::make('word')
                                            ->label('So\'z')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->addActionLabel('Kalit so\'z qo\'shish')
                                    ->collapsible()
                                    ->defaultItems(1)
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->collapsible()
                            ->defaultItems(0)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Yangi blank'),
                    ])
                    ->visible(fn ($get) =>
                        $get('question_type_id') &&
                        QuestionType::find($get('question_type_id'))?->name === 'Fill in the Blank'
                    )
                    ->collapsible(),

                // Multiple Choice, True/False, Matching uchun options
                Section::make('Javob Variantlari (Options)')
                    ->schema([
                        Repeater::make('options')
                            ->label('Options')
                            ->relationship('options')
                            ->schema([
                                \Filament\Forms\Components\Checkbox::make('is_correct')
                                    ->label('To\'g\'ri javob')
                                    ->inline()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Agar bu checkbox belgilangan bo'lsa, boshqalarini o'chirish
                                        if ($state) {
                                            $options = $get('../../options') ?? [];
                                            foreach ($options as $key => $option) {
                                                if ($key !== $get('../../' . $get('statePath'))) {
                                                    $set("../../options.{$key}.is_correct", false);
                                                }
                                            }
                                        }
                                    })
                                    ->columnSpanFull(),

                                TextInput::make('title')
                                    ->label('Option matni (qisqa)')
                                    ->required()
                                    ->columnSpanFull(),

                                RichEditor::make('text')
                                    ->label('Option matni (to\'liq)')
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->addActionLabel('Option qo\'shish')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                $state['title'] ?? 'Yangi option'
                            )
                            ->reorderable()
                            ->defaultItems(fn ($get) =>
                                $get('question_type_id') &&
                                QuestionType::find($get('question_type_id'))?->name === 'True/False'
                                ? 2
                                : 4
                            ),
                    ])
                    ->visible(fn ($get) =>
                        $get('question_type_id') &&
                        in_array(
                            QuestionType::find($get('question_type_id'))?->name,
                            ['Multiple Choice', 'True/False', 'Matching']
                        )
                    )
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('questionType.name')
                    ->label('Turi')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('name')
                    ->label('Savol')
                    ->searchable()
                    ->limit(50)
                    ->sortable(),

                TextColumn::make('order')
                    ->label('Tartib')
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->label('Ota savol')
                    ->searchable()
                    ->limit(30)
                    ->default('-'),

                TextColumn::make('children_count')
                    ->label('Child')
                    ->counts('children')
                    ->badge(),

                TextColumn::make('options_count')
                    ->label('Options')
                    ->counts('options')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Yaratildi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('question_type_id')
                    ->label('Savol turi')
                    ->relationship('questionType', 'name'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                // Bulk actions here
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuestions::route('/'),
            'create' => CreateQuestion::route('/create'),
            'edit' => EditQuestion::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
