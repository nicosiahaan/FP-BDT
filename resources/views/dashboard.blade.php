@extends('template.layout')

@section('title','Dashboard')

@section('content')

@if(Session::get('user_type')=='teacher')
<!-- functional button -->
<div class="row justify-content-center">
        <!--Add User -->
        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#adduser">Add User</button>
        <div id="adduser" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add User</h4>
                    </div>
                    <form action="{{ route('register_user') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                            @csrf
                            <div class="form-group">
                                <input type="text" placeholder="Name" name="name" class="form-control">
                            </div> 
                            <div class="form-group">
                                <input type="text" placeholder="Email" name="email" class="form-control">
                            </div> 
                            <div class="form-group">
                                <input type="password" placeholder="Password" name="password" class="form-control">
                            </div> 
                            <div class="form-group">
                                <select name="role">
                                    <option value="teacher">Teacher</option>
                                    <option value="student">Student</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="file" class="form-control" name="image">
                            </div>                            
                    </div>
                    <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Add</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add Examination -->
        <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#addexamination">Add Examination</button>
        <div id="addexamination" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Examination</h4>
                    </div>
                    <form action="{{route('create_examination')}}" method="post">
                    <div class="modal-body">
                            @csrf
                            <div class="form-group">
                                <input type="text" placeholder="Examination" name="name" class="form-control">
                            </div> 
                            <div class="form-group">
                                <input type="number" placeholder="Max Question" min="1" name="max_question" class="form-control">
                            </div> 
                            <div class="form-group">
                                <input type="date" placeholder="Date" name="datetime" class="form-control">
                            </div>
                            <input type="hidden" value="{{Session::get('user_id')}}" name="teacher_id">
                    </div>
                    <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Add</button>
                            <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>

<div class="row" style="margin-top:50px">
    <!-- examination list -->
    <div class="col-lg-3 text-center">
        <table class="table table-hover" >
            <thead>
                <tr>
                    <th><strong>Examinations</strong></th>
                </tr>
            </thead>
            <tbody>
            @foreach($examinations as $exam)
            <tr>
                <td>
                    <form action="{{route('show_examination_detail',$exam->examination_id)}}" method="get">
                        <button type="submit" class="btn btn-info">{{$exam->examination_name}}</button>
                    </form>
                </td>
            </tr>
            @endforeach
            <td class="row justify-content-center">
                {{$examinations->links()}}
            </td>
            </tbody>
        </table>
    </div>
    <!-- Clarifications Teacher-->
    <div class="col-lg-9 text-center">
        <table class="table table-bordered" >
            <h3>Clarifications</h3>
            <thead>
                <tr>
                    <th>Examination</th>
                    <th>Question</th>
                    <th>Asking</th>
                    <th>Answer</th>
                </tr>
            </thead>
            <tbody>
            @foreach($clarifications_teacher as $ct)
            <tr>
                <td>{{$ct->examination_name}}</td>
                <td>{{$ct->question_name}}</td>
                <td>{{$ct->clarification_asking}}</td>
                <td>
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#clarif{{$ct->clarification_id}}">Answer</button>
                <div id="clarif{{$ct->clarification_id}}" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Answer Clarification</h4>
                    </div>
                    <form action="{{route('answer_clarification')}}" method="post">
                    <div class="modal-body">
                            @csrf
                            <div class="form-group">
                                <input type="text" placeholder="Answer" name="answer" class="form-control">
                            </div>
                            <input type="hidden" value="{{$ct->clarification_id}}" name="clarification_id">
                    </div>
                    <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Answer</button>
                            <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                        </form>
                    </div>
                    </div>
                    </div>
                </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <td class="row justify-content-center">
            {{$clarifications_teacher->links()}}
        </td>
    </div>
</div>

@endif


@if(Session::get('user_type')=='student')
@if(count($student_examinations)>0)
<div class="row justify-content-center" style="margin-top:50px">
    <!-- List of Student Examinations -->
    <div class="col-lg-9 text-center">
        <table class="table table-hover" >
            <h3><strong>Examinations Today</strong></h3>
            <thead>
                <th>Examination</th>
                <th>Students</th>
                <th>Question</th>
            </thead>
            <tbody>
            
            @foreach($student_examinations as $se)
            <tr>
                <td>
                    <form action="{{route('do_exam',$se->examination_id)}}" method="get">
                        <input type="hidden" name="page" value="0">
                        <button class="btn-info">{{$se->examination_name}}</button>
                    </form>
                </td>
                <td>
                    @foreach($students as $s)
                        @if($se->examination_id == $s->exam_id)
                            {{$s->total}}
                        @endif
                    @endforeach
                </td>
                <td>
                    {{$se->total}}/{{$se->examination_max_question}}
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <div class="row justify-content-center">
            {{$student_examinations->links()}}
        </div>
    </div>
</div>
@else
    <div class="row justify-content-center" style="margin-top:150px;">
        <h3>"No Examination for today"</h3>   
    </div>
@endif

<div class="row justify-content-center" style="margin-top:50px">
    <!-- List of Student Clarifications -->
    <div class="col-lg-9 text-center">
        <table class="table table-bordered" >
            <h3><strong>Clarifications</strong></h3>
            <thead>
                <th>Examination</th>
                <th>Question</th>
                <th>Ask</th>
                <th>Answer</th>
            </thead>
            <tbody>
            
            @foreach($clarifications_student as $cs)
            <tr>
                <td>
                    {{$cs->examination_name}}
                </td>
                <td>
                    {{$cs->question_name}}
                </td>
                <td>
                    {{$cs->clarification_asking}}
                </td>
                <td>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#ans{{$cs->clarification_id}}">View</button>
                <div id="ans{{$cs->clarification_id}}" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Answer</h4>
                    </div>
                    <div class="modal-body">
                        {{$cs->clarification_answer}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    </div>
                    </div>
                    </div>
                </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <div class="row justify-content-center">
            {{$clarifications_student->links()}}
        </div>
    </div>
</div>

@endif


@endsection