@extends('template.layout')

@section('title','Login')

@section('content')

<div class="justify-content-center">
    <div class="row justify-content-center" style="height:100px;width:100%;margin-top:100px">
        <form action="{{route('login')}}" method="post" class="jumbotron">
            @csrf
            <div class="form-group">
                <h3 class="text-center"><strong>Login</strong></h3>
            </div>
            <div class="form-group">
                <input type="text" placeholder="Email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Password" name="password" class="form-control">
            </div> 
            <button type="submit" class="btn btn-info btn-lg">Login</button>
        </form>
    </div>
</div>

@endsection