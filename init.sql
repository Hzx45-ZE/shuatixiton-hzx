-- =====================================================
-- 信息技术课刷题考试系统数据库
-- 数据库名: xxjs
-- 生成时间: 2026-05-31
-- =====================================================

-- 创建数据库
CREATE DATABASE IF NOT EXISTS `xxjs` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `xxjs`;

-- =====================================================
-- 1. 用户表
-- =====================================================
CREATE TABLE IF NOT EXISTS `user` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
    `username` VARCHAR(50) NOT NULL COMMENT '用户名/账号',
    `password` VARCHAR(255) NOT NULL COMMENT '密码',
    `real_name` VARCHAR(50) NOT NULL COMMENT '真实姓名',
    `nickname` VARCHAR(50) DEFAULT NULL COMMENT '昵称',
    `avatar` VARCHAR(200) DEFAULT NULL COMMENT '头像',
    `role` TINYINT NOT NULL COMMENT '角色：1-学生、2-教师、3-管理员',
    `gender` TINYINT DEFAULT NULL COMMENT '性别：0-未知、1-男、2-女',
    `phone` VARCHAR(20) DEFAULT NULL COMMENT '手机号',
    `email` VARCHAR(100) DEFAULT NULL COMMENT '邮箱',
    `status` TINYINT DEFAULT 1 COMMENT '状态：1-正常、0-禁用',
    `last_login_time` DATETIME DEFAULT NULL COMMENT '最后登录时间',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_username` (`username`),
    KEY `idx_role` (`role`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';

-- =====================================================
-- 2. 班级表
-- =====================================================
CREATE TABLE IF NOT EXISTS `class_info` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '班级ID',
    `name` VARCHAR(50) NOT NULL COMMENT '班级名称',
    `grade` VARCHAR(20) DEFAULT NULL COMMENT '年级',
    `major` VARCHAR(50) DEFAULT NULL COMMENT '专业',
    `teacher_id` INT UNSIGNED DEFAULT NULL COMMENT '班主任ID',
    `description` VARCHAR(255) DEFAULT NULL COMMENT '班级描述',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_teacher_id` (`teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='班级表';

-- =====================================================
-- 3. 班级学生关联表
-- =====================================================
CREATE TABLE IF NOT EXISTS `class_student` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `class_id` INT UNSIGNED NOT NULL COMMENT '班级ID',
    `student_id` INT UNSIGNED NOT NULL COMMENT '学生ID',
    `join_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '加入时间',
    `status` TINYINT DEFAULT 1 COMMENT '状态：1-在读、0-已退出',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_class_student` (`class_id`, `student_id`),
    KEY `idx_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='班级学生关联表';

-- =====================================================
-- 4. 课程表
-- =====================================================
CREATE TABLE IF NOT EXISTS `course` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '课程ID',
    `name` VARCHAR(100) NOT NULL COMMENT '课程名称',
    `code` VARCHAR(50) DEFAULT NULL COMMENT '课程编码',
    `description` TEXT DEFAULT NULL COMMENT '课程描述',
    `cover` VARCHAR(200) DEFAULT NULL COMMENT '课程封面',
    `teacher_id` INT UNSIGNED DEFAULT NULL COMMENT '授课教师ID',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `status` TINYINT DEFAULT 1 COMMENT '状态：1-正常、0-禁用',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_teacher_id` (`teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='课程表';

-- =====================================================
-- 5. 章节表
-- =====================================================
CREATE TABLE IF NOT EXISTS `chapter` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '章节ID',
    `course_id` INT UNSIGNED NOT NULL COMMENT '课程ID',
    `name` VARCHAR(100) NOT NULL COMMENT '章节名称',
    `description` TEXT DEFAULT NULL COMMENT '章节描述',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='章节表';

-- =====================================================
-- 6. 知识点表
-- =====================================================
CREATE TABLE IF NOT EXISTS `knowledge` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '知识点ID',
    `chapter_id` INT UNSIGNED NOT NULL COMMENT '章节ID',
    `name` VARCHAR(100) NOT NULL COMMENT '知识点名称',
    `description` TEXT DEFAULT NULL COMMENT '知识点描述',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_chapter_id` (`chapter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='知识点表';

-- =====================================================
-- 7. 题目类型表
-- =====================================================
CREATE TABLE IF NOT EXISTS `question_type` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL COMMENT '类型名称',
    `code` VARCHAR(20) NOT NULL COMMENT '类型编码',
    `description` VARCHAR(255) DEFAULT NULL COMMENT '类型描述',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='题目类型表';

-- =====================================================
-- 8. 难度等级表
-- =====================================================
CREATE TABLE IF NOT EXISTS `difficulty` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(20) NOT NULL COMMENT '难度名称',
    `code` VARCHAR(10) NOT NULL COMMENT '难度编码',
    `score_weight` DECIMAL(3,2) DEFAULT 1.00 COMMENT '分数权重',
    `description` VARCHAR(255) DEFAULT NULL COMMENT '难度描述',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='难度等级表';

-- =====================================================
-- 9. 题目标签表
-- =====================================================
CREATE TABLE IF NOT EXISTS `tag` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL COMMENT '标签名称',
    `type` VARCHAR(20) DEFAULT NULL COMMENT '标签类型',
    `color` VARCHAR(20) DEFAULT NULL COMMENT '标签颜色',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='题目标签表';

-- =====================================================
-- 10. 题目主表
-- =====================================================
CREATE TABLE IF NOT EXISTS `question` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '题目ID',
    `course_id` INT UNSIGNED DEFAULT NULL COMMENT '课程ID',
    `chapter_id` INT UNSIGNED DEFAULT NULL COMMENT '章节ID',
    `knowledge_id` INT UNSIGNED DEFAULT NULL COMMENT '知识点ID',
    `type_id` INT UNSIGNED NOT NULL COMMENT '题目类型ID',
    `difficulty_id` INT UNSIGNED DEFAULT NULL COMMENT '难度ID',
    `title` TEXT NOT NULL COMMENT '题目标题/题干',
    `content` TEXT DEFAULT NULL COMMENT '题目内容（富文本）',
    `analysis` TEXT DEFAULT NULL COMMENT '题目解析',
    `score` DECIMAL(5,2) DEFAULT 1.00 COMMENT '默认分值',
    `source` VARCHAR(200) DEFAULT NULL COMMENT '题目来源',
    `creator_id` INT UNSIGNED DEFAULT NULL COMMENT '创建者ID',
    `status` TINYINT DEFAULT 1 COMMENT '状态：1-正常、0-禁用',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_course_id` (`course_id`),
    KEY `idx_chapter_id` (`chapter_id`),
    KEY `idx_knowledge_id` (`knowledge_id`),
    KEY `idx_type_id` (`type_id`),
    KEY `idx_difficulty_id` (`difficulty_id`),
    KEY `idx_creator_id` (`creator_id`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='题目主表';

-- =====================================================
-- 11. 题目标签关联表
-- =====================================================
CREATE TABLE IF NOT EXISTS `question_tag` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `question_id` INT UNSIGNED NOT NULL COMMENT '题目ID',
    `tag_id` INT UNSIGNED NOT NULL COMMENT '标签ID',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_question_tag` (`question_id`, `tag_id`),
    KEY `idx_tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='题目标签关联表';

-- =====================================================
-- 12. 题目选项表（选择/判断）
-- =====================================================
CREATE TABLE IF NOT EXISTS `question_option` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `question_id` INT UNSIGNED NOT NULL COMMENT '题目ID',
    `option_key` VARCHAR(10) NOT NULL COMMENT '选项标识：A、B、C、D...',
    `option_content` TEXT NOT NULL COMMENT '选项内容',
    `is_correct` TINYINT DEFAULT 0 COMMENT '是否正确答案：1-正确、0-错误',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='题目选项表';

-- =====================================================
-- 13. 填空/实操答案表
-- =====================================================
CREATE TABLE IF NOT EXISTS `question_answer` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `question_id` INT UNSIGNED NOT NULL COMMENT '题目ID',
    `blank_index` INT DEFAULT 1 COMMENT '填空序号（第几个空）',
    `answer_content` TEXT NOT NULL COMMENT '答案内容',
    `answer_type` VARCHAR(20) DEFAULT 'exact' COMMENT '答案类型：exact-精确匹配、fuzzy-模糊匹配、range-范围',
    `score` DECIMAL(5,2) DEFAULT NULL COMMENT '该空分值',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='填空/实操答案表';

-- =====================================================
-- 14. 试卷表
-- =====================================================
CREATE TABLE IF NOT EXISTS `paper` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '试卷ID',
    `name` VARCHAR(200) NOT NULL COMMENT '试卷名称',
    `description` TEXT DEFAULT NULL COMMENT '试卷描述',
    `course_id` INT UNSIGNED DEFAULT NULL COMMENT '课程ID',
    `type` VARCHAR(20) DEFAULT 'manual' COMMENT '组卷方式：manual-手动、auto-自动',
    `total_score` DECIMAL(5,2) DEFAULT 100.00 COMMENT '试卷总分',
    `question_count` INT DEFAULT 0 COMMENT '题目数量',
    `creator_id` INT UNSIGNED DEFAULT NULL COMMENT '创建者ID',
    `status` TINYINT DEFAULT 0 COMMENT '状态：0-草稿、1-发布',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_course_id` (`course_id`),
    KEY `idx_creator_id` (`creator_id`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='试卷表';

-- =====================================================
-- 15. 试卷题目关联表
-- =====================================================
CREATE TABLE IF NOT EXISTS `paper_question` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `paper_id` INT UNSIGNED NOT NULL COMMENT '试卷ID',
    `question_id` INT UNSIGNED NOT NULL COMMENT '题目ID',
    `score` DECIMAL(5,2) DEFAULT NULL COMMENT '该题分值',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_paper_id` (`paper_id`),
    KEY `idx_question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='试卷题目关联表';

-- =====================================================
-- 16. 考试配置表
-- =====================================================
CREATE TABLE IF NOT EXISTS `exam_config` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `paper_id` INT UNSIGNED NOT NULL COMMENT '试卷ID',
    `duration` INT NOT NULL COMMENT '考试时长（分钟）',
    `pass_score` DECIMAL(5,2) DEFAULT NULL COMMENT '及格分数',
    `show_answer` TINYINT DEFAULT 0 COMMENT '考试后是否显示答案：1-是、0-否',
    `allow_retry` TINYINT DEFAULT 1 COMMENT '是否允许重考：1-是、0-否',
    `retry_count` INT DEFAULT 0 COMMENT '允许重考次数，0表示不限',
    `start_time` DATETIME DEFAULT NULL COMMENT '考试开始时间',
    `end_time` DATETIME DEFAULT NULL COMMENT '考试结束时间',
    `anti_cheat` TINYINT DEFAULT 0 COMMENT '是否开启防作弊：1-是、0-否',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_paper_id` (`paper_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='考试配置表';

-- =====================================================
-- 17. 练习/考试记录表
-- =====================================================
CREATE TABLE IF NOT EXISTS `practice_record` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '记录ID',
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
    `paper_id` INT UNSIGNED DEFAULT NULL COMMENT '试卷ID（练习时可能为空）',
    `type` VARCHAR(20) NOT NULL COMMENT '类型：practice-练习、exam-考试',
    `title` VARCHAR(200) DEFAULT NULL COMMENT '记录标题',
    `score` DECIMAL(5,2) DEFAULT NULL COMMENT '得分',
    `total_score` DECIMAL(5,2) DEFAULT NULL COMMENT '总分',
    `is_passed` TINYINT DEFAULT NULL COMMENT '是否及格：1-是、0-否',
    `ip` VARCHAR(50) DEFAULT NULL COMMENT 'IP地址',
    `time_spent` INT DEFAULT NULL COMMENT '用时（秒）',
    `start_time` DATETIME DEFAULT NULL COMMENT '开始时间',
    `submit_time` DATETIME DEFAULT NULL COMMENT '提交时间',
    `status` TINYINT DEFAULT 0 COMMENT '状态：0-进行中、1-已提交',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_paper_id` (`paper_id`),
    KEY `idx_type` (`type`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='练习/考试记录表';

-- =====================================================
-- 18. 答题详情表
-- =====================================================
CREATE TABLE IF NOT EXISTS `practice_detail` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `record_id` INT UNSIGNED NOT NULL COMMENT '记录ID',
    `question_id` INT UNSIGNED NOT NULL COMMENT '题目ID',
    `user_answer` TEXT DEFAULT NULL COMMENT '用户答案',
    `is_correct` TINYINT DEFAULT NULL COMMENT '是否正确：1-正确、0-错误',
    `score` DECIMAL(5,2) DEFAULT NULL COMMENT '得分',
    `answer_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '答题时间',
    PRIMARY KEY (`id`),
    KEY `idx_record_id` (`record_id`),
    KEY `idx_question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='答题详情表';

-- =====================================================
-- 19. 错题表
-- =====================================================
CREATE TABLE IF NOT EXISTS `error_question` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
    `question_id` INT UNSIGNED NOT NULL COMMENT '题目ID',
    `wrong_count` INT DEFAULT 1 COMMENT '错误次数',
    `last_wrong_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '最后错误时间',
    `is_mastered` TINYINT DEFAULT 0 COMMENT '是否已掌握：1-是、0-否',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_question` (`user_id`, `question_id`),
    KEY `idx_is_mastered` (`is_mastered`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='错题表';

-- =====================================================
-- 20. 收藏表
-- =====================================================
CREATE TABLE IF NOT EXISTS `favorite_question` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
    `question_id` INT UNSIGNED NOT NULL COMMENT '题目ID',
    `note` VARCHAR(255) DEFAULT NULL COMMENT '备注',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_question` (`user_id`, `question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='收藏表';

-- =====================================================
-- 21. 学习资料表
-- =====================================================
CREATE TABLE IF NOT EXISTS `study_material` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL COMMENT '资料标题',
    `description` TEXT DEFAULT NULL COMMENT '资料描述',
    `type` VARCHAR(20) NOT NULL COMMENT '资料类型：doc-文档、video-视频、link-链接',
    `course_id` INT UNSIGNED DEFAULT NULL COMMENT '课程ID',
    `chapter_id` INT UNSIGNED DEFAULT NULL COMMENT '章节ID',
    `file_path` VARCHAR(500) DEFAULT NULL COMMENT '文件路径',
    `file_size` BIGINT DEFAULT NULL COMMENT '文件大小（字节）',
    `link_url` VARCHAR(500) DEFAULT NULL COMMENT '链接地址',
    `creator_id` INT UNSIGNED DEFAULT NULL COMMENT '创建者ID',
    `view_count` INT DEFAULT 0 COMMENT '浏览次数',
    `download_count` INT DEFAULT 0 COMMENT '下载次数',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `status` TINYINT DEFAULT 1 COMMENT '状态：1-正常、0-禁用',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_course_id` (`course_id`),
    KEY `idx_chapter_id` (`chapter_id`),
    KEY `idx_type` (`type`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='学习资料表';

-- =====================================================
-- 22. 学习任务表
-- =====================================================
CREATE TABLE IF NOT EXISTS `study_task` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `teacher_id` INT UNSIGNED NOT NULL COMMENT '发布教师ID',
    `class_id` INT UNSIGNED DEFAULT NULL COMMENT '班级ID（为空则全校）',
    `title` VARCHAR(200) NOT NULL COMMENT '任务标题',
    `description` TEXT DEFAULT NULL COMMENT '任务描述',
    `type` VARCHAR(20) NOT NULL COMMENT '任务类型：practice-练习、exam-考试、material-学习资料',
    `related_id` INT UNSIGNED DEFAULT NULL COMMENT '关联ID（试卷ID或资料ID）',
    `deadline` DATETIME DEFAULT NULL COMMENT '截止时间',
    `status` TINYINT DEFAULT 1 COMMENT '状态：1-进行中、0-已结束',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_teacher_id` (`teacher_id`),
    KEY `idx_class_id` (`class_id`),
    KEY `idx_type` (`type`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='学习任务表';

-- =====================================================
-- 23. 公告通知表
-- =====================================================
CREATE TABLE IF NOT EXISTS `notice` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL COMMENT '通知标题',
    `content` TEXT NOT NULL COMMENT '通知内容',
    `type` VARCHAR(20) DEFAULT 'system' COMMENT '类型：system-系统、exam-考试',
    `target_role` VARCHAR(20) DEFAULT NULL COMMENT '目标角色：student、teacher、admin（为空则全部）',
    `target_class_id` INT UNSIGNED DEFAULT NULL COMMENT '目标班级ID',
    `publish_time` DATETIME DEFAULT NULL COMMENT '发布时间',
    `view_count` INT DEFAULT 0 COMMENT '查看次数',
    `creator_id` INT UNSIGNED DEFAULT NULL COMMENT '创建者ID',
    `status` TINYINT DEFAULT 0 COMMENT '状态：0-草稿、1-已发布',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_type` (`type`),
    KEY `idx_status` (`status`),
    KEY `idx_publish_time` (`publish_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='公告通知表';

-- =====================================================
-- 24. 成绩统计表
-- =====================================================
CREATE TABLE IF NOT EXISTS `score_statistics` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `paper_id` INT UNSIGNED NOT NULL COMMENT '试卷ID',
    `class_id` INT UNSIGNED DEFAULT NULL COMMENT '班级ID',
    `course_id` INT UNSIGNED DEFAULT NULL COMMENT '课程ID',
    `avg_score` DECIMAL(5,2) DEFAULT NULL COMMENT '平均分',
    `max_score` DECIMAL(5,2) DEFAULT NULL COMMENT '最高分',
    `min_score` DECIMAL(5,2) DEFAULT NULL COMMENT '最低分',
    `pass_rate` DECIMAL(5,2) DEFAULT NULL COMMENT '及格率',
    `excellent_rate` DECIMAL(5,2) DEFAULT NULL COMMENT '优秀率',
    `student_count` INT DEFAULT 0 COMMENT '参考人数',
    `stat_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '统计时间',
    PRIMARY KEY (`id`),
    KEY `idx_paper_id` (`paper_id`),
    KEY `idx_class_id` (`class_id`),
    KEY `idx_course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='成绩统计表';

-- =====================================================
-- 25. 分数段表
-- =====================================================
CREATE TABLE IF NOT EXISTS `grade_segment` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(20) NOT NULL COMMENT '等级名称',
    `min_score` DECIMAL(5,2) NOT NULL COMMENT '最低分',
    `max_score` DECIMAL(5,2) NOT NULL COMMENT '最高分',
    `color` VARCHAR(10) DEFAULT NULL COMMENT '颜色标识',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='分数段表';

-- =====================================================
-- 26. 附件资源表
-- =====================================================
CREATE TABLE IF NOT EXISTS `attachment` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `business_type` VARCHAR(50) NOT NULL COMMENT '业务类型：question、material',
    `business_id` INT UNSIGNED NOT NULL COMMENT '业务ID',
    `file_name` VARCHAR(200) NOT NULL COMMENT '文件名',
    `file_path` VARCHAR(500) NOT NULL COMMENT '文件路径',
    `file_size` BIGINT DEFAULT NULL COMMENT '文件大小（字节）',
    `file_type` VARCHAR(50) DEFAULT NULL COMMENT '文件类型（MIME）',
    `file_ext` VARCHAR(20) DEFAULT NULL COMMENT '文件扩展名',
    `creator_id` INT UNSIGNED DEFAULT NULL COMMENT '上传者ID',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_business` (`business_type`, `business_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='附件资源表';

-- =====================================================
-- 27. 系统日志表
-- =====================================================
CREATE TABLE IF NOT EXISTS `system_log` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED DEFAULT NULL COMMENT '用户ID',
    `module` VARCHAR(50) DEFAULT NULL COMMENT '模块',
    `action` VARCHAR(50) DEFAULT NULL COMMENT '操作',
    `method` VARCHAR(20) DEFAULT NULL COMMENT '请求方式',
    `url` VARCHAR(500) DEFAULT NULL COMMENT '请求URL',
    `params` TEXT DEFAULT NULL COMMENT '请求参数',
    `ip` VARCHAR(50) DEFAULT NULL COMMENT 'IP地址',
    `user_agent` VARCHAR(500) DEFAULT NULL COMMENT '用户代理',
    `response_code` INT DEFAULT NULL COMMENT '响应状态码',
    `error_msg` TEXT DEFAULT NULL COMMENT '错误信息',
    `execution_time` INT DEFAULT NULL COMMENT '执行时间（毫秒）',
    `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_module` (`module`),
    KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统日志表';

-- =====================================================
-- 初始化数据
-- =====================================================

-- 初始化题目类型
INSERT INTO `question_type` (`name`, `code`, `description`, `sort`) VALUES
('单选题', 'single_choice', '单选题', 1),
('多选题', 'multiple_choice', '多选题', 2),
('判断题', 'judgment', '判断题', 3),
('填空题', 'fill_blank', '填空题', 4),
('简答题', 'short_answer', '简答题', 5);

-- 初始化难度等级
INSERT INTO `difficulty` (`name`, `code`, `score_weight`, `description`, `sort`) VALUES
('简单', 'easy', 0.80, '简单题', 1),
('中等', 'medium', 1.00, '中等题', 2),
('困难', 'hard', 1.20, '困难题', 3);

-- 初始化分数段
INSERT INTO `grade_segment` (`name`, `min_score`, `max_score`, `color`, `sort`) VALUES
('不及格', 0, 59.99, '#FF4D4F', 1),
('及格', 60, 74.99, '#FA8C16', 2),
('良好', 75, 89.99, '#FAAD14', 3),
('优秀', 90, 100, '#52C41A', 4);

-- 初始化题目标签
INSERT INTO `tag` (`name`, `type`, `color`) VALUES
('高频考点', 'knowledge', '#1890FF'),
('易错点', 'knowledge', '#FA8C16'),
('必考题', 'knowledge', '#F5222D'),
('拓展题', 'knowledge', '#13C2C2'),
('真题', 'source', '#722ED1'),
('模拟题', 'source', '#1890FF');

-- 初始化管理员账号（密码：admin123，实际使用时请修改）
INSERT INTO `user` (`username`, `password`, `real_name`, `role`, `status`) VALUES
('admin', '$2y$10$DflOeekZU7.PuElaDd.oEenhS/wnpFYAiOhh6MCN5MjidjkBTajZm', '管理员', 3, 1);

-- =====================================================
-- Session 表（用于Session数据库存储）
-- =====================================================
CREATE TABLE IF NOT EXISTS `session` (
    `id` VARCHAR(255) NOT NULL COMMENT 'Session ID',
    `data` TEXT DEFAULT NULL COMMENT 'Session数据',
    `expire` INT DEFAULT 0 COMMENT '过期时间戳',
    PRIMARY KEY (`id`),
    KEY `idx_expire` (`expire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Session表';

