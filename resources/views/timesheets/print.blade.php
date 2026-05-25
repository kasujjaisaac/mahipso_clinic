<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timesheet {{ $timesheet->month->format('F Y') }}</title>
    <style>
        body { font-family: Calibri, Arial, sans-serif; color: #000; margin: 0; background: #f3f3f3; font-size: 11px; }
        .toolbar { padding: 12px; text-align: right; }
        .sheet { width: 794px; min-height: 1123px; margin: 0 auto 24px; background: #fff; padding: 24px 72px 36px; box-shadow: 0 4px 18px rgba(0,0,0,.12); box-sizing: border-box; position: relative; }
        .logo { width: 74px; height: 96px; object-fit: contain; position: absolute; top: 22px; left: 50%; transform: translateX(-50%); }
        .top { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; line-height: 1.25; font-family: Arial, sans-serif; font-size: 9px; min-height: 96px; padding-top: 8px; }
        .org { text-align: center; margin: 0; font-family: "Bookman Old Style", Georgia, serif; font-weight: 700; font-size: 14px; line-height: 1.2; }
        .org .underlined { display: inline-block; border-bottom: 1px solid #000; padding: 0 62px 1px; }
        .title { text-align: center; font-weight: 700; font-size: 16px; margin: 14px 0 13px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 0 2px; margin-bottom: 0; font-size: 11px; }
        .meta div { border: 1px inset #777; padding: 4px 6px; min-height: 18px; }
        .meta .job-title { grid-column: 1 / -1; }
        .line { border-bottom: 1px dotted #000; min-height: 14px; display: inline-block; min-width: 178px; padding: 0 4px; vertical-align: bottom; }
        .months { display: grid; grid-template-columns: 1fr 1fr; gap: 0 2px; margin: 0; font-size: 11px; }
        .months > div { border: 1px inset #777; border-top: 0; padding: 4px 6px; min-height: 18px; }
        .employee-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0; margin: 10px 0; }
        .employee-row div { border: 1px solid #000; min-height: 22px; padding: 4px 6px; }
        .box { display: inline-block; width: 9px; height: 9px; border: 1px solid #000; margin-right: 4px; vertical-align: -1px; }
        .box.checked::after { content: "\2713"; position: relative; left: 1px; top: -5px; font-size: 12px; }
        table { width: 100%; border-collapse: separate; border-spacing: 1px; }
        th, td { border: 1px solid #000; padding: 2px 5px; text-align: left; vertical-align: top; height: 18px; }
        th { text-align: center; font-weight: 700; }
        .signatures { display: grid; gap: 12px; margin-top: 18px; font-size: 12px; }
        .button { border: 1px solid #111; background: #fff; padding: 8px 12px; cursor: pointer; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { width: auto; min-height: auto; margin: 0; padding: 0; box-shadow: none; }
            .logo { top: 0; }
            @page { size: A4 portrait; margin: 10mm 18mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="button" onclick="window.print()">Print</button>
    </div>

    <main class="sheet">
        @php $logoVersion = file_exists(public_path('mahipso-logo.png')) ? filemtime(public_path('mahipso-logo.png')) : time(); @endphp
        <img class="logo" src="{{ asset('mahipso-logo.png') }}?v={{ $logoVersion }}" alt="MAHIPSO logo">
        <div class="top">
            <div>
                Tel: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+256 755 711 264<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+256 704 702 443<br>
                Email: &nbsp;&nbsp;&nbsp;&nbsp;kpmahipso@gmail.com<br>
                Facebook: www.facebook.com/mahipso
            </div>
            <div style="text-align:right;">
                MAHIPSO<br>
                P.O BOX 1821<br>
                Masaka - Uganda
            </div>
        </div>

        <div class="org">MASAKA KP HIV PREVENTION AND SUPPORT ORGANISATION<br><span class="underlined">(MAHIPSO)</span></div>
        <div class="title">MONTHLY TIME SHEET</div>

        <div class="meta">
            <div>Name: <span class="line">{{ $timesheet->user->name ?? '' }}</span></div>
            <div>Date: <span class="line">{{ optional($timesheet->prepared_at)->format('Y-m-d') }}</span></div>
            <div class="job-title">Job title: <span class="line" style="min-width: 510px;">{{ $timesheet->job_title }}</span></div>
        </div>

        @php $selectedMonth = (int) $timesheet->month->format('n'); @endphp
        <div class="months">
            <div>
                @foreach([1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun'] as $number => $label)
                    <span><span class="box {{ $selectedMonth === $number ? 'checked' : '' }}"></span>{{ $label }}&nbsp;&nbsp;</span>
                @endforeach
            </div>
            <div>
                @foreach([7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'] as $number => $label)
                    <span><span class="box {{ $selectedMonth === $number ? 'checked' : '' }}"></span>{{ $label }}&nbsp;&nbsp;</span>
                @endforeach
            </div>
        </div>

        <div class="employee-row">
            <div><strong>EMPLOYEE NUMBER</strong></div>
            <div>{{ $timesheet->employee_number }}</div>
        </div>

        @php $entries = $timesheet->entries->keyBy('day'); @endphp
        <table>
            <thead>
                <tr>
                    <th style="width: 38px;">Day</th>
                    <th>Specification of work</th>
                    <th style="width: 110px;">Time Start</th>
                    <th style="width: 122px;">Time Finish</th>
                </tr>
            </thead>
            <tbody>
                @for($day = 1; $day <= 31; $day++)
                    @php $entry = $entries->get($day); @endphp
                    <tr>
                        <td>{{ $day }}</td>
                        <td>{{ $entry->work_specification ?? '' }}</td>
                        <td>{{ $entry->time_start ?? '' }}</td>
                        <td>{{ $entry->time_finish ?? '' }}</td>
                    </tr>
                @endfor
                <tr>
                    <td></td>
                    <td><strong>TOTAL HOURS WORKED</strong></td>
                    <td colspan="2" style="text-align:right;">{{ number_format($timesheet->total_hours, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="signatures">
            <div>HRM Signature: <span class="line" style="min-width: 260px;">{{ $timesheet->hrReceivedBy->name ?? '' }}</span> Date Received: <span class="line">{{ optional($timesheet->hr_received_at)->format('Y-m-d') }}</span></div>
            <div>Employee Signature: <span class="line" style="min-width: 260px;">{{ $timesheet->user->name ?? '' }}</span> Date: <span class="line">{{ optional($timesheet->submitted_at)->format('Y-m-d') }}</span></div>
            <div>Supervisor Signature: <span class="line" style="min-width: 260px;">{{ $timesheet->lineSupervisor->name ?? '' }}</span> Date: <span class="line">{{ optional($timesheet->supervisor_reviewed_at)->format('Y-m-d') }}</span></div>
        </div>
    </main>
</body>
</html>
