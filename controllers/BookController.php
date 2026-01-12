<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Book;
use app\models\Author;
use app\models\BookAuthor;
use yii\web\UploadedFile;

class BookController extends Controller
{
    /**
     * Список всех книг (доступен всем)
     */
    public function actionIndex()
    {
        $books = Book::find()->with('authors')->all();
        return $this->render('index', ['books' => $books]);
    }

    /**
     * Детальная страница книги (доступна всем)
     * @param int $id ID книги
     */
    public function actionView($id)
    {
        $book = Book::findOne($id);
        if (!$book) {
            throw new NotFoundHttpException('Книга не найдена.');
        }
        return $this->render('view', ['book' => $book]);
    }

    /**
     * Создание новой книги (только для авторизованных)
     */
    public function actionCreate()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $model = new Book();

        if ($model->load(Yii::$app->request->post())) {
            // 1. Валидация ISBN (очистка и проверка формата)
            $cleanIsbn = preg_replace('/[^0-9X]/i', '', strtoupper($model->isbn));
            Yii::debug('Исходный ISBN: ' . $model->isbn);
            Yii::debug('Очищенный ISBN: ' . $cleanIsbn);

            // 2. Проверка на существование ISBN в базе
            $existingBook = Book::findOne(['isbn' => $cleanIsbn]);
            if ($existingBook) {
                $model->addError('isbn', 'Книга с таким ISBN уже существует.');
            }

            // 3. Если ошибок нет — сохраняем модель
            if (!$model->hasErrors() && $model->save()) {
                // 4. Обработка загрузки файла
                $image = UploadedFile::getInstance($model, 'image_path');
                if ($image && !$image->hasError) {
                    $fileName = 'book_' . $model->id . '_' . $image->baseName . '.' . $image->extension;
                    $savePath = Yii::getAlias('@webroot/uploads/books/') . $fileName;

                    if ($image->saveAs($savePath)) {
                        $model->image_path = '/uploads/books/' . $fileName;
                        $model->save(false); // Сохраняем путь к изображению без повторной валидации
                    } else {
                        Yii::error('Не удалось сохранить файл: ' . $image->error);
                    }
                }

                // 5. Сохранение связей с авторами
                $authorIds = Yii::$app->request->post('authors', []);
                foreach ($authorIds as $authorId) {
                    $link = new BookAuthor([
                        'book_id' => $model->id,
                        'author_id' => $authorId
                    ]);
                    if (!$link->save()) {
                        Yii::error('Ошибка сохранения связи с автором ID: ' . $authorId);
                    }
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        // 6. Подготовка данных для представления
        $authors = Author::find()->all();
        return $this->render('create', [
            'model' => $model,
            'authors' => $authors
        ]);
    }


    /**
     * Редактирование книги (только для авторизованных)
     * @param int $id ID книги
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $book = Book::findOne($id);
        if (!$book) {
            throw new NotFoundHttpException('Книга не найдена.');
        }

        if ($book->load(Yii::$app->request->post()) && $book->save()) {
            // Пересоздаём связи с авторами
            BookAuthor::deleteAll(['book_id' => $book->id]);
            $authorIds = Yii::$app->request->post('authors', []);
            foreach ($authorIds as $authorId) {
                (new BookAuthor([
                    'book_id' => $book->id,
                    'author_id' => $authorId
                ]))->save();
            }
            return $this->redirect(['view', 'id' => $book->id]);
        }

        $authors = Author::find()->all();
        return $this->render('update', [
            'model' => $book,
            'authors' => $authors
        ]);
    }

    /**
     * Удаление книги (только для авторизованных)
     * @param int $id ID книги
     */
    public function actionDelete($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $book = Book::findOne($id);
        if ($book) {
            $book->delete();
        }
        return $this->redirect(['index']);
    }
}
