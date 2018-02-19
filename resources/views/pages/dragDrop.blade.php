@extends('layouts.app')
@section('content')
    <head>
        <link href="/css/color.css" rel="stylesheet">
    </head>
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-2 sidenav tohide">
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
                <h4 id="page-title"><small>Weekly Schedule</small></h4>
                <hr>
				<button class="btn btn-mg btn-default"> <a href="javascript:window.print()">Print Schedule</a></button>
				
                <div class="row">
                    <div class="col-md-10">
					
                        <div class="form-group">
                            <h3 id="print-title" hidden>Week of {{$calendarDetails['firstOfWeek']}}</h3>
                            <h3>Week of {{$calendarDetails['firstOfWeek']}}</h3>
                            {{Form::open(['url'=>'dragDrop'])}}
                            <div class="form-group">
                                <!-- TODO Gylphicons clickable to next/prev week-->
                                <input type="hidden" name="selected_term_id" value="{{$term->term_id}}"/>
                                <button class="glyphicon glyphicon-chevron-left week_date_control" id="week_back"></button>
                                <input type="date" id="schedule_select" name="schedule_select_date" value="{{$calendarDetails['goToDate']}}">
                                <button class="glyphicon glyphicon-chevron-right week_date_control" id="week_forward"></button>
                                {{Form::submit('Submit')}}
                            </div> <!--div inner form-group end-->
                            {{Form::close()}}
                        </div> <!--div outter form-group end-->
						
                        <script>
                            function convertDate(date) {
                                var yyyy = date.getFullYear().toString();
                                var mm = (date.getMonth()+1).toString();
                                var dd  = date.getDate().toString();

                                var mmChars = mm.split('');
                                var ddChars = dd.split('');

                                return yyyy + '-' + (mmChars[1]?mm:"0"+mmChars[0]) + '-' + (ddChars[1]?dd:"0"+ddChars[0]);
                            }

                            $('#week_forward').click(function(e) {
                                e.preventDefault();
                                var date = new Date($('#schedule_select').val());
                                date.setDate(date.getDate() + 8);
                                $('#schedule_select').val(convertDate(date));

                                $(this).submit();
                            });
                            $('#week_back').click(function(e) {
                                e.preventDefault();
                                var date = new Date($('#schedule_select').val());
                                date.setDate(date.getDate() - 6);
                                $('#schedule_select').val(convertDate(date));
                                $(this).submit();
                            });
                        </script>
                    </div> <!-- div col-md-10 end-->
					
                    <div class="col-sm-2">
                        <h2>Course List</h2>
                        @if($term->intake_no == 'A')
                  4      <h3>{{DateTime::createFromFormat('Y-m-d', $term->start_date)->format('Y')+2}}{{$term->intake_no}} Term:{{$term->term_no}}</h3>
                        @else
                        <h3>{{DateTime::createFromFormat('Y-m-d', $term->start_date)->format('Y')+1}}{{$term->intake_no}} Term:{{$term->term_no}}</h3>
                        @endif
                    </div> <!-- div col-sm-2 end -->

				</div> <!-- div row end -->
				
                <div class="row">
                    <div class="col-md-10">
                        {!! Form::open(['url' => 'addschedule']) !!}
                        <input type="hidden" name="selected_term_id" value="{{$term->term_id}}"/>
                        <!-- This is so we know where to send the application when we finish saving-->
                        <input type="hidden" name="schedule_date" value="{{$calendarDetails['goToDate']}}"/>
                        <table class='table table-bordered' id='drag_schedule_table'>
                            <thead>
                            <tr>
                                <th class='drag_schedule_row_head'>Room</th>
                                <th>Mon {{$calendarDetails['mon']}}</th>
                                <th>Tues {{$calendarDetails['tues']}}</th>
                                <th>Wed {{$calendarDetails['wed']}}</th>
                                <th>Thurs {{$calendarDetails['thurs']}}</th>
                                <th>Fri {{$calendarDetails['fri']}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="drag_schedule_row">
                                <th class='drag_schedule_row_head'>M1-AM</th>
                                @for($i=0; $i<5; $i++)
                                    <td ondrop="drop(event, this)" ondragover="allowDrop(event)" class="M1-am[] drop-timeslot">
                                        {!! Form::hidden('M1-am[]','empty', ['class'=>'timeslot_input']) !!}</td>
                                @endfor
                            </tr>
                            <tr class="drag_schedule_row">
                                <th class='drag_schedule_row_head'>A1-AM</th>
                                @for($i=0; $i<5; $i++)
                                    <td ondrop="drop(event, this)" ondragover="allowDrop(event)" class="A1-am[] drop-timeslot">
                                        {!! Form::hidden('A1-am[]', 'empty',['class'=>'timeslot_input']) !!}</td>
                                @endfor
                            </tr>
                            <tr class="drag_schedule_row">
                                <th class='drag_schedule_row_head'>P1-AM</th>
                                @for($i=0; $i<5; $i++)
                                    <td ondrop="drop(event, this)" ondragover="allowDrop(event)" class="P1-am[] drop-timeslot">
                                        {!! Form::hidden('P1-am[]', 'empty', ['class'=>'timeslot_input']) !!}</td>
                                @endfor
                            </tr>
                            <tr class="drag_schedule_row">
                                <th class='drag_schedule_row_head'>P2-AM</th>
                                @for($i=0; $i<5; $i++)
                                    <td ondrop="drop(event, this)" ondragover="allowDrop(event)" class="P2-am[] drop-timeslot">
                                        {!! Form::hidden('P2-am[]', 'empty', ['class'=>'timeslot_input']) !!}</td>
                                @endfor
                            </tr>
                            <tr > <!--Spacing row-->
                                <th></th>
                                @for($i=0; $i<5; $i++)
                                    <td></td>
                                @endfor
                            </tr>
                            <tr class="drag_schedule_row">
                                <th class='drag_schedule_row_head'>M1-PM</th>
                                @for($i=0; $i<5; $i++)
                                    <td ondrop="drop(event, this)" ondragover="allowDrop(event)" class="M1-pm[] drop-timeslot">
                                        {!! Form::hidden('M1-pm[]', 'empty', ['class'=>'timeslot_input']) !!}</td>
                                @endfor
                            </tr>
                            <tr class="drag_schedule_row">
                                <th class='drag_schedule_row_head'>A1-PM</th>
                                @for($i=0; $i<5; $i++)
                                    <td ondrop="drop(event, this)" ondragover="allowDrop(event)" class="A1-pm[] drop-timeslot">
                                        {!! Form::hidden('A1-pm[]', 'empty', ['class'=>'timeslot_input']) !!}</td>
                                @endfor
                            </tr>
                            <tr class="drag_schedule_row">
                                <th class='drag_schedule_row_head'>P1-PM</th>
                                @for($i=0; $i<5; $i++)
                                    <td ondrop="drop(event, this)" ondragover="allowDrop(event)" class="P1-pm[] drop-timeslot">
                                        {!! Form::hidden('P1-pm[]', 'empty', ['class'=>'timeslot_input']) !!}</td>
                                @endfor
                            </tr>
                            <tr class="drag_schedule_row">
                                <th class='drag_schedule_row_head'>P2-PM</th>
                                @for($i=0; $i<5; $i++)
                                    <td ondrop="drop(event, this)" ondragover="allowDrop(event)" class="P2-pm[] drop-timeslot">
                                        {!! Form::hidden('P2-pm[]', 'empty', ['class'=>'timeslot_input']) !!}</td>
                                @endfor
                            </tr>
                            </tbody>
                        </table>  <!-- draggable schedule table ends here-->
						
						@if(Auth::user()->usertype == 'admin')
                    
							<!-- Save & Clear buttons start here--> 
							<div class="form-group">
								{!! Form::submit('Save', ['class'=>'btn btn-primary']) !!}
								{!! Form::close() !!}
								<button class='btn btn-primary' id='clearScheduleBtn' onclick="clearSchedule()">Clear</button>
							</div>
							<!-- Save & Clear buttons ends here--> 
							
						@endif
                        @foreach ($roomsByWeek as $timeslot)
								<script>
                                var dayOfWeek ='<?php echo $timeslot->cdayOfWeek;?>' - 2; //decrement to account for array and MySQL
                                var room_id ='<?php echo $timeslot->room_id;?>';
                                var crn='<?php echo $timeslot->crn;?>';
                                var grad_year = '<?php
                                                    $date= DateTime::createFromFormat('Y-m-d', $timeslot->start_date);
                                                    if($timeslot->intake_no == 'A') {
                                                        echo $date->format('Y')+2;
                                                    } else {
                                                        echo $date->format('Y')+1;
                                                    }?>';
                                var intake_no = '<?php echo $timeslot->intake_no;?>';
                                var color = '<?php echo $timeslot->color;?>';
                                var course_id ='<?php echo $timeslot->course_id;?>';
                                var timeSlotName = room_id+'-'+'<?php echo $timeslot->time;?>'+'[]';
                                var instructor = '<?php echo $timeslot->name;?>';
                                var ta = '<?php echo $timeslot->ta_name;?>'
                                //TODO set to not hardcoded practical
                                appendToTimeSlot(new CourseOfferingPanel(course_id, crn, instructor, ta, intake_no, grad_year, color),
                                    timeSlotName, dayOfWeek);
                            </script>
                        @endforeach
						

                    </div> <!-- col-md-10 end -->
				
					@if(Auth::user()->usertype != 'admin')  <!--Column List Start-->
					
					@else
                    <div class="col-sm-2 drag_course_offering_list" id="courses_listing_panel">
                        @foreach($courseOfferings as $course)
                            <script>
                                var course_id = '<?php echo $course->course_id;?>';
                                var crn = '<?php echo $course->crn;?>';
                                var grad_year = '<?php
                                    $date= DateTime::createFromFormat('Y-m-d', $term->start_date);
                                    if($term->intake_no == 'A') {
                                        echo $date->format('Y')+2;
                                    } else {
                                        echo $date->format('Y')+1;
                                    }?>';
                                var intake_no = '<?php echo $term->intake_no;?>';
                                var color = '<?php echo $course->color;?>';
                                var sessions = '<?php echo $courseOfferingsSessions[$course->crn];?>';
                                var instructor = '<?php echo $course->name;?>';
                                var ta = '<?php echo $course->ta_name;?>'
                                appendToCourseListings(new CourseListingPanel(course_id, crn, instructor, ta, intake_no, grad_year, color, sessions));
                            </script>
                        @endforeach
                    @endif
					</div><!--Column List Panel End-->
					  
				</div>	
			</div>
        </div>
    </div>
@endsection
