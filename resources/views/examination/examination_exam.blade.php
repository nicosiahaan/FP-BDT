@extends('template.layout')

@section('title','Exam')

@section('content')
<div class="row justify-content-center">
    <h1>Examination Name</h1>
    <button class="btn btn-danger" data-toggle="modal" data-target="#myModal">Clarification</button>    
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Clarification for Examination Name</h4>
                </div>
                <form action="{{route('ask_clarification')}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="teacher_id" value="{{$teacher->teacher_id}}">
                        <input type="hidden" name="question_id" value="{{$question['question_id']}}">
                        <input type="hidden" name="student_id" value="{{Session::get('user_id')}}">
                        <input type="hidden" name="page" value="{{$page}}">
                        <input type="hidden" name="examination_id" value="{{$teacher->examination_id}}">
                        <input type="text" name="asking" style="border:1px solid black">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info">Ask</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form action="{{route('do_exam',$examid)}}" method="get">
        <input type="hidden" name="page" value="{{$prevpage}}">
        <button class="btn-info">PREV</button>
    </form>

    <form action="{{route('do_exam',$examid)}}" method="get">
        <input type="hidden" name="page" value="{{$nextpage}}">
        <button class="btn-info">NEXT</button>
    </form>

    <div class="col-lg-12" style="height:150px;border:1px solid black">
        <h4><strong>{{$question['question_name']}}</strong></h4>
        <p>{{$question['question_description']}}</p>
    </div>

    <div class="col-lg-12" style="height:250px;border:1px solid black">
        <h4><strong>Image</strong></h4>
        <div class="row">
        @foreach($questionimages as $qi)
            <img style="height:200px;width:200px" src="{{url('uploads/'.$qi->question_image_filename)}}" alt="{{$qi->question_image_filename}}">
        @endforeach
        </div>
    </div>

    <div class="col-lg-7" style="height:150px;border:1px solid black">
        <h4><strong>Answer</strong></h4>
        <form action="{{route('answer')}}" method="post">
            @csrf
            <textarea type="text" name="answer" rows="1" cols="60"></textarea>
            <input type="hidden" name="question_id" value="{{$question['question_id']}}">
            <input type="hidden" name="student_id" value="{{Session::get('user_id')}}">
            <input type="hidden" name="examination_id" value="{{$examid}}">
            <input type="hidden" name="page" value="{{$page}}">
            <button type="submit" class="btn btn-success btn-lg">Save</button>
        </form>
    </div>
    <div class="col-lg-5" style="height:150px;border:1px solid black">
        <h4><strong>Your Answer</strong></h4>
        {{$answer}}
    </div>
</div>
@endsection