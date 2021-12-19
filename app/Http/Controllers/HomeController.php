<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;

class HomeController extends Controller
{
    public function show_home()
    {
        return view('home');
    }

    public function show_login()
    {
        return view('login');
    }

    public function show_dashboard()
    {
        //teacher
        $examinations = DB::table('examination')->where('examination.teacher_id','=',Session::get('user_id'))->orderBy('examination.examination_datetime','desc')->paginate(3,['*'],'examinations');
        
        //student
        $today =  date("Y-m-d h:i:s");
        $student_examinations = DB::table('studentexamination')->leftJoin('student','student.student_id','=','studentexamination.student_examination_student_id')
                                                                ->leftJoin('examination','examination.examination_id','studentexamination.student_examination_examination_id')
                                                                ->join('question','examination.examination_id','=','question.examination_id')
                                                                ->select('examination.examination_name','examination.examination_id','examination.examination_max_question',
                                                                    DB::raw('count(question.question_id) as total'))
                                                                ->where('student.student_id','=',Session::get('user_id'))
                                                                ->groupBy('examination.examination_name','examination.examination_id','examination.examination_max_question')
                                                                // ->where('examination.examination_datetime',$today)
                                                                ->paginate(3,['*'],'student_examinations');

        //count students per examination
        $students = DB::table('student')->join('studentexamination','studentexamination.student_examination_student_id','=','student.student_id')
                                        ->select('studentexamination.student_examination_examination_id as exam_id',DB::raw('count(*) as total'))
                                        ->groupBy('studentexamination.student_examination_examination_id')
                                        ->get();

        //clarifications for student
        $clarifications_student = DB::table('clarification')->join('question','question.question_id','=','clarification.question_id')
                                                            ->join('examination','examination.examination_id','=','question.examination_id')
                                                            ->where('student_id','=',Session::get('user_id'))
                                                            ->orderBy('clarification_asking_timedate','asc')->paginate(3,['*'],'clarifications_student');

        //clarifications for teacher
        $clarifications_teacher = DB::table('clarification')->join('question','question.question_id','=','clarification.question_id')->join('student','student.student_id','=','clarification.student_id')
                                                            ->join('examination','examination.examination_id','=','question.examination_id')
                                                            ->where('clarification.teacher_id','=',Session::get('user_id'))
                                                            ->where('clarification.clarification_answer','=',NULL)
                                                            ->orderBy('clarification_asking_timedate','asc')
                                                            ->paginate(5,['*'],'clarifications_teacher');


        return view('dashboard',['examinations' => $examinations,'student_examinations'=>$student_examinations,'students'=>$students,'clarifications_student'=>$clarifications_student,'clarifications_teacher'=>$clarifications_teacher]);
    }

    public function answer_clarification(Request $request)
    {
        $answer = $request->get('answer');
        $clarification_id = $request->get('clarification_id');
        $clarification = DB::table('clarification')->where('clarification_id','=',$clarification_id);
        $clarification->update(['clarification_answer'=>$answer]);
        return redirect('/dashboard');
    }
}