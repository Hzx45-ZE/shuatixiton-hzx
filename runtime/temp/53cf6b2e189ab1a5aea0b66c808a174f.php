<?php /*a:1:{s:72:"D:\phpstudy\phpstudy_pro\WWW\xxjsdati.com\app\view\student\practice.html";i:1781065604;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>在线练习 - 信息技术课刷题考试系统</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Microsoft YaHei', 'PingFang SC', sans-serif; background: #f5f7fa; padding-bottom: 80px; }
        .header {
            background: white; box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 0 20px; height: 60px; display: flex; align-items: center; justify-content: space-between;
        }
        .header .logo { font-size: 18px; font-weight: 600; color: #667eea; }
        .header .nav { display: flex; gap: 30px; }
        .header .nav a {
            text-decoration: none; color: #666; font-size: 14px; padding: 0 10px;
            line-height: 60px; border-bottom: 3px solid transparent; transition: all 0.3s ease;
        }
        .header .nav a:hover, .header .nav a.active { color: #667eea; border-bottom-color: #667eea; }
        .header .user { display: flex; align-items: center; gap: 15px; }
        .header .user .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none;
        }
        .header .user a { text-decoration: none; color: #666; font-size: 14px; }

        .main-content { max-width: 900px; margin: 0 auto; padding: 20px; }

        .back-link {
            display: inline-flex; align-items: center; gap: 5px;
            color: #667eea; text-decoration: none; font-size: 14px; margin-bottom: 15px; cursor: pointer;
        }

        .chapter-list { background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); overflow: hidden; }
        .course-group { padding: 0; }
        .course-group-title {
            padding: 18px 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; font-size: 16px; font-weight: 600;
        }
        .chapter-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px 25px; border-bottom: 1px solid #f0f0f0; cursor: pointer;
            transition: background 0.2s ease;
        }
        .chapter-item:last-child { border-bottom: none; }
        .chapter-item:hover { background: #fafafa; }
        .chapter-item .chapter-info h4 { font-size: 15px; color: #333; margin-bottom: 4px; }
        .chapter-item .chapter-info .meta { font-size: 12px; color: #999; }
        .chapter-item .arrow { color: #ccc; font-size: 18px; }
        .chapter-item .btn-practice {
            padding: 8px 20px; background: #667eea; color: white; border: none;
            border-radius: 6px; cursor: pointer; font-size: 13px; transition: all 0.3s ease;
        }
        .chapter-item .btn-practice:hover { background: #5a6fd6; }
        .chapter-item .btn-practice.completed {
            background: #52c41a;
        }
        .chapter-item .btn-practice.completed:hover {
            background: #45a614;
        }
        .chapter-item .status-tag {
            padding: 4px 10px; border-radius: 4px; font-size: 12px; margin-right: 10px;
        }
        .chapter-item .status-tag.completed { background: #f6ffed; color: #52c41a; }
        .chapter-item .status-tag.pending { background: #fff7e6; color: #fa8c16; }

        .question-card {
            background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 25px; margin-bottom: 15px;
        }
        .question-header { display: flex; gap: 15px; margin-bottom: 15px; align-items: center; }
        .question-type { background: #f0f5ff; color: #667eea; padding: 3px 12px; border-radius: 4px; font-size: 12px; }
        .question-difficulty { color: #999; font-size: 12px; }
        .question-score { background: #fff7e6; color: #fa8c16; padding: 3px 12px; border-radius: 4px; font-size: 12px; }
        .question-title { font-size: 16px; color: #333; line-height: 1.6; margin-bottom: 20px; }
        .option-list { list-style: none; }
        .option-item {
            display: flex; align-items: flex-start; padding: 12px 15px;
            border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 10px;
            cursor: pointer; transition: all 0.3s ease;
        }
        .option-item:hover { border-color: #667eea; background: #fafafa; }
        .option-item.selected { border-color: #667eea; background: #f0f5ff; }
        .option-item.correct { border-color: #52c41a !important; background: #f6ffed !important; }
        .option-item.wrong { border-color: #ff4d4f !important; background: #fff2f0 !important; }
        .option-item input { margin-right: 12px; margin-top: 4px; transform: scale(1.2); cursor: pointer; }
        .option-item .option-key { font-weight: 600; color: #667eea; margin-right: 10px; min-width: 20px; }
        .option-item .option-content { font-size: 14px; color: #333; }
        .fill-blank-input { width: 100%; padding: 10px 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; outline: none; }
        .fill-blank-input:focus { border-color: #667eea; }
        .answer-area textarea {
            width: 100%; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px;
            font-size: 14px; min-height: 120px; resize: vertical; outline: none;
        }
        .answer-area textarea:focus { border-color: #667eea; }

        .submit-section {
            background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            padding: 20px 25px; display: flex; justify-content: space-between; align-items: center; margin-top: 30px;
        }
        .btn { padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 500; transition: all 0.3s ease; }
        .btn.submit { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn.submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102,126,234,0.3); }
        .question-progress { font-size: 14px; color: #666; }
        .btn.secondary { background: #f5f5f5; color: #666; }

        .result-detail { margin-top: 15px; padding: 12px 15px; border-radius: 6px; font-size: 14px; font-weight: 500; }
        .result-detail.correct { background: #f6ffed; color: #52c41a; }
        .result-detail.wrong { background: #fff2f0; color: #ff4d4f; }
        .result-detail .correct-answer { font-weight: 600; margin-top: 4px; }

        .empty { text-align: center; padding: 60px; color: #999; }

        .question-actions { display: flex; gap: 10px; margin-top: 15px; padding-top: 12px; border-top: 1px solid #f0f0f0; }
        .btn-action {
            padding: 6px 14px; border: 1px solid #e0e0e0; border-radius: 6px;
            background: white; color: #666; font-size: 12px; cursor: pointer; transition: all 0.3s ease;
        }
        .btn-action:hover { border-color: #667eea; color: #667eea; background: #f0f5ff; }
        .btn-action .icon { margin-right: 4px; }

        .overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center;
            z-index: 1000;
        }
        .overlay.show { display: flex; }
        .modal {
            background: white; border-radius: 16px; padding: 40px; text-align: center;
            max-width: 400px; width: 90%;
        }
        .modal .emoji { font-size: 48px; margin-bottom: 10px; }
        .modal h3 { font-size: 20px; color: #333; margin-bottom: 15px; }
        .modal .stats { display: flex; justify-content: center; gap: 40px; margin-bottom: 20px; }
        .modal .stat-item { text-align: center; }
        .modal .stat-value { font-size: 28px; font-weight: 600; color: #667eea; }
        .modal .stat-label { font-size: 13px; color: #999; }
        .modal .btn { margin: 0 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">📚 刷题考试系统</div>
        <div class="nav">
            <a href="/student">首页</a>
            <a href="/student/courses">课程学习</a>
            <a href="/student/practice" class="active">在线练习</a>
            <a href="/student/exam">模拟考试</a>
            <a href="/student/error">错题本</a>
        </div>
        <div class="user">
            <a href="/student/profile" class="user-avatar"><?php echo htmlentities((string) mb_substr($user['real_name'],0,1)); ?></a>
            <a href="/student/profile"><?php echo htmlentities((string) $user['real_name']); ?></a>
            <a href="/logout">退出</a>
        </div>
    </div>

    <div class="main-content">
        <?php if($chapterId || $knowledgeId): ?>
        <a href="/student/practice" class="back-link">← 返回章节列表</a>

        <?php if($questions && count($questions) > 0): ?>
        <div class="question-container">
            <form id="practiceForm">
                <input type="hidden" name="question_ids" id="questionIdsInput" value="">
                <?php if(is_array($questions) || $questions instanceof \think\Collection || $questions instanceof \think\Paginator): $index = 0; $__LIST__ = $questions;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$question): $mod = ($index % 2 );++$index;?>
                <div class="question-card" data-question-id="<?php echo htmlentities((string) $question['id']); ?>">
                    <div class="question-header">
                        <span class="question-type"><?php echo htmlentities((string) $question['type_name']); ?></span>
                        <span class="question-difficulty">难度：<?php echo htmlentities((string) $question['difficulty_name']); ?></span>
                        <span class="question-score"><?php echo htmlentities((string) $question['score']); ?>分</span>
                    </div>
                    <div class="question-title">
                        <span style="font-weight:600;margin-right:8px;"><?php echo htmlentities((string) $index); ?>.</span>
                        <?php echo htmlentities((string) $question['title']); ?>
                    </div>

                    <?php if(isset($question['options']) && $question['options']): if(in_array($question['type_code'], ['single_choice', 'multiple_choice', 'judgment'])): ?>
                    <ul class="option-list">
                        <?php if(is_array($question['options']) || $question['options'] instanceof \think\Collection || $question['options'] instanceof \think\Paginator): $i = 0; $__LIST__ = $question['options'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i;?>
                        <li class="option-item">
                            <?php if($question['type_code'] == 'single_choice' || $question['type_code'] == 'judgment'): ?>
                            <input type="radio" name="answers[<?php echo htmlentities((string) $question['id']); ?>]" value="<?php echo htmlentities((string) $option['option_key']); ?>" class="answer-input">
                            <?php else: ?>
                            <input type="checkbox" name="answers[<?php echo htmlentities((string) $question['id']); ?>][]" value="<?php echo htmlentities((string) $option['option_key']); ?>" class="answer-input">
                            <?php endif; ?>
                            <span class="option-key"><?php echo htmlentities((string) $option['option_key']); ?></span>
                            <span class="option-content"><?php echo htmlentities((string) $option['option_content']); ?></span>
                        </li>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                    <?php elseif($question['type_code'] == 'fill_blank'): ?>
                    <div>
                        <input type="text" class="fill-blank-input" name="answers[<?php echo htmlentities((string) $question['id']); ?>]" placeholder="请输入答案（多空用分号分隔）">
                    </div>
                    <?php else: ?>
                    <div class="answer-area">
                        <textarea name="answers[<?php echo htmlentities((string) $question['id']); ?>]" placeholder="请输入答案..."></textarea>
                    </div>
                    <?php endif; else: ?>
                    <p style="color:#999;padding:10px;">该题目暂无选项</p>
                <?php endif; ?>
                
                    <div class="question-actions">
                        <button type="button" class="btn-action" onclick="addToError(<?php echo htmlentities((string) $question['id']); ?>)">📋 加入错题</button>
                        <button type="button" class="btn-action" onclick="addToFavorite(<?php echo htmlentities((string) $question['id']); ?>)">⭐ 收藏</button>
                    </div>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>

                <div class="submit-section">
                    <div class="question-progress">
                        已完成 <span id="completedCount">0</span> / <span id="totalQuestions"><?php echo count($questions); ?></span> 题
                    </div>
                    <button type="submit" class="btn submit">提交练习</button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="empty">
            <p>该章节暂无可用题目</p>
            <button class="btn secondary" onclick="location.href='/student/practice'" style="margin-top:20px;">返回章节列表</button>
        </div>
        <?php endif; else: if($chapter_groups && count($chapter_groups) > 0): if(is_array($chapter_groups) || $chapter_groups instanceof \think\Collection || $chapter_groups instanceof \think\Paginator): $i = 0; $__LIST__ = $chapter_groups;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?>
        <div class="chapter-list" style="margin-bottom: 20px;">
            <div class="course-group-title">📖 <?php echo htmlentities((string) $group['course_name']); ?></div>
            <?php if(is_array($group['chapters']) || $group['chapters'] instanceof \think\Collection || $group['chapters'] instanceof \think\Paginator): $i = 0; $__LIST__ = $group['chapters'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$chapter): $mod = ($i % 2 );++$i;?>
            <div class="chapter-item">
                <div class="chapter-info">
                    <h4><?php echo htmlentities((string) $chapter['name']); ?></h4>
                    <div class="meta">
                        共 <?php echo htmlentities((string) $chapter['question_count']); ?> 道题目
                        <?php if($chapter['is_completed']): ?>
                        <span class="status-tag completed">已完成</span>
                        <?php else: ?>
                        <span class="status-tag pending">未完成</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if($chapter['question_count'] > 0): if($chapter['is_completed']): ?>
                    <button class="btn-practice completed" onclick="startPractice(<?php echo htmlentities((string) $chapter['id']); ?>)">再次练习</button>
                    <?php else: ?>
                    <button class="btn-practice" onclick="startPractice(<?php echo htmlentities((string) $chapter['id']); ?>)">开始练习</button>
                    <?php endif; else: ?>
                <span style="color:#ccc;font-size:13px;">暂无题目</span>
                <?php endif; ?>
            </div>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <?php endforeach; endif; else: echo "" ;endif; else: ?>
        <div class="empty">
            <p>暂无可用练习，请联系教师添加题目后再来练习</p>
            <button class="btn secondary" onclick="location.href='/student'" style="margin-top:20px;">返回首页</button>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <div id="resultOverlay" class="overlay">
        <div class="modal">
            <div class="emoji">🎉</div>
            <h3>练习完成！</h3>
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-value" id="resultScore">0</div>
                    <div class="stat-label">得分</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="resultTotal">0</div>
                    <div class="stat-label">总分</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="resultRate">0%</div>
                    <div class="stat-label">正确率</div>
                </div>
            </div>
            <button class="btn submit" onclick="closeResultOverlay()">查看详情</button>
            <button class="btn secondary" onclick="finishPractice()">结束答题</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            collectQuestionIds();
            updateCompletedCount();
            addAnswerEventListeners();
        });

        function collectQuestionIds() {
            const cards = document.querySelectorAll('.question-card');
            const ids = [];
            cards.forEach(card => {
                const id = card.getAttribute('data-question-id');
                if (id) ids.push(id);
            });
            document.getElementById('questionIdsInput').value = ids.join(',');
        }

        function addAnswerEventListeners() {
            const inputs = document.querySelectorAll('.answer-input');
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    const li = this.closest('.option-item');
                    if (this.type === 'radio') {
                        const name = this.name;
                        document.querySelectorAll(`input[name="${name}"]`).forEach(i => {
                            i.closest('.option-item').classList.remove('selected');
                        });
                    }
                    if (this.checked) {
                        li.classList.add('selected');
                    } else {
                        li.classList.remove('selected');
                    }
                    updateCompletedCount();
                });
            });

            const textInputs = document.querySelectorAll('input[type="text"], textarea');
            textInputs.forEach(input => {
                input.addEventListener('input', updateCompletedCount);
            });
        }

        function updateCompletedCount() {
            const cards = document.querySelectorAll('.question-card');
            let completed = 0;
            
            cards.forEach(card => {
                const radios = card.querySelectorAll('input[type="radio"]');
                const checkboxes = card.querySelectorAll('input[type="checkbox"]');
                const textInput = card.querySelector('input[type="text"]');
                const textarea = card.querySelector('textarea');
                
                if (radios.length > 0) {
                    if ([...radios].some(r => r.checked)) completed++;
                } else if (checkboxes.length > 0) {
                    if ([...checkboxes].some(c => c.checked)) completed++;
                } else if (textInput) {
                    if (textInput.value.trim()) completed++;
                } else if (textarea) {
                    if (textarea.value.trim()) completed++;
                }
            });
            
            document.getElementById('completedCount').textContent = completed;
        }

        function startPractice(chapterId) {
            location.href = `/student/practice?chapter_id=${chapterId}`;
        }

        document.getElementById('practiceForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const answers = {};
            const questionIds = formData.get('question_ids').split(',');

            formData.forEach((value, key) => {
                if (key === 'question_ids') return;
                const match = key.match(/answers\[(\d+)\](\[\])?/);
                if (match) {
                    const questionId = match[1];
                    if (match[2]) {
                        if (!answers[questionId]) answers[questionId] = [];
                        answers[questionId].push(value);
                    } else {
                        answers[questionId] = value;
                    }
                }
            });

            if (Object.keys(answers).length === 0) {
                alert('请至少回答一道题');
                return;
            }

            fetch('/student/practice/submit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    answers: answers, 
                    question_ids: questionIds,
                    chapter_id: new URLSearchParams(window.location.search).get('chapter_id')
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.code === 1) {
                    document.getElementById('resultScore').textContent = data.data.score;
                    document.getElementById('resultTotal').textContent = data.data.total_score;
                    document.getElementById('resultRate').textContent = data.data.rate + '%';
                    document.getElementById('resultOverlay').classList.add('show');
                    
                    showCorrectAnswers(data.data.details);
                } else {
                    alert(data.msg);
                }
            })
            .catch(err => {
                console.error('提交失败:', err);
                alert('提交失败，请重试');
            });
        });

        function closeResultOverlay() {
            document.getElementById('resultOverlay').classList.remove('show');
        }

        function finishPractice() {
            location.href = '/student/practice';
        }

        function addToError(questionId) {
            fetch('/student/error/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ question_id: questionId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.code === 1) {
                    alert(data.msg);
                } else {
                    alert(data.msg);
                }
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
                if (data.code === 1) {
                    alert(data.msg);
                } else {
                    alert(data.msg);
                }
            });
        }

        function showCorrectAnswers(details) {
            if (!details || typeof details !== 'object') {
                console.error('没有批改数据');
                return;
            }

            console.log('批改数据:', details);

            Object.keys(details).forEach(questionId => {
                const card = document.querySelector(`.question-card[data-question-id="${questionId}"]`);
                if (!card) {
                    console.log(`找不到题目卡片: ${questionId}`);
                    return;
                }

                const detail = details[questionId];
                if (!detail) return;

                console.log(`题目${questionId}:`, {
                    is_correct: detail.is_correct,
                    correct_answer: detail.correct_answer,
                    user_answer: detail.user_answer,
                    debug: detail.debug
                });

                const options = card.querySelectorAll('.option-item');
                options.forEach(opt => {
                    const input = opt.querySelector('input');
                    if (!input) return;
                    
                    input.disabled = true;
                    opt.style.pointerEvents = 'none';

                    const val = input.value;
                    
                    if (detail.correct_answer && detail.correct_answer.indexOf(val) !== -1) {
                        opt.classList.add('correct');
                        opt.classList.remove('selected', 'wrong');
                    }
                    
                    if (detail.is_correct == 0 && input.checked) {
                        opt.classList.add('wrong');
                    }
                });

                const resultDiv = document.createElement('div');
                resultDiv.className = 'result-detail ' + (detail.is_correct == 1 ? 'correct' : 'wrong');
                
                let statusText = detail.is_correct == 1 ? '✓ 回答正确' : '✗ 回答错误';
                let correctInfo = '';
                
                if (detail.correct_answer && detail.correct_answer.length > 0) {
                    correctInfo = '<br>正确答案：' + detail.correct_answer.join(', ');
                } else if (!detail.is_correct) {
                    correctInfo = '<br>数据库中未设置正确答案';
                }
                
                resultDiv.innerHTML = statusText + '（+' + detail.score + '分）' + correctInfo;
                card.appendChild(resultDiv);
            });

            const submitSection = document.querySelector('.submit-section');
            if (submitSection) {
                submitSection.style.display = 'none';
            }
        }
    </script>
</body>
</html>