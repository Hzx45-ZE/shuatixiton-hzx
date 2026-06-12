<?php
declare(strict_types=1);

namespace app\controller\admin;

use app\model\SystemLog;
use think\facade\View;
use think\facade\Session;

class System
{
    public function index()
    {
        $user = Session::get('user');
        
        $logs = SystemLog::order('create_time', 'desc')->limit(50)->select();
        
        View::assign([
            'user' => $user,
            'logs' => $logs,
        ]);
        
        return View::fetch('admin/system');
    }
}
