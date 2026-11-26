<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ZaminJob — Kirish')</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='10' fill='%23102c20'/%3E%3Cpath d='M9 11h22v4.4L16.2 26.6H31V31H9v-4.4L23.8 15.4H9V11z' fill='%23ffffff'/%3E%3Cpath d='M12 24.5c2.2-6 8.2-9.2 15.5-8.5-2.6 5.6-7.8 9.2-15.5 8.5z' fill='%2316a34a'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #15803d;
            --deep: #064e32;
            --bg: #f7f9f5;
            --ink: #102c20;
            --muted: #64746a;
            --line: #e2ebe3;
            --white: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--ink);
            font-family: "DM Sans", system-ui, sans-serif;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            animation: pageReveal .45s ease-out both;
        }
        @keyframes pageReveal {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        /* 60fps GPU Ambient Agro Background (Safari-safe, zero blur repaint) */
        .bg-ambient {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .bg-orb {
            position: absolute;
            border-radius: 50%;
            will-change: transform;
        }
        .bg-orb.o1 {
            width: 680px;
            height: 680px;
            top: -220px;
            left: -180px;
            background: radial-gradient(circle, rgba(22, 163, 74, .14) 0%, rgba(22, 163, 74, .04) 45%, transparent 72%);
            animation: orbFloatA 22s ease-in-out infinite alternate;
        }
        .bg-orb.o2 {
            width: 620px;
            height: 620px;
            right: -160px;
            bottom: -200px;
            background: radial-gradient(circle, rgba(6, 78, 50, .13) 0%, rgba(16, 185, 129, .05) 45%, transparent 72%);
            animation: orbFloatB 26s ease-in-out infinite alternate;
        }
        .bg-orb.o3 {
            width: 460px;
            height: 460px;
            left: 42%;
            top: 28%;
            background: radial-gradient(circle, rgba(163, 230, 53, .11) 0%, rgba(22, 163, 74, .03) 48%, transparent 72%);
            animation: orbFloatC 20s ease-in-out infinite alternate;
        }
        .bg-leaf {
            position: absolute;
            opacity: .075;
            will-change: transform;
        }
        .bg-leaf.l1 { top: 18%; left: 6%; width: 120px; animation: leafDriftA 18s ease-in-out infinite alternate; }
        .bg-leaf.l2 { bottom: 14%; right: 7%; width: 140px; animation: leafDriftB 21s ease-in-out infinite alternate; }
        @keyframes orbFloatA {
            0% { transform: translate3d(0, 0, 0) scale(1); }
            100% { transform: translate3d(90px, 65px, 0) scale(1.08); }
        }
        @keyframes orbFloatB {
            0% { transform: translate3d(0, 0, 0) scale(1); }
            100% { transform: translate3d(-85px, -60px, 0) scale(1.1); }
        }
        @keyframes orbFloatC {
            0% { transform: translate3d(0, 0, 0) scale(1); }
            100% { transform: translate3d(-55px, 50px, 0) scale(1.06); }
        }
        @keyframes leafDriftA {
            0% { transform: translate3d(0, 0, 0) rotate(-8deg); }
            100% { transform: translate3d(26px, -22px, 0) rotate(10deg); }
        }
        @keyframes leafDriftB {
            0% { transform: translate3d(0, 0, 0) rotate(12deg); }
            100% { transform: translate3d(-24px, -20px, 0) rotate(-6deg); }
        }
        .auth-main { position: relative; z-index: 1; }
        a { text-decoration: none; color: inherit; }

        .top-nav {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 20;
        }
        .top-inner {
            width: min(1600px, calc(100% - 64px));
            margin: auto;
            height: 74px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .nav-right { display: flex; align-items: center; gap: 10px; }

        .btn {
            position: relative;
            overflow: hidden;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 999px;
            font: 700 13px "DM Sans", sans-serif;
            border: 0;
            cursor: pointer;
            transition: transform .25s cubic-bezier(.22,1,.36,1), color .5s ease, border-color .5s ease, box-shadow .25s ease;
        }
        .btn > span, .btn > svg { position: relative; z-index: 2; }
        .btn:hover { transform: translateY(-1px); }
        .btn-green {
            background: linear-gradient(135deg, #168746, #075a35);
            color: #fff;
            box-shadow: 0 8px 20px rgba(21, 128, 61, 0.22);
        }
        .btn-outline {
            background: #fff;
            color: var(--ink);
            border: 1px solid #cbd9ce;
        }

        /* Suvli to'lqin hover animatsiyasi — faqat sichqoncha borganida chiqadi */
        .btn-wave::before,
        .btn-wave::after {
            content: "";
            position: absolute;
            left: 50%;
            top: 132%;
            width: 380px;
            height: 380px;
            margin-left: -190px;
            border-radius: 42% 48% 45% 40%;
            transform: rotate(0deg);
            transition: top 1.15s cubic-bezier(.4, 0, .2, 1);
            pointer-events: none;
            z-index: 0;
        }
        .btn-wave::before {
            background: rgba(255, 255, 255, .20);
            animation: liquidSpin 7.5s linear infinite paused;
        }
        .btn-wave::after {
            border-radius: 46% 40% 44% 48%;
            background: rgba(16, 185, 129, .38);
            animation: liquidSpin 10s linear infinite reverse paused;
            transition-duration: 1.35s;
        }
        .btn-wave:hover::before {
            top: -70%;
            animation-play-state: running;
        }
        .btn-wave:hover::after {
            top: -58%;
            animation-play-state: running;
        }
        .btn-outline.btn-wave::before { background: rgba(22, 135, 70, .88); }
        .btn-outline.btn-wave::after { background: rgba(7, 90, 53, .95); }
        .btn-outline.btn-wave:hover {
            color: #fff;
            border-color: #075a35;
            box-shadow: 0 10px 24px rgba(21, 128, 61, 0.22);
        }
        @keyframes liquidSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .auth-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 36px 24px;
        }
        .auth-shell {
            width: min(1140px, 100%);
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 40px;
            align-items: center;
            transition: opacity .24s ease, transform .24s ease;
        }
        .auth-shell.leaving {
            opacity: 0;
            transform: translate3d(0, 14px, 0) scale(.985);
        }

        /* Chap tomondagi ZaminJob vitrina paneli */
        .auth-showcase {
            background: linear-gradient(145deg, #0b462e 0%, #102c20 100%);
            color: #f3faf5;
            border-radius: 26px;
            padding: 38px 36px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 24px 55px rgba(16, 44, 32, 0.16);
            animation: slideLeft .7s cubic-bezier(.22, 1, .36, 1) both;
        }
        .auth-showcase::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .10);
            right: -120px;
            top: -120px;
            box-shadow: 0 0 0 40px rgba(255, 255, 255, .03);
            pointer-events: none;
        }
        .sc-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 13px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .11);
            color: #bbf7d0;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 18px;
        }
        .auth-showcase h2 {
            font: 800 clamp(26px, 2.7vw, 34px)/1.15 Manrope, sans-serif;
            letter-spacing: -1.1px;
            margin: 0 0 12px;
            color: #ffffff;
        }
        .sc-desc {
            font-size: 14px;
            line-height: 1.65;
            color: #cde3d4;
            margin: 0 0 26px;
        }
        .sc-list {
            display: grid;
            gap: 14px;
            margin-bottom: 28px;
        }
        .sc-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 13px 15px;
            border-radius: 15px;
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .09);
        }
        .sc-ic {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(34, 197, 94, .18);
            color: #86efac;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }
        .sc-item strong {
            display: block;
            font-size: 14px;
            color: #fff;
            margin-bottom: 2px;
        }
        .sc-item span {
            font-size: 12.5px;
            color: #bed6c6;
            line-height: 1.45;
        }
        .sc-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, .14);
        }
        .sc-stats div strong {
            display: block;
            font: 800 20px Manrope, sans-serif;
            color: #86efac;
        }
        .sc-stats div small {
            font-size: 11.5px;
            color: #bed6c6;
        }

        .auth-card {
            width: 100%;
            max-width: 470px;
            justify-self: center;
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 36px 34px;
            box-shadow: 0 22px 52px rgba(16, 44, 32, 0.08);
            animation: slideRight .7s cubic-bezier(.22, 1, .36, 1) .06s both;
        }
        @keyframes slideLeft {
            from { opacity: 0; transform: translate3d(-36px, 0, 0); }
            to { opacity: 1; transform: translate3d(0, 0, 0); }
        }
        @keyframes slideRight {
            from { opacity: 0; transform: translate3d(36px, 0, 0); }
            to { opacity: 1; transform: translate3d(0, 0, 0); }
        }
        .auth-card h1 {
            font: 800 26px/1.2 Manrope, sans-serif;
            letter-spacing: -0.8px;
            margin: 0 0 6px;
        }
        .auth-sub { color: var(--muted); font-size: 14px; margin: 0 0 24px; }
        .field { margin-bottom: 16px; }
        .field label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #244032;
        }
        .field input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid #d5e2d7;
            background: #fbfdfb;
            font: 500 14px "DM Sans", sans-serif;
            color: var(--ink);
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .field input:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 4px rgba(21, 128, 61, 0.12);
            background: #fff;
        }
        .role-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .role-opt { cursor: pointer; display: block; }
        .role-opt input { display: none; }
        .role-box {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 11px 13px;
            border-radius: 13px;
            border: 1.5px solid #d5e2d7;
            background: #fbfdfb;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }
        .role-box strong { font-size: 13.5px; color: var(--ink); }
        .role-box small { font-size: 11.5px; color: var(--muted); }
        .role-opt input:checked + .role-box {
            border-color: var(--green);
            background: #eef8f0;
            box-shadow: 0 0 0 3px rgba(21, 128, 61, 0.12);
        }
        .at-wrap { position: relative; display: flex; align-items: center; }
        .at-sign { position: absolute; left: 14px; font-weight: 700; color: var(--muted); font-size: 14px; pointer-events: none; }
        .at-wrap input { padding-left: 30px; }
        .pw-wrap { position: relative; display: flex; align-items: center; }
        .pw-wrap input { padding-right: 44px; }
        .pw-eye {
            position: absolute; right: 10px; width: 32px; height: 32px; border: 0;
            background: transparent; color: var(--muted); border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center; cursor: pointer;
            transition: color .2s, background .2s;
        }
        .pw-eye:hover { color: var(--green); background: #eef6ef; }
        .gh-av {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid #d5e2d7;
            background: #102c20;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font: 800 14px Manrope, sans-serif;
            overflow: hidden;
            transition: border-color .2s, transform .2s;
        }
        .gh-av:hover { border-color: var(--green); transform: scale(1.04); }
        .err { color: #dc2626; font-size: 12px; margin-top: 5px; display: block; }
        .check-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 8px 0 20px;
            font-size: 13px;
            color: var(--muted);
        }
        .check-row label { display: inline-flex; align-items: center; gap: 7px; cursor: pointer; }
        .auth-foot {
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid var(--line);
            text-align: center;
            font-size: 13px;
            color: var(--muted);
        }
        .auth-foot a { color: var(--green); font-weight: 700; }

        @media (max-width: 920px) {
            .top-inner { width: calc(100% - 32px); }
            .auth-shell { grid-template-columns: 1fr; gap: 24px; }
            .auth-showcase { order: 2; padding: 26px 22px; }
            .auth-card { order: 1; padding: 28px 22px; }
        }
        @media (max-width: 560px) {
            .nav-right .hide-sm { display: none; }
        }
    </style>
</head>
<body>
    <div class="bg-ambient" aria-hidden="true">
        <div class="bg-orb o1"></div>
        <div class="bg-orb o2"></div>
        <div class="bg-orb o3"></div>
        <svg class="bg-leaf l1" viewBox="0 0 120 120" fill="none"><path d="M20 96C26 48 60 22 104 20C96 64 68 98 20 96Z" fill="#15803D"/><path d="M24 92C52 68 76 46 98 26" stroke="#f7f9f5" stroke-width="3" stroke-linecap="round"/></svg>
        <svg class="bg-leaf l2" viewBox="0 0 120 120" fill="none"><path d="M16 88C34 40 70 18 108 26C92 70 60 98 16 88Z" fill="#064E32"/><path d="M22 84C50 62 76 44 100 30" stroke="#f7f9f5" stroke-width="3" stroke-linecap="round"/></svg>
    </div>
    <header class="top-nav">
        <div class="top-inner">
            <a href="{{ url('/') }}" aria-label="ZaminJob" style="display:inline-flex;align-items:center">
                <svg width="160" height="38" viewBox="0 0 206 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 10H31.5V15.6L11.4 33.4H32V39H0.5V33.4L20.6 15.6H1V10Z" fill="#102C20"/>
                    <path d="M5.5 29.8C8.6 21.8 17.4 17.5 29.2 18.2C25.4 26.2 17.8 31.2 5.5 29.8Z" fill="#15803D"/>
                    <path d="M7.5 28.6C13.5 24.4 20.2 21.4 26.8 19.6" stroke="#F7F9F5" stroke-width="1.4" stroke-linecap="round"/>
                    <text x="33.5" y="38.5" fill="#102C20" font-family="Manrope, 'DM Sans', sans-serif" font-weight="800" font-size="34" letter-spacing="-1.3">aminJob</text>
                </svg>
            </a>
            <div class="nav-right">
                <a class="btn btn-outline btn-wave hide-sm" href="{{ route('bozor') }}"><span>Mahsulotlar</span></a>
                @guest
                    <a class="btn btn-outline btn-wave" href="{{ route('login') }}" data-auth-nav><span>Kirish</span></a>
                    @if (Route::has('register'))
                        <a class="btn btn-green btn-wave" href="{{ route('register') }}" data-auth-nav><span>Ro‘yxatdan o‘tish</span></a>
                    @endif
                @else
                    <a class="btn btn-green btn-wave" href="{{ route('kabinet') }}"><span>Kabinet</span></a>
                    <a class="btn btn-outline btn-wave" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span>Chiqish</span></a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
                @endguest
            </div>
        </div>
    </header>

    <main class="auth-main">
        <div class="auth-shell" id="authShell">
            <aside class="auth-showcase">
                <div class="sc-badge">O‘zbekiston agro-platformasi</div>
                <h2>Ish, ulgurji savdo va eksport — yagona maydonda</h2>
                <p class="sc-desc">Telefon raqamingiz orqali bir daqiqada ro‘yxatdan o‘ting va mamlakat bo‘ylab fermerlar, mutaxassislar hamda xaridorlar bilan to‘g‘ridan-to‘g‘ri bog‘laning.</p>

                <div class="sc-list">
                    <div class="sc-item">
                        <div class="sc-ic">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                        <div>
                            <strong>AI aqlli qidiruv va filtrlar</strong>
                            <span>Kasb, viloyat, maosh va o‘xshash yo‘nalishlar bo‘yicha eng mos vakansiya va ishchilarni toping.</span>
                        </div>
                    </div>
                    <div class="sc-item">
                        <div class="sc-ic">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div>
                            <strong>Agro-Pasport ishonch tizimi</strong>
                            <span>Tasdiqlangan profillar, reytinglar va shaffof hamkorlik tarixi.</span>
                        </div>
                    </div>
                    <div class="sc-item">
                        <div class="sc-ic">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17h4V5H2v12h3"/><path d="M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                        </div>
                        <div>
                            <strong>20 tonnalik eksport konsolidatsiya</strong>
                            <span>Hosilni Qozog‘iston, Rossiya va BAA bozorlariga yuqori narxda chiqarish imkoniyati.</span>
                        </div>
                    </div>
                </div>

                <div class="sc-stats">
                    <div><strong>25 000+</strong><small>Foydalanuvchi</small></div>
                    <div><strong>12 000+</strong><small>Faol e’lonlar</small></div>
                    <div><strong>14</strong><small>Hudud qamrovi</small></div>
                </div>
            </aside>

            @yield('content')
        </div>
    </main>

    <script>
        // +998 telefon raqami avtomatik maskasi (o'chirib yuborishdan himoyalangan)
        document.querySelectorAll('input[data-uz-phone]').forEach(input => {
            const formatUzPhone = (val) => {
                let digits = val.replace(/\D/g, '');
                if (digits.startsWith('998')) digits = digits.slice(3);
                digits = digits.slice(0, 9);
                let out = '+998 ';
                if (digits.length > 0) out += digits.slice(0, 2);
                if (digits.length > 2) out += ' ' + digits.slice(2, 5);
                if (digits.length > 5) out += ' ' + digits.slice(5, 7);
                if (digits.length > 7) out += ' ' + digits.slice(7, 9);
                return out;
            };
            input.value = formatUzPhone(input.value);
            input.addEventListener('input', () => {
                input.value = formatUzPhone(input.value);
            });
            input.addEventListener('keydown', e => {
                if ((e.key === 'Backspace' || e.key === 'Delete') && input.selectionStart <= 5 && input.selectionEnd <= 5) {
                    e.preventDefault();
                }
            });
            input.addEventListener('focus', () => {
                if (!input.value.startsWith('+998 ')) input.value = formatUzPhone(input.value);
                requestAnimationFrame(() => {
                    const len = input.value.length;
                    input.setSelectionRange(len, len);
                });
            });
        });

        // Kirish maydoni: raqam yozilsa +998 formatlaydi, harf (masalan: mnodir) yozilsa tegmaydi
        document.querySelectorAll('input[data-uz-login]').forEach(input => {
            const formatUzPhone = (val) => {
                let digits = val.replace(/\D/g, '');
                if (digits.startsWith('998')) digits = digits.slice(3);
                digits = digits.slice(0, 9);
                let out = '+998 ';
                if (digits.length > 0) out += digits.slice(0, 2);
                if (digits.length > 2) out += ' ' + digits.slice(2, 5);
                if (digits.length > 5) out += ' ' + digits.slice(5, 7);
                if (digits.length > 7) out += ' ' + digits.slice(7, 9);
                return out;
            };
            input.addEventListener('input', () => {
                const v = input.value;
                if (v === '') return;
                if (/^[a-zA-Z]/.test(v)) return;
                if (/^[\d+]/.test(v)) {
                    input.value = formatUzPhone(v);
                }
            });
        });

        // Jonli @username band/bo'sh tekshiruvi (avtomatik yashil/qizil)
        document.querySelectorAll('input[data-live-username]').forEach(input => {
            const st = document.getElementById('usernameStatus');
            if (!st) return;
            let tmr = 0;
            input.addEventListener('input', () => {
                clearTimeout(tmr);
                const val = input.value.replace(/^@+/, '').trim().toLowerCase();
                if (!val) { st.style.display = 'none'; return; }
                st.style.display = 'block';
                st.style.color = '#64746a';
                st.textContent = 'Tekshirilmoqda...';
                tmr = setTimeout(() => {
                    fetch('/api/check-username?u=' + encodeURIComponent(val))
                        .then(r => r.json())
                        .then(d => {
                            st.style.display = 'block';
                            st.textContent = d.message || '';
                            if (d.status === 'free') {
                                st.style.color = '#15803d';
                                input.style.borderColor = '#16a34a';
                            } else if (d.status === 'taken') {
                                st.style.color = '#dc2626';
                                input.style.borderColor = '#dc2626';
                            } else {
                                st.style.color = '#b45309';
                                input.style.borderColor = '#d5e2d7';
                            }
                        })
                        .catch(() => { st.style.display = 'none'; });
                }, 280);
            });
        });

        // Parolni ko'rsatish / yashirish tugmasi
        document.querySelectorAll('[data-toggle-pw]').forEach(btn => {
            btn.addEventListener('click', () => {
                const inp = document.getElementById(btn.dataset.togglePw);
                if (!inp) return;
                const show = inp.type === 'password';
                inp.type = show ? 'text' : 'password';
                btn.setAttribute('aria-label', show ? 'Parolni yashirish' : 'Parolni ko‘rsatish');
                btn.innerHTML = show
                    ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
                    : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
            });
        });

        // Kamida 8 talik parol jonli tekshiruvi
        document.querySelectorAll('input[data-min8]').forEach(inp => {
            const hint = document.getElementById('pwMinHint');
            if (!hint) return;
            inp.addEventListener('input', () => {
                const len = inp.value.length;
                if (!len) { hint.style.color = '#64746a'; hint.textContent = 'Kamida 8 ta belgi kiriting'; return; }
                if (len >= 8) {
                    hint.style.color = '#15803d';
                    hint.textContent = 'Parol uzunligi yetarli (' + len + ' ta belgi)';
                    inp.style.borderColor = '#16a34a';
                } else {
                    hint.style.color = '#dc2626';
                    hint.textContent = 'Kamida 8 ta belgi bo‘lishi kerak (' + len + '/8)';
                    inp.style.borderColor = '#dc2626';
                }
            });
        });

        // Kirish <-> Ro'yxatdan o'tish sahifalari orasida silliq o'tish animatsiyasi
        document.querySelectorAll('a[data-auth-nav]').forEach(link => {
            link.addEventListener('click', e => {
                const href = link.getAttribute('href');
                if (!href || href === window.location.href) return;
                e.preventDefault();
                const shell = document.getElementById('authShell');
                if (shell) shell.classList.add('leaving');
                setTimeout(() => { window.location.href = href; }, 220);
            });
        });
    </script>
</body>
</html>
