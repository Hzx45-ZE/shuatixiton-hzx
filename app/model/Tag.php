<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 题目标签表 tag
 */

class Tag extends Model
{
    protected $name = 'tag';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public function questions()
    {
        return $this->belongsToMany(Question::class, QuestionTag::class, 'question_id', 'tag_id');
    }

    public function questionTags()
    {
        return $this->hasMany(QuestionTag::class, 'tag_id');
    }
}
