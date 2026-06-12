<?php
use think\facade\Route;

Route::group(function () {
    Route::get('/', 'auth/login');
    Route::get('login', 'auth/login');
    Route::post('login', 'auth/doLogin');
    Route::get('register', 'auth/register');
    Route::post('register', 'auth/doRegister');
    Route::get('captcha', 'auth/captcha');
    Route::get('logout', 'auth/logout');
    Route::get('contact', 'contact/index');
    Route::post('contact/submit', 'contact/submit');
    Route::get('home', function () {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        if ($user['role'] == 3) {
            return redirect('/admin');
        } elseif ($user['role'] == 2) {
            return redirect('/teacher');
        } else {
            return redirect('/student');
        }
    });
})->middleware(\think\middleware\SessionInit::class);

Route::get('admin/login', 'auth/adminLogin')->middleware(\app\middleware\AdminSessionInit::class);
Route::post('admin/login', 'auth/doAdminLogin')->middleware(\app\middleware\AdminSessionInit::class);
Route::get('admin/captcha', 'auth/captcha')->middleware(\app\middleware\AdminSessionInit::class);

Route::group('admin', function () {
    Route::get('/', 'admin.Index/index');
    Route::get('logout', 'auth/adminLogout');
    Route::get('users', 'admin.User/index');
    Route::post('users/resetPassword', 'admin.User/resetPassword');
    Route::get('courses', 'admin.Course/index');
    Route::get('courses/add', 'admin.Course/add');
    Route::post('courses/add', 'admin.Course/add');
    Route::get('courses/edit', 'admin.Course/edit');
    Route::post('courses/edit', 'admin.Course/edit');
    Route::post('courses/delete', 'admin.Course/delete');
    Route::get('classes', 'admin.ClassManager/index');
    Route::post('classes/save', 'admin.ClassManager/save');
    Route::get('classes/get', 'admin.ClassManager/get');
    Route::post('classes/delete', 'admin.ClassManager/delete');
    Route::get('classes/students', 'admin.ClassManager/students');
    Route::post('classes/addStudent', 'admin.ClassManager/addStudent');
    Route::post('classes/removeStudent', 'admin.ClassManager/removeStudent');
    Route::get('questions', 'admin.Question/index');
    Route::get('questions/list', 'admin.Question/list');
    Route::post('questions/save', 'admin.Question/save');
    Route::get('questions/get', 'admin.Question/get');
    Route::post('questions/delete', 'admin.Question/delete');
    Route::post('questions/import', 'admin.Question/import');
    Route::get('questions/importTemplate', 'admin.Question/importTemplate');
    Route::get('questions/getChapters', 'admin.Question/getChapters');
    Route::get('questions/getKnowledges', 'admin.Question/getKnowledges');
    Route::get('system', 'admin.System/index');
})->middleware([\app\middleware\AdminSessionInit::class, \app\middleware\CheckAdminLogin::class]);

Route::group('teacher', function () {
    Route::get('/', 'teacher.Index/index');
    Route::get('courses', 'teacher.Course/index');
    Route::get('courses/detail', 'teacher.Course/detail');
    Route::post('chapters/save', 'teacher.Chapter/save');
    Route::get('chapters/get', 'teacher.Chapter/get');
    Route::post('chapters/delete', 'teacher.Chapter/delete');
    Route::get('knowledges', 'teacher.Knowledge/index');
    Route::post('knowledges/save', 'teacher.Knowledge/save');
    Route::get('knowledges/get', 'teacher.Knowledge/get');
    Route::post('knowledges/delete', 'teacher.Knowledge/delete');
    Route::get('questions', 'teacher.Question/index');
    Route::post('questions/save', 'teacher.Question/save');
    Route::get('questions/detail', 'teacher.Question/detail');
    Route::get('questions/get', 'teacher.Question/get');
    Route::post('questions/delete', 'teacher.Question/delete');
    Route::post('questions/import', 'teacher.Question/import');
    Route::get('questions/importTemplate', 'teacher.Question/importTemplate');
    Route::get('classes', 'teacher.ClassManager/index');
    Route::post('classes/join', 'teacher.ClassManager/join');
    Route::get('students', 'teacher.Student/index');
    Route::get('students/detail', 'teacher.Student/detail');
    Route::get('tasks', 'teacher.Task/index');
    Route::get('tasks/create', 'teacher.Task/create');
    Route::post('tasks/create', 'teacher.Task/create');
    Route::post('tasks/delete', 'teacher.Task/delete');
    Route::get('papers', 'teacher.Paper/index');
    Route::get('papers/add', 'teacher.Paper/add');
    Route::post('papers/add', 'teacher.Paper/add');
    Route::get('papers/questions', 'teacher.Paper/questions');
    Route::get('papers/getChapters', 'teacher.Paper/getChapters');
    Route::post('papers/quickSave', 'teacher.Paper/quickSave');
    Route::get('papers/publish', 'teacher.Paper/publish');
    Route::get('papers/delete', 'teacher.Paper/delete');
    Route::get('papers/preview', 'teacher.Paper/preview');
    Route::get('statistics', 'teacher.Statistics/index');
    Route::get('statistics/grade_review', 'teacher.Statistics/gradeReview');
    Route::post('statistics/save_grade', 'teacher.Statistics/saveGrade');
    Route::get('profile', 'teacher.Index/profile');
    Route::post('profile/update', 'teacher.Index/updateProfile');
    Route::post('profile/password', 'teacher.Index/updatePassword');
})->middleware([\think\middleware\SessionInit::class, \app\middleware\CheckLogin::class]);

Route::group('student', function () {
    Route::get('/', 'student.Index/index');
    Route::get('courses', 'student.Course/index');
    Route::get('courses/detail', 'student.Course/detail');
    Route::get('practice', 'student.Practice/index');
    Route::post('practice/submit', 'student.Practice/submit');
    Route::get('exam', 'student.Exam/index');
    Route::get('exam/start', 'student.Exam/start');
    Route::post('exam/submit', 'student.Exam/submit');
    Route::get('exam/detail', 'student.Exam/detail');
    Route::get('tasks', 'student.Task/index');
    Route::get('tasks/start', 'student.Task/start');
    Route::get('error', 'student.Error/index');
    Route::post('error/remove', 'student.Error/remove');
    Route::post('error/grade', 'student.Error/grade');
    Route::get('material', 'student.Material/index');
    Route::get('classes', 'student.ClassManager/index');
    Route::post('classes/join', 'student.ClassManager/join');
    Route::get('profile', 'student.Index/profile');
    Route::post('profile/update', 'student.Index/updateProfile');
    Route::post('profile/password', 'student.Index/updatePassword');
    Route::post('favorite/add', 'student.Index/favoriteAdd');
    Route::post('error/add', 'student.Index/errorAdd');
    Route::post('ai/generate', 'student.Ai/generate');
    Route::post('ai/grade', 'student.Ai/grade');
    Route::post('ai/report', 'student.Ai/report');
})->middleware([\think\middleware\SessionInit::class, \app\middleware\CheckLogin::class]);