<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة</title>

    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/DejaVuSans.ttf') }}") format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1e293b;
            font-size: 12px;
            direction: rtl;
            text-align: right;
            unicode-bidi: bidi-override;
            margin: 0;
            padding: 0;
        }

        .head {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .head td {
            vertical-align: top;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            color: #4338ca;
        }

        .muted {
            color: #64748b;
        }

        .box {
            background: #f8fafc;
            padding: 12px;
            border: 1px solid #e2e8f0;
            margin-bottom: 15px;
        }

        table {
            direction: rtl;
        }

        th,
        td {
            text-align: right;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table.items th {
            background: #4338ca;
            color: #ffffff;
            padding: 8px;
            text-align: right;
            font-size: 11px;
        }

        table.items td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .num {
            text-align: left;
            direction: ltr;
        }

        .totals {
            width: 45%;
            margin-top: 15px;
            margin-right: auto;
            border-collapse: collapse;
        }

        .totals td {
            padding: 6px 8px;
        }

        .grand td {
            border-top: 2px solid #1e293b;
            font-weight: bold;
            font-size: 14px;
            color: #4338ca;
        }

        .notes {
            margin-top: 25px;
            clear: both;
        }
    </style>
</head>
<body>

    <table class="head">
        <tr>
            <td>
                <div class="title">فاتورة</div>
                <div class="muted">INVOICE</div>
            </td>

            <td style="text-align:left;">
                <div>
                    <strong>{{ $invoice->number }}</strong>
                </div>

                <div class="muted">
                    {{ optional($invoice->issue_date)->format('d/m/Y') }}
                </div>
            </td>
        </tr>
    </table>

    <div class="box">
        <div>
            <strong>
                {{ optional($invoice->customer)->name_ar ?: optional($invoice->customer)->name }}
            </strong>
        </div>

        @if(optional($invoice->customer)->tax_number)
            <div class="muted">
                الرقم الضريبي: {{ $invoice->customer->tax_number }}
            </div>
        @endif

        @if(optional($invoice->customer)->address)
            <div class="muted">
                {{ $invoice->customer->address }}
            </div>
        @endif
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>الوصف</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>خصم %</th>
                <th>ضريبة %</th>
                <th>الإجمالي</th>
            </tr>
        </thead>

        <tbody>
            @forelse($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="num">{{ number_format($item->quantity, 2) }}</td>
                    <td class="num">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="num">{{ number_format($item->discount, 2) }}</td>
                    <td class="num">{{ number_format($item->tax_rate, 2) }}</td>
                    <td class="num">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;">
                        لا توجد عناصر
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="muted">المجموع الفرعي</td>
            <td class="num">
                {{ number_format($invoice->subtotal, 2) }}
                {{ $invoice->currency }}
            </td>
        </tr>

        <tr>
            <td class="muted">الخصم</td>
            <td class="num">
                {{ number_format($invoice->discount_total, 2) }}
                {{ $invoice->currency }}
            </td>
        </tr>

        <tr>
            <td class="muted">الضريبة</td>
            <td class="num">
                {{ number_format($invoice->tax_total, 2) }}
                {{ $invoice->currency }}
            </td>
        </tr>

        <tr class="grand">
            <td>الإجمالي النهائي</td>
            <td class="num">
                {{ number_format($invoice->grand_total, 2) }}
                {{ $invoice->currency }}
            </td>
        </tr>
    </table>

    @if(!empty($invoice->notes))
        <div class="notes muted">
            {{ $invoice->notes }}
        </div>
    @endif

</body>
</html>