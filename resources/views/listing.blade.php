<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $item->title }} — ZaminJob</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f7f9f5] text-slate-800 min-h-screen pb-24">
    @php
        $imgs = json_decode((string)$item->images_json, true) ?: ['https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1000&q=80'];
        $months = [1=>'yanvar','fevral','mart','aprel','may','iyun','iyul','avgust','sentabr','oktabr','noyabr','dekabr'];
        $ts = strtotime($item->created_at ?? 'now');
        $uzDate = date('j', $ts) . ' ' . ($months[(int)date('n', $ts)] ?? 'noyabr') . ' ' . date('Y', $ts);
        $telClean = preg_replace('/[^\d+]/', '', (string)$item->seller_phone);
        $handleClean = ltrim(trim((string)($item->seller_handle ?? '')), '@');
    @endphp

    <nav class="bg-white/95 backdrop-blur border-b border-emerald-100 sticky top-0 z-40">
        <div class="max-w-3xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="{{ route('bozor') }}" class="text-sm font-bold text-slate-700 hover:text-emerald-700">← Bozorga qaytish</a>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700">{{ $item->category }} · {{ $item->subcategory }}</span>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-5 space-y-5">
        @if(session('success'))
            <div class="px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="relative bg-slate-950 h-[340px] sm:h-[440px]">
                <img id="mainImg" src="{{ $imgs[0] }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                <span id="imgCnt" class="absolute bottom-4 left-4 px-3 py-1 rounded-full bg-black/70 text-white text-xs font-bold">1 / {{ count($imgs) }}</span>
            </div>
            @if(count($imgs) > 1)
                <div class="p-3 bg-slate-50 border-t border-slate-100 flex gap-2.5 overflow-x-auto">
                    @foreach($imgs as $i => $src)
                        <button type="button" onclick="pickImg({{ $i }}, '{{ $src }}', {{ count($imgs) }})"
                                class="gal-t w-20 h-16 rounded-xl overflow-hidden border-2 shrink-0 transition {{ $i === 0 ? 'border-emerald-600' : 'border-transparent opacity-70 hover:opacity-100' }}">
                            <img src="{{ $src }}" class="w-full h-full object-cover" alt="">
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="p-6">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-400">{{ $uzDate }}</span>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">Faol e’lon</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-2">{{ $item->title }}</h1>
                <div class="text-3xl font-extrabold text-emerald-700 mt-2">{{ number_format((int)$item->price, 0, '.', ' ') }} <span class="text-base font-bold text-slate-500">{{ $item->unit }}</span></div>

                <div class="flex flex-wrap gap-2 mt-4">
                    <span class="px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">Toifa: {{ $item->subcategory }}</span>
                    @if($item->spec_1)<span class="px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">{{ $item->spec_1 }}</span>@endif
                    @if($item->spec_2)<span class="px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">{{ $item->spec_2 }}</span>@endif
                </div>

                <div class="mt-6 pt-5 border-t border-slate-100">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">TAVSIF</div>
                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $item->description }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-4">FOYDALANUVCHI</div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-[#102c20] text-white font-extrabold text-xl flex items-center justify-center overflow-hidden shrink-0">
                    @if($seller && !empty($seller->avatar_path))
                        <img src="{{ asset($seller->avatar_path) }}" class="w-full h-full object-cover" alt="">
                    @else
                        {{ mb_strtoupper(mb_substr($item->seller_name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <div class="font-extrabold text-lg text-slate-900">{{ $item->seller_name }}</div>
                    <div class="text-xs text-slate-500">ZaminJob’da {{ $uzDate }} dan beri{{ $item->seller_phone ? ' • '.$item->seller_phone : '' }}</div>
                </div>
            </div>
            <div class="mt-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-900">
                <strong>A’lo:</strong> Tasdiqlangan Agro-Pasport foydalanuvchisi — aksariyat xaridorlar bu e’lon egasidan mamnun.
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-1">MANZIL</div>
                <div class="font-extrabold text-lg text-slate-900">{{ $item->region }}</div>
                <div class="text-sm text-slate-600">{{ $item->district ?: 'Markaziy hudud' }}</div>
            </div>
            <div class="w-full sm:w-52 h-28 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center relative overflow-hidden">
                <svg class="w-full h-full opacity-40" viewBox="0 0 200 100"><path d="M0 30H200M0 70H200M50 0V100M130 0V100" stroke="#15803d" stroke-width="1.2"/></svg>
                <div class="absolute w-12 h-12 rounded-full bg-emerald-500/25 border-2 border-emerald-600 flex items-center justify-center">
                    <div class="w-3 h-3 rounded-full bg-emerald-700"></div>
                </div>
            </div>
        </div>

        <div id="msgBox" class="hidden bg-white rounded-3xl border border-emerald-200 p-6 shadow-sm">
            <h3 class="font-extrabold text-base text-slate-900 mb-3">E’lon egasiga saytdan xabar yozish</h3>
            <form action="{{ route('bozor.message', $item->id) }}" method="POST" class="space-y-3">
                @csrf
                @guest
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="sender_name" placeholder="Ismingiz" required class="text-sm border border-slate-300 rounded-xl px-3.5 py-2.5">
                        <input type="tel" name="sender_phone" placeholder="Telefon raqamingiz" required class="text-sm border border-slate-300 rounded-xl px-3.5 py-2.5">
                    </div>
                @endguest
                <textarea name="message" rows="3" placeholder="Xabaringizni yozing (narx, hajm, yetkazib berish)..." required class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5"></textarea>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700">Xabarni yuborish</button>
            </form>
        </div>

        @if($others->count() > 0)
        <div>
            <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-3">BOSHQA E’LONLAR</div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach($others as $ot)
                    @php $oImgs = json_decode((string)$ot->images_json, true) ?: []; @endphp
                    <a href="{{ route('bozor.show', $ot->id) }}" class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:border-emerald-400 transition">
                        <img src="{{ $oImgs[0] ?? '' }}" class="w-full h-28 object-cover" alt="">
                        <div class="p-3">
                            <div class="font-bold text-xs text-slate-900 truncate">{{ $ot->title }}</div>
                            <div class="text-sm font-extrabold text-emerald-700 mt-1">{{ number_format((int)$ot->price, 0, '.', ' ') }} so‘m</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </main>

    <div class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur border-t border-slate-200 p-3 z-40">
        <div class="max-w-3xl mx-auto grid grid-cols-3 gap-2.5">
            @if($telClean)
                <a href="tel:{{ $telClean }}" class="py-3 rounded-xl border-2 border-slate-900 text-slate-900 font-extrabold text-xs sm:text-sm text-center hover:bg-slate-900 hover:text-white transition">Qo‘ng‘iroq qilish</a>
            @else
                <button type="button" onclick="const b=document.getElementById('msgBox');b.classList.remove('hidden');b.scrollIntoView({behavior:'smooth'});" class="py-3 rounded-xl border-2 border-slate-900 text-slate-900 font-extrabold text-xs sm:text-sm text-center hover:bg-slate-900 hover:text-white transition">Bog‘lanish</button>
            @endif
            <button type="button" onclick="const b=document.getElementById('msgBox');b.classList.remove('hidden');b.scrollIntoView({behavior:'smooth'});" class="py-3 rounded-xl bg-emerald-600 text-white font-extrabold text-xs sm:text-sm text-center hover:bg-emerald-700 transition">Xabar yozish</button>
            <a href="https://telegram.me/{{ $handleClean ?: 'zaminjob_uz' }}" target="_blank" rel="noopener" class="py-3 rounded-xl bg-sky-600 text-white font-extrabold text-xs sm:text-sm text-center hover:bg-sky-700 transition">Telegram</a>
        </div>
    </div>

    <script>
        function pickImg(i, src, total) {
            document.getElementById('mainImg').src = src;
            document.getElementById('imgCnt').textContent = (i + 1) + ' / ' + total;
            document.querySelectorAll('.gal-t').forEach((el, k) => {
                el.classList.toggle('border-emerald-600', k === i);
                el.classList.toggle('border-transparent', k !== i);
                el.classList.toggle('opacity-70', k !== i);
            });
        }
    </script>
</body>
</html>
