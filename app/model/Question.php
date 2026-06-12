<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 题目主表 question
 */

class Question extends Model
{
    protected $name = 'question';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = 'update_time';
    protected $createTime = 'create_time';

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function knowledge()
    {
        return $this->belongsTo(Knowledge::class, 'knowledge_id');
    }

    public function type()
    {
        return $this->belongsTo(QuestionType::class, 'type_id');
    }

    public function difficulty()
    {
        return $this->belongsTo(Difficulty::class, 'difficulty_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->order('sort asc');
    }

    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class, 'question_id')->order('blank_index asc');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, QuestionTag::class, 'tag_id', 'question_id');
    }

    public function paperQuestions()
    {
        return $this->hasMany(PaperQuestion::class, 'question_id');
    }

    public function practiceDetails()
    {
        return $this->hasMany(PracticeDetail::class, 'question_id');
    }

    public function errorQuestions()
    {
        return $this->hasMany(ErrorQuestion::class, 'question_id');
    }

    public function favoriteQuestions()
    {
        return $this->hasMany(FavoriteQuestion::class, 'question_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'business_id')
            ->where('business_type', 'question');
    }

    public function isChoiceType(): bool
    {
        $code = $this->type->code ?? '';
        return in_array($code, [
            QuestionType::CODE_SINGLE_CHOICE,
            QuestionType::CODE_MULTIPLE_CHOICE,
            QuestionType::CODE_JUDGMENT
        ]);
    }

    public function getCorrectOptionIds(): array
    {
        return $this->options()
            ->where('is_correct', 1)
            ->column('id');
    }
}
