<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 题目选项表 question_option
 */

class QuestionOption extends Model
{
    protected $name = 'question_option';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function isCorrect(): bool
    {
        return $this->is_correct === 1;
    }
}
