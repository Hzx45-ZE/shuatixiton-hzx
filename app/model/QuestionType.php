<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 题目类型表 question_type
 */

class QuestionType extends Model
{
    protected $name = 'question_type';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public const CODE_SINGLE_CHOICE = 'single_choice';
    public const CODE_MULTIPLE_CHOICE = 'multiple_choice';
    public const CODE_JUDGMENT = 'judgment';
    public const CODE_FILL_BLANK = 'fill_blank';
    public const CODE_PRACTICE = 'practice';
    public const CODE_PROGRAMMING = 'programming';

    public function questions()
    {
        return $this->hasMany(Question::class, 'type_id');
    }
}
