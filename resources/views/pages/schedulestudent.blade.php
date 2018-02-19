@extends('layouts.viewscheduleapp')

@section('content')
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
			
			@if(Auth::user()->usertype == 'student')
                <br>
                <ul class="nav nav-pills nav-stacked">
                    <li class="active"><a href="{{ url('/selectschedulestudent') }}" onClick="">Schedule View</a></li>
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
            <h4><small>Display schedule</small></h4>
            <h3 id="print-title">Grad
                @if($details['intake_info']->intake_no=='A')
                {{$details['intake_info']->start_date->format('Y')+2}}{{$details['intake_info']->intake_no}}
                @else
                {{$details['intake_info']->start_date->format('Y')+1}}{{$details['intake_info']->intake_no}}
                @endif
                {{$details['schedule_date']->format('F Y')}}
            </h3>
            <hr>
            <button class="btn btn-mg btn-default"> <a href="javascript:window.print()">Print Schedule</a></button>
        <div id ="pdf2htmldiv">
            <div class="row" >
                <div class="col-md-6">

                    <h3><!--<span class="glyphicon glyphicon-chevron-left"></span>-->
                        {{$details['schedule_date']->format('F Y')}}
                        <!--<span class="glyphicon glyphicon-chevron-right"></span></h3>-->
                        {{Form::open(['url' => 'schedulestudent','id' => 'dateSelectForm'])}} </h3>
                        <div class="form-group">
                            <input type="hidden" name="schedule_intake" value="{{$details['intake_id']}}">

                            <!-- TODO Gylphicons clickable to next/prev week-->
                            <!--<button class="glyphicon glyphicon-chevron-left week_date_control" id="week_back"></button>-->
                            <input type="date" id="schedule_select" name="schedule_starting_date" value="{{$details['schedule_date']->format('Y-m-01')}}">
                            <!--<button class="glyphicon glyphicon-chevron-right week_date_control" id="week_forward"></button>-->
                            {{ Form::submit('Submit') }}
                        </div>
                    {{Form::close()}}

                    <br><br>

                </div>
                <div class="col-md-6">
                    <h3 style="float:right">Intake
                        @if($details['intake_info']->intake_no=='A')
                        {{$details['intake_info']->start_date->format('Y')+2}}{{$details['intake_info']->intake_no}}
                        @else
                        {{$details['intake_info']->start_date->format('Y')+1}}{{$details['intake_info']->intake_no}}
                        @endif
                    </h3>
                </div>
            </div>

            <div >
                <table class="table table-striped table-bordered table-hover text-center" id="schedule_view_table">
                    <thead class="thead-default">
                        <tr class="success">
                            <th>Mon</th>
                            <th>Tues</th>
                            <th>Wed</th>
                            <th>Thurs</th>
                            <th>Fri</th>
                        </tr>
                    </thead>
                    <tbody>
                    @for($i=0; $i<sizeof($weeks);$i++)
                        <tr class="schedule_wk_{{$i}}">
                        <!-- first row -->
                        @if($i==0)
                            @for($j=0; $j<5-sizeof($weeks[$i]); $j++)
                                <td></td>
                            @endfor
                            @for($j=0; $j<sizeof($weeks[$i]); $j++)
                                    <td><span class="schedule_day_of_month">{{$weeks[$i][$j]}}</span>
                                        <div class="am"></div>
                                        <div class="pm"></div>
                                    </td>
                            @endfor
                        <!-- last row -->
                        @elseif($i == sizeof($weeks)-1)
                            @for($j=0; $j<sizeof($weeks[$i]); $j++)
                                <td><span class="schedule_day_of_month">{{$weeks[$i][$j]}}</span>
                                    <div class="am"></div>
                                    <div class="pm"></div>
                                </td>
                            @endfor
                            @for($j=0; $j<5-sizeof($weeks[$i]);$j++)
                                <td></td>
                            @endfor
                        @else
                            @for($j=0; $j<5; $j++)
                                <td>
                                    <span class="schedule_day_of_month">{{$weeks[$i][$j]}}</span>
                                    <div class="am"></div>
                                    <div class="pm"></div>
                                </td>
                            @endfor
                        @endif
                        </tr>
                    @endfor
                    </tbody>
                </table>

                @foreach($courses['am_courses'] as $amcourse)
                    <script>
                        var course_id = '<?php echo $amcourse->course_id;?>';
                        var room_id =  '<?php echo $amcourse->room_id;?>';
                        var color = '<?php echo $amcourse->color;?>';
                        var instructor = '<?php echo $amcourse->first_name;?>';
                        var date = new Date('<?php echo $amcourse->cdate;?>').getUTCDate();
                        var dates = document.getElementsByTagName('span');
                        for(var i=0; i<dates.length; i++) {
                            if(dates.item(i).innerHTML == date) {
                                dates.item(i).nextElementSibling.append(new Panel(course_id, room_id, color, instructor, 'AM', i));
                                break;
                            }
                        }
                    </script>
                @endforeach

                @foreach($courses['pm_courses'] as $pmcourse)
                    <script>
                        var course_id = '<?php echo $pmcourse->course_id;?>';
                        var room_id =  '<?php echo $pmcourse->room_id;?>';
                        var color = '<?php echo $pmcourse->color;?>';
                        var instructor = '<?php echo $pmcourse->first_name;?>';
                        var date = new Date('<?php echo $pmcourse->cdate;?>').getUTCDate();
                        var dates = document.getElementsByTagName('span');
                        for(var i=0; i<dates.length; i++) {
                            if(dates.item(i).innerHTML == date) {
                                dates.item(i).nextElementSibling.nextElementSibling.append(new Panel(course_id, room_id, color, instructor, 'PM', i));
                                break;
                            }
                        }
                    </script>
                @endforeach
            </div>
        </div>
    </div>
    </div>
</div>

<!--
            <script type="text/javascript">
                function pdfToHTML(){
                    var pdf = new jsPDF('p', 'pt', 'letter');
                    source = $('#pdf2htmldiv')[0];
                    specialElementHandlers = {
                        '#bypassme': function(element, renderer){
                            return true
                        }
                    }
                    margins = {
                        top: 50,
                        left: 60,
                        width: 545
                    };
                    pdf.fromHTML(
                        source // HTML string or DOM elem ref.
                        , margins.left // x coord
                        , margins.top // y coord
                        , {
                            'width': margins.width // max width of content on PDF
                            , 'elementHandlers': specialElementHandlers
                        },
                        function (dispose) {
                            // dispose: object with X, Y of the last line add to the PDF
                            //          this allow the insertion of new lines after html
                            pdf.save('ovcmtTimetable.pdf');
                        }
                    )
                }

            </script>-->


@endsection
