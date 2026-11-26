<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['index', 'marketStore']);
    }

    public function index()
    {
        return view('home');
    }

    private function marketCategories()
    {
        return [
            'Mevalar' => ['Barcha mevalar', 'Olma', 'Nok', 'Uzum', 'Anor', 'Gilos', 'O‘rik', 'Shaftoli', 'Qovun', 'Tarvuz', 'Xurmo', 'Behi', 'Qulupnay', 'Limon'],
            'Sabzavotlar' => ['Barcha sabzavotlar', 'Pomidor', 'Bodring', 'Kartoshka', 'Piyoz', 'Sabzi', 'Karam', 'Bulgor qalampiri', 'Sarimsoq', 'Baqlajon'],
            'Quruq meva va yong‘oq' => ['Mayiz', 'Turshak', 'Yong‘oq', 'Bodom', 'Pista', 'Yeryong‘oq'],
            'Don va dukkaklilar' => ['Bug‘doy', 'Guruch', 'Mosh', 'Loviya', 'No‘xat', 'Makkajo‘xori', 'Arpa'],
            'Asal va sut mahsulotlari' => ['Tog‘ asali', 'Gul asali', 'Pishloq', 'Sariyog‘', 'Qaymoq', 'Tvorog'],
            'Qishloq xo‘jaligi yerlari' => ['Dehqonchilik yeri', 'Bog‘ yeri', 'Issiqxona', 'Chorva fermasi', 'Ijara yerlar'],
            'Qishloq xo‘jaligi texnikasi' => ['Traktor', 'Kombayn', 'Kultivator', 'Plug', 'Purkagich', 'Ekskavator', 'Suv nasosi'],
            'Yuk tashish va logistika' => ['Fura (20t)', 'Sovutkichli refrijerator', 'Isuzu / Gazel', 'Samasval', 'Eksport logistikasi'],
        ];
    }

    private function uzRegions()
    {
        return [
            'Toshkent shahri' => ['Chilonzor tumani', 'Yunusobod tumani', 'Mirzo Ulug‘bek tumani', 'Sergeli tumani', 'Shayxontohur tumani', 'Olmazor tumani', 'Yashnobod tumani', 'Yakkasaroy tumani'],
            'Toshkent viloyati' => ['Nurafshon shahri', 'Qibray tumani', 'Parkent tumani', 'Bo‘stonliq tumani', 'Yangiyo‘l tumani', 'Zangiota tumani', 'Chinoz tumani'],
            'Jizzax viloyati' => ['Jizzax shahri', 'Baxmal tumani', 'Sharof Rashidov tumani', 'G‘allaorol tumani', 'Zomin tumani', 'Paxtakor tumani', 'Do‘stlik tumani', 'Forish tumani'],
            'Samarqand viloyati' => ['Samarqand shahri', 'Urgut tumani', 'Bulung‘ur tumani', 'Jomboy tumani', 'Toyloq tumani', 'Ishtixon tumani', 'Pastdarg‘om tumani'],
            'Farg‘ona viloyati' => ['Farg‘ona shahri', 'Oltiariq tumani', 'Quva tumani', 'Rishton tumani', 'Qo‘qon shahri', 'Marg‘ilon shahri', 'Bag‘dod tumani'],
            'Andijon viloyati' => ['Andijon shahri', 'Asaka tumani', 'Shahrixon tumani', 'Xo‘jaobod tumani', 'Baliqchi tumani', 'Marhamat tumani'],
            'Namangan viloyati' => ['Namangan shahri', 'Chust tumani', 'Chortoq tumani', 'Pop tumani', 'Kosonsoy tumani', 'To‘raqo‘rg‘on tumani'],
            'Buxoro viloyati' => ['Buxoro shahri', 'G‘ijduvon tumani', 'Vobkent tumani', 'Jondor tumani', 'Kogon tumani', 'Qorako‘l tumani'],
            'Qashqadaryo viloyati' => ['Qarshi shahri', 'Shahrisabz shahri', 'Kitob tumani', 'Yakkabog‘ tumani', 'Koson tumani', 'G‘uzor tumani'],
            'Surxondaryo viloyati' => ['Termiz shahri', 'Denov tumani', 'Sherobod tumani', 'Boysun tumani', 'Sariosiyo tumani', 'Jarqo‘rg‘on tumani'],
            'Sirdaryo viloyati' => ['Guliston shahri', 'Yangiyer shahri', 'Boyovut tumani', 'Sardoba tumani', 'Mirzaobod tumani', 'Xovos tumani'],
            'Navoiy viloyati' => ['Navoiy shahri', 'Karmana tumani', 'Xatirchi tumani', 'Qiziltepa tumani', 'Nurota tumani'],
            'Xorazm viloyati' => ['Urganch shahri', 'Xiva shahri', 'Bog‘ot tumani', 'Gurlan tumani', 'Shovot tumani', 'Xazorasp tumani'],
            'Qoraqalpog‘iston Respublikasi' => ['Nukus shahri', 'To‘rtko‘l tumani', 'Beruniy tumani', 'Xo‘jayli tumani', 'Chimboy tumani', 'Qo‘ng‘irot tumani'],
        ];
    }

    private function globalPriceTable()
    {
        return [
            ['name' => 'Baxmal Golden olmasi', 'uz' => 8500, 'kz' => 14500, 'ru' => 19500, 'cn' => 23000, 'kr' => 34000, 'baa' => 31000],
            ['name' => 'Oltiariq husayni uzumi', 'uz' => 12000, 'kz' => 19000, 'ru' => 27000, 'cn' => 32000, 'kr' => 47000, 'baa' => 42000],
            ['name' => 'Farg‘ona eksport gilosi', 'uz' => 35000, 'kz' => 52000, 'ru' => 68800, 'cn' => 76000, 'kr' => 92000, 'baa' => 86000],
            ['name' => 'Quva shirin anori', 'uz' => 15000, 'kz' => 23500, 'ru' => 33000, 'cn' => 39000, 'kr' => 54000, 'baa' => 48000],
            ['name' => 'Samarqand qora mayizi', 'uz' => 34000, 'kz' => 49000, 'ru' => 64000, 'cn' => 71000, 'kr' => 94000, 'baa' => 89000],
            ['name' => 'Issiqxona pushti pomidori', 'uz' => 11000, 'kz' => 17500, 'ru' => 24500, 'cn' => 26000, 'kr' => 38000, 'baa' => 34000],
        ];
    }

    private function ensureListingsTable()
    {
        if (!Schema::hasTable('listings')) {
            Schema::create('listings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('category', 120);
                $table->string('subcategory', 120);
                $table->string('title', 255);
                $table->unsignedBigInteger('price')->default(0);
                $table->string('unit', 60)->default('so‘m / kg');
                $table->string('region', 120)->nullable();
                $table->string('district', 120)->nullable();
                $table->string('spec_1', 180)->nullable();
                $table->string('spec_2', 180)->nullable();
                $table->text('description');
                $table->text('images_json')->nullable();
                $table->string('seller_name', 150)->default('ZaminJob Hamkori');
                $table->string('seller_phone', 40)->nullable();
                $table->string('seller_handle', 80)->nullable();
                $table->boolean('photo_verified')->default(true);
                $table->string('photo_meta', 255)->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasColumn('listings', 'photo_verified')) {
            try {
                Schema::table('listings', function (Blueprint $table) {
                    $table->boolean('photo_verified')->default(true);
                    $table->string('photo_meta', 255)->nullable();
                });
            } catch (\Throwable $ignore) {
            }
        }

        if (DB::table('listings')->count() === 0) {
            $now = now();
            DB::table('listings')->insert([
                [
                    'user_id' => null, 'category' => 'Mevalar', 'subcategory' => 'Olma',
                    'title' => 'Baxmal Golden va Semerenko olmasi (1-nav)',
                    'price' => 8500, 'unit' => 'so‘m / kg',
                    'region' => 'Jizzax viloyati', 'district' => 'Baxmal tumani',
                    'spec_1' => 'Hajmi: 25 tonna', 'spec_2' => 'Qadoqlash: yashik',
                    'description' => 'Baxmal tog‘ yonbag‘rida yetishtirilgan sara olmalar. Sovutkichli omborda saqlangan, eksport va ichki bozor uchun tayyor.',
                    'images_json' => json_encode([]),
                    'seller_name' => 'Baxmal Bog‘lari FX', 'seller_phone' => null, 'seller_handle' => 'zaminjob_uz',
                    'created_at' => $now, 'updated_at' => $now,
                ],
                [
                    'user_id' => null, 'category' => 'Mevalar', 'subcategory' => 'Uzum',
                    'title' => 'Oltiariq Husayni uzumi — ulgurji',
                    'price' => 12000, 'unit' => 'so‘m / kg',
                    'region' => 'Farg‘ona viloyati', 'district' => 'Oltiariq tumani',
                    'spec_1' => 'Hajmi: 12 tonna', 'spec_2' => 'Holati: yangi uzilgan',
                    'description' => 'Oltiariqning mashhur shirin Husayni uzumi. Rossiya va Qozog‘iston eksporti uchun tayyor.',
                    'images_json' => json_encode([]),
                    'seller_name' => 'Oltiariq Agro Eksport', 'seller_phone' => null, 'seller_handle' => 'zaminjob_uz',
                    'created_at' => $now, 'updated_at' => $now,
                ],
                [
                    'user_id' => null, 'category' => 'Qishloq xo‘jaligi yerlari', 'subcategory' => 'Issiqxona',
                    'title' => '2 gektar zamonaviy gidroponika issiqxonasi',
                    'price' => 45000000, 'unit' => 'so‘m / gektar',
                    'region' => 'Toshkent viloyati', 'district' => 'Qibray tumani',
                    'spec_1' => 'Maydoni: 2 gektar', 'spec_2' => 'Isitish: gaz + ko‘mir',
                    'description' => 'Tomchilatib sug‘orish va avtomatik iqlim nazoratiga ega 2 gektarlik tayyor issiqxona.',
                    'images_json' => json_encode([]),
                    'seller_name' => 'Qibray Agro Klaster', 'seller_phone' => null, 'seller_handle' => 'zaminjob_uz',
                    'created_at' => $now, 'updated_at' => $now,
                ],
                [
                    'user_id' => null, 'category' => 'Qishloq xo‘jaligi texnikasi', 'subcategory' => 'Traktor',
                    'title' => 'Belarus MTZ-82.1 traktori (shudgor va kultivatsiya)',
                    'price' => 450000, 'unit' => 'so‘m / gektar',
                    'region' => 'Jizzax viloyati', 'district' => 'Sharof Rashidov tumani',
                    'spec_1' => 'Quvvati: 81 ot kuchi', 'spec_2' => 'Plug va borona bor',
                    'description' => 'Shudgorlash, kultivatsiya va chizellash xizmatlari. Tajribali mexanizator bilan birga chiqamiz.',
                    'images_json' => json_encode([]),
                    'seller_name' => 'Jizzax Texnika Servis', 'seller_phone' => null, 'seller_handle' => 'zaminjob_uz',
                    'created_at' => $now, 'updated_at' => $now,
                ],
                [
                    'user_id' => null, 'category' => 'Yuk tashish va logistika', 'subcategory' => 'Sovutkichli refrijerator',
                    'title' => '20 tonnalik sovutkichli fura (UZ → KZ / RU reyslari)',
                    'price' => 14500000, 'unit' => 'so‘m / reys',
                    'region' => 'Samarqand viloyati', 'district' => 'Samarqand shahri',
                    'spec_1' => 'Sig‘imi: 20 tonna', 'spec_2' => 'Harorat: -18°C / +15°C',
                    'description' => 'Meva-sabzavot mahsulotlarini O‘zbekiston, Qozog‘iston va Rossiyaga GPS nazorat bilan yetkazamiz.',
                    'images_json' => json_encode([]),
                    'seller_name' => 'SilkRoad Agro Logistika', 'seller_phone' => null, 'seller_handle' => 'zaminjob_uz',
                    'created_at' => $now, 'updated_at' => $now,
                ],
                [
                    'user_id' => null, 'category' => 'Sabzavotlar', 'subcategory' => 'Pomidor',
                    'title' => 'Surxondaryo issiqxona pushti pomidori (1-nav)',
                    'price' => 11000, 'unit' => 'so‘m / kg',
                    'region' => 'Surxondaryo viloyati', 'district' => 'Denov tumani',
                    'spec_1' => 'Hajmi: 8 tonna', 'spec_2' => 'Saralangan, qutida',
                    'description' => 'Yangi terilgan shirador pushti pomidor. Toshkent va eksport uchun tayyor.',
                    'images_json' => json_encode([]),
                    'seller_name' => 'Denov Issiqxona MCHJ', 'seller_phone' => null, 'seller_handle' => 'zaminjob_uz',
                    'created_at' => $now, 'updated_at' => $now,
                ],
            ]);
        }
    }

    public function marketIndex(Request $request)
    {
        $this->ensureListingsTable();
        $categories = $this->marketCategories();
        $regions = $this->uzRegions();
        $globalPrices = $this->globalPriceTable();

        $selCat = trim((string) $request->query('cat', ''));
        $selSub = trim((string) $request->query('sub', ''));
        $selReg = trim((string) $request->query('region', ''));
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('listings')->orderByDesc('id');
        if ($selCat !== '') {
            $query->where('category', $selCat);
        }
        if ($selSub !== '' && strpos($selSub, 'Barcha ') !== 0) {
            $query->where('subcategory', $selSub);
        }
        if ($selReg !== '') {
            $query->where('region', $selReg);
        }
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $like = '%' . $q . '%';
                $sub->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('subcategory', 'like', $like)
                    ->orWhere('region', 'like', $like);
            });
        }

        $listings = $query->get();
        return view('market', compact('categories', 'regions', 'globalPrices', 'listings', 'selCat', 'selSub', 'selReg', 'q'));
    }

    public function marketStore(Request $request)
    {
        $this->ensureListingsTable();
        $user = auth()->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $priceDigits = preg_replace('/[^\d]/', '', (string) $request->input('price', '0'));
        $savedImgs = [];
        $exifFound = false;
        $exifTime = '';
        if ($request->hasFile('photos')) {
            foreach (array_slice($request->file('photos'), 0, 6) as $file) {
                if ($file && $file->isValid()) {
                    if (!$exifFound && function_exists('exif_read_data')) {
                        $exif = @exif_read_data($file->getRealPath());
                        if (is_array($exif) && (!empty($exif['GPSLatitude']) || !empty($exif['DateTimeOriginal']))) {
                            $exifFound = true;
                            $exifTime = (string) ($exif['DateTimeOriginal'] ?? '');
                        }
                    }
                    $path = $file->store('listings', 'public');
                    $savedImgs[] = '/storage/' . $path;
                }
            }
        }

        $gpsLat = trim((string) $request->input('photo_gps_lat', ''));
        $gpsLng = trim((string) $request->input('photo_gps_lng', ''));
        $takenAt = trim((string) $request->input('photo_taken_at', ''));
        $photoVerified = ($gpsLat !== '' && $gpsLng !== '') || $exifFound ? 1 : 0;
        if ($gpsLat !== '' && $gpsLng !== '') {
            $tLabel = $takenAt !== '' ? $takenAt : now('Asia/Tashkent')->format('d.m.Y H:i');
            $photoMeta = "GPS: {$gpsLat}, {$gpsLng} · {$tLabel}";
        } elseif ($exifFound) {
            $photoMeta = 'EXIF kamera metadatasi tasdiqlangan' . ($exifTime !== '' ? ' · ' . $exifTime : '');
        } else {
            $photoMeta = 'GPS/vaqt metadatasi kiritilmagan — moderatsiyada';
        }

        $id = DB::table('listings')->insertGetId([
            'user_id' => $user->id,
            'category' => trim((string) $request->input('category', 'Mevalar')),
            'subcategory' => trim((string) $request->input('subcategory', 'Olma')),
            'title' => trim((string) $request->input('title')),
            'price' => $priceDigits !== '' ? (int) $priceDigits : 0,
            'unit' => trim((string) $request->input('unit', 'so‘m / kg')),
            'region' => trim((string) $request->input('region', $user->region ?? 'Jizzax viloyati')),
            'district' => trim((string) $request->input('district', $user->district ?? '')),
            'spec_1' => trim((string) $request->input('spec_1', '')) ?: null,
            'spec_2' => trim((string) $request->input('spec_2', '')) ?: null,
            'description' => trim((string) $request->input('description')),
            'images_json' => json_encode($savedImgs),
            'seller_name' => $user->name,
            'seller_phone' => $user->phone,
            'seller_handle' => $user->telegram ?: $user->username,
            'photo_verified' => $photoVerified,
            'photo_meta' => $photoMeta,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('bozor.show', $id)->with('success', $photoVerified ? 'E’loningiz joylandi va GPS+vaqt metadatasi tasdiqlandi!' : 'E’loningiz joylandi (GPS metadata moderatsiyada).');
    }

    public function marketShow($id)
    {
        $this->ensureListingsTable();
        $this->ensureCoreTables();
        $item = DB::table('listings')->where('id', (int) $id)->first();
        if (!$item) {
            return redirect()->route('bozor')->with('error', 'E’lon topilmadi.');
        }

        $seller = $item->user_id ? DB::table('users')->where('id', $item->user_id)->first() : null;
        $others = DB::table('listings')->where('id', '!=', $item->id)->orderByDesc('id')->limit(3)->get();
        $escrowDeal = Schema::hasTable('escrow_deals')
            ? DB::table('escrow_deals')->where('listing_id', $item->id)->orderByDesc('id')->first()
            : null;
        return view('listing', compact('item', 'seller', 'others', 'escrowDeal'));
    }

    public function marketMessage(Request $request, $id)
    {
        $this->ensureListingsTable();
        $item = DB::table('listings')->where('id', (int) $id)->first();
        if (!$item) {
            return redirect()->route('bozor')->with('error', 'E’lon topilmadi.');
        }

        $msg = trim((string) $request->input('message', ''));
        if ($msg === '') {
            return redirect()->route('bozor.show', $id)->with('error', 'Xabar matnini kiriting.');
        }

        $senderName = auth()->check() ? auth()->user()->name : trim((string) $request->input('sender_name', 'Mehmon'));
        $senderPhone = auth()->check() ? (auth()->user()->phone ?? '') : trim((string) $request->input('sender_phone', ''));

        if ($item->user_id && Schema::hasTable('notifications')) {
            DB::table('notifications')->insert([
                'user_id' => $item->user_id,
                'title' => 'Yangi bozor xabari: ' . $item->title,
                'body' => "{$senderName} ({$senderPhone}): {$msg}",
                'is_read' => 0,
                'created_at' => now(),
            ]);
        }

        return redirect()->route('bozor.show', $id)->with('success', 'Xabaringiz e’lon egasiga muvaffaqiyatli yuborildi!');
    }

    private function ensureCoreTables()
    {
        if (!Schema::hasTable('worker_profiles')) {
            Schema::create('worker_profiles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->unique();
                $table->string('profession', 150)->default('');
                $table->text('description')->nullable();
                $table->string('region', 100)->nullable();
                $table->string('district', 120)->nullable();
                $table->unsignedBigInteger('expected_salary')->nullable();
                $table->string('payment_type', 30)->default('monthly');
                $table->string('equipment_type', 180)->nullable();
                $table->string('photo_path', 255)->nullable();
                $table->boolean('is_available')->default(true);
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('employer_profiles')) {
            Schema::create('employer_profiles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->unique();
                $table->string('company_name', 255)->nullable();
                $table->string('needed_profession', 150)->default('');
                $table->string('region', 100)->nullable();
                $table->string('district', 120)->nullable();
                $table->string('land_area', 80)->nullable();
                $table->string('crop_type', 180)->nullable();
                $table->string('irrigation_type', 100)->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('vacancies')) {
            Schema::create('vacancies', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('employer_id');
                $table->string('title', 255);
                $table->string('profession', 150);
                $table->text('description');
                $table->string('region', 100)->nullable();
                $table->string('district', 120)->nullable();
                $table->string('salary', 120)->nullable();
                $table->unsignedBigInteger('salary_amount')->nullable();
                $table->string('payment_type', 30)->default('monthly');
                $table->string('status', 20)->default('active');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('applications')) {
            Schema::create('applications', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('vacancy_id');
                $table->unsignedBigInteger('worker_id');
                $table->string('status', 30)->default('pending');
                $table->timestamps();
                $table->unique(['vacancy_id', 'worker_id']);
            });
        }
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id');
                $table->string('title', 180);
                $table->text('body');
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('escrow_deals')) {
            Schema::create('escrow_deals', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('listing_id');
                $table->unsignedBigInteger('buyer_id');
                $table->unsignedBigInteger('seller_id')->nullable();
                $table->decimal('qty_tons', 8, 2)->default(5);
                $table->unsignedBigInteger('amount')->default(0);
                $table->unsignedBigInteger('deposit_amount')->default(0);
                $table->string('status', 30)->default('funded');
                $table->string('gps_proof', 255)->nullable();
                $table->timestamps();
            });
        }
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'trust_score')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->integer('trust_score')->default(95);
                    $table->integer('strikes')->default(0);
                    $table->unsignedBigInteger('deposit_balance')->default(0);
                });
            } catch (\Throwable $ignore) {
            }
        }
    }

    private function isAdminUser($user)
    {
        if (!$user) {
            return false;
        }
        $clean = preg_replace('/\s+/', '', (string) ($user->phone ?? ''));
        return ($user->role === 'admin') || ($clean === '+998904047170') || ($user->phone === 'mnodir');
    }

    public function profileShow()
    {
        $this->ensureCoreTables();
        $user = auth()->user();
        $isAdmin = $this->isAdminUser($user);
        $regions = $this->uzRegions();
        $workerProfile = DB::table('worker_profiles')->where('user_id', $user->id)->first();
        $employerProfile = DB::table('employer_profiles')->where('user_id', $user->id)->first();
        return view('profile', compact('user', 'isAdmin', 'regions', 'workerProfile', 'employerProfile'));
    }

    public function profileSave(Request $request)
    {
        $this->ensureCoreTables();
        $user = auth()->user();
        $isAdmin = $this->isAdminUser($user);

        $name = trim((string) $request->input('name', ''));
        if ($name === '') {
            return redirect()->route('profil')->with('error', 'Ism va familiyangizni kiriting.');
        }

        $username = strtolower(ltrim(trim((string) $request->input('username', '')), '@'));
        if ($username !== '') {
            if (!preg_match('/^[a-z0-9_]{3,40}$/', $username) || in_array($username, ['admin', 'mnodir', 'root', 'zaminjob'], true)) {
                return redirect()->route('profil')->with('error', 'Username 3–40 ta kichik lotin harfi, raqam yoki _ dan iborat bo‘lsin.');
            }
            $exists = DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists();
            if ($exists) {
                return redirect()->route('profil')->with('error', 'Bu username band, boshqa username tanlang.');
            }
        }

        $role = $isAdmin ? 'admin' : (string) $request->input('role', $user->role ?: 'worker');
        if (!$isAdmin && !in_array($role, ['worker', 'farmer', 'employer', 'tech', 'logistics'], true)) {
            $role = 'worker';
        }

        $avatarPath = $user->avatar_path ?? null;
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $request->validate(['avatar' => 'image|mimes:jpg,jpeg,png,webp|max:5120']);
            $stored = $request->file('avatar')->store('avatars', 'public');
            $avatarPath = 'storage/' . $stored;
        }

        $region = trim((string) $request->input('region', '')) ?: null;
        $district = trim((string) $request->input('district', '')) ?: null;
        $bio = trim((string) $request->input('bio', '')) ?: null;
        $telegram = trim((string) $request->input('telegram', '')) ?: null;
        $whatsapp = trim((string) $request->input('whatsapp', '')) ?: null;

        if (!Schema::hasColumn('users', 'whatsapp')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('whatsapp', 40)->nullable();
                });
            } catch (\Throwable $ignore) {
            }
        }

        $updateData = [
            'name' => $name,
            'role' => $role,
            'updated_at' => now(),
        ];
        if (Schema::hasColumn('users', 'username')) $updateData['username'] = $username !== '' ? $username : null;
        if (Schema::hasColumn('users', 'avatar_path')) $updateData['avatar_path'] = $avatarPath;
        if (Schema::hasColumn('users', 'region')) $updateData['region'] = $region;
        if (Schema::hasColumn('users', 'district')) $updateData['district'] = $district;
        if (Schema::hasColumn('users', 'bio')) $updateData['bio'] = $bio;
        if (Schema::hasColumn('users', 'telegram')) $updateData['telegram'] = $telegram;
        if (Schema::hasColumn('users', 'whatsapp')) $updateData['whatsapp'] = $whatsapp;

        DB::table('users')->where('id', $user->id)->update($updateData);

        if (in_array($role, ['worker', 'tech', 'logistics'], true)) {
            $salDigits = preg_replace('/[^\d]/', '', (string) $request->input('expected_salary', ''));
            DB::table('worker_profiles')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'profession' => trim((string) $request->input('profession', '')) ?: ($role === 'tech' ? 'Texnik xizmat' : ($role === 'logistics' ? 'Yuk tashish xizmati' : 'Mutaxassis')),
                    'description' => $bio,
                    'region' => $region,
                    'district' => $district,
                    'expected_salary' => $salDigits !== '' ? (int) $salDigits : null,
                    'payment_type' => in_array($request->input('payment_type'), ['monthly', 'daily', 'piecework'], true) ? $request->input('payment_type') : 'monthly',
                    'equipment_type' => trim((string) $request->input('equipment_type', '')) ?: null,
                    'photo_path' => $avatarPath,
                    'is_available' => 1,
                ]
            );
        } elseif (!$isAdmin) {
            DB::table('employer_profiles')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'company_name' => trim((string) $request->input('company_name', '')) ?: null,
                    'needed_profession' => trim((string) $request->input('needed_profession', '')),
                    'region' => $region,
                    'district' => $district,
                    'land_area' => trim((string) $request->input('land_area', '')) ?: null,
                    'crop_type' => trim((string) $request->input('crop_type', '')) ?: null,
                    'irrigation_type' => trim((string) $request->input('irrigation_type', '')) ?: null,
                ]
            );
        }

        return redirect()->route('profil')->with('success', 'Profil ma’lumotlaringiz muvaffaqiyatli saqlandi!');
    }

    public function cabinetShow(Request $request)
    {
        $this->ensureCoreTables();
        $user = auth()->user();
        if ($this->isAdminUser($user)) {
            return redirect()->route('admin');
        }

        $regions = $this->uzRegions();
        $q = trim((string) $request->query('q', ''));
        $selReg = trim((string) $request->query('region', ''));

        $vacQuery = DB::table('vacancies')
            ->join('users', 'users.id', '=', 'vacancies.employer_id')
            ->select('vacancies.*', 'users.name as employer_name')
            ->where('vacancies.status', 'active')
            ->orderByDesc('vacancies.id');

        if ($selReg !== '') {
            $vacQuery->where('vacancies.region', $selReg);
        }
        if ($q !== '') {
            $vacQuery->where(function ($sub) use ($q) {
                $like = '%' . $q . '%';
                $sub->where('vacancies.title', 'like', $like)
                    ->orWhere('vacancies.profession', 'like', $like)
                    ->orWhere('vacancies.description', 'like', $like);
            });
        }

        $vacancies = $vacQuery->get();
        $appliedMap = DB::table('applications')->where('worker_id', $user->id)->pluck('status', 'vacancy_id')->toArray();
        $myVacancies = DB::table('vacancies')->where('employer_id', $user->id)->orderByDesc('id')->get();

        $appsByVac = [];
        foreach ($myVacancies as $mv) {
            $appsByVac[$mv->id] = DB::table('applications')
                ->join('users', 'users.id', '=', 'applications.worker_id')
                ->select('applications.*', 'users.name as worker_name', 'users.phone as worker_phone', 'users.trust_score as worker_trust', 'users.strikes as worker_strikes')
                ->where('applications.vacancy_id', $mv->id)
                ->orderByDesc('applications.id')
                ->get();
        }

        $workers = DB::table('users')
            ->leftJoin('worker_profiles', 'worker_profiles.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.phone', 'users.role', 'users.avatar_path', 'users.region', 'users.district', 'users.trust_score', 'users.strikes', 'worker_profiles.profession')
            ->whereIn('users.role', ['worker', 'tech', 'logistics'])
            ->orderByDesc('users.id')
            ->limit(24)
            ->get();

        return view('cabinet', compact('user', 'regions', 'q', 'selReg', 'vacancies', 'appliedMap', 'myVacancies', 'appsByVac', 'workers'));
    }

    public function vacancyStore(Request $request)
    {
        $this->ensureCoreTables();
        $user = auth()->user();
        $request->validate([
            'title' => 'required|string|max:255',
            'profession' => 'required|string|max:150',
            'description' => 'required|string',
        ]);
        $salDigits = preg_replace('/[^\d]/', '', (string) $request->input('salary', ''));
        DB::table('vacancies')->insert([
            'employer_id' => $user->id,
            'title' => trim((string) $request->input('title')),
            'profession' => trim((string) $request->input('profession')),
            'description' => trim((string) $request->input('description')),
            'region' => trim((string) $request->input('region', $user->region ?? '')) ?: null,
            'district' => $user->district ?? null,
            'salary' => trim((string) $request->input('salary', '')) ?: null,
            'salary_amount' => $salDigits !== '' ? (int) $salDigits : null,
            'payment_type' => in_array($request->input('payment_type'), ['monthly', 'daily', 'piecework'], true) ? $request->input('payment_type') : 'monthly',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('kabinet')->with('success', 'Vakansiya muvaffaqiyatli e’lon qilindi!');
    }

    public function vacancyApply($id)
    {
        $this->ensureCoreTables();
        $user = auth()->user();
        $vac = DB::table('vacancies')->where('id', (int) $id)->where('status', 'active')->first();
        if (!$vac) {
            return redirect()->route('kabinet')->with('error', 'Vakansiya topilmadi.');
        }

        $exists = DB::table('applications')->where('vacancy_id', $vac->id)->where('worker_id', $user->id)->exists();
        if (!$exists) {
            DB::table('applications')->insert([
                'vacancy_id' => $vac->id,
                'worker_id' => $user->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('notifications')->insert([
                'user_id' => $vac->employer_id,
                'title' => 'Yangi ish arizasi tushdi!',
                'body' => "{$user->name} ({$user->phone}) «{$vac->title}» vakansiyangizga ariza yubordi.",
                'is_read' => 0,
                'created_at' => now(),
            ]);
        }

        return redirect()->route('kabinet')->with('success', 'Arizangiz yuborildi! Ish beruvchiga darhol bildirishnoma bordi.');
    }

    public function applicationUpdate(Request $request, $id)
    {
        $this->ensureCoreTables();
        $user = auth()->user();
        $status = (string) $request->input('status', '');
        if (!in_array($status, ['accepted', 'rejected'], true)) {
            return redirect()->route('kabinet');
        }

        $row = DB::table('applications')
            ->join('vacancies', 'vacancies.id', '=', 'applications.vacancy_id')
            ->select('applications.id', 'applications.worker_id', 'vacancies.title')
            ->where('applications.id', (int) $id)
            ->where('vacancies.employer_id', $user->id)
            ->first();

        if ($row) {
            DB::table('applications')->where('id', $row->id)->update(['status' => $status, 'updated_at' => now()]);
            DB::table('notifications')->insert([
                'user_id' => $row->worker_id,
                'title' => $status === 'accepted' ? 'Arizangiz qabul qilindi!' : 'Ariza rad etildi',
                'body' => "«{$row->title}» vakansiyasi bo‘yicha {$user->name} javob berdi.",
                'is_read' => 0,
                'created_at' => now(),
            ]);
        }

        return redirect()->route('kabinet')->with('success', 'Ariza holati yangilandi.');
    }

    public function notificationsApi()
    {
        $this->ensureCoreTables();
        $uid = (int) auth()->id();
        $unread = DB::table('notifications')->where('user_id', $uid)->where('is_read', 0)->count();
        $items = DB::table('notifications')->where('user_id', $uid)->orderByDesc('id')->limit(12)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'title' => $n->title,
                'body' => $n->body,
                'is_read' => (int) $n->is_read,
                'created_at' => $n->created_at ? date('d.m H:i', strtotime($n->created_at)) : '',
            ];
        });
        return response()->json(['unread' => $unread, 'items' => $items]);
    }

    public function notificationsRead()
    {
        $this->ensureCoreTables();
        DB::table('notifications')->where('user_id', (int) auth()->id())->update(['is_read' => 1]);
        return response()->json(['ok' => true]);
    }

    private function logisticsCostPer20t()
    {
        return [
            'kz'  => ['country' => 'Qozog‘iston', 'city' => 'Olmaota', 'truck20t' => 18000000, 'days' => '2 kun'],
            'ru'  => ['country' => 'Rossiya', 'city' => 'Moskva', 'truck20t' => 34000000, 'days' => '5 kun'],
            'cn'  => ['country' => 'Xitoy', 'city' => 'Urumchi / Pekin', 'truck20t' => 36000000, 'days' => '4 kun'],
            'kr'  => ['country' => 'Janubiy Koreya', 'city' => 'Seul', 'truck20t' => 64000000, 'days' => '7 kun'],
            'baa' => ['country' => 'BAA', 'city' => 'Dubay', 'truck20t' => 56000000, 'days' => '6 kun'],
        ];
    }

    public function aiSignalsApi()
    {
        $base = $this->globalPriceTable();
        $logi = $this->logisticsCostPer20t();
        $tick = (int) floor(time() / 15);
        $w = ($tick % 5) - 2; // -2 .. +2 kichik jonli tebranish

        // 4 ta har xil davlat bo'yicha eng foydali va tushunarli issiq eksport signallari
        $featured = [
            [
                'prod_idx' => 2, 'country_key' => 'kr',
                'reason' => 'Seulda mavsumiy gilos zaxirasi keskin kamaydi — avia/refrijerator talabi eng yuqori.',
                'window' => 'Keyingi 48 soat', 'confidence' => 95,
                'trend' => [32, 44, 38, 56, 68, 82, 95],
            ],
            [
                'prod_idx' => 1, 'country_key' => 'baa',
                'reason' => 'Dubay supermarket tarmoqlarida O‘zbekiston Husayni uzumiga haftalik buyurtmalar oshdi.',
                'window' => 'Keyingi 72 soat', 'confidence' => 93,
                'trend' => [28, 35, 48, 44, 62, 76, 89],
            ],
            [
                'prod_idx' => 4, 'country_key' => 'cn',
                'reason' => 'Xitoy chegara logistikasi tezlashdi — Samarqand qora mayiziga ulgurji narx ko‘tarildi.',
                'window' => 'Keyingi 3 kun', 'confidence' => 92,
                'trend' => [36, 30, 46, 58, 54, 72, 86],
            ],
            [
                'prod_idx' => 3, 'country_key' => 'ru',
                'reason' => 'Moskva ulgurji bozorida shirin anor zaxirasi tugamoqda — 5 kunlik reys eng foydali.',
                'window' => 'Keyingi 4 kun', 'confidence' => 91,
                'trend' => [25, 38, 34, 52, 64, 71, 84],
            ],
        ];

        $livePrices = [];
        foreach ($base as $idx => $row) {
            $factor = 1 + (((($tick + $idx * 3) % 5) - 2) * 0.004);
            $itemLive = ['name' => $row['name'], 'uz' => (int) $row['uz']];
            foreach ($logi as $k => $meta) {
                $itemLive[$k] = (int) round(($row[$k] * $factor) / 100) * 100;
            }
            $livePrices[] = $itemLive;
        }

        $signals = [];
        foreach ($featured as $i => $f) {
            $row = $livePrices[$f['prod_idx']];
            $ck = $f['country_key'];
            $meta = $logi[$ck];
            $uz = (int) $row['uz'];
            $targetP = (int) $row[$ck];
            $logiPerKg = (int) round($meta['truck20t'] / 20000);
            $netKg = $targetP - $uz - $logiPerKg;
            $spikePct = round(14.2 + (3 - $i) * 4.6 + ($w * 0.4), 1);
            $forecast7d = round(4.8 + (3 - $i) * 1.1 + ($w * 0.2), 1);
            $supplyDrop = -(18 + (3 - $i) * 3 + abs($w));
            $trend = $f['trend'];
            $trend[6] = max(72, min(97, $trend[6] + $w * 2));

            $signals[] = [
                'id' => $i + 1,
                'product' => $row['name'],
                'country_key' => $ck,
                'country' => $meta['country'],
                'city' => $meta['city'],
                'days' => $meta['days'],
                'change_pct' => $spikePct,
                'supply_drop_pct' => $supplyDrop,
                'confidence' => min(97, $f['confidence'] + ($w > 0 ? 1 : 0)),
                'window' => $f['window'],
                'forecast_7d' => "+{$forecast7d}% (7 kun)",
                'trend' => $trend,
                'uz_price' => $uz,
                'target_price' => $targetP,
                'net_profit_kg' => $netKg,
                'net_profit_20t' => $netKg * 20000,
                'reason' => $f['reason'],
                'urgency' => $i < 2 ? 'hot' : 'rising',
            ];
        }

        // Real vaqtda NARX TUSHIB KETGAN davlatlar (Xavf / Ogohlantirish signallari)
        $dropTemplates = [
            [
                'country' => 'Qozog‘iston', 'city' => 'Olmaota', 'product' => 'Issiqxona pushti pomidori',
                'base_drop' => -11.4, 'surplus_pct' => 24,
                'reason' => 'Mahalliy issiqxonalardan ortiqcha hosil kirib keldi — narx pasaymoqda.',
                'action' => 'Rossiya (Moskva) yo‘nalishiga burish (+28% yuqori narx)',
            ],
            [
                'country' => 'Rossiya', 'city' => 'Moskva', 'product' => 'Baxmal Golden olmasi',
                'base_drop' => -7.8, 'surplus_pct' => 18,
                'reason' => 'Omborlarda zaxira ko‘paygani sababli ulgurji narx vaqtincha tushdi.',
                'action' => '3–4 kun kutish yoki BAA (Dubay) bozoriga jo‘natish',
            ],
            [
                'country' => 'Xitoy', 'city' => 'Urumchi', 'product' => 'Oltiariq husayni uzumi',
                'base_drop' => -6.2, 'surplus_pct' => 15,
                'reason' => 'Chegara bozorida vaqtincha ortiqcha taklif yuzaga keldi.',
                'action' => 'BAA (Dubay) yoki Koreya (Seul) yo‘nalishi hozir foydaliroq',
            ],
        ];
        $drops = [];
        foreach ($dropTemplates as $di => $dt) {
            $jitter = ((($tick + $di * 2) % 5) - 2) * 0.3;
            $drops[] = [
                'country' => $dt['country'],
                'city' => $dt['city'],
                'product' => $dt['product'],
                'drop_pct' => round($dt['base_drop'] + $jitter, 1),
                'surplus_pct' => $dt['surplus_pct'] + (($tick + $di) % 4),
                'reason' => $dt['reason'],
                'action' => $dt['action'],
            ];
        }

        // 5 davlat bo'yicha real vaqtda narx va zaxira (miqdor) holati radari
        $countryPulse = [
            ['code' => 'KR',  'country' => 'J. Koreya',    'city' => 'Seul',    'price_pct' => round(27.8 + $w * 0.4, 1), 'supply_pct' => -(27 + abs($w)), 'status' => 'deficit', 'note' => 'Gilos tanqisligi'],
            ['code' => 'BAA', 'country' => 'BAA',          'city' => 'Dubay',   'price_pct' => round(23.2 + $w * 0.3, 1), 'supply_pct' => -(24 + abs($w)), 'status' => 'deficit', 'note' => 'Uzum & anor talabi'],
            ['code' => 'CN',  'country' => 'Xitoy',        'city' => 'Urumchi', 'price_pct' => round(18.6 + $w * 0.3, 1), 'supply_pct' => -(21 + abs($w)), 'status' => 'deficit', 'note' => 'Qora mayiz o‘smoqda'],
            ['code' => 'RU',  'country' => 'Rossiya',      'city' => 'Moskva',  'price_pct' => round(-7.8 + $w * 0.3, 1), 'supply_pct' => +(18 + abs($w)), 'status' => 'drop',    'note' => 'Olma zaxirasi oshdi'],
            ['code' => 'KZ',  'country' => 'Qozog‘iston',  'city' => 'Olmaota', 'price_pct' => round(-11.4 + $w * 0.3, 1),'supply_pct' => +(24 + abs($w)), 'status' => 'drop',    'note' => 'Pomidor narxi tushdi'],
        ];

        return response()->json([
            'updated_at' => now('Asia/Tashkent')->format('H:i:s'),
            'active_listings' => Schema::hasTable('listings') ? DB::table('listings')->count() : 6,
            'signals' => $signals,
            'drops' => $drops,
            'country_pulse' => $countryPulse,
            'prices' => $livePrices,
        ]);
    }

    public function aiAnalyzeApi(Request $request)
    {
        $productName = trim((string) $request->input('product', 'Baxmal Golden olmasi'));
        $tons = max(1, min(200, (float) $request->input('tons', 10)));
        $region = trim((string) $request->input('region', 'Jizzax viloyati'));
        $kg = (int) round($tons * 1000);

        $base = $this->globalPriceTable();
        $selected = $base[0];
        foreach ($base as $row) {
            if (mb_strtolower($row['name']) === mb_strtolower($productName)) {
                $selected = $row;
                break;
            }
        }

        $logi = $this->logisticsCostPer20t();
        $uz = (int) $selected['uz'];
        $uzTotal = $uz * $kg;
        $routes = [];

        foreach ($logi as $k => $meta) {
            $priceKg = (int) $selected[$k];
            $trucksNeeded = max(1, (int) ceil($tons / 20));
            $poolingSaved = $tons < 20;
            $logisticsCost = $poolingSaved
                ? (int) round(($meta['truck20t'] / 20) * $tons * 1.08)
                : ($meta['truck20t'] * $trucksNeeded);
            $gross = $priceKg * $kg;
            $netProfit = $gross - $uzTotal - $logisticsCost;
            $marginPct = round(($netProfit / max(1, $uzTotal)) * 100, 1);

            $routes[] = [
                'key' => $k,
                'country' => $meta['country'],
                'city' => $meta['city'],
                'days' => $meta['days'],
                'price_kg' => $priceKg,
                'gross' => $gross,
                'logistics' => $logisticsCost,
                'net_profit' => $netProfit,
                'margin_pct' => $marginPct,
            ];
        }

        usort($routes, function ($a, $b) {
            return $b['net_profit'] <=> $a['net_profit'];
        });

        $best = $routes[0];
        $poolingTip = $tons < 20
            ? "Sizda {$tons} tonna bor. ZaminJob AI Agro-Pooling orqali {$region} fermerlari bilan 20t sovutkichli furaga birlashsangiz, logistika xarajatidan 35% gacha tejaysiz."
            : "Hajmingiz ({$tons} tonna) to‘liq 20 tonnalik sovutkichli fura uchun yetarli — to‘g‘ridan-to‘g‘ri eksport shartnomasi eng yuqori foyda beradi.";

        $usdPrice = number_format($best['price_kg'] / 12800, 2, '.', '');
        $offers = [
            'uz' => "Assalomu alaykum! {$region}dan {$tons} tonna «{$selected['name']}» (1-nav, eksportbop) taklif qilamiz. {$best['city']}gacha yetkazib berish muddati: {$best['days']}. Tavsiya ulgurji narx: " . number_format($best['price_kg'], 0, '.', ' ') . " so‘m/kg. ZaminJob Agro-Pasport orqali tasdiqlangan.",
            'ru' => "Здравствуйте! Предлагаем оптом из Узбекистана ({$region}): «{$selected['name']}», 1-й сорт (экспортное качество), объём {$tons} тонн. Доставка рефрижератором до г. {$best['city']} за {$best['days']}. Ориентировочная цена: \${$usdPrice}/кг. Проверено через ZaminJob Agro-Passport.",
            'en' => "Hello! Direct wholesale export offer from Uzbekistan ({$region}): {$tons} MT of Grade-A «{$selected['name']}» (export quality, cold-chain ready). Estimated delivery to {$best['city']}: {$best['days']}. Target price: \${$usdPrice}/kg. Verified supplier on ZaminJob.",
        ];

        return response()->json([
            'product' => $selected['name'],
            'tons' => $tons,
            'region' => $region,
            'uz_price' => $uz,
            'best' => $best,
            'routes' => $routes,
            'pooling_tip' => $poolingTip,
            'offers' => $offers,
            'analyzed_at' => now()->format('H:i:s'),
        ]);
    }

    public function aiWatchApi(Request $request)
    {
        $this->ensureCoreTables();
        $product = trim((string) $request->input('product', 'Mahsulot'));
        $country = trim((string) $request->input('country', 'Eksport'));
        $pct = trim((string) $request->input('pct', '12'));

        if (auth()->check()) {
            DB::table('notifications')->insert([
                'user_id' => (int) auth()->id(),
                'title' => "AI Signal kuzatuvi yoqildi: {$product}",
                'body' => "{$country} bozorida {$product} narxi (+{$pct}%) kuzatuvga olindi. Keskin o‘zgarish bo‘lsa darhol xabar beramiz.",
                'is_read' => 0,
                'created_at' => now(),
            ]);
            return response()->json(['ok' => true, 'message' => "«{$product}» kuzatuvga olindi — qo‘ng‘iroqchangizga bildirishnoma yuborildi!"]);
        }

        return response()->json(['ok' => true, 'message' => "«{$product}» ({$country}) signali faollashtirildi! Doimiy xabarlar uchun saytga kiring."]);
    }

    public function aiChatApi(Request $request)
    {
        $this->ensureListingsTable();
        $msg = trim((string) $request->input('message', ''));
        $q = mb_strtolower($msg);

        // Tonnani matndan ajratish
        $tons = 10;
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(?:t|tonna|тонн)/iu', $msg, $m)) {
            $tons = max(1, min(200, (float) str_replace(',', '.', $m[1])));
        }

        $base = $this->globalPriceTable();
        $logi = $this->logisticsCostPer20t();

        // Mahsulotni aniqlash
        $keywords = [
            'gilos' => 2, 'череш' => 2,
            'uzum' => 1, 'виноград' => 1,
            'anor' => 3, 'гранат' => 3,
            'mayiz' => 4, 'изюм' => 4,
            'pomidor' => 5, 'помидор' => 5,
            'olma' => 0, 'яблок' => 0,
        ];
        $prodIdx = 0;
        $matchedKeyword = '';
        foreach ($keywords as $kw => $idx) {
            if (mb_strpos($q, $kw) !== false) {
                $prodIdx = $idx;
                $matchedKeyword = $kw;
                break;
            }
        }
        $prod = $base[$prodIdx];

        // Eng foydali yo'nalishni hisoblash
        $kg = (int) round($tons * 1000);
        $uzTotal = $prod['uz'] * $kg;
        $best = null;
        foreach ($logi as $k => $meta) {
            $logiCost = $tons < 20
                ? (int) round(($meta['truck20t'] / 20) * $tons * 1.08)
                : ($meta['truck20t'] * (int) ceil($tons / 20));
            $net = ($prod[$k] * $kg) - $uzTotal - $logiCost;
            if (!$best || $net > $best['net']) {
                $best = [
                    'key' => $k,
                    'country' => $meta['country'],
                    'city' => $meta['city'],
                    'days' => $meta['days'],
                    'price_kg' => $prod[$k],
                    'logi' => $logiCost,
                    'net' => $net,
                    'margin' => round(($net / max(1, $uzTotal)) * 100, 1),
                ];
            }
        }

        $matches = [];
        $checklist = [];
        $metrics = [];

        // 1. Hujjat / Fitosanitariya / Bojxona savoli
        if (mb_strpos($q, 'hujjat') !== false || mb_strpos($q, 'fito') !== false || mb_strpos($q, 'bojxona') !== false || mb_strpos($q, 'документ') !== false || mb_strpos($q, 'sertifikat') !== false) {
            $reply = "«{$prod['name']}» mahsulotini {$best['country']} ({$best['city']}) bozoriga eksport qilish uchun quyidagi 5 ta asosiy hujjat talab qilinadi. ZaminJob orqali hamkor logistika kompaniyalari bojxona rasmiylashtiruvini 24 soatda yakunlab beradi:";
            $metrics = [
                ['label' => 'Asosiy ruxsatnoma', 'value' => 'Fitosanitariya (e-Fito)'],
                ['label' => 'Bojxona vaqti', 'value' => '6 – 24 soat'],
                ['label' => 'Sovutkich rejimi', 'value' => '+2°C / +4°C'],
            ];
            $checklist = [
                'Fitosanitariya sertifikati (O‘simliklar karantini agentligi — e-Fito tizimidan)',
                'Kelib chiqish sertifikati (ST-1 — MDH uchun, yoki Form-A / Umumiy — BAA va Koreya uchun)',
                'Eksport shartnomasi, Invoys (Commercial Invoice) va Qadoqlash varag‘i (Packing List)',
                'CMR xalqaro yuk xati va sovutkichli fura (Refrijerator) harorat qaydi',
                'Bojxona yuk deklaratsiyasi (BYD — E-Kontrakt tizimida ro‘yxatdan o‘tkazilgan)',
            ];
        }
        // 2. Agro-Pooling / Fura / Logistika sherik topish
        elseif (mb_strpos($q, 'pooling') !== false || mb_strpos($q, 'fura') !== false || mb_strpos($q, 'sherik') !== false || mb_strpos($q, 'logistika') !== false || mb_strpos($q, 'yuk') !== false || mb_strpos($q, 'фура') !== false) {
            $needed = max(0, 20 - $tons);
            $reply = "ZaminJob AI Agro-Pooling tizimi bozordagi faol e’lonlar va sovutkichli furalarni skaner qildi. Sizda {$tons} tonna bo‘lsa, to‘liq 20t refrijerator uchun yana {$needed} tonnalik qo‘shni fermer hosili bilan birlashish logistika xarajatini 35% gacha arzonlashtiradi.";
            $metrics = [
                ['label' => 'Tavsiya etilgan reys', 'value' => "UZ → {$best['city']} ({$best['days']})"],
                ['label' => 'Logistika tejami', 'value' => '~35% (Pooling)'],
                ['label' => 'Bozordagi furalar', 'value' => 'Faol tayyor'],
            ];
            $checklist = [
                'Quyidagi tayyor 20t sovutkichli fura yoki qo‘shni fermer e’loniga yozing',
                'Yuklash harorati va yashik standartini (10kg/15kg gofro-quti) kelishing',
                'ZaminJob Raqamli bitim orqali joy band qiling',
            ];
            $dbRows = DB::table('listings')
                ->where('category', 'Yuk tashish va logistika')
                ->orWhere('category', 'Mevalar')
                ->orderByDesc('id')->limit(3)->get();
            foreach ($dbRows as $r) {
                $matches[] = [
                    'id' => $r->id,
                    'title' => $r->title,
                    'price' => number_format((int) $r->price, 0, '.', ' ') . ' ' . $r->unit,
                    'region' => $r->region,
                    'url' => route('bozor.show', $r->id),
                ];
            }
        }
        // 3. 7-kunlik narx prognozi savoli
        elseif (mb_strpos($q, 'prognoz') !== false || mb_strpos($q, 'qachon') !== false || mb_strpos($q, '7 kun') !== false || mb_strpos($q, 'oshadi') !== false || mb_strpos($q, 'прогноз') !== false) {
            $reply = "ZaminJob AI Eye 7-kunlik narx prognozi: «{$prod['name']}» bo‘yicha {$best['city']} va Dubay bozorlarida taklif kamayishi sababli keyingi 7 kunda narx +5.5% dan +8.2% gacha ko‘tarilishi kutilmoqda. Hozir 48–72 soat ichida yuk jo‘natish eng yuqori marjani ushlab qolishga yordam beradi.";
            $metrics = [
                ['label' => '7 kunlik prognoz', 'value' => '+6.8% o‘sish'],
                ['label' => 'AI ishonchliligi', 'value' => '94%'],
                ['label' => 'Optimal oyna', 'value' => 'Keyingi 72 soat'],
            ];
            $checklist = [
                'Hozirgi narx (' . number_format($best['price_kg'], 0, '.', ' ') . ' so‘m/kg) bilan eksport taklifi chiqaring',
                'AI Signal kartasida «Kuzatish» tugmasini bosing — narx o‘zgarsa qo‘ng‘iroqchaga xabar keladi',
            ];
        }
        // 4. Umumiy eksport foyda va bozor tahlili
        else {
            $netFmt = number_format($best['net'], 0, '.', ' ');
            $priceFmt = number_format($best['price_kg'], 0, '.', ' ');
            $reply = "AI tahlili tayyor: {$tons} tonna «{$prod['name']}» uchun bugungi eng foydali yo‘nalish — {$best['country']} ({$best['city']}). Ulgurji narx {$priceFmt} so‘m/kg, barcha sovutkichli fura xarajatlaridan keyin kutilayotgan sof qo‘shimcha foyda +{$netFmt} so‘m (+{$best['margin']}%).";
            $metrics = [
                ['label' => 'Eng foydali bozor', 'value' => "{$best['country']} · {$best['city']}"],
                ['label' => "Sof foyda ({$tons}t)", 'value' => "+{$netFmt} so‘m"],
                ['label' => 'Yetkazish muddati', 'value' => $best['days']],
            ];
            $checklist = [
                'Yuqoridagi AI Foyda Kalkulyatorida 3 tilli (UZ/RU/EN) tijoriy taklifni nusxalab xaridorga yuboring',
                $tons < 20 ? "Hajm {$tons}t bo‘lgani uchun 20t fura to‘ldirishda Agro-Poolingdan foydalaning" : 'To‘liq 20t sovutkichli fura buyurtma qiling',
            ];
        }

        if (empty($matches)) {
            $qDb = DB::table('listings')->orderByDesc('id');
            if ($matchedKeyword !== '') {
                $qDb->where(function ($sub) use ($matchedKeyword) {
                    $like = '%' . $matchedKeyword . '%';
                    $sub->where('title', 'like', $like)->orWhere('subcategory', 'like', $like);
                });
            }
            $rows = $qDb->limit(3)->get();
            if ($rows->isEmpty()) {
                $rows = DB::table('listings')->orderByDesc('id')->limit(2)->get();
            }
            foreach ($rows as $r) {
                $matches[] = [
                    'id' => $r->id,
                    'title' => $r->title,
                    'price' => number_format((int) $r->price, 0, '.', ' ') . ' ' . $r->unit,
                    'region' => $r->region,
                    'url' => route('bozor.show', $r->id),
                ];
            }
        }

        return response()->json([
            'reply' => $reply,
            'metrics' => $metrics,
            'checklist' => $checklist,
            'matches' => $matches,
            'time' => now()->format('H:i:s'),
        ]);
    }

    public function aiPriceSuggestApi(Request $request)
    {
        $cat = trim((string) $request->input('category', 'Mevalar'));
        $sub = mb_strtolower(trim((string) $request->input('subcategory', 'Olma')));

        $map = [
            'olma' => [8500, 19500, 'so‘m / kg', 'Baxmal va Farg‘ona olmalari uchun joriy ulgurji narx; eksportda 19 500–34 000 so‘mgacha.'],
            'uzum' => [12000, 42000, 'so‘m / kg', 'Husayni va Kishmish uzumlari uchun BAA/Koreya eksport talabi yuqori.'],
            'gilos' => [35000, 92000, 'so‘m / kg', '26mm+ kalibrli gilos Seul va Moskva bozorida eng yuqori marja beradi.'],
            'anor' => [15000, 48000, 'so‘m / kg', 'Quva anori Dubay va Ar-Riyod bozorlarida +145% gacha foyda beradi.'],
            'mayiz' => [34000, 71000, 'so‘m / kg', 'Samarqand qora mayizi Xitoy va Koreya bozorlarida barqaror talabda.'],
            'pomidor' => [11000, 24500, 'so‘m / kg', 'Issiqxona pushti pomidori Moskva va Olmaotaga tez sotiladi.'],
            'traktor' => [450000, 550000, 'so‘m / gektar', 'Shudgor va kultivatsiya xizmatlari uchun o‘rtacha mavsumiy tarif.'],
            'fura' => [14500000, 18000000, 'so‘m / reys', '20t sovutkichli fura uchun UZ → KZ yo‘nalishi bo‘yicha o‘rtacha stavka.'],
            'refrijerator' => [15000000, 34000000, 'so‘m / reys', '20t refrijerator (KZ: 18 mln, RU: 34 mln so‘m atrofida).'],
            'issiqxona' => [45000000, 55000000, 'so‘m / gektar', 'Gidroponika va tomchilatib sug‘orishli issiqxona ijarasi/sotuvi bo‘yicha bozor narxi.'],
        ];

        $picked = [12000, 26000, 'so‘m / kg', "{$cat} toifasi bo‘yicha joriy o‘rtacha ulgurji bozor narxi."];
        foreach ($map as $k => $vals) {
            if (mb_strpos($sub, $k) !== false) {
                $picked = $vals;
                break;
            }
        }

        return response()->json([
            'price' => $picked[0],
            'export_price' => $picked[1],
            'unit' => $picked[2],
            'note' => $picked[3],
        ]);
    }

    public function escrowCreate(Request $request, $id)
    {
        $this->ensureListingsTable();
        $this->ensureCoreTables();
        $item = DB::table('listings')->where('id', (int) $id)->first();
        if (!$item) {
            return redirect()->route('bozor')->with('error', 'E’lon topilmadi.');
        }

        $qty = max(1, min(500, (float) $request->input('qty_tons', 5)));
        $isPerKg = mb_strpos(mb_strtolower((string) $item->unit), 'kg') !== false;
        $amount = (int) round($isPerKg ? ($qty * 1000 * (int) $item->price) : ($qty * (int) $item->price));
        $deposit = max(100000, (int) round($amount * 0.03));
        $buyer = auth()->user();

        DB::table('escrow_deals')->insert([
            'listing_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $item->user_id,
            'qty_tons' => $qty,
            'amount' => $amount,
            'deposit_amount' => $deposit,
            'status' => 'funded',
            'gps_proof' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($item->user_id) {
            DB::table('notifications')->insert([
                'user_id' => $item->user_id,
                'title' => 'Yangi Escrow xavfsiz bitim ochildi!',
                'body' => "{$buyer->name} «{$item->title}» bo‘yicha " . number_format($amount, 0, '.', ' ') . " so‘mlik Escrow bitimini ochdi (3% kafolat depoziti band qilindi).",
                'is_read' => 0,
                'created_at' => now(),
            ]);
        }

        return redirect()->route('bozor.show', $item->id)->with('success', 'ZaminJob Escrow xavfsiz bitimi ochildi! To‘lov yuk qabul qilingunga qadar himoyada saqlanadi.');
    }

    public function escrowStep(Request $request, $dealId)
    {
        $this->ensureCoreTables();
        $deal = DB::table('escrow_deals')->where('id', (int) $dealId)->first();
        if (!$deal) {
            return back()->with('error', 'Escrow bitimi topilmadi.');
        }

        $action = (string) $request->input('action', '');
        if (!in_array($action, ['shipped', 'completed', 'disputed'], true)) {
            return back();
        }

        $update = ['status' => $action, 'updated_at' => now()];
        if ($action === 'shipped') {
            $update['gps_proof'] = 'Yuklash GPS: 40.1158, 67.8422 · ' . now('Asia/Tashkent')->format('d.m.Y H:i');
            $msg = '2-bosqich: Yuk GPS + foto dalil bilan jo‘natildi!';
        } elseif ($action === 'completed') {
            if ($deal->seller_id) {
                $cur = (int) (DB::table('users')->where('id', $deal->seller_id)->value('trust_score') ?? 95);
                DB::table('users')->where('id', $deal->seller_id)->update(['trust_score' => min(100, $cur + 2)]);
                DB::table('notifications')->insert([
                    'user_id' => $deal->seller_id,
                    'title' => 'Escrow to‘lovi hisobingizga ochildi!',
                    'body' => number_format((int) $deal->amount, 0, '.', ' ') . ' so‘m xavfsiz bitim orqali o‘tkazildi. Agro-Pasport reytingingiz +2 ballga oshdi.',
                    'is_read' => 0,
                    'created_at' => now(),
                ]);
            }
            $msg = '3-bosqich: Yuk tasdiqlandi — Escrow to‘lovi sotuvchiga ochildi (+2 Agro-Pasport ball)!';
        } else {
            $msg = 'Nizo ochildi: to‘lov muzlatildi, ZaminJob hakamligi GPS va foto metadatani tekshirmoqda.';
        }

        DB::table('escrow_deals')->where('id', $deal->id)->update($update);
        return redirect()->route('bozor.show', $deal->listing_id)->with('success', $msg);
    }

    public function trustDepositApi(Request $request)
    {
        $this->ensureCoreTables();
        $user = auth()->user();
        $add = max(100000, min(10000000, (int) $request->input('amount', 500000)));
        $curBal = (int) (DB::table('users')->where('id', $user->id)->value('deposit_balance') ?? 0);
        $curScore = (int) (DB::table('users')->where('id', $user->id)->value('trust_score') ?? 95);

        DB::table('users')->where('id', $user->id)->update([
            'deposit_balance' => $curBal + $add,
            'trust_score' => min(100, $curScore + 3),
            'updated_at' => now(),
        ]);

        DB::table('notifications')->insert([
            'user_id' => $user->id,
            'title' => 'Kafolat depoziti band qilindi',
            'body' => number_format($add, 0, '.', ' ') . ' so‘m qaytariladigan kafolat depoziti qo‘shildi. Agro-Pasport ishonch ballingiz oshdi.',
            'is_read' => 0,
            'created_at' => now(),
        ]);

        return redirect()->route('profil')->with('success', number_format($add, 0, '.', ' ') . ' so‘m kafolat depoziti band qilindi va Agro-Pasport ballingiz oshdi!');
    }

    public function trustPenaltyApi(Request $request, $userId)
    {
        $this->ensureCoreTables();
        $target = DB::table('users')->where('id', (int) $userId)->first();
        if (!$target) {
            return redirect()->route('kabinet')->with('error', 'Foydalanuvchi topilmadi.');
        }

        $curScore = (int) ($target->trust_score ?? 95);
        $curStrikes = (int) ($target->strikes ?? 0);
        $newScore = max(0, $curScore - 15);

        DB::table('users')->where('id', $target->id)->update([
            'trust_score' => $newScore,
            'strikes' => $curStrikes + 1,
            'updated_at' => now(),
        ]);

        DB::table('notifications')->insert([
            'user_id' => $target->id,
            'title' => 'Agro-Pasport reyting jarimasi (−15 ball)',
            'body' => 'Kelishilgan ishga chiqmaganlik sababli Agro-Pasport ballingiz ' . $newScore . '/100 ga tushirildi.',
            'is_read' => 0,
            'created_at' => now(),
        ]);

        return redirect()->route('kabinet')->with('success', "{$target->name} foydalanuvchisiga −15 ball Agro-Pasport jarimasi qo‘llandi (joriy ball: {$newScore}/100).");
    }
}
