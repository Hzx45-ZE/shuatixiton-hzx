<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 考试配置表 exam_config
 */

class ExamConfig extends Model
{
    protected $name = 'exam_config';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = 'update_time';
    protected $createTime = 'create_time';

    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id');
    }

    public function isOpen(): bool
    {
        $now = date('Y-m-d H:i:s');
        $startTime = $this->start_time;
        $endTime = $this->end_time;

        if ($startTime && $now < $startTime) {
            return false;
        }
        if ($endTime && $now > $endTime) {
            return false;
        }
        return true;
    }

    public function canRetry(int $attemptCount): bool
    {
        if (!$this->allow_retry) {
            return false;
        }
        if ($this->retry_count === 0) {
            return true;
        }
        return $attemptCount < $this->retry_count;
    }
}
