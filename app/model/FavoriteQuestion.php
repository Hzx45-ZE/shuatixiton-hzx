<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 收藏表 favorite_question
 */

class FavoriteQuestion extends Model
{
    protected $name = 'favorite_question';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
