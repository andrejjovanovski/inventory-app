<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Member;

class MemberVerificationController extends Controller
{
    public function verify(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired verification link.');
        }

        $member = Member::findOrFail($id);

        if (! $member->is_email_verified) {
            $member->is_email_verified = true;
            $member->save();

            // Send welcome email
            \Illuminate\Support\Facades\Mail::to($member->email)->queue(new \App\Mail\WelcomeMemberMail($member));
        }

        return "Email verified successfully for member: " . $member->full_name . "Feel free to close this window.";
    }
}
