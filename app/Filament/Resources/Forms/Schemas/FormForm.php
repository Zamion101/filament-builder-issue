<?php

namespace App\Filament\Resources\Forms\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;

class FormForm
{
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
                                        TextInput::make('label')
                                            ->label('Label')
                                            ->required(),

                                        TextInput::make('key')
                                            ->label('Key')
                                            ->helperText('Unique key (database column name).')
                                            ->required(),

                                        Select::make('type')
                                            ->label('Field type')
//                                            ->live()
                                            ->options([
                                                'text' => 'Text input',
                                                'email' => 'Email',
                                                'phone' => 'Phone Number',
                                                'textarea' => 'Textarea',
                                                'number' => 'Number',
                                                'select' => 'Select',
                                                'checkbox' => 'Checkbox',
                                                'toggle' => 'Toggle',
                                                'date' => 'Date',
                                                'yes-no' => 'Yes/No',
                                                'address' => 'Address',
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

                                        KeyValue::make('options')
                                            ->label('Options')
                                            ->helperText('Only for select / checkbox / radio fields')
                                            ->keyLabel('Option value')
                                            ->valueLabel('Option label')
                                            ->visible(fn($get) => $get('type') === 'select'),

                                        Toggle::make('required')
                                            ->label('Required'),
                                    ])
//                                    ->preview('forms.builder.field-preview')
                                    ->columns(),
                            ])
//                            ->live()
//                            ->partiallyRenderComponentsAfterStateUpdated($builder->getFields())
                            ->addBetweenAction(function (Action $action) {
                                return $action->slideOver()->modalWidth(Width::Large);
                            })
                            ->addAction(function (Action $action) {
                                return $action->slideOver()->modalWidth(Width::Large);
                            })
                            ->editAction(function (Action $action) {
                                return $action->slideOver()->modalWidth(Width::Large);
                            })
//                            ->view('forms.builder.builder')
                    ]),
            ]);
    }
}
