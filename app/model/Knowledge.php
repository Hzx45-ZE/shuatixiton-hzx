<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 知识点表 knowledge
 */

class Knowledge extends Model
{
    protected $name = 'knowledge';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = 'update_time';
    protected $createTime = 'create_time';

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'knowledge_id');
    }
}
