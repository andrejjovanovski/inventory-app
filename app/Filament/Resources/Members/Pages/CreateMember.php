<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Mail\WelcomeMemberMail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    protected function afterCreate(): void
    {
        $member = $this->record;

        // Handle temporary documents folder
        $tempPath = "members/new/documents";
        $newPath = "members/{$member->id}/documents";

        if (Storage::disk('public')->exists($tempPath)) {
            // Create target directory if it doesn't exist
            Storage::disk('public')->makeDirectory($newPath);

            // Move each file from temp to member folder
            foreach (Storage::disk('public')->files($tempPath) as $file) {
                $filename = basename($file);
                Storage::disk('public')->move($file, "{$newPath}/{$filename}");
            }

            // Delete the temporary folder
            Storage::disk('public')->deleteDirectory("members/new");
        }

        // Send welcome email
        if ($member->email) {
            Mail::to($member->email)->queue(new WelcomeMemberMail($member));
        }
    }
}