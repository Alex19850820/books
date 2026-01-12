<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $author app\models\Author */

$this->title = $author->full_name;
?>
<h1><?= Html::encode($this->title) ?></h1>

<?= DetailView::widget([
    'model' => $author,
    'attributes' => [
        'id',
        'full_name',
    ],
]) ?>

<h2>Книги автора</h2>
<ul>
<?php foreach ($author->books as $book): ?>
    <li>
        <?= Html::a(
            Html::encode($book->title),
            ['/book/view', 'id' => $book->id]
        ) ?>
        (<?= $book->year ?>)
    </li>
<?php endforeach; ?>
</ul>

<p>
    <?= Html::a('Вернуться к списку авторов', ['index'], ['class' => 'btn btn-primary']) ?>
</p>
