<?php

namespace common\models;


use common\components\Utility;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\IdentityInterface;


/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property integer $type
 * @property string $device_uuid
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property integer $status
 * @property string $email
 * @property string $mobile
 * @property string $salt
 * @property integer $sex
 * @property integer $avatar
 * @property string $qq
 * @property string $weibo
 * @property string $weixin
 * @property string $created_at
 * @property string $updated_at
 * @property string $from_app
 * @property string $personal_sign
 */
class User extends ActiveRecord implements IdentityInterface
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    const DEVICE_TYPE = 1;
    const MOBILE_TYPE = 2;
    const THIRD_TYPE = 3;

    public $client = null;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'auth_key'], 'required'],
            [['created_at', 'updated_at', 'sex', 'personal_sign'], 'safe'],
            [['username', 'email', 'password_hash', 'password_reset_token', 'device_uuid'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['status'], 'integer'],
        ];
    }


    public function getAccessToken() {
        $where = [
            'user_id'=>$this->id,
            'client_id'=>$this->client,
        ];
        $token = OauthAccessTokens::findOne($where);
        return empty($token) ? '' : $token->access_token;
    }

    public function getRefreshToken() {
        $where = [
            'user_id'=>$this->id,
            'client_id'=>$this->client,
        ];
        $token = OauthRefreshTokens::findOne($where);
        return empty($token) ? '' : $token->refresh_token;
    }

    public function fields()
    {

        $fields = [
            'accessToken','refreshToken',
            'avatarSid', 'personal_sign', 'mobile',
            'bindQq', 'bindWeibo', 'bindWeixin', 'nameEditable'
        ];
        return $fields;
    }

    public function getBindQq() {
        return empty($this->qq) ? false : true;
    }

    public function getBindWeixin() {
        return empty($this->weixin) ? false : true;
    }
    public function getBindWeibo() {
        return empty($this->weibo) ? false : true;
    }

    public function getAvatarSid() {
        return Utility::sid($this->avatar);
    }

    public function getAvatarImg() {
        $avatarImg = Image::findOne($this->avatar);
        return $avatarImg;
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'User Name',
            'type' => 'Type',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'status' => 'Status',
            'email' => 'Email',
            'mobile' => 'Mobile Number',
            'salt' => 'Salt',
            'sex' => 'Sex',
            'avatar' => 'Avatar Img Id',
            'qq' => 'Qq',
            'weibo' => 'Weibo',
            'weixin' => 'WeiXin',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'from_app' => 'From App',
            'personal_sign' => '个人签名',
        ];
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $accessToken = OauthAccessTokens::findOne([
            'access_token' => $token,
        ]);
        if (!empty($accessToken) && strtotime($accessToken->expires) > time()) {
            return static::findOne($accessToken->user_id);
        } else {
            return false;
        }

    }

    public static function getAuthUser() {
        $request = Yii::$app->request;
        $authHeader = $request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            $identity = self::findIdentityByAccessToken($matches[1]);
            return $identity;
        }
        return false;
    }

    public static function findIdentityByTokenAndClient($token, $client)
    {
        $accessToken = OauthAccessTokens::findOne([
            'access_token' => $token,
            'client_id' => $client,
        ]);
        if (!empty($accessToken) && strtotime($accessToken->expires) > time()) {
            return static::findOne($accessToken->user_id);
        } else {
            return false;
        }
    }


    public function hasTokenExpired($client) {
        $accessToken = OauthAccessTokens::findOne([
            'user_id' => $this->id,
            'client_id' => $client,
        ]);
        if (!empty($accessToken) && strtotime($accessToken->expires) > time()) {
            return true;
        }
        return false;
    }

    public function isNameEditable() {
        return (time() - $this->updated_at) >= 86400 * 90;
    }


    public static function findIdentityByRefreshToken($token, $client)
    {
        $refreshToken = OauthRefreshTokens::findOne([
            'refresh_token' => $token,
            'client_id' => $client,
        ]);
        if (!empty($refreshToken) && strtotime($refreshToken->expires) > time()) {
            return static::findOne($refreshToken->user_id);
        } else {
            return false;
        }

    }

    public static function findIdentityByAuthKey($authKey)
    {
        return static::findOne(['auth_key'=>$authKey]);

    }
    /**
     * Finds user by devide uuid
     *
     * @param string $uuid
     * @return static|null
     */

    public static function findByDeviceUuid($uuid)
    {
        return static::findOne(['device_uuid' => $uuid, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by user_name
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByMobile($mobile)
    {
        return static::findOne(['mobile' => $mobile, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByThirdAccount($oid, $from)
    {
        $where = [];
        switch($from) {
            case "qq":
                $where['qq'] = $oid;
                break;
            case "weibo":
                $where['weibo'] = $oid;
                break;
            case "weixin":
                $where['weixin'] = $oid;
                break;
        }
        return static::findOne($where);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }
    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }


    public function genRandomPassword()
    {
        $password = Utility::getRandString(8);
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateRefreshToken($client = null) {
        $token = OauthRefreshTokens::findOne([
            'client_id'=>$client,
            'user_id'=>strval($this->id),
        ]);
        if (empty($token)) {
            $token = new OauthRefreshTokens();
            $token->setAttributes([
                'refresh_token'=>Yii::$app->security->generateRandomString(),
                'client_id'=>$client,
                'user_id'=>strval($this->id),
                'expires'=>date('Y-m-d H:i:s', time() + 360 * 86400),
            ], false);
            if (!$token->save()) {
                $this->addErrors($token->getErrors());
                return false;
            }
            return true;
        } else {
            $token->refresh_token = Yii::$app->security->generateRandomString();
            $token->expires = date('Y-m-d H:i:s', time() + 360 * 86400);
            if (!$token->save()) {
                $this->addErrors($token->getErrors());
                return false;
            }
            return true;
        }
    }

    public function generateToken($client = null, $refreshToken = null, $skipRefreshToken = false)
    {
        $token = OauthAccessTokens::findOne([
            'client_id'=>$client,
            'user_id'=>strval($this->id),
        ]);
        if (empty($token)) {
            $token = new OauthAccessTokens();
            $token->setAttributes([
                'access_token'=>Yii::$app->security->generateRandomString(),
                'client_id'=>$client,
                'user_id'=>strval($this->id),
                'expires'=>date('Y-m-d H:i:s', time() + 30 * 86400),
            ], false);
            if (!$token->save()) {
                $this->addErrors($token->getErrors());
                return false;
            }

            if (!$this->generateRefreshToken($client)) {
                $this->addError('', '生成刷新token失败');
                return false;
            }
            return true;
        } else {
            $refToken = OauthRefreshTokens::findOne([
                'client_id'=>$client,
                'user_id'=>strval($this->id),
                'refresh_token'=>$refreshToken,
            ]);
            if (!empty($refToken) || $skipRefreshToken) {
                $token->access_token = Yii::$app->security->generateRandomString();
                $token->expires = date('Y-m-d H:i:s', time() + 30 * 86400);
                if (!$token->save()) {
                    $this->addErrors($token->getErrors());
                    return false;
                }
                return true;
            } else {
                $this->addError('', '刷新token验证失败');
                return false;
            }
        }
    }
    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getNameEditable() {
        return [
            'editable'=>$this->isNameEditable(),
            'editable_time'=>strtotime($this->updated_at) + 86400 * 90,
        ];
    }


}
