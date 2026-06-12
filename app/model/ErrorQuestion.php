<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 错题表 error_question
 */

class ErrorQuestion extends Model
{
    protected $name = 'error_question';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = 'update_time';
    protected $createTime = 'create_time';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function incrementWrongCount(): void
    {
        $this->wrong_count++;
        $this->last_wrong_time = date('Y-m-d H:i:s');
        $this->save();
    }

    public function markAsMastered(): void
    {
        $this->is_mastered = 1;
        $this->save();
    }

    public function markAsNotMastered(): void
    {
        $this->is_mastered = 0;
        $this->save();
    }
}
