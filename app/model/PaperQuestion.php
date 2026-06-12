<?php
declare(strict_types=1);

namespace app\model;

use think\model\Pivot;

/**
 * 试卷题目关联表 paper_question
 */

class PaperQuestion extends Pivot
{
    protected $name = 'paper_question';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
