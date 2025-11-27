<?php

namespace App\Filament\Resources\Members\Schemas;

// Import Schema class required for the configure method signature
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * This class provides the component array for the Member form.
 * It is designed to be used within a Resource's form method in Filament 4
 * by calling `->schema(MemberForm::getComponents())`.
 */
class MemberForm
{
    /**
     * Configures the provided Filament Schema with the form components (Wizard).
     * This method is used by MemberResource::form().
     *
     * @return \Filament\Schemas\Schema
     */
    public static function configure(Schema $schema): Schema
    {
        // Use the components() method on the Schema object to define the form structure
        return $schema->components([
            Wizard::make([
                // Step 1 (Index 0): Member Info
                Step::make('Member Info')
                    ->schema([
                        Grid::make(2) // 2 columns
                            ->schema([
                                Section::make('Image & Personal Details')
                                    ->schema([
                                        // Left column: Image
                                        FileUpload::make('image_path')
                                            ->label('Image')
                                            ->directory('members')
                                            ->maxSize(2048)
                                            ->imageEditor()
                                            ->imagePreviewHeight(213)
                                            ->columnSpan(1), // left column

                                        // Right column: Full Name + DOB
                                        Group::make()
                                            ->schema([
                                                TextInput::make('full_name')
                                                    ->label('Full Name')
                                                    ->required(),
                                                DatePicker::make('date_of_birth')
                                                    ->label('Date of Birth')
                                                    ->required()
                                                    ->reactive(),
                                                Select::make('gender')
                                                    ->label('Gender')
                                                    ->options([
                                                        'male' => 'Машко',
                                                        'female' => 'Женско',
                                                        'other' => 'Друго',
                                                    ])
                                                    ->required(),
                                            ])
                                            ->columnSpan(1),
                                    ])->columns(2)->columnSpan(2),
                            ]),

                        // Continue with other fields below the grid
                        Section::make("Private Personal Details")
                            ->schema([
                                TextInput::make('embg')
                                    ->label('EMBG')
                                    ->required(),

                                Group::make()
                                    ->schema([
                                        TextInput::make('national_id')
                                            ->label('National ID'),

                                        DatePicker::make('nid_expiration_date')
                                            ->label('NID Expiration Date'),
                                    ])->columns(2),

                                Group::make()
                                    ->schema([
                                        TextInput::make('passport_number')
                                            ->label('Passport Number'),

                                        DatePicker::make('passport_expiration_date')
                                            ->label('Passport Expiration Date'),
                                    ])->columns(2),
                            ])
                            ->columns(1)
                            ->columnSpan('full'),

                        Section::make("Contact Information")
                            ->schema([
                                TextInput::make('address')
                                    ->label('Address'),

                                TextInput::make('phone_number')
                                    ->label('Phone Number')
                                    ->tel(),

                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required(),
                            ]),

                        Hidden::make('created_by')
                            ->default(fn() => Auth::id()),

                        Grid::make(2) // 2 columns
                            ->schema([
                                // Left column can be empty to push field to right
                                // Use a placeholder or just leave first column empty
                                Grid::make()->schema([])->columnSpan(1),

                                // Right column: the field
                                Select::make('groups')
                                    ->required()
                                    ->relationship('groups', 'name')
                                    ->preload()
                                    ->multiple()
                                    ->searchable()
                                    ->helperText('Изберете група на која припаѓа членот')
                                    ->columnSpan(1),
                            ])
                            ->columnSpan('full'),
                    ]),

                // Step 2 (Index 1): Parent Info (only if under 18)
                Step::make('Parent Info')
                    ->schema([
                        TextInput::make('parent_name')
                            ->label('Parent / Guardian Name')
                            ->required(),
                        TextInput::make('parent_phone')
                            ->label('Parent / Guardian Phone'),
                    ])
                    ->visible(function ($get) {
                        $dob = $get('date_of_birth');
                        // Ensure $dob is a Carbon instance before calculating age
                        if (!$dob) {
                            return false;
                        }
                        // Use Carbon to safely calculate age, handling string or Carbon objects
                        $dob = $dob instanceof Carbon ? $dob : Carbon::parse($dob);

                        $age = $dob->diffInYears(now());
                        return $age < 18;
                    }),

                // Step 3 (Index 2): Documents
                Step::make('Documents')
                    ->schema([
                        FileUpload::make('documents')
                            ->label('Member Documents')
                            ->multiple()
                            ->directory('members')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048),
                    ]),
            ])
                ->reactive()
                ->columnSpan('full'),
        ]);
    }
}