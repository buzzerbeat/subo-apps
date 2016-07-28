<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/4/25
 * Time: 21:24
 */

namespace frontend\controllers;


use common\components\LevelFilter;
use frontend\models\BindForm;
use frontend\models\DeviceLoginForm;
use frontend\models\DeviceRegisterForm;
use frontend\models\LoginForm;
use frontend\models\MobileValidForm;
use frontend\models\NameValidForm;
use frontend\models\RegisterForm;
use common\models\User;
use frontend\models\ThirdRegisterForm;
use frontend\models\ThirdValidForm;
use frontend\models\TokenForm;
use frontend\models\UserInfoForm;
use frontend\models\VerifyForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\web\Response;

class UserController extends Controller
{


    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'only' => ['info',  'edit', 'bind'],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        $behaviors['access'] = [
            'class' => LevelFilter::className(),
            'only' => ['info', 'edit', 'bind'],
            'levels' => ['info', 'edit'],
            'rules' => [
                [
                    'actions' => ['info', 'edit', 'bind'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actionInfo() {

        $user = Yii::$app->user->identity;
//        $user->client = Yii::$app->request->get('client', null);
        return $user;
    }

    public function actionRegister() {
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->get(), '') && $model->register()) {
            return ["status"=>0, "message"=>"", "user"=>$model->user];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }

    public function actionDeviceRegister() {
        $model = new DeviceRegisterForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->register()) {
            return ["status"=>0, "message"=>"", "user"=>$model->user];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }

    public function actionThirdRegister() {
        $model = new ThirdRegisterForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->thirdRegister()) {
            return ["status"=>0, "message"=>"", "user"=>$model->user];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }


    public function actionLogin() {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->login()) {
            return ["status"=>0, "message"=>"", "user"=>$model->user];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }

//    public function actionDeviceLogin() {
//        $model = new DeviceLoginForm();
//        if ($model->load(Yii::$app->request->post(), '') && $model->login()) {
//            return ["status"=>0, "message"=>"", "user"=>$model->user];
//        }
//        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
//    }

    public function actionThirdLogin() {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->thirdLogin()) {
            return ["status"=>0, "message"=>"", "user"=>$model->user];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }

    public function actionRequestCode() {
        $model = new VerifyForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->request()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }

    public function actionVerifyCode() {
        $model = new VerifyForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->verify()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }

    public function actionEdit() {
        $model = new UserInfoForm();
        $model->load(Yii::$app->request->post(), '');
        if ($model->edit()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }

    public function actionBind() {
        $model = new BindForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->bind()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }

    public function actionRefresh() {
        $model = new TokenForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->revoke()) {
            return ["status"=>0, "message"=>"", "user"=>$model->user];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }

    public function actionMobileValid() {
        $model = new MobileValidForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }

    public function actionThirdValid() {
        $model = new ThirdValidForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->validate() && $model->validateDuplicated('oid')) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }

    public function actionNameValid() {
        $model = new NameValidForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode("\n", $model->getFirstErrors())];
    }

    public function actionTokenCheck() {
        $token = Yii::$app->request->get('token', '');
        $user = User::findIdentityByAccessToken($token);
        if (!empty($user)) {
            return ["status"=>0, "message"=>""];
        } else {
            return ["status"=>1, "message"=>""];
        }
    }

}