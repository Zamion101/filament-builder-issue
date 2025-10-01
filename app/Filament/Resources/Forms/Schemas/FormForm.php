<?php

namespace App\Filament\Resources\Forms\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FormForm
{


    public static function fieldOptions(Get $get): array
    {
        $type = $get('type');
        $options = [];

        if ($type === 'select') {
            $options[] = KeyValue::make('options')
                ->key('options')
                ->statePath('options')
                ->hiddenLabel()
                ->keyLabel('Option value')
                ->valueLabel('Option label')
                ->columnSpan(2);
            return $options;
        }
        if ($type === 'yes-no') {
            $options[] = TextInput::make('input_yes_label')
                ->label('"Yes" Label')
                ->default('Yes')
                ->columnStart(1)
                ->columnSpan(1);
            $options[] = TextInput::make('input_no_label')
                ->label('"No" Label')
                ->default('No')
                ->columnSpan(1);
            return $options;
        }
        if ($type === 'number') {
            $options[] = TextInput::make('min')->label('Minimum')->numeric();
            $options[] = TextInput::make('max')->label('Maximum')->numeric();
            return $options;
        }
        if ($type === 'text' || $type === 'textarea') {
            $options[] = TextInput::make('min_length')->label('Minimum Length')->numeric();
            $options[] = TextInput::make('max_length')->label('Maximum Length')->numeric();
            return $options;
        }
        if ($type === 'repeater') {
            $options[] = TextInput::make('min_items')->label('Minimum Items')->numeric();
            $options[] = TextInput::make('max_items')->label('Maximum Items')->numeric();
            $options[] = Builder::make('schema')
                ->label('Form Fields')
                ->columnSpanFull()
                // show a preview instead of the full sub‑form
                ->blockPreviews(areInteractive: true)
                ->addActionLabel('Add field')
                ->blockNumbers(false)
                ->addActionAlignment(Alignment::Start)
//                ->live()
                ->addBetweenAction(function (Action $action) {
                    return $action->slideOver()->modalWidth(Width::TwoExtraLarge);
                })
                ->addAction(function (Action $action) {
                    return $action->slideOver()->modalWidth(Width::TwoExtraLarge);
                })
                ->editAction(function (Action $action) {
                    return $action->slideOver()->modalWidth(Width::TwoExtraLarge);
                })
                ->view('forms.builder.builder')
                ->blocks([
                    Block::make('field')->schema([

                        Grid::make()
                            ->schema([
                                TextInput::make('label')->label('Label')->required(),
                                TextInput::make('key')->label('Key')->required(),
                                Select::make('type')
                                    ->label('Field type')
                                    ->live()
                                    ->native(false)
                                    ->options([
                                        'text' => 'Text input',
                                        'email' => 'Email',
                                        'phone' => 'Phone Number',
                                        'textarea' => 'Textarea',
                                        'number' => 'Number',
                                        'select' => 'Select',
                                        'country' => 'Country',
                                        'checkbox' => 'Checkbox',
                                        'toggle' => 'Toggle',
                                        'date' => 'Date',
                                        'yes-no' => 'Yes/No',
                                        'address' => 'Address',
                                    ])
                                    ->required(),
                                Grid::make()
                                    ->schema(fn() => self::fieldOptions($get))->columnSpan(2)
                                    ->visible(
                                        fn() => in_array(
                                            $get('type'),
                                            ['text', 'textarea', 'number', 'select', 'yes-no']
                                        )
                                    ),
                                Toggle::make('required')
                                    ->label('Required')
                                    ->columnStart(1)
                            ])
                    ])
                        ->label('Field')
                        ->preview('forms.builder.field-preview')
                        ->columns(),
                ]);
            return $options;
        }

        return $options;
    }

    protected static function extractFields(array $blocks): array
    {
        $fields = [];

        foreach ($blocks as $block) {
            $data = $block['data'] ?? [];
            $key = $data['key'] ?? null;
            $label = $data['label'] ?? null;

            if ($key) {
                $name = $label ?: Str::headline($key);
                $fields[$key]['name'] = $name;
                if (strlen($name) > 16) {
                    $fields[$key]['name'] = Str::substr($name, 0, 16) . '...';
                }
                $fields[$key]['type'] = $data['type'];
            }
        }

        return $fields;
    }

