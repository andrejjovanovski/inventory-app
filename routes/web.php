<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect("/admin");
});

Route::get('/member/verify/{id}', [App\Http\Controllers\MemberVerificationController::class, 'verify'])
    ->name('member.verify')
    ->middleware('signed');
