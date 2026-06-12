<?php /*a:1:{s:69:"D:\phpstudy\phpstudy_pro\WWW\xxjsdati.com\app\view\teacher\class.html";i:1781065604;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>班级管理 - 信息技术课刷题考试系统</title>
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

        .class-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; }

        .class-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .class-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }
        .class-card h4 { font-size: 16px; color: #333; margin-bottom: 10px; }
        .class-card .info { font-size: 13px; color: #999; margin-bottom: 6px; }
        .class-card .info span { margin-right: 15px; }
        .class-card .student-count { font-size: 24px; font-weight: 600; color: #667eea; }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .btn.add { background: #667eea; color: white; padding: 8px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .btn.add:hover { background: #5a6fd6; }

        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .modal.show { display: flex; }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
        }
        .modal-content h3 { margin-bottom: 20px; font-size: 18px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 14px; color: #333; }
        .form-group select { width: 100%; padding: 10px 12px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 14px; outline: none; }
        .form-group select:focus { border-color: #667eea; }
        .modal-footer { display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end; }
        .btn.cancel { background: #f5f5f5; color: #666; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .btn.confirm { background: #667eea; color: white; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 15px; display: none; }
        .alert.error { background: #fff5f5; color: #dc2626; border: 1px solid #fee2e2; }
        .alert.success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

        @media (max-width: 768px) {
            .header .nav { gap: 15px; }
            .header .nav a { font-size: 12px; }
            .class-list { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">📚 刷题考试系统</div>
        <div class="nav">
            <a href="/teacher">首页</a>
            <a href="/teacher/courses">课程管理</a>
            <a href="/teacher/classes" class="active">班级管理</a>
            <a href="/teacher/students">学生管理</a>
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
        <div class="section">
            <div class="section-header">
                <h3>我的班级</h3>
                <button class="btn add" onclick="openJoinModal()">加入班级</button>
            </div>

            <?php if($classCount > 0): ?>
            <div class="class-list">
                <?php if(is_array($classes) || $classes instanceof \think\Collection || $classes instanceof \think\Paginator): $i = 0; $__LIST__ = $classes;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$class): $mod = ($i % 2 );++$i;?>
                <div class="class-card" onclick="location.href='/teacher/students?class_id=<?php echo htmlentities((string) $class['id']); ?>'">
                    <h4><?php echo htmlentities((string) $class['name']); ?></h4>
                    <div class="info">
                        <span>年级：<?php echo htmlentities((string) (isset($class['grade']) && ($class['grade'] !== '')?$class['grade']:'未设置')); ?></span>
                        <span>专业：<?php echo htmlentities((string) (isset($class['major']) && ($class['major'] !== '')?$class['major']:'未设置')); ?></span>
                    </div>
                    <div class="info"><?php echo htmlentities((string) (isset($class['description']) && ($class['description'] !== '')?$class['description']:'暂无描述')); ?></div>
                    <div style="margin-top: 12px;">
                        <span class="student-count"><?php echo htmlentities((string) $class['student_count']); ?></span>
                        <span style="color: #999; font-size: 13px;">名学生</span>
                    </div>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <?php else: ?>
            <div class="empty">
                <p>暂无班级数据，请点击"加入班级"选择班级</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal" id="joinModal">
        <div class="modal-content">
            <div class="alert" id="joinAlert"></div>
            <h3>加入班级</h3>
            <form id="joinForm">
                <div class="form-group">
                    <label>选择班级</label>
                    <select name="class_id">
                        <option value="">请选择班级</option>
                        <?php if(is_array($allClasses) || $allClasses instanceof \think\Collection || $allClasses instanceof \think\Paginator): $i = 0; $__LIST__ = $allClasses;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo htmlentities((string) $c['id']); ?>"><?php echo htmlentities((string) $c['name']); ?> - <?php echo htmlentities((string) (isset($c['teacher']['real_name']) && ($c['teacher']['real_name'] !== '')?$c['teacher']['real_name']:'暂无教师')); ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancel" onclick="closeJoinModal()">取消</button>
                    <button type="submit" class="btn confirm">确认加入</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openJoinModal() {
            document.getElementById('joinAlert').style.display = 'none';
            document.getElementById('joinForm').reset();
            document.getElementById('joinModal').classList.add('show');
        }

        function closeJoinModal() {
            document.getElementById('joinModal').classList.remove('show');
        }

        document.getElementById('joinForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = {};
            formData.forEach((v, k) => data[k] = v);

            fetch('/teacher/classes/join', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data)
            })
            .then(res => res.json())
            .then(result => {
                if (result.code === 1) {
                    location.reload();
                } else {
                    const alert = document.getElementById('joinAlert');
                    alert.textContent = result.msg;
                    alert.className = 'alert error';
                    alert.style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>