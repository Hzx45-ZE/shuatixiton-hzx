<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 班级表 class_info
 */

class ClassInfo extends Model
{
    protected $name = 'class_info';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = 'update_time';
    protected $createTime = 'create_time';

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, ClassStudent::class, 'student_id', 'class_id');
    }

    public function classStudents()
    {
        return $this->hasMany(ClassStudent::class, 'class_id');
    }

    public function studyTasks()
    {
        return $this->hasMany(StudyTask::class, 'class_id');
    }
}
