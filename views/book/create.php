<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Book */
/* @var $authors app\models\Author[] */

$this->title = 'Создать книгу';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->errorSummary($model) ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>


<?= $form->field($model, 'year')
    ->textInput(['type' => 'number', 'placeholder' => 'Например: 2024', 'min' => 1000, 'max' => date('Y') + 1])
    ->label('Год выпуска') ?>

<?= $form->field($model, 'description')->textarea(['rows' => 6, 'placeholder' => 'Введите описание книги']) ?>


<?= $form->field($model, 'isbn')->textInput([
    'maxlength' => 17,
    'placeholder' => 'Например: 978-5-06-002611-5 или 0-471-954-11-0',
    'title' => 'Введите ISBN (10 или 13 цифр с дефисами)',
])->label('ISBN') ?>


<div class="form-group">
    <label>Авторы</label>
    <?php if (!empty($authors)): ?>
        <?php foreach ($authors as $author): ?>
            <div>
                <input
                    type="checkbox"
                    name="authors[]"
                    value="<?= $author->id ?>"
                    id="author-<?= $author->id ?>"
                >
                <label for="author-<?= $author->id ?>">
                    <?= Html::encode($author->full_name) ?>
                </label>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Нет доступных авторов.</p>
    <?php endif; ?>
</div>

<?= $form->field($model, 'image_path')->fileInput(['accept' => 'image/*'])->label('Обложка книги') ?>


<div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Назад', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>
<script>
document.querySelector('[name="Book[isbn]"]').addEventListener('input', function() {
    this.value = this.value
        .replace(/[^0-9X-]/gi, '')  // Удаляем всё, кроме цифр, X и дефисов
        .toUpperCase();                // X → X (не x)
});
</script>