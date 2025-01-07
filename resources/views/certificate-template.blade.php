<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: -45px;
            padding: 0;
        }
        .container {
            position: relative;
            width: 100%;
            height: 100%;
        }
        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .content {
            position: absolute;
            top: 40%;
            left: 20%;
        }
        .content h1, .content p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ public_path('img/certificate_pdf_page-0001.jpg') }}" alt="Background" class="background">
        <!-- ชื่อผู้รับ -->
        <div class="recipient-name"
            style="position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    font-size: 32px;
                    font-weight: bold;">
            {{ $recipientName }}
        </div>

        <!-- Program Title -->
        <div class="program-title" style="position: absolute; top: 525px; left: 445px; font-size: 18px;">
            {{ $programTitle }}
        </div>

        <!-- Code Number -->
        <div class="code-number" style="position: absolute; top: 565px; left: 450px; font-size: 18px;">
            {{ $codeNumber }}
        </div>

        <!-- Points -->
        <div class="points" style="position: absolute; top: 605px; left: 375px; font-size: 18px;">
            {{ $points }}
        </div>
    </div>
</body>
</html>
