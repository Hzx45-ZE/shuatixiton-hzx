<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 课程表 course
 */

class Course extends Model
{
    protected $name = 'course';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = 'update_time';
    protected $createTime = 'create_time';

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'course_id')->order('sort asc');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'course_id');
    }

    public function papers()
    {
        return $this->hasMany(Paper::class, 'course_id');
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class, 'course_id');
    }
}
