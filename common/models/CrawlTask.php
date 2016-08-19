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
            [['status', 'start_time', 'end_time', 'success_num', 'fail_num'], 'integer'],
            [['error_json', 'command'], 'string'],
        ];
    }

    public function getComments() {
        return $this->hasMany(Comment::className(),
            ['item_id' => 'id'])->andFilterWhere(['item_type' => 'article/article']);
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
            'error_json' => 'Error Json',
        ];
    }



    public function getSuccessEntityNum() {
        return CrawlThread::find()->where(['task_id'=>$this->id])->sum('success_num');
    }

    public function getFailEntityNum() {
        return CrawlThread::find()->where(['task_id'=>$this->id])->sum('fail_num');
    }

    public function getDuplicateEntityNum() {
        return CrawlThread::find()->where(['task_id'=>$this->id])->sum('duplicate_num');
    }

    public function getFilterEntityNum() {
        return CrawlThread::find()->where(['task_id'=>$this->id])->sum('filter_num');
    }

    public function getTotalEntityNum() {
        return CrawlThread::find()->where(['task_id'=>$this->id])->sum('total_num');
    }
}