    public static function fieldRules(string $label, string $key): Grid
    {
        $extractedFields = [];
        return Grid::make(3)
            ->schema([
                Fieldset::make("$label when")
                    ->schema([
                        Hidden::make("rule.$key.source_type"),
                        Select::make("rule.$key.source")
                            ->live()
                            ->partiallyRenderComponentsAfterStateUpdated([
                                "rule.$key.source_type",
                                "rule.$key.action",
                                "rule.$key.value",
                                "rule.$key.value_2",
                            ])
                            ->options(function ($livewire) use($extractedFields) {
                                if(empty($extractedFields)){
                                    $extractedFields = self::extractFields($livewire->data['schema']);
                                }
                                return collect($extractedFields)
                                    ->filter(fn($data) => filled($data['name']))
                                    ->map(fn($data) => $data['name'])
                                    ->toArray();
                            })
                            ->afterStateUpdated(function ($livewire, Get $get, Set $set, $state) use ($key, $extractedFields) {
                                if(empty($extractedFields)){
                                    $extractedFields = self::extractFields($livewire->data['schema']);
                                }
//                                Log::info(json_encode($extractedFields, JSON_THROW_ON_ERROR));
                                if (array_key_exists($state, $extractedFields)) {
                                    $field = $extractedFields[$state];
                                    $set("rule.$key.source_type", $field['type']);
                                }
                            }),
                        Select::make("rule.$key.action")
                            ->live()
                            ->partiallyRenderComponentsAfterStateUpdated([
                                "rule.$key.value",
                                "rule.$key.value_2",
                            ])
                            ->options(function (Get $get) use ($key) {
                                return match ($get("rule.$key.source_type")) {
                                    'text', 'textarea' => [
                                        'eq' => "Equals (=)",
                                        'neq' => "Not Equals (!=)",
                                        'contains' => "Contains",
                                        'startsWith' => "Starts with",
                                        'endsWith' => "Ends with",
                                        'filled' => "Filled",
                                        'empty' => "Empty",
                                    ],
                                    'number' => [
                                        'eq' => "Equals (=)",
                                        'neq' => "Not Equals (!=)",
                                        'lt' => "Less than (<)",
                                        'gt' => "Greater than (>)",
                                        'lte' => "Less than or equal (<=)",
                                        'gte' => "Greater than or equal (>=)",
                                        'between' => "Between",
                                    ],
                                    'yes-no' => [
                                        'true' => "Selected Yes",
                                        'false' => "Selected No",
                                    ],
                                    default => [
                                        'filled' => "Filled",
                                        'empty' => "Empty",
                                    ]
                                };
                            })
                            ->required(fn(Get $get) => filled($get("rule.$key.source"))),
                        TextInput::make("rule.$key.value")
                            ->label(fn(Get $get) => $get("rule.$key.action") === 'between' ? 'First Value' : 'Value')
                            ->visible(
                                fn($get) => !in_array($get("rule.$key.action"), ['filled', 'empty']) &&
                                    $get("rule.$key.source_type") !== 'yes-no'
                            )
                            ->required(
                                fn(Get $get) => filled($get("rule.$key.source")) &&
                                    !in_array($get("rule.$key.action"), ['filled', 'empty']) &&
                                    $get("rule.$key.source_type") !== 'yes-no'
                            )
                            ->numeric(fn($get) => in_array($get("rule.$key.action"), ['between', 'lte', 'gt', 'lt', 'gte']))
                            ->columnSpan(fn(Get $get) => $get("rule.$key.action") === 'between' ? 1 : 2),
                        TextInput::make("rule.$key.value_2")
                            ->label(fn(Get $get) => $get("rule.$key.action") === 'between' ? 'Second Value' : 'Value')
                            ->visible(fn($get) => $get("rule.$key.action") === 'between')
                            ->numeric(fn($get) => in_array($get("rule.$key.action"), ['between', 'lte', 'gt', 'lt', 'gte']))
                            ->required(fn(Get $get) => filled($get("rule.$key.source")) && $get("rule.$key.action") === 'between')
                    ])
                    ->columns(4)
                    ->columnSpanFull()
            ]);
    }


    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns()
            ->components([
                Section::make('Builder')
                    ->schema([
                        // Builder field to define form fields
                        Builder::make('schema')
                            ->label('Form Fields')
                            ->columnSpan('full')
                            // show a preview instead of the full sub‑form
                            ->blockPreviews(areInteractive: true)
                            ->addActionLabel('Add field')
                            ->blockNumbers(false)
                            ->addActionAlignment(Alignment::Start)
                            ->blocks([
                                Block::make('field')
                                    ->label('Field')
                                    ->schema([
                                        Tabs::make()->tabs([
                                            Tabs\Tab::make('Settings')
                                                ->schema([
                                                    Grid::make()
                                                        ->schema([
                                                            TextInput::make('label')
                                                                ->label('Label')
                                                                ->required(),

                                                            TextInput::make('key')
                                                                ->label('Key')
                                                                ->helperText('Unique key.')
                                                                ->required(),

                                                            Select::make('type')
                                                                ->label('Field type')
                                                                ->live()
                                                                ->native(false)
                                                                ->options([
                                                                    'text' => 'Text input',
                                                                    'email' => 'Email',
                                                                    'phone' => 'Phone Number',
                                                                    'textarea' => 'Textarea',
                                                                    'number' => 'Number',
                                                                    'select' => 'Select',
                                                                    'country' => 'Country',
                                                                    'checkbox' => 'Checkbox',
                                                                    'toggle' => 'Toggle',
                                                                    'date' => 'Date',
                                                                    'yes-no' => 'Yes/No',
                                                                    'address' => 'Address',
                                                                    'repeater' => 'Repeater',
                                                                ])
                                                                ->required(),
                                                            ToggleButtons::make('column_span')
                                                                ->label('Column Span')
                                                                ->options([
                                                                    1 => 1,
                                                                    2 => 2,
                                                                    3 => 3,
                                                                    4 => 4,
                                                                    5 => 5,
                                                                    6 => 6,
                                                                ])
                                                                ->grouped(),
                                                            TextInput::make('placeholder')
                                                                ->label('Placeholder')
                                                                ->visible(fn($get) => !in_array($get('type'), ['checkbox', 'toggle'])),
                                                            Grid::make()
                                                                ->schema(fn(Get $get) => self::fieldOptions($get))->columnSpan(2)
                                                                ->visible(
                                                                    fn($get) => in_array(
                                                                        $get('type'),
                                                                        ['text', 'textarea', 'number', 'select', 'yes-no', 'repeater']
                                                                    )
                                                                ),
                                                            Toggle::make('required')
                                                                ->label('Required')
                                                                ->columnStart(1),
                                                        ])
                                                ]),
                                            Tabs\Tab::make('Rules')
                                                ->schema([
                                                    //TODO: uncommenting this multiply exponentially the amount of getClone() called
//                                                    self::fieldRules('Visible', 'visible'),
                                                ])
                                        ])
                                    ])
                                    ->preview('forms.builder.field-preview')
                                    ->columns(),
                            ])
//                            ->live()
                            ->addBetweenAction(function (Action $action) {
                                return $action->slideOver()->modalWidth(Width::TwoExtraLarge);
                            })
                            ->addAction(function (Action $action) {
                                return $action->slideOver()->modalWidth(Width::TwoExtraLarge);
                            })
                            ->editAction(function (Action $action) {
                                return $action->slideOver()->modalWidth(Width::TwoExtraLarge);
                            })
                            ->view('forms.builder.builder')
                    ]),
                Section::make('Settings')
                    ->schema([
                        TextInput::make('meta.name')
                            ->columnSpan(3)
                            ->required(),
                        TextInput::make('meta.title')
                            ->columnSpan(3)
                            ->required(),
                        RichEditor::make('meta.description')
                            ->columnSpanFull()
                    ])
                    ->columns(12),
            ]);
    }
}
