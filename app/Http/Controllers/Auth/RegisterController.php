<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/profil';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function ensureDatabaseReady(): void
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

            $cols = [
                'username' => 'VARCHAR(60) NULL UNIQUE AFTER `name`',
                'phone' => 'VARCHAR(30) NULL UNIQUE AFTER `username`',
                'role' => 'VARCHAR(30) NULL AFTER `password`',
                'is_onboarded' => 'TINYINT(1) NOT NULL DEFAULT 1 AFTER `role`',
                'avatar_path' => 'VARCHAR(255) NULL AFTER `is_onboarded`',
                'bio' => 'TEXT NULL AFTER `avatar_path`',
                'region' => 'VARCHAR(100) NULL AFTER `bio`',
                'district' => 'VARCHAR(120) NULL AFTER `region`',
                'telegram' => 'VARCHAR(100) NULL AFTER `district`',
            ];
            foreach ($cols as $col => $def) {
                $chk = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = ?");
                $chk->execute([$col]);
                if (!$chk->fetch()) {
                    $pdo->exec("ALTER TABLE `users` ADD COLUMN `{$col}` {$def}");
                }
            }
            try {
                $pdo->exec("ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL");
            } catch (\Throwable $ignore) {
            }

            DB::purge('mysql');
            DB::reconnect('mysql');
        } catch (\Throwable $e) {
        }
    }

    protected function validator(array $data)
    {
        $this->ensureDatabaseReady();

        $data['username'] = strtolower(ltrim(trim((string) ($data['username'] ?? '')), '@'));

        return Validator::make($data, [
            'role' => ['required', 'in:worker,farmer,employer,tech,logistics'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:40', 'regex:/^[a-z0-9_]+$/', 'not_in:admin,mnodir,root,zaminjob', 'unique:users,username'],
            'phone' => ['required', 'string', 'min:7', 'max:30', 'not_in:mnodir', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'role.required' => 'Iltimos, sohangizni tanlang.',
            'name.required' => 'Ism va familiyangizni kiriting.',
            'username.required' => 'Username kiriting.',
            'username.min' => 'Username kamida 3 ta belgidan iborat bo‘lsin.',
            'username.regex' => 'Username faqat kichik lotin harflari, raqam va _ dan iborat bo‘lsin.',
            'username.unique' => 'Bu username allaqachon olingan, boshqasini tanlang.',
            'username.not_in' => 'Bu username band.',
            'phone.required' => 'Telefon raqamingizni kiriting.',
            'phone.unique' => 'Bu telefon raqam allaqachon ro‘yxatdan o‘tgan.',
            'password.required' => 'Parolni kiriting.',
            'password.min' => 'Parol kamida 8 ta belgidan iborat bo‘lishi kerak.',
            'password.confirmed' => 'Parollar bir-biriga mos kelmadi.',
        ]);
    }

    protected function create(array $data)
    {
        $this->ensureDatabaseReady();

        $cleanUsername = strtolower(ltrim(trim((string) $data['username']), '@'));

        return User::create([
            'name' => trim($data['name']),
            'username' => $cleanUsername,
            'phone' => trim($data['phone']),
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_onboarded' => 1,
        ]);
    }

    protected function registered(Request $request, $user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['user_id'] = (int) $user->id;

        return redirect()->route('profil');
    }
}
