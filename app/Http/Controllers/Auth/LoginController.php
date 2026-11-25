<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function login(Request $request)
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
        } catch (\Throwable $e) {
        }

        return $this->traitLogin($request);
    }
}
