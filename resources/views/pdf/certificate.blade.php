<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ storage_path("fonts/Cairo-Bold.ttf") }}') format('truetype');
            font-weight: 700;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Cairo', 'DejaVu Sans', sans-serif;
            direction: rtl;
            background: #fff;
            color: #1a1a2a;
        }

        .certificate {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding: 40px 30px;
            border: 4px double #b8860b;
            position: relative;
            text-align: center;
        }

        .top-bar {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, #0d7377 0%, #b8860b 100%);
        }

        .corner {
            position: absolute;
            width: 40px; height: 40px;
            border: 2px solid #b8860b;
        }
        .corner.top-right { top: 16px; right: 16px; border-left: none; border-bottom: none; }
        .corner.top-left { top: 16px; left: 16px; border-right: none; border-bottom: none; }
        .corner.bottom-right { bottom: 16px; right: 16px; border-left: none; border-top: none; }
        .corner.bottom-left { bottom: 16px; left: 16px; border-right: none; border-top: none; }

        .logo-text {
            font-size: 20px;
            font-weight: 700;
            color: #0d7377;
            margin-bottom: 5px;
        }

        .eyebrow {
            font-size: 9px;
            font-weight: 700;
            color: #b8860b;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .cert-title {
            font-size: 22px;
            font-weight: 700;
            color: #0d7377;
            margin-bottom: 4px;
        }

        .org {
            font-size: 11px;
            color: #8888a0;
            margin-bottom: 20px;
        }

        .medal-circle {
            width: 70px; height: 70px;
            border: 2px solid rgba(184,134,11,0.4);
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(184,134,11,0.05);
            color: #b8860b;
            font-size: 28px;
        }

        .pre-text {
            font-size: 12px;
            color: #8888a0;
            margin-bottom: 5px;
        }

        .user-name {
            font-size: 28px;
            font-weight: 700;
            color: #0d7377;
            margin-bottom: 8px;
        }

        .post-text {
            font-size: 12px;
            color: #4a4a5e;
            line-height: 1.8;
            margin-bottom: 20px;
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
        }

        .scores-grid {
            display: table;
            width: 90%;
            margin: 0 auto 20px;
        }

        .score-item {
            display: inline-block;
            width: 23%;
            text-align: center;
            padding: 10px 5px;
            margin: 0 0.5%;
            border-radius: 8px;
            background: #f5f5f5;
        }

        .score-label {
            font-size: 10px;
            font-weight: 700;
            color: #0d7377;
            margin-bottom: 3px;
        }

        .score-value {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a2a;
        }

        .details-grid {
            display: table;
            width: 80%;
            margin: 0 auto 15px;
        }

        .detail-item {
            display: inline-block;
            width: 48%;
            text-align: center;
            padding: 10px;
            background: #faf9f6;
            border: 1px solid #f0ece4;
            border-radius: 8px;
            margin: 0 0.5%;
        }

        .detail-label {
            font-size: 9px;
            color: #8888a0;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .detail-value {
            font-size: 13px;
            color: #0d7377;
            font-weight: 700;
        }

        .verify-note {
            font-size: 9px;
            color: #8888a0;
            margin-top: 10px;
        }

        .qr-note {
            font-size: 8px;
            color: #b8860b;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="top-bar"></div>
        <div class="corner top-right"></div>
        <div class="corner top-left"></div>
        <div class="corner bottom-right"></div>
        <div class="corner bottom-left"></div>

        <div class="logo-text">يسّرو</div>
        <div class="eyebrow">YASSIRU CERTIFICATE</div>
        <h2 class="cert-title">شهادة الجاهزية الزواجية</h2>
        <p class="org">منصة يسّرو لتيسير الزواج</p>

        <div class="medal-circle">&#9733;</div>

        <p class="pre-text">تشهد منصة يسّرو بأن</p>
        <h1 class="user-name">{{ $userName }}</h1>
        <p class="post-text">قد أتمّ بنجاح الدورة التأهيلية الزواجية بمساراتها الأربعة بتقدير ممتاز</p>

        <div class="scores-grid">
            <div class="score-item">
                <div class="score-label">شرعي</div>
                <div class="score-value">{{ $shariahScore }}</div>
            </div>
            <div class="score-item">
                <div class="score-label">نفسي</div>
                <div class="score-value">{{ $psychologyScore }}</div>
            </div>
            <div class="score-item">
                <div class="score-label">مالي</div>
                <div class="score-value">{{ $financialScore }}</div>
            </div>
            <div class="score-item">
                <div class="score-label">عملي</div>
                <div class="score-value">{{ $practicalScore }}</div>
            </div>
        </div>

        <div class="details-grid">
            <div class="detail-item">
                <div class="detail-label">رقم الشهادة</div>
                <div class="detail-value">{{ $certificateNumber }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">تاريخ الإصدار</div>
                <div class="detail-value">{{ $issuedAt }}</div>
            </div>
        </div>

        <p class="verify-note">يمكن التحقق من هذه الشهادة عبر إدخال رقمها في المنصة</p>
        <p class="qr-note">{{ url('/') }}/certificates/verify/{{ $certificateNumber }}</p>
    </div>
</body>
</html>
