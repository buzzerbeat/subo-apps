<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/19
 * Time: 15:11
 */

namespace frontend\models;


use common\components\Utility;
use common\models\Comment;
use yii\base\Model;

class CommentLikeForm extends BaseForm
{
    public $sid;
    private $userId;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['sid'], 'required'],
        ];
    }

    public function getId() {
        return Utility::id($this->sid);
    }

    public function like()
    {
        $user = \Yii::$app->user->identity;
        $this->userId = $user->id;
        $comment = Comment::find()->where([
            'resource_id' => $this->getId(),
            'client_id' => $this->getClientId($user),
            'user' => $this->userId,
        ])->one();
        if ($comment) {
            if (!$comment->updateCounters(['dig' => 1])) {
                $this->addErrors($comment->getErrors());
                return false;
            }
        }
        return true;
    }

}