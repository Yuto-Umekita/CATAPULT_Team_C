<!DOCTYPE html> 
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>もちログ - ようこそ</title>

    <link rel="icon" href="{{ url('favicon.png') }}" type="image/png">
    @vite('resources/css/app.css')

    <style>
        /* 🌸 フェードイン効果 */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 1.3s ease-out forwards;
        }

        /* 背景グラデーション */
        body {
            background: linear-gradient(to bottom right, #E0E7FF, #FFFFFF, #FFE4E6);
            background-size: 200% 200%;
            animation: gradientShift 10s ease-in-out infinite alternate;
        }
        @keyframes gradientShift {
            from { background-position: left top; }
            to { background-position: right bottom; }
        }

        /* ガラス風ボタン */
        .glass-btn {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(14px) saturate(180%);
            -webkit-backdrop-filter: blur(14px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 20px rgba(173, 135, 255, 0.15);
            transition: all 0.3s ease-in-out;
        }
        .glass-btn:hover {
            background: rgba(255, 255, 255, 0.45);
            box-shadow: 0 8px 24px rgba(255, 182, 193, 0.35);
            transform: translateY(-2px);
        }

        /* カード背景 */
        .glass-card {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px rgba(173, 135, 255, 0.15);
        }

        /* 🌸 タイトル文字（もちログ） */
        .logo-wrapper {
            position: absolute;
            top: 10vh; /* 画面上から少し下に */
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: none;
        }

        .logo-text {
            font-family: 'Zen Maru Gothic', 'Hiragino Maru Gothic ProN', sans-serif;
            font-size: 3.6rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.95);
            text-shadow:
                0 0 5px rgba(255, 255, 255, 0.9),
                0 0 10px rgba(255, 192, 203, 0.45),
                0 0 20px rgba(255, 192, 203, 0.25);
            -webkit-text-stroke: 1px rgba(255, 255, 255, 0.7); /* 白縁 */
            letter-spacing: 0.06em;
            animation: fadeIn 1.8s ease-out forwards;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center relative overflow-hidden fade-in">

    {{-- 🌸 タイトル文字（完全中央配置） --}}
    <div class="logo-wrapper">
        <h1 class="logo-text">もちログ</h1>
    </div>

    {{-- 🌸 メインカード --}}
    <div class="relative z-10 text-center glass-card shadow-2xl rounded-3xl px-10 py-12 max-w-md mx-auto">
        <div class="mb-8">
            <p class="text-gray-500 mt-2 text-sm">
                アカウントにログインして始めましょう
            </p>
        </div>

        {{-- 💎 ボタン群 --}}
        <div class="flex flex-col space-y-5 fade-in relative">
            <a href="{{ route('login') }}"
               class="glass-btn relative px-6 py-3 rounded-full text-indigo-600 font-semibold text-lg shadow-md hover:shadow-lg transition-all duration-300">
                ログイン
            </a>

            <span class="text-gray-500 text-sm">または</span>

            <a href="{{ route('register') }}"
               class="glass-btn relative px-6 py-3 rounded-full text-indigo-600 font-semibold text-lg shadow-md hover:shadow-lg transition-all duration-300">
                アカウント登録
            </a>
        </div>
    </div>

    {{-- 🌸 フッター --}}
    <footer class="absolute bottom-4 w-full text-center text-sm text-gray-600 fade-in">
        © {{ date('Y') }} もちログ.
    </footer>
</body>
</html>
