<?php

namespace App\Filament\Resources\Members\RelationManagers;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Item;
use App\Models\Transaction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $relatedResource = TransactionResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('items.category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('type')
                    ->label("Статус")
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'returned' => 'Вратено',
                        'assigned' => 'Задужено',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'assigned' => 'danger',
                        'returned' => 'success',
                        default => 'gray',
                    })
                        ->icon(fn (string $state): string => match ($state) {
                            'assigned' => 'heroicon-s-x-circle', // X icon for assigned (out of stock)
                            'returned' => 'heroicon-s-check-circle', // Checkmark icon for returned (in stock)
                            default => null,
                        }),

                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assigned By')
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'assigned' => 'Assigned',
                        'returned' => 'Returned',
                    ]),
                Tables\Filters\SelectFilter::make('item_id')
                    ->label('Item')
                    ->relationship('items', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('items.category_id')
                    ->label('Category')
                    ->relationship('items.category', 'name')
                    ->searchable()
                    ->preload(),
            ])  
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('transaction_date', 'desc');
    }
}
