<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 题目标签关联表 question_tag
 */

class QuestionTag extends Model
{
    protected $name = 'question_tag';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }
}
