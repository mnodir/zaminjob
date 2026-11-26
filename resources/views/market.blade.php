<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ZaminJob — Mahsulot va Xizmatlar Bozori</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='10' fill='%23102c20'/%3E%3Cpath d='M9 11h22v4.4L16.2 26.6H31V31H9v-4.4L23.8 15.4H9V11z' fill='%23ffffff'/%3E%3Cpath d='M12 24.5c2.2-6 8.2-9.2 15.5-8.5-2.6 5.6-7.8 9.2-15.5 8.5z' fill='%2316a34a'/%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes subGlideIn {
            from { opacity: 0; transform: translate3d(30px, 0, 0) scale(.98); }
            to { opacity: 1; transform: translate3d(0, 0, 0) scale(1); }
        }
        @keyframes leftGlideIn {
            from { opacity: 0; transform: translate3d(-26px, 0, 0); }
            to { opacity: 1; transform: translate3d(0, 0, 0); }
        }
        .glide-left {
            animation: leftGlideIn .58s cubic-bezier(.16, 1, .3, 1) calc(var(--i, 0) * 42ms) both;
            will-change: transform, opacity;
        }
        .cat-panel.anim .cat-sub-item {
            animation: subGlideIn .56s cubic-bezier(.16, 1, .3, 1) calc(var(--i, 0) * 32ms) both;
            will-change: transform, opacity;
        }
        .cat-sub-item {
            transition: transform .26s cubic-bezier(.16, 1, .3, 1), border-color .22s ease, background-color .22s ease, box-shadow .24s ease;
        }
        .cat-sub-item:hover { transform: translate3d(5px, 0, 0); }
        .cat-btn {
            transition: background-color .24s ease, color .24s ease, transform .28s cubic-bezier(.16, 1, .3, 1);
        }
        .cat-btn:hover { transform: translate3d(5px, 0, 0); }
        .glide-card {
            animation: subGlideIn .62s cubic-bezier(.16, 1, .3, 1) calc(var(--i, 0) * 55ms) both;
            will-change: transform, opacity;
        }
        .bozor-card {
            opacity: 0;
            transform: translate3d(34px, 16px, 0) scale(.985);
            transition: opacity .68s cubic-bezier(.16, 1, .3, 1), transform .68s cubic-bezier(.16, 1, .3, 1), box-shadow .32s ease, border-color .28s ease;
            transition-delay: calc(var(--d, 0) * 75ms), calc(var(--d, 0) * 75ms), 0ms, 0ms;
            will-change: transform, opacity;
        }
        .bozor-card.in {
            opacity: 1;
            transform: translate3d(0, 0, 0) scale(1);
        }
        .bozor-card.in:hover {
            transform: translate3d(0, -6px, 0) scale(1.01);
            transition-delay: 0ms;
        }
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
                        <button type="button" data-cat="{{ $catName }}" onmouseenter="switchCat(this)" onclick="switchCat(this)" style="--i:{{ $loop->index }}"
                                class="cat-btn glide-left w-full text-left px-3.5 py-2.5 rounded-xl text-sm font-bold flex items-center justify-between {{ $activeCat === $catName ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-white' }}">
                            <span>{{ $catName }}</span><span class="opacity-50">›</span>
                        </button>
                    @endforeach
                </div>
                <div class="md:col-span-9 p-5 overflow-hidden">
                    @foreach($categories as $catName => $subs)
                        <div class="cat-panel grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 {{ $activeCat === $catName ? 'anim' : 'hidden' }}" data-panel="{{ $catName }}">
                            @foreach($subs as $subName)
                                <a href="{{ route('bozor', ['cat' => $catName, 'sub' => $subName]) }}" style="--i:{{ $loop->index }}"
                                   class="cat-sub-item flex items-center gap-2.5 px-3 py-2.5 rounded-xl border text-sm font-semibold {{ ($selSub === $subName && $selCat === $catName) ? 'border-emerald-600 bg-emerald-50 text-emerald-800' : 'border-transparent hover:border-emerald-200 hover:bg-emerald-50/40 text-slate-700' }}">
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
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700">Narxi (so‘m)</label>
                            <button type="button" onclick="suggestAiPrice()" class="text-[11px] font-extrabold text-emerald-700 hover:text-emerald-900">AI narx tavsiyasi</button>
                        </div>
                        <input type="text" name="price" id="listPriceInp" required class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5">
                        <div id="aiPriceHint" class="hidden text-[11px] text-emerald-700 font-semibold mt-1"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Birlik</label>
                        <select name="unit" id="listUnitSel" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white">
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
                        <div class="text-sm font-bold text-slate-800">Rasmlarni yuklang (6 tagacha) · EXIF + GPS tekshiruvi</div>
                        <div id="photosLbl" class="text-xs text-slate-500">Real dalada olingan rasmlarga avtomatik «GPS + Vaqt tasdiqlangan» belgisi beriladi</div>
                    </div>
                    <span class="px-3.5 py-2 rounded-xl bg-white border border-emerald-200 text-xs font-bold text-emerald-700 shrink-0">Rasmlar tanlash</span>
                </div>
                <input type="file" name="photos[]" id="photosInp" accept="image/*" multiple class="hidden">
                <input type="hidden" name="photo_gps_lat" id="photoGpsLat">
                <input type="hidden" name="photo_gps_lng" id="photoGpsLng">
                <input type="hidden" name="photo_taken_at" id="photoTakenAt">
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-slate-50 border border-slate-200 p-3.5">
                    <div class="text-xs">
                        <div class="font-bold text-slate-800">1-qavat himoya: Dala GPS va vaqt metadatasi</div>
                        <div id="gpsMetaStatus" class="text-slate-500 mt-0.5">Soxta e’lonlarga qarshi dala koordinatasi va vaqtini biriktiring</div>
                    </div>
                    <button type="button" onclick="captureFieldGps()" class="px-3.5 py-2 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-extrabold hover:bg-emerald-100 transition">Dala GPS’ini ulash</button>
                </div>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700">E’lonni joylash</button>
            </form>
        </div>
        @endauth

        <!-- ZAMINJOB AI EKSPORT SIGNALLARI (REAL VAQT: O'SISH + TUSHISH + TANQISLIK) -->
        <section id="ai-eye" class="bg-gradient-to-br from-[#0b3b27] via-[#102c20] to-[#082218] text-white rounded-3xl p-5 sm:p-6 shadow-lg border border-emerald-800/60 space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-[11px] font-extrabold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>ZaminJob AI Eye · Real vaqt signallari</span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-extrabold mt-2">AI Bozor Signallari: Narx O‘sishi, Tushishi va Tanqislik Radari</h2>
                </div>
                <div class="flex items-center gap-2.5">
                    <span id="aiClock" class="text-xs font-semibold text-emerald-200/90 bg-white/10 px-3 py-1.5 rounded-full">Yangilanmoqda...</span>
                    <button type="button" onclick="loadAiSignals(true)" class="px-3.5 py-1.5 rounded-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-extrabold transition">Yangilash</button>
                </div>
            </div>

            <!-- 5 DAVLAT BO'YICHA REAL VAQT RADARI (NARX VA ZAXIRA O'SISH/TUSHISHI) -->
            <div class="space-y-2">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-300">5 Davlat Real Vaqt Radari</div>
                    <div class="flex items-center gap-1.5 text-[11px] font-bold">
                        <button type="button" onclick="setAiTab('all')" data-aitab="all" class="ai-tab-btn px-3 py-1 rounded-full bg-emerald-400 text-slate-950 transition">Barchasi</button>
                        <button type="button" onclick="setAiTab('hot')" data-aitab="hot" class="ai-tab-btn px-3 py-1 rounded-full bg-white/10 text-emerald-100 hover:bg-white/20 transition">Foydali eksport (4)</button>
                        <button type="button" onclick="setAiTab('drop')" data-aitab="drop" class="ai-tab-btn px-3 py-1 rounded-full bg-white/10 text-rose-200 hover:bg-white/20 transition">Narx tushgan (3)</button>
                    </div>
                </div>
                <div id="aiCountryPulse" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5"></div>
            </div>

            <!-- 4 TA JONLI AI EKSPORT IMKONIYATI KARTASI -->
            <div id="aiHotWrap" class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-emerald-300">Hozir Eksport Qilish Eng Foydali (4 Davlat)</div>
                    <span class="text-[11px] text-emerald-200/80 hidden sm:inline">UZ narxidan logistikani ayirib hisoblangan</span>
                </div>
                <div id="aiSignalsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <div class="p-4 rounded-2xl bg-white/10 text-xs text-emerald-100">Jonli AI signallar yuklanmoqda...</div>
                </div>
            </div>

            <!-- REAL VAQTDA NARX TUSHIB KETGAN DAVLATLAR (XAVF OGOHLANTIRISHI) -->
            <div id="aiDropWrap" class="space-y-2.5">
                <div class="flex items-center justify-between gap-2">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-rose-300">Narx Tushib Ketgan Davlatlar (Hozir Jo‘natmang)</div>
                    <span class="text-[10.5px] font-bold px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-200 border border-rose-400/30">Zarardan himoya</span>
                </div>
                <div id="aiDropsGrid" class="grid grid-cols-1 sm:grid-cols-3 gap-3.5"></div>
            </div>

            <!-- INTERAKTIV AI AGRO-MASLAHATCHI (JONLI SAVDO & EKSPORT COPILOT) -->
            <div class="bg-slate-950/45 backdrop-blur rounded-2xl p-4 sm:p-5 border border-emerald-400/25 space-y-3.5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="text-xs font-extrabold uppercase tracking-wider text-emerald-300">ZaminJob AI Agro-Maslahatchi (Jonli tahlil)</div>
                        <p class="text-xs text-emerald-100/80">Bozordagi real e’lonlar, eksport foydasi, 20t Agro-Pooling va bojxona hujjatlari bo‘yicha savol bering</p>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-200 border border-emerald-400/30">MySQL + Bozor AI</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="askAiQuick('10 tonna gilosni qayerga eksport qilsam eng foydali?')" class="px-3 py-1.5 rounded-full bg-white/10 hover:bg-emerald-500/30 border border-white/15 text-[11.5px] font-semibold text-emerald-50 transition">10t gilos eksport foydasi</button>
                    <button type="button" onclick="askAiQuick('8 tonna olma uchun 20t fura to‘ldirishda Agro-Pooling sherik top')" class="px-3 py-1.5 rounded-full bg-white/10 hover:bg-emerald-500/30 border border-white/15 text-[11.5px] font-semibold text-emerald-50 transition">20t fura Agro-Pooling</button>
                    <button type="button" onclick="askAiQuick('Dubayga anor eksport qilish uchun qanday hujjat va fitosanitariya kerak?')" class="px-3 py-1.5 rounded-full bg-white/10 hover:bg-emerald-500/30 border border-white/15 text-[11.5px] font-semibold text-emerald-50 transition">Eksport hujjatlari</button>
                    <button type="button" onclick="askAiQuick('Baxmal olmasi narxi keyingi 7 kunda qanday o‘zgaradi?')" class="px-3 py-1.5 rounded-full bg-white/10 hover:bg-emerald-500/30 border border-white/15 text-[11.5px] font-semibold text-emerald-50 transition">7-kunlik narx prognozi</button>
                </div>
                <form onsubmit="sendAiChat(event)" class="flex flex-col sm:flex-row gap-2">
                    <input type="text" id="aiChatInp" placeholder="Savolingizni yozing (masalan: 15 tonna uzumni Dubayga jo‘natsam qancha foyda?)" class="flex-1 text-sm text-slate-900 font-medium rounded-xl px-4 py-2.5 bg-white" required>
                    <button type="submit" id="aiChatBtn" class="px-5 py-2.5 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 font-extrabold text-sm transition shrink-0">AI tahlil qilsin</button>
                </form>
                <div id="aiChatOut" class="hidden bg-white text-slate-900 rounded-2xl p-4 space-y-3"></div>
            </div>

            <!-- INTERAKTIV AI FOYDA KALKULYATORI + 3 TILLI EKSPORT TAKLIFI -->
            <div class="bg-white/10 backdrop-blur rounded-2xl p-4 sm:p-5 border border-white/15 space-y-4">
                <div>
                    <div class="text-xs font-extrabold uppercase tracking-wider text-emerald-300">AI Eksport Foyda Kalkulyatori</div>
                    <p class="text-xs text-emerald-100/80">Mahsulot va tonnani tanlang — AI logistikani ayirib eng foydali davlat va 3 tilli taklif chiqaradi</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                    <select id="aiProd" class="text-sm text-slate-900 font-semibold rounded-xl px-3 py-2.5 bg-white">
                        @foreach($globalPrices as $gp)<option value="{{ $gp['name'] }}">{{ $gp['name'] }}</option>@endforeach
                    </select>
                    <input type="number" id="aiTons" value="10" min="1" max="200" placeholder="Hajm (tonna)" class="text-sm text-slate-900 font-semibold rounded-xl px-3.5 py-2.5 bg-white">
                    <select id="aiReg" class="text-sm text-slate-900 font-semibold rounded-xl px-3 py-2.5 bg-white">
                        @foreach($regions as $r => $ds)<option value="{{ $r }}" {{ $r === 'Jizzax viloyati' ? 'selected' : '' }}>{{ $r }}</option>@endforeach
                    </select>
                    <button type="button" onclick="runAiAnalyze()" class="px-5 py-2.5 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 font-extrabold text-sm transition">AI Hisoblasin</button>
                </div>

                <div id="aiResultBox" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-4 pt-2">
                    <div class="lg:col-span-7 bg-white text-slate-900 rounded-2xl p-4 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <div class="text-[11px] font-extrabold uppercase text-emerald-700">AI Tavsiyasi · Eng yuqori sof foyda</div>
                                <div id="aiBestTitle" class="text-base font-extrabold text-slate-900"></div>
                            </div>
                            <span id="aiBestNet" class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold"></span>
                        </div>
                        <div id="aiPoolingTip" class="text-xs bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl p-3 font-medium"></div>
                        <div id="aiRoutesBars" class="space-y-2"></div>
                    </div>
                    <div class="lg:col-span-5 bg-slate-950/60 border border-white/15 rounded-2xl p-4 flex flex-col justify-between gap-3">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2.5">
                                <span class="text-xs font-extrabold text-emerald-300 uppercase">3 Tilli Eksport Taklifi</span>
                                <div class="flex gap-1">
                                    <button type="button" onclick="showOfferLang('uz')" data-olang="uz" class="olang-btn px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500 text-slate-950">UZ</button>
                                    <button type="button" onclick="showOfferLang('ru')" data-olang="ru" class="olang-btn px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white/10 text-white">RU</button>
                                    <button type="button" onclick="showOfferLang('en')" data-olang="en" class="olang-btn px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white/10 text-white">EN</button>
                                </div>
                            </div>
                            <p id="aiOfferText" class="text-xs leading-relaxed text-emerald-50/95 bg-white/5 rounded-xl p-3 border border-white/10"></p>
                        </div>
                        <button type="button" onclick="copyAiOffer()" id="aiCopyBtn" class="w-full py-2 rounded-xl bg-white text-slate-900 text-xs font-extrabold hover:bg-emerald-100 transition">Taklifni nusxalash (Telegram / WhatsApp)</button>
                    </div>
                </div>
            </div>
            <div id="aiToast" class="hidden text-xs font-bold bg-emerald-400 text-slate-950 px-4 py-2.5 rounded-xl"></div>
        </section>

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
                            <tr class="glide-left hover:bg-emerald-50/30" style="--i:{{ $loop->index }}">
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
        @php
            $subMap = [
                'Pomidor' => ['img' => 'images/listings/pomidor.jpg', 'vid' => 'videos/15909400-uhd_3840_2160_25fps.mp4#t=2'],
                'Sovutkichli refrijerator' => ['img' => 'images/listings/fura.jpg', 'vid' => 'videos/180618-864656629.mp4#t=3'],
                'Fura (20t)' => ['img' => 'images/listings/fura.jpg', 'vid' => 'videos/180618-864656629.mp4#t=6'],
                'Traktor' => ['img' => 'images/listings/traktor.jpg', 'vid' => 'videos/14571969_2560_1440_24fps.mp4#t=3'],
                'Issiqxona' => ['img' => 'images/listings/issiqxona.jpg', 'vid' => 'videos/14571969_2560_1440_24fps.mp4#t=8'],
                'Uzum' => ['img' => 'images/listings/uzum.jpg', 'vid' => 'videos/15909400-uhd_3840_2160_25fps.mp4#t=7'],
                'Olma' => ['img' => 'images/listings/olma.jpg', 'vid' => 'videos/15909400-uhd_3840_2160_25fps.mp4#t=11'],
            ];
            $defItem = ['img' => 'images/listings/olma.jpg', 'vid' => 'videos/15909400-uhd_3840_2160_25fps.mp4#t=4'];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($listings as $item)
                @php
                    $imgs = json_decode((string)$item->images_json, true) ?: [];
                    $meta = $subMap[$item->subcategory] ?? $defItem;
                    $hasLocalImg = !empty($imgs[0]) || file_exists(public_path($meta['img']));
                    $cover = !empty($imgs[0]) ? $imgs[0] : asset($meta['img']);
                    $vidCover = asset($meta['vid']);
                    $photoCount = max(1, count($imgs));
                @endphp
                <a href="{{ route('bozor.show', $item->id) }}" style="--d:{{ $loop->index % 6 }}" class="bozor-card group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl hover:border-emerald-300 flex flex-col">
                    <div class="relative h-48 bg-emerald-950 overflow-hidden">
                        @if($hasLocalImg)
                            <img src="{{ $cover }}" alt="{{ $item->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <video muted playsinline preload="metadata" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $vidCover }}"></video>
                        @endif
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-slate-900/75 text-white text-[11px] font-bold">{{ $item->subcategory }}</span>
                        @if(($item->photo_verified ?? 1))
                            <span class="absolute top-3 right-3 px-2 py-0.5 rounded-md bg-emerald-600/95 text-white text-[10px] font-extrabold">GPS + Vaqt ✓</span>
                        @else
                            <span class="absolute top-3 right-3 px-2 py-0.5 rounded-md bg-amber-500/95 text-slate-950 text-[10px] font-extrabold">Tekshirilmoqda</span>
                        @endif
                        <span class="absolute bottom-2.5 right-2.5 px-2 py-0.5 rounded-md bg-black/65 text-white text-[11px] font-bold">{{ $photoCount }} rasm</span>
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[11px] text-slate-400 font-semibold">{{ date('j.m.Y', strtotime($item->created_at ?? 'now')) }}</span>
                            <span class="text-[10.5px] font-bold {{ ($item->photo_verified ?? 1) ? 'text-emerald-700' : 'text-amber-700' }}">{{ ($item->photo_verified ?? 1) ? 'Real dala tasdig‘i' : 'Metadata kutilmoqda' }}</span>
                        </div>
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
                p.classList.remove('anim');
                if (show) { void p.offsetWidth; p.classList.add('anim'); }
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
            if (l && pi.files.length) l.textContent = pi.files.length + ' ta rasm tanlandi — EXIF + GPS tekshiruviga tayyor';
        });
        function captureFieldGps() {
            const st = document.getElementById('gpsMetaStatus');
            const latEl = document.getElementById('photoGpsLat');
            const lngEl = document.getElementById('photoGpsLng');
            const tEl = document.getElementById('photoTakenAt');
            const nowStr = new Date().toLocaleString('ru-RU', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' });
            const setCoords = (lat, lng, note) => {
                if (latEl) latEl.value = lat;
                if (lngEl) lngEl.value = lng;
                if (tEl) tEl.value = nowStr;
                if (st) { st.textContent = `Tasdiqlandi: GPS ${lat}, ${lng} · ${nowStr}` + (note ? ` (${note})` : ''); st.className = 'text-emerald-700 font-bold mt-0.5'; }
            };
            if (navigator.geolocation) {
                if (st) st.textContent = 'GPS koordinata aniqlanmoqda...';
                navigator.geolocation.getCurrentPosition(
                    p => setCoords(p.coords.latitude.toFixed(4), p.coords.longitude.toFixed(4), 'real geolokatsiya'),
                    () => setCoords('40.1158', '67.8422', 'hududiy GPS'),
                    { timeout: 4000 }
                );
            } else {
                setCoords('40.1158', '67.8422', 'hududiy GPS');
            }
        }

        // === REAL VAQT AI EKSPORT SIGNALLARI + KALKULYATOR ===
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const fmt = n => Math.round(Number(n) || 0).toLocaleString('ru-RU');
        let lastOffers = { uz: '', ru: '', en: '' };
        let activeOfferLang = 'uz';
        let aiSignalsList = [];

        function aiNotify(msg) {
            const t = document.getElementById('aiToast');
            if (!t) return;
            t.textContent = msg;
            t.classList.remove('hidden');
            clearTimeout(window._aiT);
            window._aiT = setTimeout(() => t.classList.add('hidden'), 3200);
        }

        function setAiTab(mode) {
            const hot = document.getElementById('aiHotWrap');
            const drp = document.getElementById('aiDropWrap');
            if (hot) hot.classList.toggle('hidden', mode === 'drop');
            if (drp) drp.classList.toggle('hidden', mode === 'hot');
            document.querySelectorAll('.ai-tab-btn').forEach(b => {
                const on = b.dataset.aitab === mode;
                b.className = 'ai-tab-btn px-3 py-1 rounded-full transition ' + (on ? 'bg-emerald-400 text-slate-950' : 'bg-white/10 text-emerald-100 hover:bg-white/20');
            });
        }

        function sparkSvg(arr, uid) {
            const vals = Array.isArray(arr) && arr.length ? arr : [30,42,38,56,68,80,92];
            const minV = Math.min(...vals) - 6, maxV = Math.max(...vals) + 4;
            const coords = vals.map((v, i) => {
                const x = Math.round((i / Math.max(1, vals.length - 1)) * 160);
                const y = Math.round(36 - ((v - minV) / Math.max(1, maxV - minV)) * 28);
                return [x, y];
            });
            const line = coords.map(c => c.join(',')).join(' ');
            const area = `0,40 ${line} 160,40`;
            const last = coords[coords.length - 1];
            return `<svg viewBox="0 0 160 42" class="w-full h-11 mt-2" fill="none">
                <defs><linearGradient id="sg${uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#34d399" stop-opacity="0.38"/><stop offset="100%" stop-color="#34d399" stop-opacity="0.02"/></linearGradient></defs>
                <polygon points="${area}" fill="url(#sg${uid})"/>
                <polyline points="${line}" stroke="#34d399" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="${last[0]}" cy="${last[1]}" r="3.5" fill="#6ee7b7" stroke="#064e3b" stroke-width="1.5"/>
            </svg>`;
        }

        function loadAiSignals(manual) {
            fetch('/api/ai-signals', { credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => {
                    const clk = document.getElementById('aiClock');
                    if (clk) clk.textContent = 'Jonli · ' + (data.updated_at || '');

                    // 5 Davlat Real Vaqt Radari
                    const pulseEl = document.getElementById('aiCountryPulse');
                    if (pulseEl && Array.isArray(data.country_pulse)) {
                        pulseEl.innerHTML = data.country_pulse.map(c => {
                            const isDrop = c.price_pct < 0;
                            const cardCls = isDrop
                                ? 'border-rose-400/40 bg-rose-950/45 hover:bg-rose-950/65'
                                : 'border-emerald-400/40 bg-emerald-950/45 hover:bg-emerald-950/65';
                            const priceCls = isDrop ? 'text-rose-300' : 'text-emerald-300';
                            const badge = isDrop
                                ? `<span class="px-1.5 py-0.5 rounded bg-rose-500/25 text-rose-200 text-[10px] font-bold">Ortiqcha +${c.supply_pct}%</span>`
                                : `<span class="px-1.5 py-0.5 rounded bg-amber-400/20 text-amber-200 text-[10px] font-bold">Zaxira ${c.supply_pct}%</span>`;
                            return `<div class="rounded-2xl border ${cardCls} px-3.5 py-3 transition">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-xs font-extrabold text-white">${c.code} · ${c.city}</span>
                                    <span class="text-sm font-extrabold ${priceCls}">${c.price_pct > 0 ? '+' : ''}${c.price_pct}%</span>
                                </div>
                                <div class="mt-1.5 flex items-center justify-between gap-1">
                                    ${badge}
                                </div>
                                <div class="text-[11px] text-white/75 truncate mt-1.5">${c.note}</div>
                            </div>`;
                        }).join('');
                    }

                    // 4 ta Har Xil Davlat Issiq Eksport Signali
                    const grid = document.getElementById('aiSignalsGrid');
                    if (grid && Array.isArray(data.signals)) {
                        aiSignalsList = data.signals;
                        grid.innerHTML = data.signals.map((s, idx) => {
                            const badgeCls = s.urgency === 'hot' ? 'bg-amber-400 text-slate-950' : 'bg-emerald-400 text-slate-950';
                            const badgeTxt = s.urgency === 'hot' ? 'ISSIQ SIGNAL' : 'YUQORI TALAB';
                            return `<div class="glide-card rounded-2xl bg-white/[0.08] backdrop-blur border border-white/15 p-4 flex flex-col justify-between gap-3 hover:bg-white/[0.13] hover:border-emerald-400/40 transition" style="--i:${idx}">
                                <div>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold ${badgeCls}">${badgeTxt}</span>
                                        <span class="text-emerald-300 font-extrabold text-base">+${s.change_pct}%</span>
                                    </div>
                                    <div class="font-extrabold text-[15px] text-white mt-2.5">${s.country} · ${s.city}</div>
                                    <div class="text-xs font-bold text-emerald-300 mt-0.5">${s.product}</div>
                                    <div class="mt-2.5 grid grid-cols-2 gap-1.5 bg-slate-950/40 rounded-xl p-2 border border-white/10 text-[11px]">
                                        <div><span class="text-white/55 block text-[10px]">O‘zbekistonda</span><b class="text-white">${fmt(s.uz_price)} so‘m</b></div>
                                        <div><span class="text-emerald-300/80 block text-[10px]">${s.city} bozorida</span><b class="text-emerald-300">${fmt(s.target_price)} so‘m</b></div>
                                    </div>
                                    ${sparkSvg(s.trend, idx)}
                                    <div class="flex items-center justify-between text-[11px] mt-1">
                                        <span class="px-2 py-0.5 rounded-md bg-amber-400/20 text-amber-200 font-bold">Zaxira ${s.supply_drop_pct}%</span>
                                        <span class="text-emerald-200 font-bold">AI: ${s.confidence}% · ${s.forecast_7d}</span>
                                    </div>
                                    <p class="text-[11.5px] text-emerald-50/75 mt-2 leading-relaxed">${s.reason}</p>
                                </div>
                                <div class="pt-2.5 border-t border-white/10 space-y-2.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-white/70">Sof foyda / kg:</span>
                                        <span class="font-extrabold text-emerald-300 text-sm">+${fmt(s.net_profit_kg)} so‘m</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" onclick="pickSignalIdx(${idx})" class="py-2 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 text-xs font-extrabold transition">Hisoblash</button>
                                        <button type="button" onclick="watchSignalIdx(${idx})" class="py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition">Kuzatish</button>
                                    </div>
                                </div>
                            </div>`;
                        }).join('');
                    }

                    // Narx tushib ketgan davlatlar (Xavf ogohlantirishlari)
                    const dropsEl = document.getElementById('aiDropsGrid');
                    if (dropsEl && Array.isArray(data.drops)) {
                        dropsEl.innerHTML = data.drops.map((d, idx) => `
                            <div class="glide-card rounded-2xl bg-rose-950/50 border border-rose-400/35 p-4 flex flex-col justify-between gap-2.5 hover:border-rose-400/60 transition" style="--i:${idx}">
                                <div>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-extrabold">NARX TUSHDI</span>
                                        <span class="text-rose-300 font-extrabold text-base">${d.drop_pct}%</span>
                                    </div>
                                    <div class="font-extrabold text-[15px] text-white mt-2">${d.country} · ${d.city}</div>
                                    <div class="text-xs font-bold text-rose-200 mt-0.5">${d.product} · <span class="text-rose-300">Ortiqcha +${d.surplus_pct}%</span></div>
                                    <p class="text-[11.5px] text-rose-100/80 mt-1.5 leading-relaxed">${d.reason}</p>
                                </div>
                                <div class="rounded-xl bg-emerald-500/15 border border-emerald-400/30 px-3 py-2 text-[11.5px] font-bold text-emerald-200">
                                    AI Yechim: ${d.action}
                                </div>
                            </div>`).join('');
                    }

                    if (manual) aiNotify('AI bozor signallari real vaqtda yangilandi (' + data.updated_at + ')');
                })
                .catch(() => {});
        }

        function askAiQuick(text) {
            const inp = document.getElementById('aiChatInp');
            if (inp) inp.value = text;
            sendAiChat(null, text);
        }

        function sendAiChat(ev, preset) {
            if (ev) ev.preventDefault();
            const inp = document.getElementById('aiChatInp');
            const out = document.getElementById('aiChatOut');
            const btn = document.getElementById('aiChatBtn');
            const message = (preset || inp?.value || '').trim();
            if (!message || !out) return;
            if (btn) btn.textContent = 'Tahlil...';
            fetch('/api/ai-chat', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ message })
            })
            .then(r => r.json())
            .then(d => {
                out.classList.remove('hidden');
                const metricsHtml = (d.metrics || []).map(m =>
                    `<div class="rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-2"><div class="text-[10.5px] font-bold uppercase text-emerald-700">${m.label}</div><div class="text-xs sm:text-sm font-extrabold text-slate-900 mt-0.5">${m.value}</div></div>`
                ).join('');
                const checkHtml = (d.checklist || []).length
                    ? `<div class="space-y-1 pt-1"><div class="text-[11px] font-extrabold uppercase text-slate-500">AI Tavsiya Qadamlari</div><ul class="space-y-1 text-xs text-slate-700 list-disc pl-4">${d.checklist.map(c => `<li>${c}</li>`).join('')}</ul></div>`
                    : '';
                const matchHtml = (d.matches || []).length
                    ? `<div class="pt-2 border-t border-slate-100"><div class="text-[11px] font-extrabold uppercase text-slate-500 mb-2">Bozordagi mos jonli e’lonlar</div><div class="grid grid-cols-1 sm:grid-cols-3 gap-2">${d.matches.map(m => `<a href="${m.url}" class="block rounded-xl border border-slate-200 hover:border-emerald-500 p-2.5 transition"><div class="text-xs font-bold text-slate-900 line-clamp-1">${m.title}</div><div class="text-xs font-extrabold text-emerald-700 mt-0.5">${m.price}</div><div class="text-[11px] text-slate-500">${m.region || ''}</div></a>`).join('')}</div></div>`
                    : '';
                out.innerHTML = `<div class="flex items-center justify-between text-[11px] font-bold text-emerald-700"><span>ZaminJob AI Javobi</span><span class="text-slate-400">${d.time || ''}</span></div>
                    <p class="text-xs sm:text-sm leading-relaxed text-slate-800 font-medium">${d.reply || ''}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">${metricsHtml}</div>
                    ${checkHtml}
                    ${matchHtml}`;
            })
            .finally(() => { if (btn) btn.textContent = 'AI tahlil qilsin'; });
        }

        function suggestAiPrice() {
            const category = document.getElementById('catSel')?.value || 'Mevalar';
            const subcategory = document.getElementById('subSel')?.value || 'Olma';
            fetch('/api/ai-price-suggest', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ category, subcategory })
            })
            .then(r => r.json())
            .then(d => {
                const inp = document.getElementById('listPriceInp');
                const unitSel = document.getElementById('listUnitSel');
                const hint = document.getElementById('aiPriceHint');
                if (inp && d.price) inp.value = d.price;
                if (unitSel && d.unit) unitSel.value = d.unit;
                if (hint) {
                    hint.textContent = `AI tavsiyasi: ${fmt(d.price)} so‘m (eksport: ~${fmt(d.export_price)} so‘m) — ${d.note}`;
                    hint.classList.remove('hidden');
                }
            })
            .catch(() => {});
        }

        function pickSignalIdx(idx) {
            const s = aiSignalsList[idx];
            if (!s) return;
            const sel = document.getElementById('aiProd');
            if (sel) sel.value = s.product;
            runAiAnalyze();
        }

        function watchSignalIdx(idx) {
            const s = aiSignalsList[idx];
            if (!s) return;
            fetch('/api/ai-watch', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ product: s.product, country: s.country, pct: s.change_pct })
            })
            .then(r => r.json())
            .then(d => aiNotify(d.message || 'Kuzatuv yoqildi!'))
            .catch(() => aiNotify('Kuzatuv yoqildi!'));
        }

        function runAiAnalyze() {
            const product = document.getElementById('aiProd')?.value || '';
            const tons = document.getElementById('aiTons')?.value || 10;
            const region = document.getElementById('aiReg')?.value || 'Jizzax viloyati';
            fetch('/api/ai-analyze', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ product, tons, region })
            })
            .then(r => r.json())
            .then(d => {
                const box = document.getElementById('aiResultBox');
                if (box) box.classList.remove('hidden');
                document.getElementById('aiBestTitle').textContent = `${d.product} (${d.tons}t) → ${d.best.country} (${d.best.city}) · ${d.best.days}`;
                document.getElementById('aiBestNet').textContent = `Sof foyda: +${fmt(d.best.net_profit)} so‘m (+${d.best.margin_pct}%)`;
                document.getElementById('aiPoolingTip').textContent = d.pooling_tip;

                const maxNet = Math.max(...d.routes.map(r => r.net_profit), 1);
                document.getElementById('aiRoutesBars').innerHTML = d.routes.map((r, i) => {
                    const w = Math.max(12, Math.round((r.net_profit / maxNet) * 100));
                    return `<div class="text-xs">
                        <div class="flex justify-between font-bold mb-1">
                            <span>${i + 1}. ${r.country} (${r.city}) · <span class="text-slate-400 font-semibold">${r.days}</span></span>
                            <span class="text-emerald-700">+${fmt(r.net_profit)} so‘m</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full ${i === 0 ? 'bg-emerald-600' : 'bg-emerald-400/80'}" style="width:${w}%"></div>
                        </div>
                    </div>`;
                }).join('');

                lastOffers = d.offers || lastOffers;
                showOfferLang(activeOfferLang);
            })
            .catch(() => {});
        }

        function showOfferLang(lang) {
            activeOfferLang = lang;
            document.getElementById('aiOfferText').textContent = lastOffers[lang] || '';
            document.querySelectorAll('.olang-btn').forEach(b => {
                const on = b.dataset.olang === lang;
                b.className = 'olang-btn px-2.5 py-1 rounded-lg text-[11px] font-bold ' + (on ? 'bg-emerald-500 text-slate-950' : 'bg-white/10 text-white');
            });
        }

        function copyAiOffer() {
            const txt = document.getElementById('aiOfferText')?.textContent || '';
            if (!txt) return;
            navigator.clipboard?.writeText(txt).then(() => {
                const b = document.getElementById('aiCopyBtn');
                if (b) { b.textContent = 'Nusxalandi!'; setTimeout(() => b.textContent = 'Taklifni nusxalash (Telegram / WhatsApp)', 1800); }
            });
        }

        loadAiSignals(false);
        setInterval(() => loadAiSignals(false), 15000);
        setTimeout(runAiAnalyze, 400);

        // E'lon kartalari uchun silliq ketma-ket scroll animatsiyasi
        const bozorObs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) { e.target.classList.add('in'); bozorObs.unobserve(e.target); }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
        document.querySelectorAll('.bozor-card').forEach(el => {
            const r = el.getBoundingClientRect();
            if (r.top < window.innerHeight * 0.95) el.classList.add('in');
            else bozorObs.observe(el);
        });
    </script>
</body>
</html>
