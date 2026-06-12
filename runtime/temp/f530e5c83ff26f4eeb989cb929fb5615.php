<?php /*a:1:{s:71:"D:\phpstudy\phpstudy_pro\WWW\xxjsdati.com\app\view\teacher\student.html";i:1781065604;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学生管理 - 信息技术课刷题考试系统</title>
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
        
        .filter-bar {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .filter-bar label { font-size: 14px; color: #666; }
        
        .filter-bar select {
            padding: 8px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        
        .filter-bar select:focus { border-color: #667eea; }
        
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .table-container table { width: 100%; border-collapse: collapse; }
        
        .table-container th, .table-container td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .table-container th {
            background: #fafafa;
            font-weight: 600;
            color: #666;
            font-size: 14px;
        }
        
        .table-container td { font-size: 14px; color: #333; }
        
        .table-container tr:hover { background: #fafafa; }
        
        .btn {
            padding: 6px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s ease;
        }
        
        .btn.primary { background: #667eea; color: white; }
        .btn.primary:hover { background: #5a6fd6; }
        
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
            <a href="/teacher/students" class="active">学生管理</a>
            <a href="/teacher/tasks">任务布置</a>
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
        <div class="filter-bar">
            <label>选择班级：</label>
            <select id="classSelect" onchange="filterByClass()">
                <option value="">全部班级</option>
                <?php if(is_array($classes) || $classes instanceof \think\Collection || $classes instanceof \think\Paginator): $i = 0; $__LIST__ = $classes;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$class): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo htmlentities((string) $class['id']); ?>" <?php if($selectedClass == $class['id']): ?>selected<?php endif; ?>><?php echo htmlentities((string) $class['name']); ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>学号</th>
                        <th>姓名</th>
                        <th>手机号</th>
                        <th>邮箱</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($students): if(is_array($students) || $students instanceof \think\Collection || $students instanceof \think\Paginator): $i = 0; $__LIST__ = $students;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$student): $mod = ($i % 2 );++$i;?>
                    <tr>
                        <td><?php echo htmlentities((string) $student['username']); ?></td>
                        <td><?php echo htmlentities((string) $student['real_name']); ?></td>
                        <td><?php echo htmlentities((string) (isset($student['phone']) && ($student['phone'] !== '')?$student['phone']:'-')); ?></td>
                        <td><?php echo htmlentities((string) (isset($student['email']) && ($student['email'] !== '')?$student['email']:'-')); ?></td>
                        <td><?php echo $student['status']==1 ? '正常'  :  '禁用'; ?></td>
                        <td>
                            <button class="btn primary" onclick="viewDetail(<?php echo htmlentities((string) $student['id']); ?>)">查看详情</button>
                        </td>
                    </tr>
                    <?php endforeach; endif; else: echo "" ;endif; else: ?>
                    <tr>
                        <td colspan="6" class="empty">
                            <i>👥</i>
                            <p>暂无学生数据</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        function filterByClass() {
            const classId = document.getElementById('classSelect').value;
            location.href = `/teacher/students${classId ? '?class_id=' + classId : ''}`;
        }
        
        function viewDetail(studentId) {
            location.href = `/teacher/students/detail?student_id=${studentId}`;
        }
    </script>
</body>
</html>