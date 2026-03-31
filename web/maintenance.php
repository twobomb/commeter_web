<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Технические работы</title>
    <link rel="stylesheet" href="/css/fontawesome-pro-5.8.2/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #1f2937;
        }
        .maintenance {
            width: 100%;
            max-width: 560px;
            margin: 20px;
            padding: 40px 28px;
            text-align: center;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
        }
        .maintenance__icon {
            font-size: 56px;
            color: #f59e0b;
            margin-bottom: 16px;
        }
        .maintenance__title {
            margin: 0 0 10px;
            font-size: 28px;
            font-weight: 700;
        }
        .maintenance__text {
            margin: 0 0 24px;
            font-size: 16px;
            line-height: 1.5;
            color: #4b5563;
        }
        .maintenance__button {
            border: 0;
            border-radius: 10px;
            padding: 12px 22px;
            font-size: 15px;
            font-weight: 600;
            color: #ffffff;
            background: #2563eb;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .maintenance__button:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <main class="maintenance">
        <div class="maintenance__icon">
            <i class="fas fa-tools" aria-hidden="true"></i>
        </div>
        <h1 class="maintenance__title">Технические работы</h1>
        <p class="maintenance__text">Ведутся технические работы, попробуйте зайти через несколько минут.</p>
        <button class="maintenance__button" type="button" onclick="window.location.reload();">Повторить попытку</button>
    </main>
</body>
</html>
