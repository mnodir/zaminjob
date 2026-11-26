<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil va sozlamalar — ZaminJob</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='10' fill='%23102c20'/%3E%3Cpath d='M9 11h22v4.4L16.2 26.6H31V31H9v-4.4L23.8 15.4H9V11z' fill='%23ffffff'/%3E%3Cpath d='M12 24.5c2.2-6 8.2-9.2 15.5-8.5-2.6 5.6-7.8 9.2-15.5 8.5z' fill='%2316a34a'/%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes glideL { from { opacity: 0; transform: translate3d(-28px, 0, 0); } to { opacity: 1; transform: translate3d(0, 0, 0); } }
        @keyframes glideR { from { opacity: 0; transform: translate3d(28px, 0, 0); } to { opacity: 1; transform: translate3d(0, 0, 0); } }
        main .grid > *:first-child { animation: glideL .62s cubic-bezier(.16, 1, .3, 1) both; }
        main .grid > *:last-child { animation: glideR .66s cubic-bezier(.16, 1, .3, 1) 70ms both; }
    </style>
</head>
<body class="bg-[#f7f9f5] text-slate-800 min-h-screen flex flex-col">
    @php
        $roleLabels = ['worker' => 'Ishchi', 'farmer' => 'Dehqon', 'employer' => 'Tadbirkor', 'tech' => 'Texnik xizmat', 'logistics' => 'Yuk tashish xizmati', 'admin' => 'Admin'];
        $payLabels = ['monthly' => 'Oylik', 'daily' => 'Kunbay', 'piecework' => 'Ishbay'];
        $role = $isAdmin ? 'admin' : ($user->role ?: 'worker');
    @endphp

    <nav class="bg-white/95 backdrop-blur border-b border-emerald-100 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-3">
            <div class="flex items-center gap-5">
                <a href="{{ url('/') }}" class="flex items-center">
                    <svg width="136" height="32" viewBox="0 0 206 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 10H31.5V15.6L11.4 33.4H32V39H0.5V33.4L20.6 15.6H1V10Z" fill="#102C20"/>
                        <path d="M5.5 29.8C8.6 21.8 17.4 17.5 29.2 18.2C25.4 26.2 17.8 31.2 5.5 29.8Z" fill="#15803D"/>
                        <path d="M7.5 28.6C13.5 24.4 20.2 21.4 26.8 19.6" stroke="#F7F9F5" stroke-width="1.4" stroke-linecap="round"/>
                        <text x="33.5" y="38.5" fill="#102C20" font-family="system-ui, sans-serif" font-weight="800" font-size="34" letter-spacing="-1.3">aminJob</text>
                    </svg>
                </a>
                <div class="hidden sm:flex items-center gap-1 bg-slate-100 p-1 rounded-full text-xs font-bold">
                    <a href="{{ $isAdmin ? route('admin') : route('kabinet') }}" class="px-3.5 py-1.5 rounded-full text-slate-600 hover:text-slate-900">{{ $isAdmin ? 'Admin Panel' : 'Kabinet' }}</a>
                    <a href="{{ route('profil') }}" class="px-3.5 py-1.5 rounded-full bg-white text-emerald-700 shadow-sm">Profil va sozlamalar</a>
                    <a href="{{ route('bozor') }}" class="px-3.5 py-1.5 rounded-full text-slate-600 hover:text-slate-900">Mahsulotlar</a>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="hidden md:inline-block text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $roleLabels[$role] ?? 'Foydalanuvchi' }}</span>
                <a href="{{ url('/') }}" class="px-3.5 py-2 rounded-full text-xs font-bold border border-slate-200 bg-white text-slate-700 hover:border-emerald-600">Bosh sahifa</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl w-full mx-auto px-4 py-6">
        @if(session('success'))
            <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- CHAP KARTA (GITHUB PROFIL KO'RINISHI) -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 h-fit text-center shadow-sm">
                <div class="relative inline-block group cursor-pointer" onclick="document.getElementById('avatarFileInput').click()">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-emerald-100 bg-[#102c20] text-white flex items-center justify-center text-4xl font-extrabold mx-auto shadow-md">
                        @if(!empty($user->avatar_path))
                            <img id="avatarPreviewImg" src="{{ asset($user->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <img id="avatarPreviewImg" src="" alt="Avatar" class="w-full h-full object-cover hidden">
                            <span id="avatarFallbackLetter">{{ mb_strtoupper(mb_substr($user->name ?? 'Z', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="absolute bottom-1 right-1 w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-lg border-2 border-white group-hover:scale-110 transition">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                    </div>
                </div>

                <h2 class="font-extrabold text-xl text-slate-900 mt-4">{{ $user->name }}</h2>
                <div class="text-sm font-semibold text-emerald-700">{{ !empty($user->username) ? '@'.$user->username : $user->phone }}</div>
                <div class="mt-2 inline-block text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                    {{ $roleLabels[$role] ?? 'Foydalanuvchi' }}
                </div>
                @if(!empty($user->region))
                    <div class="text-xs text-slate-500 mt-3">{{ $user->region }}{{ !empty($user->district) ? ', '.$user->district : '' }}</div>
                @endif
                @if(!empty($user->bio))
                    <p class="text-xs text-slate-600 mt-3 leading-relaxed">{{ $user->bio }}</p>
                @endif

                <a href="{{ $isAdmin ? route('admin') : route('kabinet') }}" class="mt-5 inline-flex items-center justify-center w-full py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition">
                    {{ $isAdmin ? 'Admin panelga qaytish' : 'Mening kabinetimga o‘tish' }}
                </a>
            </div>

            <!-- O'NG KARTA (SOZLAMALAR FORMASI) -->
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm">
                <h1 class="text-xl font-extrabold text-slate-900 mb-1">Profil sozlamalari</h1>
                <p class="text-xs text-slate-500 mb-6">Ma’lumotlaringizni to‘ldiring — mijozlar va hamkorlar sizni tezroq topadi.</p>

                <form action="{{ route('profil.save') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <input type="file" name="avatar" id="avatarFileInput" accept="image/*" class="hidden">

                    <div onclick="document.getElementById('avatarFileInput').click()" class="cursor-pointer border-2 border-dashed border-emerald-200 hover:border-emerald-500 bg-emerald-50/40 rounded-2xl p-4 flex items-center justify-between gap-4 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">Profil rasmini yuklash yoki o‘zgartirish</div>
                                <div id="avatarFileLabel" class="text-xs text-slate-500">JPG, PNG yoki WEBP (5 MB gacha) — bosib tanlang</div>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-3.5 py-2 rounded-xl bg-white border border-emerald-200 text-emerald-700 shrink-0">Rasm tanlash</span>
                    </div>

                    @if(!$isAdmin)
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Sohangiz</label>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                            @foreach(['worker' => 'Ishchi', 'farmer' => 'Dehqon', 'employer' => 'Tadbirkor', 'tech' => 'Texnik xizmat', 'logistics' => 'Yuk tashish'] as $rk => $rl)
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="{{ $rk }}" class="peer hidden" onchange="toggleRoleBlocks('{{ $rk }}')" {{ $role === $rk ? 'checked' : '' }}>
                                    <div class="text-center py-2.5 px-2 rounded-xl border border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-800 font-bold text-xs transition">{{ $rl }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ism va familiya</label>
                            <input type="text" name="name" value="{{ $user->name }}" required class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Username (avtomatik tekshiriladi)</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 text-slate-400 font-bold text-sm">@</span>
                                <input type="text" name="username" id="profUsername" value="{{ $user->username ?? '' }}" pattern="[a-zA-Z0-9_]{3,40}" class="w-full text-sm border border-slate-300 rounded-xl pl-8 pr-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                            </div>
                            <span id="profUsernameStatus" class="hidden text-xs font-bold mt-1 block"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Viloyat</label>
                            <select name="region" id="profRegion" onchange="syncDist()" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                                <option value="">Viloyatni tanlang</option>
                                @foreach($regions as $reg => $dists)
                                    <option value="{{ $reg }}" {{ ($user->region ?? '') === $reg ? 'selected' : '' }}>{{ $reg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tuman / Shahar</label>
                            <select name="district" id="profDistrict" data-selected="{{ $user->district ?? '' }}" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                                <option value="">Avval viloyatni tanlang</option>
                            </select>
                        </div>
                    </div>

                    @if(!$isAdmin)
                    <div id="roleBlockWorker" class="{{ in_array($role, ['worker', 'tech', 'logistics']) ? '' : 'hidden' }} space-y-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kasbingiz / Xizmat turi</label>
                                <input type="text" name="profession" value="{{ $workerProfile->profession ?? '' }}" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                            <div id="equipField" class="{{ in_array($role, ['tech', 'logistics']) ? '' : 'hidden' }}">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Texnika / Yuk mashina rusumi</label>
                                <input type="text" name="equipment_type" value="{{ $workerProfile->equipment_type ?? '' }}" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kutilayotgan maosh / narx (so‘m)</label>
                                <input type="text" name="expected_salary" value="{{ $workerProfile->expected_salary ?? '' }}" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">To‘lov turi</label>
                                <select name="payment_type" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                                    @foreach($payLabels as $pk => $pl)
                                        <option value="{{ $pk }}" {{ ($workerProfile->payment_type ?? 'monthly') === $pk ? 'selected' : '' }}>{{ $pl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="roleBlockEmployer" class="{{ in_array($role, ['farmer', 'employer']) ? '' : 'hidden' }} space-y-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Xo‘jalik / Kompaniya nomi</label>
                                <input type="text" name="company_name" value="{{ $employerProfile->company_name ?? '' }}" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kerakli kasb / mutaxassis</label>
                                <input type="text" name="needed_profession" value="{{ $employerProfile->needed_profession ?? '' }}" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                        </div>
                        <div id="farmerFields" class="{{ $role === 'farmer' ? '' : 'hidden' }} grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Yer maydoni (gektar)</label>
                                <input type="text" name="land_area" value="{{ $employerProfile->land_area ?? '' }}" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Asosiy ekin turi</label>
                                <input type="text" name="crop_type" value="{{ $employerProfile->crop_type ?? '' }}" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Sug‘orish turi</label>
                                <input type="text" name="irrigation_type" value="{{ $employerProfile->irrigation_type ?? '' }}" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">O‘zingiz / xo‘jaligingiz haqida</label>
                        <textarea name="bio" rows="3" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">{{ $user->bio ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Telegram (@username yoki Chat ID)</label>
                        <input type="text" name="telegram" value="{{ $user->telegram ?? '' }}" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition shadow-sm">
                        O‘zgarishlarni saqlash
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        const REGS = @json($regions);
        function syncDist() {
            const r = document.getElementById('profRegion'), d = document.getElementById('profDistrict');
            if (!r || !d) return;
            const list = REGS[r.value] || [];
            const sel = d.dataset.selected || '';
            d.innerHTML = `<option value="">${list.length ? 'Tuman / Shaharni tanlang' : 'Avval viloyatni tanlang'}</option>` +
                list.map(x => `<option value="${x}" ${x === sel ? 'selected' : ''}>${x}</option>`).join('');
            d.dataset.selected = '';
        }
        syncDist();

        function toggleRoleBlocks(role) {
            const w = document.getElementById('roleBlockWorker'), e = document.getElementById('roleBlockEmployer');
            const eq = document.getElementById('equipField'), fm = document.getElementById('farmerFields');
            if (!w || !e) return;
            const isW = (role === 'worker' || role === 'tech' || role === 'logistics');
            w.classList.toggle('hidden', !isW);
            e.classList.toggle('hidden', isW);
            if (eq) eq.classList.toggle('hidden', role !== 'tech' && role !== 'logistics');
            if (fm) fm.classList.toggle('hidden', role !== 'farmer');
        }

        const avInp = document.getElementById('avatarFileInput');
        if (avInp) avInp.addEventListener('change', () => {
            const f = avInp.files && avInp.files[0];
            if (!f) return;
            document.getElementById('avatarFileLabel').textContent = f.name + ' tanlandi — saqlash tugmasini bosing';
            const rd = new FileReader();
            rd.onload = e => {
                const img = document.getElementById('avatarPreviewImg'), fb = document.getElementById('avatarFallbackLetter');
                if (img) { img.src = e.target.result; img.classList.remove('hidden'); }
                if (fb) fb.classList.add('hidden');
            };
            rd.readAsDataURL(f);
        });

        const uInp = document.getElementById('profUsername'), uStat = document.getElementById('profUsernameStatus');
        let uTimer = null;
        if (uInp && uStat) {
            uInp.addEventListener('input', () => {
                clearTimeout(uTimer);
                const val = uInp.value.replace(/^@+/, '').trim();
                if (!val) { uStat.classList.add('hidden'); return; }
                uStat.classList.remove('hidden', 'text-emerald-700', 'text-red-600');
                uStat.classList.add('text-slate-500');
                uStat.textContent = 'Tekshirilmoqda...';
                uTimer = setTimeout(() => {
                    fetch('/api/check-username?u=' + encodeURIComponent(val), { credentials: 'same-origin' })
                        .then(r => r.json())
                        .then(d => {
                            uStat.classList.remove('text-slate-500', 'text-emerald-700', 'text-red-600');
                            uStat.classList.add(d.status === 'free' ? 'text-emerald-700' : 'text-red-600');
                            uStat.textContent = d.message;
                        }).catch(() => {});
                }, 280);
            });
        }
    </script>
</body>
</html>
