<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZaminJob — Mahsulot va Xizmatlar Bozori</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes catPanelIn { from { opacity: 0; transform: translate3d(28px, 0, 0); } to { opacity: 1; transform: translate3d(0, 0, 0); } }
        .cat-panel-anim { animation: catPanelIn .44s cubic-bezier(.22, 1, .36, 1) both; will-change: transform, opacity; }
    </style>
</head>
<body class="bg-[#f7f9f5] text-slate-800 min-h-screen flex flex-col">
    <nav class="bg-white/95 backdrop-blur border-b border-emerald-100 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-3">
            <a href="{{ url('/') }}" class="flex items-center">
                <svg width="136" height="32" viewBox="0 0 206 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 10H31.5V15.6L11.4 33.4H32V39H0.5V33.4L20.6 15.6H1V10Z" fill="#102C20"/>
                    <path d="M5.5 29.8C8.6 21.8 17.4 17.5 29.2 18.2C25.4 26.2 17.8 31.2 5.5 29.8Z" fill="#15803D"/>
                    <path d="M7.5 28.6C13.5 24.4 20.2 21.4 26.8 19.6" stroke="#F7F9F5" stroke-width="1.4" stroke-linecap="round"/>
                    <text x="33.5" y="38.5" fill="#102C20" font-family="system-ui, sans-serif" font-weight="800" font-size="34" letter-spacing="-1.3">aminJob</text>
                </svg>
            </a>
            <div class="flex items-center gap-2.5">
                <a href="{{ url('/') }}" class="hidden sm:inline-block px-3.5 py-2 rounded-full text-xs font-bold text-slate-600 hover:bg-slate-100">Bosh sahifa</a>
                @auth
                    <a href="{{ route('kabinet') }}" class="px-3.5 py-2 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100">Kabinet</a>
                    <a href="{{ route('profil') }}" class="w-9 h-9 rounded-full overflow-hidden border-2 border-emerald-600 bg-[#102c20] text-white font-bold text-sm flex items-center justify-center">
                        @if(!empty(auth()->user()->avatar_path))
                            <img src="{{ asset(auth()->user()->avatar_path) }}" class="w-full h-full object-cover" alt="">
                        @else
                            {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'Z', 0, 1)) }}
                        @endif
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-full border border-slate-300 bg-white text-xs font-bold text-slate-800">Kirish</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-emerald-600 text-white text-xs font-bold">Ro‘yxatdan o‘tish</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-6xl w-full mx-auto px-4 py-6 space-y-6">
        @if(session('success'))
            <div class="px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold">{{ session('error') }}</div>
        @endif

        <!-- 1-RASM: MAHSULOT TOIFALARI MEGA-KATALOGI -->
        @php $activeCat = ($selCat !== '' && isset($categories[$selCat])) ? $selCat : array_key_first($categories); @endphp
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">MAHSULOT TOIFALARI</div>
                    <h1 class="text-xl font-extrabold text-slate-900">Mahsulot, yer, texnika va yuk tashish qidiruvi</h1>
                </div>
                @auth
                    <button type="button" onclick="document.getElementById('newListingForm').classList.toggle('hidden')" class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition">+ Yangi e’lon joylash</button>
                @else
                    <a href="{{ route('register') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition">+ E’lon joylash</a>
                @endauth
            </div>
            <div class="grid grid-cols-1 md:grid-cols-12">
                <div class="md:col-span-3 border-b md:border-b-0 md:border-r border-slate-100 p-3 space-y-1 bg-slate-50/50">
                    @foreach($categories as $catName => $subs)
                        <button type="button" data-cat="{{ $catName }}" onmouseenter="switchCat(this)" onclick="switchCat(this)"
                                class="cat-btn w-full text-left px-3.5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-between transition {{ $activeCat === $catName ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-white' }}">
                            <span>{{ $catName }}</span><span class="opacity-50">›</span>
                        </button>
                    @endforeach
                </div>
                <div class="md:col-span-9 p-5">
                    @foreach($categories as $catName => $subs)
                        <div class="cat-panel grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 {{ $activeCat === $catName ? '' : 'hidden' }}" data-panel="{{ $catName }}">
                            @foreach($subs as $subName)
                                <a href="{{ route('bozor', ['cat' => $catName, 'sub' => $subName]) }}"
                                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($selSub === $subName && $selCat === $catName) ? 'border-emerald-600 bg-emerald-50 text-emerald-800' : 'border-transparent hover:border-emerald-200 hover:bg-emerald-50/40 text-slate-700' }}">
                                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="8"/><path d="M12 8v8M8 12h8"/></svg>
                                    </span>
                                    <span class="truncate">{{ $subName }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- YANGI E'LON JOYLASH (KO'P RASMLI) -->
        @auth
        <div id="newListingForm" class="hidden bg-white rounded-3xl border border-emerald-200 p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-slate-900 mb-4">Yangi e’lon joylash (mahsulot, yer, texnika yoki fura)</h2>
            <form action="{{ route('bozor.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Toifa</label>
                        <select name="category" id="catSel" onchange="syncSub()" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white">
                            @foreach($categories as $c => $s)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Turi</label>
                        <select name="subcategory" id="subSel" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white"></select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sarlavha</label>
                        <input type="text" name="title" required class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div><label class="block text-xs font-bold text-slate-700 mb-1">Narxi (so‘m)</label><input type="text" name="price" required class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5"></div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Birlik</label>
                        <select name="unit" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white">
                            <option>so‘m / kg</option><option>so‘m / tonna</option><option>so‘m / gektar</option><option>so‘m / reys</option><option>so‘m / dona</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Viloyat</label>
                        <select name="region" id="regSel" onchange="syncDist()" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white">
                            @foreach($regions as $r => $ds)<option value="{{ $r }}">{{ $r }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tuman / Shahar</label>
                        <select name="district" id="distSel" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white"></select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="text" name="spec_1" placeholder="1-xususiyat (masalan: Hajmi: 15 tonna / Maydon: 5 ga)" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5">
                    <input type="text" name="spec_2" placeholder="2-xususiyat (masalan: Holati: 1-nav / Sovutkichli)" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5">
                </div>
                <textarea name="description" rows="3" required placeholder="Batafsil tavsif..." class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5"></textarea>
                <div onclick="document.getElementById('photosInp').click()" class="cursor-pointer border-2 border-dashed border-emerald-300 bg-emerald-50/40 rounded-2xl p-4 flex items-center justify-between gap-3 hover:border-emerald-600 transition">
                    <div>
                        <div class="text-sm font-bold text-slate-800">Rasmlarni yuklang (6 tagacha)</div>
                        <div id="photosLbl" class="text-xs text-slate-500">Mahsulot, yer, mashina yoki texnika rasmlarini tanlang</div>
                    </div>
                    <span class="px-3.5 py-2 rounded-xl bg-white border border-emerald-200 text-xs font-bold text-emerald-700">Rasmlar tanlash</span>
                </div>
                <input type="file" name="photos[]" id="photosInp" accept="image/*" multiple class="hidden">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700">E’lonni joylash</button>
            </form>
        </div>
        @endauth

        <!-- DAVLATLARARO NARX SOLISHTIRISH -->
        <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Davlatlararo mahsulot narxlari solishtirmasi (Osiyo va Eksport)</h2>
                    <p class="text-xs text-slate-500">Hozirgi ulgurji narxlar 1 kg so‘mda — 26 noyabr 2026</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700">UZ • KZ • RU • CN • KR • BAA</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-[11px] uppercase text-slate-400 bg-slate-50">
                        <tr><th class="px-3 py-2.5 rounded-l-xl">Mahsulot</th><th class="px-3 py-2.5">O‘zbekiston</th><th class="px-3 py-2.5">Qozog‘iston</th><th class="px-3 py-2.5">Rossiya</th><th class="px-3 py-2.5">Xitoy</th><th class="px-3 py-2.5">J. Koreya</th><th class="px-3 py-2.5">BAA</th><th class="px-3 py-2.5 rounded-r-xl">Maks. foyda</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($globalPrices as $gp)
                            @php $maxP = max($gp['kr'], $gp['baa']); $pct = round((($maxP - $gp['uz']) / $gp['uz']) * 100); @endphp
                            <tr class="hover:bg-emerald-50/30">
                                <td class="px-3 py-2.5 font-bold text-slate-900">{{ $gp['name'] }}</td>
                                <td class="px-3 py-2.5 font-semibold">{{ number_format($gp['uz'], 0, '.', ' ') }} so‘m</td>
                                <td class="px-3 py-2.5 font-semibold">{{ number_format($gp['kz'], 0, '.', ' ') }} so‘m</td>
                                <td class="px-3 py-2.5 font-semibold">{{ number_format($gp['ru'], 0, '.', ' ') }} so‘m</td>
                                <td class="px-3 py-2.5 font-semibold">{{ number_format($gp['cn'], 0, '.', ' ') }} so‘m</td>
                                <td class="px-3 py-2.5 font-extrabold text-emerald-700">{{ number_format($gp['kr'], 0, '.', ' ') }} so‘m</td>
                                <td class="px-3 py-2.5 font-semibold text-emerald-700">{{ number_format($gp['baa'], 0, '.', ' ') }} so‘m</td>
                                <td class="px-3 py-2.5"><span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold">+{{ $pct }}%</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- QIDIRUV SATRI -->
        <form action="{{ route('bozor') }}" method="GET" class="flex flex-wrap gap-2.5 bg-white p-3.5 rounded-2xl border border-slate-200">
            <input type="text" name="q" value="{{ $q }}" placeholder="Mahsulot, yer, texnika yoki fura qidirish..." class="flex-1 min-w-[200px] text-sm border border-slate-300 rounded-xl px-3.5 py-2">
            <select name="region" class="text-sm border border-slate-300 rounded-xl px-3 py-2 bg-white">
                <option value="">Barcha viloyatlar</option>
                @foreach($regions as $r => $ds)<option value="{{ $r }}" {{ $selReg === $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach
            </select>
            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800">Qidirish</button>
            @if($selCat !== '' || $q !== '' || $selReg !== '')
                <a href="{{ route('bozor') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-600">Barchasi</a>
            @endif
        </form>

        <!-- E'LONLAR KARTALARI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($listings as $item)
                @php $imgs = json_decode((string)$item->images_json, true) ?: []; $cover = $imgs[0] ?? 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800&q=80'; @endphp
                <a href="{{ route('bozor.show', $item->id) }}" class="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-lg hover:border-emerald-300 transition flex flex-col">
                    <div class="relative h-48 bg-slate-100 overflow-hidden">
                        <img src="{{ $cover }}" alt="{{ $item->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-slate-900/75 text-white text-[11px] font-bold">{{ $item->subcategory }}</span>
                        <span class="absolute bottom-2.5 right-2.5 px-2 py-0.5 rounded-md bg-black/65 text-white text-[11px] font-bold">{{ count($imgs) }} rasm</span>
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="text-[11px] text-slate-400 font-semibold">{{ date('j.m.Y', strtotime($item->created_at ?? 'now')) }}</div>
                        <h3 class="font-bold text-base text-slate-900 mt-0.5 group-hover:text-emerald-700">{{ $item->title }}</h3>
                        <div class="text-lg font-extrabold text-emerald-700 mt-2">{{ number_format((int)$item->price, 0, '.', ' ') }} <span class="text-xs font-semibold text-slate-500">{{ $item->unit }}</span></div>
                        <div class="mt-auto pt-3 flex items-center justify-between text-xs text-slate-500 border-t border-slate-100">
                            <span>{{ $item->region }}{{ $item->district ? ', '.$item->district : '' }}</span>
                            <span class="font-bold text-emerald-700">Ko‘rish ›</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </main>

    <script>
        const CATS = @json($categories);
        const REGS = @json($regions);
        function switchCat(btn) {
            const c = btn.dataset.cat;
            document.querySelectorAll('.cat-btn').forEach(b => {
                const on = b === btn;
                b.classList.toggle('bg-emerald-50', on); b.classList.toggle('text-emerald-700', on); b.classList.toggle('text-slate-700', !on);
            });
            document.querySelectorAll('.cat-panel').forEach(p => {
                const show = p.dataset.panel === c;
                p.classList.toggle('hidden', !show);
                p.classList.remove('cat-panel-anim');
                if (show) { void p.offsetWidth; p.classList.add('cat-panel-anim'); }
            });
        }
        function syncSub() {
            const c = document.getElementById('catSel'), s = document.getElementById('subSel');
            if (!c || !s) return;
            s.innerHTML = (CATS[c.value] || []).map(x => `<option value="${x}">${x}</option>`).join('');
        }
        function syncDist() {
            const r = document.getElementById('regSel'), d = document.getElementById('distSel');
            if (!r || !d) return;
            d.innerHTML = (REGS[r.value] || []).map(x => `<option value="${x}">${x}</option>`).join('');
        }
        syncSub(); syncDist();
        const pi = document.getElementById('photosInp');
        if (pi) pi.addEventListener('change', () => {
            const l = document.getElementById('photosLbl');
            if (l && pi.files.length) l.textContent = pi.files.length + ' ta rasm tanlandi';
        });
    </script>
</body>
</html>
