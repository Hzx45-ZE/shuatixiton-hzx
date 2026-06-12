<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 班级学生关联表 class_student
 */

class ClassStudent extends Model
{
    protected $name = 'class_student';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'join_time';
    protected $updateTime = false;

    public function classInfo()
    {
        return $this->belongsTo(ClassInfo::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
