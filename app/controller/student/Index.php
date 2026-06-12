<?php
declare(strict_types=1);

namespace app\controller\student;

use app\model\User;
use app\model\Course;
use app\model\ClassInfo;
use app\model\ClassStudent;
use app\model\StudyTask;
use app\model\ErrorQuestion;
use app\model\FavoriteQuestion;
use app\model\Question;
use app\model\PracticeRecord;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Index
{
    public function index()
    {
        $user = Session::get('user');
        
        $classStudent = ClassStudent::where('student_id', $user['id'])->find();
        $className = '';
        if ($classStudent) {
            $classInfo = ClassInfo::find($classStudent['class_id']);
            $className = $classInfo ? $classInfo['name'] : '';
        }
        
        $favoriteQuestions = FavoriteQuestion::where('user_id', $user['id'])
            ->order('create_time', 'desc')
            ->limit(5)
            ->select();
        $favorites = [];
        foreach ($favoriteQuestions as $fq) {
            $question = Question::find($fq['question_id']);
            if ($question) {
                $favorites[] = $question->toArray();
            }
        }
        
        $errorCount = ErrorQuestion::where('user_id', $user['id'])->count();
        $practiceCount = PracticeRecord::where('user_id', $user['id'])->where('type', 'practice')->count();
        $examCount = PracticeRecord::where('user_id', $user['id'])->where('type', 'exam')->count();
        $favoriteCount = FavoriteQuestion::where('user_id', $user['id'])->count();
        
        $tasks = StudyTask::where('status', 1)->select();
        
        $taskList = [];
        foreach ($tasks as $task) {
            $deadline = $task['deadline'] ?? '';
            if ($deadline && !is_numeric($deadline)) {
                $deadline = strtotime($deadline);
            }
            $task['formatted_deadline'] = $deadline ? date('Y-m-d H:i', (int)$deadline) : '未设置';
            $taskList[] = $task;
        }
        
        View::assign([
            'user' => $user,
            'className' => $className,
            'favorites' => $favorites,
            'errorCount' => $errorCount,
            'practiceCount' => $practiceCount,
            'examCount' => $examCount,
            'favoriteCount' => $favoriteCount,
            'tasks' => $taskList,
        ]);
        
        return View::fetch('student/index');
    }

    public function profile()
    {
        $user = Session::get('user');
        $userInfo = User::find($user['id']);
        
        $classStudent = ClassStudent::where('student_id', $user['id'])->find();
        $className = '';
        if ($classStudent) {
            $classInfo = ClassInfo::find($classStudent['class_id']);
            $className = $classInfo ? $classInfo['name'] : '';
        }
        
        View::assign([
            'user' => $userInfo,
            'className' => $className,
        ]);
        
        return View::fetch('student/profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Session::get('user');
        $data = $request->post();
        
        $updateData = [];
        if (isset($data['nickname'])) {
            $updateData['nickname'] = trim($data['nickname']);
        }
        if (isset($data['phone'])) {
            $updateData['phone'] = trim($data['phone']);
        }
        if (isset($data['email'])) {
            $updateData['email'] = trim($data['email']);
        }
        if (isset($data['gender'])) {
            $updateData['gender'] = (int)$data['gender'];
        }
        
        if (empty($updateData)) {
            return json(['code' => 0, 'msg' => '没有需要修改的信息']);
        }
        
        User::where('id', $user['id'])->update($updateData);
        $newUser = User::find($user['id'])->toArray();
        Session::set('user', $newUser);
        
        return json(['code' => 1, 'msg' => '个人信息修改成功']);
    }

    public function updatePassword(Request $request)
    {
        $user = Session::get('user');
        $oldPassword = $request->post('old_password');
        $newPassword = $request->post('new_password');
        $confirmPassword = $request->post('confirm_password');
        
        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            return json(['code' => 0, 'msg' => '请填写完整信息']);
        }
        
        if ($newPassword !== $confirmPassword) {
            return json(['code' => 0, 'msg' => '两次输入的新密码不一致']);
        }
        
        if (strlen($newPassword) < 6) {
            return json(['code' => 0, 'msg' => '新密码长度不能少于6位']);
        }
        
        $userModel = User::find($user['id']);
        if (!$userModel->verifyPassword($oldPassword)) {
            return json(['code' => 0, 'msg' => '原密码错误']);
        }
        
        $userModel->password = $newPassword;
        $userModel->save();
        
        return json(['code' => 1, 'msg' => '密码修改成功，请重新登录']);
    }

    public function favoriteAdd(Request $request)
    {
        $user = Session::get('user');
        $questionId = $request->post('question_id');
        
        if (!$questionId) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        $exists = FavoriteQuestion::where('user_id', $user['id'])
            ->where('question_id', $questionId)
            ->find();
        
        if ($exists) {
            return json(['code' => 1, 'msg' => '已收藏过该题目']);
        }
        
        FavoriteQuestion::create([
            'user_id' => $user['id'],
            'question_id' => $questionId,
        ]);
        
        return json(['code' => 1, 'msg' => '收藏成功']);
    }

    public function errorAdd(Request $request)
    {
        $user = Session::get('user');
        $questionId = $request->post('question_id');
        
        if (!$questionId) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        $exists = ErrorQuestion::where('user_id', $user['id'])
            ->where('question_id', $questionId)
            ->find();
        
        if ($exists) {
            $exists->wrong_count = $exists->wrong_count + 1;
            $exists->last_wrong_time = date('Y-m-d H:i:s');
            $exists->save();
            return json(['code' => 1, 'msg' => '已加入错题本']);
        }
        
        ErrorQuestion::create([
            'user_id' => $user['id'],
            'question_id' => $questionId,
            'wrong_count' => 1,
            'last_wrong_time' => date('Y-m-d H:i:s'),
        ]);
        
        return json(['code' => 1, 'msg' => '已加入错题本']);
    }
}
