<?php
declare(strict_types=1);

namespace app\controller;

use app\model\Notice;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Contact
{
    public function index()
    {
        $user = Session::get('user');
        
        View::assign([
            'user' => $user,
        ]);
        
        return View::fetch('contact/index');
    }
    
    public function submit(Request $request)
    {
        $user = Session::get('user');
        $content = $request->post('content', '');
        $contact = $request->post('contact', '');
        
        if (empty($content)) {
            return json(['code' => 0, 'msg' => '请输入反馈内容']);
        }
        
        $notice = Notice::create([
            'title' => '用户反馈 - ' . $user['real_name'],
            'content' => $content,
            'type' => 'feedback',
            'target_role' => 'admin',
            'creator_id' => $user['id'],
            'status' => 0,
        ]);
        
        if ($notice) {
            return json(['code' => 1, 'msg' => '反馈提交成功，管理员将尽快处理']);
        }
        
        return json(['code' => 0, 'msg' => '提交失败，请重试']);
    }
}