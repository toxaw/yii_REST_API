<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Post;
use yii\web\UploadedFile;

class ApiController extends Controller
{
    /**
     * {@inheritdoc}
     */

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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
                'class' => VerbFilter::className(),
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

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if(Yii::$app->controller->action->id!='auth')
        {
            $token = preg_replace('/^Bearer\s/', '', Yii::$app->request->headers->get('authorization'));

            $model = new User();

            if(!$model->findIdentityByAccessToken($token))
            {
                Yii::$app->response->statusCode = 401;

                Yii::$app->response->statusText = 'Unauthorized';    

                Yii::$app->response->data = [
                    'message'   =>  'Unauthorized',
                ];     

                return false;                  
            }
        }

        return true;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionAuth()
    {      
        $model = new User();
        
        if ($model->load(['User' => Yii::$app->request->post()]) && $token = $model->auth()) 
        {

            Yii::$app->response->statusCode = 200;

            Yii::$app->response->statusText = 'Successful authorization';    

            return [
                'status'    =>  true,
                'token'     =>  $token
            ];
        }

        Yii::$app->response->statusCode = 401;

        Yii::$app->response->statusText = 'Invalid authorization data';    

        return [
            'status'    =>  false,
            'message'   =>  'Invalid authorization data'
        ];     
    }

    public function actionPosts()
    {

        $model = new Post();
        
        $model->load(['Post' => Yii::$app->request->post()]);

        $model->image = UploadedFile::getInstanceByName('image');

        if ($post_id = $model->create()) 
        {
            Yii::$app->response->statusCode = 201;

            Yii::$app->response->statusText = 'Successful creation';    

            return [
                'status'    =>  true,
                'post_id'     =>  $post_id
            ];
        }

        Yii::$app->response->statusCode = 400;

        Yii::$app->response->statusText = 'Creating error';    

        return [
            'status'    =>  false,
            'message'   =>  $this->formatError($model)
        ]; 
    }

    protected function formatError($model)
    {
        $error = [];

        foreach ($model->getErrors() as $key => $value) 
        {
            $error[$key] = $value[0];
        }

        return $error;
    }
}
