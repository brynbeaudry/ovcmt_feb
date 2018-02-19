@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row content">
        <div class="col-sm-2 sidenav" >
            @include('includes.sidebar')
        </div>

        <div class="col-sm-8">
            <h4><small>Manage Instructors </small></h4>
            <hr>
            @if(session('duplicate_instructor_email'))
                <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('duplicate_instructor_email') }}</p>
            @endif
            @if(session('duplicate_course_instructor'))
                <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('duplicate_course_instructor') }}</p>
            @endif
            <button href="#addNewInstructor" class="btn btn-default" data-toggle="collapse">Add Instructor</button>
            <div class="collapse" id="addNewInstructor">
                <h2>Add a New Instructor</h2>
            {!! Form::open(['url' => 'manageInstructor']) !!}
                    {{csrf_field()}}
                    <div class="form-group">
                    {!! Form::label('first_name', 'First Name:') !!}
                    {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                    {!! Form::label('email', 'Email:') !!}
                    {!! Form::text('email', null, ['class' => 'form-control']) !!}
                    </div>
                <p>Check all time slots for which the instructor is available:</p>
                <div class="form-group">
                    {!! Form::label('date_start', 'Date effective:') !!}
                    {!! Form::date('date_start') !!}
                </div>
                <div class="form-group">
                <table class = "table table-striped table-bordered table-hover table-condensed">
                    <tr class = "info">
                        <th>Time</th><th>Mon</th><th>Tues</th><th>Wed</th><th>Thurs</th><th>Fri</th>
                    </tr>
                    <tr>
                        <td>Morning</td>
                        <td>{!! Form::checkbox('mon_am') !!}</td>
                        <td>{!! Form::checkbox('tues_am') !!}</td>
                        <td>{!! Form::checkbox('wed_am') !!}</td>
                        <td>{!! Form::checkbox('thurs_am') !!}</td>
                        <td>{!! Form::checkbox('fri_am') !!}</td>

                    </tr>
                    <tr>
                        <td>Afternoon</td>
                        <td>{!! Form::checkbox('mon_pm') !!}</td>
                        <td>{!! Form::checkbox('tues_pm') !!}</td>
                        <td>{!! Form::checkbox('wed_pm') !!}</td>
                        <td>{!! Form::checkbox('thurs_pm') !!}</td>
                        <td>{!! Form::checkbox('fri_pm') !!}</td>
                    </tr>
                </table>
                </div>

                <div class="form-group">
                    {!! Form::submit('Add instructor',['class'=> 'btn btn-primary form-control']) !!}
                </div>

                {!! Form::close() !!}
            </div> <!-- Close the add instructor div-->
            <hr/>



<!-- Display instructor -->
            <h2>Display Instructors</h2><br>
                <div class="form-group col-md-7">
                    <div class="input-group">
                        <span class="input-group-addon">Search</span>
                        <input type="text" name="search" id ="search" placeholder="Search by Instructor Name" class ="form-control">
                    </div>
                </div>
                <br><br><br>
                <hr>
            <table class="table table-striped table-bordered table-hover table-condensed text-center">
                <thead class="thead-default">
                    <tr class = "success">
                        <th class="text-center">ID</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Mon AM</th>
                        <th class="text-center">Tues AM</th>
                        <th class="text-center">Wed AM</th>
                        <th class="text-center">Thur AM</th>
                        <th class="text-center">Fri AM</th>
                        <th class="text-center">Mon PM</th>
                        <th class="text-center">Tues PM</th>
                        <th class="text-center">Wed PM</th>
                        <th class="text-center">Thur PM</th>
                        <th class="text-center">Fri PM</th>
                        <th class="text-center">Edit Instructor</th>
                        <th class="text-center">Teachable Courses</th>
                        <th class="text-center">Delete</th>
                    </tr>
                </thead>

            <tbody class = "searchbody">

            </tbody>

            </table>
            <script type = "text/javascript">
                $('#search').on('keyup',function(){
                    value = $(this).val();
                    $.ajax ({
                        type : 'GET',
                        url  : '/searchInstructor',
                        data: { 'search' : value },
                        success: function (data) {
                                $('.searchbody').html(data);
                        }
                    });
                })
            </script>
<!-- end display -->

            <div class="modal fade" id="editInstructorModal" tabindex="-1" role="dialog" aria-labeleledby="editInstructorModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="editInstructorModalLabel">Edit</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(['url' => 'editInstructor']) !!}
                            <p>New Availability</p>
                            <div class="form-group">
                                {!! Form::hidden('modal_instructor_id', '', array('id'=>'modal_instructor_id')) !!}
                                {!! Form::label('modal_instructor_name', 'Instructor:') !!}
                                {!! Form::text('modal_instructor_name', '', array('id'=>'modal_instructor_name','readonly'=>'readonly'))!!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('modal_instruct_avail_start_date', 'Effective date:') !!}
                                {!! Form::date('modal_instruct_avail_start_date')!!}
                            </div>
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <th>Time</th><th>Mon</th><th>Tues</th><th>Wed</th><th>Thurs</th><th>Fri</th>
                                    </tr>
                                    <tr>
                                        <td>Morn</td>
                                        <td>{!! Form::checkbox('modal_mon_am') !!}</td>
                                        <td>{!! Form::checkbox('modal_tues_am') !!}</td>
                                        <td>{!! Form::checkbox('modal_wed_am') !!}</td>
                                        <td>{!! Form::checkbox('modal_thurs_am') !!}</td>
                                        <td>{!! Form::checkbox('modal_fri_am') !!}</td>
                                    </tr>
                                    <tr>
                                        <td>Aft</td>
                                        <td>{!! Form::checkbox('modal_mon_pm') !!}</td>
                                        <td>{!! Form::checkbox('modal_tues_pm') !!}</td>
                                        <td>{!! Form::checkbox('modal_wed_pm') !!}</td>
                                        <td>{!! Form::checkbox('modal_thurs_pm') !!}</td>
                                        <td>{!! Form::checkbox('modal_fri_pm') !!}</td>
                                    </tr>
                                </table>
                            </div>
                            <div>
                                <h4>Courses this instructor can teach</h4>
                                <div id="courseListing"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            {!! Form::submit('Save',['class'=> 'btn btn-primary']) !!}
                            <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                            {!! Form::close() !!}
                        </div>
                        <script>
                            $(document).on('click', '.open-EditInstructorDialog', function() {
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                var instructor_id = $(this).parent().siblings(":first").text();
                                var instructor_name = $(this).parent().siblings(":nth-child(2)").text();
                                $('.modal-body #modal_instructor_id').attr('value',instructor_id);
                                $('.modal-body #modal_instructor_name').attr('value',instructor_name);
                                $.ajax({
                                    type: 'POST',
                                    url: '/showInstructorDetails',
                                    data: {"instructor_id": instructor_id},
                                    dataType: 'json',
                                    success: function (data) {
                                        $('#courseListing').empty();
                                        for (let i = 0; i < data['courses'].length; i++) {
                                            var panel = "<div class='panel panel-default'><div class='panel-heading'><div class='row'><div class='col-sm-4 text-left'>" + data['courses'][i]['course_id']
                                                +"</div><div class='col-md-8 text-right'>" +
                                                '{{Form::open(["url" => "deleteCourseInstructor"])}}' +
                                                "<input type='hidden' name='instructor_id' value='" + data['courses'][i]['instructor_id'] + "'>" +
                                                "<input type='hidden' name='course_id' value='" + data['courses'][i]['course_id'] + "'>" +
                                                "<button class='btn btn-danger' type='submit' value='Submit'>Delete</button>" + "</form>" +
                                                "</div></div></div> <div class='panel-body'>" +
                                                "Intake: " + data['courses'][i]['intake_no'] +
                                                ((data['courses'][i]['instructor_type'] == 1) ? " - Instructor" : " - TA")
                                                + "</div></div>";
                                            $('#courseListing').append(panel);
                                        }
                                        var avail = data['avail'][0];
                                        $('input[name="modal_instruct_avail_start_date"]').val(avail['date_start']);
                                        $('input:checkbox[name="modal_mon_am"]')
                                            .prop('checked', (avail['mon_am'] == 1) ? true : false);
                                        $('input:checkbox[name="modal_tues_am"]')
                                            .prop('checked', (avail['tues_am'] == 1) ? true : false);
                                        $('input:checkbox[name="modal_wed_am"]')
                                            .prop('checked', (avail['wed_am'] == 1) ? true : false);
                                        $('input:checkbox[name="modal_thurs_am"]')
                                            .prop('checked', (avail['thurs_am'] == 1) ? true : false);
                                        $('input:checkbox[name="modal_fri_am"]')
                                            .prop('checked', (avail['fri_am'] == 1) ? true : false);
                                        $('input:checkbox[name="modal_mon_pm"]')
                                            .prop('checked', (avail['mon_pm'] == 1) ? true : false);
                                        $('input:checkbox[name="modal_tues_pm"]')
                                            .prop('checked', (avail['tues_pm'] == 1) ? true : false);
                                        $('input:checkbox[name="modal_wed_pm"]')
                                            .prop('checked', (avail['wed_pm'] == 1) ? true : false);
                                        $('input:checkbox[name="modal_thurs_pm"]')
                                            .prop('checked', (avail['thurs_pm'] == 1) ? true : false);
                                        $('input:checkbox[name="modal_fri_pm"]')
                                            .prop('checked', (avail['fri_pm'] == 1) ? true : false);
                                    }
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="assignInstructorModal" tabindex="-1" role="dialog" aria-labeleledby="assignInstructorModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="assignInstructorModallabel">Choose Courses This Instructor Teach</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(['url' => 'courseInstructor']) !!}
                            <div class="form-group">
                                {!! Form::hidden('course_instructor_id', '', array('id'=>'course_instructor_id')) !!}
                            </div>
                            <div class="form-group">
                                <select id="course_id" name="course_id" class="form-control">
                                    @foreach($courses as $course)
                                        <option name="course_id">{{$course->course_id}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <b>Choose 1: </b>
                                <label class="radio-inline">
                                    <input type="radio" id = "a" name ="intake_no" value ="A" checked="checked" />Intake A
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id = "b" name ="intake_no" value ="B" />Intake B
                                </label>
                            </div>
                            <div class="form-group">
                                <b>Choose 1: </b>
                                <label class="radio-inline">
                                    <input type="radio" id = "ta" name ="instructor_type" value ="0" />TA
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id = "inst" name ="instructor_type" value ="1" checked="checked"/>Instructor
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            {!! Form::submit('Assign',['class'=> 'btn btn-primary ', 'id'=>'addbtn']) !!}
                            <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                            {!! Form::close() !!}
                        </div>
                        <script>
                            $(document).on('click', '.open-AssignCourseDialog', function() {
                                var instructor_id1 = $(this).parent().siblings(":first").text();
                                $('.modal-body #course_instructor_id').attr('value',instructor_id1);
                            });
                        </script>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="deleteInstructorModal" tabindex="-1" role="dialog" aria-labeleledby="deleteInstructorModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteInstructorModalLabel">Delete Individual Instructor</h4>
                        </div>

                        {!! Form::open(['url' => '/manageInstructorDelete', 'id' => 'deleteInstructorForm']) !!}
                        <div class="modal-body">
                            <div class="form-group">
                                <table class="table table-bordered table-condensed">
                                    {!! Form::hidden('modal_instructorid_delete', '', ['id'=>'modal_instructorid_delete']) !!}
                                    {!! Form::submit('Confirm',['class'=> 'btn btn-info',
                                                     'id' => 'deleteInstructorBtn']) !!}
                                </table>
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="closeDeleteInstructorBtn" class="btn btn-warning" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        $(document).on('click', '.open-DeleteInstructorDialog', function() {
            document.getElementById('deleteInstructorForm').reset();
            var instructor_id = $(this).parent().siblings(":first").text();
            $('.modal-body #modal_instructorid_delete').attr('value', instructor_id);
        });
    </script>
@endsection