<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Mail\WelcomeMemberMail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    protected function afterCreate(): void
    {
        $member = $this->record;

        Mail::to($member->email)->queue(new WelcomeMemberMail($member));
    }
}
