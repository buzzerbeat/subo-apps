<?php

namespace common\models;

use common\components\Utility;
use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property integer $id
 * @property string $content
 * @property integer $status
 * @property string $client_id
 * @property integer $user_id
 * @property integer $parent
 * @property integer $resource_id
 * @property string $user_ip
 * @property integer $create_time
 * @property string $user_agent
 */
class Comment extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'client_id', 'user_id',  'resource_id',  'create_time'], 'required'],
            [['user_id', 'parent', 'resource_id', 'create_time', 'status'], 'integer'],
            [['content', 'user_agent'], 'string', 'max' => 1024],
            [['client_id', 'user_ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'content' => 'Content',
            'status' => 'Status',
            'client_id' => 'Client Id',
            'user_id' => 'User ID',
            'parent' => 'Parent',
            'resource_id' => 'Resource ID',
            'user_ip' => 'User Ip',
            'create_time' => 'Create Time',
            'user_agent' => 'User Agent',
        ];
    }

    public function getSid() {
        return Utility::sid($this->id);
    }

    public function getElapsedTime() {
        return Utility::time_get_past($this->create_time);
    }

    public function getUsername() {
        $user = User::findOne($this->user_id);
        return !empty($user) ? $user->username : "";
    }

    public function getUserAvatar() {
        $user = User::findOne($this->user_id);
        return !empty($user) ? $user->avatarImg : "";
    }

    public function getParentSid() {
        return Utility::sid($this->parent);
    }

    public function getResourceSid() {
        return Utility::sid($this->parent);
    }

    public function fields()
    {
        $fields = [
            'sid',
            'client_id',
            'userName',
            'userAvatar',
            'content',
            'parentSid',
            'elapsedTime',
            'resourceSid',
        ];
        return $fields;
    }
}
