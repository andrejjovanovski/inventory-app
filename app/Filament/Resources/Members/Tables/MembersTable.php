<?php

namespace App\Filament\Resources\Members\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("full_name")->searchable(),
                TextColumn::make("date_of_birth")->searchable(),
                TextColumn::make("groups.name")->badge()->searchable(),
                TextColumn::make("gender")->toggleable()->searchable(),
                TextColumn::make("parent_name")->toggleable()->searchable(),
                TextColumn::make("address")->toggleable()->searchable(),
                TextColumn::make("phone_number")->toggleable()->searchable(),
                TextColumn::make("email")
                    ->label("Email address")
                    ->toggleable()
                    ->searchable(),
                TextColumn::make("passport_expiration_date")
                    ->label("Passport Expiration")
                    ->date()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make("is_passport_valid")
                    ->label("Status")
                    ->getStateUsing(function ($record) {
                        $expiration = $record->passport_expiration_date;

                        if (!$expiration) {
                            return "missing"; // missing date
                        }

                        $expiration = \Carbon\Carbon::parse($expiration);
                        $months = now()->diffInMonths($expiration, false);

                        if ($months < 0) {
                            return "expired";
                        }

                        if ($months <= 3) {
                            return "warning";
                        }

                        return "valid";
                    })
                    ->icon(
                        fn($state) => match ($state) {
                            "expired" => "heroicon-o-exclamation-triangle",
                            "warning" => "heroicon-o-exclamation-circle",
                            "valid" => "heroicon-o-check-circle",
                            "missing" => "heroicon-o-exclamation-triangle",
                            default => "heroicon-o-question-mark-circle",
                        },
                    )
                    ->color(
                        fn($state) => match ($state) {
                            "expired" => "danger",
                            "warning" => "warning",
                            "valid" => "success",
                            "missing" => "gray",
                            default => "gray",
                        },
                    )
                    ->tooltip(function ($record) {
                        $expiration = $record->passport_expiration_date;

                        if (!$expiration) {
                            return "Passport expiration date is missing.";
                        }

                        $expiration = \Carbon\Carbon::parse($expiration);
                        $months = now()->diffInMonths($expiration, false);

                        if ($months < 0) {
                            return "Passport expired on " .
                                $expiration->format("d.m.Y");
                        }

                        if ($months <= 3) {
                            return "Passport expires soon (on " .
                                $expiration->format("d.m.Y") .
                                ")";
                        }

                        return "Passport is valid (expires on " .
                            $expiration->format("d.m.Y") .
                            ")";
                    }),
                TextColumn::make("created_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("updated_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([TrashedFilter::make()])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
