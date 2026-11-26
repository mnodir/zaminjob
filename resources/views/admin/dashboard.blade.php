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
      animation: fadeSlide .5s cubic-bezier(.22, 1, .36, 1) both;
    }
    @keyframes fadeSlide {
      from { opacity: 0; transform: translate3d(-18px, 0, 0); }
      to { opacity: 1; transform: translate3d(0, 0, 0); }
    }
    .kpi-top { display: flex; justify-content: space-between; align-items: center; color: var(--muted); font-size: 12.5px; font-weight: 600; }
    .kpi-top i { width: 18px; color: var(--green); }
    .kpi strong { display: block; font: 800 28px Manrope, sans-serif; margin: 10px 0 4px; letter-spacing: -1px; }
    .kpi small { color: var(--green); font-size: 12px; font-weight: 700; }

    /* Panels */
    .panel {
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: 18px;
      padding: 20px 22px;
      box-shadow: 0 6px 22px rgba(16, 44, 32, 0.04);
      margin-bottom: 20px;
    }
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
      <button data-tab="leads"><i data-lucide="message-square"></i> Aziz AI Lidlar</button>
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

    <div class="kpis">
      <div class="kpi">
        <div class="kpi-top"><span>Foydalanuvchilar</span><i data-lucide="users"></i></div>
        <strong>{{ $usersCount > 0 ? $usersCount : '1 240' }}</strong>
        <small>Laravel bazasi faol</small>
      </div>
      <div class="kpi">
        <div class="kpi-top"><span>Yig‘ilayotgan eksport</span><i data-lucide="package"></i></div>
        <strong id="kpi-tons">60 / 80 t</strong>
        <small>4 ta 20t fura ochiq</small>
      </div>
      <div class="kpi">
        <div class="kpi-top"><span>Bo‘sh furalar</span><i data-lucide="truck"></i></div>
        <strong id="kpi-trucks">4 ta</strong>
        <small>-35% bo‘sh qaytish mavjud</small>
      </div>
      <div class="kpi">
        <div class="kpi-top"><span>Aziz AI lidlar</span><i data-lucide="sparkles"></i></div>
        <strong id="kpi-leads">3 ta</strong>
        <small>EN/RU → UZ avto-tarjima</small>
      </div>
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
        <div class="panel-hd"><h2>Aziz AI Menejeri orqali kelgan issiq so‘rovlar</h2></div>
        <div class="tbl-wrap">
          <table>
            <thead><tr><th>Mijoz</th><th>Til</th><th>Asl xabar → O‘zbekcha tarjima</th><th>Holati</th><th>Amal</th></tr></thead>
            <tbody id="tbl-leads"></tbody>
          </table>
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
      { id: 1, who: "Al-Futtaim Fresh (Dubay)", lang: "EN → UZ", msg: "20 tonna Kishmish uzumi kerak, +2°C refrijerator bilan", done: false },
      { id: 2, who: "Magnit Opt (Moskva)", lang: "RU → UZ", msg: "Baxmal olmasi haftalik 40 tonna ulgurji narxi qancha?", done: false },
      { id: 3, who: "«Quva Meva» F/X", lang: "UZ", msg: "Farg‘onadan Toshkentga 5t bo‘sh Isuzu kerak", done: true }
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
        <td style="white-space:normal;max-width:340px">${l.msg}</td>
        <td><span class="tag ${l.done ? '' : 'warn'}">${l.done ? 'Bog‘lanildi' : 'Yangi'}</span></td>
        <td>${l.done ? '<span style="color:var(--muted);font-size:12px">Yopilgan</span>' : `<button class="btn sm" onclick="closeLead(${l.id})">Bog‘landim</button>`}</td>
      </tr>`).join('');
    }

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
