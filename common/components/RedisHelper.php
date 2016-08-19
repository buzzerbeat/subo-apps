<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/28
 * Time: 14:09
 */

namespace common\components;

//require __DIR__ . '/../../vendor/predis/predis/src/Autoloader.php';

use Predis;
use yii\base\Component;

class RedisHelper extends Component
{
    function redisLink()
    {
        static $r = false;
        if ($r) return $r;
        $r = new Predis\Client('tcp://127.0.0.1:6379?database=0');
        return $r;
    }

    function createTask($task)
    {
        $r = $this->redisLink();
        $r->hmset("task:" . $task->id, [
                "id" => $task->id,
                "concurrency" => $task->max_concurrency_num,
                "total" => $task->total_num,
                "status" => 1,
                "success_num" => 0,
                "fail_num" => 0,
                "start_time" => time(),
                "end_time" => 0,
            ]
        );
        foreach ($task->steps as $step) {
            if (!empty($step->webTask)) {
                $r->hmset("task_steps:" . $task->id . ":" . $step->id, [
                    'id' => $step->id,
                    'type' => $step->webTask->type,
                    'url' => $step->webTask->url,
                    'method' => $step->webTask->method,
                    'params' => $step->webTask->params,
                    'pattern' => $step->webTask->pattern,
                    'matches' => $step->webTask->matches,
                ]);

                $r->zadd("task_steps:" . $task->id, [
                    "task_steps:" . $task->id . ":" . $step->id => $step->id
                    ]
                );
            }

        }

        $r->set("task_amounts:" . $task->id, $task->total_num);
        $r->rpush("task_queue", $task->id);
    }
    function reportTask($taskId, $deviceId, $stepId, $result, $output)
    {
        $r = $this->redisLink();
        $deviceTaskStepsId = $r->zscore("device_task_steps", $deviceId .":". $taskId .":". $stepId);
        if (empty($deviceTaskStepsId)) {
            $deviceTaskStepsId = $r->incr("device_task_steps_id");
            $r->hmset("device_task_steps:" . $deviceTaskStepsId, [
                    "task_id" => $taskId,
                    "step_id" => $stepId,
                    "result" => $result,
                    "output" => $output,
                    "device_id" => $deviceId,
                ]
            );
            $r->zadd("device_task_steps", [$deviceId .":". $taskId .":". $stepId=> $deviceTaskStepsId]);
            if ($result == -1) {
                //Fail
                $this->finishTask($taskId, $deviceId, $result);
            } else if ($result == 0) {
                $taskSteps = $r->zrange("task_steps:" . $taskId, 0, -1);
                if (count($taskSteps) > 0) {
                    $lastStepId = $r->hget($taskSteps[count($taskSteps) - 1], 'id');
                    if ($lastStepId == $stepId) {
                        $this->finishTask($taskId, $deviceId, $result);
                    }
                }
            }
        }
        return true;



    }

    function getTask($taskId)
    {
        $r = $this->redisLink();
        $task = $r->hgetall("task:" . $taskId);
        $taskSteps = $r->zrange("task_steps:" . $taskId, 0, -1);
        $task['steps'] = [];
        foreach($taskSteps as $tStep) {
            $task['steps'][] = $r->hgetall($tStep);
        }

        return $task;
    }


    function queryTaskSteps($taskId) {

    }

    function queryTask($deviceId)
    {
        $r = $this->redisLink();
        $taskId = $r->lindex("task_queue", 0);
        $deviceTaskId = $r->zscore("device_tasks", $deviceId .":". $taskId);
        if (!empty($deviceTaskId)) {
            return false;
        }

        $qLen = $r->llen("task_queue");

        if (!$qLen) {
            return false;
        }

        $amount = $r->get("task_amounts:" . $taskId);
        if ($amount > 0) {
            $concurrency = $r->hget("task:" . $taskId, 'concurrency');
            $working_num = $r->zcard("working_queues:".$taskId);
            if ($concurrency > 0 && $working_num >= $concurrency) {
                return false;
            }
            return $this->getTask($taskId);
        } else {
            return false;
        }
    }

    function finishTask($taskId, $deviceId, $result)
    {
        $r = $this->redisLink();
        $deviceTaskId = $r->zscore("working_queues:".$taskId, $deviceId);
        if (!empty($deviceTaskId)) {
            $r->hmset("device_task:" . $deviceTaskId, [
                    "status" => 1,
                    "result" => $result,
                ]
            );
            $r->zrem("working_queues:".$taskId, $deviceId);
        }

    }

    function fetchTask($deviceId, $taskId)
    {
        $r = $this->redisLink();
        $amount = $r->get("task_amounts:" . $taskId);
        if ($amount > 0) {
            $concurrency = $r->hget("task:" . $taskId, 'concurrency');
            $working_num = $r->zcard("working_queues:" . $taskId);
            if ($concurrency > 0 && $working_num >= $concurrency) {
                return false;
            }

            $deviceTaskId = $r->zscore("working_queues:".$taskId, $deviceId);
            if (!empty($deviceTaskId)) {
                return false;
            }


            //Able to fetch a Task
            $deviceTaskId = $r->incr("device_task_id");
            $r->hmset("device_task:" . $deviceTaskId, [
                    "device_id" => $deviceId,
                    "task_id" => $taskId,
                    "status" => 0,
                ]
            );
            $r->zadd("device_tasks", [$deviceId .":". $taskId => $deviceTaskId]);
            $r->set("tasks:".$taskId, $deviceId);
            $r->zadd("working_queues:".$taskId, [$deviceId => $deviceTaskId]);
            $restAmount = $r->decr("task_amounts:" . $taskId);
            if ($restAmount == 0) {
                $r->hset("task:" . $taskId, "status", 2);
                $r->lpop("task_queue");
            }
            return $this->getTask($taskId);
        } else {
//            echo "No more task given\n";
            return false;
        }

    }


}