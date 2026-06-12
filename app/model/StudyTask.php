<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 学习任务表 study_task
 */

class StudyTask extends Model
{
    protected $name = 'study_task';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = 'update_time';
    protected $createTime = 'create_time';

    public const TYPE_PRACTICE = 'practice';
    public const TYPE_EXAM = 'exam';
    public const TYPE_MATERIAL = 'material';

    public const STATUS_ONGOING = 1;
    public const STATUS_ENDED = 0;

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classInfo()
    {
        return $this->belongsTo(ClassInfo::class, 'class_id');
    }

    public function isEnded(): bool
    {
        if ($this->status === self::STATUS_ENDED) {
            return true;
        }
        if ($this->deadline && date('Y-m-d H:i:s') > $this->deadline) {
            return true;
        }
        return false;
    }

    public function paper()
    {
        return $this->belongsTo(Paper::class, 'related_id');
    }

    public function getRelatedPaper()
    {
        if (in_array($this->type, [self::TYPE_PRACTICE, self::TYPE_EXAM])) {
            return $this->paper;
        }
        return null;
    }

    public function getRelatedMaterial()
    {
        if ($this->type === self::TYPE_MATERIAL) {
            return StudyMaterial::find($this->related_id);
        }
        return null;
    }
}
