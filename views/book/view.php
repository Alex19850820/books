<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $book app\models\Book */

$this->title = $book->title;
?>
<h1><?= Html::encode($this->title) ?></h1>

<p><strong>Год:</strong> <?= $book->year ?></p>

<h2>Авторы</h2>
<?php foreach ($book->authors as $author): ?>
    <p><?= Html::encode($author->full_name) ?></p>
<?php endforeach; ?>

<p>
    <?= Html::a('Редактировать', ['update', 'id' => $book->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Удалить', ['delete', 'id' => $book->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
            'method' => 'post',
        ],
    ]) ?>
</p>
