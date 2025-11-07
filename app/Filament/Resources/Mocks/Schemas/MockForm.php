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
use App\Models\QuestionType;

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
                                    Section::make('Qo\'shimcha')->schema([
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
                                    ])
                                    ->columnSpanFull()
                                    ->collapsed(),
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
                                                ->columnSpanFull()
                                                ->live()
                                                ->default(function ($get) {
                                                    // Hozirgi skill'dagi part'lar sonini sanash
                                                    $parts = $get('../../parts') ?? [];
                                                    $partNumber = count($parts);

                                                    // Database'dan "Part {$partNumber}" nomli part'ni topish
                                                    $part = Part::where('name', 'Part ' . $partNumber)->first();
                                                    return $part?->id;
                                                }),

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
                                                ->columnSpanFull()
                                                ->default(function ($get) {
                                                    // Hozirgi skill'dagi part'lar sonini sanash
                                                    $parts = $get('../../parts') ?? [];
                                                    return 'Part ' . count($parts);
                                                }),

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
                                                        ->columnSpanFull()
                                                        ->createOptionForm([
                                                            Select::make('question_type_id')
                                                                ->label('Savol turi')
                                                                ->options(QuestionType::pluck('name', 'id'))
                                                                ->required()
                                                                ->searchable()
                                                                ->preload()
                                                                ->columnSpanFull(),

                                                            TextInput::make('name')
                                                                ->label('Savol nomi')
                                                                ->required()
                                                                ->maxLength(255)
                                                                ->columnSpanFull(),

                                                            Textarea::make('text')
                                                                ->label('Savol matni')
                                                                ->rows(3)
                                                                ->maxLength(65535)
                                                                ->columnSpanFull(),
                                                        ])
                                                        ->createOptionUsing(function (array $data): int {
                                                            $question = Question::create($data);
                                                            return $question->id;
                                                        }),

                                                    TextInput::make('limit_taymer')
                                                        ->label('Vaqt limiti (soniya)')
                                                        ->numeric()
                                                        ->suffix('s')
                                                        ->default(0)
                                                        ->minValue(0)
                                                        ->columnSpanFull(),
                                                ])
                                                ->collapsed()
                                                ->cloneable()
                                                ->reorderableWithButtons()
                                                ->addActionLabel('Savol qo\'shish')
                                                ->itemLabel(function (array $state, Repeater $component): ?string {
                                                    $statePath = $component->getStatePath();
                                                    $items = data_get($component->getLivewire(), $statePath) ?? [];

                                                    // Hozirgi item'ning indeksini topish
                                                    $index = 1;
                                                    foreach ($items as $i => $item) {
                                                        if ($item == $state) {
                                                            break;
                                                        }
                                                    }

                                                    return 'Savol ' . $index;
                                                })
                                                ->defaultItems(0)
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(2)
                                        ->collapsible()
                                        ->collapsed()
                                        ->itemLabel(function (array $state): ?string {
                                            // Agar title to'ldirilgan bo'lsa uni ko'rsatish
                                            if (!empty($state['title'])) {
                                                return $state['title'];
                                            }

                                            // Aks holda part_id bo'yicha nom olish
                                            if (isset($state['part_id'])) {
                                                $part = Part::find($state['part_id']);
                                                return $part ? $part->name : 'Yangi part';
                                            }

                                            return 'Yangi part';
                                        })
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
                ->columnSpanFull(),
            ]);
    }
}
