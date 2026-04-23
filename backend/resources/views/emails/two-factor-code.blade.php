<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0f172a;
            margin: 0;
            padding: 0;
            color: #f8fafc;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .header {
            background: linear-gradient(to right, #10b981, #059669);
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: white;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .content {
            padding: 40px;
            text-align: center;
        }
        .code-box {
            background: rgba(16, 185, 129, 0.1);
            border: 2px dashed #10b981;
            border-radius: 16px;
            padding: 30px;
            margin: 30px 0;
            display: inline-block;
            min-width: 250px;
        }
        .code {
            font-size: 48px;
            font-weight: 900;
            letter-spacing: 12px;
            color: #10b981;
            margin: 0;
            text-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
        }
        .message {
            font-size: 16px;
            line-height: 1.6;
            color: #94a3b8;
            margin-bottom: 20px;
        }
        .footer {
            padding: 30px;
            background: rgba(0, 0, 0, 0.2);
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        .footer p {
            font-size: 13px;
            color: #64748b;
            margin: 5px 0;
        }
        .warning {
            font-size: 12px;
            color: #ef4444;
            margin-top: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sarwary Mukrian</h1>
        </div>
        <div class="content">
            <p class="message" dir="rtl">سڵاو، تکایە ئەم کۆدە بەکاربهێنە بۆ تەواوکردنی لۆگینەکە لە سیستەمی کۆمپانیای سەروەر موکریان.</p>
            
            <div class="code-box">
                <h2 class="code">{{ $code }}</h2>
            </div>
            
            <p class="message" dir="rtl">ئەم کۆدە بۆ ماوەی ١٠ خولەک کاردەکات.</p>
            
            <p class="warning" dir="rtl">ئەگەر تۆ نەبوویت ویستبێتت بچیتە ناو سیستمەکە، تکایە ڕاستەوخۆ وشەی نهێنیت بگۆڕە.</p>
        </div>
        <div class="footer">
            <p>© 2026 Sarwary Mukrian ERP. All rights reserved.</p>
            <p>Enterprise Financial Management System</p>
        </div>
    </div>
</body>
</html>
