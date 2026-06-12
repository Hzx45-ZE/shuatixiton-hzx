<?php /*a:1:{s:73:"D:\phpstudy\phpstudy_pro\WWW\xxjsdati.com\app\view\teacher\paper_add.html";i:1781226406;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新建试卷 - 信息技术课刷题考试系统</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Microsoft YaHei', 'PingFang SC', sans-serif; background: #f5f7fa; }
        
        .header {
            background: white;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 0 20px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .header .logo { font-size: 18px; font-weight: 600; color: #667eea; }
        
        .header .nav { display: flex; gap: 30px; }
        .header .nav a {
            text-decoration: none;
            color: #666;
            font-size: 14px;
            padding: 0 10px;
            line-height: 60px;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .header .nav a:hover, .header .nav a.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        
        .header .user { display: flex; align-items: center; gap: 15px; }
        .header .user .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none;
            transition: transform 0.3s ease;
        }
        .header .user .user-avatar:hover { transform: scale(1.1); }
        .header .user .user-name {
            font-size: 14px; color: #333; cursor: pointer; text-decoration: none;
        }
        .header .user .user-name:hover { color: #667eea; }
        .header .user span { font-size: 14px; color: #333; }
        .header .user a { text-decoration: none; color: #666; font-size: 14px; }
        
        .main-content { padding: 20px; max-width: 900px; margin: 0 auto; }
        
        .tab-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 10px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            background: white;
            color: #666;
            transition: all 0.3s ease;
        }
        
        .tab.active { background: #667eea; color: white; }
        
        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 25px;
        }
        
        .form-group { margin-bottom: 20px; }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }
        
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: #667eea;
        }
        
        .form-group textarea { resize: vertical; min-height: 80px; }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        .filter-section {
            background: #fafafa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .filter-row { display: flex; gap: 20px; flex-wrap: wrap; }
        
        .filter-group { display: flex; align-items: center; gap: 10px; }
        
        .filter-group label { font-size: 13px; color: #666; }
        
        .filter-group select, .filter-group input {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 13px;
        }
        
        .question-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }
        
        .question-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        
        .question-item:hover { background: #fafafa; }
        
        .question-item.selected { background: #f0f5ff; }
        
        .question-item .checkbox {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            cursor: pointer;
        }
        
        .question-item .info {
            flex: 1;
        }
        
        .question-item .info .title {
            font-size: 14px;
            color: #333;
            margin-bottom: 4px;
        }
        
        .question-item .info .meta {
            font-size: 12px;
            color: #999;
        }
        
        .question-item .score-input {
            width: 60px;
            padding: 6px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            text-align: center;
            font-size: 13px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn.primary { background: #667eea; color: white; }
        .btn.primary:hover { background: #5a6fd6; }
        
        .btn.secondary { background: #f5f5f5; color: #666; }
        
        .btn-group { display: flex; gap: 15px; justify-content: flex-end; }
        
        .selected-count {
            display: inline-block;
            padding: 8px 16px;
            background: #f0f5ff;
            color: #667eea;
            border-radius: 20px;
            font-size: 13px;
            margin-bottom: 15px;
        }
        
        .empty {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            align-items: center; justify-content: center;
            z-index: 1000;
        }
        .modal.show { display: flex; }
        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .modal-content h3 { margin-bottom: 20px; font-size: 18px; color: #333; }
        .modal-content .form-group { margin-bottom: 15px; }
        .modal-content .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            color: #666;
        }
        .modal-content .form-group input,
        .modal-content .form-group select,
        .modal-content .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 13px;
            outline: none;
        }
        .modal-content .form-group input:focus,
        .modal-content .form-group select:focus,
        .modal-content .form-group textarea:focus {
            border-color: #667eea;
        }
        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .option-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }
        .option-group label {
            width: 20px;
            font-weight: 600;
            color: #667eea;
        }
        .option-group input {
            flex: 1;
            padding: 6px 10px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 13px;
        }
        .alert {
            display: none;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 13px;
        }
        .alert.success { background: #f6ffed; color: #52c41a; border: 1px solid #b7eb8f; }
        .alert.error { background: #fff2f0; color: #ff4d4f; border: 1px solid #ffccc7; }
        .btn.small { padding: 6px 14px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; }
        .btn.create { background: #52c41a; color: white; }
        .btn.create:hover { background: #45a614; }
        .btn.cancel { background: #f5f5f5; color: #666; }
        .btn.confirm { background: #667eea; color: white; }
        .btn.confirm:hover { background: #5a6fd6; }
        .type-switch { display: none; }
        .type-switch.show { display: block; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">📚 刷题考试系统</div>
        <div class="nav">
            <a href="/teacher">首页</a>
            <a href="/teacher/courses">课程管理</a>
            <a href="/teacher/classes">班级管理</a>
            <a href="/teacher/students">学生管理</a>
            <a href="/teacher/tasks">任务布置</a>
            <a href="/teacher/papers" class="active">试卷管理</a>
            <a href="/teacher/statistics">学情分析</a>
        </div>
        
        <div class="user">
            <a href="/teacher/profile" class="user-avatar"><?php echo htmlentities((string) mb_substr($user['real_name'],0,1)); ?></a>
            <a href="/teacher/profile" class="user-name"><?php echo htmlentities((string) $user['real_name']); ?></a>
            <a href="/logout">退出</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="tab-bar">
            <button class="tab active" onclick="switchMode('manual')">手动组卷</button>
            <button class="tab" onclick="switchMode('auto')">自动组卷</button>
        </div>
        
        <form id="paperForm" class="form-card">
            <div class="form-row">
                <div class="form-group">
                    <label>试卷名称 *</label>
                    <input type="text" name="name" placeholder="请输入试卷名称" required>
                </div>
                <div class="form-group">
                    <label>总分</label>
                    <input type="number" name="total_score" value="100" min="1">
                </div>
            </div>
            
            <div class="form-group">
                <label>关联课程</label>
                <select name="course_id">
                    <option value="">不关联课程</option>
                    <?php if(is_array($courses) || $courses instanceof \think\Collection || $courses instanceof \think\Paginator): $i = 0; $__LIST__ = $courses;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$course): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo htmlentities((string) $course['id']); ?>"><?php echo htmlentities((string) $course['name']); ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>试卷描述</label>
                <textarea name="description" placeholder="请输入试卷描述（可选）"></textarea>
            </div>
            
            <input type="hidden" name="type" value="manual">
            
            <div id="manualPanel">
                <div class="filter-section">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>课程：</label>
                            <select id="filterCourse" onchange="loadChaptersForFilter()">
                                <option value="">全部课程</option>
                                <?php if(is_array($courses) || $courses instanceof \think\Collection || $courses instanceof \think\Paginator): $i = 0; $__LIST__ = $courses;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$course): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo htmlentities((string) $course['id']); ?>"><?php echo htmlentities((string) $course['name']); ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>章节：</label>
                            <select id="filterChapter">
                                <option value="">全部章节</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>题型：</label>
                            <select id="filterType">
                                <option value="">全部题型</option>
                                <?php if(is_array($types) || $types instanceof \think\Collection || $types instanceof \think\Paginator): $i = 0; $__LIST__ = $types;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo htmlentities((string) $type['id']); ?>"><?php echo htmlentities((string) $type['name']); ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>难度：</label>
                            <select id="filterDifficulty">
                                <option value="">全部难度</option>
                                <?php if(is_array($difficulties) || $difficulties instanceof \think\Collection || $difficulties instanceof \think\Paginator): $i = 0; $__LIST__ = $difficulties;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$diff): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo htmlentities((string) $diff['id']); ?>"><?php echo htmlentities((string) $diff['name']); ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>知识点：</label>
                            <select id="filterKnowledge">
                                <option value="">全部知识点</option>
                                <?php if(is_array($knowledges) || $knowledges instanceof \think\Collection || $knowledges instanceof \think\Paginator): $i = 0; $__LIST__ = $knowledges;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$k): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo htmlentities((string) $k['id']); ?>"><?php echo htmlentities((string) $k['name']); ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>搜索：</label>
                            <input type="text" id="filterKeyword" placeholder="搜索题目">
                        </div>
                        <button type="button" class="btn secondary" onclick="loadQuestions()">筛选</button>
                        <button type="button" class="btn small create" onclick="openQuickCreate()">+ 快速创建题目</button>
                    </div>
                </div>
                
                <div class="selected-count" id="selectedCount">已选择 0 道题目</div>
                
                <div class="question-list" id="questionList">
                    <div class="empty">点击筛选按钮加载题目列表</div>
                </div>
            </div>
            
            <div id="autoPanel" style="display: none;">
                <div class="form-row">
                    <div class="form-group">
                        <label>题目总数 <span style="color:#999;font-weight:400;" id="autoTotalCount">(共0题)</span></label>
                        <input type="hidden" name="question_count" value="0">
                        <div style="padding:12px 15px;border:1px solid #e0e0e0;border-radius:8px;font-size:14px;color:#667eea;background:#f8f9ff;" id="autoTotalDisplay">请先勾选题型并设置题目数量</div>
                    </div>
                </div>
                
                <div class="filter-section">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>题型：</label>
                            <div class="checkbox-group" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
                                <label style="display:flex;align-items:center;gap:4px;cursor:pointer;font-size:13px;">
                                    <input type="checkbox" value="all" onchange="toggleAllTypes(this)" checked> 全部
                                </label>
                                <?php if(is_array($types) || $types instanceof \think\Collection || $types instanceof \think\Paginator): $i = 0; $__LIST__ = $types;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i;?>
                                <label style="display:flex;align-items:center;gap:4px;cursor:pointer;font-size:13px;">
                                    <input type="checkbox" name="type_ids[]" value="<?php echo htmlentities((string) $type['id']); ?>" onchange="onTypeChange(this)"> <?php echo htmlentities((string) $type['name']); ?>
                                    <input type="number" name="type_counts[<?php echo htmlentities((string) $type['id']); ?>]" value="3" min="1" max="50" style="width:65px;margin-left:4px;" placeholder="题数"> 题
                                    <input type="number" name="type_scores[<?php echo htmlentities((string) $type['id']); ?>]" value="5" min="1" max="100" style="width:65px;margin-left:4px;" placeholder="分值"> 分
                                </label>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </div>
                        </div>
                        <div class="filter-group">
                            <label>难度：</label>
                            <select name="difficulty_id">
                                <option value="">全部难度</option>
                                <?php if(is_array($difficulties) || $difficulties instanceof \think\Collection || $difficulties instanceof \think\Paginator): $i = 0; $__LIST__ = $difficulties;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$diff): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo htmlentities((string) $diff['id']); ?>"><?php echo htmlentities((string) $diff['name']); ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>知识点：</label>
                            <select name="knowledge_id">
                                <option value="">全部知识点</option>
                                <?php if(is_array($knowledges) || $knowledges instanceof \think\Collection || $knowledges instanceof \think\Paginator): $i = 0; $__LIST__ = $knowledges;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$k): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo htmlentities((string) $k['id']); ?>"><?php echo htmlentities((string) $k['name']); ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <p style="color: #667eea; font-size: 13px; margin-top: 10px;">
                    系统将调用AI为每种题型生成全新的题目，每张试卷题目均不重复
                </p>
            </div>
            
            <div class="btn-group">
                <button type="button" class="btn secondary" onclick="location.href='/teacher/papers'">取消</button>
                <button type="submit" class="btn primary">创建试卷</button>
            </div>
        </form>
    </div>

    <div class="modal" id="quickCreateModal">
        <div class="modal-content">
            <div class="alert" id="quickAlert"></div>
            <h3>快速创建题目</h3>
            <form id="quickCreateForm">
                <div class="form-group">
                    <label>题干 *</label>
                    <textarea name="title" rows="3" placeholder="请输入题目标题"></textarea>
                </div>
                <div class="form-group">
                    <label>题型 *</label>
                    <select name="type_id" onchange="switchQuickType(this)">
                        <?php if(is_array($types) || $types instanceof \think\Collection || $types instanceof \think\Paginator): $i = 0; $__LIST__ = $types;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$t): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo htmlentities((string) $t['id']); ?>" data-code="<?php echo htmlentities((string) $t['code']); ?>"><?php echo htmlentities((string) $t['name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>难度</label>
                    <select name="difficulty_id">
                        <?php if(is_array($difficulties) || $difficulties instanceof \think\Collection || $difficulties instanceof \think\Paginator): $i = 0; $__LIST__ = $difficulties;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$d): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo htmlentities((string) $d['id']); ?>"><?php echo htmlentities((string) $d['name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>所属课程</label>
                    <select name="course_id" onchange="loadQuickChapters(this.value)">
                        <option value="">无</option>
                        <?php if(is_array($courses) || $courses instanceof \think\Collection || $courses instanceof \think\Paginator): $i = 0; $__LIST__ = $courses;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo htmlentities((string) $c['id']); ?>"><?php echo htmlentities((string) $c['name']); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>所属章节</label>
                    <select name="chapter_id">
                        <option value="">无</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>分值</label>
                    <input type="number" name="score" value="1" min="1">
                </div>

                <div class="type-switch show" id="quickChoiceOptions">
                    <div class="form-group">
                        <label>选项列表</label>
                        <div class="option-group">
                            <label>A</label>
                            <input type="text" name="options[]" placeholder="选项A">
                        </div>
                        <div class="option-group">
                            <label>B</label>
                            <input type="text" name="options[]" placeholder="选项B">
                        </div>
                        <div class="option-group">
                            <label>C</label>
                            <input type="text" name="options[]" placeholder="选项C">
                        </div>
                        <div class="option-group">
                            <label>D</label>
                            <input type="text" name="options[]" placeholder="选项D">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>正确答案（多选可多选）</label>
                        <select name="answer[]" multiple style="min-height: 60px;">
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                </div>

                <div class="type-switch" id="quickFillBlank">
                    <div class="form-group">
                        <label>正确答案（多个填空用分号分隔）</label>
                        <input type="text" name="answer" placeholder="如：答案1;答案2">
                    </div>
                </div>

                <div class="type-switch" id="quickShortAnswer">
                    <div class="form-group">
                        <label>参考答案</label>
                        <textarea name="answer" rows="2" placeholder="请输入参考答案"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label>题目解析</label>
                    <textarea name="analysis" rows="2" placeholder="请输入题目解析"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn small cancel" onclick="closeQuickCreate()">取消</button>
                    <button type="submit" class="btn small confirm">确认保存</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        const selectedQuestions = [];

        function switchMode(mode) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');

            document.getElementById('manualPanel').style.display = mode === 'manual' ? 'block' : 'none';
            document.getElementById('autoPanel').style.display = mode === 'auto' ? 'block' : 'none';
            document.querySelector('input[name="type"]').value = mode;

            if (mode === 'auto') {
                loadAutoQuestions();
            }
        }

        function toggleAllTypes(el) {
            var checkboxes = document.querySelectorAll('input[name="type_ids[]"]');
            checkboxes.forEach(function(cb) { cb.checked = el.checked; });
            updateAutoTotal();
        }

        function onTypeChange(el) {
            var allCheckbox = document.querySelector('input[value="all"]');
            var checkboxes = document.querySelectorAll('input[name="type_ids[]"]');
            var allChecked = true;
            checkboxes.forEach(function(cb) { if (!cb.checked) allChecked = false; });
            allCheckbox.checked = allChecked;
            updateAutoTotal();
        }
        
        function updateAutoTotal() {
            var total = 0;
            var checkboxes = document.querySelectorAll('input[name="type_ids[]"]');
            checkboxes.forEach(function(cb) {
                if (cb.checked) {
                    var countInput = cb.parentElement.querySelector('input[name^="type_counts["]');
                    if (countInput) {
                        total += parseInt(countInput.value) || 0;
                    }
                }
            });
            document.querySelector('input[name="question_count"]').value = total;
            document.getElementById('autoTotalCount').textContent = '(共' + total + '题)';
            var display = document.getElementById('autoTotalDisplay');
            if (total > 0) {
                display.textContent = '将生成 ' + total + ' 道题目';
            } else {
                display.textContent = '请先勾选题型并设置题目数量';
            }
        }
        
        // 监听题目数量变化
        document.getElementById('autoPanel').addEventListener('input', function(e) {
            if (e.target.name && e.target.name.startsWith('type_counts[')) {
                updateAutoTotal();
            }
        });

        function loadChaptersForFilter() {
            const courseId = document.getElementById('filterCourse').value;
            const chapterSelect = document.getElementById('filterChapter');
            chapterSelect.innerHTML = '<option value="">全部章节</option>';
            if (!courseId) return;

            fetch('/teacher/papers/getChapters?course_id=' + courseId)
            .then(res => res.json())
            .then(result => {
                if (result.code === 1 && result.data) {
                    result.data.forEach(chapter => {
                        chapterSelect.innerHTML += `<option value="${chapter.id}">${chapter.name}</option>`;
                    });
                }
            });
        }

        function loadQuestions() {
            const type = document.getElementById('filterType').value;
            const difficulty = document.getElementById('filterDifficulty').value;
            const knowledge = document.getElementById('filterKnowledge').value;
            const chapter = document.getElementById('filterChapter').value;
            const keyword = document.getElementById('filterKeyword').value;

            fetch(`/teacher/papers/questions?type_id=${type}&difficulty_id=${difficulty}&knowledge_id=${knowledge}&chapter_id=${chapter}&keyword=${encodeURIComponent(keyword)}`)
            .then(res => res.json())
            .then(data => {
                if (data.code === 1) {
                    renderQuestionList(data.data);
                }
            });
        }

        function renderQuestionList(questions) {
            const list = document.getElementById('questionList');

            if (!questions.length) {
                list.innerHTML = '<div class="empty">暂无符合条件的题目，请点击"快速创建题目"添加</div>';
                return;
            }

            list.innerHTML = questions.map(q => `
                <div class="question-item" data-id="${q.id}">
                    <input type="checkbox" class="checkbox" onchange="toggleQuestion(this, ${q.id})" ${selectedQuestions.includes(q.id) ? 'checked' : ''}>
                    <div class="info">
                        <div class="title">${q.title}</div>
                        <div class="meta">${q.type_name} | ${q.difficulty_name}</div>
                    </div>
                    <input type="number" class="score-input" value="${q.score}" onchange="updateScore(${q.id}, this.value)" placeholder="分值">
                </div>
            `).join('');
        }

        function toggleQuestion(checkbox, qId) {
            const index = selectedQuestions.indexOf(qId);
            if (checkbox.checked) {
                if (index === -1) selectedQuestions.push(qId);
            } else {
                if (index !== -1) selectedQuestions.splice(index, 1);
            }
            updateSelectedCount();
        }

        function updateScore(qId, score) {
        }

        function updateSelectedCount() {
            document.getElementById('selectedCount').textContent = `已选择 ${selectedQuestions.length} 道题目`;
        }

        document.getElementById('paperForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const mode = document.querySelector('input[name="type"]').value;

            if (mode === 'manual' && selectedQuestions.length === 0) {
                alert('请至少选择一道题目');
                return;
            }
            
            if (mode === 'auto') {
                var total = parseInt(document.querySelector('input[name="question_count"]').value) || 0;
                if (total <= 0) {
                    alert('请选择至少一种题型并设置题目数量');
                    return;
                }
                if (total > 50) {
                    alert('题目总数不能超过50道');
                    return;
                }
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = '正在生成试卷...';

            const formData = new FormData(this);

            // 手动序列化以确保数组参数正确传递
            const params = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                params.append(key, value);
            }

            if (mode === 'manual') {
                const scores = [];
                document.querySelectorAll('.question-item input[type="number"]').forEach(input => {
                    if (input.value) scores.push(input.value);
                });
                params.append('question_ids', JSON.stringify(selectedQuestions));
                params.append('scores', JSON.stringify(scores));
            }

            fetch('/teacher/papers/add', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: params
            })
            .then(function(res) {
                // 检查 Content-Type 是否为 JSON
                var contentType = res.headers.get('Content-Type') || '';
                if (contentType.indexOf('application/json') === -1) {
                    return res.text().then(function(text) {
                        throw new Error('服务器返回了非 JSON 响应（可能是超时或错误），请查看 runtime/ai_generate_log.txt 日志');
                    });
                }
                return res.json();
            })
            .then(data => {
                if (data.code === 1) {
                    alert(data.msg);
                    location.href = data.url;
                } else {
                    alert(data.msg);
                    submitBtn.disabled = false;
                    submitBtn.textContent = '创建试卷';
                }
            })
            .catch(err => {
                var msg = err.message || '未知错误';
                // 去掉 "Unexpected token" 这类对用户无意义的信息
                if (msg.indexOf('is not valid JSON') > -1 || msg.indexOf('Unexpected token') > -1) {
                    msg = '服务器返回异常，可能因为 AI 出题超时。请减少题目数量后重试，或查看 runtime/ai_generate_log.txt 日志。';
                }
                alert('请求失败：' + msg);
                submitBtn.disabled = false;
                submitBtn.textContent = '创建试卷';
            });
        });

        function loadAutoQuestions() {
        }

        function openQuickCreate() {
            document.getElementById('quickAlert').style.display = 'none';
            document.getElementById('quickCreateForm').reset();
            document.getElementById('quickCreateModal').classList.add('show');
            switchQuickType(document.querySelector('#quickCreateForm select[name="type_id"]'));
        }

        function closeQuickCreate() {
            document.getElementById('quickCreateModal').classList.remove('show');
        }

        function switchQuickType(select) {
            const code = select.options[select.selectedIndex].dataset.code;
            document.querySelectorAll('#quickCreateModal .type-switch').forEach(el => el.classList.remove('show'));
            if (['single_choice', 'multiple_choice', 'judgment'].includes(code)) {
                document.getElementById('quickChoiceOptions').classList.add('show');
            } else if (code === 'fill_blank') {
                document.getElementById('quickFillBlank').classList.add('show');
            } else {
                document.getElementById('quickShortAnswer').classList.add('show');
            }
        }

        function loadQuickChapters(courseId) {
            const chapterSelect = document.querySelector('#quickCreateForm select[name="chapter_id"]');
            chapterSelect.innerHTML = '<option value="">无</option>';
            if (!courseId) return;

            fetch('/teacher/papers/getChapters?course_id=' + courseId)
            .then(res => res.json())
            .then(result => {
                if (result.code === 1 && result.data) {
                    result.data.forEach(chapter => {
                        chapterSelect.innerHTML += `<option value="${chapter.id}">${chapter.name}</option>`;
                    });
                }
            });
        }

        function showQuickAlert(msg, type) {
            const alert = document.getElementById('quickAlert');
            alert.textContent = msg;
            alert.className = 'alert ' + type;
            alert.style.display = 'block';
        }

        document.getElementById('quickCreateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('/teacher/papers/quickSave', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                if (result.code === 1) {
                    showQuickAlert(result.msg, 'success');
                    const newQuestion = result.data;
                    selectedQuestions.push(newQuestion.id);
                    updateSelectedCount();
                    loadQuestions();
                    setTimeout(() => {
                        closeQuickCreate();
                    }, 800);
                } else {
                    showQuickAlert(result.msg, 'error');
                }
            });
        });
    </script>
</body>
</html>