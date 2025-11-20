<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->relationship('member', 'id')
                    ->required(),

                // Repeater for items (pivot relationship)
                Repeater::make('items')
                    ->label('Items')
                    ->relationship('items')
                    ->schema([
                        Select::make('item_id')
                            ->label('Item')
                            ->relationship('item', 'name')
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                    ])
                    ->columns(2)
                    ->required(),

                Select::make('type')
                    ->options([
                        'assigned' => 'Assigned',
                        'returned' => 'Returned',
                    ])
                    ->default('assigned')
                    ->required(),

                DatePicker::make('transaction_date')
                    ->required(),

                Textarea::make('notes')
                    ->columnSpanFull(),

                Select::make('user_id')
                    ->relationship('user', 'name'),
            ]);
    }
}
