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
use Filament\Support\Enums\Alignment;
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
                                        ->label('PART')
                                        ->schema([
                                            Section::make('Qo\'shimcha')->schema([
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
                                                        $part = Part::where('name', $partNumber)->first();
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
                                                    ->maxSize(5120)
                                            ])
                                            ->columnSpanFull()
                                            ->collapsed(),

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
                                                                ->live()
                                                                ->columnSpanFull(),

                                                            TextInput::make('name')
                                                                ->label('Savol nomi')
                                                                ->required()
                                                                ->maxLength(255)
                                                                ->columnSpanFull(),

                                                            RichEditor::make('text')
                                                                ->label('Savol matni')
                                                                ->toolbarButtons([
                                                                    'bold',
                                                                    'italic',
                                                                    'underline',
                                                                    'link',
                                                                    'bulletList',
                                                                    'orderedList',
                                                                ])
                                                                ->columnSpanFull(),

                                                            // Multiple Choice uchun Options
                                                            Repeater::make('options')
                                                                ->label('Javob variantlari')
                                                                ->schema([
                                                                    \Filament\Forms\Components\Checkbox::make('is_correct')
                                                                        ->label('To\'g\'ri javob')
                                                                        ->inline()
                                                                        ->columnSpanFull(),

                                                                    TextInput::make('title')
                                                                        ->label('Variant matni (qisqa)')
                                                                        ->required()
                                                                        ->columnSpanFull(),

                                                                    RichEditor::make('text')
                                                                        ->label('Variant matni (to\'liq)')
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
                                                                ->reorderable()
                                                                ->collapsible()
                                                                ->defaultItems(fn ($get) =>
                                                                    $get('question_type_id') &&
                                                                    QuestionType::find($get('question_type_id'))?->name === 'True/False'
                                                                    ? 2
                                                                    : 4
                                                                )
                                                                ->visible(function ($get) {
                                                                    if (!$get('question_type_id')) {
                                                                        return false;
                                                                    }
                                                                    $questionType = QuestionType::find($get('question_type_id'));
                                                                    return $questionType && in_array($questionType->name, ['Multiple Choice', 'True/False']);
                                                                })
                                                                ->columnSpanFull(),

                                                            // Fill in the Blank uchun Question Data
                                                            Section::make('Fill in the Blank - Passage')
                                                                ->schema([
                                                                    RichEditor::make('question_data.text')
                                                                        ->label('Passage matni ([blank] dan foydalaning)')
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
                                                                        ->required()
                                                                        ->columnSpanFull()
                                                                        ->live(debounce: 500)
                                                                        ->afterStateUpdated(function ($state, $set, $get) {
                                                                            // [blank] larni sanash
                                                                            $blankCount = substr_count(strtolower($state ?? ''), '[blank]');

                                                                            // Hozirgi child questions
                                                                            $currentChildren = $get('child_questions') ?? [];
                                                                            $currentCount = count($currentChildren);

                                                                            // Agar [blank] lar ko'p bo'lsa, yangilarini qo'shamiz
                                                                            if ($blankCount > $currentCount) {
                                                                                for ($i = $currentCount; $i < $blankCount; $i++) {
                                                                                    $currentChildren[] = [
                                                                                        'name' => 'Blank ' . ($i + 1),
                                                                                        'keywords' => [
                                                                                            ['word' => '']
                                                                                        ]
                                                                                    ];
                                                                                }
                                                                                $set('child_questions', $currentChildren);
                                                                            }
                                                                            // Agar [blank] lar kam bo'lsa, ortiqchalarini o'chiramiz
                                                                            elseif ($blankCount < $currentCount) {
                                                                                $currentChildren = array_slice($currentChildren, 0, $blankCount);
                                                                                $set('child_questions', $currentChildren);
                                                                            }
                                                                        }),

                                                                    FileUpload::make('question_data.audio')
                                                                        ->label('Passage Audio')
                                                                        ->disk('public')
                                                                        ->directory('question-data')
                                                                        ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'])
                                                                        ->maxSize(10240)
                                                                        ->columnSpanFull(),

                                                                    Repeater::make('child_questions')
                                                                        ->label('Blanklar uchun savollar')
                                                                        ->schema([
                                                                            TextInput::make('name')
                                                                                ->label('Savol nomi')
                                                                                ->required()
                                                                                ->maxLength(255)
                                                                                ->columnSpanFull(),

                                                                            Repeater::make('keywords')
                                                                                ->label('Kalit so\'zlar')
                                                                                ->schema([
                                                                                    TextInput::make('word')
                                                                                        ->label('Kalit so\'z')
                                                                                        ->required()
                                                                                        ->maxLength(255)
                                                                                        ->columnSpanFull(),
                                                                                ])
                                                                                ->columns(1)
                                                                                ->collapsible()
                                                                                ->defaultItems(1)
                                                                                ->columnSpanFull()
                                                                                ->addActionLabel('Kalit so\'z qo\'shish'),
                                                                        ])
                                                                        ->columns(1)
                                                                        ->collapsible()
                                                                        ->columnSpanFull()
                                                                        ->addable(false)
                                                                        ->deletable(false)
                                                                        ->reorderable(false)
                                                                        ->defaultItems(0)
                                                                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Yangi blank'),
                                                                ])
                                                                ->visible(function ($get) {
                                                                    if (!$get('question_type_id')) {
                                                                        return false;
                                                                    }
                                                                    $questionType = QuestionType::find($get('question_type_id'));
                                                                    return $questionType && $questionType->name === 'Fill in the Blank';
                                                                })
                                                                ->collapsed()
                                                                ->columnSpanFull(),
                                                        ])
                                                        ->createOptionUsing(function (array $data): int {
                                                            // Question yaratish
                                                            $question = Question::create([
                                                                'question_type_id' => $data['question_type_id'],
                                                                'name' => $data['name'],
                                                                'text' => $data['text'] ?? null,
                                                            ]);

                                                            // Multiple Choice va True/False uchun Options yaratish
                                                            $questionType = QuestionType::find($data['question_type_id']);
                                                            if ($questionType && in_array($questionType->name, ['Multiple Choice', 'True/False'])) {
                                                                $trueOptionId = null;

                                                                if (isset($data['options']) && count($data['options']) > 0) {
                                                                    foreach ($data['options'] as $index => $optionData) {
                                                                        $option = $question->options()->create([
                                                                            'title' => $optionData['title'],
                                                                            'text' => $optionData['text'] ?? null,
                                                                            'order' => $index, // Index avtomatik 0, 1, 2, 3...
                                                                        ]);

                                                                        // To'g'ri javob checkbox ni tekshirish
                                                                        if (isset($optionData['is_correct']) && $optionData['is_correct']) {
                                                                            $trueOptionId = $option->id;
                                                                        }
                                                                    }
                                                                }

                                                                // True option ID ni saqlash
                                                                if ($trueOptionId) {
                                                                    $question->update(['true_option_id' => $trueOptionId]);
                                                                }
                                                            }

                                                            // Fill in the Blank uchun QuestionData va Child Questions yaratish
                                                            if ($questionType && $questionType->name === 'Fill in the Blank') {
                                                                // QuestionData yaratish
                                                                if (isset($data['question_data'])) {
                                                                    $questionData = $question->questionData()->create([
                                                                        'text' => $data['question_data']['text'] ?? null,
                                                                        'audio' => $data['question_data']['audio'] ?? null,
                                                                    ]);

                                                                    // Child questions yaratish
                                                                    if (isset($data['child_questions']) && count($data['child_questions']) > 0) {
                                                                        foreach ($data['child_questions'] as $childData) {
                                                                            // Child question yaratish
                                                                            $childQuestion = Question::create([
                                                                                'question_type_id' => $data['question_type_id'],
                                                                                'name' => $childData['name'],
                                                                                'parent_id' => $question->id,
                                                                            ]);

                                                                            // Kalit so'zlarni yaratish
                                                                            if (isset($childData['keywords']) && count($childData['keywords']) > 0) {
                                                                                foreach ($childData['keywords'] as $keywordData) {
                                                                                    \App\Models\KeyWord::create([
                                                                                        'question_data_id' => $questionData->id,
                                                                                        'question_id' => $childQuestion->id,
                                                                                        'word' => $keywordData['word'],
                                                                                    ]);
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }

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
                                            // part_id bo'yicha nom olish
                                            if (isset($state['part_id'])) {
                                                $part = Part::find($state['part_id']);
                                                if ($part) {
                                                    // Agar title to'ldirilgan bo'lsa, "Part name - title" formatida
                                                    if (!empty($state['title'])) {
                                                        return $part->name . ' - ' . $state['title'];
                                                    }
                                                    // Aks holda faqat part name
                                                    return $part->name;
                                                }
                                            }

                                            return 'Yangi part';
                                        })
                                        ->defaultItems(0)
                                        ->addActionLabel('+ Part qo\'shish')
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
