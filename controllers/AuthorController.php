<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Author;

class AuthorController extends Controller
{
    /**
     * Список всех авторов (доступен всем)
     */
    public function actionIndex()
    {
        $authors = Author::find()->all();
        return $this->render('index', ['authors' => $authors]);
    }

    /**
     * Детальная страница автора (доступна всем)
     * @param int $id ID автора
     */
    public function actionView($id)
    {
        $author = Author::findOne($id);
        if (!$author) {
            throw new NotFoundHttpException('Автор не найден.');
        }
        return $this->render('view', ['author' => $author]);
    }

    /**
     * Создание автора (только для авторизованных)
     */
    public function actionCreate()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $model = new Author();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    /**
     * Редактирование автора (только для авторизованных)
     * @param int $id ID автора
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $author = Author::findOne($id);
        if (!$author) {
            throw new NotFoundHttpException('Автор не найден.');
        }

        if ($author->load(Yii::$app->request->post()) && $author->save()) {
            return $this->redirect(['view', 'id' => $author->id]);
        }
        return $this->render('update', ['model' => $author]);
    }

    /**
     * Удаление автора (только для авторизованных)
     * @param int $id ID автора
     */
    public function actionDelete($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $author = Author::findOne($id);
        if ($author) {
            $author->delete();
        }
        return $this->redirect(['index']);
    }
}
