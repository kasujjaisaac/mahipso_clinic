<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisition {{ $requisition->serial_number }}</title>
    <style>
        body { font-family: Georgia, "Times New Roman", Times, serif; color: #000; margin: 0; background: #f3f3f3; font-size: 12px; }
        .toolbar { padding: 12px; text-align: right; }
        .sheet { width: 794px; min-height: 1123px; margin: 0 auto 24px; background: #fff; padding: 43px 42px 20px 54px; box-shadow: 0 4px 18px rgba(0,0,0,.12); box-sizing: border-box; position: relative; overflow: hidden; }
        .side-band { position: absolute; left: 0; top: 38px; width: 32px; height: 300px; background: #c00000; }
        .header { display: grid; grid-template-columns: 1fr 150px; gap: 24px; align-items: start; line-height: 1.16; min-height: 122px; }
        .brand { max-width: 410px; padding-left: 0; }
        .logo { width: 108px; height: 142px; object-fit: contain; display: block; margin-left: auto; margin-top: -4px; }
        .office { text-align: right; font-weight: 700; padding-top: 0; }
        .title { text-align: center; font-weight: 700; text-decoration: underline; font-size: 17px; letter-spacing: 0; margin: 8px 0 22px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 11px 40px; margin-bottom: 16px; font-size: 12px; }
        .line { border-bottom: 1px dotted #000; min-height: 18px; display: inline-block; min-width: 185px; padding: 0 4px; vertical-align: bottom; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 5px 7px; text-align: left; vertical-align: top; height: 23px; }
        th { font-weight: 700; text-align: center; }
        .ruled-space { border-bottom: 1px solid #000; height: 21px; }
        .amount-words { margin: 0 0 26px; border-bottom: 1px solid #000; min-height: 25px; padding-top: 5px; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 44px; margin-top: 20px; }
        .signature-block { min-height: 110px; }
        .button { border: 1px solid #111; background: #fff; padding: 8px 12px; cursor: pointer; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { width: auto; min-height: auto; margin: 0; padding: 0 0 0 12mm; box-shadow: none; overflow: visible; }
            .side-band { left: -10mm; top: 0; }
            @page { size: A4 portrait; margin: 14mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="button" onclick="window.print()">Print</button>
    </div>

    <main class="sheet">
        <div class="side-band"></div>
        <div class="header">
            <div class="brand">
                <strong>MASAKA KP HIV PREVENTION AND SUPPORT ORGANISATION (MAHIPSO)</strong><br>
                P.O BOX 1821 MASAKA (U)<br>
                +256 755 711 264<br>
                Info@mahipso.org<br>
                www.mahipso.org<br>
                @mahipso<br>
                kpmahipso@gmail.com
                <div class="office">Office of the Finance</div>
            </div>
            <img class="logo" src="{{ asset('mahipso-logo.png') }}" alt="MAHIPSO logo">
        </div>

        <div class="title">REQUISITION</div>

        <div class="meta">
            <div>Serial No. <span class="line">{{ $requisition->serial_number }}</span></div>
            <div>Date: <span class="line">{{ $requisition->requested_at->format('Y-m-d') }}</span></div>
            <div>From: <span class="line">{{ $requisition->requester->name ?? '' }}</span></div>
            <div>Department: <span class="line">{{ $requisition->department ?? '' }}</span></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No.</th>
                    <th>Item</th>
                    <th style="width: 90px;">Unit cost</th>
                    <th style="width: 80px;">Quantity</th>
                    <th style="width: 70px;">Freq</th>
                    <th style="width: 100px;">Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requisition->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item }}</td>
                        <td>{{ number_format($item->unit_cost, 2) }}</td>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>{{ $item->frequency }}</td>
                        <td>{{ number_format($item->total_cost, 2) }}</td>
                    </tr>
                @endforeach
                @for($i = $requisition->items->count(); $i < 10; $i++)
                    <tr><td>{{ $i + 1 }}</td><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                @endfor
                <tr>
                    <th colspan="5" style="text-align:right;">Total</th>
                    <th>{{ number_format($requisition->total_amount, 2) }}</th>
                </tr>
            </tbody>
        </table>

        <div class="ruled-space"></div>
        <div class="ruled-space"></div>
        <div class="amount-words">
            Total amount in words:
            {{ $requisition->amount_in_words }}
        </div>

        <div class="signatures">
            <div class="signature-block">
                Checked by: <span class="line">{{ $requisition->checkedBy->name ?? '' }}</span><br><br>
                Signature: <span class="line"></span>
            </div>
            <div class="signature-block">
                <strong>APPROVED BY:</strong> <span class="line">{{ $requisition->approvedBy->name ?? '' }}</span><br><br>
                Signature: <span class="line"></span><br><br>
                Date: <span class="line">{{ optional($requisition->approved_at)->format('Y-m-d') }}</span>
            </div>
        </div>
    </main>
</body>
</html>
