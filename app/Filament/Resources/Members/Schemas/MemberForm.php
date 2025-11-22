<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->required(),

                DatePicker::make('date_of_birth'),

                Select::make('gender')
                    ->options([
                        'male' => 'Машко',
                        'female' => 'Женско',
                        'other' => 'Друго',
                    ]),

                TextInput::make('parent_name'),

                TextInput::make('national_id'),

                TextInput::make('passport_number'),

                DatePicker::make('passport_expiration_date'),

                TextInput::make('address'),

                TextInput::make('phone_number')
                    ->tel(),

                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),

                Select::make('groups')
                    ->relationship('groups', 'name')
                    ->preload()
                    ->multiple()
                    ->searchable()
                    ->helperText('Изберете група на која припаѓа членот'),
            ]);
    }
}
