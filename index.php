<?php
require __DIR__ . '/db.php';
$slug = $_GET['cat'] ?? '';
$sql  = 'SELECT a.id, a.title, a.price, a.city, a.image, c.name AS cat
         FROM ads a JOIN categories c ON c.id = a.category_id';
if ($slug !== '') {
    $st = db()->prepare($sql . ' WHERE c.slug = ? ORDER BY a.created_at DESC');
    $st->execute([$slug]);
} else {
    $st = db()->query($sql . ' ORDER BY a.created_at DESC');
}
$ads   = $st->fetchAll();
$title = 'Доска объявлений';
include __DIR__ . '/includes/header.php';
?>
<h1>Доска объявлений</h1>
<p>Найдено объявлений: <?= count($ads) ?></p>
<div class="grid">
<?php foreach ($ads as $a): ?>
  <article class="card">
    <a href="/ad.php?id=<?= (int)$a['id'] ?>"><img src="/img/<?= e($a['image']) ?>" width="400" height="300" alt="<?= e($a['title']) ?>"></a>
    <div><h3><a href="/ad.php?id=<?= (int)$a['id'] ?>"><?= e($a['title']) ?></a></h3>
    <p class="price"><?= price($a['price']) ?></p>
    <p><?= e($a['cat']) ?> · <?= e($a['city']) ?></p></div>
  </article>
<?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
