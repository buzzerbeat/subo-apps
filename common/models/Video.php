<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/5
 * Time: 15:03
 */

namespace common\models;
use common\components\Utility;

/**
 * This is the model class for table "video".
 *
 * @property integer $id
 * @property integer $status
 * @property string $key
 * @property string $desc
 * @property string $cover_img
 * @property integer $length
 * @property integer $width
 * @property integer $height
 * @property integer $size
 * @property integer $add_time
 * @property integer $pub_time
 * @property integer $watermark
 * @property string $url
 * @property string $m3u8_url
 * @property string $local
 * @property integer $regex_setting
 * @property string $site_url
 */

class Video extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS_MAP = [
        self::STATUS_INACTIVE => "不可用",
        self::STATUS_ACTIVE => "可用",
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'video';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status','cover_img', 'length', 'width', 'height', 'size', 'add_time', 'pub_time', 'watermark', 'regex_setting'], 'integer'],
            [['key'], 'required'],
            [['m3u8_url', 'desc'], 'string'],
            [['url', 'local', 'site_url', 'key'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'key' => 'Key',
            'desc' => 'Desc',
            'cover_img' => 'Cover Img',
            'length' => 'Length',
            'width' => 'Width',
            'height' => 'Height',
            'size' => 'Size',
            'add_time' => 'Add Time',
            'pub_time' => 'Pub Time',
            'watermark' => 'Watermark',
            'url' => 'Url',
            'm3u8_url' => 'm3u8 Url',
            'local' => 'Local',
            'regex_setting' => 'Regex Setting',
            'site_url' => 'Site Url',
        ];
    }

    public function getSid() {
        return Utility::sid($this->id);
    }
    public function getCoverImg() {
        return Image::findOne($this->cover_img);
    }
    
    public function fields()
    {
        $fields = [
            'sid',
            'coverImg',
            'length',
            'width',
            'height',
            'size',
            'url',
            'm3u8_url',
            'site_url',
            'regexSetting',
        ];
        return $fields;
    }

    public function getRegexSetting() {
        $regexSetting = SiteRegexSetting::findOne(['id'=>$this->regex_setting]);
        if (!empty($regexSetting)) {
            $regexSetting->app_req_url = str_replace('%s', $this->keyId, $regexSetting->app_req_url);
        }
		if (empty($regexSetting->app_req_url)) {
			$regexSetting->app_req_url = $this->site_url;
		}
        return $regexSetting;
    }

    public function getKeyId() {
        $keys = explode('/', $this->key);
        return $keys[1];
    }
}
