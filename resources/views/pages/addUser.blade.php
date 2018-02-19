@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-2 sidenav" >
                @include('includes.sidebar')
            </div>

            <div class="col-sm-10">
                <h4><small>Manage User </small></h4>
                <hr>
                
                <div class="col-sm-10">
                    @if (session()->has('message'))
                    <p class="alert {{ Session::get('alert-class', 'alert-success') }}">{{ session()->get('message') }}</p>
                    @elseif ($errors->any())
                        @if ($errors->has('name'))
                        <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ $errors->first('name') }}</p>
                        @endif
                        @if ($errors->has('email'))
                        <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ $errors->first('email') }}</p>
                        @endif
                        @if ($errors->has('password'))
                        <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ $errors->first('password') }}</p>
                        @endif
                    @endif
                    <form class="col-sm-10" role="form" method="POST" action="{{ url('/addUsers') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <div class="col-sm-4">
                                <label for="name">Name</label>
                            </div>
                            <div class="col-sm-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                            </div>
                        </div>
                        <br>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <br>
                            <div class="col-sm-4">
                                <label for="email" >E-Mail Address</label>
                            </div>
                            <div class="col-sm-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <br>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <br>
                            <div class="col-sm-4">
                                <label for="password" >Password</label>
                            </div>
                            <div class="col-sm-6">
                                <input id="password" type="password"  class="form-control" name="password" required>
                            </div>
                        </div>
                        <br>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <br>
                            <div class="col-sm-4">
                                <label for="password-confirm" >Confirm Password</label>
                            </div>
                            <div class="col-sm-6">
                            <input id="password-confirm" type="password"  class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <br>
                            <div class="col-sm-10">
                              <label for="usertype" >Select Account Type</label> &nbsp
                              <br>
                              <label class="radio-inline"><input type="radio" name="usertype" value="admin" checked>Admin</label>
                              <label class="radio-inline"><input type="radio" name="usertype" value="staff">Staff</label>
                              <label class="radio-inline"><input type="radio" name="usertype" value="student">Student</label>
                            </div>
                        </div>
                        
                        <div class="col-sm-10 form-group">
                            <br>
                            <button type="submit" class="btn btn-primary pull-right">Register</button>
                        </div>
                    </form>
                </div>


            <div class="col-sm-9">
                <h4><small>Select user to display</small></h4>
                <hr>
                <!-- <button type="button" class="btn btn-primary" id="adminButton">Admin</button>
                <button type="button" class="btn btn-primary" id="staffButton">Staff</button>
                <button type="button" class="btn btn-primary" id="studentButton">Students</button> -->
                <input type="radio" id="adminButton" checked> Admin</input>
                <input type="radio" id="staffButton" style="margin-left: 30px;"> Staff</input>
                <input type="radio" id="studentButton" style="margin-left: 30px;"> Student</input>
            </div>

            <!-- ADMIN TABLE -->
            <div class="col-sm-9 adminTable visible" id="adminTable">
                <tr>
            <h2>Admins</h2>
            <table class="table table-striped table-bordered table-hover table-condensed text-center ">
                <thead>
                <tr class = "success">
                    <th class="text-center">ID</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Delete</th>
                </tr>
                </thead>
                <tbody>

                @foreach($admins as $admin)
                    <tr>
                        <td class="text-center">{{$admin->id}}</td>
                        <td class="text-center name" data-name="{{$admin->name}}">{{$admin->name}}</td>
                        <td class="text-center email" data-name="{{$admin->email}}">{{$admin->email}}</td>
                        <td class="text-center">
                            <button class=" btn btn-primary open-editUserDialog"
                                    data-toggle="modal"
                                    data-target="#editUserModal">Edit</button>
                        </td>
                        <td class="text-center">
                            <button class=" btn btn-danger open-DeleteAdminUserDialog"
                                    data-toggle="modal"
                                    data-target="#deleteAdminUserModal">Delete</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>

            <!-- STAFF TABLE -->
            <div class="col-sm-9 staffTable hidden" id="staffTable">
                <tr>
            <h2>Staff</h2>
            <table class="table table-striped table-bordered table-hover table-condensed text-center ">
                
				<thead><!-- Table Header -->
                <tr class = "success">
                    <th class="text-center">ID</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Delete</th>
                </tr>
                </thead>
				
                <tbody><!-- Table Body -->
				@foreach($staffs as $staff)
                    <tr>
                        <td class="text-center">{{$staff->id}}</td>
                        <td class="text-center name" data-name="{{$staff->name}}">{{$staff->name}}</td>
                        <td class="text-center email" data-name="{{$staff->email}}">{{$staff->email}}</td>
                        <td class="text-center">
                            <button class=" btn btn-primary open-editUserDialog"
                                    data-toggle="modal"
                                    data-target="#editUserModal">Edit</button>
                        </td>
                        <td class="text-center">
                            <button class=" btn btn-danger open-DeleteAdminUserDialog"
                                    data-toggle="modal"
                                    data-target="#deleteAdminUserModal">Delete</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
			<!-- STAFF TABLE END -->
			
            <!-- STUDENT TABLE -->
            <div class="col-sm-9 staffTable hidden" id="studentTable">
                <tr>
            <h2>Students</h2>
            <table class="table table-striped table-bordered table-hover table-condensed text-center ">
                <thead>
                <tr class = "success">
                    <th class="text-center">ID</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Delete</th>
                </tr>
                </thead>
                <tbody>

                @foreach($students as $student)
                    <tr>
                        <td class="text-center">{{$student->id}}</td>
                        <td class="text-center name" data-name="{{$student->name}}">{{$student->name}}</td>
                        <td class="text-center email" data-name="{{$student->email}}">{{$student->email}}</td>
                        <td class="text-center">
                            <button class=" btn btn-primary open-editUserDialog"
                                    data-toggle="modal"
                                    data-target="#editUserModal">Edit</button>
                        </td>
                        <td class="text-center">
                            <button class=" btn btn-danger open-DeleteAdminUserDialog"
                                    data-toggle="modal"
                                    data-target="#deleteAdminUserModal">Delete</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>

			<!-- Edit Button -->
            <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labeleledby="editUserModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="editUserModalLabel">Edit User</h4>
                        </div>

                        {!! Form::open(['url' => '/editUser', 'id' => 'editUserForm']) !!}
                        <div class="modal-body">
                            <div class="form-group">
                                <?php 

                                ?>
                                <table class="table table-bordered table-condensed">
                                    {!! Form::hidden('modal_user_edit', '', ['id'=>'modal_user_edit']) !!}
                                    {!! Form::label('modal_nameLabel_edit', 'Name: ', ['class'=>'control-label']) !!}
                                    <br>
                                    {!! Form::text('modal_name_edit', '', array('id'=>'modal_name_edit'))!!}
                                    <br><br>
                                    {!! Form::label('modal_emailLabel_edit', 'Email: ', ['class'=>'control-label']) !!}
                                    <br>
                                    {!! Form::text('modal_email_edit', '', array('id'=>'modal_email_edit')) !!}
                                    <br><br>
                                    
                                    {!! Form::submit('Save',['class'=> 'btn btn-info',
                                                     'id' => 'editUserButton']) !!}
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
			<!-- Edit Button End -->
			
			<!-- Delete Button -->
            <div class="modal fade" id="deleteAdminUserModal" tabindex="-1" role="dialog" aria-labeleledby="deleteAdminUserModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteAdminUserModalLabel">Delete Admin User</h4>
                        </div>

                        {!! Form::open(['url' => '/adminUserDelete', 'id' => 'adminUserDeleteForm']) !!}
                        <div class="modal-body">
                            <div class="form-group">
                                <table class="table table-bordered table-condensed">
                                    {!! Form::hidden('modal_adminUserId_delete', '', ['id'=>'modal_adminUserId_delete']) !!}
                                    {!! Form::submit('Confirm',['class'=> 'btn btn-info',
                                                     'id' => 'deleteAdminBtn']) !!}
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
			<!-- Delete Button End -->

        </div>
    </div>
    
    <script>
        // CODE FOR EDIT USERS
        $(document).on('click', '.open-editUserDialog', function() {
            document.getElementById('editUserForm').reset();
            var user_id = $(this).parent().siblings(":first").text();
            var user_name = $(this).parent().siblings(".name").text();
            var user_email = $(this).parent().siblings(".email").text();
            console.log(user_id);
            console.log(user_name);
            console.log(user_email);

            $('.modal-body #modal_user_edit').attr('value', user_id);
            $('.modal-body #modal_name_edit').attr('value', user_name);
            $('.modal-body #modal_email_edit').attr('value', user_email);
        });

        // CODE FOR DELETE USERS
        $(document).on('click', '.open-DeleteAdminUserDialog', function() {
            document.getElementById('adminUserDeleteForm').reset();
            var user_id = $(this).parent().siblings(":first").text();
            console.log(user_id);

            $('.modal-body #modal_adminUserId_delete').attr('value', user_id);
        });

        // CODE FOR DISPLAY TABLES
        var adminTable = document.getElementById('adminTable');
        var staffTable = document.getElementById('staffTable');
        var studentTable = document.getElementById('studentTable');

        var adminButton = document.getElementById('adminButton');
        var staffButton = document.getElementById('staffButton');
        var studentButton = document.getElementById('studentButton');

        adminButton.onclick = function() {
            document.getElementById('staffButton').checked = false;
            document.getElementById('studentButton').checked = false;

            adminTable.setAttribute('class', 'col-sm-9 visible');
            staffTable.setAttribute('class', 'col-sm-9 hidden');
            studentTable.setAttribute('class', 'col-sm-9 hidden');
        };

        staffButton.onclick = function() {
            document.getElementById('adminButton').checked = false;
            document.getElementById('studentButton').checked = false;

            adminTable.setAttribute('class', 'col-sm-9 hidden');
            staffTable.setAttribute('class', 'col-sm-9 visible');
            studentTable.setAttribute('class', 'col-sm-9 hidden');
        };

        studentButton.onclick = function() {
            document.getElementById('adminButton').checked = false;
            document.getElementById('staffButton').checked = false;

            adminTable.setAttribute('class', 'col-sm-9 hidden');
            staffTable.setAttribute('class', 'col-sm-9 hidden');
            studentTable.setAttribute('class', 'col-sm-9 visible');
        };       
    </script>

@endsection
