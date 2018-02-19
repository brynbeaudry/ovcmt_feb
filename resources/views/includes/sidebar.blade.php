

<script>
    $(document).ready(function(){
        $('#expand').on("click", function() {
            console.log($(this).attr('class'));
            if($(this).attr('class') == "active") {
                $('#expand').removeClass("active");
                $('#manageclick').css('color','#337ab7');
            } else {
                $(this).addClass("active");
                $('#manageclick').css('color','#ffffff');
            }
        });
    });
</script>
<br>

<ul class="nav nav-pills nav-stacked introSidebar" >
    <li><a href="{{ url('/adminauth') }}" onClick="">Instructions</a></li>
</ul><br>
<ul class="nav nav-pills nav-stacked introSidebar">
    <li><a href="{{ url('/addUser') }}" onClick="">Manage Users</a></li>
</ul>
<br>

<ul class="nav nav-pills nav-stacked sidebar">
    <li><a href="{{ url('/manageIntake') }}" onClick="">Manage Intake</a></li>
</ul><br>

<ul class="nav nav-pills nav-stacked sidebar">
    <li><a href="{{ url('/manageCourse') }}" onClick="">Manage Course</a></li>
</ul><br>

<ul class="nav nav-pills nav-stacked sidebar">
    <li><a href="{{ url('/manageInstructor') }}" onClick="">Manage Instructor</a></li>
</ul><br>


<ul class="nav nav-pills nav-stacked sidebar">
    <li><a href="{{ url('/manageTerm') }}" onClick="">Manage Term</a></li>
</ul><br>

<ul class="nav nav-pills nav-stacked sidebar">
    <li><a href="{{ url('/newsPage') }}" onClick="">Manage News</a></li>
</ul><br>

<ul class="nav nav-pills nav-stacked sidebar">
    <li id="expand" class="active"><a href="#schedule" data-toggle="collapse" id="manageclick">Manage Schedule</a></li>
    <div id="schedule" class="collapse">
        <ul class="nav nav-pills nav-stacked sidebar">
            <li><a href="{{ url('/assign') }}">Assign Courses</a></li>
            <li><a href="{{ url('/dragDrop') }}">Generate Weekly Schedule</a></li>
            <li><a href="{{ url('/propagateschedule') }}"> Propagate Weekly Schedule</a></li>
            <li><a href="{{ url('/substitutions')}}">Edit Substitutions</a></li>
            <li><a href="{{ url('/selectschedulestudent')}}">View Student Schedule</a></li>
            <li><a href="{{ url('/selectinstructorschedule')}}">View Instructor Schedule</a></li>
            <li><a href="{{ url('/schedulemaster')}}">View Master Schedule</a></li>
        </ul>
    </div>
</ul><br>



<br>

<br>
