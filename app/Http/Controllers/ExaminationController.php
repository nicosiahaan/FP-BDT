<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Examination;
use App\Question;
use App\QuestionImage;
use App\StudentExamination;
use Illuminate\Support\Facades\Redis;
use DB;
use Session;
use App\Answer;
use App\Clarification;

class ExaminationController extends Controller
{
    public function create_examination(Request $request)
    {
        $examination = new Examination();
        $examination->examination_id = sha1($request->get('name'));
        $examination->examination_name = $request->get('name');
        $examination->teacher_id = $request->get('teacher_id');
        $examination->examination_max_question = $request->get('max_question');
        $examination->examination_datetime = $request->get('datetime');
        $examination->save();
        return redirect('/dashboard');
    }

    public function show_examination_detail(Request $request, $examination_id)
    {
        //list of paginated questions
        $questions = DB::table('question')->where('question.examination_id', '=', $examination_id)->paginate(3, ['*'], 'question_page');

        //questions in examination images
        $questionsimages = DB::table('questionimage')->get();

        //sum of questions in examination score
        $max_score = DB::table('question')->where('question.examination_id', '=', $examination_id)->sum('question.question_max_score');

        //current examination details
        $examination = DB::table('examination')->where('examination.examination_id', '=', $examination_id)->first();

        //count students in exam
        $students_exam = DB::table('studentexamination')->where('studentexamination.student_examination_examination_id', '=', $examination_id)->count();

        //take students out of current examination
        $students_out = DB::table('studentexamination')->where('studentexamination.student_examination_examination_id', '=', $examination_id)->select('studentexamination.student_examination_student_id');
        $students = DB::table('student')->whereNotIn('student.student_id', $students_out)->get();

        //total score of students in rank
        $rank = DB::table('studentexamination')->leftJoin('student', 'student.student_id', '=', 'studentexamination.student_examination_student_id')->leftJoin('answer', 'answer.student_id', '=', 'studentexamination.student_examination_student_id')
            ->select(
                DB::raw('sum(answer.answer_score) as total'),
                'studentexamination.student_examination_student_id',
                'student.student_name'
            )
            ->where('studentexamination.student_examination_examination_id', '=', $examination_id)
            ->groupBy('studentexamination.student_examination_student_id', 'student.student_name')
            ->orderBy('total', 'desc')
            ->paginate(3, ['*'], 'rank_page');

        return view('examination.examination_detail', [
            'examination_id' => $examination_id, 'questions' => $questions, 'examination' => $examination,
            'students_exam' => $students_exam, 'max_score' => $max_score,
            'questionsimages' => $questionsimages, 'students' => $students,
            'rank' => $rank
        ]);
    }

    public function create_question(Request $request)
    {

        $exam = DB::table('examination')->where('examination_id', '=', $request->get('examination_id'))->first();
        $count_question = DB::table('question')->where('question.examination_id', '=', $request->get('examination_id'))->count();

        if ($exam->examination_max_question <= $count_question) {
            return back();
        }
        $question = new Question();
        $question->question_id = sha1($request->get('name') . $request->get('description'));
        $question->examination_id = $request->get('examination_id');
        $question->question_name = $request->get('name');
        $question->question_description = $request->get('description');
        $question->question_max_score = $request->get('max_score');
        $question->save();
        $se = StudentExamination::where('student_examination_examination_id', $request->get('examination_id'))->get();
        foreach ($se as $student_exam) {
            $student_exam_questions = DB::table('question')->where('question.examination_id', '=', $student_exam->student_examination_examination_id)->get()->shuffle();
            Redis::connection('write')->set($student_exam->student_examination_student_id . $student_exam->student_examination_examination_id, $student_exam_questions);
        }
        return redirect()->route('show_examination_detail', [$request->get('examination_id')]);
    }

    public function create_image(Request $request)
    {
        $examination = DB::table('question')->where('question.question_id', '=', $request->get('question_id'))->select('question.examination_id as examination_id')->first();
        $picture = $request->file('image');
        $extension = $picture->getClientOriginalExtension();
        Storage::disk('public')->put($picture->getFilename() . '.' . $extension, File::get($picture));

        $questionimage = new QuestionImage();
        $questionimage->question_image_id = sha1($request->get('question_id') . $picture);
        $questionimage->question_id = $request->get('question_id');
        $questionimage->question_image_filename = $picture->getFilename() . '.' . $extension;
        $questionimage->question_image_mime = $picture->getClientMimeType();
        $questionimage->question_image_original_filename = $picture->getClientOriginalName();
        $questionimage->save();
        return redirect()->route('show_examination_detail', [$examination->examination_id]);
    }

