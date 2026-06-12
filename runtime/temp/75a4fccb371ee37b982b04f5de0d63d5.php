<?php /*a:1:{s:74:"D:\phpstudy\phpstudy_pro\WWW\xxjsdati.com\app\view\teacher\statistics.html";i:1781065604;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学情分析 - 信息技术课刷题考试系统</title>
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            text-align: center;
        }
        
        .stat-card .value {
            font-size: 28px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            font-size: 14px;
            color: #999;
        }
        
        .charts-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .chart-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 25px;
        }
        
        .chart-card h3 {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .chart-container {
            height: 250px;
            position: relative;
        }
        
        .chart-bars {
            display: flex;
            align-items: flex-end;
            height: 200px;
            gap: 15px;
        }
        
        .chart-bar {
            flex: 1;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            border-radius: 6px 6px 0 0;
            position: relative;
            transition: height 0.5s ease;
        }
        
        .chart-bar-label {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            color: #666;
            white-space: nowrap;
        }
        
        .chart-bar-value {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            font-weight: 600;
            color: #667eea;
        }
        
        .ranking-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .ranking-table th, .ranking-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .ranking-table th {
            background: #fafafa;
            font-weight: 600;
            color: #666;
            font-size: 14px;
        }
        
        .ranking-table td { font-size: 14px; color: #333; }
        
        .ranking-table .rank {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        
        .ranking-table .rank.top1 { background: #ffd700; color: #8b4513; }
        .ranking-table .rank.top2 { background: #c0c0c0; color: #333; }
        .ranking-table .rank.top3 { background: #cd7f32; color: #333; }
        .ranking-table .rank.other { background: #f0f0f0; color: #666; }
        
        .knowledge-list {
            list-style: none;
        }
        
        .knowledge-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .knowledge-item:last-child { border-bottom: none; }
        
        .knowledge-name { font-size: 14px; color: #333; }
        
        .knowledge-bar {
            width: 150px;
            height: 8px;
            background: #f0f0f0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .knowledge-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }
        
        .knowledge-bar-fill.high { background: #52c41a; }
        .knowledge-bar-fill.medium { background: #faad14; }
        .knowledge-bar-fill.low { background: #f5222d; }
        
        .knowledge-rate {
            width: 50px;
            text-align: right;
            font-size: 14px;
            font-weight: 600;
            color: #666;
        }
        
        .error-pie {
            width: 100%;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }
        
        .pie-chart {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: conic-gradient(var(--pie-colors));
            position: relative;
        }
        
        .pie-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #666;
        }
        
        .pie-legend {
            list-style: none;
        }
        
        .pie-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 13px;
            color: #666;
        }
        
        .pie-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }
        
        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty i { font-size: 48px; margin-bottom: 15px; }
        
        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .charts-row { grid-template-columns: 1fr; }
        }
        
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
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
            <a href="/teacher/tasks">任务布置</a>
            <a href="/teacher/papers">试卷管理</a>
            <a href="/teacher/statistics" class="active">学情分析</a>
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
                <option value="">选择班级</option>
                <?php if(is_array($classes) || $classes instanceof \think\Collection || $classes instanceof \think\Paginator): $i = 0; $__LIST__ = $classes;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$class): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo htmlentities((string) $class['id']); ?>" <?php if($selectedClass && $selectedClass['id'] == $class['id']): ?>selected<?php endif; ?>><?php echo htmlentities((string) $class['name']); ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
            <?php if($selectedClass): ?>
            <span style="color: #667eea; font-weight: 500;">当前班级：<?php echo htmlentities((string) $selectedClass['name']); ?></span>
            <?php endif; ?>
        </div>
        
        <?php if($selectedClass): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="value"><?php echo htmlentities((string) count($studentRanking)); ?></div>
                <div class="label">学生人数</div>
            </div>
            <div class="stat-card">
                <div class="value">
                    <?php if($studentRanking): ?>
                    <?php echo htmlentities((string) $studentRanking[0]['avg_score']); else: ?>
                    0
                    <?php endif; ?>
                </div>
                <div class="label">最高分</div>
            </div>
            <div class="stat-card">
                <div class="value">
                    <?php 
                    $total = 0; foreach($studentRanking as $s) { $total += $s['avg_score']; } echo $studentRanking ? round($total/count($studentRanking), 1) : 0;
                     ?>
                </div>
                <div class="label">平均分</div>
            </div>
            <div class="stat-card">
                <div class="value"><?php echo array_sum($errorDistribution); ?></div>
                <div class="label">错题总数</div>
            </div>
        </div>
        
        <div class="charts-row">
            <div class="chart-card">
                <h3>班级平均分趋势</h3>
                <div class="chart-container">
                    <div class="chart-bars">
                        <?php if(is_array($avgTrend) || $avgTrend instanceof \think\Collection || $avgTrend instanceof \think\Paginator): $i = 0; $__LIST__ = $avgTrend;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                        <div class="chart-bar" style="height: <?php echo htmlentities((string) $item['avg_score']); ?>%">
                            <div class="chart-bar-value"><?php echo htmlentities((string) $item['avg_score']); ?>%</div>
                            <div class="chart-bar-label"><?php echo htmlentities((string) substr($item['date'],5)); ?></div>
                        </div>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="chart-card">
                <h3>知识点掌握率</h3>
                <ul class="knowledge-list">
                    <?php if(is_array($knowledgeStats) || $knowledgeStats instanceof \think\Collection || $knowledgeStats instanceof \think\Paginator): $i = 0; $__LIST__ = $knowledgeStats;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                    <li class="knowledge-item">
                        <span class="knowledge-name"><?php echo htmlentities((string) $item['name']); ?></span>
                        <div class="knowledge-bar">
                            <div class="knowledge-bar-fill <?php echo $item['rate']>=80 ? 'high'  :  ($item['rate'] >= 60 ? 'medium' : 'low'); ?>" style="width: <?php echo htmlentities((string) $item['rate']); ?>%"></div>
                        </div>
                        <span class="knowledge-rate"><?php echo htmlentities((string) $item['rate']); ?>%</span>
                    </li>
                    <?php endforeach; endif; else: echo "" ;endif; if(empty($knowledgeStats) || (($knowledgeStats instanceof \think\Collection || $knowledgeStats instanceof \think\Paginator ) && $knowledgeStats->isEmpty())): ?>
                    <li style="text-align: center; color: #999; padding: 20px;">暂无数据</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <div class="charts-row">
            <div class="chart-card">
                <h3>学生成绩排名</h3>
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th>排名</th>
                            <th>姓名</th>
                            <th>平均分</th>
                            <th>考试次数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(is_array($studentRanking) || $studentRanking instanceof \think\Collection || $studentRanking instanceof \think\Paginator): $index = 0; $__LIST__ = $studentRanking;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$student): $mod = ($index % 2 );++$index;?>
                        <tr>
                            <td><div class="rank <?php echo $index<=3 ? 'top' . $index  :  'other'; ?>"><?php echo htmlentities((string) $index); ?></div></td>
                            <td><?php echo htmlentities((string) $student['name']); ?></td>
                            <td><strong><?php echo htmlentities((string) $student['avg_score']); ?></strong></td>
                            <td><?php echo htmlentities((string) $student['exam_count']); ?></td>
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; if(empty($studentRanking) || (($studentRanking instanceof \think\Collection || $studentRanking instanceof \think\Paginator ) && $studentRanking->isEmpty())): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #999;">暂无考试记录</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="chart-card">
                <h3>错题分布统计</h3>
                <div class="error-pie">
                    <div class="pie-chart" style="--pie-colors: #f5222d 30%, #fa8c16 25%, #faad14 20%, #52c41a 15%, #1890ff 10%">
                        <div class="pie-center"><?php echo array_sum($errorDistribution); ?>道</div>
                    </div>
                    <ul class="pie-legend">
                        <?php if(is_array($errorDistribution) || $errorDistribution instanceof \think\Collection || $errorDistribution instanceof \think\Paginator): $i = 0; $__LIST__ = $errorDistribution;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                        <li class="pie-item">
                            <div class="pie-color" style="background: <?php echo htmlentities((string) (isset($item['color']) && ($item['color'] !== '')?$item['color']:'#667eea')); ?>"></div>
                            <span><?php echo htmlentities((string) $item['name']); ?></span>
                            <span style="font-weight: 600;"><?php echo htmlentities((string) $item['count']); ?>道</span>
                        </li>
                        <?php endforeach; endif; else: echo "" ;endif; if(empty($errorDistribution) || (($errorDistribution instanceof \think\Collection || $errorDistribution instanceof \think\Paginator ) && $errorDistribution->isEmpty())): ?>
                        <li style="color: #999;">暂无错题数据</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="empty">
            <i>📊</i>
            <p>请选择班级查看统计数据</p>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        function filterByClass() {
            const classId = document.getElementById('classSelect').value;
            location.href = `/teacher/statistics${classId ? '?class_id=' + classId : ''}`;
        }
    </script>
</body>
</html>