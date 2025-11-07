<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock - {{ $mock->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 14px;
            color: #666;
        }

        .skill-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .skill-title {
            background-color: #dbeafe;
            padding: 12px;
            margin-bottom: 15px;
            border-left: 4px solid #2563eb;
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
        }

        .skill-content {
            margin-left: 10px;
            padding: 10px;
            background-color: #f8fafc;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .part-section {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
        }

        .part-title {
            font-size: 14px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #d1fae5;
        }

        .part-info {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            font-size: 11px;
        }

        .part-info-row {
            display: table-row;
        }

        .part-info-label {
            display: table-cell;
            font-weight: bold;
            padding: 4px 8px;
            background-color: #f0fdf4;
            width: 150px;
        }

        .part-info-value {
            display: table-cell;
            padding: 4px 8px;
        }

        .question {
            margin-bottom: 20px;
            padding: 12px;
            background-color: #fff;
            border: 1px solid #e5e7eb;
            page-break-inside: avoid;
        }

        .question-header {
            font-weight: bold;
            color: #000;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .question-type {
            display: inline-block;
            background-color: #f3f4f6;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: normal;
            color: #6b7280;
            margin-left: 8px;
        }

        .question-text {
            margin: 10px 0;
            padding: 10px;
            background-color: #fff;
            border-radius: 4px;
        }

        .options {
            margin-top: 10px;
        }

        .option {
            padding: 8px;
            margin-bottom: 6px;
            background-color: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .option.correct {
            background-color: #fff;
            border-color: #000;
            font-weight: normal;
        }

        .option-label {
            font-weight: bold;
            margin-right: 8px;
            color: #374151;
        }

        .children-section {
            margin-top: 12px;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .child-item {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f9fafb;
            border-left: 2px solid #9ca3af;
        }

        .keywords {
            margin-top: 6px;
            font-size: 11px;
        }

        .keyword-badge {
            display: inline-block;
            background-color: #e0e7ff;
            padding: 2px 8px;
            margin-right: 5px;
            margin-bottom: 3px;
            border-radius: 3px;
            color: #4338ca;
        }

        .question-data {
            margin-top: 10px;
            padding: 10px;
            background-color: #fff;
            border: none;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $mock->name }}</h1>
        @if($mock->description)
            <p>{{ $mock->description }}</p>
        @endif
        <p style="margin-top: 10px; font-size: 11px;">Generated: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @foreach($mockData as $skillIndex => $skillItem)
        <div class="skill-section">
            <div class="skill-title">
                {{ $skillIndex + 1 }}. Skill: {{ $skillItem['skill']->name }}
            </div>

            @if($skillItem['pivot']->title || $skillItem['pivot']->text)
                <div class="skill-content">
                    @if($skillItem['pivot']->title)
                        <strong>Sarlavha:</strong> {{ $skillItem['pivot']->title }}<br>
                    @endif
                    @if($skillItem['pivot']->text)
                        <div style="margin-top: 8px;">
                            {!! $skillItem['pivot']->text !!}
                        </div>
                    @endif
                </div>
            @endif

            @foreach($skillItem['parts'] as $partIndex => $partItem)
                <div class="part-section">
                    <div class="part-title">
                        Part {{ $partIndex + 1 }}: {{ $partItem['part']->name }}
                    </div>

                    <div class="part-info">
                        @if($partItem['pivot']->title)
                            <div class="part-info-row">
                                <div class="part-info-label">Sarlavha:</div>
                                <div class="part-info-value">{{ $partItem['pivot']->title }}</div>
                            </div>
                        @endif
                        <div class="part-info-row">
                            <div class="part-info-label">Kutish vaqti:</div>
                            <div class="part-info-value">{{ $partItem['pivot']->waiting_time }} soniya</div>
                        </div>
                        <div class="part-info-row">
                            <div class="part-info-label">Taymer:</div>
                            <div class="part-info-value">{{ $partItem['pivot']->timer }} soniya</div>
                        </div>
                    </div>

                    @if($partItem['pivot']->text)
                        <div style="padding: 10px; background-color: #f9fafb; margin-bottom: 15px; border-radius: 4px;">
                            {!! $partItem['pivot']->text !!}
                        </div>
                    @endif

                    @foreach($partItem['questions'] as $qIndex => $mockQuestion)
                        <div class="question">
                            <div class="question-header">
                                Savol {{ $qIndex + 1 }}: {{ $mockQuestion->question->name }}
                                <span class="question-type">{{ $mockQuestion->question->questionType->name }}</span>
                                @if($mockQuestion->limit_taymer > 0)
                                    <span style="float: right; font-size: 11px; color: #dc2626;">
                                        ⏱ {{ $mockQuestion->limit_taymer }}s
                                    </span>
                                @endif
                            </div>

                            @if($mockQuestion->question->text)
                                <div class="question-text">
                                    {!! $mockQuestion->question->text !!}
                                </div>
                            @endif

                            @if($mockQuestion->question->questionData && $mockQuestion->question->questionData->count() > 0)
                                <div class="question-data">
                                    @foreach($mockQuestion->question->questionData as $qData)
                                        @php
                                            // [blank] ni pastki chiziq bilan almashtirish
                                            $textWithBlanks = preg_replace_callback(
                                                '/\[blank\]/i',
                                                function($matches) {
                                                    return '<span style="display: inline-block; min-width: 100px; border-bottom: 1px solid #000; height: 20px; margin: 0 3px;"></span>';
                                                },
                                                $qData->text
                                            );
                                        @endphp
                                        {!! $textWithBlanks !!}
                                        @if($qData->audio)
                                            <p style="margin-top: 5px; font-size: 10px; color: #6b7280;">
                                                🔊 Audio: {{ basename($qData->audio) }}
                                            </p>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            @if($mockQuestion->question->options && $mockQuestion->question->options->count() > 0)
                                <div class="options">
                                    <strong style="font-size: 11px;">Javob variantlari:</strong>
                                    @foreach($mockQuestion->question->options as $optIndex => $option)
                                        <div class="option {{ $mockQuestion->question->true_option_id == $option->id ? 'correct' : '' }}">
                                            <span class="option-label">{{ chr(65 + $optIndex) }})</span>
                                            {{ $option->title }}
                                            @if($option->text)
                                                <div style="margin-left: 20px; margin-top: 4px; font-size: 11px;">
                                                    {!! $option->text !!}
                                                </div>
                                            @endif
                                            @if($mockQuestion->question->true_option_id == $option->id)
                                                <span style="color: #000; font-weight: bold; margin-left: 10px;">(To'g'ri javob)</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($mockQuestion->question->children && $mockQuestion->question->children->count() > 0)
                                <div class="children-section">
                                    <strong style="font-size: 11px; display: block; margin-bottom: 8px;">Bo'sh joylar va kalit so'zlar:</strong>
                                    @foreach($mockQuestion->question->children as $child)
                                        <div class="child-item">
                                            <strong>{{ $child->name }}:</strong> {{ $child->text }}
                                            @if($child->keyWords && $child->keyWords->count() > 0)
                                                <div class="keywords">
                                                    <strong>Kalit so'zlar:</strong>
                                                    @foreach($child->keyWords as $keyword)
                                                        <span class="keyword-badge">{{ $keyword->word }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="footer">
        <p>Mock ID: {{ $mock->id }} | Generated with Laravel & DomPDF</p>
    </div>
</body>
</html>
