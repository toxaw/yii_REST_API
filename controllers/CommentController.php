<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Post;
use app\models\Comment;
use yii\web\UploadedFile;

class CommentController extends Controller
{
    /**
     * {@inheritdoc}
     */
   
    protected $isAdmin, $token;
    
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
        $this->isAdmin = false;

        Yii::$app->response->format = Response::FORMAT_JSON;

        if(!in_array(Yii::$app->controller->action->id, ['comments']))
        {
            $this->token = preg_replace('/^Bearer\s/', '', Yii::$app->request->headers->get('authorization'));

            $model = new User();

            if(!$model->findIdentityByAccessToken($this->token))
            {
                Yii::$app->response->statusCode = 401;

                Yii::$app->response->statusText = 'Unauthorized';    

                Yii::$app->response->data = [
                    'message'   =>  'Unauthorized',
                ];     

                return false;                  
            }
            else
            {
                 $this->isAdmin = true;
            }
        }

        return true;
    }

    public function actionComments($post_id = null)
    {
        if($model = Post::findOne($post_id))
        {   
            $model = new Comment();

            $model->post_id = $post_id;

            $model->load(['Comment' => Yii::$app->request->post()]);
            
            if($this->isAdmin)
            {
                $model->author = User::findOne(['token' => $this->token])->login;
            }

            if($comment_id = $model->create())
            {
                Yii::$app->response->statusCode = 201;

                Yii::$app->response->statusText = 'Successful creation';    

                return [
                    'status'    =>  'true'
                ];
            }         
        }
        else
        {          
            Yii::$app->response->statusCode = 404;

            Yii::$app->response->statusText = 'Post not found';    

            return [
                'message' => 'Post not found'
            ]; 
        }

        Yii::$app->response->statusCode = 400;

        Yii::$app->response->statusText = 'Creating error';    

        return [
            'status'    =>  'false',
            'message'   =>  $this->formatError($model)
        ];
    }

    public function actionDelete($post_id = null)
    {
        if($model = Post::findOne($post_id))
        {   
            $model->delete();
            
            Yii::$app->response->statusCode = 201;

            Yii::$app->response->statusText = 'Successful delete';    

            return [
                'status'    =>  'true',
            ];         
        }
        else
        {          
            Yii::$app->response->statusCode = 404;

            Yii::$app->response->statusText = 'Post not found';    

            return [
                'message' => 'Post not found'
            ]; 
        }
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
