<?php /*a:1:{s:74:"D:\phpstudy\phpstudy_pro\WWW\xxjsdati.com\app\view\student\exam_start.html";i:1781091673;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>在线考试 - 信息技术课刷题考试系统</title>
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
        
        .header .timer {
            font-size: 18px;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 6px;
            background: #fff1f0;
            color: #f5222d;
        }
        
        .header .timer.warning { background: #fff7e6; color: #fa8c16; }
        .header .timer.safe { background: #f6ffed; color: #52c41a; }
        
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
        
        .main-content { padding: 20px; max-width: 900px; margin: 0 auto; }
        
        .paper-info {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .paper-info h2 { font-size: 18px; color: #333; }
        
        .paper-info .meta { display: flex; gap: 20px; font-size: 14px; color: #666; }
        
        .question-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 25px;
        }
        
        .question-header {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .question-type {
            padding: 4px 10px;
            background: #f0f5ff;
            color: #667eea;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .question-score {
            padding: 4px 10px;
            background: #fff7e6;
            color: #fa8c16;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .question-title {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .option-list { list-style: none; }
        
        .option-item {
            display: flex;
            align-items: flex-start;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .option-item:hover { border-color: #667eea; }
        .option-item.selected { border-color: #667eea; background: #f0f5ff; }
        
        .option-item .option-key {
            font-weight: 600;
            color: #667eea;
            margin-right: 12px;
            min-width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e6f7ff;
            border-radius: 50%;
            font-size: 14px;
        }
        
        .option-item.selected .option-key { background: #667eea; color: white; }
        
        .option-item .option-content { font-size: 14px; color: #333; padding-top: 3px; }
        
        .fill-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            margin-bottom: 15px;
        }
        
        .fill-input:focus { border-color: #667eea; }
        
        .fill-input:disabled { background: #fafafa; cursor: not-allowed; }
        
        .question-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }
        
        .nav-buttons { display: flex; gap: 10px; }
        
        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn.primary { background: #667eea; color: white; }
        .btn.primary:hover { background: #5a6fd6; }
        
        .btn.secondary { background: #f5f5f5; color: #666; }
        .btn.secondary:hover { background: #e8e8e8; }
        
        .btn.submit { background: #f5222d; color: white; }
        .btn.submit:hover { background: #cf1322; }
        
        .progress-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .progress-item {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .progress-item.unanswered { background: #f5f5f5; color: #999; }
        .progress-item.answered { background: #667eea; color: white; }
        .progress-item.current { box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3); }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
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
            text-align: center;
        }
        
        .modal-content .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .modal-content.warning .icon { color: #fa8c16; }
        .modal-content.success .icon { color: #52c41a; }
        
        .modal-content h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .modal-content p {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .question-actions { display: flex; gap: 10px; margin-top: 15px; padding-top: 12px; border-top: 1px solid #f0f0f0; }
        .btn-action {
            padding: 6px 14px; border: 1px solid #e0e0e0; border-radius: 6px;
            background: white; color: #666; font-size: 12px; cursor: pointer; transition: all 0.3s ease;
        }
        .btn-action:hover { border-color: #667eea; color: #667eea; background: #f0f5ff; }
        .btn-action .icon { margin-right: 4px; }
    </style>
</head>
<body oncopy="return false" oncut="return false" onpaste="return false">
    <div class="header">
        <div class="logo">📚 刷题考试系统</div>
        <div class="timer" id="timer">00:00</div>
        <div class="user">
            <a href="/student/profile" class="user-avatar"><?php echo htmlentities((string) mb_substr($user['real_name'],0,1)); ?></a>
            <a href="/student/profile" class="user-name"><?php echo htmlentities((string) $user['real_name']); ?></a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="paper-info">
            <h2><?php echo htmlentities((string) $paper['name']); ?></h2>
            <div class="meta">
                <span>总分：<?php echo htmlentities((string) $paper['total_score']); ?>分</span>
                <span>共<?php echo htmlentities((string) count($questions)); ?>题</span>
            </div>
        </div>
        
        <div class="progress-bar" id="progressBar">
            <?php if(is_array($questions) || $questions instanceof \think\Collection || $questions instanceof \think\Paginator): $index = 0; $__LIST__ = $questions;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$q): $mod = ($index % 2 );++$index;?>
            <div class="progress-item unanswered" data-index="<?php echo htmlentities((string) $index - 1); ?>" onclick="goToQuestion(<?php echo htmlentities((string) $index - 1); ?>)"><?php echo htmlentities((string) $index); ?></div>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        
        <div class="question-container" id="questionContainer">
            <?php if(is_array($questions) || $questions instanceof \think\Collection || $questions instanceof \think\Paginator): $index = 0; $__LIST__ = $questions;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$question): $mod = ($index % 2 );++$index;?>
            <div class="question" data-question-id="<?php echo htmlentities((string) $question['id']); ?>" style="<?php if($index != 1): ?>display: none<?php endif; ?>">
                <div class="question-header">
                    <span class="question-type"><?php echo htmlentities((string) $question['type_name']); ?></span>
                    <span class="question-score"><?php echo htmlentities((string) $question['paper_score']); ?>分</span>
                </div>
                
                <div class="question-title">
                    <span style="font-weight: 600; margin-right: 8px;"><?php echo htmlentities((string) $index); ?>.</span>
                    <?php echo htmlentities((string) $question['title']); ?>
                </div>
                
                <?php if($question['type_id'] <= 3): ?>
                <ul class="option-list">
                    <?php if(is_array($question['options']) || $question['options'] instanceof \think\Collection || $question['options'] instanceof \think\Paginator): $i = 0; $__LIST__ = $question['options'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i;?>
                    <li class="option-item" data-option="<?php echo htmlentities((string) $option['option_key']); ?>" onclick="selectOption(this)">
                        <span class="option-key"><?php echo htmlentities((string) $option['option_key']); ?></span>
                        <span class="option-content"><?php echo htmlentities((string) $option['option_content']); ?></span>
                    </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
                <?php endif; if($question['type_id'] == 4): ?>
                <input type="text" class="fill-input" placeholder="请输入答案" data-question-id="<?php echo htmlentities((string) $question['id']); ?>">
                <?php endif; ?>
                
                <div class="question-actions">
                    <button type="button" class="btn-action" onclick="addToError(<?php echo htmlentities((string) $question['id']); ?>)">📋 加入错题</button>
                    <button type="button" class="btn-action" onclick="addToFavorite(<?php echo htmlentities((string) $question['id']); ?>)">⭐ 收藏</button>
                </div>
            </div>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        
        <div class="question-nav">
            <div class="nav-buttons">
                <button class="btn secondary" id="prevBtn" onclick="prevQuestion()">上一题</button>
                <button class="btn secondary" id="nextBtn" onclick="nextQuestion()">下一题</button>
            </div>
            <button class="btn submit" onclick="confirmSubmit()">提交试卷</button>
        </div>
    </div>
    
    <div class="modal" id="submitModal">
        <div class="modal-content warning">
            <div class="icon">⚠️</div>
            <h3>确认提交</h3>
            <p>提交后将无法修改答案，确定要提交吗？</p>
            <div class="modal-footer">
                <button class="btn secondary" onclick="closeModal()">取消</button>
                <button class="btn submit" onclick="submitExam()">确认提交</button>
            </div>
        </div>
    </div>
    
    <div class="modal" id="resultModal">
        <div class="modal-content success">
            <div class="icon">🎉</div>
            <h3>考试完成</h3>
            <p>得分：<span id="resultScore">0</span> / <span id="resultTotal">0</span>分</p>
            <p id="resultDetail"></p>
            <div class="modal-footer">
                <button class="btn secondary" onclick="location.href='/student/exam'">返回列表</button>
                <button class="btn primary" id="btnViewDetail" onclick="">查看详情</button>
            </div>
        </div>
    </div>
    
    <div class="modal" id="timeoutModal">
        <div class="modal-content warning">
            <div class="icon">⏰</div>
            <h3>考试时间已到</h3>
            <p>系统将自动提交您的答案...</p>
        </div>
    </div>
    
    <script>
        const questions = document.querySelectorAll('.question');
        const progressItems = document.querySelectorAll('.progress-item');
        const answers = {};
        let currentIndex = 0;
        let totalSeconds = <?php echo htmlentities((string) $duration); ?>;
        let timerInterval = null;
        
        function startTimer() {
            timerInterval = setInterval(() => {
                totalSeconds--;
                updateTimer();
                
                if (totalSeconds <= 0) {
                    clearInterval(timerInterval);
                    timeoutSubmit();
                }
            }, 1000);
        }
        
        function updateTimer() {
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            
            let timeStr = '';
            if (hours > 0) {
                timeStr += hours.toString().padStart(2, '0') + ':';
            }
            timeStr += minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
            
            const timer = document.getElementById('timer');
            timer.textContent = timeStr;
            
            if (totalSeconds <= 60) {
                timer.className = 'timer';
            } else if (totalSeconds <= 300) {
                timer.className = 'timer warning';
            } else {
                timer.className = 'timer safe';
            }
        }
        
        function timeoutSubmit() {
            document.getElementById('timeoutModal').classList.add('show');
            setTimeout(() => {
                submitExam();
            }, 2000);
        }
        
        function goToQuestion(index) {
            saveCurrentAnswer();
            
            questions.forEach((q, i) => {
                q.style.display = i === index ? 'block' : 'none';
            });
            
            progressItems.forEach((item, i) => {
                item.classList.remove('current');
                if (i === index) {
                    item.classList.add('current');
                }
            });
            
            currentIndex = index;
            updateNavButtons();
        }
        
        function prevQuestion() {
            if (currentIndex > 0) {
                goToQuestion(currentIndex - 1);
            }
        }
        
        function nextQuestion() {
            if (currentIndex < questions.length - 1) {
                goToQuestion(currentIndex + 1);
            }
        }
        
        function updateNavButtons() {
            document.getElementById('prevBtn').disabled = currentIndex === 0;
            document.getElementById('nextBtn').disabled = currentIndex === questions.length - 1;
        }
        
        function selectOption(el) {
            const question = el.closest('.question');
            const options = question.querySelectorAll('.option-item');
            
            options.forEach(opt => opt.classList.remove('selected'));
            el.classList.add('selected');
            
            const qId = question.dataset.questionId;
            if (qId) {
                answers[qId] = el.dataset.option;
            }
            
            updateProgress();
        }
        
        function saveCurrentAnswer() {
            const currentQuestion = questions[currentIndex];
            const qId = currentQuestion.dataset.questionId;
            if (!qId) return;
            
            const fillInput = currentQuestion.querySelector('.fill-input');
            const selectedOption = currentQuestion.querySelector('.option-item.selected');
            
            if (fillInput) {
                answers[qId] = fillInput.value.trim();
            } else if (selectedOption) {
                answers[qId] = selectedOption.dataset.option;
            }
        }
        
        function updateProgress() {
            const qId = questions[currentIndex].dataset.questionId;
            const hasAnswer = answers[qId] && String(answers[qId]).trim();
            const classes = ['progress-item'];
            classes.push(hasAnswer ? 'answered' : 'unanswered');
            classes.push('current');
            progressItems[currentIndex].className = classes.join(' ');
        }
        
        function confirmSubmit() {
            saveCurrentAnswer();
            document.getElementById('submitModal').classList.add('show');
        }
        
        function closeModal() {
            document.getElementById('submitModal').classList.remove('show');
        }
        
        function submitExam() {
            saveCurrentAnswer();
            
            document.querySelectorAll('.modal').forEach(m => m.classList.remove('show'));
            
            var formData = new URLSearchParams();
            Object.keys(answers).forEach(function(qId) {
                var val = answers[qId];
                if (val !== undefined && val !== null) {
                    formData.append('answers[' + qId + ']', String(val));
                }
            });
            
            fetch('/student/exam/submit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
            .then(res => res.json())
            .then(data => {
                if (data.code === 1) {
                    document.getElementById('resultScore').textContent = data.data.score;
                    document.getElementById('resultTotal').textContent = data.data.total_score;
                    document.getElementById('resultDetail').textContent = 
                        '答对' + data.data.correct_count + '题，共' + data.data.total_count + '题';
                    document.getElementById('btnViewDetail').setAttribute(
                        'onclick', 'location.href=\'/student/exam/detail?record_id=' + data.data.record_id + '\'');
                    document.getElementById('resultModal').classList.add('show');
                } else {
                    alert(data.msg || '提交失败');
                }
            })
            .catch(function(err) {
                alert('网络错误，提交失败: ' + err.message);
            });
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Control' || e.key === 'Meta' || e.key === 'c' || e.key === 'x' || e.key === 'v') {
                e.preventDefault();
            }
        });
        
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        
        document.querySelectorAll('.fill-input').forEach(function(input) {
            input.addEventListener('input', function() {
                var qId = questions[currentIndex].dataset.questionId;
                if (qId) {
                    answers[qId] = input.value.trim();
                    updateProgress();
                }
            });
        });
        
        startTimer();
        
        function addToError(questionId) {
            fetch('/student/error/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ question_id: questionId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.code === 1) alert(data.msg);
                else alert(data.msg);
            });
        }

        function addToFavorite(questionId) {
            fetch('/student/favorite/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ question_id: questionId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.code === 1) alert(data.msg);
                else alert(data.msg);
            });
        }
    </script>
</body>
</html>