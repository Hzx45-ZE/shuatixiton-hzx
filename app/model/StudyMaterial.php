<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 学习资料表 study_material
 */

class StudyMaterial extends Model
{
    protected $name = 'study_material';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = 'update_time';
    protected $createTime = 'create_time';

    public const TYPE_DOC = 'doc';
    public const TYPE_VIDEO = 'video';
    public const TYPE_LINK = 'link';

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'business_id')
            ->where('business_type', 'material');
    }

    public function incrementViewCount(): void
    {
        $this->view_count++;
        $this->save();
    }

    public function incrementDownloadCount(): void
    {
        $this->download_count++;
        $this->save();
    }
}
