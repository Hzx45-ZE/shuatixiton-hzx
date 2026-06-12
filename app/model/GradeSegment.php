<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 分数段表 grade_segment
 */

class GradeSegment extends Model
{
    protected $name = 'grade_segment';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public static function getGradeName(float $score): ?string
    {
        $segment = self::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->find();
        return $segment ? $segment->name : null;
    }

    public static function getGradeColor(float $score): ?string
    {
        $segment = self::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->find();
        return $segment ? $segment->color : null;
    }
}
