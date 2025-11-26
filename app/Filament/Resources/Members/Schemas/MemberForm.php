<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Auth as Auth;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make("full_name")->required(),

            DatePicker::make("date_of_birth")->required(),

            Select::make("gender")
                ->options([
                    "male" => "Машко",
                    "female" => "Женско",
                    "other" => "Друго",
                ])
                ->required(),

            TextInput::make("parent_name")->required(),

            TextInput::make("embg")->required(),

            TextInput::make("national_id"),

            TextInput::make("passport_number"),

            DatePicker::make("passport_expiration_date"),

            TextInput::make("address"),

            TextInput::make("phone_number")->tel(),

            TextInput::make("email")
                ->label("Email address")
                ->email()
                ->required(),

            Hidden::make("created_by")->default(fn() => Auth::id()),

            Select::make("groups")
                ->required()
                ->relationship("groups", "name")
                ->preload()
                ->multiple()
                ->searchable()
                ->helperText("Изберете група на која припаѓа членот"),
        ]);
    }
}
