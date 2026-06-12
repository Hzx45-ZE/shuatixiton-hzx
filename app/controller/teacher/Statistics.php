<?php
declare(strict_types=1);

namespace app\controller\teacher;

use app\model\ClassInfo;
use app\model\ClassStudent;
use app\model\User;
use app\model\PracticeRecord;
use app\model\ErrorQuestion;
use app\model\Question;
use app\model\Knowledge;
use think\facade\View;
use think\facade\Session;
use think\Request;

class Statistics
{
    public function index(Request $request)
    {
        $user = Session::get('user');
        
        $classId = $request->param('class_id');
        
        $classes = ClassInfo::where('teacher_id', $user['id'])->select();
        
        $selectedClass = null;
        $avgTrend = [];
        $knowledgeStats = [];
        $studentRanking = [];
        $errorDistribution = [];
        
        if ($classId && $classes->where('id', $classId)->count()) {
            $selectedClass = ClassInfo::find($classId);
            
            $studentIds = ClassStudent::where('class_id', $classId)->where('status', 1)->column('student_id');
            
            $avgTrend = $this->getAvgTrend($studentIds);
            
            $knowledgeStats = $this->getKnowledgeStats($studentIds);
            
            $studentRanking = $this->getStudentRanking($studentIds);
            
            $errorDistribution = $this->getErrorDistribution($studentIds);
        }
        
        View::assign([
            'user' => $user,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'avgTrend' => $avgTrend,
            'knowledgeStats' => $knowledgeStats,
            'studentRanking' => $studentRanking,
            'errorDistribution' => $errorDistribution,
        ]);
        
        return View::fetch('teacher/statistics');
    }
    
