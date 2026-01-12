<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\MainForm;
use linslin\yii2\curl;
use Da\QrCode\QrCode;
use app\models\UrlQrCode;
use app\models\Author;
use app\models\Subscription;
use yii\web\NotFoundHttpException;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionMainForm()
    {
        
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new MainForm();

        if ($model->load(Yii::$app->request->post())) {
            if ( ! $model->validate()) {
                if (\Yii::$app->request->isAjax) {
                    $message = 'Ваше сообщение не отправлено';
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $response = [
                        'success' => false,
                        'message' => $message
                    ];
                    return $response;
                }
                return $this->render('index',['model'=>$model]);
            } else {
                $url = $model->url;
                $curl = new curl\Curl();
                $result = $curl->reset()->setOption(
                    CURLOPT_POSTFIELDS,
                    http_build_query(array(
                        'text' => '123'
                    )
                ))->post($url);
                if (\Yii::$app->request->isAjax) {
                    $message = ($curl->response) ? 'Ваше сообщение успешно отправлено - сайт существует' : 'Ваше сообщение успешно отправлено - сайт не существует';
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $qrCodeResult = '';
                    if ($curl->response)  {
                        $qrCode = (new QrCode($url))
                        ->setSize(250)
                        ->setMargin(5)
                        ->setBackgroundColor(51, 153, 255);
                        $qrCode->writeFile(__DIR__ . '/code.png');
                        $qrCodeResult = '<img src="' . $qrCode->writeDataUri() . '">';
                        $urlQrCode = new UrlQrCode();
                        if( !urlQrCode::find()->where(['href'=> $url])->one() ) {
                            $urlQrCode->href = $url;
                            $urlQrCode->qr_code = $qrCode->writeDataUri();
                            $urlQrCode->save();
                        } else {
                            $message = 'Ваше сообщение уже есть в бд!';
                        }
                    }
                    $response = [
                        'success' => true,
                        'message' => $message,
                        'img' => $qrCodeResult,
                    ];
                    
                    return $response;
                } 
                return $this->render('index-confirm',['model'=>$model]);
            }   
        }

        return $this->render('index',['model'=>$model]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionTopAuthors($year = null)
    {
        if (!$year) {
            $year = date('Y');  // текущий год
        }

        $authors = Author::find()
            ->select(['author.id', 'author.full_name', 'COUNT(book.id) as book_count'])
            ->join('INNER JOIN', 'book_author', 'book_author.author_id = author.id')
            ->join('INNER JOIN', 'book', 'book.id = book_author.book_id')
            ->where(['book.year' => $year])
            ->groupBy('author.id')
            ->orderBy(['book_count' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('top-authors', ['authors' => $authors, 'year' => $year]);
    }

    /**
     * Подписка гостя на уведомления о новых книгах автора
     * @param int $id ID автора
     */
    public function actionSubscribe($id)
    {
        $subscription = new Subscription();
        $subscription->author_id = $id;

        if ($subscription->load(Yii::$app->request->post()) && $subscription->validate()) {
            if ($subscription->save()) {
                Yii::$app->session->setFlash('success', 'Вы успешно подписались!');
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка при сохранении подписки.');
                }
        }
        // Получаем данные автора для отображения в форме
        $author = Author::findOne($subscription->author_id);
        if (!$author) {
            throw new NotFoundHttpException('Автор не найден.');
        }

        return $this->render('subscribe', [
            'subscription' => $subscription,
            'author' => $author
        ]);
    }

}
