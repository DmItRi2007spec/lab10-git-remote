<?php
require __DIR__ . '/config.php';
mb_internal_encoding('UTF-8');
header('Content-Type: text/html; charset=utf-8');
if (DEBUG) { ini_set('display_errors', '1'); error_reporting(E_ALL); }
else       { ini_set('display_errors', '0'); }

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            exit(DEBUG ? 'Ошибка подключения к БД: ' . htmlspecialchars($e->getMessage()) : 'Сайт временно недоступен.');
        }
    }
    return $pdo;
}
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function price($p): string { return number_format((int)$p, 0, '', ' ') . ' ₽'; }
