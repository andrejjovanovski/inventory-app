<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Models\Event; // <-- Essential import for type hinting and clarity
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    /**
     * Enforces the 'max_attendees' limit from the parent Event record.
     */
    protected function canAttach(): bool
    {
        /** @var \App\Models\Event $event */
        $event = $this->getOwnerRecord();

        // 2. Get the maximum attendees limit
        $maxAttendees = $event->max_attendees;

        // If the limit is not set (null or 0), allow attaching.
        if (empty($maxAttendees) || $maxAttendees === 0) {
            return true;
        }

        // 3. Count the currently attached members
        $currentAttendeesCount = $event->members()->count();

        // 4. Return true (allow) if the current count is less than the limit, otherwise return false (deny).
        return $currentAttendeesCount < $maxAttendees;
    }

    public function table(Table $table): Table
    {
        // Calculate dynamic values for the Attach action's label and tooltip
        /** @var \App\Models\Event $event */
        $event = $this->getOwnerRecord();
        $maxAttendees = $event->max_attendees;

        // CRITICAL: Force the relation to be reloaded before counting to get the latest state
        $event->load('members');

        $currentAttendeesCount = $event->members()->count();

        $canAttach = $currentAttendeesCount < $maxAttendees;
        $remainingSlots = max(0, $maxAttendees - $currentAttendeesCount); // Ensure slots are not negative
        $maxAttendeesLabel = empty($maxAttendees) ? 'No Limit' : $maxAttendees.' Total';

        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                TextColumn::make('full_name')
                    ->searchable(),
                TextColumn::make('parent_name')
                    ->label('Parent Name'),
                TextColumn::make('date_of_birth')
                    ->label('Date of Birth')
                    ->date(),
                TextColumn::make('email')
                    ->label('Email'),
                TextColumn::make('phone_number')
                    ->label('Phone Number'),
            ])
            ->headerActions([
                AttachAction::make()
                    // Explicitly disable the action if $canAttach is false.
                    ->disabled(fn () => ! $canAttach)
                    // Custom label showing remaining slots
                    ->label('Attach Member ('.$remainingSlots.' Slots Left)')
                    // Custom tooltip to explain why the button is disabled (if it is)
                    ->tooltip(fn () => $canAttach ?
                        'Attach new members to this event (Max: '.$maxAttendeesLabel.')' :
                        'Maximum attendees limit ('.$maxAttendeesLabel.') has been reached. Detach members to free up space.')
                    // FIX: Dispatch a custom event to trigger a full component refresh
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshComponent');
                    })
                    ->preloadRecordSelect()
                    ->multiple()
                    ->recordSelectOptionsQuery(fn ($query) => $query->orderBy('full_name')),
            ])
            ->recordActions([
                DetachAction::make()
                    // FIX: Dispatch a custom event to trigger a full component refresh
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshComponent');
                    }),
                ViewAction::make()
                    ->schema([
                        TextInput::make('embg')->label('EMBG'),
                        TextInput::make('national_id')->label('Број на лична карта'),
                        TextInput::make('passport_number')->label('Број на пасош'),
                        TextInput::make('passport_expiration_date')->label('Важи до:'),
                    ]),
                //                    ->url(fn ($record) => MemberResource::getUrl('view', ['record' => $record->id]))
                //                    ->openUrlInNewTab(false),
            ])
            ->toolbarActions([
                DetachBulkAction::make()
                    // FIX: Dispatch a custom event to trigger a full component refresh
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshComponent');
                    }),
            ]);
    }

    /**
     * Defines Livewire listeners for the component.
     */
    protected function getListeners(): array
    {
        // Define the 'refreshComponent' event to call the public refreshComponent method.
        return [
            'refreshComponent' => 'refreshComponent',
        ];
    }

    /**
     * Public method called by the 'refreshComponent' event.
     * Its existence forces a full Livewire re-render.
     */
    public function refreshComponent(): void
    {
        // The simple existence of this public method and its listener mapping
        // is enough to trigger a full component re-render.
    }
}
