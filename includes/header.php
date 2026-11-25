<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header><div class="in"><a class="logo" href="/">ОБЪЯВЛЕНИЯ</a>
<nav><a href="/">Все объявления</a>
<?php foreach (db()->query('SELECT name, slug FROM categories ORDER BY id') as $c): ?>
<a href="/?cat=<?= e($c['slug']) ?>"><?= e($c['name']) ?></a>
<?php endforeach; ?>
</nav></div></header>
<main>
