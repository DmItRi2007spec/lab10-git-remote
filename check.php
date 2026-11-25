<?php
// Страница диагностики после переноса. Откройте: /check.php?key=<CHECK_KEY>. После проверки удалите файл!
require __DIR__ . '/config.php';
header('Content-Type: text/html; charset=utf-8');
if (($_GET['key'] ?? '') !== CHECK_KEY) { http_response_code(403); exit('Доступ запрещён'); }
ini_set('display_errors', '1'); error_reporting(E_ALL);
$rows = [];
$rows[] = ['Версия PHP', PHP_VERSION, version_compare(PHP_VERSION, '7.4', '>=')];
$rows[] = ['Расширение pdo_mysql', extension_loaded('pdo_mysql') ? 'загружено' : 'нет', extension_loaded('pdo_mysql')];
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS,
                   [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $rows[] = ['Подключение к БД ' . DB_NAME, 'успешно', true];
    foreach (['categories' => 5, 'ads' => 10, 'messages' => 3] as $t => $need) {
        $n = (int)$pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        $rows[] = ["Строк в таблице $t (ожидается $need)", $n, $n === $need];
    }
    $miss = 0;
    foreach ($pdo->query('SELECT image FROM ads') as $r) { if (!is_file(__DIR__ . '/img/' . $r['image'])) { $miss++; } }
    $rows[] = ['Изображений объявлений не найдено', $miss, $miss === 0];
    $chk = $pdo->query("SELECT title FROM ads WHERE id = 1")->fetchColumn();
    $rows[] = ['Проверка кириллицы (объявление № 1)', $chk, mb_strpos((string)$chk, 'Велосипед') !== false];
} catch (PDOException $e) {
    $rows[] = ['Подключение к БД', htmlspecialchars($e->getMessage()), false];
}
echo '<!DOCTYPE html><meta charset="utf-8"><title>Диагностика</title><h1>Диагностика после переноса</h1><table border="1" cellpadding="6">';
foreach ($rows as $r) { echo '<tr><td>' . $r[0] . '</td><td>' . $r[1] . '</td><td>' . ($r[2] ? 'OK' : 'ОШИБКА') . '</td></tr>'; }
echo '</table>';
