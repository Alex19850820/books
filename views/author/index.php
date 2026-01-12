<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $authors app\models\Author[] */

$this->title = 'Авторы';
?>
<h1><?= Html::encode($this->title) ?></h1>

<ul>
<?php foreach ($authors as $author): ?>
    <li>
        <?= Html::a(
            Html::encode($author->full_name),
            ['view', 'id' => $author->id]
        ) ?>
        <?php if (!Yii::$app->user->isGuest): ?>
            | 
            <?= Html::a('Редактировать', ['update', 'id' => $author->id]) ?>
            |
            <?= Html::a('Удалить', ['delete', 'id' => $author->id], [
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этого автора?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>

<?php if (!Yii::$app->user->isGuest): ?>
    <p>
        <?= Html::a('Добавить автора', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php endif; ?>
