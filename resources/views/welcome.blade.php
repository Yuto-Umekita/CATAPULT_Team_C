<x-guest-layout>
  <div id="backgroundContainer" class="relative min-h-screen w-full flex items-center justify-center overflow-hidden transition-colors duration-1000">

    <canvas id="floatingParticles" class="absolute inset-0 w-full h-full"></canvas>

    {{-- 🌗 モード切り替え --}}
    <button id="toggleMode"
      class="absolute bottom-6 right-6 z-20 flex items-center gap-2 bg-white/30 backdrop-blur-lg text-gray-800 text-sm px-4 py-2 rounded-full shadow-lg hover:bg-white/50 transition-all duration-300 border border-white/30">
      <span id="toggleIcon">🌙</span>
      <span id="toggleLabel">夜モードへ</span>
    </button>

    {{-- ✍️ タイトル「もちログ」 --}}
    <h1 id="appTitle" class="absolute top-[35%] text-7xl font-bold tracking-widest z-20 text-center w-full select-none">
      <span id="titleStroke">もちログ</span>
    </h1>

    {{-- 📋 メインカード（ボタンのみ） --}}
    <div id="mainCard" class="relative z-10 bg-white/10 backdrop-blur-2xl border border-white/30 rounded-3xl shadow-2xl p-12 w-[400px] text-center transition-all duration-500 hover:shadow-[0_0_25px_rgba(255,255,255,0.3)] mt-60">

      {{-- ログインボタン --}}
      <a id="loginButton"
         href="{{ route('login') }}"
         class="block w-full py-4 mb-6 text-2xl rounded-2xl font-semibold tracking-wide border border-white/50 backdrop-blur-lg transition-all duration-300 shadow-lg">
        ログイン
      </a>

      {{-- 新規登録ボタン --}}
      <a id="registerButton"
         href="{{ route('register') }}"
         class="block w-full py-4 text-2xl rounded-2xl font-semibold tracking-wide border border-white/50 backdrop-blur-lg transition-all duration-300 shadow-lg">
        アカウント登録
      </a>
    </div>
  </div>

  <style>
    /* 🖋️ 筆ペン風フォント */
    @import url('https://fonts.googleapis.com/css2?family=Kaisei+Decol:wght@700&display=swap');

    #titleStroke {
      font-family: 'Kaisei Decol', serif;
      display: inline-block;
      color: transparent;
      -webkit-text-stroke: 1.6px rgba(255,255,255,0.9);
      background-image: linear-gradient(90deg, #ffffff 0%, #ffffff 100%);
      background-repeat: no-repeat;
      -webkit-background-clip: text;
      background-clip: text;
      background-size: 0% 100%;
      animation: drawText 3.5s ease forwards, glowPulse 3s ease-in-out infinite alternate;
    }

    @keyframes drawText {
      0% { background-size: 0% 100%; opacity: 0.2; filter: blur(3px); transform: scale(0.98); }
      50% { filter: blur(1px); opacity: 0.8; }
      100% { background-size: 100% 100%; opacity: 1; filter: blur(0); transform: scale(1); }
    }

    @keyframes glowPulse {
      0% { text-shadow: 0 0 5px rgba(255,255,255,0.15), 0 0 10px rgba(255,255,255,0.2); }
      100% { text-shadow: 0 0 25px rgba(255,255,255,0.5), 0 0 40px rgba(255,255,255,0.6); }
    }

    /* 💎 大きめガラスボタン */
    #loginButton, #registerButton {
      border-radius: 1.25rem;
      border: 2px solid rgba(255,255,255,0.6);
      background: rgba(255,255,255,0.1);
      color: #fff;
      backdrop-filter: blur(25px);
      -webkit-backdrop-filter: blur(25px);
      box-shadow: inset 0 0 15px rgba(255,255,255,0.15), 0 4px 15px rgba(255,255,255,0.08);
      transition: all 0.3s ease;
    }

    #loginButton:hover, #registerButton:hover {
      background: rgba(255,255,255,0.25);
      box-shadow: 0 0 35px rgba(255,255,255,0.3);
      transform: scale(1.05);
    }
  </style>

  <script>
    const container = document.getElementById('backgroundContainer');
    const canvas = document.getElementById('floatingParticles');
    const ctx = canvas.getContext('2d');
    const toggleBtn = document.getElementById('toggleMode');
    const toggleIcon = document.getElementById('toggleIcon');
    const toggleLabel = document.getElementById('toggleLabel');
    const loginButton = document.getElementById('loginButton');
    const registerButton = document.getElementById('registerButton');
    const title = document.getElementById('titleStroke');

    let w, h, particles = [];

    const themes = {
      morning: {
        label: '夜モードへ',
        icon: '🌙',
        gradient: 'linear-gradient(to top, #bfe9ff, #e8faff, #ffffff)',
        textColor: '#226699',
        btnText: '#204c70',
        btnBg: 'rgba(255,255,255,0.2)',
        btnBorder: 'rgba(0,0,0,0.15)',
        count: 35,
        size: [45, 95],
        speed: [0.15, 0.5],
        alpha: [0.35, 0.55],
        sway: 0.8,
        glow: 25
      },
      dawn: {
        label: '朝モードへ',
        icon: '🌅',
        gradient: 'linear-gradient(to top, #0d1b2a, #1b263b, #415a77, #778da9)',
        textColor: '#ffffff',
        btnText: '#2d2d2d',
        btnBg: 'linear-gradient(to right, #ffe56c, #fff0a6)',
        btnBorder: 'rgba(255,255,255,0.5)',
        count: 50,
        size: [3, 7],
        speed: [0.05, 0.25],
        alpha: [0.4, 0.8],
        sway: 0.5,
        glow: 15
      }
    };

    function getCurrentTheme() {
      const hour = new Date().getHours();
      return (hour >= 5 && hour < 16) ? 'morning' : 'dawn';
    }

    let currentTheme = getCurrentTheme();
    applyTheme(currentTheme);

    function resize() {
      w = canvas.width = window.innerWidth;
      h = canvas.height = window.innerHeight;
      const t = themes[currentTheme];
      particles = Array.from({ length: t.count }, () => ({
        x: Math.random() * w,
        y: Math.random() * h,
        r: Math.random() * (t.size[1] - t.size[0]) + t.size[0],
        s: Math.random() * (t.speed[1] - t.speed[0]) + t.speed[0],
        a: Math.random() * (t.alpha[1] - t.alpha[0]) + t.alpha[0],
        offset: Math.random() * Math.PI * 2
      }));
    }

    function draw() {
      ctx.clearRect(0, 0, w, h);
      const t = themes[currentTheme];
      const time = Date.now() / 1000;

      for (const p of particles) {
        const swayX = Math.sin(time * 0.5 + p.offset) * t.sway * 20;
        const gradient = ctx.createRadialGradient(p.x + swayX, p.y, 0, p.x + swayX, p.y, p.r);
        gradient.addColorStop(0, `rgba(255,255,255,${p.a})`);
        gradient.addColorStop(1, `rgba(255,255,255,0)`);

        ctx.shadowBlur = t.glow;
        ctx.shadowColor = `rgba(255,255,255,${p.a})`;
        ctx.fillStyle = gradient;
        ctx.beginPath();
        ctx.arc(p.x + swayX, p.y, p.r, 0, Math.PI * 2);
        ctx.fill();

        p.y -= p.s;
        if (p.y + p.r < 0) {
          p.y = h + p.r;
          p.x = Math.random() * w;
        }
      }

      ctx.shadowBlur = 0;
      requestAnimationFrame(draw);
    }

    function applyTheme(name) {
      const t = themes[name];
      container.style.background = t.gradient;
      title.style.webkitTextStroke = `1.6px ${t.textColor}`;

      // 🎨 ボタン反映
      [loginButton, registerButton].forEach(btn => {
        btn.style.background = t.btnBg;
        btn.style.color = t.btnText;
        btn.style.borderColor = t.btnBorder;
      });

      toggleLabel.textContent = t.label;
      toggleIcon.textContent = t.icon;
      resize();
    }

    window.addEventListener('resize', resize);
    resize();
    draw();

    toggleBtn.addEventListener('click', () => {
      currentTheme = (currentTheme === 'morning') ? 'dawn' : 'morning';
      applyTheme(currentTheme);
    });
  </script>
</x-guest-layout>
