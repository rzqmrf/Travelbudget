<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Trip - {{ $trip->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #334155;
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .header p {
            margin: 4px 0 0;
            opacity: 0.8;
            font-size: 11px;
        }

        .section {
            margin-bottom: 24px;
        }

        .section h2 {
            font-size: 16px;
            color: #1E293B;
            border-bottom: 2px solid #E2E8F0;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            padding: 6px 12px 6px 0;
            color: #64748B;
            font-size: 11px;
            width: 40%;
        }

        .info-value {
            display: table-cell;
            padding: 6px 0;
            font-weight: 600;
        }

        .expense-table {
            width: 100%;
            border-collapse: collapse;
        }

        .expense-table th {
            background: #F1F5F9;
            padding: 8px 12px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #64748B;
            border-bottom: 2px solid #E2E8F0;
        }

        .expense-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #F1F5F9;
        }

        .expense-table tr:last-child td {
            border-bottom: none;
        }

        .total-row {
            background: #F8FAFC;
            font-weight: 700;
        }

        .budget-bar {
            height: 12px;
            background: #E2E8F0;
            border-radius: 6px;
            overflow: hidden;
            margin: 8px 0;
        }

        .budget-fill {
            height: 100%;
            border-radius: 6px;
        }

        .category-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
        }

        .footer {
            text-align: center;
            color: #94A3B8;
            font-size: 10px;
            margin-top: 40px;
            padding-top: 16px;
            border-top: 1px solid #E2E8F0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $trip->name }}</h1>
        <p>{{ $trip->origin_name }} &rarr; {{ $trip->destination_name }}</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    <div class="section">
        <h2>Informasi Perjalanan</h2>
        <div class="info-grid">
            <div class="info-row"><span class="info-label">Kendaraan</span><span class="info-value">{{ $trip->vehicle?->name ?? '-' }}</span></div>
            <div class="info-row"><span class="info-label">Jarak</span><span class="info-value">{{ $trip->distance_km ?? '-' }} km</span></div>
            <div class="info-row"><span class="info-label">Durasi</span><span class="info-value">{{ $trip->formatted_duration }}</span></div>
            <div class="info-row"><span class="info-label">Status</span><span class="info-value">{{ $trip->status->label() }}</span></div>
            <div class="info-row"><span class="info-label">Dibuat</span><span class="info-value">{{ $trip->created_at->translatedFormat('d M Y') }}</span></div>
            @if($trip->started_at)
            <div class="info-row"><span class="info-label">Dimulai</span><span class="info-value">{{ $trip->started_at->translatedFormat('d M Y, H:i') }}</span></div>
            @endif
            @if($trip->completed_at)
            <div class="info-row"><span class="info-label">Selesai</span><span class="info-value">{{ $trip->completed_at->translatedFormat('d M Y, H:i') }}</span></div>
            @endif
        </div>
    </div>

    <div class="section">
        <h2>Ringkasan Anggaran</h2>
        <div class="info-grid">
            <div class="info-row"><span class="info-label">Anggaran</span><span class="info-value">Rp {{ number_format($trip->budget_amount, 0, ',', '.') }}</span></div>
            <div class="info-row"><span class="info-label">Total Pengeluaran</span><span class="info-value">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</span></div>
            <div class="info-row"><span class="info-label">Sisa</span><span class="info-value" style="color: {{ ($trip->budget_amount - $totalExpenses) >= 0 ? '#10B981' : '#EF4444' }}">Rp {{ number_format($trip->budget_amount - $totalExpenses, 0, ',', '.') }}</span></div>
        </div>
        @php $pct = $trip->budget_amount > 0 ? min(100, ($totalExpenses / $trip->budget_amount) * 100) : 0; @endphp
        <div class="budget-bar">
            <div class="budget-fill" style="width: {{ $pct }}%; background: {{ $pct >= 80 ? '#EF4444' : ($pct >= 50 ? '#F59E0B' : '#10B981') }};"></div>
        </div>

        @if($expensesByCategory->isNotEmpty())
        <h3 style="font-size: 13px; margin-top: 16px; color: #475569;">Per Kategori:</h3>
        <div class="info-grid" style="margin-top: 8px;">
            @foreach($expensesByCategory as $cat => $total)
            <div class="info-row"><span class="info-label">{{ $cat }}</span><span class="info-value">Rp {{ number_format($total, 0, ',', '.') }}</span></div>
            @endforeach
        </div>
        @endif
    </div>

    @if($trip->expenses->isNotEmpty())
    <div class="section">
        <h2>Rincian Pengeluaran</h2>
        <table class="expense-table">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Catatan</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trip->expenses->sortBy('spent_at') as $expense)
                <tr>
                    <td>{{ $expense->category->icon() }} {{ $expense->category->label() }}</td>
                    <td style="font-weight: 600;">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                    <td>{{ $expense->note ?: '-' }}</td>
                    <td>{{ $expense->spent_at->translatedFormat('d M Y, H:i') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total</td>
                    <td>Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Laporan ini digenerate otomatis oleh TravelBudget &bull; {{ now()->year }}
    </div>
</body>

</html>