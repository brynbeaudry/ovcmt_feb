@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-2 sidenav">
                @include('includes.sidebar')
            </div>



            <div class="col-sm-9">
                <h2>Instructions</h2>
                <ul class="instructions_list">
                    <a href="#step_1"><li>#  Add an Intake</li></a>
                    <a href="#step_2"><li>#  Add Courses</li></a>
                    <a href="#step_3"><li>#  Add Instructors</li></a>
                    <a href="#step_4"><li>#  Assign Instructors to Courses for the Term</li></a>
                    <a href="#step_5"><li>#  Generate Weekly Schedule</li></a>
                    <a href="#step_6"><li>#  Propagate Schedule</li></a>
                </ul>
                <div id="step_1">
                    <h3><small><a href="{{url('/manageIntake')}}">1. Add an Intake</a></small></h3>
                    <p>Add an intake and edit existing intakes on side</p>
                    <p>The four terms will be automatically generated with each intake</p>
                    <p>Terms are generated with default values so please edit in Manage Terms page before scheduling</p>
                    <div class="intake-image">
                        <span><img src="/images/intake1.png"><img src="/images/intake2.png"></span>
                    </div>
                </div>
                <div id="step_2">
                    <h3><small><a href="{{url('/manageCourse')}}">2. Add Courses</a></small></h3>
                    <p>Add a course and edit existing courses on the side</p>
                    <div class="course-image">
                        <img src="/images/course1.png"><img src="/images/course2.png">
                    </div>
                </div>
                <div id="step_3">
                    <h3><small><a href="{{url('/manageInstructor')}}">3. Add Instructors</a></small></h3>
                    <p>Add an instructor and add courses the instructor can teach</p>
                    <div class="instructor-image">
                        <img src="/images/instructor1.png"><img src="/images/instructor2.png">
                    </div>
                </div>
                <div id="step_4">
                    <h3><small><a href="{{url('/assign')}}">4. Assign Instructors to Courses for the Term</a></small></h3>
                    <p>Select a term, click on an instructor, and assign a course for that instructor to teach for the selected term</p>
                    <div class="assign-image">
                        <img src="/images/assign1.png"><img src="/images/assign2.png"><img src="/images/assign3.png">
                    </div>
                </div>
                <div id="step_5">
                    <h3><small><a href="{{url('/dragDrop')}}">5. Generate Weekly Schedule</a></small></h3>
                    <p>Select a term, drag courses into the desired position and click save</p>
                    <div class="dragdrop-image">
                        <img src="/images/dragdrop1.png"><img src="/images/dragdrop2.png">
                    </div>
                </div>
                <div id="step_6">
                    <h3><small><a href="{{url('/propagateschedule')}}">6. Propagate Schedule</a></small></h3>
                    <p>Select the day you would like to begin propagation from and enter number of weeks to extend that week's schedule for and click "Submit"</p>
                    <div class="propagate-image">
                        <img src="/images/propagate1.png">
                    </div>
                    <p>If a Course runs out of Session Days during the propagation process, note the day the error occurs. (That week will be rolled back)</p>
                    <div class="propagate-error-image">
                        <img src="/images/propagateerror1.png">
                    </div>

                    <p>Return to <a href="{{url('/dragDrop')}}">Generate Schedule</a> and enter the date the propagation failed on then amend the schedule for that week and repeat the process until all courses have been placed.</p>
                    <div class="propagate-error-image">
                        <img src="/images/generateagain1.png">
                    </div>
                </div>
            </div>
    </div>
</div>


@endsection