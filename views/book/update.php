<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Book */
/* @var $authors app\models\Author[] */

$this->title = 'Редактировать книгу: ' . $model->title;
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(['action' => ['book/update', 'id' => $model->id]]); ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>


<?= $form->field($model, 'year')->textInput(['type' => 'number']) ?>

<div class="form-group">
    <label>Авторы</label>
    <?php foreach ($authors as $author): ?>
        <div>
            <input type="checkbox" name="authors[]" value="<?= $author->id ?>" 
                <?= in_array($author->id, $model->authorIds) ? 'checked' : '' ?>>
            <?= Html::encode($author->full_name) ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Назад', ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
</div>

<?php ActiveForm::end(); ?>
