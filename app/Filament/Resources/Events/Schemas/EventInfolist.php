<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextEntry::make('title'),
                    TextEntry::make('description')
                        ->placeholder('-')
                        ->columnSpanFull(),
                    TextEntry::make('start_date')
                        ->dateTime(),
                    TextEntry::make('end_date')
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('location')
                        ->placeholder('-'),
                    TextEntry::make('max_attendees')
                        ->numeric()
                        ->placeholder('-'),
                    TextEntry::make('created_at')
                        ->dateTime()
                        ->placeholder('-'),
                ])
                    ->columnSpanFull()
                    ->columns(2),
            ]);
    }
}
