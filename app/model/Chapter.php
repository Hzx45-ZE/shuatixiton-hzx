<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 章节表 chapter
 */

class Chapter extends Model
{
    protected $name = 'chapter';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = 'update_time';
    protected $createTime = 'create_time';

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function knowledges()
    {
        return $this->hasMany(Knowledge::class, 'chapter_id')->order('sort asc');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'chapter_id');
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class, 'chapter_id');
    }
}
