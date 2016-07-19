<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "app".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $path
 */
class App extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name', 'path'], 'required'],
            [['key', 'name', 'path'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'path' => 'Path',
        ];
    }
}
