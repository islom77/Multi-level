<?php

namespace App\Filament\Resources\Mocks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use App\Models\Skill;
use App\Models\Part;
use App\Models\Question;

class MockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                ->schema([
                    Section::make('Mock Ma\'lumotlari')
                        ->schema([
                            TextInput::make('name')
                                ->label('Mock Test Nomi')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('description')
                                ->label('Ta\'rif')
                                ->maxLength(255),
                        ])
                        ->columns(2),

                    Section::make('Skills va Parts')
                        ->description('Mock uchun skill\'larni va har bir skill uchun part\'larni qo\'shing.')
                        ->schema([
                            Repeater::make('skills')
                                ->relationship('skills')
                                ->schema([
                                    Select::make('id')
                                        ->label('Skill')
                                        ->options(Skill::pluck('name', 'id'))
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->live()
                                        ->columnSpanFull(),

                                    TextInput::make('pivot.title')
                                        ->label('Skill Sarlavhasi')
                                        ->maxLength(255),

                                    RichEditor::make('pivot.text')
                                        ->label('Skill Matni')
                                        ->toolbarButtons([
                                            'bold',
                                            'italic',
                                            'underline',
                                            'link',
                                            'bulletList',
                                            'orderedList',
                                        ])
                                        ->columnSpanFull(),

                                    FileUpload::make('pivot.audio')
                                        ->label('Skill Audio')
                                        ->disk('public')
                                        ->directory('mock-skills')
                                        ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'])
                                        ->maxSize(10240),

                                    FileUpload::make('pivot.photo')
                                        ->label('Skill Rasm')
                                        ->disk('public')
                                        ->directory('mock-skills')
                                        ->image()
                                        ->maxSize(5120),

                                    // Skill ichida Parts
                                    Repeater::make('parts')
                                        ->label('Part\'lar')
                                        ->schema([
                                            Select::make('part_id')
                                                ->label('Part')
                                                ->options(Part::pluck('name', 'id'))
                                                ->required()
                                                ->searchable()
                                                ->preload()
                                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                ->columnSpanFull(),

                                            TextInput::make('waiting_time')
                                                ->label('Kutish vaqti (soniya)')
                                                ->numeric()
                                                ->suffix('s')
                                                ->default(0)
                                                ->minValue(0),

                                            TextInput::make('timer')
                                                ->label('Timer (soniya)')
                                                ->numeric()
                                                ->suffix('s')
                                                ->default(0)
                                                ->minValue(0),

                                            TextInput::make('title')
                                                ->label('Part Sarlavhasi')
                                                ->maxLength(255)
                                                ->columnSpanFull(),

                                            RichEditor::make('text')
                                                ->label('Part Matni (Passage)')
                                                ->toolbarButtons([
                                                    'bold',
                                                    'italic',
                                                    'underline',
                                                    'link',
                                                    'bulletList',
                                                    'orderedList',
                                                    'h2',
                                                    'h3',
                                                ])
                                                ->columnSpanFull(),

                                            FileUpload::make('audio')
                                                ->label('Part Audio')
                                                ->disk('public')
                                                ->directory('mock-parts')
                                                ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'])
                                                ->maxSize(10240),

                                            FileUpload::make('photo')
                                                ->label('Part Rasm')
                                                ->disk('public')
                                                ->directory('mock-parts')
                                                ->image()
                                                ->maxSize(5120),

                                            // Part ichida Questions
                                            Repeater::make('questions')
                                                ->label('Savollar')
                                                ->schema([
                                                    Select::make('question_id')
                                                        ->label('Savol')
                                                        ->options(Question::with('questionType')
                                                            ->get()
                                                            ->mapWithKeys(function ($question) {
                                                                return [$question->id => $question->name . ' (' . ($question->questionType->name ?? 'N/A') . ')'];
                                                            }))
                                                        ->required()
                                                        ->searchable()
                                                        ->preload()
                                                        ->columnSpan(2),

                                                    TextInput::make('limit_taymer')
                                                        ->label('Vaqt limiti (soniya)')
                                                        ->numeric()
                                                        ->suffix('s')
                                                        ->default(0)
                                                        ->minValue(0)
                                                        ->columnSpan(1),
                                                ])
                                                ->columns(3)
                                                ->collapsible()
                                                ->collapsed()
                                                ->itemLabel(fn (array $state): ?string =>
                                                    isset($state['question_id'])
                                                        ? Question::find($state['question_id'])?->name ?? 'Yangi savol'
                                                        : 'Yangi savol'
                                                )
                                                ->defaultItems(0)
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(2)
                                        ->collapsible()
                                        ->collapsed()
                                        ->itemLabel(fn (array $state): ?string =>
                                            isset($state['part_id'])
                                                ? Part::find($state['part_id'])?->name ?? 'Yangi part'
                                                : 'Yangi part'
                                        )
                                        ->defaultItems(0)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->addActionLabel('Skill qo\'shish')
                                ->collapsible()
                                ->collapsed()
                                ->itemLabel(fn (array $state): ?string =>
                                    isset($state['id'])
                                        ? Skill::find($state['id'])?->name ?? 'Yangi skill'
                                        : 'Yangi skill'
                                )
                                ->reorderable()
                                ->default(function () {
                                    return Skill::all()->map(function ($skill) {
                                        return ['id' => $skill->id];
                                    })->toArray();
                                }),
                        ])
                        ->collapsible(),
                ])
            ]);
    }
}
