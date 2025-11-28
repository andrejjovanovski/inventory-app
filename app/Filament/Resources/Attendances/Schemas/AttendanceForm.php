<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Models\Group;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make("mentor_id")
                ->label("Mentor")
                ->options(User::all()->pluck("name", "id"))
                ->required()
                ->reactive()
                ->afterStateUpdated(
                    fn($state, $set, $get) => $set(
                        "title",
                        self::buildAttendanceTitle($get),
                    ),
                ),

            Select::make("group_id")
                ->label("Group")
                ->options(Group::all()->pluck("name", "id"))
                ->required()
                ->reactive()
                ->afterStateUpdated(
                    fn($state, $set, $get) => $set(
                        "title",
                        self::buildAttendanceTitle($get),
                    ),
                ),

            DatePicker::make("attendance_date")
                ->default(fn() => Carbon::now()->format("Y-m-d"))
                ->required()
                ->reactive()
                ->afterStateUpdated(
                    fn($state, $set, $get) => $set(
                        "title",
                        self::buildAttendanceTitle($get),
                    ),
                ),

            TimePicker::make("start_time")
                ->required()
                ->reactive()
                ->seconds(false)
                ->afterStateUpdated(
                    fn($state, $set, $get) => $set(
                        "title",
                        self::buildAttendanceTitle($get),
                    ),
                ),

            TimePicker::make("end_time")
                ->required()
                ->reactive()
                ->seconds(false)
                ->afterStateUpdated(
                    fn($state, $set, $get) => $set(
                        "title",
                        self::buildAttendanceTitle($get),
                    ),
                ),

            TextInput::make("title")
                ->label("Title")
                ->readOnly()
                ->required()
                ->reactive(),

            Hidden::make("created_by")->default(Auth::id())->required(),
        ]);
    }

    protected static function buildAttendanceTitle($get)
    {
        $mentor = User::query()->find($get("mentor_id"));
        $mentorName = $mentor ? $mentor->name : "Unknown Mentor";
        $date = $get("attendance_date") ?? Carbon::now()->format("Y-m-d");
        $start = $get("start_time") ?? "00:00";
        $end = $get("end_time") ?? "00:00";

        return "{$mentorName} - {$date} ({$start} - {$end})";
    }
}
