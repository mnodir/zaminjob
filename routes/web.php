<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'AdminController@index')->name('home');
Route::get('/admin', 'AdminController@index')->name('admin');

// 100% Native Laravel Kabinet, Profil va Bildirishnomalar
Route::middleware('auth')->group(function () {
    Route::get('/profil', 'HomeController@profileShow')->name('profil');
    Route::post('/profil', 'HomeController@profileSave')->name('profil.save');
    Route::get('/kabinet', 'HomeController@cabinetShow')->name('kabinet');
    Route::post('/kabinet/vacancy', 'HomeController@vacancyStore')->name('kabinet.vacancy');
    Route::post('/kabinet/apply/{id}', 'HomeController@vacancyApply')->where('id', '[0-9]+')->name('kabinet.apply');
    Route::post('/kabinet/application/{id}', 'HomeController@applicationUpdate')->where('id', '[0-9]+')->name('kabinet.app');
    Route::get('/api/notifications', 'HomeController@notificationsApi');
    Route::get('/api/notifications/read', 'HomeController@notificationsRead');
});

Route::get('/bozor', 'HomeController@marketIndex')->name('bozor');
Route::post('/bozor', 'HomeController@marketStore')->middleware('auth')->name('bozor.store');
Route::get('/bozor/{id}', 'HomeController@marketShow')->where('id', '[0-9]+')->name('bozor.show');
Route::post('/bozor/{id}/message', 'HomeController@marketMessage')->where('id', '[0-9]+')->name('bozor.message');

// ZaminJob AI Eksport Signallari (Real-Time), AI Maslahatchi va Narx Tavsiyasi API
Route::get('/api/ai-signals', 'HomeController@aiSignalsApi');
Route::post('/api/ai-analyze', 'HomeController@aiAnalyzeApi');
Route::post('/api/ai-watch', 'HomeController@aiWatchApi');
Route::post('/api/ai-chat', 'HomeController@aiChatApi');
Route::post('/api/ai-price-suggest', 'HomeController@aiPriceSuggestApi');

// Jonli username band/bosh tekshiruvi
Route::get('/api/check-username', function (Request $request) {
    $raw = strtolower(ltrim(trim((string) $request->query('u', '')), '@'));
    if ($raw === '' || strlen($raw) < 3) {
        return response()->json(['status' => 'short', 'message' => 'Kamida 3 ta belgi kiriting']);
    }
    if (!preg_match('/^[a-z0-9_]{3,40}$/', $raw)) {
        return response()->json(['status' => 'invalid', 'message' => 'Faqat kichik lotin harflari, raqam va _']);
    }
    if (in_array($raw, ['admin', 'mnodir', 'root', 'zaminjob'], true)) {
        return response()->json(['status' => 'taken', 'message' => 'Bu username band']);
    }
    try {
        $q = DB::table('users')->where('username', $raw);
        if (auth()->check()) {
            $q->where('id', '!=', auth()->id());
        }
        $exists = $q->exists();
        return response()->json([
            'status' => $exists ? 'taken' : 'free',
            'message' => $exists ? 'Bu username band' : 'Bu username bo‘sh',
        ]);
    } catch (\Throwable $e) {
        return response()->json(['status' => 'free', 'message' => 'Bu username bo‘sh']);
    }
});
