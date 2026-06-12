<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 答题详情表 practice_detail
 */

class PracticeDetail extends Model
{
    protected $name = 'practice_detail';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'answer_time';
    protected $updateTime = false;

    protected $schema = [
        'id'             => 'int',
        'record_id'      => 'int',
        'question_id'    => 'int',
        'user_answer'    => 'text',
        'is_correct'     => 'int',
        'score'          => 'decimal',
        'question_type'  => 'varchar',
        'ai_score'       => 'decimal',
        'ai_comment'     => 'text',
        'final_score'    => 'decimal',
        'grade_status'   => 'varchar',
        'answer_time'    => 'datetime',
    ];

    public function record()
    {
        return $this->belongsTo(PracticeRecord::class, 'record_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function isCorrect(): bool
    {
        return $this->is_correct === 1;
    }
}
