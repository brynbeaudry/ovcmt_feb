@extends('layouts.app')
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
                <h4><small>Generate Schedule</small></h4>
                <div class="form-inline">
                    {!! Form::open(['url' => '/dragDrop', 'class' => 'form-inline', 'id' => 'select_term']) !!}
                    <select name="selected_term_id" id="selected_term_id" class="form-control">
                        @foreach ($terms as $term)
                            <option value="{{$term->term_id}}">
                                @if($term->intake_no == 'A')
                                {{DateTime::createFromFormat('Y-m-d', $term->program_start)->format('Y')+2}}{{$term->intake_no}}
                                @else
                                {{DateTime::createFromFormat('Y-m-d', $term->program_start)->format('Y')+1}}{{$term->intake_no}}
                                @endif
                                Term {{$term->term_no}}</option>
                        @endforeach
                    </select>
                    {!! Form::submit('Choose Term',['class'=> 'btn btn-primary form-inline']) !!}
                </div>

            </div>
        </div>
    </div>
@endsection