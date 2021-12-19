@extends('template.layout')

@section('title','Examination Detail')

@section('content')
@if(Session::get('user_type')=='teacher')
<div class="row justify-content-center">
    <!--Add Question -->
    <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#addquestion">Add Question</button>
    <div id="addquestion" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Question</h4>
                </div>
                <form action="{{route('create_question')}}" method="post">
                <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <input type="text" placeholder="Name" name="name" class="form-control">
                        </div> 
                        <div class="form-group">
                            <textarea placeholder="Description" name="description" id="" cols="48" rows="3"></textarea>
                        </div> 
                        <div class="form-group">
                            <input type="number" placeholder="Score" name="max_score" class="form-control">
                        </div>
                        <input type="hidden" value="{{$examination_id}}" name="examination_id">       
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Student -->
    <button class="btn btn-warning btn-lg" data-toggle="modal" data-target="#addstudent">Add Student</button>
    <div id="addstudent" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Student</h4>
                </div>                    
                <form action="{{route('assign_student_examination')}}" method="post">
                    <div class="modal-body">
                        @csrf
                        <select name="student">
                            <option selected="selected" disabled>-- student --</option>
                            @foreach($students as $student)
                                <option value="{{$student->student_id}}">{{$student->student_name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" value="{{$examination_id}}" name="examination_id">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Assign</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
   <!-- Scoring -->
    <form action="{{route('scoring',$examination_id)}}" method="get">
        <button class="btn btn-info btn-lg">Scoring</button>
    </form>
</div>


<div class="row" style="margin-top:50px">
    <div class="col-lg-6 text-center">
        <!-- List Question -->
        <table class="table table-bordered" >
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($questions as $question)
                <tr>
                    <td>{{ $question->question_name }}</td>
                    <td>
                        <div class="text-center">
                            <button class="btn btn-info" data-toggle="modal" data-target="#image{{$question->question_id}}">Image</button>
                            <div id="image{{$question->question_id}}" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            {{ $question->question_name }}
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{route('create_image')}}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="file" name="image" style="border:1px solid black">
                                                <input type="hidden" value="{{$question->question_id}}" name="question_id">
                                                <button type="submit" class=" btn-info">Save</button>
                                            </form>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                @foreach($questionsimages as $qm)
                                                    @if($qm->question_id == $question->question_id)
                                                    <div class="col-lg-4">
                                                        <img style="height:100px;width:100px" src="{{url('uploads/'.$qm->question_image_filename)}}" alt="{{$qm->question_image_filename}}">
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#detail{{$question->question_id}}">Detail</button>
                            <div id="detail{{$question->question_id}}" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            {{$question->question_name}}
                                        </div>
                                        <div class="modal-body">
                                            {{$question->question_description}}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{$questions->links()}}
    </div>
    <!-- Examination Details -->
    <div class="col-lg-6 text-center">
        <table class="table table-bordered" >
            <tbody>
                <tr>
                    <th>Examination</th>
                    <td>{{$examination->examination_name}}</td>
                </tr>
                <tr>
                    <th>Datetime</th>
                    <td>{{$examination->examination_datetime}}</td>
                </tr>
                <tr>
                    <th>Students</th>
                    <td>{{$students_exam}}</td>
                </tr>
                <tr>
                    <th>Questions</th>
                    <td>{{$questions->total()}}</td>
                </tr>
                <tr>
                    <th>Max Score</th>
                    <td>{{$max_score}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Table Student in Examination -->
<div class="row justify-content-center text-center" style="margin-top:50px">
    <div class="row justify-content-center">
        <h2>Rank</h2>
        <table class="table table-bordered" >
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Total Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rank as $r)
                <tr>
                    <td>{{$r->student_name}}</td>
                    <td>{{$r->total}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$rank->links()}}
    </div>
</div>
@endif

@endsection