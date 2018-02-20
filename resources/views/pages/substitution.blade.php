@extends('layouts.app')
@section('content')
<style media="screen">
  #subs_table > thead, th {
    background: #428bca;
    color : white;
    text-align: center;
  }

  #subs_table tr:nth-child(even) {
    background: rgba(91,192,222, 0.3)
  }

  #subs_table tr:nth-child(odd) {

  }

  #subs_table{
    text-align: center;
    border-radius: 50%;
    border: 0;
  }
  .float-right {
    float : right;
  }
</style>
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-2 sidenav">
                @if(Auth::user()->usertype == 'admin')
                    @include('includes.sidebar')
                @else
                    <br>
                    <ul class="nav nav-pills nav-stacked">
                        <li class="active"><a href="{{ url('/selectinstructorschedule') }}" onClick="">Schedule View</a></li>
                    </ul><br>
                @endif
            </div>
            <div class="col-sm-10">
                <h4>View Substitutions</h4>
                <hr>
                <button class="btn btn-primary open-addNewSubstitutionModal"
                            data-toggle="modal"
                            data-target="#addNewSubstitutionModal">Add Substitution</button>
                  <br>
                  <br>
                  <table class="table table-hover" id="subs_table">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Cousre Id</th>
                        <th>Term</th>
                        <th>Intake No.</th>
                        <th>Replacement</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Delete?</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if(isset($substitutions))
                        @foreach($substitutions as $sub)
                          <tr>
                            <td>{{$sub->original_instructor}}</td>
                            @if($sub->original_instructor_id == $sub->instructor_id)
                              <td>Instructor</td>
                            @elseif($sub->original_instructor_id == $sub->ta_id)
                              <td>TA</td>
                            @endif
                            <td>{{$sub->course_id}}</td>
                            <td>{{$sub->term_id}}</td>
                            <td>{{$sub->intake_no}}</td>
                            <td>{{$sub->substitute_instructor}}</td>
                            <td>{{$sub->start_date}}</td>
                            <td>{{$sub->end_date}}</td>
                            <td><button class="btn btn-danger btn-xs delete" id="{{$sub->id}}">x</button></td>
                          </tr>
                        @endforeach
                      @endif
                    </tbody>

                  </table>
            </div>


                <!--the modal for adding a new sub -->
                <!-- TODO edit term functionality -->
                <div class="modal fade" id="addNewSubstitutionModal" tabindex="-1" role="dialog" aria-labeleledby="addSubstitutionModal">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="addSubstitutionModal">Add New Substitution</h4>
                            </div>
                            <div class="modal-body" id="sub-modal-body" data-crsindex="-1">
                                {{ Form::open(['url' => '']) }}

                                <div class="form-group">
                                    <label for="searchInstructor" class="col-md-4 control-label">Instructor</label>
                                    <input class="form-control" id="searchInstructor" type="text"  placeholder="Search for an instructor" />
                                    <select id="instructorSelect" class="form-control" style="display:none" size="5" placeholder="" name="instructor" required>
                                      @if(isset($instructors))
                                      @foreach($instructors as $instructor)
                                        <option value="{{$instructor->instructor_id}}">
                                          {{$instructor->first_name}}
                                        </option>
                                      @endforeach
                                      @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('modal_substitution_start_date', 'Substitution Start Date:', ['class'=>'control-label']) !!}
                                    {!! Form::date('modal_substitution_start_date', '', array('id'=>'modal_substitution_start_date',
                                            'class'=>'form-control')) !!}
                                </div>
                                <div class="form-group has-danger">
                                    {!! Form::label('modal_substitution_end_date', 'Substitution End Date:', ['class'=>'control-label']) !!}
                                    {!! Form::date('modal_substitution_end_date', '', array('id'=>'modal_substitution_end_date',
                                            'class'=>'form-control')) !!}
                                    <p class="text-danger" id="end_date_error"></p>
                                </div>
                                <!--Drop down menu -->

                            </div>
                            <div class="modal-footer">

                                <!--<button class="btn btn-danger">Delete</button>-->
                                {!! Form::submit('Save',['class'=> 'btn btn-primary', 'id'=> 'sub_modal_save'] ) !!}
                                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
        </div>
		<script src="{{asset('js/page/substitution.js')}}">



		</script>
    </div>
@endsection
