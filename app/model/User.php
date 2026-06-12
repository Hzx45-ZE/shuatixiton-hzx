<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 用户表 user
 */

class User extends Model
{
    protected $name = 'user';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = 'update_time';
    protected $createTime = 'create_time';

    protected $hidden = ['password'];

    public function setPasswordAttr($value): string
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function classes()
    {
        return $this->belongsToMany(ClassInfo::class, ClassStudent::class, 'class_id', 'student_id');
    }

    public function teachingCourses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function practiceRecords()
    {
        return $this->hasMany(PracticeRecord::class, 'user_id');
    }

    public function errorQuestions()
    {
        return $this->hasMany(ErrorQuestion::class, 'user_id');
    }

    public function favoriteQuestions()
    {
        return $this->hasMany(FavoriteQuestion::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 3;
    }

    public function isTeacher(): bool
    {
        return $this->role === 2;
    }

    public function isStudent(): bool
    {
        return $this->role === 1;
    }
}
