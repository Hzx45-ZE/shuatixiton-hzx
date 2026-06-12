<?php /*a:1:{s:69:"D:\phpstudy\phpstudy_pro\WWW\xxjsdati.com\app\view\teacher\paper.html";i:1781169498;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>试卷管理 - 信息技术课刷题考试系统</title>
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
        
        .main-content { padding: 20px; }
        
        .section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 25px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-header h3 { font-size: 18px; color: #333; }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn.primary { background: #667eea; color: white; }
        .btn.primary:hover { background: #5a6fd6; }
        
        .btn.publish { background: #52c41a; color: white; }
        .btn.publish:hover { background: #389e0d; }
        
        .btn.unpublish { background: #faad14; color: white; }
        .btn.delete { background: #f5222d; color: white; }
        .btn.delete:hover { background: #cf1322; }
        
        .paper-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .paper-card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .paper-card:hover { border-color: #667eea; }
        
        .paper-card h4 {
            font-size: 16px;
            color: #333;
            margin-bottom: 12px;
        }
        
        .paper-card p {
            font-size: 13px;
            color: #999;
            margin-bottom: 12px;
        }
        
        .paper-card .meta {
            display: flex;
            gap: 15px;
            font-size: 13px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .paper-card .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            margin-bottom: 15px;
        }
        
        .paper-card .status.draft { background: #f5f5f5; color: #666; }
        .paper-card .status.published { background: #f6ffed; color: #52c41a; }
        
        .paper-card .actions { display: flex; gap: 10px; }
        
        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty i { font-size: 48px; margin-bottom: 15px; }
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
        <div class="section">
            <div class="section-header">
                <h3>我的试卷</h3>
                <button class="btn primary" onclick="location.href='/teacher/papers/add'">+ 新建试卷</button>
            </div>
            
            <?php if($papers): ?>
            <div class="paper-list">
                <?php if(is_array($papers) || $papers instanceof \think\Collection || $papers instanceof \think\Paginator): $i = 0; $__LIST__ = $papers;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$paper): $mod = ($i % 2 );++$i;?>
                <div class="paper-card">
                    <h4><?php echo htmlentities((string) $paper['name']); ?></h4>
                    <p><?php echo htmlentities((string) (isset($paper['description']) && ($paper['description'] !== '')?$paper['description']:'暂无描述')); ?></p>
                    <div class="meta">
                        <span>📝 <?php echo htmlentities((string) $paper['question_count']); ?>题</span>
                        <span>🏆 <?php echo htmlentities((string) $paper['total_score']); ?>分</span>
                        <span><?php echo $paper['type']=='manual' ? '手动组卷'  :  '自动组卷'; ?></span>
                    </div>
                    <span class="status <?php echo $paper['status']==1 ? 'published'  :  'draft'; ?>"><?php echo $paper['status']==1 ? '已发布'  :  '草稿'; ?></span>
                    <div class="actions">
                        <button class="btn" style="background: #1677ff; color: white;" onclick="previewPaper(<?php echo htmlentities((string) $paper['id']); ?>)">预览</button>
                        <button class="btn <?php echo $paper['status']==1 ? 'unpublish'  :  'publish'; ?>" onclick="togglePublish(<?php echo htmlentities((string) $paper['id']); ?>)">
                            <?php echo $paper['status']==1 ? '取消发布'  :  '发布'; ?>
                        </button>
                        <button class="btn delete" onclick="deletePaper(<?php echo htmlentities((string) $paper['id']); ?>)">删除</button>
                    </div>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <?php else: ?>
            <div class="empty">
                <i>📋</i>
                <p>暂无试卷，点击上方按钮创建</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function previewPaper(paperId) {
            window.open('/teacher/papers/preview?paper_id=' + paperId, '_blank');
        }
        
        function togglePublish(paperId) {
            fetch(`/teacher/papers/publish?paper_id=${paperId}`)
            .then(res => res.json())
            .then(data => {
                if (data.code === 1) {
                    location.reload();
                } else {
                    alert(data.msg);
                }
            });
        }
        
        function deletePaper(paperId) {
            if (!confirm('确定要删除这份试卷吗？')) return;
            
            fetch(`/teacher/papers/delete?paper_id=${paperId}`)
            .then(res => res.json())
            .then(data => {
                if (data.code === 1) {
                    location.reload();
                } else {
                    alert(data.msg);
                }
            });
        }
    </script>
</body>
</html>