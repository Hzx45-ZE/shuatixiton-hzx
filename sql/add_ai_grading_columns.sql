ALTER TABLE `practice_detail` 
ADD COLUMN `ai_score` DECIMAL(5,2) NULL DEFAULT NULL COMMENT 'AI参考分数',
ADD COLUMN `ai_comment` TEXT NULL COMMENT 'AI批改评语',
ADD COLUMN `final_score` DECIMAL(5,2) NULL DEFAULT NULL COMMENT '教师最终打分',
ADD COLUMN `grade_status` VARCHAR(20) NULL DEFAULT NULL COMMENT '批改状态: pending/reviewed';