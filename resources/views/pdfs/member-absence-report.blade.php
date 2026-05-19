<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Absence Report</title>
    <style>
        body {
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            margin: 48px;
        }

        h1 {
            font-size: 24px;
            margin: 0 0 32px;
            text-align: center;
            text-transform: uppercase;
        }

        .meta {
            margin-bottom: 32px;
        }

        .meta p,
        .statement {
            margin: 0 0 12px;
        }

        .signature {
            margin-top: 72px;
            width: 220px;
        }

        .signature-line {
            border-top: 1px solid #111827;
            padding-top: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Absence Report</h1>

    <div class="meta">
        <p><strong>Member:</strong> {{ $member->full_name }}</p>
        <p><strong>Badge number:</strong> {{ $member->badge_number ?: 'N/A' }}</p>
        <p><strong>Date:</strong> {{ $absenceDate->format('F j, Y') }}</p>
    </div>

    <p class="statement">
        {{ $member->full_name }} was absent on {{ $absenceDate->format('F j, Y') }}
        for reasons related to the organization.
    </p>

    <div class="signature">
        <div class="signature-line">Authorized signature</div>
    </div>
</body>
</html>
