<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================================================
// 1. MA'LUMOTLAR BAZASI (MySQL / MariaDB — Laravel 7 .env bilan avtomatik)
// =========================================================================
function loadLaravelEnv(string $envPath): array {
    $vars = [];
    if (!is_file($envPath)) {
        return $vars;
    }
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }
        [$k, $v] = explode('=', $line, 2);
        $vars[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
    }
    return $vars;
}

$env = loadLaravelEnv(__DIR__ . '/.env');
$dbHost = $env['DB_HOST'] ?? '127.0.0.1';
$dbPort = $env['DB_PORT'] ?? '3306';
$dbName = $env['DB_DATABASE'] ?? 'zaminjob';
$dbUser = $env['DB_USERNAME'] ?? 'root';
$dbPass = $env['DB_PASSWORD'] ?? '';
$tgBotToken = $env['TELEGRAM_BOT_TOKEN'] ?? '';

try {
    $pdoInit = new PDO(
        "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $safeDb = preg_replace('/[^a-zA-Z0-9_]/', '', $dbName);
    $pdoInit->exec("CREATE DATABASE IF NOT EXISTS `{$safeDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdoInit = null;

    $pdo = new PDO(
        "mysql:host={$dbHost};port={$dbPort};dbname={$safeDb};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die(
        '<div style="font-family:sans-serif;max-width:560px;margin:40px auto;padding:20px;border:1px solid #fecaca;background:#fef2f2;border-radius:12px;color:#991b1b">'
        . '<strong>MySQL bazasiga ulanishda xatolik:</strong><br>'
        . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>'
    );
}

function ensureColumn(PDO $pdo, string $table, string $column, string $definition): void {
    $stmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
    }
}

$pdo->exec("
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `username` VARCHAR(60) NULL UNIQUE,
    `email` VARCHAR(255) NULL UNIQUE,
    `phone` VARCHAR(30) NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(30) DEFAULT 'worker',
    `is_onboarded` TINYINT(1) NOT NULL DEFAULT 1,
    `avatar_path` VARCHAR(255) NULL,
    `bio` TEXT NULL,
    `region` VARCHAR(100) NULL,
    `district` VARCHAR(120) NULL,
    `telegram` VARCHAR(100) NULL,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

ensureColumn($pdo, 'users', 'username', 'VARCHAR(60) NULL UNIQUE AFTER `name`');
ensureColumn($pdo, 'users', 'phone', 'VARCHAR(30) NULL UNIQUE AFTER `username`');
ensureColumn($pdo, 'users', 'role', "VARCHAR(30) DEFAULT 'worker' AFTER `password`");
ensureColumn($pdo, 'users', 'is_onboarded', 'TINYINT(1) NOT NULL DEFAULT 1 AFTER `role`');
ensureColumn($pdo, 'users', 'avatar_path', 'VARCHAR(255) NULL AFTER `is_onboarded`');
ensureColumn($pdo, 'users', 'bio', 'TEXT NULL AFTER `avatar_path`');
ensureColumn($pdo, 'users', 'region', 'VARCHAR(100) NULL AFTER `bio`');
ensureColumn($pdo, 'users', 'district', 'VARCHAR(120) NULL AFTER `region`');
ensureColumn($pdo, 'users', 'telegram', 'VARCHAR(100) NULL AFTER `district`');
try {
    $pdo->exec("ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL");
} catch (Throwable $ignore) {
}

$pdo->exec("
CREATE TABLE IF NOT EXISTS `worker_profiles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `profession` VARCHAR(150) NOT NULL DEFAULT '',
    `description` TEXT NULL,
    `region` VARCHAR(100) DEFAULT NULL,
    `district` VARCHAR(120) DEFAULT NULL,
    `expected_salary` BIGINT UNSIGNED DEFAULT NULL,
    `payment_type` VARCHAR(30) NOT NULL DEFAULT 'monthly',
    `equipment_type` VARCHAR(180) DEFAULT NULL,
    `photo_path` VARCHAR(255) DEFAULT NULL,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_wp_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `employer_profiles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `company_name` VARCHAR(255) DEFAULT NULL,
    `needed_profession` VARCHAR(150) NOT NULL DEFAULT '',
    `region` VARCHAR(100) DEFAULT NULL,
    `district` VARCHAR(120) DEFAULT NULL,
    `land_area` VARCHAR(80) DEFAULT NULL,
    `crop_type` VARCHAR(180) DEFAULT NULL,
    `irrigation_type` VARCHAR(100) DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_ep_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `vacancies` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `employer_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `profession` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `region` VARCHAR(100) DEFAULT NULL,
    `district` VARCHAR(120) DEFAULT NULL,
    `salary` VARCHAR(120) DEFAULT NULL,
    `salary_amount` BIGINT UNSIGNED DEFAULT NULL,
    `payment_type` VARCHAR(30) NOT NULL DEFAULT 'monthly',
    `status` VARCHAR(20) NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_vac_employer` FOREIGN KEY (`employer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `applications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `vacancy_id` BIGINT UNSIGNED NOT NULL,
    `worker_id` BIGINT UNSIGNED NOT NULL,
    `status` VARCHAR(30) NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uniq_vac_worker` (`vacancy_id`, `worker_id`),
    CONSTRAINT `fk_app_vacancy` FOREIGN KEY (`vacancy_id`) REFERENCES `vacancies`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_app_worker` FOREIGN KEY (`worker_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `notifications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(180) NOT NULL,
    `body` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_notif_user_read` (`user_id`, `is_read`),
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

ensureColumn($pdo, 'worker_profiles', 'region', 'VARCHAR(100) NULL AFTER `description`');
ensureColumn($pdo, 'worker_profiles', 'district', 'VARCHAR(120) NULL AFTER `region`');
ensureColumn($pdo, 'worker_profiles', 'expected_salary', 'BIGINT UNSIGNED NULL AFTER `district`');
ensureColumn($pdo, 'worker_profiles', 'payment_type', "VARCHAR(30) NOT NULL DEFAULT 'monthly' AFTER `expected_salary`");
ensureColumn($pdo, 'worker_profiles', 'equipment_type', 'VARCHAR(180) NULL AFTER `payment_type`');

ensureColumn($pdo, 'employer_profiles', 'region', 'VARCHAR(100) NULL AFTER `needed_profession`');
ensureColumn($pdo, 'employer_profiles', 'district', 'VARCHAR(120) NULL AFTER `region`');
ensureColumn($pdo, 'employer_profiles', 'land_area', 'VARCHAR(80) NULL AFTER `district`');
ensureColumn($pdo, 'employer_profiles', 'crop_type', 'VARCHAR(180) NULL AFTER `land_area`');
ensureColumn($pdo, 'employer_profiles', 'irrigation_type', 'VARCHAR(100) NULL AFTER `crop_type`');

ensureColumn($pdo, 'vacancies', 'region', 'VARCHAR(100) NULL AFTER `description`');
ensureColumn($pdo, 'vacancies', 'district', 'VARCHAR(120) NULL AFTER `region`');
ensureColumn($pdo, 'vacancies', 'salary_amount', 'BIGINT UNSIGNED NULL AFTER `salary`');

// =========================================================================
// 2. O'ZBEKISTON 14 VILOYAT -> BARCHA TUMAN VA SHAHARLAR + AI KLASTERLAR
// =========================================================================
const UZ_LOCATIONS = [
    'Toshkent shahri' => ['Bektemir tumani', 'Chilonzor tumani', 'Mirzo Ulug‘bek tumani', 'Mirobod tumani', 'Olmazor tumani', 'Sergeli tumani', 'Shayxontohur tumani', 'Uchtepa tumani', 'Yakkasaroy tumani', 'Yashnobod tumani', 'Yunusobod tumani', 'Yangihayot tumani'],
    'Toshkent viloyati' => ['Nurafshon shahri', 'Olmaliq shahri', 'Angren shahri', 'Bekobod tumani', 'Bo‘ka tumani', 'Bo‘stonliq tumani', 'Chinoz tumani', 'Qibray tumani', 'Ohangaron tumani', 'Oqqo‘rg‘on tumani', 'Parkent tumani', 'Piskent tumani', 'Quyi Chirchiq tumani', 'O‘rta Chirchiq tumani', 'Yuqori Chirchiq tumani', 'Yangiyo‘l tumani', 'Zangiota tumani', 'Toshkent tumani'],
    'Jizzax viloyati' => ['Jizzax shahri', 'Arnasoy tumani', 'Baxmal tumani', 'Do‘stlik tumani', 'Forish tumani', 'G‘allaorol tumani', 'Mirzacho‘l tumani', 'Paxtakor tumani', 'Sharof Rashidov tumani', 'Yangiobod tumani', 'Zafarobod tumani', 'Zarbdor tumani', 'Zomin tumani'],
    'Samarqand viloyati' => ['Samarqand shahri', 'Kattaqo‘rg‘on shahri', 'Bulung‘ur tumani', 'Ishtixon tumani', 'Jomboy tumani', 'Narpay tumani', 'Nurobod tumani', 'Oqdaryo tumani', 'Paxtachi tumani', 'Payariq tumani', 'Pastdarg‘om tumani', 'Qo‘shrabot tumani', 'Samarqand tumani', 'Toyloq tumani', 'Urgut tumani'],
    'Farg‘ona viloyati' => ['Farg‘ona shahri', 'Qo‘qon shahri', 'Marg‘ilon shahri', 'Quvasoy shahri', 'Bag‘dod tumani', 'Beshariq tumani', 'Buvayda tumani', 'Dang‘ara tumani', 'Farg‘ona tumani', 'Furqat tumani', 'Oltiariq tumani', 'O‘zbekiston tumani', 'Qo‘shtepa tumani', 'Quva tumani', 'Rishton tumani', 'So‘x tumani', 'Toshloq tumani', 'Uchko‘prik tumani', 'Yozyovon tumani'],
    'Andijon viloyati' => ['Andijon shahri', 'Xonobod shahri', 'Andijon tumani', 'Asaka tumani', 'Baliqchi tumani', 'Bo‘ston tumani', 'Buloqboshi tumani', 'Izboskan tumani', 'Jalaquduq tumani', 'Xo‘jaobod tumani', 'Qo‘rg‘ontepa tumani', 'Marhamat tumani', 'Oltinko‘l tumani', 'Paxtaobod tumani', 'Shahrixon tumani', 'Ulug‘nor tumani'],
    'Namangan viloyati' => ['Namangan shahri', 'Chortoq tumani', 'Chust tumani', 'Kosonsoy tumani', 'Mingbuloq tumani', 'Namangan tumani', 'Norin tumani', 'Pop tumani', 'To‘raqo‘rg‘on tumani', 'Uchqo‘rg‘on tumani', 'Uychi tumani', 'Yangiqo‘rg‘on tumani'],
    'Buxoro viloyati' => ['Buxoro shahri', 'Kogon shahri', 'Buxoro tumani', 'G‘ijduvon tumani', 'Jondor tumani', 'Kogon tumani', 'Olot tumani', 'Peshku tumani', 'Qorako‘l tumani', 'Qorovulbozor tumani', 'Romitan tumani', 'Shofirkon tumani', 'Vobkent tumani'],
    'Qashqadaryo viloyati' => ['Qarshi shahri', 'Shahrisabz shahri', 'Chiroqchi tumani', 'Dehqonobod tumani', 'G‘uzor tumani', 'Kasbi tumani', 'Kitob tumani', 'Koson tumani', 'Mirishkor tumani', 'Muborak tumani', 'Nishon tumani', 'Qamashi tumani', 'Qarshi tumani', 'Yakkabog‘ tumani', 'Ko‘kdala tumani'],
    'Surxondaryo viloyati' => ['Termiz shahri', 'Angor tumani', 'Bandixon tumani', 'Boysun tumani', 'Denov tumani', 'Jarqo‘rg‘on tumani', 'Muzrabot tumani', 'Oltinsoy tumani', 'Qiziriq tumani', 'Qumqo‘rg‘on tumani', 'Sariosiyo tumani', 'Sherobod tumani', 'Sho‘rchi tumani', 'Termiz tumani', 'Uzun tumani'],
    'Sirdaryo viloyati' => ['Guliston shahri', 'Shirin shahri', 'Yangiyer shahri', 'Boyovut tumani', 'Guliston tumani', 'Mirzaobod tumani', 'Oqoltin tumani', 'Sardoba tumani', 'Sayxunobod tumani', 'Sirdaryo tumani', 'Xovos tumani'],
    'Navoiy viloyati' => ['Navoiy shahri', 'Zarafshon shahri', 'G‘ozg‘on shahri', 'Karmana tumani', 'Konimex tumani', 'Navbahor tumani', 'Nurota tumani', 'Qiziltepa tumani', 'Tomdi tumani', 'Uchquduq tumani', 'Xatirchi tumani'],
    'Xorazm viloyati' => ['Urganch shahri', 'Xiva shahri', 'Bog‘ot tumani', 'Gurlan tumani', 'Qo‘shko‘pir tumani', 'Shovot tumani', 'Tuproqqal’a tumani', 'Urganch tumani', 'Xazorasp tumani', 'Xiva tumani', 'Xonqa tumani', 'Yangiariq tumani', 'Yangibozor tumani'],
    'Qoraqalpog‘iston Respublikasi' => ['Nukus shahri', 'Amudaryo tumani', 'Beruniy tumani', 'Bo‘zatov tumani', 'Chimboy tumani', 'Ellikqal’a tumani', 'Kegeyli tumani', 'Mo‘ynoq tumani', 'Nukus tumani', 'Qanliko‘l tumani', 'Qo‘ng‘irot tumani', 'Qorao‘zak tumani', 'Shumanay tumani', 'Taxtako‘pir tumani', 'To‘rtko‘l tumani', 'Xo‘jayli tumani', 'Taxiatosh tumani'],
];

const ROLE_LABELS = [
    'worker' => 'Ishchi',
    'farmer' => 'Dehqon',
    'employer' => 'Tadbirkor',
    'tech' => 'Texnik xizmat',
    'logistics' => 'Yuk tashish xizmati',
    'admin' => 'Admin',
];

const AI_PROFESSION_CLUSTERS = [
    'texnika' => [
        'label' => 'Qishloq xo‘jaligi texnikasi va haydovchilik',
        'terms' => ['traktorchi', 'kombaynchi', 'mexanizator', 'haydovchi', 'shofyor', 'fura', 'yuk mashina', 'ekskavatorchi', 'buldozerchi', 'kranchi', 'texnika', 'mtz', 'voditel'],
    ],
    'dehqonchilik' => [
        'label' => 'Bog‘dorchilik, issiqxona va dehqonchilik',
        'terms' => ['bog‘bon', 'bogbon', 'dehqon', 'agronom', 'issiqxonachi', 'parnikchi', 'parnik', 'teplitsa', 'ko‘chat', 'kochat', 'tokchi', 'uzumchi', 'mevachi', 'sabzavotchi', 'sug‘oruvchi', 'mirob', 'suvchi', 'terimchi', 'brigada'],
    ],
    'chorvachilik' => [
        'label' => 'Chorvachilik va veterinariya',
        'terms' => ['cho‘pon', 'chopon', 'otboqar', 'molboqar', 'chorvador', 'veterinar', 'sog‘uvchi', 'parrandachi', 'asalarichi', 'ferma'],
    ],
    'qurilish' => [
        'label' => 'Qurilish, payvandlash va ustalichilik',
        'terms' => ['payvandchi', 'svarkachi', 'svarka', 'elektrik', 'santexnik', 'quruvchi', 'g‘isht teruvchi', 'suvoqchi', 'duradgor', 'usta', 'temirchi', 'betonchi', 'tom yopuvchi', 'chilangar', 'slesar'],
    ],
    'logistika' => [
        'label' => 'Logistika, ombor va qadoqlash',
        'terms' => ['yukchi', 'gruzchik', 'omborchi', 'qadoqlovchi', 'sortirovka', 'ekspeditor', 'kuryer', 'yetkazib beruvchi', 'logist'],
    ],
    'xizmat' => [
        'label' => 'Savdo, oshxona va boshqaruv',
        'terms' => ['sotuvchi', 'kassir', 'oshpaz', 'nonvoy', 'qassob', 'farrosh', 'qo‘riqchi', 'qorovul', 'buxgalter', 'hisobchi', 'menejer', 'ish boshqaruvchi'],
    ],
];

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function normText(?string $text): string {
    $t = mb_strtolower(trim((string)$text), 'UTF-8');
    return str_replace(["‘", "’", "`", "'", "ʻ", "ʼ"], "'", $t);
}

function parseSalaryAmount(?string $raw): ?int {
    if ($raw === null || trim($raw) === '') {
        return null;
    }
    $lower = mb_strtolower(trim($raw), 'UTF-8');
    if (preg_match('/(\d+(?:[.,]\d+)?)\s*(mln|million)/u', $lower, $m)) {
        return (int)round((float)str_replace(',', '.', $m[1]) * 1000000);
    }
    if (preg_match('/(\d+(?:[.,]\d+)?)\s*(ming|k\b)/u', $lower, $m)) {
        return (int)round((float)str_replace(',', '.', $m[1]) * 1000);
    }
    $digits = preg_replace('/[^\d]/', '', $lower);
    return $digits === '' ? null : (int)$digits;
}

function formatSom(?int $amount): string {
    return (!$amount || $amount <= 0) ? '' : number_format($amount, 0, '.', ' ') . ' so‘m';
}

function smartExpandProfessions(string $query, bool $enableAi = true): array {
    $q = normText($query);
    if ($q === '') {
        return ['exact' => [], 'related' => [], 'clusters' => []];
    }
    $tokens = array_values(array_filter(preg_split('/[\s,\/\-]+/u', $q), fn($w) => mb_strlen($w, 'UTF-8') >= 2));
    $exact = array_unique(array_merge([$q], $tokens));
    $related = [];
    $clusters = [];
    if (!$enableAi) {
        return ['exact' => $exact, 'related' => [], 'clusters' => []];
    }
    foreach (AI_PROFESSION_CLUSTERS as $cluster) {
        $matched = false;
        foreach ($cluster['terms'] as $term) {
            $nt = normText($term);
            foreach ($exact as $tok) {
                if (mb_strpos($nt, $tok, 0, 'UTF-8') !== false || mb_strpos($tok, $nt, 0, 'UTF-8') !== false) {
                    $matched = true;
                    break 2;
                }
                if (mb_strlen($tok, 'UTF-8') >= 4 && mb_strlen($nt, 'UTF-8') >= 4) {
                    similar_text($tok, $nt, $pct);
                    if ($pct >= 72.0) {
                        $matched = true;
                        break 2;
                    }
                }
            }
        }
        if ($matched) {
            $clusters[] = $cluster['label'];
            foreach ($cluster['terms'] as $term) {
                $nt = normText($term);
                if (!in_array($nt, $exact, true)) {
                    $related[] = $nt;
                }
            }
        }
    }
    return ['exact' => array_values($exact), 'related' => array_values(array_unique($related)), 'clusters' => array_values(array_unique($clusters))];
}

function scoreMatch(string $haystack, array $aiInfo): array {
    $h = normText($haystack);
    if (empty($aiInfo['exact'])) {
        return ['score' => 1, 'type' => 'all'];
    }
    foreach ($aiInfo['exact'] as $term) {
        if (mb_strpos($h, $term, 0, 'UTF-8') !== false) {
            return ['score' => 100, 'type' => 'exact'];
        }
    }
    $words = array_filter(preg_split('/[\s,.\-\/]+/u', $h), fn($w) => mb_strlen($w, 'UTF-8') >= 4);
    foreach ($aiInfo['exact'] as $term) {
        if (mb_strlen($term, 'UTF-8') < 4) continue;
        foreach ($words as $w) {
            similar_text($term, $w, $pct);
            if ($pct >= 75.0) return ['score' => 85, 'type' => 'ai'];
        }
    }
    foreach ($aiInfo['related'] as $rel) {
        if (mb_strpos($h, $rel, 0, 'UTF-8') !== false) return ['score' => 70, 'type' => 'ai'];
    }
    return ['score' => 0, 'type' => 'none'];
}

function pushNotification(PDO $pdo, int $userId, string $title, string $body, string $tgBotToken = ''): void {
    $stmt = $pdo->prepare("INSERT INTO `notifications` (`user_id`, `title`, `body`, `is_read`) VALUES (?, ?, ?, 0)");
    $stmt->execute([$userId, $title, $body]);

    if ($tgBotToken !== '') {
        $uStmt = $pdo->prepare("SELECT `telegram` FROM `users` WHERE `id` = ?");
        $uStmt->execute([$userId]);
        $tg = trim((string)($uStmt->fetchColumn() ?: ''));
        if ($tg !== '' && preg_match('/^-?\d+$/', $tg)) {
            $ctx = stream_context_create(['http' => ['timeout' => 2]]);
            $text = urlencode("ZaminJob: {$title}\n{$body}");
            @file_get_contents("https://api.telegram.org/bot{$tgBotToken}/sendMessage?chat_id={$tg}&text={$text}", false, $ctx);
        }
    }
}

function getUploadBaseDir(): array {
    $dir = is_dir(__DIR__ . '/public') ? (__DIR__ . '/public/uploads') : (__DIR__ . '/uploads');
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    return ['fs' => $dir, 'web' => 'uploads'];
}

function photoExists(?string $photoPath): bool {
    if (empty($photoPath)) return false;
    return is_file(__DIR__ . '/' . $photoPath) || is_file(__DIR__ . '/public/' . $photoPath);
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function currentUser(PDO $pdo): ?array {
    if (empty($_SESSION['user_id'])) return null;
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $u = $stmt->fetch();
    if (!$u) {
        unset($_SESSION['user_id']);
        return null;
    }
    return $u;
}

$self = basename($_SERVER['SCRIPT_NAME'] ?? __FILE__);

// Laravel /kabinet va /profil dan kelgan imzolangan token orqali sessiyani ochish
if (isset($_GET['uid'], $_GET['ts'], $_GET['sig'])) {
    $bUid = (int) $_GET['uid'];
    $bTs = (int) $_GET['ts'];
    $bSig = (string) $_GET['sig'];
    $appKey = $env['APP_KEY'] ?? 'zaminjob_key';
    $expected = hash_hmac('sha256', $bUid . '|' . $bTs, $appKey);
    if ($bUid > 0 && abs(time() - $bTs) <= 300 && hash_equals($expected, $bSig)) {
        $_SESSION['user_id'] = $bUid;
        $target = (($_GET['view'] ?? '') === 'profile') ? ($self . '?view=profile') : $self;
        redirect($target);
    }
}

$user = currentUser($pdo);

// =========================================================================
// 3. JONLI BILDIRISHNOMALAR AJAX API (?api=notifications / mark_read)
// =========================================================================
if (isset($_GET['api'])) {
    header('Content-Type: application/json; charset=utf-8');
    if (!$user) {
        echo json_encode(['unread' => 0, 'items' => []]);
        exit;
    }
    if ($_GET['api'] === 'mark_read') {
        $pdo->prepare("UPDATE `notifications` SET `is_read` = 1 WHERE `user_id` = ?")->execute([$user['id']]);
        echo json_encode(['ok' => true]);
        exit;
    }
    if ($_GET['api'] === 'notifications') {
        $cStmt = $pdo->prepare("SELECT COUNT(*) FROM `notifications` WHERE `user_id` = ? AND `is_read` = 0");
        $cStmt->execute([$user['id']]);
        $unread = (int)$cStmt->fetchColumn();

        $iStmt = $pdo->prepare("SELECT `id`, `title`, `body`, `is_read`, DATE_FORMAT(`created_at`, '%d.%m %H:%i') AS `created_at` FROM `notifications` WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 12");
        $iStmt->execute([$user['id']]);
        echo json_encode(['unread' => $unread, 'items' => $iStmt->fetchAll()], JSON_UNESCAPED_UNICODE);
        exit;
    }
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    redirect('/');
}

// Agar foydalanuvchi login qilmagan bo'lsa, asosiy Laravel /login sahifasiga olib boramiz
if (!$user) {
    redirect('/login');
}

$isAdminUser = (($user['role'] ?? '') === 'admin') || (preg_replace('/\s+/', '', (string)($user['phone'] ?? '')) === '+998904047170') || (($user['phone'] ?? '') === 'mnodir');
if ($isAdminUser && ($_GET['view'] ?? '') !== 'profile' && ($_POST['action'] ?? '') !== 'save_profile') {
    redirect('/admin');
}

// =========================================================================
// 4. BARCHA POST AMALLARI
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- A. GITHUB USLUBIDAGI PROFIL VA SOZLAMALARNI SAQLASH ---
    if ($action === 'save_profile') {
        $name = trim($_POST['name'] ?? '');
        $username = strtolower(ltrim(trim($_POST['username'] ?? ''), '@'));
        $role = $_POST['role'] ?? ($user['role'] ?: 'worker');
        $region = trim($_POST['region'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $telegram = trim($_POST['telegram'] ?? '');

        if ($name === '') {
            setFlash('error', 'Ism va familiyangizni kiriting.');
            redirect($self . '?view=profile');
        }
        if ($username !== '') {
            if (!preg_match('/^[a-z0-9_]{3,40}$/', $username) || in_array($username, ['admin', 'mnodir', 'root', 'zaminjob'], true)) {
                setFlash('error', 'Username 3–40 ta kichik lotin harfi, raqam yoki _ dan iborat bo‘lsin.');
                redirect($self . '?view=profile');
            }
            $uChk = $pdo->prepare("SELECT id FROM `users` WHERE `username` = ? AND `id` != ? LIMIT 1");
            $uChk->execute([$username, $user['id']]);
            if ($uChk->fetch()) {
                setFlash('error', 'Bu username allaqachon olingan, boshqa username tanlang.');
                redirect($self . '?view=profile');
            }
        }
        if ($isAdminUser) {
            $role = 'admin';
        } elseif (!in_array($role, ['worker', 'farmer', 'employer', 'tech', 'logistics'], true)) {
            $role = 'worker';
        }

        // Zamonaviy avatar yuklash
        $avatarPath = $user['avatar_path'] ?? null;
        if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed, true) && $_FILES['avatar']['size'] <= 5 * 1024 * 1024) {
                $up = getUploadBaseDir();
                $fileName = 'av_' . $user['id'] . '_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['avatar']['tmp_name'], $up['fs'] . '/' . $fileName);
                $avatarPath = $up['web'] . '/' . $fileName;
            }
        }

        $pdo->prepare("
            UPDATE `users`
            SET `name` = ?, `username` = ?, `role` = ?, `is_onboarded` = 1, `avatar_path` = ?, `bio` = ?, `region` = ?, `district` = ?, `telegram` = ?
            WHERE `id` = ?
        ")->execute([
            $name,
            $username !== '' ? $username : null,
            $role,
            $avatarPath,
            $bio !== '' ? $bio : null,
            $region !== '' ? $region : null,
            $district !== '' ? $district : null,
            $telegram !== '' ? $telegram : null,
            $user['id'],
        ]);

        if (in_array($role, ['worker', 'tech', 'logistics'], true)) {
            $profession = trim($_POST['profession'] ?? '');
            $equipmentType = trim($_POST['equipment_type'] ?? '');
            $expectedSalary = parseSalaryAmount($_POST['expected_salary'] ?? '');
            $paymentType = $_POST['payment_type'] ?? 'monthly';
            $isAvailable = isset($_POST['is_available']) ? 1 : 0;

            $pdo->prepare("
                INSERT INTO `worker_profiles` (`user_id`, `profession`, `description`, `region`, `district`, `expected_salary`, `payment_type`, `equipment_type`, `photo_path`, `is_available`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    `profession` = VALUES(`profession`),
                    `description` = VALUES(`description`),
                    `region` = VALUES(`region`),
                    `district` = VALUES(`district`),
                    `expected_salary` = VALUES(`expected_salary`),
                    `payment_type` = VALUES(`payment_type`),
                    `equipment_type` = VALUES(`equipment_type`),
                    `photo_path` = VALUES(`photo_path`),
                    `is_available` = VALUES(`is_available`)
            ")->execute([
                $user['id'],
                $profession !== '' ? $profession : ($role === 'tech' ? 'Texnik xizmat' : ($role === 'logistics' ? 'Yuk tashish xizmati' : 'Mutaxassis')),
                $bio,
                $region !== '' ? $region : null,
                $district !== '' ? $district : null,
                $expectedSalary,
                in_array($paymentType, ['monthly', 'daily', 'piecework'], true) ? $paymentType : 'monthly',
                $equipmentType !== '' ? $equipmentType : null,
                $avatarPath,
                $isAvailable,
            ]);
        } else {
            $companyName = trim($_POST['company_name'] ?? '');
            $neededProf = trim($_POST['needed_profession'] ?? '');
            $landArea = trim($_POST['land_area'] ?? '');
            $cropType = trim($_POST['crop_type'] ?? '');
            $irrigationType = trim($_POST['irrigation_type'] ?? '');

            $pdo->prepare("
                INSERT INTO `employer_profiles` (`user_id`, `company_name`, `needed_profession`, `region`, `district`, `land_area`, `crop_type`, `irrigation_type`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    `company_name` = VALUES(`company_name`),
                    `needed_profession` = VALUES(`needed_profession`),
                    `region` = VALUES(`region`),
                    `district` = VALUES(`district`),
                    `land_area` = VALUES(`land_area`),
                    `crop_type` = VALUES(`crop_type`),
                    `irrigation_type` = VALUES(`irrigation_type`)
            ")->execute([
                $user['id'],
                $companyName !== '' ? $companyName : null,
                $neededProf,
                $region !== '' ? $region : null,
                $district !== '' ? $district : null,
                $landArea !== '' ? $landArea : null,
                $cropType !== '' ? $cropType : null,
                $irrigationType !== '' ? $irrigationType : null,
            ]);
        }

        setFlash('success', 'Profil ma’lumotlaringiz muvaffaqiyatli saqlandi!');
        redirect($self . '?view=profile');
    }

    // --- B. VAKANSIYA JOYLASH (Tadbirkor va Dehqon) ---
    if ($action === 'create_vacancy' && in_array($user['role'], ['employer', 'farmer'], true)) {
        $title = trim($_POST['title'] ?? '');
        $profession = trim($_POST['profession'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $region = trim($_POST['region'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $salary = trim($_POST['salary'] ?? '');
        $salaryAmount = parseSalaryAmount($salary);
        $paymentType = $_POST['payment_type'] ?? 'monthly';

        if ($title === '' || $profession === '' || $description === '' || $region === '') {
            setFlash('error', 'Sarlavha, kasb, viloyat va tavsifni to‘ldiring.');
            redirect($self);
        }

        $pdo->prepare("
            INSERT INTO `vacancies` (`employer_id`, `title`, `profession`, `description`, `region`, `district`, `salary`, `salary_amount`, `payment_type`, `status`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
        ")->execute([
            $user['id'], $title, $profession, $description, $region,
            $district !== '' ? $district : null,
            $salary !== '' ? $salary : null,
            $salaryAmount,
            in_array($paymentType, ['monthly', 'daily', 'piecework'], true) ? $paymentType : 'monthly',
        ]);

        setFlash('success', 'Vakansiya muvaffaqiyatli e’lon qilindi!');
        redirect($self);
    }

    if ($action === 'toggle_vacancy' && in_array($user['role'], ['employer', 'farmer'], true)) {
        $vacancyId = (int)($_POST['vacancy_id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM `vacancies` WHERE `id` = ? AND `employer_id` = ?");
        $stmt->execute([$vacancyId, $user['id']]);
        if ($v = $stmt->fetch()) {
            $newStatus = $v['status'] === 'active' ? 'closed' : 'active';
            $pdo->prepare("UPDATE `vacancies` SET `status` = ? WHERE `id` = ?")->execute([$newStatus, $vacancyId]);
            setFlash('success', 'Vakansiya holati yangilandi.');
        }
        redirect($self);
    }

    if ($action === 'update_application' && in_array($user['role'], ['employer', 'farmer'], true)) {
        $appId = (int)($_POST['application_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['accepted', 'rejected'], true)) {
            $stmt = $pdo->prepare("
                SELECT a.id, a.worker_id, v.title FROM `applications` a
                JOIN `vacancies` v ON v.id = a.vacancy_id
                WHERE a.id = ? AND v.employer_id = ?
            ");
            $stmt->execute([$appId, $user['id']]);
            if ($row = $stmt->fetch()) {
                $pdo->prepare("UPDATE `applications` SET `status` = ? WHERE `id` = ?")->execute([$status, $appId]);
                $msgTitle = $status === 'accepted' ? 'Arizangiz qabul qilindi!' : 'Ariza rad etildi';
                $msgBody = "«{$row['title']}» vakansiyasi bo‘yicha {$user['name']} javob berdi.";
                pushNotification($pdo, (int)$row['worker_id'], $msgTitle, $msgBody, $tgBotToken);
                setFlash('success', 'Ariza holati yangilandi va ishchiga bildirishnoma yuborildi.');
            }
        }
        redirect($self);
    }

    // --- C. ISH OLISH / ARIZA YUBORISH (Ishchi, Texnik xizmat va Yuk tashish) ---
    if ($action === 'apply_vacancy' && in_array($user['role'], ['worker', 'tech', 'logistics'], true)) {
        $vacancyId = (int)($_POST['vacancy_id'] ?? 0);
        $stmt = $pdo->prepare("SELECT id, employer_id, title FROM `vacancies` WHERE `id` = ? AND `status` = 'active'");
        $stmt->execute([$vacancyId]);
        if ($v = $stmt->fetch()) {
            $ins = $pdo->prepare("INSERT IGNORE INTO `applications` (`vacancy_id`, `worker_id`, `status`) VALUES (?, ?, 'pending')");
            $ins->execute([$vacancyId, $user['id']]);
            if ($ins->rowCount() > 0) {
                pushNotification(
                    $pdo,
                    (int)$v['employer_id'],
                    'Yangi ish arizasi tushdi!',
                    "{$user['name']} ({$user['phone']}) «{$v['title']}» vakansiyangizga ariza yubordi.",
                    $tgBotToken
                );
            }
            setFlash('success', 'Arizangiz yuborildi! Ish beruvchiga darhol bildirishnoma bordi.');
        } else {
            setFlash('error', 'Bu vakansiya yopilgan yoki topilmadi.');
        }
        redirect($self);
    }

    redirect($self);
}

// =========================================================================
// 5. PROFIL VA EKRAN MA'LUMOTLARINI YUKLASH
// =========================================================================
$role = $isAdminUser ? 'admin' : (in_array($user['role'] ?? '', ['worker', 'farmer', 'employer', 'tech', 'logistics'], true) ? $user['role'] : 'worker');
$isEmployerSide = in_array($role, ['employer', 'farmer'], true);
$view = ($isAdminUser || (($_GET['view'] ?? 'dashboard') === 'profile')) ? 'profile' : 'dashboard';
$flash = getFlash();
$payLabels = ['monthly' => 'Oylik', 'daily' => 'Kunbay', 'piecework' => 'Ishbay'];

$wpStmt = $pdo->prepare("SELECT * FROM `worker_profiles` WHERE `user_id` = ?");
$wpStmt->execute([$user['id']]);
$workerProfile = $wpStmt->fetch() ?: [];

$epStmt = $pdo->prepare("SELECT * FROM `employer_profiles` WHERE `user_id` = ?");
$epStmt->execute([$user['id']]);
$employerProfile = $epStmt->fetch() ?: [];

$avatarSrc = $user['avatar_path'] ?? ($workerProfile['photo_path'] ?? null);
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZaminJob — <?= e(ROLE_LABELS[$role] ?? 'Kabinet') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gh-drop { display: none; }
        .gh-drop.open { display: block; animation: popIn .18s ease-out both; }
        @keyframes popIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: none; } }
    </style>
</head>
<body class="bg-[#f7f9f5] text-slate-800 min-h-screen flex flex-col">

<!-- GITHUB / YOUTUBE USLUBIDAGI HEADER -->
<nav class="bg-white/95 backdrop-blur border-b border-emerald-100 sticky top-0 z-40">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
        <div class="flex items-center gap-6">
            <a href="/" class="flex items-center gap-2">
                <svg width="136" height="32" viewBox="0 0 206 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 10H31.5V15.6L11.4 33.4H32V39H0.5V33.4L20.6 15.6H1V10Z" fill="#102C20"/>
                    <path d="M5.5 29.8C8.6 21.8 17.4 17.5 29.2 18.2C25.4 26.2 17.8 31.2 5.5 29.8Z" fill="#15803D"/>
                    <path d="M7.5 28.6C13.5 24.4 20.2 21.4 26.8 19.6" stroke="#F7F9F5" stroke-width="1.4" stroke-linecap="round"/>
                    <text x="33.5" y="38.5" fill="#102C20" font-family="system-ui, sans-serif" font-weight="800" font-size="34" letter-spacing="-1.3">aminJob</text>
                </svg>
            </a>
            <div class="hidden sm:flex items-center gap-1 bg-slate-100 p-1 rounded-full text-xs font-bold">
                <?php if ($isAdminUser): ?>
                    <a href="/admin" class="px-3.5 py-1.5 rounded-full transition text-slate-600 hover:text-slate-900">Admin Panel</a>
                <?php else: ?>
                    <a href="<?= e($self) ?>" class="px-3.5 py-1.5 rounded-full transition <?= $view === 'dashboard' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' ?>">
                        <?= $isEmployerSide ? 'Vakansiyalar va Ishchilar' : 'Vakansiyalar' ?>
                    </a>
                <?php endif; ?>
                <a href="<?= e($self) ?>?view=profile" class="px-3.5 py-1.5 rounded-full transition <?= $view === 'profile' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' ?>">
                    Profil va Sozlamalar
                </a>
            </div>
        </div>

        <div class="flex items-center gap-3 relative">
            <span class="hidden md:inline-block text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                <?= e(ROLE_LABELS[$role] ?? 'Foydalanuvchi') ?>
            </span>

            <!-- JONLI QONGIROQCHA -->
            <button type="button" id="bellBtn" class="relative w-10 h-10 rounded-full border border-slate-200 bg-white hover:border-emerald-600 flex items-center justify-center text-slate-700 transition" aria-label="Bildirishnomalar">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                <span id="bellDot" class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[10px] font-extrabold leading-[18px] text-center">0</span>
            </button>

            <!-- DUMALOQ PROFIL RASMI -->
            <button type="button" id="avatarBtn" class="w-10 h-10 rounded-full overflow-hidden border-2 border-emerald-600 bg-[#102c20] text-white font-bold text-sm flex items-center justify-center hover:scale-105 transition" aria-label="Profil">
                <?php if (photoExists($avatarSrc)): ?>
                    <img src="<?= e($avatarSrc) ?>" alt="Profil" class="w-full h-full object-cover">
                <?php else: ?>
                    <?= e(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?>
                <?php endif; ?>
            </button>

            <!-- QONGIROQCHA DROPDOWN -->
            <div id="bellDrop" class="gh-drop absolute right-0 top-12 w-80 bg-white border border-slate-200 rounded-2xl shadow-xl p-3 z-50">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                    <span class="font-bold text-sm text-slate-900">Bildirishnomalar</span>
                    <span class="text-[11px] text-emerald-700 font-semibold">Jonli</span>
                </div>
                <div id="bellList" class="space-y-2 max-h-64 overflow-y-auto text-xs text-slate-600">
                    <div class="p-2.5 bg-slate-50 rounded-xl">Yuklanmoqda...</div>
                </div>
            </div>

            <!-- PROFIL DROPDOWN -->
            <div id="avatarDrop" class="gh-drop absolute right-0 top-12 w-64 bg-white border border-slate-200 rounded-2xl shadow-xl p-2 z-50">
                <div class="px-3 py-2.5 border-b border-slate-100 mb-1">
                    <div class="font-bold text-sm text-slate-900 truncate"><?= e($user['name']) ?></div>
                    <div class="text-xs text-slate-500 truncate"><?= !empty($user['username']) ? '@' . e($user['username']) : e($user['phone']) ?></div>
                </div>
                <a href="<?= e($self) ?>?view=profile" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Profil va sozlamalar</a>
                <a href="<?= e($self) ?>" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Ishlar va katalog</a>
                <a href="/" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Bosh sahifa</a>
                <a href="<?= e($self) ?>?logout=1" class="block px-3 py-2 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50">Chiqish</a>
            </div>
        </div>
    </div>
</nav>

<main class="flex-1 max-w-6xl w-full mx-auto px-4 py-6">
    <?php if ($flash): ?>
        <div class="mb-5 px-4 py-3 rounded-xl text-sm <?= $flash['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?php if ($view === 'profile'): ?>
        <!-- GITHUB USLUBIDAGI PROFIL VA SOZLAMALAR SAHIFASI -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 h-fit text-center">
                <div class="relative inline-block group cursor-pointer" onclick="document.getElementById('avatarFileInput').click()">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-emerald-100 bg-[#102c20] text-white flex items-center justify-center text-4xl font-extrabold mx-auto shadow-md">
                        <?php if (photoExists($avatarSrc)): ?>
                            <img id="avatarPreviewImg" src="<?= e($avatarSrc) ?>" alt="Avatar" class="w-full h-full object-cover">
                        <?php else: ?>
                            <img id="avatarPreviewImg" src="" alt="Avatar" class="w-full h-full object-cover hidden">
                            <span id="avatarFallbackLetter"><?= e(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="absolute bottom-1 right-1 w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-lg border-2 border-white group-hover:scale-110 transition">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                    </div>
                </div>

                <h2 class="font-extrabold text-xl text-slate-900 mt-4"><?= e($user['name']) ?></h2>
                <div class="text-sm font-semibold text-emerald-700"><?= !empty($user['username']) ? '@' . e($user['username']) : e($user['phone']) ?></div>
                <div class="mt-2 inline-block text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <?= e(ROLE_LABELS[$role] ?? '') ?>
                </div>
                <?php if (!empty($user['region'])): ?>
                    <div class="text-xs text-slate-500 mt-3"><?= e($user['region']) ?><?= !empty($user['district']) ? ', ' . e($user['district']) : '' ?></div>
                <?php endif; ?>
                <?php if (!empty($user['bio'])): ?>
                    <p class="text-xs text-slate-600 mt-3 leading-relaxed"><?= e($user['bio']) ?></p>
                <?php endif; ?>

                <a href="<?= e($self) ?>" class="mt-5 inline-flex items-center justify-center w-full py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition">
                    Ishlar va katalogga o‘tish
                </a>
            </div>

            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 sm:p-7">
                <h1 class="text-xl font-extrabold text-slate-900 mb-1">Profil sozlamalari</h1>
                <p class="text-xs text-slate-500 mb-6">Ma’lumotlaringizni to‘ldiring — mijozlar va hamkorlar sizni tezroq topadi.</p>

                <form action="<?= e($self) ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="action" value="save_profile">
                    <input type="file" name="avatar" id="avatarFileInput" accept="image/*" class="hidden">

                    <!-- ZAMONAVIY RASM YUKLASH DROPZONE -->
                    <div onclick="document.getElementById('avatarFileInput').click()" class="cursor-pointer border-2 border-dashed border-emerald-200 hover:border-emerald-500 bg-emerald-50/40 rounded-2xl p-4 flex items-center justify-between gap-4 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">Profil rasmini yuklash yoki o‘zgartirish</div>
                                <div id="avatarFileLabel" class="text-xs text-slate-500">JPG, PNG yoki WEBP (5 MB gacha) — bosib tanlang</div>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-3.5 py-2 rounded-xl bg-white border border-emerald-200 text-emerald-700 shrink-0">Rasm tanlash</span>
                    </div>

                    <!-- SOHA TANLASH -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Sohangiz</label>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                            <?php foreach (['worker' => 'Ishchi', 'farmer' => 'Dehqon', 'employer' => 'Tadbirkor', 'tech' => 'Texnik xizmat', 'logistics' => 'Yuk tashish'] as $rk => $rl): ?>
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="<?= e($rk) ?>" class="peer hidden" onchange="toggleRoleBlocks('<?= e($rk) ?>')" <?= $role === $rk ? 'checked' : '' ?>>
                                    <div class="text-center py-2.5 px-2 rounded-xl border border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-800 font-bold text-xs transition">
                                        <?= e($rl) ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ism va familiya</label>
                            <input type="text" name="name" value="<?= e($user['name']) ?>" required class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Username (avtomatik tekshiriladi)</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 text-slate-400 font-bold text-sm">@</span>
                                <input type="text" name="username" id="profUsername" value="<?= e($user['username'] ?? '') ?>" pattern="[a-zA-Z0-9_]{3,40}" class="w-full text-sm border border-slate-300 rounded-xl pl-8 pr-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                            </div>
                            <span id="profUsernameStatus" class="hidden text-xs font-bold mt-1 block"></span>
                        </div>
                    </div>

                    <!-- VILOYAT -> TUMAN / SHAHAR BOG'LIQ RO'YXAT -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Viloyat</label>
                            <select name="region" data-region-select data-target="profDistrict" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white focus:outline-none focus:border-emerald-600">
                                <option value="">Viloyatni tanlang</option>
                                <?php foreach (UZ_LOCATIONS as $reg => $dists): ?>
                                    <option value="<?= e($reg) ?>" <?= ($user['region'] ?? '') === $reg ? 'selected' : '' ?>><?= e($reg) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tuman / Shahar</label>
                            <select name="district" id="profDistrict" data-selected="<?= e($user['district'] ?? '') ?>" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white focus:outline-none focus:border-emerald-600">
                                <option value="">Avval viloyatni tanlang</option>
                            </select>
                        </div>
                    </div>

                    <!-- ISHCHI / TEXNIK XIZMAT / YUK TASHISH MAYDONLARI -->
                    <div id="roleBlockWorker" class="<?= in_array($role, ['worker', 'tech', 'logistics'], true) ? '' : 'hidden' ?> space-y-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kasbingiz / Xizmat turi</label>
                                <input type="text" name="profession" value="<?= e($workerProfile['profession'] ?? '') ?>" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                            <div id="equipField" class="<?= in_array($role, ['tech', 'logistics'], true) ? '' : 'hidden' ?>">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Texnika / Yuk mashina rusumi</label>
                                <input type="text" name="equipment_type" value="<?= e($workerProfile['equipment_type'] ?? '') ?>" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kutilayotgan maosh / narx (so‘m)</label>
                                <input type="text" name="expected_salary" value="<?= !empty($workerProfile['expected_salary']) ? e((string)$workerProfile['expected_salary']) : '' ?>" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">To‘lov turi</label>
                                <select name="payment_type" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                                    <?php foreach ($payLabels as $pk => $pl): ?>
                                        <option value="<?= e($pk) ?>" <?= ($workerProfile['payment_type'] ?? 'monthly') === $pk ? 'selected' : '' ?>><?= e($pl) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                            <input type="checkbox" name="is_available" value="1" <?= ((int)($workerProfile['is_available'] ?? 1)) === 1 ? 'checked' : '' ?> class="rounded text-emerald-600">
                            <span>Hozir ish / buyurtma olishga tayyorman</span>
                        </label>
                    </div>

                    <!-- DEHQON / TADBIRKOR MAYDONLARI -->
                    <div id="roleBlockEmployer" class="<?= in_array($role, ['farmer', 'employer'], true) ? '' : 'hidden' ?> space-y-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Xo‘jalik / Kompaniya nomi</label>
                                <input type="text" name="company_name" value="<?= e($employerProfile['company_name'] ?? '') ?>" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kerakli kasb / mutaxassis</label>
                                <input type="text" name="needed_profession" value="<?= e($employerProfile['needed_profession'] ?? '') ?>" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                            </div>
                        </div>
                        <div id="farmerFields" class="<?= $role === 'farmer' ? '' : 'hidden' ?> grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Yer maydoni (gektar)</label>
                                <input type="text" name="land_area" value="<?= e($employerProfile['land_area'] ?? '') ?>" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Asosiy ekin turi</label>
                                <input type="text" name="crop_type" value="<?= e($employerProfile['crop_type'] ?? '') ?>" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Sug‘orish turi</label>
                                <input type="text" name="irrigation_type" value="<?= e($employerProfile['irrigation_type'] ?? '') ?>" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">O‘zingiz / xo‘jaligingiz haqida</label>
                        <textarea name="bio" rows="3" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600"><?= e($user['bio'] ?? ($workerProfile['description'] ?? '')) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Telegram (bildirishnomalar uchun @username yoki Chat ID)</label>
                        <input type="text" name="telegram" value="<?= e($user['telegram'] ?? '') ?>" class="w-full text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition shadow-sm">
                        O‘zgarishlarni saqlash
                    </button>
                </form>
            </div>
        </div>

    <?php elseif (!$isEmployerSide): ?>
        <!-- ISHCHI VA TEXNIK XIZMAT KABINETI: VAKANSIYALAR + AI QIDIRUV -->
        <?php
        $fQuery = trim($_GET['q'] ?? '');
        $fRegion = trim($_GET['region'] ?? '');
        $fDistrict = trim($_GET['district'] ?? '');
        $fPayType = trim($_GET['payment_type'] ?? '');
        $fMinSal = parseSalaryAmount($_GET['min_salary'] ?? '');
        $fMaxSal = parseSalaryAmount($_GET['max_salary'] ?? '');
        $fAi = !isset($_GET['filter']) || (($_GET['ai'] ?? '0') === '1');

        $sql = "
            SELECT v.*, u.name AS employer_name, ep.company_name
            FROM `vacancies` v
            JOIN `users` u ON u.id = v.employer_id
            LEFT JOIN `employer_profiles` ep ON ep.user_id = u.id
            WHERE v.status = 'active'
        ";
        $params = [];
        if ($fRegion !== '') { $sql .= " AND v.region = ?"; $params[] = $fRegion; }
        if ($fDistrict !== '') { $sql .= " AND v.district = ?"; $params[] = $fDistrict; }
        if (in_array($fPayType, ['monthly', 'daily', 'piecework'], true)) { $sql .= " AND v.payment_type = ?"; $params[] = $fPayType; }
        if ($fMinSal !== null) { $sql .= " AND (v.salary_amount IS NOT NULL AND v.salary_amount >= ?)"; $params[] = $fMinSal; }
        if ($fMaxSal !== null) { $sql .= " AND (v.salary_amount IS NOT NULL AND v.salary_amount <= ?)"; $params[] = $fMaxSal; }
        $sql .= " ORDER BY v.id DESC";

        $vacStmt = $pdo->prepare($sql);
        $vacStmt->execute($params);
        $rawVacancies = $vacStmt->fetchAll();

        $aiSearch = smartExpandProfessions($fQuery, $fAi);
        $profileAi = smartExpandProfessions($workerProfile['profession'] ?? '', true);
        $vacancies = [];
        foreach ($rawVacancies as $v) {
            $hay = ($v['profession'] ?? '') . ' ' . ($v['title'] ?? '') . ' ' . ($v['description'] ?? '') . ' ' . ($v['district'] ?? '');
            if ($fQuery !== '') {
                $m = scoreMatch($hay, $aiSearch);
                if ($m['score'] === 0) continue;
                $v['_match_score'] = $m['score']; $v['_match_type'] = $m['type'];
            } else {
                $pm = scoreMatch($hay, $profileAi);
                $v['_match_score'] = $pm['score']; $v['_match_type'] = $pm['type'];
            }
            $vacancies[] = $v;
        }
        usort($vacancies, fn($a, $b) => $b['_match_score'] !== $a['_match_score'] ? $b['_match_score'] <=> $a['_match_score'] : ((int)$b['id'] <=> (int)$a['id']));

        $appStmt = $pdo->prepare("SELECT vacancy_id, status FROM `applications` WHERE `worker_id` = ?");
        $appStmt->execute([$user['id']]);
        $appliedMap = [];
        foreach ($appStmt->fetchAll() as $row) $appliedMap[$row['vacancy_id']] = $row['status'];
        ?>
        <div class="space-y-5">
            <form action="<?= e($self) ?>" method="GET" class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3">
                <input type="hidden" name="filter" value="1">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h1 class="text-lg font-extrabold text-slate-900">Vakansiyalarni qidirish</h1>
                    <label class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full cursor-pointer">
                        <input type="checkbox" name="ai" value="1" <?= $fAi ? 'checked' : '' ?> class="rounded text-emerald-600">
                        <span>AI o‘xshash kasblar</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <input type="text" name="q" value="<?= e($fQuery) ?>" placeholder="Kasb yoki kalit so‘z..." class="sm:col-span-1 text-sm border border-slate-300 rounded-xl px-3.5 py-2.5">
                    <select name="region" data-region-select data-target="fltDistrict" class="text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white">
                        <option value="">Barcha viloyatlar</option>
                        <?php foreach (UZ_LOCATIONS as $reg => $dists): ?>
                            <option value="<?= e($reg) ?>" <?= $fRegion === $reg ? 'selected' : '' ?>><?= e($reg) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="district" id="fltDistrict" data-selected="<?= e($fDistrict) ?>" class="text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white">
                        <option value="">Barcha tumanlar</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                    <select name="payment_type" class="text-sm border border-slate-300 rounded-xl px-3 py-2 bg-white">
                        <option value="">To‘lov turi (barchasi)</option>
                        <?php foreach ($payLabels as $pk => $pl): ?>
                            <option value="<?= e($pk) ?>" <?= $fPayType === $pk ? 'selected' : '' ?>><?= e($pl) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="min_salary" value="<?= e($_GET['min_salary'] ?? '') ?>" placeholder="Min maosh (so‘m)" class="text-sm border border-slate-300 rounded-xl px-3 py-2">
                    <input type="text" name="max_salary" value="<?= e($_GET['max_salary'] ?? '') ?>" placeholder="Max maosh (so‘m)" class="text-sm border border-slate-300 rounded-xl px-3 py-2">
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-emerald-600 text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-emerald-700 transition">Qidirish</button>
                        <?php if (isset($_GET['filter'])): ?>
                            <a href="<?= e($self) ?>" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50">Tozalash</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="space-y-3">
                <?php if (count($vacancies) > 0): ?>
                    <?php foreach ($vacancies as $v): ?>
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700"><?= e($v['profession']) ?></span>
                                    <?php if (!empty($v['region'])): ?>
                                        <span class="text-xs font-medium px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700"><?= e($v['region']) ?><?= !empty($v['district']) ? ', ' . e($v['district']) : '' ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($v['salary']) || !empty($v['salary_amount'])): ?>
                                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md"><?= e($v['salary'] ?: formatSom((int)$v['salary_amount'])) ?> (<?= e($payLabels[$v['payment_type']] ?? 'Oylik') ?>)</span>
                                    <?php endif; ?>
                                    <?php if (($v['_match_type'] ?? '') === 'ai'): ?>
                                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-md bg-violet-50 text-violet-700">AI o‘xshash</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="font-bold text-lg text-slate-900"><?= e($v['title']) ?></h3>
                                <p class="text-sm text-slate-600 mt-1"><?= e($v['description']) ?></p>
                                <p class="text-xs text-slate-400 mt-2">Ish beruvchi: <?= e($v['company_name'] ?: $v['employer_name']) ?></p>
                            </div>
                            <div class="shrink-0">
                                <?php if (isset($appliedMap[$v['id']])): ?>
                                    <?php $st = $appliedMap[$v['id']]; ?>
                                    <span class="inline-block px-4 py-2 rounded-xl text-xs font-bold <?= $st === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($st === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800') ?>">
                                        <?= $st === 'accepted' ? 'Qabul qilindingiz' : ($st === 'rejected' ? 'Rad etildi' : 'Ariza yuborilgan') ?>
                                    </span>
                                <?php else: ?>
                                    <form action="<?= e($self) ?>" method="POST">
                                        <input type="hidden" name="action" value="apply_vacancy">
                                        <input type="hidden" name="vacancy_id" value="<?= (int)$v['id'] ?>">
                                        <button type="submit" class="bg-emerald-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl hover:bg-emerald-700 transition">Ish olish</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-sm text-slate-500">Hozircha mos vakansiya topilmadi.</div>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <!-- DEHQON VA TADBIRKOR KABINETI: VAKANSIYA JOYLASH + ISHCHILAR KATALOGI -->
        <?php
        $myVacStmt = $pdo->prepare("SELECT * FROM `vacancies` WHERE `employer_id` = ? ORDER BY `id` DESC");
        $myVacStmt->execute([$user['id']]);
        $myVacancies = $myVacStmt->fetchAll();

        $appStmt = $pdo->prepare("
            SELECT a.*, u.name AS worker_name, u.phone AS worker_phone, u.avatar_path,
                   wp.profession AS worker_profession, wp.description AS worker_description, wp.region AS worker_region
            FROM `applications` a
            JOIN `users` u ON u.id = a.worker_id
            LEFT JOIN `worker_profiles` wp ON wp.user_id = u.id
            WHERE a.vacancy_id = ?
            ORDER BY a.id DESC
        ");

        $wQuery = trim($_GET['wq'] ?? '');
        $wRegion = trim($_GET['w_region'] ?? '');
        $wDistrict = trim($_GET['w_district'] ?? '');
        $wPayType = trim($_GET['w_payment_type'] ?? '');
        $wMaxSal = parseSalaryAmount($_GET['w_max_salary'] ?? '');
        $wAi = !isset($_GET['wfilter']) || (($_GET['w_ai'] ?? '0') === '1');

        $wSql = "
            SELECT u.id, u.name, u.username, u.phone, u.avatar_path, u.role,
                   wp.profession, wp.description, wp.region, wp.district, wp.expected_salary, wp.payment_type
            FROM `users` u
            LEFT JOIN `worker_profiles` wp ON wp.user_id = u.id
            WHERE u.role IN ('worker', 'tech', 'logistics') AND (wp.is_available IS NULL OR wp.is_available = 1)
        ";
        $wParams = [];
        if ($wRegion !== '') { $wSql .= " AND COALESCE(wp.region, u.region) = ?"; $wParams[] = $wRegion; }
        if ($wDistrict !== '') { $wSql .= " AND COALESCE(wp.district, u.district) = ?"; $wParams[] = $wDistrict; }
        if (in_array($wPayType, ['monthly', 'daily', 'piecework'], true)) { $wSql .= " AND wp.payment_type = ?"; $wParams[] = $wPayType; }
        if ($wMaxSal !== null) { $wSql .= " AND (wp.expected_salary IS NULL OR wp.expected_salary <= ?)"; $wParams[] = $wMaxSal; }
        $wSql .= " ORDER BY u.id DESC";

        $wStmt = $pdo->prepare($wSql);
        $wStmt->execute($wParams);
        $rawWorkers = $wStmt->fetchAll();

        $wAiSearch = smartExpandProfessions($wQuery !== '' ? $wQuery : ($employerProfile['needed_profession'] ?? ''), $wAi);
        $matchingWorkers = [];
        foreach ($rawWorkers as $w) {
            $hay = ($w['profession'] ?? '') . ' ' . ($w['name'] ?? '') . ' ' . ($w['description'] ?? '') . ' ' . ($w['district'] ?? '');
            $m = scoreMatch($hay, $wAiSearch);
            if ($wQuery !== '' && $m['score'] === 0) continue;
            $w['_match_score'] = $m['score']; $w['_match_type'] = $m['type'];
            $matchingWorkers[] = $w;
        }
        usort($matchingWorkers, fn($a, $b) => $b['_match_score'] !== $a['_match_score'] ? $b['_match_score'] <=> $a['_match_score'] : ((int)$b['id'] <=> (int)$a['id']));
        ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 h-fit">
                <h2 class="font-extrabold text-lg text-slate-900 mb-1">+ Vakansiya joylash</h2>
                <p class="text-xs text-slate-500 mb-4">E’lon ishchi va texnik xizmatlarga darhol ko‘rinadi</p>
                <form action="<?= e($self) ?>" method="POST" class="space-y-3">
                    <input type="hidden" name="action" value="create_vacancy">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sarlavha</label>
                        <input type="text" name="title" required class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kerakli kasb</label>
                        <input type="text" name="profession" value="<?= e($employerProfile['needed_profession'] ?? '') ?>" required class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Viloyat</label>
                            <select name="region" required data-region-select data-target="vacDistrict" class="w-full text-sm border border-slate-300 rounded-xl px-2.5 py-2 bg-white">
                                <option value="">Tanlang</option>
                                <?php foreach (UZ_LOCATIONS as $reg => $dists): ?>
                                    <option value="<?= e($reg) ?>" <?= ($user['region'] ?? '') === $reg ? 'selected' : '' ?>><?= e($reg) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tuman / Shahar</label>
                            <select name="district" id="vacDistrict" data-selected="<?= e($user['district'] ?? '') ?>" class="w-full text-sm border border-slate-300 rounded-xl px-2.5 py-2 bg-white">
                                <option value="">Tanlang</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Maosh (so‘m)</label>
                            <input type="text" name="salary" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">To‘lov turi</label>
                            <select name="payment_type" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2 bg-white">
                                <?php foreach ($payLabels as $pk => $pl): ?>
                                    <option value="<?= e($pk) ?>"><?= e($pl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ish sharoitlari</label>
                        <textarea name="description" rows="3" required class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 text-white text-sm font-bold py-2.5 rounded-xl hover:bg-emerald-700 transition">Vakansiyani e’lon qilish</button>
                </form>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="space-y-3">
                    <h2 class="text-lg font-extrabold text-slate-900">Mening vakansiyalarim</h2>
                    <?php if (count($myVacancies) > 0): ?>
                        <?php foreach ($myVacancies as $v): ?>
                            <?php $appStmt->execute([$v['id']]); $apps = $appStmt->fetchAll(); ?>
                            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-bold text-base text-slate-900"><?= e($v['title']) ?></h3>
                                            <span class="text-xs px-2 py-0.5 rounded-full <?= $v['status'] === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' ?>"><?= $v['status'] === 'active' ? 'Faol' : 'Yopilgan' ?></span>
                                            <?php if (!empty($v['region'])): ?><span class="text-xs text-slate-500">• <?= e($v['region']) ?><?= !empty($v['district']) ? ', ' . e($v['district']) : '' ?></span><?php endif; ?>
                                        </div>
                                        <p class="text-xs text-slate-600 mt-1"><?= e($v['description']) ?></p>
                                    </div>
                                    <form action="<?= e($self) ?>" method="POST">
                                        <input type="hidden" name="action" value="toggle_vacancy">
                                        <input type="hidden" name="vacancy_id" value="<?= (int)$v['id'] ?>">
                                        <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-50"><?= $v['status'] === 'active' ? 'Yopish' : 'Ochish' ?></button>
                                    </form>
                                </div>
                                <div class="mt-3 pt-3 border-t border-slate-100">
                                    <div class="text-[11px] font-bold uppercase text-slate-400 mb-2">Arizalar (<?= count($apps) ?>)</div>
                                    <?php foreach ($apps as $app): ?>
                                        <div class="flex flex-wrap items-center justify-between gap-2 bg-slate-50 p-3 rounded-xl mb-2">
                                            <div>
                                                <div class="font-bold text-xs text-slate-900"><?= e($app['worker_name']) ?> — <span class="text-emerald-700"><?= e($app['worker_profession'] ?: 'Mutaxassis') ?></span></div>
                                                <div class="text-xs text-blue-600 font-semibold"><?= e($app['worker_phone']) ?></div>
                                            </div>
                                            <div class="flex gap-2">
                                                <?php if ($app['status'] === 'pending'): ?>
                                                    <form action="<?= e($self) ?>" method="POST"><input type="hidden" name="action" value="update_application"><input type="hidden" name="application_id" value="<?= (int)$app['id'] ?>"><input type="hidden" name="status" value="accepted"><button type="submit" class="bg-emerald-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg">Qabul qilish</button></form>
                                                    <form action="<?= e($self) ?>" method="POST"><input type="hidden" name="action" value="update_application"><input type="hidden" name="application_id" value="<?= (int)$app['id'] ?>"><input type="hidden" name="status" value="rejected"><button type="submit" class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1.5 rounded-lg">Rad etish</button></form>
                                                <?php else: ?>
                                                    <span class="text-xs font-bold px-3 py-1 rounded-lg <?= $app['status'] === 'accepted' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>"><?= $app['status'] === 'accepted' ? 'Qabul qilingan' : 'Rad etilgan' ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (count($apps) === 0): ?><p class="text-xs text-slate-400">Hali ariza kelmagan.</p><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 text-xs text-slate-500">Hali vakansiya joylamagansiz.</div>
                    <?php endif; ?>
                </div>

                <!-- ISHCHILAR VA TEXNIK XIZMATLAR KATALOGI -->
                <div class="space-y-3" id="workers-catalog">
                    <form action="<?= e($self) ?>#workers-catalog" method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 space-y-3">
                        <input type="hidden" name="wfilter" value="1">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-base font-extrabold text-slate-900">Ishchilar va texnik xizmatlar katalogi</h2>
                            <label class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full cursor-pointer">
                                <input type="checkbox" name="w_ai" value="1" <?= $wAi ? 'checked' : '' ?> class="rounded text-emerald-600">
                                <span>AI o‘xshash kasblar</span>
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <input type="text" name="wq" value="<?= e($wQuery) ?>" placeholder="Kasb yoki ism..." class="text-sm border border-slate-300 rounded-xl px-3 py-2">
                            <select name="w_region" data-region-select data-target="wFltDistrict" class="text-sm border border-slate-300 rounded-xl px-3 py-2 bg-white">
                                <option value="">Barcha viloyatlar</option>
                                <?php foreach (UZ_LOCATIONS as $reg => $dists): ?>
                                    <option value="<?= e($reg) ?>" <?= $wRegion === $reg ? 'selected' : '' ?>><?= e($reg) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="w_district" id="wFltDistrict" data-selected="<?= e($wDistrict) ?>" class="text-sm border border-slate-300 rounded-xl px-3 py-2 bg-white">
                                <option value="">Barcha tumanlar</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500">Topildi: <strong><?= count($matchingWorkers) ?> ta</strong></span>
                            <button type="submit" class="bg-slate-900 text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-slate-800 transition">Filtrlash</button>
                        </div>
                    </form>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach ($matchingWorkers as $w): ?>
                            <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-start gap-3">
                                <?php if (photoExists($w['avatar_path'] ?? null)): ?>
                                    <img src="<?= e($w['avatar_path']) ?>" class="w-12 h-12 rounded-full object-cover shrink-0 border border-slate-200">
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold shrink-0"><?= e(mb_strtoupper(mb_substr($w['name'], 0, 1))) ?></div>
                                <?php endif; ?>
                                <div class="min-w-0">
                                    <div class="font-bold text-sm text-slate-900"><?= e($w['name']) ?></div>
                                    <div class="text-xs font-bold text-emerald-700"><?= e($w['profession'] ?: (ROLE_LABELS[$w['role']] ?? 'Mutaxassis')) ?></div>
                                    <?php if (!empty($w['region'])): ?><div class="text-xs text-slate-500"><?= e($w['region']) ?><?= !empty($w['district']) ? ', ' . e($w['district']) : '' ?></div><?php endif; ?>
                                    <div class="text-xs font-semibold text-blue-600 mt-1"><?= e($w['phone']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
    // 1. O'zbekiston 14 viloyat -> Tuman/Shahar bog'liq ro'yxati
    const UZ_LOCATIONS = <?= json_encode(UZ_LOCATIONS, JSON_UNESCAPED_UNICODE) ?>;

    function syncDistricts(regionSelect) {
        const targetId = regionSelect.dataset.target;
        const distSelect = document.getElementById(targetId);
        if (!distSelect) return;
        const reg = regionSelect.value;
        const selected = distSelect.dataset.selected || '';
        const list = UZ_LOCATIONS[reg] || [];
        distSelect.innerHTML = `<option value="">${list.length ? 'Tuman / Shaharni tanlang' : 'Barcha tumanlar'}</option>`;
        list.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d;
            opt.textContent = d;
            if (d === selected) opt.selected = true;
            distSelect.appendChild(opt);
        });
        distSelect.dataset.selected = '';
    }
    document.querySelectorAll('[data-region-select]').forEach(sel => {
        syncDistricts(sel);
        sel.addEventListener('change', () => syncDistricts(sel));
    });

    // 2. Zamonaviy avatar jonli ko'rinishi (Choose File o'rniga)
    const avInput = document.getElementById('avatarFileInput');
    if (avInput) {
        avInput.addEventListener('change', () => {
            const file = avInput.files && avInput.files[0];
            if (!file) return;
            const label = document.getElementById('avatarFileLabel');
            if (label) label.textContent = file.name + ' tanlandi — saqlash tugmasini bosing';
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.getElementById('avatarPreviewImg');
                const fallback = document.getElementById('avatarFallbackLetter');
                if (img) { img.src = e.target.result; img.classList.remove('hidden'); }
                if (fallback) fallback.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });
    }

    // 3. Profil sozlamalarida soha bloklarini almashtirish
    function toggleRoleBlocks(role) {
        const wBlock = document.getElementById('roleBlockWorker');
        const eBlock = document.getElementById('roleBlockEmployer');
        const fFields = document.getElementById('farmerFields');
        const eqField = document.getElementById('equipField');
        if (!wBlock || !eBlock) return;
        if (role === 'worker' || role === 'tech' || role === 'logistics') {
            wBlock.classList.remove('hidden');
            eBlock.classList.add('hidden');
            if (eqField) eqField.classList.toggle('hidden', role !== 'tech' && role !== 'logistics');
        } else {
            eBlock.classList.remove('hidden');
            wBlock.classList.add('hidden');
            if (fFields) fFields.classList.toggle('hidden', role !== 'farmer');
        }
    }

    // 4. Qongiroqcha va Dumaloq Profil menyusi + Jonli 8 soniyalik polling
    const bellBtn = document.getElementById('bellBtn');
    const bellDrop = document.getElementById('bellDrop');
    const bellDot = document.getElementById('bellDot');
    const bellList = document.getElementById('bellList');
    const avatarBtn = document.getElementById('avatarBtn');
    const avatarDrop = document.getElementById('avatarDrop');

    const closeDrops = () => {
        if (bellDrop) bellDrop.classList.remove('open');
        if (avatarDrop) avatarDrop.classList.remove('open');
    };
    if (avatarBtn && avatarDrop) {
        avatarBtn.addEventListener('click', e => {
            e.stopPropagation();
            const open = !avatarDrop.classList.contains('open');
            closeDrops();
            if (open) avatarDrop.classList.add('open');
        });
    }
    if (bellBtn && bellDrop) {
        bellBtn.addEventListener('click', e => {
            e.stopPropagation();
            const open = !bellDrop.classList.contains('open');
            closeDrops();
            if (open) {
                bellDrop.classList.add('open');
                fetch('<?= e($self) ?>?api=mark_read', { credentials: 'same-origin' })
                    .then(() => { if (bellDot) bellDot.classList.add('hidden'); })
                    .catch(() => {});
            }
        });
    }
    document.addEventListener('click', closeDrops);

    // 5. Jonli @username band/bo'sh tekshiruvi
    const profUserInp = document.getElementById('profUsername');
    const profUserStat = document.getElementById('profUsernameStatus');
    let profUserTimer = null;
    if (profUserInp && profUserStat) {
        profUserInp.addEventListener('input', () => {
            clearTimeout(profUserTimer);
            const val = profUserInp.value.replace(/^@+/, '').trim();
            if (!val) { profUserStat.classList.add('hidden'); return; }
            profUserStat.classList.remove('hidden', 'text-emerald-700', 'text-red-600');
            profUserStat.classList.add('text-slate-500');
            profUserStat.textContent = 'Tekshirilmoqda...';
            profUserTimer = setTimeout(() => {
                fetch('/api/check-username?u=' + encodeURIComponent(val), { credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(d => {
                        profUserStat.classList.remove('text-slate-500', 'text-emerald-700', 'text-red-600');
                        profUserStat.classList.add(d.status === 'free' ? 'text-emerald-700' : 'text-red-600');
                        profUserStat.textContent = d.message;
                    })
                    .catch(() => {});
            }, 300);
        });
    }

    function pollNotifications() {
        fetch('<?= e($self) ?>?api=notifications', { credentials: 'same-origin' })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                if (!data || !Array.isArray(data.items)) return;
                if (bellDot) {
                    if (data.unread > 0) {
                        bellDot.textContent = data.unread > 9 ? '9+' : String(data.unread);
                        bellDot.classList.remove('hidden');
                    } else {
                        bellDot.classList.add('hidden');
                    }
                }
                if (bellList) {
                    bellList.innerHTML = data.items.length === 0
                        ? '<div class="p-3 bg-slate-50 rounded-xl text-slate-500">Hozircha yangi bildirishnoma yo‘q.</div>'
                        : data.items.map(n =>
                            `<div class="p-2.5 rounded-xl ${n.is_read ? 'bg-slate-50' : 'bg-emerald-50 border border-emerald-100'}">
                                <div class="font-bold text-slate-900">${n.title}</div>
                                <div class="text-slate-600 mt-0.5">${n.body}</div>
                                <div class="text-[10px] text-slate-400 mt-1">${n.created_at}</div>
                            </div>`
                        ).join('');
                }
            })
            .catch(() => {});
    }
    pollNotifications();
    setInterval(pollNotifications, 8000);
</script>
</body>
</html>
