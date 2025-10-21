<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>もちログ - ログイン</title>

  <!-- 🧁 やわらかいフォント -->
  <link href="https://fonts.googleapis.com/css2?family=Yomogi&display=swap" rel="stylesheet">

  <style>
    /* 🌿 ページ全体（白背景＆リセット） */
    * {
      box-sizing: border-box;
      padding: 0;
      margin: 0;
    }

    html, body {
      width: 100%;
      height: 100%;
      background-color: #ffffff; /* ← 白背景固定 */
      overflow: hidden;
      font-family: 'Yomogi', 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
    }

    /* 💫 GIF：左外→右外をゆっくり移動 */
    @keyframes move-right {
      0%   { transform: translateX(-150vw); }
      100% { transform: translateX(150vw); }
    }

    .moving-gif {
      position: absolute;
      bottom: 52%; /* ← 少し上に配置 */
      height: 360px;
      animation: move-right 34s linear infinite; /* ← ゆっくり移動 */
      opacity: 1;
      pointer-events: none;
      z-index: 1;
      filter: brightness(1.05) contrast(1.1) saturate(1.1);
    }

    /* 🌸 タイトル */
    .title {
      position: fixed;
      top: 110px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 5rem;
      font-weight: 900;
      color: #3e6b4d;
      text-shadow: 3px 3px 10px rgba(255,255,255,0.9);
      letter-spacing: 0.05em;
      z-index: 3;
      text-align: center;
    }

    /* 🌱 左上のロゴ */
    .logo {
      position: fixed;
      top: 20px;
      left: 25px;
      height: 50px;
      z-index: 3;
    }

    /* 🔐 ボタン（中央配置） */
    .button-container {
      position: relative;
      z-index: 2;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 25px;
    }

    .btn {
      background: rgba(255, 255, 255, 0.8);
      border: none;
      border-radius: 30px;
      padding: 16px 60px;
      font-size: 1.4rem;
      color: #2f6045;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      backdrop-filter: blur(8px);
      transition: all 0.3s ease;
    }

    .btn:hover {
      background: rgba(240, 255, 240, 0.95);
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    /* 📱 スマホ最適化 */
    @media (max-width: 768px) {
      html, body {
        background-color: #ffffff; /* ← グレー化防止 */
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
      }

      .moving-gif {
        height: 200px;
        bottom: 58%;
        animation-duration: 36s; /* ← 少しだけさらにゆっくり */
      }

      .title {
        font-size: 3rem;
        top: 80px;
      }

      .btn {
        width: 200px;
        font-size: 1.1rem;
        padding: 12px 24px;
      }

      .button-container {
        margin-top: 280px;
        gap: 20px;
      }
    }

    @media (max-width: 480px) {
      .title {
        font-size: 2.4rem;
        top: 60px;
      }

      .moving-gif {
        height: 160px;
        bottom: 60%;
      }

      .button-container {
        margin-top: 240px;
      }
    }
  </style>
</head>

<body>
  <!-- 🌱 ロゴ -->
  <img src="images/ielog-icon.svg" alt="IeLog ロゴ" class="logo">

  <!-- 💫 背景GIF（流れる） -->
  <img src="images/your-bg.gif" alt="背景アニメーション" class="moving-gif">

  <!-- 🌸 タイトル -->
  <div class="title">もちログ</div>

  <!-- 🔐 ログイン＆登録ボタン -->
  <div class="button-container">
    <button class="btn" onclick="location.href='{{ route('login') }}'">ログイン</button>
    <button class="btn" onclick="location.href='{{ route('register') }}'">アカウント登録</button>
  </div>
</body>
</html>
