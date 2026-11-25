<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================================================
// 1. MA'LUMOTLAR BAZASI (MySQL — Laravel 7 .env sozlamalari bilan avtomatik)
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
$dbName = $env['DB_DATABASE'] ?? 'laravel';
$dbUser = $env['DB_USERNAME'] ?? 'root';
$dbPass = $env['DB_PASSWORD'] ?? '';

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
        . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
        . '<br><small style="color:#7f1d1d">MySQL server ishlayotganini va <code>.env</code> fayldagi DB_HOST/DB_DATABASE/DB_USERNAME/DB_PASSWORD sozlamalarini tekshiring.</small></div>'
    );
}

function ensureColumn(PDO $pdo, string $table, string $column, string $definition): void {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
    $stmt->execute([$column]);
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
    }
}

$pdo->exec("
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NULL UNIQUE,
    `phone` VARCHAR(30) NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(30) DEFAULT NULL,
    `is_onboarded` TINYINT(1) NOT NULL DEFAULT 0,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// Agar Laravel migratsiyasi avval `users` jadvalini yaratgan bo'lsa, kerakli ustunlarni xavfsiz qo'shamiz
ensureColumn($pdo, 'users', 'phone', 'VARCHAR(30) NULL UNIQUE AFTER `name`');
ensureColumn($pdo, 'users', 'role', 'VARCHAR(30) NULL AFTER `password`');
ensureColumn($pdo, 'users', 'is_onboarded', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER `role`');
try {
    $pdo->exec("ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL");
} catch (Throwable $ignore) {
}

$pdo->exec("
CREATE TABLE IF NOT EXISTS `worker_profiles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `profession` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `region` VARCHAR(100) DEFAULT NULL,
    `district` VARCHAR(120) DEFAULT NULL,
    `expected_salary` BIGINT UNSIGNED DEFAULT NULL,
    `payment_type` VARCHAR(30) NOT NULL DEFAULT 'monthly',
    `photo_path` VARCHAR(255) DEFAULT NULL,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_wp_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `employer_profiles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `company_name` VARCHAR(255) DEFAULT NULL,
    `needed_profession` VARCHAR(150) NOT NULL,
    `region` VARCHAR(100) DEFAULT NULL,
    `district` VARCHAR(120) DEFAULT NULL,
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
    INDEX `idx_vac_status_region` (`status`, `region`),
    INDEX `idx_vac_payment_salary` (`payment_type`, `salary_amount`),
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
");

// Mavjud jadvallarga 1-band (Qidiruv va filtrlar) ustunlarini xavfsiz qo'shish
ensureColumn($pdo, 'worker_profiles', 'region', 'VARCHAR(100) NULL AFTER `description`');
ensureColumn($pdo, 'worker_profiles', 'district', 'VARCHAR(120) NULL AFTER `region`');
ensureColumn($pdo, 'worker_profiles', 'expected_salary', 'BIGINT UNSIGNED NULL AFTER `district`');
ensureColumn($pdo, 'worker_profiles', 'payment_type', "VARCHAR(30) NOT NULL DEFAULT 'monthly' AFTER `expected_salary`");

ensureColumn($pdo, 'employer_profiles', 'region', 'VARCHAR(100) NULL AFTER `needed_profession`');
ensureColumn($pdo, 'employer_profiles', 'district', 'VARCHAR(120) NULL AFTER `region`');

ensureColumn($pdo, 'vacancies', 'region', 'VARCHAR(100) NULL AFTER `description`');
ensureColumn($pdo, 'vacancies', 'district', 'VARCHAR(120) NULL AFTER `region`');
ensureColumn($pdo, 'vacancies', 'salary_amount', 'BIGINT UNSIGNED NULL AFTER `salary`');

// =========================================================================
// 2. YORDAMCHI FUNKSIYALAR + AQLLI (AI) QIDIRUV KLUSTERLARI
// =========================================================================
const UZ_REGIONS = [
    'Toshkent shahri',
    'Toshkent viloyati',
    'Samarqand',
    'Farg‘ona',
    'Andijon',
    'Namangan',
    'Buxoro',
    'Qashqadaryo',
    'Surxondaryo',
    'Jizzax',
    'Sirdaryo',
    'Navoiy',
    'Xorazm',
    'Qoraqalpog‘iston',
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
    $t = str_replace(["‘", "’", "`", "'", "ʻ", "ʼ"], "'", $t);
    return $t;
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
    if ($digits === '') {
        return null;
    }
    return (int)$digits;
}

function formatSom(?int $amount): string {
    if (!$amount || $amount <= 0) {
        return '';
    }
    return number_format($amount, 0, '.', ' ') . ' so‘m';
}

/**
 * Aqlli (AI) o'xshash kasblarni kengaytirish:
 * To'g'ridan-to'g'ri kalit so'z + semantik klasterlar + imlo xatolariga chidamli qidiruv.
 */
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

    foreach (AI_PROFESSION_CLUSTERS as $clusterKey => $cluster) {
        $matchedCluster = false;
        foreach ($cluster['terms'] as $term) {
            $nt = normText($term);
            foreach ($exact as $tok) {
                if (mb_strpos($nt, $tok, 0, 'UTF-8') !== false || mb_strpos($tok, $nt, 0, 'UTF-8') !== false) {
                    $matchedCluster = true;
                    break 2;
                }
                // Imlo xatolari uchun (masalan: "traktirchi" -> "traktorchi")
                if (mb_strlen($tok, 'UTF-8') >= 4 && mb_strlen($nt, 'UTF-8') >= 4) {
                    similar_text($tok, $nt, $pct);
                    if ($pct >= 72.0) {
                        $matchedCluster = true;
                        break 2;
                    }
                }
            }
        }
        if ($matchedCluster) {
            $clusters[] = $cluster['label'];
            foreach ($cluster['terms'] as $term) {
                $nt = normText($term);
                if (!in_array($nt, $exact, true)) {
                    $related[] = $nt;
                }
            }
        }
    }

    return [
        'exact' => array_values(array_unique($exact)),
        'related' => array_values(array_unique($related)),
        'clusters' => array_values(array_unique($clusters)),
    ];
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
    // Fuzzy match against words in haystack
    $words = array_filter(preg_split('/[\s,.\-\/]+/u', $h), fn($w) => mb_strlen($w, 'UTF-8') >= 4);
    foreach ($aiInfo['exact'] as $term) {
        if (mb_strlen($term, 'UTF-8') < 4) {
            continue;
        }
        foreach ($words as $w) {
            similar_text($term, $w, $pct);
            if ($pct >= 75.0) {
                return ['score' => 85, 'type' => 'ai'];
            }
        }
    }
    foreach ($aiInfo['related'] as $rel) {
        if (mb_strpos($h, $rel, 0, 'UTF-8') !== false) {
            return ['score' => 70, 'type' => 'ai'];
        }
    }
    return ['score' => 0, 'type' => 'none'];
}

function getUploadBaseDir(): array {
    if (is_dir(__DIR__ . '/public')) {
        $dir = __DIR__ . '/public/uploads';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return ['fs' => $dir, 'web' => 'uploads'];
    }
    $dir = __DIR__ . '/uploads';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    return ['fs' => $dir, 'web' => 'uploads'];
}

function photoExists(?string $photoPath): bool {
    if (empty($photoPath)) {
        return false;
    }
    return is_file(__DIR__ . '/' . $photoPath) || is_file(__DIR__ . '/public/' . $photoPath);
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function currentUser(PDO $pdo): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!$user) {
        unset($_SESSION['user_id']);
        return null;
    }
    return $user;
}

$self = basename($_SERVER['SCRIPT_NAME'] ?? __FILE__);
$user = currentUser($pdo);

// =========================================================================
// 3. LOGOUT (CHIQISH)
// =========================================================================
if (isset($_GET['logout'])) {
    session_destroy();
    redirect($self);
}

// =========================================================================
// 4. BARCHA POST SO'ROVLARINI BOSHQARISH
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- A. RO'YXATDAN O'TISH (REGISTER) ---
    if ($action === 'register') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirmation'] ?? '';

        if ($name === '' || $phone === '' || $password === '') {
            setFlash('error', 'Barcha maydonlarni to‘ldiring.');
            redirect($self . '?tab=register');
        }
        if (strlen($password) < 6) {
            setFlash('error', 'Parol kamida 6 ta belgidan iborat bo‘lishi kerak.');
            redirect($self . '?tab=register');
        }
        if ($password !== $passwordConfirm) {
            setFlash('error', 'Parollar bir-biriga mos kelmadi.');
            redirect($self . '?tab=register');
        }

        $check = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
        $check->execute([$phone]);
        if ($check->fetch()) {
            setFlash('error', 'Bu telefon raqam allaqachon ro‘yxatdan o‘tgan.');
            redirect($self . '?tab=register');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, phone, password, role, is_onboarded) VALUES (?, ?, ?, NULL, 0)");
        $stmt->execute([$name, $phone, $hash]);

        $_SESSION['user_id'] = (int)$pdo->lastInsertId();
        redirect($self);
    }

    // --- B. KIRISH (LOGIN) ---
    if ($action === 'login') {
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        $found = $stmt->fetch();

        if (!$found || !password_verify($password, $found['password'])) {
            setFlash('error', 'Telefon raqam yoki parol noto‘g‘ri.');
            redirect($self . '?tab=login');
        }

        $_SESSION['user_id'] = (int)$found['id'];
        redirect($self);
    }

    if (!$user) {
        redirect($self);
    }

    // --- C. SO'ROVNOMA (ONBOARDING) ---
    if ($action === 'onboarding' && (int)$user['is_onboarded'] === 0) {
        $role = $_POST['role'] ?? '';

        if (!in_array($role, ['worker', 'employer'], true)) {
            setFlash('error', 'Iltimos, Tadbirkor yoki Ishchi ekanligingizni tanlang.');
            redirect($self);
        }

        if ($role === 'worker') {
            $profession = trim($_POST['profession'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $region = trim($_POST['region'] ?? '');
            $district = trim($_POST['district'] ?? '');
            $expectedSalary = parseSalaryAmount($_POST['expected_salary'] ?? '');
            $paymentType = $_POST['payment_type'] ?? 'monthly';

            if ($profession === '' || $description === '' || $region === '') {
                setFlash('error', 'Kasb, hudud (viloyat) va o‘zingiz haqingizdagi ma’lumotni to‘ldiring.');
                redirect($self);
            }

            if (empty($_FILES['photo']['name']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                setFlash('error', 'Iltimos, rasm yuklang.');
                redirect($self);
            }

            $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt, true)) {
                setFlash('error', 'Faqat JPG, PNG yoki WEBP formatdagi rasm yuklash mumkin.');
                redirect($self);
            }

            if ($_FILES['photo']['size'] > 4 * 1024 * 1024) {
                setFlash('error', 'Rasm hajmi 4 MB dan oshmasligi kerak.');
                redirect($self);
            }

            $up = getUploadBaseDir();
            $fileName = 'worker_' . $user['id'] . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], $up['fs'] . '/' . $fileName);
            $photoDbPath = $up['web'] . '/' . $fileName;

            $pdo->beginTransaction();
            $stmt = $pdo->prepare("
                INSERT INTO worker_profiles (user_id, profession, description, region, district, expected_salary, payment_type, photo_path, is_available)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([
                $user['id'],
                $profession,
                $description,
                $region,
                $district !== '' ? $district : null,
                $expectedSalary,
                in_array($paymentType, ['monthly', 'daily', 'piecework'], true) ? $paymentType : 'monthly',
                $photoDbPath,
            ]);

            $upd = $pdo->prepare("UPDATE users SET role = 'worker', is_onboarded = 1 WHERE id = ?");
            $upd->execute([$user['id']]);
            $pdo->commit();

            setFlash('success', 'Profilingiz tayyor! Endi vakansiyalarni qidirib ish olishingiz mumkin.');
            redirect($self);
        } else {
            $neededProfession = trim($_POST['needed_profession'] ?? '');
            $companyName = trim($_POST['company_name'] ?? '');
            $region = trim($_POST['region'] ?? '');
            $district = trim($_POST['district'] ?? '');

            if ($neededProfession === '') {
                setFlash('error', 'Qanaqa kasb egasi kerakligini yozing.');
                redirect($self);
            }

            $pdo->beginTransaction();
            $stmt = $pdo->prepare("
                INSERT INTO employer_profiles (user_id, company_name, needed_profession, region, district)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user['id'],
                $companyName !== '' ? $companyName : null,
                $neededProfession,
                $region !== '' ? $region : null,
                $district !== '' ? $district : null,
            ]);

            $upd = $pdo->prepare("UPDATE users SET role = 'employer', is_onboarded = 1 WHERE id = ?");
            $upd->execute([$user['id']]);
            $pdo->commit();

            setFlash('success', 'Profilingiz sozlandi! Endi vakansiya joylash va ishchilarni filtrlash mumkin.');
            redirect($self);
        }
    }

    // --- D. TADBIRKOR AMALLARI ---
    if ((int)$user['is_onboarded'] === 1 && $user['role'] === 'employer') {
        if ($action === 'create_vacancy') {
            $title = trim($_POST['title'] ?? '');
            $profession = trim($_POST['profession'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $region = trim($_POST['region'] ?? '');
            $district = trim($_POST['district'] ?? '');
            $salary = trim($_POST['salary'] ?? '');
            $salaryAmount = parseSalaryAmount($salary);
            $paymentType = $_POST['payment_type'] ?? 'monthly';

            if ($title === '' || $profession === '' || $description === '' || $region === '') {
                setFlash('error', 'Sarlavha, kasb, hudud (viloyat) va ish tavsifini kiriting.');
                redirect($self);
            }

            $stmt = $pdo->prepare("
                INSERT INTO vacancies (employer_id, title, profession, description, region, district, salary, salary_amount, payment_type, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            $stmt->execute([
                $user['id'],
                $title,
                $profession,
                $description,
                $region,
                $district !== '' ? $district : null,
                $salary !== '' ? $salary : null,
                $salaryAmount,
                in_array($paymentType, ['monthly', 'daily', 'piecework'], true) ? $paymentType : 'monthly',
            ]);

            setFlash('success', 'Vakansiya muvaffaqiyatli joylandi!');
            redirect($self);
        }

        if ($action === 'toggle_vacancy') {
            $vacancyId = (int)($_POST['vacancy_id'] ?? 0);
            $stmt = $pdo->prepare("SELECT * FROM vacancies WHERE id = ? AND employer_id = ?");
            $stmt->execute([$vacancyId, $user['id']]);
            $vacancy = $stmt->fetch();

            if ($vacancy) {
                $newStatus = $vacancy['status'] === 'active' ? 'closed' : 'active';
                $upd = $pdo->prepare("UPDATE vacancies SET status = ? WHERE id = ?");
                $upd->execute([$newStatus, $vacancyId]);
                setFlash('success', 'Vakansiya holati yangilandi.');
            }
            redirect($self);
        }

        if ($action === 'update_application') {
            $appId = (int)($_POST['application_id'] ?? 0);
            $status = $_POST['status'] ?? '';

            if (in_array($status, ['accepted', 'rejected'], true)) {
                $stmt = $pdo->prepare("
                    SELECT a.id FROM applications a
                    JOIN vacancies v ON v.id = a.vacancy_id
                    WHERE a.id = ? AND v.employer_id = ?
                ");
                $stmt->execute([$appId, $user['id']]);
                if ($stmt->fetch()) {
                    $upd = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
                    $upd->execute([$status, $appId]);
                    setFlash('success', 'Ariza holati o‘zgartirildi.');
                }
            }
            redirect($self);
        }
    }

    // --- E. ISHCHI AMALLARI ---
    if ((int)$user['is_onboarded'] === 1 && $user['role'] === 'worker') {
        if ($action === 'apply_vacancy') {
            $vacancyId = (int)($_POST['vacancy_id'] ?? 0);
            $stmt = $pdo->prepare("SELECT id FROM vacancies WHERE id = ? AND status = 'active'");
            $stmt->execute([$vacancyId]);
            if ($stmt->fetch()) {
                $ins = $pdo->prepare("INSERT IGNORE INTO applications (vacancy_id, worker_id, status) VALUES (?, ?, 'pending')");
                $ins->execute([$vacancyId, $user['id']]);
                setFlash('success', 'Arizangiz yuborildi! Tadbirkor tez orada ko‘rib chiqadi.');
            } else {
                setFlash('error', 'Bu vakansiya yopilgan yoki topilmadi.');
            }
            redirect($self);
        }

        if ($action === 'update_worker_profile') {
            $profession = trim($_POST['profession'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $region = trim($_POST['region'] ?? '');
            $district = trim($_POST['district'] ?? '');
            $expectedSalary = parseSalaryAmount($_POST['expected_salary'] ?? '');
            $paymentType = $_POST['payment_type'] ?? 'monthly';
            $isAvailable = isset($_POST['is_available']) ? 1 : 0;

            if ($profession === '' || $description === '') {
                setFlash('error', 'Kasb va tavsif maydonlari bo‘sh bo‘lmasligi kerak.');
                redirect($self);
            }

            $profStmt = $pdo->prepare("SELECT * FROM worker_profiles WHERE user_id = ?");
            $profStmt->execute([$user['id']]);
            $profile = $profStmt->fetch();
            $photoPath = $profile['photo_path'] ?? null;

            if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
                $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, $allowedExt, true) && $_FILES['photo']['size'] <= 4 * 1024 * 1024) {
                    $up = getUploadBaseDir();
                    if ($photoPath && is_file(__DIR__ . '/public/' . $photoPath)) {
                        @unlink(__DIR__ . '/public/' . $photoPath);
                    } elseif ($photoPath && is_file(__DIR__ . '/' . $photoPath)) {
                        @unlink(__DIR__ . '/' . $photoPath);
                    }
                    $fileName = 'worker_' . $user['id'] . '_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['photo']['tmp_name'], $up['fs'] . '/' . $fileName);
                    $photoPath = $up['web'] . '/' . $fileName;
                }
            }

            $upd = $pdo->prepare("
                UPDATE worker_profiles
                SET profession = ?, description = ?, region = ?, district = ?, expected_salary = ?, payment_type = ?, photo_path = ?, is_available = ?, updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ?
            ");
            $upd->execute([
                $profession,
                $description,
                $region !== '' ? $region : null,
                $district !== '' ? $district : null,
                $expectedSalary,
                in_array($paymentType, ['monthly', 'daily', 'piecework'], true) ? $paymentType : 'monthly',
                $photoPath,
                $isAvailable,
                $user['id'],
            ]);

            setFlash('success', 'Profilingiz yangilandi!');
            redirect($self);
        }
    }

    redirect($self);
}

// =========================================================================
// 5. QAYSI EKRAN KO'RSATILISHINI ANIQLASH
// =========================================================================
$screen = 'auth';
if ($user) {
    if ((int)$user['is_onboarded'] === 0) {
        $screen = 'onboarding';
    } elseif ($user['role'] === 'worker') {
        $screen = 'worker';
    } else {
        $screen = 'employer';
    }
}

$flash = getFlash();
$payLabels = ['monthly' => 'Oylik', 'daily' => 'Kunbay', 'piecework' => 'Ishbay'];
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZaminJob — Ish va Vakansiyalar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

<?php if ($screen === 'auth'): ?>
    <?php $tab = ($_GET['tab'] ?? 'login') === 'register' ? 'register' : 'login'; ?>
    <div class="flex-1 flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white font-bold flex items-center justify-center text-lg">Z</div>
                    <div>
                        <div class="font-bold text-lg text-slate-900 leading-tight">ZaminJob</div>
                        <div class="text-xs text-slate-500">Ishchilar va tadbirkorlar platformasi</div>
                    </div>
                </div>
                <a href="/" class="text-xs font-medium text-emerald-700 hover:underline">Bosh sahifa</a>
            </div>

            <div class="grid grid-cols-2 bg-slate-100 p-1 rounded-xl mb-6">
                <a href="<?= e($self) ?>?tab=login"
                   class="text-center py-2 text-sm font-semibold rounded-lg transition <?= $tab === 'login' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800' ?>">
                    Kirish
                </a>
                <a href="<?= e($self) ?>?tab=register"
                   class="text-center py-2 text-sm font-semibold rounded-lg transition <?= $tab === 'register' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800' ?>">
                    Ro‘yxatdan o‘tish
                </a>
            </div>

            <?php if ($flash): ?>
                <div class="mb-4 px-4 py-3 rounded-xl text-sm <?= $flash['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' ?>">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?php if ($tab === 'login'): ?>
                <form action="<?= e($self) ?>" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="login">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Telefon raqam</label>
                        <input type="text" name="phone" placeholder="+998901234567" required
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Parol</label>
                        <input type="password" name="password" placeholder="••••••" required
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                    </div>

                    <button type="submit" class="w-full bg-slate-900 text-white font-semibold py-3 rounded-xl hover:bg-slate-800 transition">
                        Tizimga kirish
                    </button>
                </form>
            <?php else: ?>
                <form action="<?= e($self) ?>" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="register">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Ism va familiyangiz</label>
                        <input type="text" name="name" placeholder="Masalan: Sardor Karimov" required
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Telefon raqam</label>
                        <input type="text" name="phone" placeholder="+998901234567" required
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Parol (kamida 6 ta belgi)</label>
                        <input type="password" name="password" required
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Parolni tasdiqlang</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 text-white font-semibold py-3 rounded-xl hover:bg-emerald-700 transition">
                        Davom etish
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

<?php elseif ($screen === 'onboarding'): ?>
    <div class="flex-1 flex items-center justify-center p-4">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-3 py-1 rounded-full">Qisqa so‘rovnoma</span>
                <a href="<?= e($self) ?>?logout=1" class="text-xs text-red-600 hover:underline">Chiqish</a>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 mb-1">Salom, <?= e($user['name']) ?>!</h1>
            <p class="text-sm text-slate-500 mb-6">Davom etish uchun o‘z yo‘nalishingizni tanlang:</p>

            <?php if ($flash): ?>
                <div class="mb-4 px-4 py-3 rounded-xl text-sm bg-red-50 text-red-700 border border-red-200">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= e($self) ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="onboarding">

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="worker" class="peer hidden" onchange="switchRole('worker')" required>
                        <div class="border-2 border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 rounded-xl p-4 text-center transition">
                            <div class="font-bold text-slate-800 text-base">Ishchi</div>
                            <div class="text-xs text-slate-500 mt-1">Ish olish uchun</div>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="employer" class="peer hidden" onchange="switchRole('employer')" required>
                        <div class="border-2 border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 rounded-xl p-4 text-center transition">
                            <div class="font-bold text-slate-800 text-base">Tadbirkor</div>
                            <div class="text-xs text-slate-500 mt-1">Vakansiya joylash uchun</div>
                        </div>
                    </label>
                </div>

                <div id="worker-fields" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Qaysi kasb egasisiz?</label>
                        <input type="text" name="profession" placeholder="Masalan: Traktorchi, Bog‘bon, Payvandchi..."
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Viloyat</label>
                            <select name="region" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white focus:outline-none focus:border-emerald-600">
                                <option value="">Tanlang...</option>
                                <?php foreach (UZ_REGIONS as $reg): ?>
                                    <option value="<?= e($reg) ?>"><?= e($reg) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tuman / Shahar</label>
                            <input type="text" name="district" placeholder="Masalan: Baxmal tumani" data-optional="1"
                                   class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Kutilayotgan maosh (so‘m)</label>
                            <input type="text" name="expected_salary" placeholder="Masalan: 4 500 000" data-optional="1"
                                   class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">To‘lov turi</label>
                            <select name="payment_type" data-optional="1" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                                <option value="monthly">Oylik</option>
                                <option value="daily">Kunbay</option>
                                <option value="piecework">Ishbay</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">O‘zingiz va tajribangiz haqida</label>
                        <textarea name="description" rows="3" placeholder="Necha yil tajribangiz bor, qaysi texnika yoki ishlarni bilasiz..."
                                  class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Rasm yuklang (Profil yoki ish jarayoni)</label>
                        <input type="file" name="photo" accept="image/*"
                               class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>
                </div>

                <div id="employer-fields" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Hozir qanaqa kasb egasi kerak?</label>
                        <input type="text" name="needed_profession" placeholder="Masalan: Traktorchi, Agronom, Haydovchi..."
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Xo‘jalik yoki kompaniya nomi (ixtiyoriy)</label>
                        <input type="text" name="company_name" placeholder="Masalan: Baraka Agro MCHJ" data-optional="1"
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-blue-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Viloyat</label>
                            <select name="region" data-optional="1" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 bg-white">
                                <option value="">Tanlang...</option>
                                <?php foreach (UZ_REGIONS as $reg): ?>
                                    <option value="<?= e($reg) ?>"><?= e($reg) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tuman / Shahar</label>
                            <input type="text" name="district" placeholder="Masalan: Zomin tumani" data-optional="1"
                                   class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-blue-600">
                        </div>
                    </div>
                </div>

                <button type="submit" id="submit-btn" class="hidden w-full mt-6 bg-slate-900 text-white font-semibold py-3 rounded-xl hover:bg-slate-800 transition">
                    Tasdiqlash va kirish
                </button>
            </form>
        </div>
    </div>

    <script>
        function switchRole(role) {
            const workerFields = document.getElementById('worker-fields');
            const employerFields = document.getElementById('employer-fields');
            const submitBtn = document.getElementById('submit-btn');

            submitBtn.classList.remove('hidden');

            if (role === 'worker') {
                workerFields.classList.remove('hidden');
                employerFields.classList.add('hidden');
                setRequired(workerFields, true);
                setRequired(employerFields, false);
            } else {
                employerFields.classList.remove('hidden');
                workerFields.classList.add('hidden');
                setRequired(employerFields, true);
                setRequired(workerFields, false);
            }
        }

        function setRequired(container, isRequired) {
            container.querySelectorAll('input, textarea, select').forEach(el => {
                if (el.dataset.optional === '1') {
                    el.required = false;
                } else {
                    el.required = isRequired;
                }
            });
        }
    </script>

<?php elseif ($screen === 'worker'): ?>
    <?php
    $profStmt = $pdo->prepare("SELECT * FROM worker_profiles WHERE user_id = ?");
    $profStmt->execute([$user['id']]);
    $profile = $profStmt->fetch();

    // --- 1-BAND: VAKANSIYALAR UCHUN QIDIRUV, FILTRLAR VA AQLLI (AI) MOSLIK ---
    $fQuery = trim($_GET['q'] ?? '');
    $fRegion = trim($_GET['region'] ?? '');
    $fDistrict = trim($_GET['district'] ?? '');
    $fPayType = trim($_GET['payment_type'] ?? '');
    $fMinSal = parseSalaryAmount($_GET['min_salary'] ?? '');
    $fMaxSal = parseSalaryAmount($_GET['max_salary'] ?? '');
    $fAi = !isset($_GET['filter']) || (($_GET['ai'] ?? '0') === '1');

    $sql = "
        SELECT v.*, u.name AS employer_name, ep.company_name
        FROM vacancies v
        JOIN users u ON u.id = v.employer_id
        LEFT JOIN employer_profiles ep ON ep.user_id = u.id
        WHERE v.status = 'active'
    ";
    $params = [];

    if ($fRegion !== '') {
        $sql .= " AND v.region = ?";
        $params[] = $fRegion;
    }
    if ($fDistrict !== '') {
        $sql .= " AND v.district LIKE ?";
        $params[] = '%' . $fDistrict . '%';
    }
    if (in_array($fPayType, ['monthly', 'daily', 'piecework'], true)) {
        $sql .= " AND v.payment_type = ?";
        $params[] = $fPayType;
    }
    if ($fMinSal !== null) {
        $sql .= " AND (v.salary_amount IS NOT NULL AND v.salary_amount >= ?)";
        $params[] = $fMinSal;
    }
    if ($fMaxSal !== null) {
        $sql .= " AND (v.salary_amount IS NOT NULL AND v.salary_amount <= ?)";
        $params[] = $fMaxSal;
    }
    $sql .= " ORDER BY v.id DESC";

    $vacStmt = $pdo->prepare($sql);
    $vacStmt->execute($params);
    $rawVacancies = $vacStmt->fetchAll();

    // AI qidiruv va tartiblash
    $aiSearch = smartExpandProfessions($fQuery, $fAi);
    $profileAi = smartExpandProfessions($profile['profession'] ?? '', true);
    $vacancies = [];

    foreach ($rawVacancies as $v) {
        $hay = ($v['profession'] ?? '') . ' ' . ($v['title'] ?? '') . ' ' . ($v['description'] ?? '') . ' ' . ($v['district'] ?? '');
        if ($fQuery !== '') {
            $m = scoreMatch($hay, $aiSearch);
            if ($m['score'] === 0) {
                continue;
            }
            $v['_match_score'] = $m['score'];
            $v['_match_type'] = $m['type'];
        } else {
            $pm = scoreMatch($hay, $profileAi);
            $v['_match_score'] = $pm['score'];
            $v['_match_type'] = $pm['type'];
        }
        $vacancies[] = $v;
    }

    usort($vacancies, function ($a, $b) {
        if ($b['_match_score'] !== $a['_match_score']) {
            return $b['_match_score'] <=> $a['_match_score'];
        }
        return (int)$b['id'] <=> (int)$a['id'];
    });

    $appStmt = $pdo->prepare("SELECT vacancy_id, status FROM applications WHERE worker_id = ?");
    $appStmt->execute([$user['id']]);
    $appliedMap = [];
    foreach ($appStmt->fetchAll() as $row) {
        $appliedMap[$row['vacancy_id']] = $row['status'];
    }
    ?>
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-bold flex items-center justify-center">Z</div>
                <span class="font-bold text-lg text-slate-900">ZaminJob</span>
            </a>

            <div class="flex items-center gap-4">
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-100 text-emerald-700">
                    Ishchi kabineti
                </span>
                <span class="text-sm font-medium text-slate-700 hidden sm:inline"><?= e($user['name']) ?></span>
                <a href="<?= e($self) ?>?logout=1" class="text-sm text-red-600 hover:underline font-medium">Chiqish</a>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-6xl w-full mx-auto px-4 py-6">
        <?php if ($flash): ?>
            <div class="mb-5 px-4 py-3 rounded-xl text-sm <?= $flash['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 h-fit">
                <div class="flex items-center gap-4 mb-4">
                    <?php if (photoExists($profile['photo_path'] ?? null)): ?>
                        <img src="<?= e($profile['photo_path']) ?>" alt="Profil rasmi" class="w-16 h-16 rounded-full object-cover border border-slate-200">
                    <?php else: ?>
                        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xl">
                            <?= e(mb_substr($user['name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h2 class="font-bold text-lg text-slate-900"><?= e($user['name']) ?></h2>
                        <span class="inline-block text-xs font-semibold bg-emerald-50 text-emerald-700 px-2.5 py-0.5 rounded-md">
                            <?= e($profile['profession']) ?>
                        </span>
                        <?php if (!empty($profile['region'])): ?>
                            <div class="text-xs text-slate-500 mt-1">
                                <?= e($profile['region']) ?><?= !empty($profile['district']) ? ', ' . e($profile['district']) : '' ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <p class="text-sm text-slate-600 mb-5"><?= e($profile['description']) ?></p>

                <hr class="my-4 border-slate-100">

                <form action="<?= e($self) ?>" method="POST" enctype="multipart/form-data" class="space-y-3">
                    <input type="hidden" name="action" value="update_worker_profile">

                    <h3 class="font-semibold text-sm text-slate-800">Profilni yangilash</h3>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Kasbingiz</label>
                        <input type="text" name="profession" value="<?= e($profile['profession']) ?>" required
                               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Viloyat</label>
                            <select name="region" class="w-full text-sm border border-slate-300 rounded-lg px-2.5 py-2 bg-white">
                                <option value="">Tanlang...</option>
                                <?php foreach (UZ_REGIONS as $reg): ?>
                                    <option value="<?= e($reg) ?>" <?= ($profile['region'] ?? '') === $reg ? 'selected' : '' ?>><?= e($reg) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Tuman</label>
                            <input type="text" name="district" value="<?= e($profile['district'] ?? '') ?>" placeholder="Tuman nomi"
                                   class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Kutilayotgan maosh</label>
                            <input type="text" name="expected_salary" value="<?= !empty($profile['expected_salary']) ? e((string)$profile['expected_salary']) : '' ?>" placeholder="4 500 000"
                                   class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">To‘lov turi</label>
                            <select name="payment_type" class="w-full text-sm border border-slate-300 rounded-lg px-2.5 py-2 bg-white">
                                <?php foreach ($payLabels as $pk => $pl): ?>
                                    <option value="<?= e($pk) ?>" <?= ($profile['payment_type'] ?? 'monthly') === $pk ? 'selected' : '' ?>><?= e($pl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">O‘zingiz haqingizda</label>
                        <textarea name="description" rows="3" required
                                  class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2"><?= e($profile['description']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Yangi rasm (ixtiyoriy)</label>
                        <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-500">
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-700 pt-1">
                        <input type="checkbox" name="is_available" value="1" <?= (int)$profile['is_available'] === 1 ? 'checked' : '' ?> class="rounded text-emerald-600">
                        <span>Hozir ishga tayyorman</span>
                    </label>

                    <button type="submit" class="w-full bg-slate-900 text-white text-sm font-medium py-2.5 rounded-xl hover:bg-slate-800 transition">
                        Profilni saqlash
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <!-- QIDIRUV VA FILTR PANELI -->
                <form action="<?= e($self) ?>" method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 space-y-3">
                    <input type="hidden" name="filter" value="1">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h1 class="text-lg font-bold text-slate-900">Vakansiyalarni qidirish</h1>
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full cursor-pointer">
                            <input type="checkbox" name="ai" value="1" <?= $fAi ? 'checked' : '' ?> class="rounded text-emerald-600">
                            <span>AI o‘xshash kasblar bilan</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <input type="text" name="q" value="<?= e($fQuery) ?>" placeholder="Kasb yoki kalit so‘z (masalan: traktorchi, parnik...)"
                               class="sm:col-span-2 text-sm border border-slate-300 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-600">
                        <select name="region" class="text-sm border border-slate-300 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:border-emerald-600">
                            <option value="">Barcha viloyatlar</option>
                            <?php foreach (UZ_REGIONS as $reg): ?>
                                <option value="<?= e($reg) ?>" <?= $fRegion === $reg ? 'selected' : '' ?>><?= e($reg) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                        <input type="text" name="district" value="<?= e($fDistrict) ?>" placeholder="Tuman / shahar"
                               class="text-sm border border-slate-300 rounded-xl px-3 py-2 focus:outline-none focus:border-emerald-600">
                        <select name="payment_type" class="text-sm border border-slate-300 rounded-xl px-3 py-2 bg-white">
                            <option value="">To‘lov turi (barchasi)</option>
                            <?php foreach ($payLabels as $pk => $pl): ?>
                                <option value="<?= e($pk) ?>" <?= $fPayType === $pk ? 'selected' : '' ?>><?= e($pl) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="min_salary" value="<?= e($_GET['min_salary'] ?? '') ?>" placeholder="Min maosh (so‘m)"
                               class="text-sm border border-slate-300 rounded-xl px-3 py-2 focus:outline-none focus:border-emerald-600">
                        <input type="text" name="max_salary" value="<?= e($_GET['max_salary'] ?? '') ?>" placeholder="Max maosh (so‘m)"
                               class="text-sm border border-slate-300 rounded-xl px-3 py-2 focus:outline-none focus:border-emerald-600">
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                        <span class="text-xs text-slate-500">Topildi: <strong class="text-slate-800"><?= count($vacancies) ?> ta</strong> vakansiya</span>
                        <div class="flex items-center gap-2">
                            <?php if (isset($_GET['filter'])): ?>
                                <a href="<?= e($self) ?>" class="text-xs font-semibold text-slate-600 px-3 py-2 rounded-xl border border-slate-300 hover:bg-slate-50">
                                    Tozalash
                                </a>
                            <?php endif; ?>
                            <button type="submit" class="bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-xl hover:bg-emerald-700 transition">
                                Qidirish
                            </button>
                        </div>
                    </div>

                    <?php if ($fQuery !== '' && $fAi && !empty($aiSearch['clusters'])): ?>
                        <div class="pt-2 border-t border-slate-100 text-xs text-slate-600">
                            <span class="font-semibold text-emerald-700">AI kengaytirilgan yo‘nalish:</span>
                            <?= e(implode(', ', $aiSearch['clusters'])) ?>
                            <span class="text-slate-400">
                                (<?= e(implode(', ', array_slice($aiSearch['related'], 0, 6))) ?>)
                            </span>
                        </div>
                    <?php endif; ?>
                </form>

                <?php if (count($vacancies) > 0): ?>
                    <?php foreach ($vacancies as $v): ?>
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700">
                                        <?= e($v['profession']) ?>
                                    </span>
                                    <?php if (!empty($v['region'])): ?>
                                        <span class="text-xs font-medium px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                            <?= e($v['region']) ?><?= !empty($v['district']) ? ', ' . e($v['district']) : '' ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($v['salary']) || !empty($v['salary_amount'])): ?>
                                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md">
                                            <?= e($v['salary'] ?: formatSom((int)$v['salary_amount'])) ?> (<?= e($payLabels[$v['payment_type']] ?? 'Oylik') ?>)
                                        </span>
                                    <?php endif; ?>
                                    <?php if (($v['_match_type'] ?? '') === 'ai'): ?>
                                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-md bg-violet-50 text-violet-700">
                                            AI o‘xshash kasb
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h3 class="font-bold text-lg text-slate-900"><?= e($v['title']) ?></h3>
                                <p class="text-sm text-slate-600 mt-1"><?= e($v['description']) ?></p>
                                <p class="text-xs text-slate-400 mt-2">
                                    Ish beruvchi: <?= e($v['company_name'] ?: $v['employer_name']) ?>
                                </p>
                            </div>

                            <div class="shrink-0">
                                <?php if (isset($appliedMap[$v['id']])): ?>
                                    <?php $st = $appliedMap[$v['id']]; ?>
                                    <span class="inline-block px-4 py-2 rounded-xl text-sm font-semibold
                                        <?= $st === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($st === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800') ?>">
                                        <?= $st === 'accepted' ? 'Qabul qilindingiz' : ($st === 'rejected' ? 'Rad etildi' : 'Ariza yuborilgan') ?>
                                    </span>
                                <?php else: ?>
                                    <form action="<?= e($self) ?>" method="POST">
                                        <input type="hidden" name="action" value="apply_vacancy">
                                        <input type="hidden" name="vacancy_id" value="<?= (int)$v['id'] ?>">
                                        <button type="submit" class="bg-emerald-600 text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-emerald-700 transition">
                                            Ish olish
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500">
                        Filtr bo‘yicha mos vakansiya topilmadi. Filtrlarni yumshatib yoki AI qidiruvni yoqib ko‘ring.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php elseif ($screen === 'employer'): ?>
    <?php
    $empStmt = $pdo->prepare("SELECT * FROM employer_profiles WHERE user_id = ?");
    $empStmt->execute([$user['id']]);
    $employerProfile = $empStmt->fetch();

    $vacStmt = $pdo->prepare("SELECT * FROM vacancies WHERE employer_id = ? ORDER BY id DESC");
    $vacStmt->execute([$user['id']]);
    $myVacancies = $vacStmt->fetchAll();

    $appStmt = $pdo->prepare("
        SELECT a.*, u.name AS worker_name, u.phone AS worker_phone,
               wp.profession AS worker_profession, wp.description AS worker_description,
               wp.region AS worker_region, wp.district AS worker_district, wp.photo_path
        FROM applications a
        JOIN users u ON u.id = a.worker_id
        LEFT JOIN worker_profiles wp ON wp.user_id = u.id
        WHERE a.vacancy_id = ?
        ORDER BY a.id DESC
    ");

    // --- 1-BAND: ISHCHILAR KATALOGI UCHUN QIDIRUV, FILTRLAR VA AQLLI (AI) MOSLIK ---
    $wQuery = trim($_GET['wq'] ?? '');
    $wRegion = trim($_GET['w_region'] ?? '');
    $wDistrict = trim($_GET['w_district'] ?? '');
    $wPayType = trim($_GET['w_payment_type'] ?? '');
    $wMaxSal = parseSalaryAmount($_GET['w_max_salary'] ?? '');
    $wAi = !isset($_GET['wfilter']) || (($_GET['w_ai'] ?? '0') === '1');

    $wSql = "
        SELECT u.id, u.name, u.phone, wp.profession, wp.description, wp.region, wp.district,
               wp.expected_salary, wp.payment_type, wp.photo_path
        FROM users u
        JOIN worker_profiles wp ON wp.user_id = u.id
        WHERE u.role = 'worker' AND wp.is_available = 1
    ";
    $wParams = [];

    if ($wRegion !== '') {
        $wSql .= " AND wp.region = ?";
        $wParams[] = $wRegion;
    }
    if ($wDistrict !== '') {
        $wSql .= " AND wp.district LIKE ?";
        $wParams[] = '%' . $wDistrict . '%';
    }
    if (in_array($wPayType, ['monthly', 'daily', 'piecework'], true)) {
        $wSql .= " AND wp.payment_type = ?";
        $wParams[] = $wPayType;
    }
    if ($wMaxSal !== null) {
        $wSql .= " AND (wp.expected_salary IS NULL OR wp.expected_salary <= ?)";
        $wParams[] = $wMaxSal;
    }
    $wSql .= " ORDER BY u.id DESC";

    $workersStmt = $pdo->prepare($wSql);
    $workersStmt->execute($wParams);
    $rawWorkers = $workersStmt->fetchAll();

    $wAiSearch = smartExpandProfessions($wQuery !== '' ? $wQuery : ($employerProfile['needed_profession'] ?? ''), $wAi);
    $matchingWorkers = [];

    foreach ($rawWorkers as $w) {
        $hay = ($w['profession'] ?? '') . ' ' . ($w['name'] ?? '') . ' ' . ($w['description'] ?? '') . ' ' . ($w['district'] ?? '');
        $m = scoreMatch($hay, $wAiSearch);
        if ($wQuery !== '' && $m['score'] === 0) {
            continue;
        }
        $w['_match_score'] = $m['score'];
        $w['_match_type'] = $m['type'];
        $matchingWorkers[] = $w;
    }

    usort($matchingWorkers, function ($a, $b) {
        if ($b['_match_score'] !== $a['_match_score']) {
            return $b['_match_score'] <=> $a['_match_score'];
        }
        return (int)$b['id'] <=> (int)$a['id'];
    });
    ?>
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-bold flex items-center justify-center">Z</div>
                <span class="font-bold text-lg text-slate-900">ZaminJob</span>
            </a>

            <div class="flex items-center gap-4">
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-blue-100 text-blue-700">
                    Tadbirkor kabineti
                </span>
                <span class="text-sm font-medium text-slate-700 hidden sm:inline"><?= e($user['name']) ?></span>
                <a href="<?= e($self) ?>?logout=1" class="text-sm text-red-600 hover:underline font-medium">Chiqish</a>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-6xl w-full mx-auto px-4 py-6">
        <?php if ($flash): ?>
            <div class="mb-5 px-4 py-3 rounded-xl text-sm <?= $flash['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 h-fit">
                <h2 class="font-bold text-lg text-slate-900 mb-1">+ Vakansiya joylash</h2>
                <p class="text-xs text-slate-500 mb-4">E’loningiz ishchilarga darhol ko‘rinadi</p>

                <form action="<?= e($self) ?>" method="POST" class="space-y-3">
                    <input type="hidden" name="action" value="create_vacancy">

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Sarlavha</label>
                        <input type="text" name="title" placeholder="Masalan: Tajribali traktorchi kerak" required
                               class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Kerakli kasb</label>
                        <input type="text" name="profession" value="<?= e($employerProfile['needed_profession']) ?>" required
                               class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Viloyat</label>
                            <select name="region" required class="w-full text-sm border border-slate-300 rounded-xl px-2.5 py-2 bg-white">
                                <option value="">Tanlang...</option>
                                <?php foreach (UZ_REGIONS as $reg): ?>
                                    <option value="<?= e($reg) ?>" <?= ($employerProfile['region'] ?? '') === $reg ? 'selected' : '' ?>><?= e($reg) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Tuman / Shahar</label>
                            <input type="text" name="district" value="<?= e($employerProfile['district'] ?? '') ?>" placeholder="Masalan: Baxmal"
                                   class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Maosh (so‘m)</label>
                            <input type="text" name="salary" placeholder="5 000 000 so‘m"
                                   class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">To‘lov turi</label>
                            <select name="payment_type" class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2 bg-white">
                                <option value="monthly">Oylik</option>
                                <option value="daily">Kunbay</option>
                                <option value="piecework">Ishbay</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Ish sharoitlari va talablar</label>
                        <textarea name="description" rows="3" placeholder="Ish vaqti, hudud, texnika turi..." required
                                  class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white text-sm font-semibold py-2.5 rounded-xl hover:bg-blue-700 transition">
                        Vakansiyani e’lon qilish
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-slate-900">Mening vakansiyalarim</h2>

                    <?php if (count($myVacancies) > 0): ?>
                        <?php foreach ($myVacancies as $v): ?>
                            <?php
                            $appStmt->execute([$v['id']]);
                            $apps = $appStmt->fetchAll();
                            ?>
                            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-bold text-lg text-slate-900"><?= e($v['title']) ?></h3>
                                            <span class="text-xs px-2 py-0.5 rounded-full <?= $v['status'] === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' ?>">
                                                <?= $v['status'] === 'active' ? 'Faol' : 'Yopilgan' ?>
                                            </span>
                                            <?php if (!empty($v['region'])): ?>
                                                <span class="text-xs text-slate-500">• <?= e($v['region']) ?><?= !empty($v['district']) ? ', ' . e($v['district']) : '' ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($v['salary'])): ?>
                                                <span class="text-xs text-slate-500">• <?= e($v['salary']) ?> (<?= e($payLabels[$v['payment_type']] ?? 'Oylik') ?>)</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-sm text-slate-600 mt-1"><?= e($v['description']) ?></p>
                                    </div>

                                    <form action="<?= e($self) ?>" method="POST">
                                        <input type="hidden" name="action" value="toggle_vacancy">
                                        <input type="hidden" name="vacancy_id" value="<?= (int)$v['id'] ?>">
                                        <button type="submit" class="text-xs font-medium px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-50">
                                            <?= $v['status'] === 'active' ? 'Yopish' : 'Qayta ochish' ?>
                                        </button>
                                    </form>
                                </div>

                                <div class="mt-4 pt-4 border-t border-slate-100">
                                    <h4 class="text-xs font-semibold uppercase text-slate-400 mb-3">
                                        Kelgan arizalar (<?= count($apps) ?>)
                                    </h4>

                                    <?php if (count($apps) > 0): ?>
                                        <?php foreach ($apps as $app): ?>
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 p-3 rounded-xl mb-2">
                                                <div class="flex items-center gap-3">
                                                    <?php if (photoExists($app['photo_path'] ?? null)): ?>
                                                        <img src="<?= e($app['photo_path']) ?>" class="w-11 h-11 rounded-full object-cover shrink-0">
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="font-semibold text-sm text-slate-900">
                                                            <?= e($app['worker_name']) ?> — <span class="text-emerald-700"><?= e($app['worker_profession']) ?></span>
                                                        </div>
                                                        <div class="text-xs text-slate-600"><?= e($app['worker_description']) ?></div>
                                                        <div class="text-xs font-medium text-blue-600 mt-0.5">
                                                            Tel: <?= e($app['worker_phone']) ?>
                                                            <?php if (!empty($app['worker_region'])): ?>
                                                                • <?= e($app['worker_region']) ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-2 shrink-0">
                                                    <?php if ($app['status'] === 'pending'): ?>
                                                        <form action="<?= e($self) ?>" method="POST">
                                                            <input type="hidden" name="action" value="update_application">
                                                            <input type="hidden" name="application_id" value="<?= (int)$app['id'] ?>">
                                                            <input type="hidden" name="status" value="accepted">
                                                            <button type="submit" class="bg-emerald-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-emerald-700">
                                                                Qabul qilish
                                                            </button>
                                                        </form>
                                                        <form action="<?= e($self) ?>" method="POST">
                                                            <input type="hidden" name="action" value="update_application">
                                                            <input type="hidden" name="application_id" value="<?= (int)$app['id'] ?>">
                                                            <input type="hidden" name="status" value="rejected">
                                                            <button type="submit" class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-red-200">
                                                                Rad etish
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-xs font-semibold px-3 py-1 rounded-lg <?= $app['status'] === 'accepted' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>">
                                                            <?= $app['status'] === 'accepted' ? 'Qabul qilingan' : 'Rad etilgan' ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-xs text-slate-400">Hali ariza kelmagan.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 text-sm text-slate-500">
                            Hali vakansiya joylamagansiz. Chap tomondagi formadan e’lon qo‘shing.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ISHCHILAR KATALOGI: QIDIRUV VA FILTRLAR -->
                <div class="space-y-3" id="workers-catalog">
                    <form action="<?= e($self) ?>#workers-catalog" method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 space-y-3">
                        <input type="hidden" name="wfilter" value="1">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h2 class="text-lg font-bold text-slate-900">
                                Ishchilar katalogi va AI qidiruv
                            </h2>
                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-blue-700 bg-blue-50 px-3 py-1 rounded-full cursor-pointer">
                                <input type="checkbox" name="w_ai" value="1" <?= $wAi ? 'checked' : '' ?> class="rounded text-blue-600">
                                <span>AI o‘xshash kasblar</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <input type="text" name="wq" value="<?= e($wQuery) ?>" placeholder="Kasb yoki ism (masalan: traktorchi, svarka...)"
                                   class="sm:col-span-2 text-sm border border-slate-300 rounded-xl px-3.5 py-2 focus:outline-none focus:border-blue-600">
                            <select name="w_region" class="text-sm border border-slate-300 rounded-xl px-3 py-2 bg-white">
                                <option value="">Barcha viloyatlar</option>
                                <?php foreach (UZ_REGIONS as $reg): ?>
                                    <option value="<?= e($reg) ?>" <?= $wRegion === $reg ? 'selected' : '' ?>><?= e($reg) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <input type="text" name="w_district" value="<?= e($wDistrict) ?>" placeholder="Tuman / shahar"
                                   class="text-sm border border-slate-300 rounded-xl px-3 py-2 focus:outline-none focus:border-blue-600">
                            <select name="w_payment_type" class="text-sm border border-slate-300 rounded-xl px-3 py-2 bg-white">
                                <option value="">To‘lov turi (barchasi)</option>
                                <?php foreach ($payLabels as $pk => $pl): ?>
                                    <option value="<?= e($pk) ?>" <?= $wPayType === $pk ? 'selected' : '' ?>><?= e($pl) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="w_max_salary" value="<?= e($_GET['w_max_salary'] ?? '') ?>" placeholder="Max kutilgan maosh (so‘m)"
                                   class="text-sm border border-slate-300 rounded-xl px-3 py-2 focus:outline-none focus:border-blue-600">
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                            <span class="text-xs text-slate-500">Topildi: <strong class="text-slate-800"><?= count($matchingWorkers) ?> ta</strong> ishchi</span>
                            <div class="flex items-center gap-2">
                                <?php if (isset($_GET['wfilter'])): ?>
                                    <a href="<?= e($self) ?>#workers-catalog" class="text-xs font-semibold text-slate-600 px-3 py-2 rounded-xl border border-slate-300 hover:bg-slate-50">
                                        Tozalash
                                    </a>
                                <?php endif; ?>
                                <button type="submit" class="bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-xl hover:bg-blue-700 transition">
                                    Filtrlash
                                </button>
                            </div>
                        </div>

                        <?php if ($wQuery !== '' && $wAi && !empty($wAiSearch['clusters'])): ?>
                            <div class="pt-2 border-t border-slate-100 text-xs text-slate-600">
                                <span class="font-semibold text-blue-700">AI kengaytirilgan yo‘nalish:</span>
                                <?= e(implode(', ', $wAiSearch['clusters'])) ?>
                            </div>
                        <?php endif; ?>
                    </form>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php if (count($matchingWorkers) > 0): ?>
                            <?php foreach ($matchingWorkers as $w): ?>
                                <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-start gap-3">
                                    <?php if (photoExists($w['photo_path'] ?? null)): ?>
                                        <img src="<?= e($w['photo_path']) ?>" class="w-14 h-14 rounded-xl object-cover shrink-0">
                                    <?php else: ?>
                                        <div class="w-14 h-14 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg shrink-0">
                                            <?= e(mb_substr($w['name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <div class="font-bold text-slate-900 text-sm"><?= e($w['name']) ?></div>
                                            <?php if (($w['_match_type'] ?? '') === 'ai'): ?>
                                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-violet-50 text-violet-700">AI o‘xshash</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-xs font-semibold text-emerald-700"><?= e($w['profession']) ?></div>
                                        <?php if (!empty($w['region'])): ?>
                                            <div class="text-xs text-slate-500 mt-0.5">
                                                <?= e($w['region']) ?><?= !empty($w['district']) ? ', ' . e($w['district']) : '' ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($w['expected_salary'])): ?>
                                            <div class="text-xs font-medium text-emerald-700 mt-0.5">
                                                <?= e(formatSom((int)$w['expected_salary'])) ?> (<?= e($payLabels[$w['payment_type']] ?? 'Oylik') ?>)
                                            </div>
                                        <?php endif; ?>
                                        <p class="text-xs text-slate-600 mt-1"><?= e($w['description']) ?></p>
                                        <div class="text-xs font-semibold text-blue-600 mt-2">Tel: <?= e($w['phone']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-sm text-slate-500 col-span-2 bg-white rounded-2xl border border-slate-200 p-6 text-center">
                                Tanlangan filtrlar bo‘yicha bo‘sh ishchilar topilmadi.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php endif; ?>

</body>
</html>
