@extends('template.layout')

@section('title','Scoring')

@section('content')

<h2 class="text-center" style="text-decoration:underline">Scoring</h2>

@if(count($answers) != 0)
@foreach($answers as $ans)
<div class="row justify-content-center" style="margin-top:25px">
    <div class="col-lg-4" style="border:1px solid black;height:200px">
        <p>Question : {{$ans->question_description}}</p>
        <div class="row justify-content-center">
            @foreach($questionimages as $qi)
                @if($qi->question_id == $ans->question_id)
                    <img style="height:150px;width:150px" src="{{url('uploads/'.$qi->question_image_filename)}}" alt="">
                @endif
            @endforeach
        </div>
    </div>
    <div class="col-lg-5" style="border:1px solid black;height:200px">
        <p>Answered by : {{$ans->student_name}}</p>
        <p>Answer : <strong>{{$ans->answer_description}}</strong></p>
    </div>

    <div class="col-lg-3" style="border:1px solid black;height:200px">
        <form action="{{route('scoringanswer')}}" method="post">
            @csrf
            <input type="hidden" name="answer_id" value="{{$ans->answer_id}}">
            <input type="hidden" name="examination_id" value="{{$examid}}">
            <div class="row" style="margin-top:25px">
                <input type="number" name="score" min="0" max="{{$ans->question_max_score}}" style="border:1px solid black">
                <button type="submit" class="btn btn-success">Submit</button>
                Max Score : {{$ans->question_max_score}}
            </div>
        </form>
    </div>
</div>
@endforeach
@else
<h4 class="text-center" style="font-style:italic;margin-top:50px">No Answers for Scoring</h4>
@endif

@endsection