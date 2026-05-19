<?php

namespace Tests\Feature;

use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MemberAbsenceReportPdfTest extends TestCase
{
    public function test_member_absence_report_pdf_renders(): void
    {
        $member = new Member([
            "full_name" => "Test Member",
            "badge_number" => "001",
        ]);

        $pdf = Pdf::loadView("pdfs.member-absence-report", [
            "absenceDate" => Carbon::parse("2026-05-15"),
            "member" => $member,
        ])->output();

        $this->assertStringStartsWith("%PDF-", $pdf);
    }
}
