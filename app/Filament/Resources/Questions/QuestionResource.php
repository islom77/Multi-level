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
                                    ->label('Matn (Rich Editor)')
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

                                FileUpload::make('audio')
                                    ->label('Audio fayl')
                                    ->disk('public')
                                    ->directory('question-audios')
                                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'])
                                    ->maxSize(10240) // 10MB
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->collapsible(),

                        // Child questions va key_words
                        Repeater::make('children')
                            ->label('Bo\'sh joylar va kalit so\'zlar')
                            ->relationship('children')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nomi')
                                    ->required()
                                    ->default(fn ($get) => "Blank " . (count($get('../../children') ?? []) + 1)),

                                TextInput::make('text')
                                    ->label('Bo\'sh joy matni')
                                    ->default(fn ($get) => "Blank " . (count($get('../../children') ?? []) + 1)),

                                TextInput::make('order')
                                    ->label('Tartib')
                                    ->numeric()
                                    ->default(fn ($get) => count($get('../../children') ?? []) + 1)
                                    ->required(),

                                Repeater::make('keyWords')
                                    ->label('Kalit so\'zlar (to\'g\'ri javoblar)')
                                    ->relationship('keyWords')
                                    ->schema([
                                        TextInput::make('word')
                                            ->label('So\'z')
                                            ->required(),
                                    ])
                                    ->addActionLabel('Kalit so\'z qo\'shish')
                                    ->collapsible()
                                    ->defaultItems(1)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->defaultItems(0)
                            ->addActionLabel('Bo\'sh joy qo\'shish'),
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
                                TextInput::make('title')
                                    ->label('Option matni')
                                    ->required()
                                    ->columnSpan(2),

                                Textarea::make('text')
                                    ->label('Qo\'shimcha ma\'lumot')
                                    ->rows(2)
                                    ->columnSpan(2),

                                TextInput::make('order')
                                    ->label('Tartib (0=A, 1=B...)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(fn ($get) => count($get('../../options') ?? []))
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(5)
                            ->addActionLabel('Option qo\'shish')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                $state['title'] ?? 'Yangi option'
                            )
                            ->reorderable()
                            ->orderColumn('order')
                            ->defaultItems(0),

                        Select::make('true_option_id')
                            ->label('To\'g\'ri javob (Matching uchun)')
                            ->relationship('trueOption', 'title')
                            ->searchable()
                            ->preload()
                            ->helperText('Matching savollarda child question uchun to\'g\'ri javobni belgilang'),
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
