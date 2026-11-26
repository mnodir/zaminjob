<!DOCTYPE html>
<html lang="uz">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ZaminJob — Admin Panel</title>
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='10' fill='%23102c20'/%3E%3Cpath d='M9 11h22v4.4L16.2 26.6H31V31H9v-4.4L23.8 15.4H9V11z' fill='%23ffffff'/%3E%3Cpath d='M12 24.5c2.2-6 8.2-9.2 15.5-8.5-2.6 5.6-7.8 9.2-15.5 8.5z' fill='%2316a34a'/%3E%3C/svg%3E">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root {
      --bg: #f5f8f5;
      --surface: #ffffff;
      --ink: #102c20;
      --muted: #64746a;
      --line: #e2ebe3;
      --green: #15803d;
      --deep: #092f22;
      --soft: #eaf5ec;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      background: var(--bg);
      color: var(--ink);
      font-family: "DM Sans", system-ui, sans-serif;
      display: grid;
      grid-template-columns: 260px 1fr;
      min-height: 100vh;
    }
    a { text-decoration: none; color: inherit; }
    button, input, select { font: inherit; }

    /* Sidebar */
    aside {
      background: var(--deep);
      color: #d8e8dc;
      padding: 24px 18px;
      display: flex;
      flex-direction: column;
      gap: 24px;
      position: sticky;
      top: 0;
      height: 100vh;
    }
    .side-nav { display: flex; flex-direction: column; gap: 6px; }
    .side-nav button, .side-nav a {
      display: flex;
      align-items: center;
      gap: 11px;
      padding: 11px 14px;
      border-radius: 12px;
      border: 0;
      background: transparent;
      color: #b7cdbe;
      font-size: 13.5px;
      font-weight: 600;
      cursor: pointer;
      text-align: left;
      transition: background .18s, color .18s;
    }
    .side-nav button:hover, .side-nav a:hover, .side-nav button.on {
      background: rgba(255, 255, 255, 0.11);
      color: #fff;
    }
    .side-nav i { width: 17px; height: 17px; }
    .side-foot {
      margin-top: auto;
      padding-top: 16px;
      border-top: 1px solid rgba(255, 255, 255, 0.12);
      display: grid;
      gap: 8px;
    }

    /* Main */
    main { padding: 28px 34px 48px; overflow-x: hidden; }
    .top-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
      margin-bottom: 24px;
    }
    .top-bar h1 {
      font: 800 26px Manrope, sans-serif;
      letter-spacing: -0.8px;
      margin: 0;
    }
    .top-bar p { margin: 4px 0 0; color: var(--muted); font-size: 13.5px; }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 10px 16px;
      border-radius: 11px;
      border: 0;
      background: var(--green);
      color: #fff;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      transition: transform .15s, background .2s;
    }
    .btn:hover { background: #0f6830; transform: translateY(-1px); }
    .btn.ghost { background: #fff; color: var(--ink); border: 1px solid var(--line); }
    .btn.ghost:hover { border-color: var(--green); color: var(--green); }
    .btn.sm { padding: 6px 11px; font-size: 12px; border-radius: 8px; }
    .btn i { width: 15px; height: 15px; }

    /* KPIs */
    .kpis {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 24px;
    }
    .kpi {
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: 18px;
      padding: 20px;
      box-shadow: 0 6px 22px rgba(16, 44, 32, 0.04);
      animation: glideRight .62s cubic-bezier(.16, 1, .3, 1) calc(var(--i, 0) * 65ms) both;
      transition: transform .28s cubic-bezier(.16, 1, .3, 1), box-shadow .28s ease, border-color .24s ease;
      will-change: transform, opacity;
    }
    .kpi:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(16, 44, 32, 0.08); border-color: #c6e2cc; }
    @keyframes glideRight {
      from { opacity: 0; transform: translate3d(26px, 0, 0) scale(.985); }
      to { opacity: 1; transform: translate3d(0, 0, 0) scale(1); }
    }
    @keyframes glideLeft {
      from { opacity: 0; transform: translate3d(-22px, 0, 0); }
      to { opacity: 1; transform: translate3d(0, 0, 0); }
    }
    .side-nav button, .side-nav a {
      animation: glideLeft .54s cubic-bezier(.16, 1, .3, 1) calc(var(--i, 0) * 45ms) both;
    }
    .side-nav button:hover, .side-nav a:hover { transform: translate3d(4px, 0, 0); }
    .kpi-top { display: flex; justify-content: space-between; align-items: center; color: var(--muted); font-size: 12.5px; font-weight: 600; }
    .kpi-top i { width: 18px; color: var(--green); }
    .kpi strong { display: block; font: 800 28px Manrope, sans-serif; margin: 10px 0 4px; letter-spacing: -1px; }
    .kpi small { color: var(--green); font-size: 12px; font-weight: 700; }

    /* Live Agro-Weather & FX Strip */
    .agro-bar {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 18px;
      padding: 14px 18px;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      flex-wrap: wrap;
      box-shadow: 0 6px 22px rgba(16, 44, 32, 0.04);
      animation: glideRight .64s cubic-bezier(.16, 1, .3, 1) both;
    }
    .agro-weather-main { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .agro-temp { font: 800 24px Manrope, sans-serif; color: var(--ink); letter-spacing: -0.6px; }
    .agro-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 11px; border-radius: 999px; background: var(--soft); color: var(--green); font-size: 12px; font-weight: 700; }
    .agro-sel { padding: 6px 10px; border-radius: 10px; border: 1px solid var(--line); background: #f8fbf8; font-size: 12.5px; font-weight: 700; color: var(--ink); cursor: pointer; }

    /* Panels */
    .panel {
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: 18px;
      padding: 20px 22px;
      box-shadow: 0 6px 22px rgba(16, 44, 32, 0.04);
      margin-bottom: 20px;
      animation: glideRight .66s cubic-bezier(.16, 1, .3, 1) both;
      will-change: transform, opacity;
    }
    tbody tr {
      animation: glideRight .48s cubic-bezier(.16, 1, .3, 1) calc(var(--r, 0) * 40ms) both;
      transition: background .2s ease;
    }
    tbody tr:hover { background: #f7fbf8; }
    .tab-pane.on { display: block; animation: glideRight .42s cubic-bezier(.16, 1, .3, 1) both; }
    .panel-hd {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 16px;
    }
    .panel-hd h2 { font: 800 17px Manrope, sans-serif; margin: 0; }

    .tbl-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    th, td { text-align: left; padding: 12px 12px; border-bottom: 1px solid var(--line); white-space: nowrap; }
    tr:last-child td { border-bottom: 0; }
    th { font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); font-weight: 700; background: #f8fbf8; }

    .tag {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 3px 10px;
      border-radius: 999px;
      font-size: 11.5px;
      font-weight: 700;
      background: var(--soft);
      color: var(--green);
    }
    .tag.warn { background: #fef3c7; color: #b45309; }
    .tag.dark { background: var(--deep); color: #fff; }
    .pb { width: 110px; height: 7px; border-radius: 99px; background: var(--soft); overflow: hidden; display: inline-block; vertical-align: middle; margin-right: 8px; }
    .pb i { display: block; height: 100%; background: var(--green); border-radius: 99px; transition: width .35s; }
    .price-inp {
      width: 96px;
      padding: 5px 8px;
      border: 1px solid var(--line);
      border-radius: 8px;
      font-weight: 700;
      font-size: 13px;
    }

    .tab-pane { display: none; }
    .tab-pane.on { display: block; }

    #toast {
      position: fixed;
      right: 22px;
      bottom: 22px;
      background: var(--deep);
      color: #fff;
      padding: 12px 18px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 600;
      box-shadow: 0 14px 36px rgba(0, 0, 0, .2);
      display: none;
      z-index: 50;
    }

    @media (max-width: 980px) {
      body { grid-template-columns: 1fr; }
      aside { height: auto; position: static; }
      .kpis { grid-template-columns: 1fr 1fr; }
      main { padding: 20px 16px; }
    }
  </style>
</head>
<body>
  <aside>
    <a href="{{ url('/') }}" aria-label="ZaminJob" style="display:inline-flex;align-items:center">
      <svg width="156" height="36" viewBox="0 0 206 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1 10H31.5V15.6L11.4 33.4H32V39H0.5V33.4L20.6 15.6H1V10Z" fill="#FFFFFF"/>
        <path d="M5.5 29.8C8.6 21.8 17.4 17.5 29.2 18.2C25.4 26.2 17.8 31.2 5.5 29.8Z" fill="#22C55E"/>
        <path d="M7.5 28.6C13.5 24.4 20.2 21.4 26.8 19.6" stroke="#092F22" stroke-width="1.4" stroke-linecap="round"/>
        <text x="33.5" y="38.5" fill="#FFFFFF" font-family="Manrope, 'DM Sans', sans-serif" font-weight="800" font-size="34" letter-spacing="-1.3">aminJob</text>
      </svg>
    </a>

    <nav class="side-nav" id="sideNav">
      <button class="on" data-tab="overview"><i data-lucide="layout-dashboard"></i> Boshqaruv paneli</button>
      <button data-tab="export"><i data-lucide="globe"></i> Eksport 20t Furalar</button>
      <button data-tab="trucks"><i data-lucide="truck"></i> Fura &amp; Bo‘sh qaytish</button>
      <button data-tab="verify"><i data-lucide="shield-check"></i> Agro-Pasportlar</button>
      <button data-tab="prices"><i data-lucide="trending-up"></i> Narx Radari (so‘m)</button>
      <button data-tab="leads"><i data-lucide="sparkles"></i> ZaminJob AI Lidlar</button>
    </nav>

    <div class="side-foot">
      <a href="{{ url('/') }}" class="btn ghost" style="justify-content:center;background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.15)">
        <i data-lucide="arrow-left"></i> Saytga qaytish
      </a>
      @auth
      <a href="{{ route('logout') }}" class="btn ghost" style="justify-content:center;background:transparent;color:#fca5a5;border-color:rgba(252,165,165,.25)"
         onclick="event.preventDefault(); document.getElementById('admin-logout').submit();">
        <i data-lucide="log-out"></i> Chiqish ({{ Auth::user()->name }})
      </a>
      <form id="admin-logout" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
      @endauth
    </div>
  </aside>

  <main>
    <div class="top-bar">
      <div>
        <h1>ZaminJob Admin Panel</h1>
        <p>Eksport 20t konsolidatsiya, bo‘sh qaytish furalari, Agro-Pasport verifikatsiya va bozor narxlari boshqaruvi.</p>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <a class="btn ghost" href="{{ route('profil') }}"><i data-lucide="user"></i> Profilim</a>
        <a class="btn ghost" href="{{ route('bozor') }}"><i data-lucide="shopping-bag"></i> Mahsulot qidirish</a>
        <a class="btn ghost" href="{{ url('/') }}"><i data-lucide="external-link"></i> Bosh sahifa</a>
        <button class="btn" onclick="addDemoLot()"><i data-lucide="plus"></i> Yangi eksport lot</button>
      </div>
    </div>

    <!-- REAL-VAQT AGRO OB-HAVO VA EKSPORT VALYUTA PANELI -->
    <div class="agro-bar">
      <div class="agro-weather-main">
        <select id="wxRegion" class="agro-sel" onchange="loadAgroWeather()" aria-label="Hudud">
          <option value="40.1158,67.8422,Jizzax">Jizzax</option>
          <option value="41.2995,69.2401,Toshkent">Toshkent</option>
          <option value="40.3842,71.7843,Farg‘ona">Farg‘ona</option>
          <option value="39.6542,66.9597,Samarqand">Samarqand</option>
          <option value="37.2242,67.2783,Surxondaryo">Surxondaryo</option>
        </select>
        <div>
          <div style="display:flex;align-items:baseline;gap:8px">
            <span class="agro-temp" id="wxTemp">--°C</span>
            <span id="wxDesc" style="font-size:12.5px;font-weight:700;color:var(--muted)">Ob-havo yuklanmoqda...</span>
          </div>
          <div id="wxMeta" style="font-size:11.5px;color:var(--muted)">Shamol: -- km/s · Namlik: --%</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <span class="agro-pill" id="wxTip">AI Agro-Tavsiya tayyorlanmoqda...</span>
        <span class="agro-pill" style="background:#f1f5f9;color:#0f172a" id="fxRate">1 USD ≈ 12 850 so‘m</span>
        <span class="agro-pill" style="background:#fef3c7;color:#92400e" id="uzClock">--:--:--</span>
      </div>
    </div>

    <div class="kpis">
      <div class="kpi" style="--i:0">
        <div class="kpi-top"><span>Foydalanuvchilar</span><i data-lucide="users"></i></div>
        <strong>{{ $usersCount }}</strong>
        <small>{{ $listingsCount ?? 0 }} ta bozor e’loni · {{ $vacanciesCount ?? 0 }} ta vakansiya</small>
      </div>
      <div class="kpi" style="--i:1">
        <div class="kpi-top"><span>Yig‘ilayotgan eksport</span><i data-lucide="package"></i></div>
        <strong id="kpi-tons">60 / 80 t</strong>
        <small>4 ta 20t fura ochiq</small>
      </div>
      <div class="kpi" style="--i:2">
        <div class="kpi-top"><span>Bo‘sh furalar</span><i data-lucide="truck"></i></div>
        <strong id="kpi-trucks">4 ta</strong>
        <small>-35% bo‘sh qaytish mavjud</small>
      </div>
      <div class="kpi" style="--i:3">
        <div class="kpi-top"><span>ZaminJob AI Lidlar</span><i data-lucide="sparkles"></i></div>
        <strong id="kpi-leads">4 ta</strong>
        <small>EN/RU/KO → UZ avto-tarjima</small>
      </div>
    </div>

    <div class="panel" style="background:linear-gradient(135deg,#0b3b27,#102c20);color:#fff;border-color:#14532d">
      <div class="panel-hd" style="margin-bottom:10px">
        <h2 style="color:#fff;display:flex;align-items:center;gap:8px"><span style="width:8px;height:8px;border-radius:50%;background:#34d399;display:inline-block"></span> ZaminJob AI Eye · Real Vaqt Bozor Radari</h2>
        <span id="admAiClock" style="font-size:12px;color:#a7f3d0;font-weight:700">Yuklanmoqda...</span>
      </div>
      <div id="admAiPulse" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px"></div>
    </div>

    <!-- OVERVIEW + EXPORT -->
    <div class="tab-pane on" id="pane-overview">
      <div class="panel">
        <div class="panel-hd">
          <h2>Eksport 20t Fura Konsolidatsiyasi</h2>
          <span class="tag">Jonli boshqaruv</span>
        </div>
        <div class="tbl-wrap">
          <table>
            <thead>
              <tr><th>Mahsulot</th><th>Hudud</th><th>To‘lishi</th><th>Narxi (so‘m/kg)</th><th>Yo‘nalish</th><th>Amal</th></tr>
            </thead>
            <tbody id="tbl-lots"></tbody>
          </table>
        </div>
      </div>

      <div class="panel">
        <div class="panel-hd">
          <h2>Agro-Pasport Verifikatsiyasi (Fermer va Haydovchilar)</h2>
          <span class="tag warn">Tasdiqlash kutilmoqda</span>
        </div>
        <div class="tbl-wrap">
          <table>
            <thead>
              <tr><th>Ism / Xo‘jalik</th><th>Yo‘nalish</th><th>Hudud</th><th>Holati</th><th>Amal</th></tr>
            </thead>
            <tbody id="tbl-verify"></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane" id="pane-export">
      <div class="panel">
        <div class="panel-hd"><h2>Barcha Eksport va Ichki Lotlar</h2></div>
        <div class="tbl-wrap">
          <table>
            <thead><tr><th>Mahsulot</th><th>Hudud</th><th>To‘lishi</th><th>Narxi</th><th>Yo‘nalish</th><th>Amal</th></tr></thead>
            <tbody id="tbl-lots-2"></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane" id="pane-trucks">
      <div class="panel">
        <div class="panel-hd"><h2>Fura va Bo‘sh Qaytish Reyslari (-35%)</h2></div>
        <div class="tbl-wrap">
          <table>
            <thead><tr><th>Transport</th><th>Yo‘nalish</th><th>Haydovchi</th><th>Narxi (so‘m)</th><th>Holati</th><th>Amal</th></tr></thead>
            <tbody id="tbl-trucks"></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane" id="pane-verify">
      <div class="panel">
        <div class="panel-hd"><h2>Agro-Pasportlar va Ro‘yxatdan O‘tganlar</h2></div>
        <div class="tbl-wrap">
          <table>
            <thead><tr><th>Ism / Xo‘jalik</th><th>Yo‘nalish</th><th>Hudud</th><th>Holati</th><th>Amal</th></tr></thead>
            <tbody id="tbl-verify-2"></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane" id="pane-prices">
      <div class="panel">
        <div class="panel-hd">
          <h2>Davlatlararo Narx Radari (1 kg so‘mda)</h2>
          <button class="btn sm" onclick="notify('Bozor narxlari yangilandi')"><i data-lucide="save"></i> Narxlarni saqlash</button>
        </div>
        <div class="tbl-wrap">
          <table>
            <thead><tr><th>Mahsulot</th><th>UZ (so‘m)</th><th>KZ (so‘m)</th><th>RU (so‘m)</th><th>BAA (so‘m)</th></tr></thead>
            <tbody id="tbl-prices"></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="tab-pane" id="pane-leads">
      <div class="panel">
        <div class="panel-hd">
          <div>
            <h2>ZaminJob AI Savdo Menejeri · Xalqaro Issiq So‘rovlar</h2>
            <p style="margin:4px 0 0;font-size:12.5px;color:var(--muted)">EN / RU / KO xaridorlardan kelgan so‘rovlar AI orqali avtomatik tarjima qilinadi, baholanadi va tayyor javob yoziladi.</p>
          </div>
          <button class="btn sm" onclick="simulateAiLead()"><i data-lucide="sparkles"></i> Yangi AI lid sinovi</button>
        </div>
        <div class="tbl-wrap">
          <table>
            <thead><tr><th>Mijoz va Davlat</th><th>Til</th><th>Asl xabar → O‘zbekcha tarjima</th><th>AI Baho / Qiymat</th><th>Holati</th><th>Amal</th></tr></thead>
            <tbody id="tbl-leads"></tbody>
          </table>
        </div>
        <div id="aiReplyBox" style="display:none;margin-top:16px;padding:16px;border-radius:14px;background:#f4faf5;border:1px solid #cce5d0">
          <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:8px">
            <strong id="aiReplyTitle" style="font-size:13.5px;color:#092f22">AI tayyorlagan tijoriy javob</strong>
            <button class="btn sm" onclick="copyAdminAiReply()">Javobni nusxalash</button>
          </div>
          <div id="aiReplyText" style="font-size:13px;line-height:1.55;color:#1f372a"></div>
        </div>
      </div>
    </div>
  </main>

  <div id="toast"></div>

  <script>
    const LOTS = [
      { id: 1, name: "Samarqand Kishmish uzumi", rg: "Samarqand", have: 16, need: 20, price: "17 200", dest: "BAA / Rossiya" },
      { id: 2, name: "Farg‘ona gilosi (26mm+)", rg: "Farg‘ona", have: 12, need: 20, price: "39 500", dest: "BAA / Koreya" },
      { id: 3, name: "Baxmal intensiv olmasi", rg: "Jizzax", have: 18, need: 20, price: "10 800", dest: "Qozog‘iston" },
      { id: 4, name: "Sherobod eksport anori", rg: "Surxondaryo", have: 14, need: 20, price: "14 500", dest: "BAA / Rossiya" }
    ];
    const TRUCKS = [
      { id: 1, kind: "20t Refrijerator · Volvo FH", route: "Samarqand → Olmaota", driver: "Shuhrat (4.9)", price: "23 500 000", booked: false },
      { id: 2, kind: "20t Refrijerator (-35% Bo‘sh qaytish)", route: "Farg‘ona → Chimkent", driver: "Dilshod (5.0)", price: "14 600 000", booked: false },
      { id: 3, kind: "Isuzu 5t (-30% Bo‘sh qaytish)", route: "Samarqand → Toshkent", driver: "Bobur (4.9)", price: "950 000", booked: false },
      { id: 4, kind: "Gazel Refrijerator 2t", route: "Jizzax → Toshkent", driver: "Azamat (4.8)", price: "650 000", booked: false }
    ];
    const VERIFY = [
      { id: 1, name: "«Sharq Bog‘i» F/X", role: "Fermer / Eksportchi", rg: "Samarqand", ok: true },
      { id: 2, name: "«Zarrin Hosil» (14 kishi)", role: "Terim brigadasi", rg: "Samarqand", ok: false },
      { id: 3, name: "Dilshod X. (Volvo FH 20t)", role: "Fura haydovchi", rg: "Farg‘ona", ok: false },
      { id: 4, name: "Rustam Karimov (MTZ-82)", role: "Traktorchi", rg: "Farg‘ona", ok: true }
    ];
    const PRICES = [
      { name: "Uzum (Kishmish)", uz: 17200, kz: 26800, ru: 33800, baa: 43300 },
      { name: "Gilos (26mm+)", uz: 39500, kz: 53500, ru: 68800, baa: 86700 },
      { name: "Issiqxona pomidori", uz: 13500, kz: 21000, ru: 27400, baa: 30600 },
      { name: "Baxmal olmasi", uz: 10800, kz: 15900, ru: 19100, baa: 24800 }
    ];
    const LEADS = [
      { id: 1, who: "Al-Futtaim Fresh (Dubay, BAA)", lang: "EN → UZ", score: "96% Issiq", value: "$42 000", msg: "20 tonna Kishmish uzumi kerak, +2°C refrijerator bilan", reply: "Hello Al-Futtaim Fresh! We have 20 MT Grade-A Samarkand Kishmish grapes ready for cold-chain (+2°C) delivery to Dubai in 6 days via ZaminJob Agro-Passport verified suppliers. Target FOB/CIF quote ready upon confirmation.", done: false },
      { id: 2, who: "Lotte Mart Agro (Seul, Koreya)", lang: "KO → UZ", score: "94% Issiq", value: "$68 500", msg: "Farg‘ona 26mm+ eksport gilosi 12 tonna, avia/refrijerator narxi?", reply: "Hello Lotte Mart Procurement Team! Fergana 26mm+ export cherries (12 MT) are available now with phytosanitary certification and cold-chain dispatch to Seoul. Wholesale export offer: $7.15/kg.", done: false },
      { id: 3, who: "Magnit Opt (Moskva, Rossiya)", lang: "RU → UZ", score: "89% O‘rta", value: "$31 200", msg: "Baxmal olmasi haftalik 40 tonna ulgurji narxi qancha?", reply: "Здравствуйте, Магнит Опт! Готовы поставлять еженедельно 40 тонн яблок Бахмал (1-й сорт) рефрижератором до Москвы за 5 дней. Ориентировочная цена: $1.52/кг. Все документы e-Fito и СТ-1 включены.", done: false },
      { id: 4, who: "«Quva Meva» F/X (O‘zbekiston)", lang: "UZ", score: "85% Tezkor", value: "18 500 000 so‘m", msg: "Farg‘onadan Toshkentga 5t bo‘sh Isuzu kerak", reply: "Assalomu alaykum! Farg‘ona → Toshkent yo‘nalishida 5t bo‘sh qaytish Isuzu tayyor (30% chegirma bilan). Haydovchi raqamini biriktirdik.", done: true }
    ];

    function notify(msg) {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.style.display = 'block';
      clearTimeout(window._tt);
      window._tt = setTimeout(() => t.style.display = 'none', 2400);
    }

    function render() {
      const lotRows = LOTS.map(l => {
        const pct = Math.min(100, Math.round(l.have / l.need * 100));
        return `<tr>
          <td><b>${l.name}</b></td>
          <td>${l.rg}</td>
          <td><span class="pb"><i style="width:${pct}%"></i></span><b>${l.have}/${l.need} t</b> (${pct}%)</td>
          <td>${l.price} so‘m</td>
          <td>${l.dest}</td>
          <td>${pct < 100 ? `<button class="btn sm" onclick="addTons(${l.id})">+2 t qo‘shish</button>` : `<span class="tag dark">Yuklashga tayyor</span>`}</td>
        </tr>`;
      }).join('');
      document.getElementById('tbl-lots').innerHTML = lotRows;
      document.getElementById('tbl-lots-2').innerHTML = lotRows;

      const sumHave = LOTS.reduce((a, b) => a + b.have, 0);
      const sumNeed = LOTS.reduce((a, b) => a + b.need, 0);
      document.getElementById('kpi-tons').textContent = `${sumHave} / ${sumNeed} t`;
      document.getElementById('kpi-trucks').textContent = `${TRUCKS.filter(t => !t.booked).length} ta`;
      document.getElementById('kpi-leads').textContent = `${LEADS.filter(l => !l.done).length} ta yangi`;

      const verRows = VERIFY.map(v => `<tr>
        <td><b>${v.name}</b></td>
        <td>${v.role}</td>
        <td>${v.rg}</td>
        <td><span class="tag ${v.ok ? '' : 'warn'}">${v.ok ? 'Tasdiqlangan' : 'Kutilmoqda'}</span></td>
        <td>${v.ok ? '<span style="color:var(--muted);font-size:12px">Faol</span>' : `<button class="btn sm" onclick="verifyUser(${v.id})">Tasdiqlash</button>`}</td>
      </tr>`).join('');
      document.getElementById('tbl-verify').innerHTML = verRows;
      document.getElementById('tbl-verify-2').innerHTML = verRows;

      document.getElementById('tbl-trucks').innerHTML = TRUCKS.map(t => `<tr>
        <td><b>${t.kind}</b></td>
        <td>${t.route}</td>
        <td>${t.driver}</td>
        <td><b>${t.price} so‘m</b></td>
        <td><span class="tag ${t.booked ? 'warn' : ''}">${t.booked ? 'Band' : 'Bo‘sh'}</span></td>
        <td><button class="btn sm ${t.booked ? 'ghost' : ''}" onclick="toggleTruck(${t.id})">${t.booked ? 'Bo‘shatish' : 'Band qilish'}</button></td>
      </tr>`).join('');

      document.getElementById('tbl-prices').innerHTML = PRICES.map(p => `<tr>
        <td><b>${p.name}</b></td>
        <td><input class="price-inp" type="number" value="${p.uz}"></td>
        <td><input class="price-inp" type="number" value="${p.kz}"></td>
        <td><input class="price-inp" type="number" value="${p.ru}"></td>
        <td><input class="price-inp" type="number" value="${p.baa}"></td>
      </tr>`).join('');

      document.getElementById('tbl-leads').innerHTML = LEADS.map(l => `<tr>
        <td><b>${l.who}</b></td>
        <td><span class="tag">${l.lang}</span></td>
        <td style="white-space:normal;max-width:320px">${l.msg}</td>
        <td><span class="tag dark">${l.score}</span><div style="font-size:12px;font-weight:700;color:#15803d;margin-top:3px">${l.value}</div></td>
        <td><span class="tag ${l.done ? '' : 'warn'}">${l.done ? 'Bog‘lanildi' : 'Yangi'}</span></td>
        <td style="display:flex;gap:6px;align-items:center">
          <button class="btn sm ghost" onclick="openAiReply(${l.id})">AI Javob</button>
          ${l.done ? '<span style="color:var(--muted);font-size:12px">Yopilgan</span>' : `<button class="btn sm" onclick="closeLead(${l.id})">Bog‘landim</button>`}
        </td>
      </tr>`).join('');
    }

    function openAiReply(id) {
      const l = LEADS.find(x => x.id === id);
      if (!l) return;
      document.getElementById('aiReplyTitle').textContent = `AI tayyorlagan javob — ${l.who} (${l.lang.split(' ')[0]})`;
      document.getElementById('aiReplyText').textContent = l.reply;
      document.getElementById('aiReplyBox').style.display = 'block';
    }
    function copyAdminAiReply() {
      const txt = document.getElementById('aiReplyText').textContent;
      if (txt) navigator.clipboard?.writeText(txt).then(() => notify('AI tijoriy javobi nusxalandi!'));
    }
    function simulateAiLead() {
      LEADS.unshift({ id: Date.now(), who: "Carrefour Gulf (Ar-Riyod, Saudiya)", lang: "AR → UZ", score: "95% Issiq", value: "$36 800", msg: "Quva shirin anori 18 tonna, 1-nav qadoqlangan holda kerak", reply: "Hello Carrefour Gulf! Quva Grade-A pomegranates (18 MT, export cartons) are ready for cold-chain dispatch to Riyadh via ZaminJob verified Agro-Passport farmers.", done: false });
      render();
      notify('Yangi xalqaro AI lid kelib tushdi (AR → UZ)!');
    }
    function loadAdminAiPulse() {
      fetch('/api/ai-signals', { credentials: 'same-origin' })
        .then(r => r.json())
        .then(d => {
          const clk = document.getElementById('admAiClock');
          if (clk) clk.textContent = 'Jonli · ' + (d.updated_at || '');
          const box = document.getElementById('admAiPulse');
          if (!box || !Array.isArray(d.country_pulse)) return;
          box.innerHTML = d.country_pulse.map(c => {
            const drop = c.price_pct < 0;
            const bg = drop ? 'rgba(225,29,72,.22)' : 'rgba(16,185,129,.20)';
            const br = drop ? 'rgba(251,113,133,.45)' : 'rgba(52,211,153,.45)';
            const col = drop ? '#fda4af' : '#6ee7b7';
            return `<div style="background:${bg};border:1px solid ${br};border-radius:12px;padding:10px 12px">
              <div style="display:flex;justify-content:space-between;font-weight:800;font-size:12.5px"><span>${c.code} · ${c.city}</span><span style="color:${col}">${c.price_pct > 0 ? '+' : ''}${c.price_pct}%</span></div>
              <div style="font-size:11px;color:#fde68a;font-weight:700;margin-top:3px">${c.supply_pct < 0 ? 'Zaxira ' + c.supply_pct + '%' : 'Ortiqcha +' + c.supply_pct + '%'}</div>
              <div style="font-size:11px;opacity:.82;margin-top:2px">${c.note}</div>
            </div>`;
          }).join('');
        }).catch(() => {});
    }
    loadAdminAiPulse();
    setInterval(loadAdminAiPulse, 15000);

    // Real-vaqt Agro Ob-havo (Open-Meteo jonli API) + Toshkent soati
    function loadAgroWeather() {
      const sel = document.getElementById('wxRegion');
      if (!sel) return;
      const [lat, lon, city] = sel.value.split(',');
      const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code&timezone=Asia%2FTashkent`;
      fetch(url)
        .then(r => r.json())
        .then(d => {
          const c = d.current || {};
          const t = Math.round(c.temperature_2m ?? 18);
          const h = Math.round(c.relative_humidity_2m ?? 45);
          const w = Math.round(c.wind_speed_10m ?? 8);
          const code = Number(c.weather_code ?? 0);
          const isRain = code >= 51;
          const desc = isRain ? 'Yomg‘ir / bulutli' : (code >= 2 ? 'Qisman bulutli' : 'Ochiq quyoshli');
          document.getElementById('wxTemp').textContent = `${t}°C`;
          document.getElementById('wxDesc').textContent = `${city} · ${desc}`;
          document.getElementById('wxMeta').textContent = `Shamol: ${w} km/s · Namlik: ${h}%`;
          document.getElementById('wxTip').textContent = isRain
            ? 'AI Tavsiya: Yomg‘ir xavfi — yuklashda yopiq ombor va tentdan foydalaning'
            : (w > 22 ? 'AI Tavsiya: Shamol kuchli — issiqxona qoplamasini tekshiring' : 'AI Tavsiya: Terim va 20t fura yuklash uchun qulay havo');
        })
        .catch(() => {
          document.getElementById('wxTemp').textContent = '19°C';
          document.getElementById('wxDesc').textContent = `${city} · Ochiq`;
          document.getElementById('wxTip').textContent = 'AI Tavsiya: Terim va eksport yuklash uchun qulay';
        });
    }
    loadAgroWeather();
    setInterval(loadAgroWeather, 300000);
    function tickUzClock() {
      const el = document.getElementById('uzClock');
      if (el) el.textContent = new Date().toLocaleTimeString('uz-UZ', { timeZone: 'Asia/Tashkent', hour12: false });
    }
    tickUzClock();
    setInterval(tickUzClock, 1000);

    function addTons(id) {
      const l = LOTS.find(x => x.id === id);
      if (l) { l.have = Math.min(l.need, l.have + 2); render(); notify(`${l.name}: ${l.have}/${l.need} t bo‘ldi`); }
    }
    function verifyUser(id) {
      const v = VERIFY.find(x => x.id === id);
      if (v) { v.ok = true; render(); notify(`${v.name} tasdiqlandi`); }
    }
    function toggleTruck(id) {
      const t = TRUCKS.find(x => x.id === id);
      if (t) { t.booked = !t.booked; render(); notify(t.booked ? 'Fura band qilindi' : 'Fura bo‘shatildi'); }
    }
    function closeLead(id) {
      const l = LEADS.find(x => x.id === id);
      if (l) { l.done = true; render(); notify('Lid belgilandi'); }
    }
    function addDemoLot() {
      LOTS.unshift({ id: Date.now(), name: "Andijon shaftolisi (Eksport)", rg: "Andijon", have: 6, need: 20, price: "14 200", dest: "Qozog‘iston / Rossiya" });
      render();
      notify("Yangi eksport lot qo‘shildi");
    }

    document.querySelectorAll('#sideNav button').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('#sideNav button').forEach(b => b.classList.remove('on'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('on'));
        btn.classList.add('on');
        document.getElementById('pane-' + btn.dataset.tab).classList.add('on');
      });
    });

    lucide.createIcons();
    render();
  </script>
</body>
</html>
