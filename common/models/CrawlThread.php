<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "crawl_thread".
 *
 * @property integer $id
 * @property integer $task_id
 * @property integer $status
 * @property string $site
 * @property string $url
 * @property string $key
 * @property integer $time
 * @property integer $duration
 * @property integer $total_num
 * @property integer $success_num
 * @property integer $fail_num
 * @property integer $duplicate_num
 * @property integer $filter_num
 * @property integer $entity_id
 * @property string $error_json
 */
class CrawlThread extends \yii\db\ActiveRecord
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 2;
    const STATUS_MAP = [
        self::STATUS_SUCCESS=>'成功',
        self::STATUS_FAIL=>'失败',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crawl_thread';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'status', 'time', 'duration', 'total_num', 'success_num', 'fail_num', 'duplicate_num', 'filter_num'], 'integer'],
            [['error_json', 'url', 'entity_id'], 'string'],
            [['site',  'key'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'status' => 'Status',
            'site' => 'Site',
            'url' => 'Url',
            'key' => 'Key',
            'time' => 'Time',
            'duration' => 'Duration',
            'total_num' => 'Total Num',
            'success_num' => 'Success Num',
            'fail_num' => 'Fail Num',
            'duplicate_num' => 'Duplicate Num',
            'filter_num' => 'Filter Num',
            'entity_id' => 'Entity ID',
            'error_json' => 'Error Json',
        ];
    }
}
