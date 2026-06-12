<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 附件资源表 attachment
 */

class Attachment extends Model
{
    protected $name = 'attachment';
    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public const BUSINESS_QUESTION = 'question';
    public const BUSINESS_MATERIAL = 'material';

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getFileSizeFormatted(): string
    {
        $size = $this->file_size;
        if ($size < 1024) {
            return $size . ' B';
        } elseif ($size < 1024 * 1024) {
            return round($size / 1024, 2) . ' KB';
        } elseif ($size < 1024 * 1024 * 1024) {
            return round($size / (1024 * 1024), 2) . ' MB';
        } else {
            return round($size / (1024 * 1024 * 1024), 2) . ' GB';
        }
    }

    public function isImage(): bool
    {
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        return in_array(strtolower($this->file_ext), $imageExts);
    }
}
