<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');

$db = ['status' => 'unknown', 'detail' => ''];
try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        getenv('DB_HOST') ?: 'mysql',
        getenv('DB_PORT') ?: '3306',
        getenv('DB_NAME') ?: 'blog',
    );
    $pdo = new PDO($dsn, getenv('DB_USER') ?: 'blog', getenv('DB_PASS') ?: 'blog', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $db['status'] = 'ok';
    $db['detail'] = (string) $pdo->query('SELECT VERSION()')->fetchColumn();
} catch (Throwable $e) {
    $db['status'] = 'error';
    $db['detail'] = $e->getMessage();
}

$smartyInstalled = is_file(__DIR__ . '/../vendor/smarty/smarty/src/Smarty.php');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Env check</title>
</head>
<body>
<h1>Environment check</h1>
<ul>
    <li>PHP: <?= htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') ?></li>
    <li>MySQL: <?= htmlspecialchars($db['status'] . ' — ' . $db['detail'], ENT_QUOTES, 'UTF-8') ?></li>
    <li>Smarty installed: <?= $smartyInstalled ? 'yes' : 'no (run composer install)' ?></li>
</ul>
</body>
</html>
