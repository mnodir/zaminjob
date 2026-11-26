<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $item->title }} — ZaminJob</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' rx='10' fill='%23102c20'/%3E%3Cpath d='M9 11h22v4.4L16.2 26.6H31V31H9v-4.4L23.8 15.4H9V11z' fill='%23ffffff'/%3E%3Cpath d='M12 24.5c2.2-6 8.2-9.2 15.5-8.5-2.6 5.6-7.8 9.2-15.5 8.5z' fill='%2316a34a'/%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes sideIn { from { opacity: 0; transform: translate3d(26px, 0, 0); } to { opacity: 1; transform: translate3d(0, 0, 0); } }
        main > * { animation: sideIn .62s cubic-bezier(.16, 1, .3, 1) both; }
        main > *:nth-child(2) { animation-delay: 70ms; }
        main > *:nth-child(3) { animation-delay: 140ms; }
        main > *:nth-child(4) { animation-delay: 210ms; }
        main > *:nth-child(5) { animation-delay: 280ms; }
    </style>
</head>
<body class="bg-[#f7f9f5] text-slate-800 min-h-screen pb-24">
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
        $meta = $subMap[$item->subcategory] ?? ['img' => 'images/listings/olma.jpg', 'vid' => 'videos/15909400-uhd_3840_2160_25fps.mp4#t=4'];
        $rawImgs = json_decode((string)$item->images_json, true) ?: [];
        $hasRealImg = !empty($rawImgs[0]) || file_exists(public_path($meta['img']));
        $imgs = !empty($rawImgs) ? $rawImgs : [asset($meta['img'])];
        $vidFallback = asset($meta['vid']);
        $months = [1=>'yanvar','fevral','mart','aprel','may','iyun','iyul','avgust','sentabr','oktabr','noyabr','dekabr'];
        $ts = strtotime($item->created_at ?? 'now');
        $uzDate = date('j', $ts) . ' ' . ($months[(int)date('n', $ts)] ?? 'noyabr') . ' ' . date('Y', $ts);
        $telClean = preg_replace('/[^\d+]/', '', (string)$item->seller_phone);
        $waRaw = ($seller && !empty($seller->whatsapp)) ? $seller->whatsapp : ($item->seller_phone ?: '+998904047170');
        $waDigits = preg_replace('/[^\d]/', '', (string)$waRaw) ?: '998904047170';
        $waText = rawurlencode('Assalomu alaykum! ZaminJob platformasidagi «' . $item->title . '» e’loningiz bo‘yicha yozayapman.');
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
                @if($hasRealImg)
                    <img id="mainImg" src="{{ $imgs[0] }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                @else
                    <video muted playsinline autoplay loop class="w-full h-full object-cover" src="{{ $vidFallback }}"></video>
                @endif
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

                @php
                    $isPhotoOk = (int)($item->photo_verified ?? 1) === 1;
                    $metaLabel = $item->photo_meta ?: ('GPS: ' . ($item->region ?: 'O‘zbekiston') . ' · ' . $uzDate);
                @endphp
                <div class="mt-4 p-3.5 rounded-2xl border {{ $isPhotoOk ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900' : 'bg-amber-50 border-amber-200 text-amber-900' }} flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="text-xs font-extrabold">{{ $isPhotoOk ? '1-qavat himoya: GPS + Vaqt metadatasi tasdiqlangan' : '1-qavat himoya: Rasm metadatasi moderatsiyada' }}</div>
                        <div class="text-[11.5px] opacity-85 mt-0.5">{{ $metaLabel }}</div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold {{ $isPhotoOk ? 'bg-emerald-600 text-white' : 'bg-amber-400 text-slate-950' }}">{{ $isPhotoOk ? 'Real dala ✓' : 'Tekshirilmoqda' }}</span>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-100">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2">TAVSIF</div>
                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $item->description }}</p>
                </div>
            </div>
        </div>

        <!-- 3-QAVAT HIMOYA: ZAMINJOB ESCROW (XAVFSIZ BITIM) -->
        @php $isPerKg = mb_strpos(mb_strtolower((string)$item->unit), 'kg') !== false; @endphp
        <div class="bg-gradient-to-br from-[#0e3223] to-[#102c20] text-white rounded-3xl p-6 shadow-md border border-emerald-800/60 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <div class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-300">3-QAVAT HIMOYA · ZAMINJOB ESCROW</div>
                    <h2 class="text-lg font-extrabold mt-0.5">Xavfsiz bitim va 3% kafolat depoziti</h2>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-400/20 border border-emerald-400/40 text-emerald-200 text-xs font-extrabold">100% pul himoyasi</span>
            </div>

            <div class="grid grid-cols-3 gap-2 text-[11px]">
                @php $st = $escrowDeal->status ?? ''; @endphp
                <div class="rounded-xl p-2.5 border {{ in_array($st, ['funded','shipped','completed','disputed']) ? 'bg-emerald-500/25 border-emerald-400 text-white' : 'bg-white/5 border-white/15 text-emerald-100/80' }}">
                    <div class="font-extrabold">1. Pul muzlatiladi</div>
                    <div class="opacity-75 mt-0.5">3% kafolat + summa</div>
                </div>
                <div class="rounded-xl p-2.5 border {{ in_array($st, ['shipped','completed','disputed']) ? 'bg-emerald-500/25 border-emerald-400 text-white' : 'bg-white/5 border-white/15 text-emerald-100/80' }}">
                    <div class="font-extrabold">2. GPS bilan yuklash</div>
                    <div class="opacity-75 mt-0.5">Foto + fura dalili</div>
                </div>
                <div class="rounded-xl p-2.5 border {{ $st === 'completed' ? 'bg-emerald-500/25 border-emerald-400 text-white' : 'bg-white/5 border-white/15 text-emerald-100/80' }}">
                    <div class="font-extrabold">3. To‘lov ochiladi</div>
                    <div class="opacity-75 mt-0.5">Yuk qabul qilingach</div>
                </div>
            </div>

            @if($escrowDeal && $escrowDeal->status !== 'completed')
                <div class="bg-white/10 border border-white/15 rounded-2xl p-4 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                        <div>
                            <span class="text-emerald-200">Bitim #{{ $escrowDeal->id }} · Hajm: <b>{{ (float)$escrowDeal->qty_tons }} {{ $isPerKg ? 'tonna' : 'birlik' }}</b></span>
                            <div class="text-base font-extrabold text-white mt-0.5">{{ number_format((int)$escrowDeal->amount, 0, '.', ' ') }} so‘m <span class="text-xs font-semibold text-emerald-300">(3% depozit: {{ number_format((int)$escrowDeal->deposit_amount, 0, '.', ' ') }} so‘m)</span></div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold {{ $escrowDeal->status === 'disputed' ? 'bg-rose-500 text-white' : 'bg-amber-400 text-slate-950' }}">
                            {{ $escrowDeal->status === 'funded' ? '1-bosqich: Pul muzlatilgan' : ($escrowDeal->status === 'shipped' ? '2-bosqich: Yo‘lda (GPS)' : 'Nizo — Hakamlikda') }}
                        </span>
                    </div>
                    @if($escrowDeal->gps_proof)
                        <div class="text-xs bg-emerald-950/60 border border-emerald-400/30 rounded-xl px-3 py-2 text-emerald-200 font-semibold">{{ $escrowDeal->gps_proof }}</div>
                    @endif
                    @auth
                        <div class="flex flex-wrap gap-2">
                            @if($escrowDeal->status === 'funded')
                                <form action="{{ route('escrow.step', $escrowDeal->id) }}" method="POST">@csrf<input type="hidden" name="action" value="shipped"><button class="px-4 py-2 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 text-xs font-extrabold transition">2-bosqich: Yuk GPS + foto bilan jo‘natildi</button></form>
                            @elseif($escrowDeal->status === 'shipped')
                                <form action="{{ route('escrow.step', $escrowDeal->id) }}" method="POST">@csrf<input type="hidden" name="action" value="completed"><button class="px-4 py-2 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 text-xs font-extrabold transition">3-bosqich: Yukni qabul qildim — to‘lovni ochish</button></form>
                                <form action="{{ route('escrow.step', $escrowDeal->id) }}" method="POST">@csrf<input type="hidden" name="action" value="disputed"><button class="px-4 py-2 rounded-xl bg-rose-500/85 hover:bg-rose-500 text-white text-xs font-bold transition">Nizo ochish (Arbitraj)</button></form>
                            @else
                                <form action="{{ route('escrow.step', $escrowDeal->id) }}" method="POST">@csrf<input type="hidden" name="action" value="completed"><button class="px-4 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-bold transition">Nizoni yopish va to‘lovni ochish</button></form>
                            @endif
                        </div>
                    @endauth
                </div>
            @else
                @if($escrowDeal && $escrowDeal->status === 'completed')
                    <div class="rounded-xl bg-emerald-500/20 border border-emerald-400/40 px-3.5 py-2.5 text-xs font-bold text-emerald-100">
                        Oxirgi Escrow bitimi (#{{ $escrowDeal->id }}) muvaffaqiyatli yakunlandi — to‘lov sotuvchiga o‘tkazildi (+2 Agro-Pasport ball).
                    </div>
                @endif
                @auth
                    <form action="{{ route('bozor.escrow', $item->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-end">
                        @csrf
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-bold text-emerald-200 mb-1">Buyurtma hajmi ({{ $isPerKg ? 'tonna' : 'birlik' }})</label>
                            <input type="number" name="qty_tons" id="escrowQty" value="5" min="1" max="500" step="1" oninput="calcEscrow()" class="w-full text-sm text-slate-900 font-bold rounded-xl px-3.5 py-2.5 bg-white">
                        </div>
                        <div class="sm:col-span-4 rounded-xl bg-white/10 border border-white/15 px-3.5 py-2 text-xs">
                            <div class="text-emerald-200/80">Escrow + 3% depozit:</div>
                            <div id="escrowPreview" class="font-extrabold text-sm text-white mt-0.5">—</div>
                        </div>
                        <div class="sm:col-span-4">
                            <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 text-xs font-extrabold transition">Escrow bitim ochish</button>
                        </div>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="inline-block px-5 py-2.5 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 text-xs font-extrabold transition">Escrow xavfsiz bitim uchun kiring</a>
                @endauth
            @endif
        </div>

        @php $sellerTrust = (int)($seller->trust_score ?? 95); @endphp
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between gap-2 mb-4">
                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">FOYDALANUVCHI · AGRO-PASPORT</div>
                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold {{ $sellerTrust >= 80 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">Ishonch balli: {{ $sellerTrust }}/100</span>
            </div>
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
                <strong>Agro-Pasport ({{ $sellerTrust }}/100):</strong> Tasdiqlangan hamkor — har bir muvaffaqiyatli Escrow bitimi reytingni oshiradi, shart buzilishi avtomatik jarima beradi.
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
        <div class="max-w-3xl mx-auto grid grid-cols-2 sm:grid-cols-4 gap-2">
            @if($telClean)
                <a href="tel:{{ $telClean }}" class="py-2.5 rounded-xl border-2 border-slate-900 text-slate-900 font-extrabold text-xs sm:text-sm text-center hover:bg-slate-900 hover:text-white transition">Qo‘ng‘iroq</a>
            @else
                <button type="button" onclick="const b=document.getElementById('msgBox');b.classList.remove('hidden');b.scrollIntoView({behavior:'smooth'});" class="py-2.5 rounded-xl border-2 border-slate-900 text-slate-900 font-extrabold text-xs sm:text-sm text-center hover:bg-slate-900 hover:text-white transition">Bog‘lanish</button>
            @endif
            <button type="button" onclick="const b=document.getElementById('msgBox');b.classList.remove('hidden');b.scrollIntoView({behavior:'smooth'});" class="py-2.5 rounded-xl bg-emerald-700 text-white font-extrabold text-xs sm:text-sm text-center hover:bg-emerald-800 transition">Saytdan yozish</button>
            <a href="https://wa.me/{{ $waDigits }}?text={{ $waText }}" target="_blank" rel="noopener" class="py-2.5 rounded-xl bg-[#25D366] text-white font-extrabold text-xs sm:text-sm text-center hover:bg-[#1ebe5d] transition">WhatsApp</a>
            <a href="https://telegram.me/{{ $handleClean ?: 'zaminjob_uz' }}" target="_blank" rel="noopener" class="py-2.5 rounded-xl bg-sky-600 text-white font-extrabold text-xs sm:text-sm text-center hover:bg-sky-700 transition">Telegram</a>
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
        function calcEscrow() {
            const qEl = document.getElementById('escrowQty'), out = document.getElementById('escrowPreview');
            if (!qEl || !out) return;
            const qty = Math.max(1, Number(qEl.value) || 1);
            const unitPrice = {{ (int)$item->price }};
            const perKg = {{ $isPerKg ? 'true' : 'false' }};
            const total = perKg ? qty * 1000 * unitPrice : qty * unitPrice;
            const dep = Math.max(100000, Math.round(total * 0.03));
            out.textContent = total.toLocaleString('ru-RU') + ' so‘m (3%: ' + dep.toLocaleString('ru-RU') + ')';
        }
        calcEscrow();
    </script>
</body>
</html>
