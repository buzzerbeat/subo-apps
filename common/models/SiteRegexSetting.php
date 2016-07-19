<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "site_regex_setting".
 *
 * @property integer $id
 * @property string $site
 * @property integer $type
 * @property string $app_req_url
 * @property string $pattern
 * @property string $matches_index
 * @property string $headers
 */
class SiteRegexSetting extends \yii\db\ActiveRecord
{

    public $key = "";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'site_regex_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site', 'pattern', 'matches_index', 'headers'], 'required'],
            [['headers'], 'string'],
            [['site', 'pattern', 'matches_index'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site' => 'Site',
            'pattern' => 'Pattern',
            'matches_index' => 'Matches Index',
            'headers' => 'Headers',
        ];
    }

    public function fields()
    {
        $fields = [
            'type',
            'site',
            'app_req_url',
            'pattern',
            'matches_index',
            'headers',
        ];
        return $fields;
    }
}
