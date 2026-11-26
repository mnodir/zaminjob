<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mening kabinetim — ZaminJob</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='10' fill='%23102c20'/%3E%3Cpath d='M9 11h22v4.4L16.2 26.6H31V31H9v-4.4L23.8 15.4H9V11z' fill='%23ffffff'/%3E%3Cpath d='M12 24.5c2.2-6 8.2-9.2 15.5-8.5-2.6 5.6-7.8 9.2-15.5 8.5z' fill='%2316a34a'/%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes glideR { from { opacity: 0; transform: translate3d(26px, 0, 0); } to { opacity: 1; transform: translate3d(0, 0, 0); } }
        main > * { animation: glideR .58s cubic-bezier(.16, 1, .3, 1) both; }
        main > *:nth-child(2) { animation-delay: 65ms; }
        main > *:nth-child(3) { animation-delay: 130ms; }
    </style>
</head>
<body class="bg-[#f7f9f5] text-slate-800 min-h-screen flex flex-col">
    @php
        $roleLabels = ['worker' => 'Ishchi', 'farmer' => 'Dehqon', 'employer' => 'Tadbirkor', 'tech' => 'Texnik xizmat', 'logistics' => 'Yuk tashish xizmati'];
        $payLabels = ['monthly' => 'Oylik', 'daily' => 'Kunbay', 'piecework' => 'Ishbay'];
        $role = $user->role ?: 'worker';
        $isEmployerSide = in_array($role, ['employer', 'farmer'], true);
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
                    <a href="{{ route('kabinet') }}" class="px-3.5 py-1.5 rounded-full bg-white text-emerald-700 shadow-sm">{{ $isEmployerSide ? 'Vakansiyalar va Ishchilar' : 'Vakansiyalar' }}</a>
                    <a href="{{ route('profil') }}" class="px-3.5 py-1.5 rounded-full text-slate-600 hover:text-slate-900">Profil va sozlamalar</a>
                    <a href="{{ route('bozor') }}" class="px-3.5 py-1.5 rounded-full text-slate-600 hover:text-slate-900">Mahsulotlar</a>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="hidden md:inline-block text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $roleLabels[$role] ?? 'Foydalanuvchi' }}</span>
                <a href="{{ route('profil') }}" class="w-9 h-9 rounded-full overflow-hidden border-2 border-emerald-600 bg-[#102c20] text-white font-bold text-sm flex items-center justify-center">
                    @if(!empty($user->avatar_path))
                        <img src="{{ asset($user->avatar_path) }}" class="w-full h-full object-cover" alt="">
                    @else
                        {{ mb_strtoupper(mb_substr($user->name ?? 'Z', 0, 1)) }}
                    @endif
                </a>
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

        @if(!$isEmployerSide)
            <!-- ISHCHI / TEXNIK XIZMAT / YUK TASHISH KABINETI -->
            <form action="{{ route('kabinet') }}" method="GET" class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-wrap gap-3 items-center">
                <input type="text" name="q" value="{{ $q }}" placeholder="Kasb, hudud yoki kalit so‘z bo‘yicha qidirish..." class="flex-1 min-w-[220px] text-sm border border-slate-300 rounded-xl px-3.5 py-2.5">
                <select name="region" class="text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white">
                    <option value="">Barcha viloyatlar</option>
                    @foreach($regions as $r => $ds)
                        <option value="{{ $r }}" {{ $selReg === $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700">Qidirish</button>
                <a href="{{ route('bozor') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800">Mahsulotlar bozori</a>
            </form>

            <div class="space-y-3">
                <h1 class="text-lg font-extrabold text-slate-900">Faol vakansiyalar ({{ count($vacancies) }})</h1>
                @forelse($vacancies as $v)
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700">{{ $v->profession }}</span>
                                @if(!empty($v->region))
                                    <span class="text-xs font-medium px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700">{{ $v->region }}{{ !empty($v->district) ? ', '.$v->district : '' }}</span>
                                @endif
                                @if(!empty($v->salary))
                                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md">{{ $v->salary }} ({{ $payLabels[$v->payment_type] ?? 'Oylik' }})</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-lg text-slate-900">{{ $v->title }}</h3>
                            <p class="text-sm text-slate-600 mt-1">{{ $v->description }}</p>
                            <p class="text-xs text-slate-400 mt-2">Ish beruvchi: {{ $v->employer_name }}</p>
                        </div>
                        <div class="shrink-0">
                            @if(isset($appliedMap[$v->id]))
                                @php $st = $appliedMap[$v->id]; @endphp
                                <span class="inline-block px-4 py-2 rounded-xl text-xs font-bold {{ $st === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($st === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $st === 'accepted' ? 'Qabul qilindingiz' : ($st === 'rejected' ? 'Rad etildi' : 'Ariza yuborilgan') }}
                                </span>
                            @else
                                <form action="{{ route('kabinet.apply', $v->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl hover:bg-emerald-700 transition">Ish olish</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-sm text-slate-500">Hozircha faol vakansiyalar yo‘q. Mahsulot va xizmatlar bozorini ko‘rishingiz mumkin.</div>
                @endforelse
            </div>
        @else
            <!-- DEHQON / TADBIRKOR KABINETI -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 h-fit">
                    <h2 class="font-extrabold text-lg text-slate-900 mb-1">+ Vakansiya joylash</h2>
                    <p class="text-xs text-slate-500 mb-4">E’lon ishchi, texnik xizmat va yuk tashuvchilarga darhol ko‘rinadi</p>
                    <form action="{{ route('kabinet.vacancy') }}" method="POST" class="space-y-3">
                        @csrf
                        <div><label class="block text-xs font-bold text-slate-700 mb-1">Sarlavha</label><input type="text" name="title" required class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2"></div>
                        <div><label class="block text-xs font-bold text-slate-700 mb-1">Kerakli kasb</label><input type="text" name="profession" required class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2"></div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Viloyat</label>
                            <select name="region" required class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2 bg-white">
                                @foreach($regions as $r => $ds)<option value="{{ $r }}" {{ ($user->region ?? '') === $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div><label class="block text-xs font-bold text-slate-700 mb-1">Maosh</label><input type="text" name="salary" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2"></div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">To‘lov turi</label>
                                <select name="payment_type" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2 bg-white">
                                    @foreach($payLabels as $pk => $pl)<option value="{{ $pk }}">{{ $pl }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <div><label class="block text-xs font-bold text-slate-700 mb-1">Ish sharoitlari</label><textarea name="description" rows="3" required class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2"></textarea></div>
                        <button type="submit" class="w-full bg-emerald-600 text-white text-sm font-bold py-2.5 rounded-xl hover:bg-emerald-700 transition">E’lon qilish</button>
                    </form>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="space-y-3">
                        <h2 class="text-lg font-extrabold text-slate-900">Mening vakansiyalarim ({{ count($myVacancies) }})</h2>
                        @forelse($myVacancies as $v)
                            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-bold text-base text-slate-900">{{ $v->title }}</h3>
                                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold">{{ $v->profession }}</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1">{{ $v->description }}</p>
                                @php $apps = $appsByVac[$v->id] ?? []; @endphp
                                <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
                                    <div class="text-[11px] font-bold uppercase text-slate-400">Arizalar ({{ count($apps) }})</div>
                                    @forelse($apps as $a)
                                        <div class="flex flex-wrap items-center justify-between gap-2 bg-slate-50 p-3 rounded-xl">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-xs text-slate-900">{{ $a->worker_name }}</span>
                                                    <span class="text-[10.5px] font-extrabold px-2 py-0.5 rounded-full {{ ((int)($a->worker_trust ?? 95)) >= 80 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">Agro-Pasport: {{ (int)($a->worker_trust ?? 95) }}/100</span>
                                                </div>
                                                <div class="text-xs text-blue-600 font-semibold">{{ $a->worker_phone }}</div>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                @if($a->status === 'pending')
                                                    <form action="{{ route('kabinet.app', $a->id) }}" method="POST">@csrf<input type="hidden" name="status" value="accepted"><button class="bg-emerald-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg">Qabul</button></form>
                                                    <form action="{{ route('kabinet.app', $a->id) }}" method="POST">@csrf<input type="hidden" name="status" value="rejected"><button class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1.5 rounded-lg">Rad etish</button></form>
                                                @else
                                                    <span class="text-xs font-bold px-3 py-1 rounded-lg {{ $a->status === 'accepted' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $a->status === 'accepted' ? 'Qabul qilingan' : 'Rad etilgan' }}</span>
                                                    @if($a->status === 'accepted')
                                                        <form action="{{ route('trust.penalty', $a->worker_id) }}" method="POST">@csrf<button class="bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 text-[11px] font-extrabold px-2.5 py-1 rounded-lg transition">Ishga chiqmadi (−15 ball)</button></form>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-400">Hali ariza kelmagan.</p>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-2xl border border-slate-200 p-6 text-xs text-slate-500">Hali vakansiya joylamagansiz.</div>
                        @endforelse
                    </div>

                    <div class="space-y-3">
                        <h2 class="text-base font-extrabold text-slate-900">Ishchilar, texnik xizmat va yuk tashuvchilar ({{ count($workers) }})</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($workers as $w)
                                <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-start gap-3">
                                    <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold shrink-0 overflow-hidden">
                                        @if(!empty($w->avatar_path))<img src="{{ asset($w->avatar_path) }}" class="w-full h-full object-cover">@else{{ mb_strtoupper(mb_substr($w->name ?? 'Z', 0, 1)) }}@endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-bold text-sm text-slate-900 truncate">{{ $w->name }}</span>
                                            <span class="text-[10.5px] font-extrabold px-2 py-0.5 rounded-full shrink-0 {{ ((int)($w->trust_score ?? 95)) >= 80 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ (int)($w->trust_score ?? 95) }}/100</span>
                                        </div>
                                        <div class="text-xs font-bold text-emerald-700">{{ $w->profession ?: ($roleLabels[$w->role] ?? 'Mutaxassis') }}</div>
                                        @if(!empty($w->region))<div class="text-xs text-slate-500">{{ $w->region }}{{ !empty($w->district) ? ', '.$w->district : '' }}</div>@endif
                                        <div class="text-xs font-semibold text-blue-600 mt-1">{{ $w->phone }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>
</body>
</html>
