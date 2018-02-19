@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-2 sidenav" >
                @include('includes.sidebar')
            </div>

        <div class="col-sm-8">
            <h4><small>Manage Course </small></h4>
            <hr>
            @if(Session::has('duplicate_course_id'))
                <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('duplicate_course_id') }}</p>
            @endif
            <button href="#addNewCourse" class="btn btn-default" data-toggle="collapse">Add Course</button>
            <div class="collapse" id="addNewCourse">
                <h2>Add a New Course</h2>

                {!! Form::open(['url' => 'manageCourseStore', 'id' => 'addCourseForm']) !!}
                <div class="form-group">
                    {!! Form::label('course_id2', 'Course Id:') !!}
                    {!! Form::text('course_id2', null, ['class' => 'form-control',
                                                        'required'=> 'true']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('sessions_days2', 'Session Days:') !!}
                    {!! Form::number('sessions_days2', '', array('id'=>'modal_sessionDays_name',
                                                                    'class'=>'form-control',
                                                                    'min'=>1,
                                                                    'max'=>99,
                                                                    'required'=>'true'))!!}
                </div>

                <div class="form-group">
                    {!! Form::label('course_type2', 'Course Type:') !!}
                    {{ Form::select('course_type2', ['Academic'=>'Academic', 'Practical'=>'Practical'], null, array('id'=>'modal_courseType_name',
                                                                                                                     'class'=>'form-control',
                                                                                                                     'required'=>'true')) }}
                </div>
                <div class="form-group">
                    {!! Form::label('color2', 'Course Color:') !!}
                    {{  Form::input('color', 'color2', null, ['id' => 'color']) }}
                </div>

                <div class="form-group">
                    {!! Form::label('term_no2', 'Term No:') !!}&nbsp;&nbsp;&nbsp;&nbsp;
                    {{ Form::radio('term_no2', 1, false, array('id'=>'modal_termNo_name1', 'required'=>'true')) }}&nbsp 1&nbsp;&nbsp;
                    {{ Form::radio('term_no2', 2, false, array('id'=>'modal_termNo_name2', 'required'=>'true')) }}&nbsp 2&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    {{ Form::radio('term_no2', 3, false, array('id'=>'modal_termNo_name3', 'required'=>'true')) }}&nbsp 3&nbsp;&nbsp;&nbsp;
                    {{ Form::radio('term_no2', 4, false, array('id'=>'modal_termNo_name4', 'required'=>'true')) }}&nbsp 4
                </div>

                <div class="form-group">
                    {!! Form::submit('Add course',['class'=> 'btn btn-primary form-control']) !!}
                </div>
                {!! Form::close() !!}
            </div>
            <hr/>
            <h2>Display Courses</h2>
            <br>
            <!-- Search bar -->
            <div class="form-group col-md-7">
                <div class="input-group">
                    <span class="input-group-addon">Search</span>
                    <input type="text" name="search" id ="search" placeholder="Search by Course Id" class ="form-control">
                </div>
            </div>
            <br><br><br>
            <hr>
            <!-- Display course Table -->
            <table id="myTalbe" class="table table-striped table-bordered table-hover table-condensed text-center">
                <thead class="thead-default">
                    <tr class = "success">
                        <th class="text-center">Course ID</th>
                        <th class="text-center">Sessions Days</th>
                        <th class="text-center">Course Type</th>
                        <th class="text-center">Term No</th>
                        <th class="text-center">Color</th>
                        <th class="text-center">Edit Course</th>
                        <th class="text-center">Delete Course</th>
                    </tr>
                </thead>

                <tbody class = "searchCourseBody">

                </tbody>

            </table>
            <script type = "text/javascript">
                $('#search').on('keyup',function(){
                    value = $(this).val();
                    $.ajax ({
                        type : 'GET',
                        url  : '/searchCourse',
                        data: { 'search' : value },
                        success: function (data) {
                            $('.searchCourseBody').html(data);
                        }
                    });
                })
            </script>

            <div class="modal fade" id="editCourseModal" tabindex="-1" role="dialog" aria-labeleledby="editCourseModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="editCourseModalLabel">Edit Individual Course</h4>
                        </div>

                        {!! Form::open(['url' => '/manageCourseUpdate', 'id' => 'editCourseForm']) !!}
                        <div class="modal-body">
                            <div class="form-group">
                                <table class="table table-bordered table-condensed">
                                    <tr class="active">
                                        <td>{!! Form::label('course_id', 'Course ID') !!}</td>
                                        <td>{!! Form::text('course_id', '', array('id'=>'modal_courseid_name',
                                                                                'class'=>'form-control',
                                                                                'readonly' => 'true'
                                                                                ))!!}</td>
                                    </tr>
                                    <tr>
                                        <td>{!! Form::label('sessions_days', 'Session Days') !!}</td>
                                        <td>{!! Form::number('sessions_days', '', array('id'=>'modal_sessionDays_name',
                                                                                    'class'=>'form-control',
                                                                                    'min'=>1,
                                                                                    'max'=>99))!!}</td>
                                    </tr>
                                    <tr>
                                        <td>{!! Form::label('course_type', 'Course Type') !!}</td>
                                        <td>{{ Form::select('course_type', ['Academic' => 'Academic', 'Practical'=> 'Practical'], array('id'=>'modal_courseType_name',
                                                                                                                                        'class'=>'form-control')) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{!! Form::label('term_no', 'Term No') !!}</td>
                                        <td>{{ Form::radio('term_no', 1, false, array('id'=>'modal_termNo_name1')) }}1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            {{ Form::radio('term_no', 2, false, array('id'=>'modal_termNo_name2')) }}2&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            {{ Form::radio('term_no', 3, false, array('id'=>'modal_termNo_name3')) }}3&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            {{ Form::radio('term_no', 4, false, array('id'=>'modal_termNo_name4')) }}4
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{!! Form::label('color', 'Course Color') !!}</td>
                                        <td>{{  Form::input('color', 'color', null, ['id' => 'modal_color']) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            {!! Form::submit('Save',['class'=> 'btn btn-primary form-control',
                                                     'id' => 'editCourseBtn']) !!}
                            <button type="button" id="closeEditCourseBtn" class="btn btn-warning" data-dismiss="modal">Close</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>


            <div class="modal fade" id="deleteCourseModal" tabindex="-1" role="dialog" aria-labeleledby="deleteCourseModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteCourseModalLabel">Delete Individual Course</h4>
                        </div>

                        {!! Form::open(['url' => '/manageCourseDelete', 'id' => 'deleteCourseForm']) !!}
                        <div class="modal-body">
                            <div class="form-group">
                                <table class="table table-bordered table-condensed">
                                    {!! Form::hidden('modal_courseid_delete', '', ['id'=>'modal_courseid_delete']) !!}
                                    {!! Form::submit('Confirm',['class'=> 'btn btn-info',
                                                     'id' => 'deleteCourseBtn']) !!}
                                </table>
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="closeDeleteCourseBtn" class="btn btn-warning" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.open-EditCourseDialog', function() {
        document.getElementById('editCourseForm').reset();
        var course_id = $(this).parent().siblings(":first").text();
        var session_days =  $(this).parent().siblings(":nth-child(2)").text();
        var course_type = $(this).parent().siblings(":nth-child(3)").text();
        var term_no = $(this).parent().siblings(":nth-child(4)").text();
        var color = $(this).parent().siblings(":nth-child(5)").text();

        //TODO: repopulate color
        // retaining original values when edit modal comes up
        $('.modal-body #modal_courseid_name').attr('value', course_id);
        $('.modal-body #modal_sessionDays_name').attr('value', session_days);
        $('select[name="course_type').val("");
        $('select[name="course_type"]').val(course_type);
        if (term_no == 1) {
            $('.modal-body #modal_termNo_name1').attr('checked', 'checked');
        } else if (term_no == 2) {
            $('.modal-body #modal_termNo_name2').attr('checked', 'checked');
        } else if (term_no == 3) {
            $('.modal-body #modal_termNo_name3').attr('checked', 'checked');
        } else {
            // value is none other than 4 folks
            $('.modal-body #modal_termNo_name4').attr('checked', 'checked');
        }
        $('.modal-body #modal_color').attr('value', color);
    });

    $(document).on('click', '.open-DeleteCourseDialog', function() {
        document.getElementById('deleteCourseForm').reset();
        var course_id = $(this).parent().siblings(":first").text();

        $('.modal-body #modal_courseid_delete').attr('value', course_id);
    });
</script>
@endsection