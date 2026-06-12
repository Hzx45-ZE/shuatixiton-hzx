<?php /*a:1:{s:66:"D:\phpstudy\phpstudy_pro\WWW\xxjsdati.com\app\view\auth\login.html";i:1780303346;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>信息技术课刷题考试系统 - 登录</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Microsoft YaHei', 'PingFang SC', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-radius: 50%;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .login-header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
            background: #fafafa;
        }

        .form-group input:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group input::placeholder {
            color: #999;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .form-footer {
            text-align: center;
            margin-top: 25px;
            position: relative;
            z-index: 1;
        }

        .form-footer a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .form-footer span {
            color: #999;
            margin: 0 10px;
        }

        .role-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .role-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            color: #666;
        }

        .role-btn.active {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .role-btn:hover {
            border-color: #667eea;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
            position: relative;
            z-index: 1;
        }

        .alert.error {
            background: #fff5f5;
            color: #dc2626;
            border: 1px solid #fee2e2;
        }

        .alert.success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .loader {
            display: none;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>欢迎登录</h1>
            <p>信息技术课刷题考试系统</p>
        </div>

        <div class="alert" id="alert"></div>

        <form id="loginForm">
            <div class="role-selector">
                <button type="button" class="role-btn active" data-role="1">学生登录</button>
                <button type="button" class="role-btn" data-role="2">教师登录</button>
            </div>

            <div class="form-group">
                <label for="username">账号</label>
                <input type="text" id="username" name="username" placeholder="请输入学号/工号" required>
            </div>

            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" id="password" name="password" placeholder="请输入密码" required>
            </div>

            <input type="hidden" id="role" name="role" value="1">

            <button type="submit" class="login-btn" id="loginBtn">
                <span id="btnText">登 录</span>
                <div class="loader" id="loader"></div>
            </button>
        </form>

        <div class="form-footer">
            <a href="/register">注册账号</a>
            <span>|</span>
            <a href="javascript:void(0)" onclick="showForgotPassword()">忘记密码</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleBtns = document.querySelectorAll('.role-btn');
            const roleInput = document.getElementById('role');

            roleBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    roleBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    roleInput.value = this.dataset.role;
                });
            });

            const form = document.getElementById('loginForm');
            const alertDiv = document.getElementById('alert');
            const loginBtn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const loader = document.getElementById('loader');

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;

                if (!username || !password) {
                    showAlert('请输入账号和密码', 'error');
                    return;
                }

                btnText.style.display = 'none';
                loader.style.display = 'block';
                loginBtn.disabled = true;

                fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&role=${roleInput.value}`
                })
                .then(response => response.json())
                .then(data => {
                    btnText.style.display = 'block';
                    loader.style.display = 'none';
                    loginBtn.disabled = false;

                    if (data.code === 1) {
                        showAlert(data.msg, 'success');
                        setTimeout(() => {
                            window.location.href = data.url;
                        }, 1500);
                    } else {
                        showAlert(data.msg, 'error');
                    }
                })
                .catch(error => {
                    btnText.style.display = 'block';
                    loader.style.display = 'none';
                    loginBtn.disabled = false;
                    showAlert('网络错误，请稍后重试', 'error');
                });
            });
        });

        function showAlert(msg, type) {
            const alertDiv = document.getElementById('alert');
            alertDiv.textContent = msg;
            alertDiv.className = `alert ${type}`;
            alertDiv.style.display = 'block';
            
            setTimeout(() => {
                alertDiv.style.display = 'none';
            }, 3000);
        }

        function showForgotPassword() {
            showAlert('请联系管理员重置密码', 'error');
        }
    </script>
</body>
</html>