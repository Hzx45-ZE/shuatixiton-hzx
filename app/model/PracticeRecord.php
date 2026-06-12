<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 练习/考试记录表 practice_record
 */

class PracticeRecord extends Model
{
    protected $name = 'practice_record';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public const TYPE_PRACTICE = 'practice';
    public const TYPE_EXAM = 'exam';

    public const STATUS_DOING = 0;
    public const STATUS_SUBMITTED = 1;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id');
    }

    public function details()
    {
        return $this->hasMany(PracticeDetail::class, 'record_id');
    }

    public function isExam(): bool
    {
        return $this->type === self::TYPE_EXAM;
    }

    public function isPractice(): bool
    {
        return $this->type === self::TYPE_PRACTICE;
    }

    public function isFinished(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function getScoreRate(): float
    {
        if (!$this->total_score || $this->total_score == 0) {
            return 0;
        }
        return round($this->score / $this->total_score * 100, 2);
    }

    public function getTimeSpentFormatted(): string
    {
        if (!$this->time_spent) {
            return '0分钟';
        }
        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;
        return $minutes . '分' . $seconds . '秒';
    }
}
