<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 公告通知表 notice
 */

class Notice extends Model
{
    protected $name = 'notice';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = 'update_time';
    protected $createTime = 'create_time';

    public const TYPE_SYSTEM = 'system';
    public const TYPE_EXAM = 'exam';

    public const STATUS_DRAFT = 0;
    public const STATUS_PUBLISHED = 1;

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function classInfo()
    {
        return $this->belongsTo(ClassInfo::class, 'target_class_id');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function incrementViewCount(): void
    {
        $this->view_count++;
        $this->save();
    }
}
