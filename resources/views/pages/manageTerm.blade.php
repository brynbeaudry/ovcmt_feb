@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-2 sidenav" >
                @include('includes.sidebar')
            </div>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-md-8">
                        <h2>Display Term</h2>
                        {{ Form::open(['url'=>'searchTerm']) }}
                        <div class="form-inline">
                            {{ Form::label('choose_intake', 'Select terms by intake (grad year):', ['class'=>'control-label']) }}
                            <select name="choose_intake" class="form-control">
                                @if(isset($intakes))
                                @foreach($intakes as $intake)
                                <option value="{{$intake->intake_id}}" class="form-control">
                                    @if($intake->intake_no == 'A')
                                        {{DateTime::createFromFormat('Y-m-d', $intake->start_date)->format('Y')+2}}{{$intake->intake_no}}
                                    @else
                                        {{DateTime::createFromFormat('Y-m-d', $intake->start_date)->format('Y')+1}}{{$intake->intake_no}}
                                    @endif
                                </option>
                                @endforeach
                                @endif
                            </select>
                            {{ Form::submit('Submit', ['class'=>'btn btn-primary']) }}
                        </div>
                        {{ Form::close() }}
                        <br>
                        <table class="table table-striped table-bordered table-hover table-condensed text-center">
                            <thead class="thead-default">
                            <tr class = "success">
                                <th class = "text-center">Term Start</th>
                                <th class = "text-center">Term</th>
                                <th class = "text-center">Intake</th>
                                <th class = "text-center">Total wks</th>
                                <th class = "text-center">Course wks</th>
                                <th class = "text-center">Exam wks</th>
                                <th class = "text-center">Break wks</th>
                                <th class = "text-center">Holidays</th>
                                <th class = "text-center">Edit</th>
                                <!--<th class = "text-center">Delete</th>-->
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($terms))
                                @foreach($terms as $term)
                                    <tr>
                                        <td>{{$term->term_start_date}}</td>
                                        <td>{{$term->term_no}}</td>
                                        <td>{{$term->intake_no}}</td>
                                        <td>{{$term->duration_weeks}}</td>
                                        <td>{{$term->course_weeks}}</td>
                                        <td>{{$term->exam_weeks}}</td>
                                        <td>{{$term->break_weeks}}</td>
                                        <td>{{$term->holidays}}</td>
                                        <td><button class="btn btn-primary open-EditTermDialog"
                                                    data-toggle="modal"
                                                    data-id="{{$term->term_id}}"
                                                    data-target="#editTermModal"
                                                    data-term_start_date = "{{$term->term_start_date}}"
                                                    data-intake_id="{{$term->intake_id}}"
                                                    data-course_weeks ="{{$term->course_weeks}}"
                                                    data-break_weeks="{{$term->break_weeks}}"
                                                    data-exam_weeks="{{$term->exam_weeks}}">Edit</button></td>
                                        <!--<td><button class="btn btn-danger open-DeleteTermDialog"
                                                 data-toggle="modal"
                                                 data-target="#deleteTermModal">Delete</button></td>-->
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        <!-- TODO edit term functionality -->
                        <div class="modal fade" id="editTermModal" tabindex="-1" role="dialog" aria-labeleledby="editTermModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="editTermModalLabel">Edit</h4>
                                    </div>
                                    <div class="modal-body">
                                        {{ Form::open(['url' => 'manageTerm']) }}
                                        <p>Edit Term</p>
                                        <div class="form-group">
                                            {!! Form::text('modal_term_id', '', array('id'=>'modal_term_id',
                                                    'class'=>'form-control hide', 'readonly'=>'readonly')) !!}
                                            {!! Form::text('modal_intake_id', '', array('id'=>'modal_intake_id',
                                                    'class'=>'form-control hide','readonly'=>'readonly'))!!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('modal_term_start_date', 'Term Start:', ['class'=>'control-label']) !!}
                                            {!! Form::date('modal_term_start_date', '', array('id'=>'modal_term_start_date',
                                                    'class'=>'form-control')) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('modal_course_weeks', 'Course Weeks:', ['class'=>'control-label']) !!}
                                            {!! Form::number('modal_course_weeks', '', ['class'=>'form-control'])!!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('modal_break_weeks', 'Break Weeks:', ['class'=>'control-label']) !!}
                                            {!! Form::number('modal_break_weeks', '', ['class'=>'form-control'])!!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('modal_exam_weeks', 'Exam Weeks:', ['class'=>'control-label']) !!}
                                            {!! Form::number('modal_exam_weeks', '', ['class'=>'form-control'])!!}
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <!--<button class="btn btn-danger">Delete</button>-->
                                        {!! Form::submit('Save',['class'=> 'btn btn-primary']) !!}
                                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                        <!--<div class="modal fade" id="deleteTermModal" tabindex="-1" role="dialog" aria-labeleledby="deleteTermModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="deleteInstructorModalLabel">Delete Individual Term</h4>
                                    </div>

                                    {!! Form::open(['url' => '/deleteTerm', 'id' => 'deleteTerm']) !!}
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <table class="table table-bordered table-condensed">
                                                {!! Form::submit('Confirm',['class'=> 'btn btn-info',
                                                                 'id' => 'deleteInstructorBtn']) !!}
                                            </table>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="closeDeleteTermBtn" class="btn btn-warning" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>-->
                        <script>
                            $(document).on('click', '.open-EditTermDialog', function() {
                                $('.modal-body #modal_term_start_date').attr('value', '');
                                $('.modal-body #modal_term_id').attr('value', '');
                                $('.modal-body #modal_intake_id').attr('value', '');
                                $('.modal-body #modal_course_weeks').attr('value', '');
                                $('.modal-body #modal_break_weeks').attr('value', '');
                                $('.modal-body #modal_exam_weeks').attr('value', '');
                                $('.modal-body #modal_term_start_date').attr('value', $(this).data('term_start_date'));
                                $('.modal-body #modal_term_id').attr('value', $(this).data('id')).text();
                                $('.modal-body #modal_intake_id').attr('value', $(this).data('intake_id')).text();
                                $('.modal-body #modal_course_weeks').attr('value', $(this).data('course_weeks')).text();
                                $('.modal-body #modal_break_weeks').attr('value', $(this).data('break_weeks')).text();
                                $('.modal-body #modal_exam_weeks').attr('value', $(this).data('exam_weeks')).text();
                            });

                            $(document).on('click', '.open-DeleteTermDialog', function() {
                                document.getElementById('deleteTermForm').reset();
                                var term_id = $(this).parent().siblings(":first").text();
                                console.log(term_id);

                                $('.modal-body #modal_termId_delete').attr('value', term_id);
                            });
                        </script>

                    </div>

            </div>
        </div>
    </div>



@endsection