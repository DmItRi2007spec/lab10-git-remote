
<?php
require __DIR__ . '/db.php';
$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare('SELECT a.*, c.name AS cat FROM ads a JOIN categories c ON c.id = a.category_id WHERE a.id = ?');
$st->execute([$id]);
$ad = $st->fetch();
if (!$ad) { http_response_code(404); readfile(__DIR__ . '/404.html'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $text    = trim($_POST['text'] ?? '');
    if ($name === '' || $contact === '' || $text === '') {
        $error = 'Заполните все поля формы.';
    } else {
        $ins = db()->prepare('INSERT INTO messages (ad_id, name, contact, text, created_at) VALUES (?, ?, ?, ?, NOW())');
        $ins->execute([$id, mb_substr($name, 0, 80), mb_substr($contact, 0, 120), mb_substr($text, 0, 2000)]);
        header('Location: /ad.php?id=' . $id . '&sent=1');
        exit;
    }
}
$title = $ad['title'] . ' — ' . price($ad['price']);
include __DIR__ . '/includes/header.php';
?>
<p><a href="/">Все объявления</a> → <?= e($ad['cat']) ?></p>
<h1><?= e($ad['title']) ?></h1>
<div class="item">
  <img src="/img/<?= e($ad['image']) ?>" width="400" height="300" alt="<?= e($ad['title']) ?>">
  <div>
    <p class="price"><?= price($ad['price']) ?></p>
    <p>Город: <?= e($ad['city']) ?></p>
    <p><?= nl2br(e($ad['description'])) ?></p>
  </div>
</div>
<h2>Написать продавцу</h2>
<?php if (isset($_GET['sent'])): ?><p class="ok">Сообщение отправлено.</p><?php endif; ?>
<?php if ($error): ?><p class="err"><?= e($error) ?></p><?php endif; ?>
<form method="post" class="form">
  <input name="name" placeholder="Ваше имя" maxlength="80">
  <input name="contact" placeholder="Телефон или e-mail" maxlength="120">
  <textarea name="text" rows="4" placeholder="Сообщение"></textarea>
  <button type="submit">Отправить</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
