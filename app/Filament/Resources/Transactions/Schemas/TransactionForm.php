<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Item;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->relationship('member', 'full_name')
                    ->required(),

                Repeater::make('transaction_items') // ⚠️ Changed name
                    // ⚠️ REMOVED ->relationship('items')
                    ->schema([
                        Select::make('item_id')
                            ->label('Item')
                            ->options(\App\Models\Item::pluck('name', 'id')->toArray())
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->disableOptionWhen(function ($value, $state, $get) {
                                $selectedItems = collect($get('../../transaction_items'))
                                    ->pluck('item_id')
                                    ->filter();
                                return $selectedItems->contains($value) && $value != $state;
                            }),

                        TextInput::make('quantity')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                    ])
                    ->columns(2)
                    ->minItems(1)
                    ->defaultItems(1)
                    ->required(),

                Select::make('type')
                    ->options([
                        'assigned' => 'Assigned',
                        'returned' => 'Returned',
                    ])
                    ->default('assigned')
                    ->required(),

                DatePicker::make('transaction_date')
                    ->required()
                    ->default(now()),

                Textarea::make('notes')
                    ->columnSpanFull(),

                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }
}