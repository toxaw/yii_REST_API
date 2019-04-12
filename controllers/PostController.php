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

class PostController extends Controller
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

        if(!in_array(Yii::$app->controller->action->id, ['getpost', 'getposts']))
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

    public function actionPosts()
    {
        $model = new Post();
        
        $model->scenario = 'create';

        $model->load(['Post' => Yii::$app->request->post()]);

        $model->image = UploadedFile::getInstanceByName('image');

        if ($post_id = $model->create()) 
        {
            Yii::$app->response->statusCode = 201;

            Yii::$app->response->statusText = 'Successful creation';    

            return [
                'status'    =>  'true',
                'post_id'     =>  $post_id
            ];
        }

        Yii::$app->response->statusCode = 400;

        Yii::$app->response->statusText = 'Creating error';    

        return [
            'status'    =>  'false',
            'message'   =>  $this->formatError($model)
        ]; 
    }

    public function actionEdit($post_id = null)
    {
        if($model = Post::findOne($post_id))
        { 
            $model->load(['Post' => Yii::$app->request->post()]);

            $model->image = UploadedFile::getInstanceByName('image');
            
            if ($model->create()) 
            {
                Yii::$app->response->statusCode = 201;

                Yii::$app->response->statusText = 'Successful creation';    

                $post = $model->toArray();

                unset($post['id']);

                $post['image'] = Yii::$app->urlManager->createAbsoluteUrl(Yii::getAlias('@imageUrl') . '/' . $post['image']['name']);

                $post['datetime'] = Yii::$app->formatter->asDate($post['datetime'], 'php:H:i d.m.Y');

                return [
                    'status'    =>  'true',
                    'post'     =>  $post
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

        Yii::$app->response->statusText = 'Editing error';    

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
      
        Yii::$app->response->statusCode = 404;

        Yii::$app->response->statusText = 'Post not found';    

        return [
            'message' => 'Post not found'
        ]; 
    }

    public function actionGetposts()
    {   
        $absoluteUrl = Yii::$app->urlManager->createAbsoluteUrl('') . Yii::getAlias('@imageUrl') . '/';

        $posts = Post::find()->select(['title',"DATE_FORMAT(`datetime`, '%H:%i %d.%m.%Y') as datetime",'anons','text','round(rating, 1) as rating',"CONCAT('$absoluteUrl', image) as image"])->asArray()->all();

        Yii::$app->response->statusCode = 200;

        Yii::$app->response->statusText = 'List posts';    

        return $posts;        
        
    }

    public function actionGetpost($post_id = null)
    {
        if($post = Post::findOne($post_id))
        {
            $post = $post->toArray();

            $post['image'] = Yii::$app->urlManager->createAbsoluteUrl(Yii::getAlias('@imageUrl') . '/' . $post['image']);

            $post['datetime'] = Yii::$app->formatter->asDate($post['datetime'], 'php:H:i d.m.Y');

            $post['rating'] = round($post['rating'], 1);

            $post['comments'] = $posts = Comment::find()->select(['id as comment_id',"DATE_FORMAT(`datetime`, '%H:%i %d.%m.%Y') as datetime",'author','comment'])->where(['post_id' => $post_id])->asArray()->all();

            Yii::$app->response->statusCode = 200;

            Yii::$app->response->statusText = 'View post';    

            return $post;
        }

        Yii::$app->response->statusCode = 404;

        Yii::$app->response->statusText = 'Post not found';    

        return [
            'message' => 'Post not found'
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
