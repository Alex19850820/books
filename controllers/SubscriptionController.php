<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Subscription;

class SubscriptionController extends Controller
{
    /**
     * Список всех подписок (только для админов/модераторов)
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->can('admin')) {
            return $this->redirect(['site/login']);
        }

        $subscriptions = Subscription::find()->with('author')->all();
        return $this->render('index', ['subscriptions' => $subscriptions]);
    }

    /**
     * Удаление подписки (только для админов)
     * @param int $id ID подписки
     */
    public function actionDelete($id)
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->can('admin')) {
            return $this->redirect(['site/login']);
        }

        $subscription = Subscription::findOne($id);
        if ($subscription) {
            $subscription->delete();
        }
        return $this->redirect(['index']);
    }
}