    public function assign_student_examination(Request $request)
    {
        // dd(Redis::connection("write"));
        $examination_id = $request->get('examination_id');
        $student_id = $request->get('student');
        $se = new StudentExamination();
        $se->student_examination_student_id = $student_id;
        $se->student_examination_examination_id = $examination_id;
        $se->save();
        $student_exam_questions = DB::table('question')->where('question.examination_id', '=', $examination_id)->get()->shuffle();
        Redis::connection('write')->set($student_id . $examination_id, $student_exam_questions);
        return redirect()->route('show_examination_detail', [$request->get('examination_id')]);
    }

    public function do_exam(Request $request, $examination_id)
    {
        $student_id = Session::get('user_id');

        //take all questions from redist
        $tests = Redis::get($student_id . $examination_id);
        if ((string)empty($tests) == 1) return back();

        //decode into json
        $questions =  json_decode($tests, true);

        //controlling page
        $page = $request->get('page');
        $nextpage = 0;
        $prevpage = 0;
        if ($page == count($questions) - 1) {
            $nextpage = $page;
        } else {
            $nextpage = $page + 1;
        }
        if ($page == 0) {
            $prevpage = 0;
        } else {
            $prevpage = $page - 1;
        }

        //take a question
        $question = $questions[$page];

        //images of current question
        $questionimages = DB::table('questionimage')->where('questionimage.question_id', '=', $question['question_id'])->get();

        //student answer
        $answer = Redis::get("answer" . Session::get('user_id') . $question['question_id']);


        //take teacher who take responsible to this exam
        $teacher = DB::table('teacher')->join('examination', 'examination.teacher_id', '=', 'teacher.teacher_id')->where('examination.examination_id', '=', $examination_id)->first();

        return view('examination.examination_exam', ['question' => $question, 'questionimages' => $questionimages, 'nextpage' => $nextpage, 'prevpage' => $prevpage, 'examid' => $examination_id, 'answer' => $answer, "page" => $page, 'teacher' => $teacher]);
    }

    public function answer(Request $request)
    {
        $question_id = $request->get('question_id');
        $student_id = $request->get('student_id');
        $examination_id = $request->get('examination_id');
        $answer = $request->get('answer');
        $page = $request->get('page');

        //save in redis
        Redis::connection('write')->set("answer" . $student_id . $question_id, $answer);

        //save in mysql
        $answer_id = sha1($student_id . $question_id);
        $answersql = DB::table('answer')->where('answer_id', '=', $answer_id)->get();
        if (count($answersql) == 0) {
            $ans = new Answer();
            $ans->answer_id = $answer_id;
            $ans->question_id = $question_id;
            $ans->student_id = $student_id;
            $ans->answer_description = $answer;
            $ans->answer_datetime = date("Y-m-d h:i:s");
            $ans->save();
        } else {
            $jawab = DB::table('answer')->where('answer_id', '=', sha1($student_id . $question_id));
            $jawab->update(['answer_description' => $answer, 'answer_datetime' => date("Y-m-d h:i:s")]);
        }
        return redirect('/exam/' . $examination_id . '/' . '?page=' . $page);
    }

    public function show_examination_score(Request $request, $examination_id)
    {
        $answers = DB::table('answer')->join('question', 'question.question_id', '=', 'answer.question_id')
            ->join('examination', 'examination.examination_id', '=', 'question.examination_id')
            ->join('student', 'student.student_id', '=', 'answer.student_id')
            ->where('examination.examination_id', '=', $examination_id)
            ->where('answer.answer_score', '=', NULL)
            ->paginate(10);
        $questionimages = DB::table('questionimage')->get();
        return view('examination.examination_scoring', ['answers' => $answers, 'examid' => $examination_id, 'questionimages' => $questionimages]);
    }

    public function scoring(Request $request)
    {
        $answer_id = $request->get('answer_id');
        $examination_id = $request->get('examination_id');
        $score = $request->get('score');
        $ans = DB::table('answer')->where('answer.answer_id', '=', $answer_id);
        $ans->update(['answer.answer_score' => $score]);
        return redirect('/exam/scoring/' . $examination_id);
    }

    public function ask_clarification(Request $request)
    {
        $teacher_id = $request->get('teacher_id');
        $question_id = $request->get('question_id');
        $student_id = $request->get('student_id');
        $ask_clarification_id = sha1($student_id . $question_id . $teacher_id . date("Y-m-d h:i:s"));
        $ask = $request->get('asking');
        $clarification = new Clarification();
        $clarification->clarification_id = $ask_clarification_id;
        $clarification->student_id = $student_id;
        $clarification->teacher_id = $teacher_id;
        $clarification->question_id = $question_id;
        $clarification->clarification_asking = $ask;
        $clarification->clarification_asking_timedate = date("Y-m-d h:i:s");
        $clarification->save();
        $page = $request->get('page');
        $examination_id = $request->get('examination_id');
        return redirect('/exam/' . $examination_id . '/' . '?page=' . $page);
    }
}
