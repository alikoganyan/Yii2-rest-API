<?php
namespace backend\controllers;

use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use yii\web\Response;

/**
 * Site controller
 */
class ApiController extends Controller
{
    /**
     * @inheritdoc
     */
    /*public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
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
    }*/

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions'=>['update-role'],
                        'roles' => ['admin'],
                    ],
                    [
                        'allow' => true,
                        'actions'=>['login','error','logout','isadmin'],
                        'roles' => ['?','@'],
                    ],
                    [
                        'allow' => false,
                        'actions'=>['getUsers'],
                        'roles' => ['?','@'],
                    ],
                    [
                        'allow' => true,
                        'actions'=>[],
                        'roles' => ['manager'],
                    ],

                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'isadmin' => ['get'],
                    'updateRole' => ['post'],
                ]
            ],
            'corsFilter'  => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Allow-Origin' => ['*'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true
                ],
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->user->isGuest) {
            return Yii::$app->user;
            return $this->goHome();
        }
        $model = new LoginForm();
        //VarDumper::dump(Yii::$app->request->post(),10,true);die;
        if ($model->load(Yii::$app->request->post())) {
            //VarDumper::dump(Yii::$app->request->post(),10,true);die;
        }
        //Yii::$app->user->logout();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $duration = $model->rememberMe ? 3600 * 24 * 30 : 0;
            $identityKey  = json_encode([
                Yii::$app->user->identity->getId(),
                Yii::$app->user->identity->getAuthKey(),
                $duration,
            ]);
            $Id  = Yii::$app->user->identity->getId();
            $Auth  = Yii::$app->user->identity->validateAuthKey(Yii::$app->user->identity->getAuthKey());

            $items = ['_identity-backend'=>$identityKey];

            return $items;

            return $this->goBack();
        } else {
            return ;
            return $this->render('//site/login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->user->logout();
        return ['action'=>true];
    }

    /**
     * @return array
     */
    public function actionIsadmin(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = 200;
        if(Yii::$app->user->isGuest){
            return ["isAuthAdmin"=>false];
        }
        $user  = [
            "id"=>Yii::$app->user->identity->getId(),
            "username"=>Yii::$app->user->identity->username,
            "email"=>Yii::$app->user->identity->email,
            "isAuthAdmin"=>true
        ];
        $user["role"] = Yii::$app->user->identity->role === 10 ? "admin" : "manager";
        return $user;
    }

    public function actionGetUsers(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = 200;
        $model = New User();
        $users = $model->getAll();
        return $users;
    }

    public function actionGetUserById(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->statusCode = 200;
        $id = Yii::$app->request->get('id');
        $model = New User();
        $users = $model->getUserById($id);
        return $users;
    }

    public function actionUpdateRole(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $userPost = Yii::$app->request->post();
        $user = User::findOne($userPost['id']);
        $user->setAttribute('role',$userPost['role']);
        $result = $user->save();
        return $user->getAttributes();
    }
}
