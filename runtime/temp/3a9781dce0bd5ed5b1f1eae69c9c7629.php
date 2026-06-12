<?php /*a:1:{s:77:"D:\phpstudy\phpstudy_pro\WWW\xxjsdati.com\app\view\teacher\paper_preview.html";i:1781181180;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>试卷预览 - <?php echo htmlentities((string) $paper['name']); ?></title>
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
        .header .back { color: #667eea; text-decoration: none; font-size: 14px; }
        .header h2 { font-size: 18px; color: #333; }
        
        .main-content { max-width: 900px; margin: 20px auto; padding: 0 20px; }
        
        .paper-info {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 20px;
            text-align: center;
        }
        .paper-info h1 { font-size: 24px; color: #333; margin-bottom: 15px; }
        .paper-info .meta { font-size: 14px; color: #999; display: flex; justify-content: center; gap: 30px; }
        .paper-info .desc { font-size: 14px; color: #666; margin-top: 15px; }
        
        .question-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 25px;
            margin-bottom: 15px;
        }
        .question-card .q-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .question-card .q-num { font-size: 16px; font-weight: 600; color: #667eea; }
        .question-card .q-meta { font-size: 12px; color: #999; }
        .question-card .q-meta span { margin-left: 10px; padding: 2px 8px; background: #f0f0f0; border-radius: 4px; }
        .question-card .q-title { font-size: 16px; color: #333; margin-bottom: 15px; line-height: 1.6; }
        
        .options { display: grid; gap: 10px; margin-bottom: 15px; }
        .option { padding: 10px 15px; background: #f9f9f9; border-radius: 8px; font-size: 14px; color: #555; }
        .option .key { font-weight: 600; color: #667eea; margin-right: 8px; }
        
        .answer-box {
            background: #f6ffed;
            border: 1px solid #b7eb8f;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .answer-box strong { color: #52c41a; }
        
        .analysis-box {
            background: #fff7e6;
            border: 1px solid #ffd591;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 13px;
            color: #666;
        }
        .analysis-box strong { color: #fa8c16; }
        
        .empty { text-align: center; padding: 60px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <a href="/teacher/papers" class="back">返回试卷管理</a>
        <h2>试卷预览</h2>
        <div></div>
    </div>
    
    <div class="main-content">
        <div class="paper-info">
            <h1><?php echo htmlentities((string) $paper['name']); ?></h1>
            <div class="meta">
                <span>共 <?php echo htmlentities((string) $paper['question_count']); ?> 题</span>
                <span>总分 <?php echo htmlentities((string) $paper['total_score']); ?> 分</span>
                <span><?php echo $paper['type']=='manual' ? '手动组卷'  :  'AI自动组卷'; ?></span>
                <span>状态：<?php echo $paper['status']==1 ? '已发布'  :  '草稿'; ?></span>
            </div>
            <?php if($paper['description']): ?>
            <div class="desc"><?php echo htmlentities((string) $paper['description']); ?></div>
            <?php endif; ?>
        </div>
        
        <?php if($questions): if(is_array($questions) || $questions instanceof \think\Collection || $questions instanceof \think\Paginator): $i = 0; $__LIST__ = $questions;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$q): $mod = ($i % 2 );++$i;?>
        <div class="question-card">
            <div class="q-header">
                <div class="q-num">第 <?php echo htmlentities((string) $i); ?> 题（<?php echo htmlentities((string) $q['score']); ?>分）</div>
                <div class="q-meta">
                    <span><?php echo htmlentities((string) $q['type_name']); ?></span>
                    <span><?php echo htmlentities((string) $q['difficulty_name']); ?></span>
                </div>
            </div>
            <div class="q-title"><?php echo htmlentities((string) $q['title']); ?></div>
            
            <?php if($q['options']): ?>
            <div class="options">
                <?php if(is_array($q['options']) || $q['options'] instanceof \think\Collection || $q['options'] instanceof \think\Paginator): $i = 0; $__LIST__ = $q['options'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$opt): $mod = ($i % 2 );++$i;?>
                <div class="option"><span class="key"><?php echo htmlentities((string) $opt['option_key']); ?>.</span><?php echo htmlentities((string) $opt['option_content']); ?></div>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="answer-box">
                <strong>正确答案：</strong><?php echo !empty($q['answer_display']) ? htmlentities((string) $q['answer_display']) : '（无）'; ?>
            </div>
            
            <?php if($q['analysis']): ?>
            <div class="analysis-box">
                <strong>解析：</strong><?php echo htmlentities((string) $q['analysis']); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; endif; else: echo "" ;endif; else: ?>
        <div class="empty">该试卷暂无题目</div>
        <?php endif; ?>
    </div>
</body>
</html>