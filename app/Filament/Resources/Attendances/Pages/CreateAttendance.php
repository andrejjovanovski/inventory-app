<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Resources\Attendances\AttendanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function afterCreate(): void
    {
        $attendance = $this->record;
        
        // Get all members from the group
        $group = $attendance->group;
        
        if ($group) {
            $memberIds = $group->members()->pluck('members.id');
            
            // Prepare pivot data for all members (is_present = false by default)
            $pivotData = [];
            foreach ($memberIds as $memberId) {
                $pivotData[$memberId] = ['is_present' => false];
            }
            
            // Attach all group members to the attendance in one batch operation
            $attendance->members()->attach($pivotData);
        }
    }
}
