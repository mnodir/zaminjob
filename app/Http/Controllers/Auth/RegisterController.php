<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function ensureDatabaseReady(): void
    {
        try {
            $host = config('database.connections.mysql.host', '127.0.0.1');
            $port = config('database.connections.mysql.port', '3306');
            $db   = preg_replace('/[^a-zA-Z0-9_]/', '', (string) config('database.connections.mysql.database', 'laravel'));
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
                    `email` VARCHAR(255) NULL UNIQUE,
                    `phone` VARCHAR(30) NULL UNIQUE,
                    `email_verified_at` TIMESTAMP NULL,
                    `password` VARCHAR(255) NOT NULL,
                    `role` VARCHAR(30) NULL,
                    `is_onboarded` TINYINT(1) NOT NULL DEFAULT 0,
                    `remember_token` VARCHAR(100) NULL,
                    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            DB::purge('mysql');
            DB::reconnect('mysql');

            if (Schema::hasTable('users') && !Schema::hasColumn('users', 'phone')) {
                DB::statement('ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(30) NULL UNIQUE AFTER `name`');
            }
            if (Schema::hasTable('users') && Schema::hasColumn('users', 'email')) {
                DB::statement('ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL');
            }
        } catch (\Throwable $e) {
        }
    }

    protected function validator(array $data)
    {
        $this->ensureDatabaseReady();

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'min:7', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Ism va familiyangizni kiriting.',
            'phone.required' => 'Telefon raqamingizni kiriting.',
            'phone.unique' => 'Bu telefon raqam allaqachon ro‘yxatdan o‘tgan.',
            'password.required' => 'Parolni kiriting.',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo‘lishi kerak.',
            'password.confirmed' => 'Parollar bir-biriga mos kelmadi.',
        ]);
    }

    protected function create(array $data)
    {
        $this->ensureDatabaseReady();

        return User::create([
            'name' => $data['name'],
            'phone' => trim($data['phone']),
            'password' => Hash::make($data['password']),
        ]);
    }
}
