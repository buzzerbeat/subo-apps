<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "crawl_task".
 *
 * @property integer $id
 * @property integer $status
 * @property string $command
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $success_num
 * @property integer $fail_num
 * @property integer $filter_num
 * @property integer $duplicate_num
 * @property string $error_json
 */
class CrawlTask extends \yii\db\ActiveRecord
{

    const STATUS_READY = 0;
    const STATUS_RUNNING = 1;
    const STATUS_FINISH = 2;
    const STATUS_MAP = [
        self::STATUS_READY=>'未开始',
        self::STATUS_RUNNING=>'执行中',
        self::STATUS_FINISH=>'已结束',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crawl_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'start_time', 'end_time', 'success_num', 'fail_num', 'filter_num', 'duplicate_num'], 'integer'],
            [['error_json', 'command'], 'string'],
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
            'command' => 'Command',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'success_num' => 'Success Num',
            'fail_num' => 'Fail Num',
            'filter_num' => 'Filter Num',
            'duplicate_num' => 'Duplicate Num',
            'error_json' => 'Error Json',
        ];
    }
}
