<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    
    <!-- Title -->
    <title>
        EssayExam | @yield('title')
    </title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('template-sources/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
    <div class="container">
        <a class="navbar-brand" href="#">EssayExam</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                @if(Session::get('user_login')!=true)
                <li class="nav-item ">
                    <a class="nav-link" href="{{url('/')}}">
                        Home
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="{{url('/login')}}">
                        Login
                    </a>
                </li>
                @endif

                @if(Session::get('user_login')==true)
                <li class="nav-item ">
                    <a class="nav-link" href="{{url('/dashboard')}}">
                        Dashboard
                    </a>
                </li>
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="">
                        {{Session::get('user_name')}}
                    </a>

                    <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="">Profile</a></li>
                        <li><a href="{{url('/logout')}}">Logout</a></li>
                    </ul>
                </li>
                @endif

            </ul>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="container">
    @yield('content')
</div>

<!-- Bootstrap core JavaScript -->
<script type="text/javascript" src="{{ asset('template-sources/jquery/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('template-sources/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

</body>

</html>