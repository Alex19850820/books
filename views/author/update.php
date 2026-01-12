<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Author */

$this->title = 'Редактировать автора: ' . $model->full_name;
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<p>
    <?= Html::a('Посмотреть', ['view', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
    <?= Html::a('Назад к списку', ['index'], ['class' => 'btn btn-secondary']) ?>
</p>
