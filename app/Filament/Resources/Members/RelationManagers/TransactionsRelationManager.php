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

                Tables\Columns\TextColumn::make('item.category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'assigned' => 'success',
                        'returned' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

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
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('item.category_id')
                    ->label('Category')
                    ->relationship('item.category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        // Items repeater
                        Repeater::make('items')
                            ->label('Items')
                            ->schema([
                                Select::make('item_id')
                                    ->label('Item')
                                    ->options(Item::query()->pluck('name', 'id'))
                                    ->getSearchResultsUsing(fn (string $search): array => Item::query()
                                        ->where('name', 'like', "%{$search}%")
                                        ->limit(50)
                                        ->pluck('name', 'id')
                                        ->toArray()
                                    )
                                    ->getOptionLabelUsing(fn ($value): ?string => Item::find($value)?->name
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required(),
                                        Select::make('category_id')
                                            ->relationship('category', 'name')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return Item::create($data)->id;
                                    }),

                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1),
                            ])
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('Add Item')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['item_id']
                                    ? Item::find($state['item_id'])?->name.' (Qty: '.($state['quantity'] ?? 1).')'
                                    : 'New Item'
                            ),

                        Select::make('type')
                            ->options([
                                'assigned' => 'Assigned',
                                'returned' => 'Returned',
                            ])
                            ->required()
                            ->default('assigned'),

                        DatePicker::make('transaction_date')
                            ->required()
                            ->default(now())
                            ->displayFormat('Y-m-d'),

                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),

                        Select::make('user_id')
                            ->label('Assigned By')
                            ->relationship('user', 'name')
                            ->default(fn () => Auth::id())
                            ->searchable()
                            ->preload(),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['member_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    })
                    ->using(function (array $data): Model {
                        // Handle multiple items from repeater
                        $items = $data['items'] ?? [];
                        $memberId = $this->getOwnerRecord()->id;
                        $type = $data['type'];
                        $transactionDate = $data['transaction_date'];
                        $notes = $data['notes'] ?? null;
                        $userId = $data['user_id'] ?? Auth::id();

                        $transactions = [];

                        DB::transaction(function () use ($items, $memberId, $type, $transactionDate, $notes, $userId, &$transactions) {
                            foreach ($items as $item) {
                                $transactions[] = Transaction::create([
                                    'member_id' => $memberId,
                                    'item_id' => $item['item_id'],
                                    'quantity' => $item['quantity'] ?? 1,
                                    'type' => $type,
                                    'transaction_date' => $transactionDate,
                                    'notes' => $notes,
                                    'user_id' => $userId,
                                ]);
                            }
                        });

                        return $transactions[0] ?? new Transaction;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('transaction_date', 'desc');
    }
}
