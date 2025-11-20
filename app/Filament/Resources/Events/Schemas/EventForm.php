<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextInput::make('title')
                        ->required()
                        ->columnSpan(2),
                    Textarea::make('description')
                        ->columnSpanFull(),
                    DateTimePicker::make('start_date')
                        ->required(),
                    DateTimePicker::make('end_date'),
                    TextInput::make('location'),
                    TextInput::make('max_attendees')
                        ->numeric(),
                ])->columns(2)->columnSpanFull(),
            ]);
    }
}
