<?php

namespace App\Filament\Resources\Mocks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use App\Models\Skill;
use App\Models\Part;
use App\Models\Question;

class MockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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

                Section::make('Savollar')
                    ->description('Mock testga savollar qo\'shing. Har bir savol uchun skill, part va vaqt belgilang.')
                    ->schema([
                        Repeater::make('mockQuestions')
                            ->relationship('mockQuestions')
                            ->schema([
                                Select::make('skill_id')
                                    ->label('Skill')
                                    ->options(Skill::pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live(),

                                Select::make('part_id')
                                    ->label('Part')
                                    ->options(Part::pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Select::make('question_id')
                                    ->label('Savol')
                                    ->options(function () {
                                        return Question::with('questionType')
                                            ->get()
                                            ->mapWithKeys(function ($question) {
                                                return [$question->id => $question->name . ' (' . ($question->questionType->name ?? 'N/A') . ')'];
                                            });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),

                                TextInput::make('limit_taymer')
                                    ->label('Vaqt limiti (soniyalarda)')
                                    ->numeric()
                                    ->suffix('soniya')
                                    ->default(0)
                                    ->minValue(0),
                            ])
                            ->columns(4)
                            ->addActionLabel('Yangi savol qo\'shish')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['question_id'])
                                    ? Question::find($state['question_id'])?->name ?? 'Yangi savol'
                                    : 'Yangi savol'
                            )
                            ->reorderable()
                            ->cloneable()
                            ->defaultItems(0),
                    ]),
            ]);
    }
}
