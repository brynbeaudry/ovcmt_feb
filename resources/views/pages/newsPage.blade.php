@extends('layouts.app')
@section('content')

    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-2 sidenav" >
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
			     @if (session()->has('message'))
                    <p class="alert {{ Session::get('alert-class', 'alert-success') }}">{{ session()->get('message') }}</p>
                @endif
                @if (session()->has('error'))
                    <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ session()->get('error') }}</p>
                @endif
				@if(Auth::user()->usertype == 'admin')
					<form class="col-sm-10" role="form" method="POST" enctype="multipart/form-data" action="{{ url('/newsPage') }}">
                        {{ csrf_field() }}
                        
						<!-- Title Field -->
						<div class="form-group{{ $errors->has('news_title') ? ' has-error' : '' }}">
                            <div class="col-sm-4">
                                <label for="news_title">Title</label>
                            </div>
                            <div class="col-sm-6">
                                <input id="news_title" type="text" class="form-control" name="news_title" value="{{ old('news_title') }}" required autofocus>
                            </div>
                        </div>
                        <br>
						
						<!-- Link field -->
                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                            <br>
                            <div class="col-sm-4">
                                <label for="news_link" >Website</label>
                            </div>
                            <div class="col-sm-6">
                                <input id="news_link" type="news_link" class="form-control" name="news_link" value="{{ old('news_link') }}">
                            </div>
                        </div>
                        <br>
						
						<!-- Date field -->
                        <div class="form-group{{ $errors->has('news_publish_date') ? ' has-error' : '' }}">
                            <br>
                            <div class="col-sm-4">
                                <label for="news_publish_date" >Publish Date</label>
                            </div>
                            <div class="col-sm-6">
                                <input id="news_publish_date" type="date" class="form-control" name="news_publish_date" value="{{ old('news_publish_date') }}" required>
                            </div>
                        </div>
                        <br>
						
						<!-- Author field -->
                        <div class="form-group{{ $errors->has('news_author') ? ' has-error' : '' }}">
                            <br>
                            <div class="col-sm-4">
                                <label for="news_author">Author</label>
                            </div>
                            <div class="col-sm-6">
                                <input id="news_author" type="news_author"  class="form-control" name="news_author" value="{{ old('news_author') }}" required>
                            </div>
                        </div>
                        <br>
                        
						<!-- Content field -->
						<div class="form-group{{ $errors->has('news_full_content') ? ' has-error' : '' }}">
                            <br>
                            <div class="col-sm-4">
                                <label for="news_full_content" >Content</label>
                            </div>
                            <div class="col-sm-6">
                            <textarea style="white-space: pre-line;" id="news_full_content" type="news_full_content"  class="form-control" name="news_full_content" rows="10" cols="50" required></textarea>
                            </div>
                        </div>
                        <br>
						
						<!-- File Image Upload -->
						<div>
                            <br>
                            <div class="col-sm-4">
                                <label for="news_image" >Upload Image</label>
                            </div>
                            <div class="col-sm-6">
                            <input id="news_image" type="file"  class="form-control" name="news_image">
                            </div>
                        </div>
                        <br>
						<div class="col-sm-10">
                        @if(Auth::user()->usertype == 'admin')
							<button type="submit" class="btn btn-primary pull-left">Create News</button><hr>
						@endif
						</div>
                    </form>
				@endif
				
			<div class="col-sm-10">
				<!-- Create News Button. Only admins can view -->
				
				<!-- Create News & Button for admins only END -->
				
				<!-- get all the column list value -->
				@foreach($getalldata as $getalldata)
                    <table class="table table-striped table-bordered table-hover text-center ">
						<tbody>
							<tr class="info">
                                @if(Auth::user()->usertype == 'admin')
                                    <button class=" btn btn-primary open-editNewsDialog"
                                    data-toggle="modal" data-target="#editNewsModal" value="{{$getalldata->news_id}}" id="{{$getalldata->news_title}}">Edit</button>&nbsp
                                    <button class=" btn btn-danger open-DeleteNewsDialog"
                                    data-toggle="modal" data-target="#deleteNewsModal" value="{{$getalldata->news_id}}" id="{{$getalldata->news_title}}">Delete</button><br>
                                @endif
                                <h2 class="news_id" value="{{$getalldata->news_id}}">
                                    <a href="{{$getalldata->news_link}}" class="news_title" value="{{$getalldata->news_title}}">{{$getalldata->news_title}}</a>
                                    <br>
                                    <p class="news_link" value="{{$getalldata->news_link}}" hidden>{{$getalldata->news_link}}</p>
                                </h2>
                                <div style='display:inline-block;'>
                                    <p class="news_publish_date" value="{{$getalldata->news_publish_date}}">{{$getalldata->news_publish_date}}</p>
                                </div>
                                <div style='display:inline-block;'>
                                    <p>&nbspby&nbsp</p>
                                </div>
                                <div style='display:inline-block;'>
                                    <p class="news_author" value="{{$getalldata->news_author}}">{{$getalldata->news_author}}</p>
                                </div>
								<td class="text-left name" data-name="{{$getalldata->news_title}}">
									@if(empty($getalldata->news_image))
										<img style="margin-right: 10px;" src="/images/ovcmt_black_logo.png" align="left" width="250" height="150">
									@else
										<img style="margin-right: 10px;" src="/images/{{$getalldata->news_image}}" align="left" width="250" height="150">
									@endif
									<p class="news_full_content" value="{{$getalldata->news_full_content}}" style="white-space: pre-line;">{{$getalldata->news_full_content}}</p>
                                    <br>
								</td>
							</tr>
						</tbody>
					</table>
                @endforeach
			</div>
			
			<!-- Edit Button Modal -->
			<div class="modal fade" id="editNewsModal" tabindex="-1" role="dialog" aria-labeleledby="editNewsModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
								<!-- Edit Modal Title -->
                            <h4 class="modal-title" id="editNewsModalLabel">Edit News</h4>
                        </div>

                        {!! Form::open(['url' => '/editNews', 'id' => 'editNewsForm', 'files' => true]) !!}
                        <div class="modal-body">
                            <div class="form-group">
                                <table class="table table-bordered table-condensed">
                                    {!! Form::hidden('modal_news_id_edit', '', ['id'=>'modal_news_id_edit']) !!}
                                    {!! Form::hidden('modal_news_title_original', '', ['id'=>'modal_news_title_original']) !!}
                                    {!! Form::label('modal_news_titleLabel_edit', 'Title: ', ['class'=>'control-label']) !!}
                                    
									<br>
									{!! Form::text('modal_news_title_edit', '', array('id'=>'modal_news_title_edit'))!!}
                                    <br><br>
									
                                    {!! Form::label('modal_news_linkLabel_edit', 'Website: ', ['class'=>'control-label']) !!}
                                    <br>
                                    {!! Form::text('modal_news_link_edit', '', array('id'=>'modal_news_link_edit'))!!}
                                    <br><br>
									
									{!! Form::label('modal_news_publish_dateLabel_edit', 'Date: ', ['class'=>'control-label']) !!}
                                    <br>
                                    {!! Form::text('modal_news_publish_date_edit', '', array('id'=>'modal_news_publish_date_edit'))!!}
                                    <br><br>
									
									{!! Form::label('modal_news_authorLabel_edit', 'Author: ', ['class'=>'control-label']) !!}
                                    <br>
                                    {!! Form::text('modal_news_author_edit', '', array('id'=>'modal_news_author_edit'))!!}
                                    <br><br>
									
									{!! Form::label('modal_news_full_contentLabel_edit', 'Content: ', ['class'=>'control-label']) !!}
                                    <br>
                                    {!! Form::textarea('modal_news_full_content_edit', '', array('id'=>'modal_news_full_content_edit'))!!}
                                    <br><br>
									
									{!! Form::label('modal_news_imageLabel_edit', 'Image:', ['class'=>'control-label']) !!}
                                    <br>
									{!! Form::file('modal_news_image_edit', array('id'=>'modal_news_image_edit'))!!}
                                    <br><br>
									
									
                                    {!! Form::submit('Save',['class'=> 'btn btn-info','id' => 'editNewsButton']) !!}
                                </table>
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="closeDeleteBtn" class="btn btn-warning" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
			<!-- Edit Button Modal -->
			
			<!-- Delete Button Modal -->
            <div class="modal fade" id="deleteNewsModal" tabindex="-1" role="dialog" aria-labeleledby="deleteNewsModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="deleteNewsModalLabel">Delete News</h4>
                        </div>
						
                        {!! Form::open(['url' => '/deleteNews', 'id' => 'deleteNewsForm']) !!}
                        <div class="modal-body">
                            <div class="form-group">
                                <table class="table table-bordered table-condensed">
                                    {!! Form::hidden('modal_news_id_delete', '', ['id'=>'modal_news_id_delete']) !!}
                                    {!! Form::hidden('modal_news_title_delete', '', ['id'=>'modal_news_title_delete']) !!}
                                    {!! Form::submit('Confirm',['class'=> 'btn btn-info',
                                                     'id' => 'deleteNewsBtn']) !!}
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
			
		</div><!-- row content end -->
	</div><!-- container-fluid end -->	
	<script>
        //  // CODE FOR EDIT NEWS
        $(document).on('click', ".open-editNewsDialog", function() {
            document.getElementById('editNewsForm').reset();
            var id = $(this).attr('value');
            var title = $(this).attr('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/getNewsData',
                data: {"news_id": id, "news_title": title},
                dataType: 'json',
                success: function (data) {
                    $('.modal-body #modal_news_id_edit').attr('value', id);
                    $('.modal-body #modal_news_title_edit').attr('value', title);
                    $('.modal-body #modal_news_title_original').attr('value', title)
                    $('.modal-body #modal_news_link_edit').attr('value', data["newsdata"][0]["news_link"]);
                    $('.modal-body #modal_news_publish_date_edit').attr('value', data["newsdata"][0]["news_publish_date"]);
                    $('.modal-body #modal_news_author_edit').attr('value', data["newsdata"][0]["news_author"]);
                    $('.modal-body #modal_news_full_content_edit').val(data["newsdata"][0]["news_full_content"]);
					$('.modal-body #modal_news_image_edit').val(data["newsdata"][0]["news_image"]);
                }
            });
        });
		
		// CODE FOR DELETE NEWS
        $(document).on('click', '.open-DeleteNewsDialog', function() {
            document.getElementById('deleteNewsForm').reset();
            var id = $(this).attr('value');
            var title = $(this).attr('id');
            console.log(id);
            console.log(title);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/getNewsData',
                data: {"news_id": id, "news_title": title},
                dataType: 'json',
                success: function (data) {
                    $('.modal-body #modal_news_id_delete').attr('value', id);
                    $('.modal-body #modal_news_title_delete').attr('value', title);
                }
            });
        });
	</script>
@endsection