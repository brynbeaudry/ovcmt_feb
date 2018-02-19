@extends('layouts.app')

@section('title')
    <title>Staff Area</title>
@stop
@section('content')
<div class="container-fluid">
    <div class="row content">
        <div class="col-sm-2 sidenav">
            @if(Auth::user()->usertype == 'admin')
                @include('includes.sidebar')
            @endif
			
			@if(Auth::user()->usertype == 'staff')
                <div>
				
				<br>
				<!-- Master Schedule View -->
                <ul class="nav nav-pills nav-stacked">
                    <li class="active"><a href="{{ url('/schedulemaster') }}" onClick="">Master Schedule View</a></li>
					<li></li>
                </ul>
				
				<!-- Instructor Schedule View -->
                <ul class="nav nav-pills nav-stacked">
                    <li class="active"><a href="{{ url('/selectinstructorschedule') }}" onClick="">Instructor Schedule View</a></li>
					<li></li>
                </ul>
				
				
				<!-- Generate Weekly View -->
				<ul class="nav nav-pills nav-stacked">
                    <li class="active"><a href="{{ url('/selecttermschedule') }}" onClick="">Generate Weekly View</a></li>
					<li></li>
                </ul>
				
				<!-- Student Schedule -->
				<ul class="nav nav-pills nav-stacked">
                    <li class="active"><a href="{{ url('/selectschedulestudent') }}" onClick="">Student Schedule View</a></li>
					<li></li>
                </ul>
				
				<!-- News Page -->
				<ul class="nav nav-pills nav-stacked">
                    <li class="active"><a href="{{ url('/newsPage') }}" onClick="">News</a></li>
					<li></li>
                </ul>
				<br>
				</div>
            @endif

        </div>
        <div class="col-sm-8">
            <h4><small>Welcome</small></h4>
            <hr>
            
        </div>
    </div>
</div>


@endsection
