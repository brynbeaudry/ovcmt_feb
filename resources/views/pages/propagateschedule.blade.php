@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-2 sidenav">
                @include('includes.sidebar')
            </div>
            <div class="col-sm-10">
                <h4><small>Propagate Schedule</small></h4>
                <hr>
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group col-md-10 offset-2" id="propagateform">

                            {{Form::open(['url' => '',
                                          'id' => 'dateSelectForm'])}}
                                <button class="glyphicon glyphicon-chevron-left" id="prevweek"></button>
                                {{Form::label('schedule_starting_date', 'Week of:')}}
                                @if(isset($errorDate))
                                    {{Form::date('schedule_starting_date', $errorDate,
                                                                           ['id' => 'schedule_starting_date']) }}
                                @else
                                    {{Form::date('schedule_starting_date', Carbon\Carbon::today(new DateTimeZone('America/Vancouver'),
                                                                           ['id' => 'schedule_starting_date'])) }}
                                @endif
                                <button class="glyphicon glyphicon-chevron-right" id="nextweek"></button>
                                {{ Form::submit('Choose Starting Date',['class'=> 'btn btn-primary form-inline']) }}
                            {{Form::close()}}
                        </div>
                                <script>
                                function convertDate(date) {
                                    var yyyy = date.getFullYear().toString();
                                    var mm = (date.getMonth()+1).toString();
                                    var dd  = date.getDate().toString();

                                    var mmChars = mm.split('');
                                    var ddChars = dd.split('');

                                    return yyyy + '-' + (mmChars[1]?mm:"0"+mmChars[0]) + '-' + (ddChars[1]?dd:"0"+ddChars[0]);
                                }

                                
                                    // update page on submit
                                    function drawPanelAM(roombyday) {
                                        var panel = document.createElement('div');
                                        var panelheading = document.createElement('div');
                                        var panelbody = document.createElement('div');
                                        panel.className=('panel panel-default');
                                        panelheading.className=('panel-heading color-panel');
                                        panelheading.style.backgroundColor=roombyday['am_color'];
                                        panelheading.append(document.createElement('p').appendChild(document.createTextNode(
                                            ' ' + roombyday['am_course_id']+ ' Intake:' + roombyday['am_intake_no'])));
                                        panelbody.className=('panel-body');
                                        if(roombyday['am_instructor_name'] != null && roombyday['am_instructor_name'] != 0) {
                                            panelbody.append(document.createElement('p').appendChild(document.createTextNode(
                                                'Instructor: ' + roombyday['am_instructor_name'])));
                                        }
                                        panelbody.append(document.createElement('br'));
                                        if(roombyday['am_ta_name'] != null && roombyday['am_ta_name'] != 0) {
                                            panelbody.append(document.createElement('p').appendChild(document.createTextNode(
                                                ' TA: ' + (roombyday['am_ta_name'] != null ? roombyday['am_ta_name'] : ""))));
                                        }
                                        panel.append(panelheading);
                                        panel.append(panelbody);
                                        return panel;
                                    }
                                    function drawPanelPM(roombyday) {
                                        var panel = document.createElement('div');
                                        var panelheading = document.createElement('div');
                                        var panelbody = document.createElement('div');
                                        panel.className=('panel panel-default');
                                        panelheading.className=('panel-heading color-panel');
                                        panelheading.style.backgroundColor=roombyday['pm_color'];
                                        panelheading.append(document.createElement('p').appendChild(document.createTextNode(
                                            ' ' + roombyday['pm_course_id']+ ' Intake:' + roombyday['pm_intake_no'])));
                                        panelbody.className=('panel-body');
                                        if(roombyday['pm_instructor_name'] != null && roombyday['pm_instructor_name'] != 0) {
                                            panelbody.append(document.createElement('p').appendChild(document.createTextNode(
                                                'Instructor: ' + roombyday['pm_instructor_name'])));
                                        }
                                        panelbody.append(document.createElement('br'));
                                        if(roombyday['pm_ta_name'] != null && roombyday['pm_ta_name'] != 0) {
                                            panelbody.append(document.createElement('p').appendChild(document.createTextNode(
                                                ' TA: ' + (roombyday['pm_ta_name'] != null ? roombyday['pm_ta_name'] : ""))));
                                        }
                                        panel.append(panelheading);
                                        panel.append(panelbody);
                                        return panel;
                                    }
                                    function dateSwitcher(cdate, week) {
                                        switch (cdate.toString()) {
                                            case week['monday']:
                                                return 'M';
                                            case week['tuesday']:
                                                return 'T';
                                            case week['wednesday']:
                                                return 'W';
                                            case week['thursday']:
                                                return 'Th';
                                            case week['friday']:
                                                return 'F';
                                            default:
                                                alert('error');
                                                break;
                                        }
                                    }
                                    function updateDate(week) {
                                        $('#Mon').text("");
                                        $('#Tues').text("");
                                        $('#Weds').text("");
                                        $('#Thurs').text("");
                                        $('#Fri').text("");
                                        $('#Mon').append(document.createTextNode("Mon " + week['monday']));
                                        $('#Tues').append(document.createTextNode("Tues " + week['tuesday']));
                                        $('#Weds').append(document.createTextNode("Weds " + week['wednesday']));
                                        $('#Thurs').append(document.createTextNode("Thurs " + week['thursday']));
                                        $('#Fri').append(document.createTextNode("Fri " + week['friday']));
                                    }



                                    $(document).ready(function() {
                                        $('#nextweek').click(function(e) {
                                            e.preventDefault();
                                            var date = new Date($('#schedule_starting_date').val());
                                            date.setDate(date.getDate() + 8);
                                            $('#schedule_starting_date').val(convertDate(date));
                                        });
                                        $('#prevweek').click(function(e) {
                                            e.preventDefault();
                                            var date = new Date($('#schedule_starting_date').val());
                                            date.setDate(date.getDate() - 8);
                                            $('#schedule_starting_date').val(convertDate(date));
                                        });
                                        $('#dateSelectForm').on('submit', function(e) {
                                            e.preventDefault();
                                            var selectedDate = $('#schedule_starting_date').val();
                                            $.ajaxSetup({
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content').val()
                                                }
                                            });
                                            $.ajax({
                                                type: 'POST',
                                                url: '/getWeeklySchedule',
                                                data: {"selected_date": selectedDate},
                                                dataType: 'json',
                                                success: function (data) {
                                                    $('#mondayTable').css('visibility', 'visible');
                                                    updateDate(data['datearray']);
                                                    for (let i = 0; i < data['roomsbyday'].length; i++) {
                                                        if (data['roomsbyday'][i]['am_crn'] != null) {
                                                            var tdid = '-AM-' + dateSwitcher(data['roomsbyday'][i]['cdate'], data['datearray']);
                                                            $('#' + data['roomsbyday'][i]['room_id'] + tdid).append(drawPanelAM(data['roomsbyday'][i]));
                                                        }
                                                        if (data['roomsbyday'][i]['pm_crn'] != null) {
                                                            var tdid = '-PM-' + dateSwitcher(data['roomsbyday'][i]['cdate'], data['datearray']);
                                                            $('#' + data['roomsbyday'][i]['room_id'] + tdid).append(drawPanelPM(data['roomsbyday'][i]));
                                                        }
                                                    }
                                                    $('#numberWeeksPropForm').css('visibility', 'visible');
                                                    $('#week_monday').val(data['datearray']['monday']);
                                                }
                                            });
                                        });
                                    });
                                </script>
                        <div id='mondayTable' style='visibility: collapse'>
                            <table class='table table-bordered' id='drag_schedule_table'>
                                <tr>
                                    <th class='drag_schedule_row_head'>Room</th>
                                    <th id="Mon"></th>
                                    <th id="Tues"></th>
                                    <th id="Weds"></th>
                                    <th id="Thurs"></th>
                                    <th id="Fri"></th>
                                </tr>
                                <tbody>
                                    <tr class="M1-AM">
                                        <th>M1-AM</th>
                                        <td id="M1-AM-M"></td>
                                        <td id="M1-AM-T"></td>
                                        <td id="M1-AM-W"></td>
                                        <td id="M1-AM-Th"></td>
                                        <td id="M1-AM-F"></td>
                                    </tr>
                                    <tr class="A1-AM">
                                        <th>A1-AM</th>
                                        <td id="A1-AM-M"></td>
                                        <td id="A1-AM-T"></td>
                                        <td id="A1-AM-W"></td>
                                        <td id="A1-AM-Th"></td>
                                        <td id="A1-AM-F"></td>
                                    </tr>
                                    <tr class="P1-AM">
                                        <th>P1-AM</th>
                                        <td id="P1-AM-M"></td>
                                        <td id="P1-AM-T"></td>
                                        <td id="P1-AM-W"></td>
                                        <td id="P1-AM-Th"></td>
                                        <td id="P1-AM-F"></td>
                                    </tr>
                                    <tr class="P2-AM">
                                        <th>P2-AM</th>
                                        <td id="P2-AM-M"></td>
                                        <td id="P2-AM-T"></td>
                                        <td id="P2-AM-W"></td>
                                        <td id="P2-AM-Th"></td>
                                        <td id="P2-AM-F"></td>
                                    </tr>
                                    <tr > <!--Spacing row-->
                                        <th></th>
                                        @for($i = 0; $i < 5; $i++)
                                            <td></td>
                                        @endfor
                                    </tr>
                                    <tr class="M1-PM">
                                        <th>M1-PM</th>
                                        <td id="M1-PM-M"></td>
                                        <td id="M1-PM-T"></td>
                                        <td id="M1-PM-W"></td>
                                        <td id="M1-PM-Th"></td>
                                        <td id="M1-PM-F"></td>
                                    </tr>
                                    <tr class="A1-PM">
                                        <th>A1-PM</th>
                                        <td id="A1-PM-M"></td>
                                        <td id="A1-PM-T"></td>
                                        <td id="A1-PM-W"></td>
                                        <td id="A1-PM-Th"></td>
                                        <td id="A1-PM-F"></td>
                                    </tr>
                                    <tr class="P1-PM">
                                        <th>P1-PM</th>
                                        <td id="P1-PM-M"></td>
                                        <td id="P1-PM-T"></td>
                                        <td id="P1-PM-W"></td>
                                        <td id="P1-PM-Th"></td>
                                        <td id="P1-PM-F"></td>
                                    </tr>
                                    <tr class="P2-PM">
                                        <th>P2-PM</th>
                                        <td id="P2-PM-M"></td>
                                        <td id="P2-PM-T"></td>
                                        <td id="P2-PM-W"></td>
                                        <td id="P2-PM-Th"></td>
                                        <td id="P2-PM-F"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class='form-group' id='numberWeeksPropForm' style="visibility: hidden">
                    {{Form::open(['url'=>'/extend'])}}
                        {{Form::label('weeks', 'Number of weeks to propagate:')}}
                        {{Form::number('weeks', '', array('id'=>'week',
                                                                        'min'=>1,
                                                                        'max'=>99,
                                                                        'required'=>'true'))}}
                        {{Form::hidden('week_monday', '', array('id'=>'week_monday'))}}
                        {{Form::submit('Submit', ['class'=> 'btn btn-primary form-inline'])}}
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endsection
