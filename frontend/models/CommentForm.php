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
use Yii;

class CommentForm extends BaseForm
{
    public $content;
    public $sid;
    public $reply;
    public $retPost;

    public function rules()
    {
        return [
            // username and password are both required
            [['content', 'sid'], 'required'],
            [['sid'],  'string'],
            ['content',  'string', 'min' => 3, 'max' => 2000],
            ['content', 'validateSimilar'],
        ];
    }

    public function validateSimilar($attribute, $params)
    {
        $latestPosts = Comment::find()
            ->orderBy(['create_time'=>SORT_DESC])
            ->limit(10)
            ->all();
        foreach($latestPosts as $post) {
            similar_text($post->content, $this->content, $percent);
            if (number_format($percent, 0) > 90){
                $this->addError($attribute, '请勿连续发表相似内容，您可以查看之前发表的内容是否已经成功');
                return;
            }
        }
    }

    public function getResourceId() {
        return Utility::id($this->sid);
    }

    public function getReplyId() {
        return Utility::id($this->reply);
    }


    public function send() {

        $user = \Yii::$app->user->identity;
        if ($this->validate()) {
            $headers = Yii::$app->request->headers;
            $post = new Comment();
            $post->setAttributes([
                'resource_id'=>$this->getResourceId(),
                'content'=>$this->content,
                'status'=>Comment::STATUS_ACTIVE,
                'user_id'=>$user->id,
                'create_time'=>time(),
                'client_id'=>$this->getClientId($user),
                'user_agent'=>$headers->get('User-Agent', ''),
                'user_ip'=>Yii::$app->request->getUserIP(),
            ]);

            if (!empty($this->reply)) {
                $reply = Comment::findOne($this->getReplyId());
                if (!empty($reply)) {
                    $post->parent = $reply->id;
                }
            }


            if (!$post->save()) {
                $this->addErrors($post->getErrors());
                return false;
            }
            $this->retPost = $post;
            return true;
        }
        return false;
    }
}