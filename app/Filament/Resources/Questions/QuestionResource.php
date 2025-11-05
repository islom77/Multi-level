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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
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
                        TextInput::make('name')
                            ->label('Savol matni')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('question_type_id')
                            ->label('Savol turi')
                            ->options(QuestionType::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('parent_id')
                            ->label('Ota savol (ierarxik savollar uchun)')
                            ->options(Question::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])
                    ->columns(2),

                Section::make('Qo\'shimcha Kontent')
                    ->description('Matn, audio va boshqa qo\'shimcha ma\'lumotlar')
                    ->schema([
                        Textarea::make('questionData.text')
                            ->label('Matn (passages, kontekst)')
                            ->rows(5)
                            ->columnSpanFull(),

                        FileUpload::make('questionData.audio')
                            ->label('Audio fayl (listening uchun)')
                            ->disk('public')
                            ->directory('question-audios')
                            ->visibility('public')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-wav'])
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->previewable(false)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Javob Variantlari')
                    ->description('Multiple choice savollar uchun javob variantlarini qo\'shing')
                    ->schema([
                        Repeater::make('options')
                            ->relationship('options')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Variant matni')
                                    ->required()
                                    ->columnSpan(3),

                                Toggle::make('correct')
                                    ->label('To\'g\'ri javob')
                                    ->default(false)
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->addActionLabel('Yangi variant qo\'shish')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                $state['title'] ?? 'Yangi variant'
                            )
                            ->reorderable()
                            ->defaultItems(0),
                    ])
                    ->collapsible(),

                Section::make('Kalit So\'zlar')
                    ->description('Writing/Speaking baholash uchun kalit so\'zlar')
                    ->schema([
                        Repeater::make('keyWords')
                            ->relationship('keyWords')
                            ->schema([
                                TextInput::make('word')
                                    ->label('Kalit so\'z')
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->addActionLabel('Kalit so\'z qo\'shish')
                            ->defaultItems(0),
                    ])
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

                TextColumn::make('name')
                    ->label('Savol')
                    ->searchable()
                    ->limit(50)
                    ->sortable(),

                TextColumn::make('questionType.name')
                    ->label('Turi')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('parent.name')
                    ->label('Ota savol')
                    ->searchable()
                    ->limit(30)
                    ->default('-'),

                TextColumn::make('options_count')
                    ->label('Variantlar')
                    ->counts('options')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Yaratildi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
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
