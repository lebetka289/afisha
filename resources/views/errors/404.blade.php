<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Страница не найдена</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            color: #fff;
            font-family: system-ui, sans-serif;
            overflow: hidden;
            position: relative;
        }
        .bg-photo {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.25;
            filter: grayscale(40%) blur(2px);
            z-index: 1;
        }
        .bg-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 40%, rgba(0,0,0,0.8) 100%);
            z-index: 2;
        }
        .checkerboard {
            position: absolute;
            width: 100px;
            height: 100px;
            background-image: repeating-conic-gradient(#7C3AED 0% 25%, transparent 0% 50%);
            background-size: 16px 16px;
            opacity: 0.25;
            z-index: 3;
        }
        .cb-tl { top: 20px; left: 20px; }
        .cb-br { bottom: 20px; right: 20px; }
        .content {
            text-align: center;
            z-index: 10;
            position: relative;
            padding: 2rem;
        }
        .code {
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-size: clamp(80px, 18vw, 240px);
            line-height: 0.85;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, #7C3AED, #EC4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .msg {
            font-size: 1rem;
            color: #a1a1aa;
            margin-top: 1rem;
        }
        .sparkle { color: #F59E0B; font-size: 1.5rem; }
        .actions {
            margin-top: 1.5rem;
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 0.625rem 1.75rem;
            font-weight: 900;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-purple { background: #7C3AED; color: #fff; }
        .btn-purple:hover { background: #6D28D9; }
        .btn-outline { border: 2px solid #fff; color: #fff; background: transparent; }
        .btn-outline:hover { background: #fff; color: #000; }
    </style>
</head>
<body>
    <img src="/images/404-bg.png" alt="" class="bg-photo">
    <div class="bg-overlay"></div>
    <div class="checkerboard cb-tl"></div>
    <div class="checkerboard cb-br"></div>
    <div class="content">
        <div class="sparkle">✦</div>
        <div class="code">404</div>
        <p class="msg">Страница не найдена</p>
        <div class="actions">
            <a href="/" class="btn btn-purple">На главную</a>
            <a href="javascript:history.back()" class="btn btn-outline">Назад</a>
        </div>
    </div>
</body>
</html>
