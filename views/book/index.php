<?php
use yii\helpers\Html;
?>
<h1>Книги</h1>
<?php foreach ($books as $book): ?>
    <div>
        <h3><?= Html::encode($book->title) ?></h3>
        <p>Год: <?= $book->year ?></p>
        <?php foreach ($book->authors as $author): ?>
            <span><?= Html::encode($author->full_name) ?></span>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
