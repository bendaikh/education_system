<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>School Invoice</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #111827; font-size: 12px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .brand { display: flex; align-items: center; gap: 10px; }
        .brand img { height: 36px; }
        .muted { color: #6b7280; }
        h1 { font-size: 20px; margin: 0 0 6px; }
        h2 { font-size: 14px; margin: 0 0 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f9fafb; }
        .right { text-align: right; }
        .total { text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header" style="justify-content: center;">
        <div class="brand" style="flex-direction: column; align-items: center; text-align: center;">
            @if(\App\Models\Setting::get('logo'))
                <img src="{{ public_path('storage/' . \App\Models\Setting::get('logo')) }}" alt="Logo" style="height: 56px;" />
            @endif
            <h1 style="margin-top: 6px;">{{ \App\Models\Setting::get('app_name', 'Academy') }}</h1>
            <div class="muted">School Invoice • {{ $month }}</div>
        </div>
    </div>
    <div class="right" style="margin-top: 8px;">
        <h2>{{ $category }}</h2>
        <div class="muted">{{ $currency }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Student</th>
                <th>Date</th>
                <th class="right">School %</th>
                <th class="right">Amount ({{ $currency }})</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $e)
                <tr>
                    <td>{{ $e['subject'] }}</td>
                    <td>{{ $e['student'] }}</td>
                    <td>{{ $e['date'] }}</td>
                    <td class="right">{{ number_format($e['school_percent'] ?? 0, 2) }}%</td>
                    <td class="right">{{ number_format($e['amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="total">Total</td>
                <td class="total">{{ number_format($total, 2) }} {{ $currency }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>


