<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemberVerificationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_member_can_verify_email_with_valid_signature()
    {
        \Illuminate\Support\Facades\Mail::fake();
        
        $user = \App\Models\User::factory()->create();
        $member = new \App\Models\Member();
        $member->full_name = 'Test Member';
        $member->email = 'test@example.com';
        $member->is_email_verified = false;
        $member->created_by = $user->id;
        $member->save();

        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'member.verify',
            now()->addMinutes(60),
            ['id' => $member->id]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(200);
        $this->assertTrue($member->fresh()->is_email_verified);

        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\WelcomeMemberMail::class, function ($mail) use ($member) {
            return $mail->member->id === $member->id;
        });
    }

    public function test_member_cannot_verify_email_with_invalid_signature()
    {
        $user = \App\Models\User::factory()->create();
        $member = new \App\Models\Member();
        $member->full_name = 'Test Member';
        $member->email = 'test@example.com';
        $member->is_email_verified = false;
        $member->created_by = $user->id;
        $member->save();

        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'member.verify',
            now()->addMinutes(60),
            ['id' => $member->id]
        );

        // Tamper with the signature
        $invalidUrl = $verificationUrl . 'invalid';

        $response = $this->get($invalidUrl);

        $response->assertStatus(403);
        $this->assertFalse($member->fresh()->is_email_verified);
    }
}
