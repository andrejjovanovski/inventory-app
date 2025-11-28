<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AttendanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('attendance_date')
                    ->date(),
                TextEntry::make('start_time')
                    ->time(),
                TextEntry::make('end_time')
                    ->time(),
                TextEntry::make('mentor_id')
                    ->numeric(),
                TextEntry::make('group_id')
                    ->numeric(),
                TextEntry::make('created_by')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
