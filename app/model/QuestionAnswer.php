<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 填空/实操答案表 question_answer
 */

class QuestionAnswer extends Model
{
    protected $name = 'question_answer';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public const TYPE_EXACT = 'exact';
    public const TYPE_FUZZY = 'fuzzy';
    public const TYPE_RANGE = 'range';

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function checkAnswer(string $userAnswer): bool
    {
        switch ($this->answer_type) {
            case self::TYPE_EXACT:
                return $userAnswer === $this->answer_content;
            case self::TYPE_FUZZY:
                return mb_strpos($this->answer_content, $userAnswer) !== false;
            case self::TYPE_RANGE:
                $range = explode(',', $this->answer_content);
                if (count($range) === 2) {
                    $userVal = floatval($userAnswer);
                    return $userVal >= floatval($range[0]) && $userVal <= floatval($range[1]);
                }
                return false;
            default:
                return $userAnswer === $this->answer_content;
        }
    }
}
