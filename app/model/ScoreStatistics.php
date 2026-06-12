<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 成绩统计表 score_statistics
 */

class ScoreStatistics extends Model
{
    protected $name = 'score_statistics';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'stat_time';
    protected $updateTime = false;

    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id');
    }

    public function classInfo()
    {
        return $this->belongsTo(ClassInfo::class, 'class_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
