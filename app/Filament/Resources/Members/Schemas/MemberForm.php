<?php

namespace App\Filament\Resources\Members\Schemas;

// Import Schema class required for the configure method signature
use Filament\Forms\Components\Textarea;
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
use Illuminate\Http\UploadedFile;


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
                                            ->directory(fn($get) => 'members/' . ($get('id') ?? 'new') . '/image')
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
                                    ->length(13)
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
                                    ->label('Address')
                                    ->required(),

                                TextInput::make('phone_number')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->required(),

                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->helperText('Маил адресата ќе служи како корисничко име за најава!')
                                    ->required(),
                            ]),

                        Hidden::make('created_by')
                            ->default(fn() => Auth::id()),

                        Section::make('Additional Information')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        DatePicker::make('joining_date')
                                            ->required(),

                                        Select::make('groups')
                                            ->required()
                                            ->relationship('groups', 'name')
                                            ->preload()
                                            ->multiple()
                                            ->searchable()
                                            ->helperText('Изберете група на која припаѓа членот')
                                            ->columnSpan(1),
                                    ])
                                    ->columns(2)
                                    ->columnSpan('full'),
                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->rows(5)
                                    ->columnSpan('full'),
                            ]),
                    ]),

                // Step 2 (Index 1): Parent Info (only if under 18)
                Step::make('Parent Info')
                    ->schema([
                        TextInput::make('parent_name')
                            ->label('Parent / Guardian Full Name')
                            ->required(fn($get) => MemberForm::isUnder18($get('date_of_birth'))),

                        TextInput::make('parent_phone')
                            ->label('Parent / Guardian Phone')
                            ->required(fn($get) => MemberForm::isUnder18($get('date_of_birth'))),

                        TextInput::make('parent_email')
                            ->label('Parent / Guardian Email')
                            ->email()
                            ->required(fn($get) => MemberForm::isUnder18($get('date_of_birth'))),

                        TextInput::make('parent_embg')
                            ->label('Parent / Guardian EMBG')
                            ->required(fn($get) => MemberForm::isUnder18($get('date_of_birth'))),
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
                            ->directory(fn($get) => $get('id') ? "members/{$get('id')}/documents" : "members/new/documents")
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(2048)
                            ->columnSpan('full')
                            ->openable()
                            ->downloadable()
                            ->dehydrated(true), // Keep dehydrated(true) if necessary, but often not needed for FileUpload

                    ]),
            ])
                ->reactive()
                ->columnSpan('full'),
        ]);
    }

    public static function isUnder18($dob): bool
    {
        if (!$dob) {
            return false;
        }

        $dob = $dob instanceof Carbon ? $dob : Carbon::parse($dob);
        return $dob->diffInYears(now()) < 18;
    }
}