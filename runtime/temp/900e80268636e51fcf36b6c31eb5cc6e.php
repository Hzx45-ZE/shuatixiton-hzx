<?php /*a:1:{s:68:"D:\phpstudy\phpstudy_pro\WWW\xxjsdati.com\app\view\teacher\task.html";i:1781065604;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>任务管理 - 信息技术课刷题考试系统</title>
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
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .section-header h3 { font-size: 16px; color: #333; }

        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        table th { background: #fafafa; color: #666; font-weight: 500; }
        table tr:hover { background: #fafafa; }

        .status { padding: 3px 10px; border-radius: 4px; font-size: 12px; }
        .status.ongoing { background: #e6f7ff; color: #1890ff; }
        .status.ended { background: #f5f5f5; color: #999; }

        .type { padding: 3px 10px; border-radius: 4px; font-size: 12px; }
        .type.practice { background: #f6ffed; color: #52c41a; }
        .type.exam { background: #fff1f0; color: #f5222d; }
        .type.material { background: #fff7e6; color: #fa8c16; }

        .btn {
            padding: 6px 15px;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn.primary { background: #667eea; color: white; }
        .btn.primary:hover { background: #5a6fd6; }
        .btn.danger { background: #ff4d4f; color: white; }
        .btn.danger:hover { background: #e04345; }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .btn.add { background: #667eea; color: white; padding: 8px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .btn.add:hover { background: #5a6fd6; }
        .btn.delete { background: #ff4d4f; color: white; padding: 4px 10px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; }

        @media (max-width: 768px) {
            .header .nav { gap: 15px; }
            .header .nav a { font-size: 12px; }
        }
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
            <a href="/teacher/tasks" class="active">任务布置</a>
            <a href="/teacher/papers">试卷管理</a>
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
                <h3>已发布任务</h3>
                <button class="btn add" onclick="location.href='/teacher/tasks/create'">发布任务</button>
            </div>

            <?php if($taskCount > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>任务标题</th>
                        <th>类型</th>
                        <th>目标班级</th>
                        <th>截止时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(is_array($tasks) || $tasks instanceof \think\Collection || $tasks instanceof \think\Paginator): $i = 0; $__LIST__ = $tasks;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$task): $mod = ($i % 2 );++$i;?>
                    <tr>
                        <td><strong><?php echo htmlentities((string) $task['title']); ?></strong></td>
                        <td>
                            <?php if($task['type'] == 'practice'): ?><span class="type practice">练习</span>
                            <?php elseif($task['type'] == 'exam'): ?><span class="type exam">考试</span>
                            <?php else: ?><span class="type material">资料</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlentities((string) $task['class_name']); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($task['deadline'])); ?></td>
                        <td>
                            <?php if($task['status'] == 1): ?><span class="status ongoing">进行中</span>
                            <?php else: ?><span class="status ended">已结束</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn delete" onclick="deleteTask(<?php echo htmlentities((string) $task['id']); ?>)">删除</button>
                        </td>
                    </tr>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty">
                <p>暂无发布的任务</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function deleteTask(id) {
            if (!confirm('确定要删除该任务吗？')) return;
            fetch('/teacher/tasks/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            })
            .then(res => res.json())
            .then(result => {
                if (result.code === 1) location.reload();
                else alert(result.msg);
            });
        }
    </script>
</body>
</html>