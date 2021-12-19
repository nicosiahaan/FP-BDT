<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Student;
use App\Teacher;
use Session;
use DB;

class UserController extends Controller
{
    public function register_user(Request $request)
    {
        $picture = $request->file('image');
        $extension = $picture->getClientOriginalExtension();
        if($request->get('role')=='student')
        {
            $student = new Student();
            $student->student_id = sha1($request->get('name').$request->get('email'));
            $student->student_name = $request->get('name');
            $student->student_email = $request->get('email');
            $student->student_password = sha1($request->get('password'));
            $student->student_profile_image_filename = $picture->getFilename().'.'.$extension;
            $student->student_profile_image_mime = $picture->getClientMimeType();
            $student->student_profile_image_original_filename = $picture->getClientOriginalName();
            $student->save();
            Storage::disk('public')->put($picture->getFilename().'.'.$extension, File::get($picture));
        }
        elseif($request->get('role')=='teacher')
        {
            $teacher = new Teacher();
            $teacher->teacher_id = sha1($request->get('name').$request->get('email'));
            $teacher->teacher_name = $request->get('name');
            $teacher->teacher_email = $request->get('email');
            $teacher->teacher_password = sha1($request->get('password'));
            $teacher->teacher_profile_image_filename = $picture->getFilename().'.'.$extension;
            $teacher->teacher_profile_image_mime = $picture->getClientMimeType();
            $teacher->teacher_profile_image_original_filename = $picture->getClientOriginalName();
            $teacher->save();
            Storage::disk('public')->put($picture->getFilename().'.'.$extension, File::get($picture));
        }
        return redirect('/dashboard');
    }

    public function login(Request $request)
    {
        $password = sha1($request->get('password'));
        $email = $request->get('email');
        $student = DB::table('student')->select('student_password','student_id','student_name')->where('student_email','=',$email)->first();
        $teacher = DB::table('teacher')->select('teacher_password','teacher_id','teacher_name')->where('teacher_email','=',$email)->first();
        $type = '';

        if($student != null)
        {
            $type = 'student';
        }
        elseif($teacher != null)
        {
            $type = 'teacher';
        }
        if($type == 'student' && $password == $student->student_password)
        {
            Session::put('user_id',$student->student_id);
            Session::put('user_name',$student->student_name);
            Session::put('user_login',true);
            Session::put('user_type','student');
            return redirect('/dashboard');
        }
        elseif($type == 'teacher' && $password == $teacher->teacher_password)
        {
            Session::put('user_id',$teacher->teacher_id);
            Session::put('user_name',$teacher->teacher_name);
            Session::put('user_login',true);
            Session::put('user_type','teacher');
            return redirect('/dashboard');
        }
        return back();
    }

    public function logout()
    {
        Session::forget(['user_name','user_login','user_type','user_id']);
        return redirect('/');
    }
}
