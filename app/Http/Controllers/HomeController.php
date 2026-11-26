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
                $table->timestamps();
            });
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
        if ($request->hasFile('photos')) {
            foreach (array_slice($request->file('photos'), 0, 6) as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('listings', 'public');
                    $savedImgs[] = '/storage/' . $path;
                }
            }
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('bozor.show', $id)->with('success', 'E’loningiz muvaffaqiyatli joylandi!');
    }

    public function marketShow($id)
    {
        $this->ensureListingsTable();
        $item = DB::table('listings')->where('id', (int) $id)->first();
        if (!$item) {
            return redirect()->route('bozor')->with('error', 'E’lon topilmadi.');
        }

        $seller = $item->user_id ? DB::table('users')->where('id', $item->user_id)->first() : null;
        $others = DB::table('listings')->where('id', '!=', $item->id)->orderByDesc('id')->limit(3)->get();
        return view('listing', compact('item', 'seller', 'others'));
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
                ->select('applications.*', 'users.name as worker_name', 'users.phone as worker_phone')
                ->where('applications.vacancy_id', $mv->id)
                ->orderByDesc('applications.id')
                ->get();
        }

        $workers = DB::table('users')
            ->leftJoin('worker_profiles', 'worker_profiles.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.phone', 'users.role', 'users.avatar_path', 'users.region', 'users.district', 'worker_profiles.profession')
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
}
