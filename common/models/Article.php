<?php

namespace common\models;

use article\models\TtArticle;
use article\models\TtArticleCount;
use article\models\TtArticleImage;
use article\models\TtArticleTag;
use article\models\TtArticleTagRel;
use article\models\TtArticleVideo;
use article\models\TtMedia;
use common\components\Utility;
use Yii;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $title
 * @property string $abstract
 * @property string $content
 * @property string $src_link
 * @property string $source
 * @property integer $pub_time
 * @property integer $cover
 * @property string $key
 * @property TtArticleCount $countInfo
 * @property Video $video
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'key'], 'required'],
            [['abstract', 'content'], 'string'],
            [['pub_time', 'cover'], 'integer'],
            [['title'], 'string', 'max' => 1024],
            [['src_link'], 'string', 'max' => 2048],
            [['source', 'key'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'abstract' => 'Abstract',
            'content' => 'Content',
            'src_link' => 'Src Link',
            'source' => 'Source',
            'pub_time' => 'Pub Time',
            'cover' => 'Cover',
            'key' => 'Key',
        ];
    }

    public function getSid() {
        return Utility::sid($this->id);
    }
//    public function getDefaultCover() {
//        return $this->hasOne(Image::className(), ['id' => 'cover']);
//    }

    public function getImages() {
        if ($this->type == 1) {
            return $this->hasMany(TtArticleImage::className(), [
                    'tt_article_id' => 'id'
            ]);
        } else {
            return $this->hasMany(TtArticleImage::className(), [
                    'tt_article_id' => 'id'
            ])->andFilterWhere([ 'is_thumb' => 0]);
        }

    }

    public function getMedia() {
        if (!empty($this->ttArticle)) {
            return $this->ttArticle->media;
        }
        return null;
    }

    public function getTtArticle() {
        return $this->hasOne(TtArticle::className(), ['article_id' => 'id']);
    }

    public function getCoverList() {
        if (!empty($this->ttArticle)) {
            $coverIds = explode(',', $this->ttArticle->cover_ids);
            if (!empty($coverIds)) {
                return Image::find()->where(['in', 'id', $coverIds])->all();
            }
        }
        return null;
    }

    public function getType()
    {
        if (!empty($this->ttArticle)) {
            return $this->ttArticle->type;
        }
        return 0;
    }

    public function getStyle()
    {
        if (!empty($this->ttArticle)) {
            return $this->ttArticle->style;
        }
        return 0;
    }

    public function getVideo() {
        return $this->hasOne(TtArticleVideo::className(), ['article_id' => 'id']);
    }

    public function getWebContent() {
        if (!empty($this->content)) {
            $decodeCnt = urldecode($this->content);
            foreach($this->images as $image) {
                $replacedImage = Yii::getAlias('@imgUrl') . "/thumb/0/0/0/" . $image->img->sid . "/". $image->img->md5 .".png";
                $decodeCnt = preg_replace('/<a class="image"\s*href="[^"]+' . str_replace('/', '\/', $image->tt_uri) . '[^"]+"[^>]+><\/a>/i', '<img src="'. $replacedImage .'"/><br>', $decodeCnt);
            }
            return $decodeCnt;
        }
        return null;


    }


    public function getAppContent() {
        if (!empty($this->content)) {
            $decodeCnt = urldecode($this->content);
            foreach($this->images as $image) {
                $replacedTag = '<img_sid>'  . $image->sid . '</img_sid>';
                $decodeCnt = preg_replace('/<a class="image"\s*href="[^"]+' . str_replace('/', '\/', $image->tt_uri) . '[^"]+"[^>]+><\/a>/i', $replacedTag, $decodeCnt);
            }
            return $decodeCnt;
        }
        return null;


    }


    public function fields()
    {
        $fields = [
            'sid',
            'title',
            'abstract',
//            'content',
//            'appContent',
            'pub_time',
//            'defaultCover',
            'media',
            'images',
            'type',
            'style',
            'coverList',
            'video',
            'tags',
            'countInfo',
        ];
        return $fields;
    }

    /**
     * @return TtArticleCount
     */
    public function getCountInfo() {
        return $this->hasOne(TtArticleCount::className(), ['article_id'=>'id']);
    }
    public function getTagRels() {
        return $this->hasMany(TtArticleTagRel::className(), ['article_id'=>'id']);
    }

    public function getTags() {
        return $this->hasMany(TtArticleTag::className(), ['id' => 'tag_id'])
            ->via('tagRels');
    }

    public function getComments() {
        return $this->hasMany(Comment::className(),
            ['item_id' => 'id'])->andFilterWhere(['item_type' => 'article/article']);
    }

    public function extraFields()
    {
        $fields = ['content', 'webContent', 'appContent', 'comments'];
        return $fields;
    }
}
