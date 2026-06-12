<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 试卷表 paper
 */

class Paper extends Model
{
    protected $name = 'paper';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = 'update_time';
    protected $createTime = 'create_time';

    public const TYPE_MANUAL = 'manual';
    public const TYPE_AUTO = 'auto';

    public const STATUS_DRAFT = 0;
    public const STATUS_PUBLISHED = 1;

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, PaperQuestion::class, 'question_id', 'paper_id')
            ->order('paper_question.sort');
    }

    public function paperQuestions()
    {
        return $this->hasMany(PaperQuestion::class, 'paper_id');
    }

    public function examConfig()
    {
        return $this->hasOne(ExamConfig::class, 'paper_id');
    }

    public function practiceRecords()
    {
        return $this->hasMany(PracticeRecord::class, 'paper_id');
    }

    public function scoreStatistics()
    {
        return $this->hasMany(ScoreStatistics::class, 'paper_id');
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }
}
