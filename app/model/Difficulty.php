<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 难度等级表 difficulty
 */

class Difficulty extends Model
{
    protected $name = 'difficulty';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public const CODE_EASY = 'easy';
    public const CODE_MEDIUM = 'medium';
    public const CODE_HARD = 'hard';

    public function questions()
    {
        return $this->hasMany(Question::class, 'difficulty_id');
    }
}