    private function getAvgTrend($studentIds)
    {
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i day"));
            $records = PracticeRecord::whereIn('user_id', $studentIds)
                ->where('status', 1)
                ->where('create_time', '>=', $date . ' 00:00:00')
                ->where('create_time', '<=', $date . ' 23:59:59')
                ->select();
            
            $totalScore = 0;
            $count = 0;
            foreach ($records as $record) {
                if ($record->score > 0 && $record->total_score > 0) {
                    $totalScore += ($record->score / $record->total_score) * 100;
                    $count++;
                }
            }
            
            $trend[] = [
                'date' => $date,
                'avg_score' => $count > 0 ? round($totalScore / $count, 1) : 0,
            ];
        }
        return $trend;
    }
    
    private function getKnowledgeStats($studentIds)
    {
        $recordIds = PracticeRecord::whereIn('user_id', $studentIds)->column('id');
        $details = \app\model\PracticeDetail::whereIn('record_id', $recordIds)->select();
        
        $knowledgeMap = [];
        foreach ($details as $detail) {
            $question = Question::find($detail['question_id']);
            if ($question && $question['knowledge_id']) {
                $knowledgeId = $question['knowledge_id'];
                if (!isset($knowledgeMap[$knowledgeId])) {
                    $knowledgeMap[$knowledgeId] = ['correct' => 0, 'total' => 0];
                }
                $knowledgeMap[$knowledgeId]['total']++;
                if ($detail['is_correct'] == 1) {
                    $knowledgeMap[$knowledgeId]['correct']++;
                }
            }
        }
        
        $stats = [];
        foreach ($knowledgeMap as $knowledgeId => $data) {
            $knowledge = Knowledge::find($knowledgeId);
            $stats[] = [
                'name' => $knowledge ? $knowledge['name'] : '未知',
                'correct' => $data['correct'],
                'total' => $data['total'],
                'rate' => round(($data['correct'] / $data['total']) * 100, 1),
            ];
        }
        
        usort($stats, function($a, $b) {
            return $b['rate'] - $a['rate'];
        });
        
        return $stats;
    }
    
    private function getStudentRanking($studentIds)
    {
        $students = User::whereIn('id', $studentIds)->where('role', 1)->select();
        
        $ranking = [];
        foreach ($students as $student) {
            $records = PracticeRecord::where('user_id', $student['id'])
                ->where('status', 1)
                ->select();
            
            $totalScore = 0;
            $count = 0;
            foreach ($records as $record) {
                if ($record->score > 0 && $record->total_score > 0) {
                    $totalScore += ($record->score / $record->total_score) * 100;
                    $count++;
                }
            }
            
            $ranking[] = [
                'id' => $student['id'],
                'name' => $student['real_name'],
                'avg_score' => $count > 0 ? round($totalScore / $count, 1) : 0,
                'exam_count' => $count,
            ];
        }
        
        usort($ranking, function($a, $b) {
            return $b['avg_score'] - $a['avg_score'];
        });
        
        return $ranking;
    }
    
    private function getErrorDistribution($studentIds)
    {
        $errors = ErrorQuestion::whereIn('user_id', $studentIds)->select();
        
        $typeMap = [];
        foreach ($errors as $error) {
            $question = Question::find($error['question_id']);
            if ($question) {
                $typeId = $question['type_id'];
                if (!isset($typeMap[$typeId])) {
                    $typeMap[$typeId] = ['count' => 0, 'name' => ''];
                }
                $typeMap[$typeId]['count'] += $error['wrong_count'];
                
                if (!$typeMap[$typeId]['name']) {
                    $type = \app\model\QuestionType::find($typeId);
                    $typeMap[$typeId]['name'] = $type ? $type['name'] : '未知';
                }
            }
        }
        
        $distribution = [];
        foreach ($typeMap as $data) {
            $distribution[] = $data;
        }
        
        usort($distribution, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        return $distribution;
    }

    public function studentDetail(Request $request)
    {
        $user = Session::get('user');
        $studentId = $request->param('student_id');
        $student = \app\model\User::find($studentId);
        if (!$student || $student->role != 1) {
            return redirect('/teacher/students');
        }
        
        $records = PracticeRecord::where('user_id', $studentId)
            ->where('status', 1)
            ->order('create_time', 'desc')
            ->select();
        
        // 统计待审核题目数量
        $pendingCount = \app\model\PracticeDetail::whereIn('record_id', function($q) use ($studentId) {
                $q->name('practice_record')->where('user_id', $studentId)->where('status', 1)->field('id');
            })
            ->whereNotNull('ai_score')
            ->where('grade_status', 'pending')
            ->count();
        
        View::assign([
            'user' => $user,
            'student' => $student,
            'records' => $records,
            'pending_count' => $pendingCount,
        ]);
        
        return View::fetch('teacher/student_detail');
    }

    public function gradeReview(Request $request)
    {
        $user = Session::get('user');
        
        $details = \app\model\PracticeDetail::with(['record.user', 'question'])
            ->whereNotNull('ai_score')
            ->where('grade_status', 'pending')
            ->order('answer_time', 'desc')
            ->paginate(20);
        
        $count = \app\model\PracticeDetail::whereNotNull('ai_score')
            ->where('grade_status', 'pending')
            ->count();
        
        View::assign([
            'user' => $user,
            'details' => $details,
            'pending_count' => $count,
        ]);
        
        return View::fetch('teacher/grade_review');
    }

    public function saveGrade(Request $request)
    {
        $user = Session::get('user');
        $id = $request->post('id');
        $action = $request->post('action');
        $teacherScore = $request->post('teacher_score');

        $detail = \app\model\PracticeDetail::find($id);
        if (!$detail) {
            return json(['code' => 0, 'msg' => '记录不存在']);
        }

        if ($action === 'accept') {
            $detail->final_score = $detail->ai_score;
        } elseif ($action === 'override') {
            if ($teacherScore === null || $teacherScore === '') {
                return json(['code' => 0, 'msg' => '请输入教师评分']);
            }
            $detail->final_score = floatval($teacherScore);
        } else {
            return json(['code' => 0, 'msg' => '无效操作']);
        }

        $detail->grade_status = 'reviewed';
        $detail->save();

        $record = \app\model\PracticeRecord::find($detail->record_id);
        if ($record) {
            $currentScore = $record->score ?? 0;
            $newScore = $currentScore - floatval($detail->ai_score) + floatval($detail->final_score);
            $record->score = max(0, $newScore);
            $record->save();
        }

        return json(['code' => 1, 'msg' => '审核完成']);
    }
}