@extends('layouts.viewscheduleapp')

@section('content')
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-2 sidenav">
                @if(Auth::user()->usertype == 'admin')
                    @include('includes.sidebar')
                @endif
				
				@if(Auth::user()->usertype == 'staff')
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
                @endif
            </div>
            <div class="col-sm-10">
                <h4><small>View Schedule</small></h4>
                <hr>
                <div class="col-sm-4">
                    {{Form::open(['url' => 'scheduleinstructor','id' => ''])}}
                    <div class="form-group">
                        {{Form::label('schedule_instructor', 'Instructor:', ['class'=>'control-label'])}}
                        <select name="schedule_instructor" class="form-control">
                            @if(isset($instructors))
                                @foreach($instructors as $instructor)
                                    <option value="{{$instructor->instructor_id}}">{{$instructor->first_name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        {{ Form::submit('Submit',['class'=> 'btn btn-primary form-inline']) }}
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endsection