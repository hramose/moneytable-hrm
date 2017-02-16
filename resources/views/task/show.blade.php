@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/task">{!! trans('messages.task') !!}</a></li>
		    <li class="active">{!! $task->title !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.task').'</strong> '.trans('messages.detail') !!}</h2>
					<div class="table-responsive">
						<table class="table table-hover table-striped show-table">
							<tbody>
								<tr><th>{!! trans('messages.title') !!}</th><td>{!! $task->title !!}</td></tr>
								<tr><th>{!! trans('messages.created_by') !!}</th><td>{!! $task->userAdded->full_name_with_designation !!}</td></tr>
								<tr><th>{!! trans('messages.start_date') !!}</th><td>{!! showDate($task->start_date) !!}</td></tr>
								<tr><th>{!! trans('messages.start_date') !!}</th><td>{!! showDate($task->start_date) !!}</td></tr>
								<tr><th>{!! trans('messages.date_of_due') !!}</th><td>{!! showDate($task->due_date) !!}</td></tr>
								<tr><th>{!! trans('messages.hours') !!}</th><td>{!! isset($task->hours)? $task->hours.' '.trans('messages.hours') : trans('messages.na') !!}</td></tr>
							</tbody>
						</table>
					</div>
				</div>

				@if(Entrust::can('assign_task'))
				<div class="box-info">
					<h2><strong>{!! trans('messages.assigned_to') !!}</strong></h2>
					  {!! Form::model($task,['method' => 'POST','route' => ['task.assign-task',$task->id] ,'class' => 'task-assign-form','id' => 'task-assign-form','data-div-alter' => 'task-assigned-user','data-no-form-clear' => 1]) !!}
					  	<div class="form-group">
						    {!! Form::label('user_id',trans('messages.employee'),[])!!}
						    {!! Form::select('user_id[]', [''=>'']+$users
						    	, $selected_user,['class'=>'form-control input-xlarge select2me','multiple'=>true,'placeholder'=>trans('messages.select_one')])!!}
					    </div>
					    {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
					  {!! Form::close() !!}
				</div>
				@endif
			</div>
			<div class="col-sm-8">
				<div class="box-info full">
					<ul class="nav nav-tabs nav-justified">
					  <li class="active"><a href="#detail" data-toggle="tab"><i class="fa fa-home"></i> {!! trans('messages.detail') !!}</a></li>
					  <li><a href="#comment" data-toggle="tab"><i class="fa fa-comment"></i> {!! trans('messages.comment') !!}</a></li>
					  <li><a href="#note" data-toggle="tab"><i class="fa fa-pencil"></i> {!! trans('messages.note') !!}</a></li>
					  <li><a href="#attachment" data-toggle="tab"><i class="fa fa-paperclip"></i> {!! trans('messages.attachment') !!}</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane animated active fadeInRight" id="detail">
							<div class="user-profile-content">
								<div class="the-notes info">{!! $task->description !!}</div>
								<div class="col-md-6">
									<h2><strong>{!! trans('messages.assigned_to') !!}</strong></h2>
									<ul class="media-list" id="task-assigned-user">
									  @foreach($task->User as $user)
									  <li class="media">
										{!! \App\Classes\Helper::getAvatar($user->id) !!}
										<div class="media-body" style="vertical-align:middle; padding-left:10px;">
										  <h4 class="media-heading"><a href="#">{{ $user->full_name }}</a> <br /> <small>{{ $user->Designation->full_designation }}</small></h4>
										  @if($user->id == $task->user_id)
											<span class="label label-danger pull-right">Admin</span>
										  @endif
										</div>
									  </li>
									  @endforeach
									</ul>
								</div>
								<div class="col-md-6">
									<h2><strong>{!! trans('messages.update').' </strong> '.trans('messages.status') !!}</h2>
									<div class="progress" id="task-progress-bar">
									  <div class="progress-bar progress-bar-{!! App\Classes\Helper::activityTaskProgressColor($task->progress) !!}" role="progressbar" aria-valuenow="{{ $task->progress }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $task->progress }}%;">
									    {{ $task->progress }}%
									  </div>
									</div>

									@if(Entrust::can('update_task_progress'))
									  {!! Form::model($task,['method' => 'POST','route' => ['task.update-task-progress',$task->id] ,'class' => 'task-progress-form','id' => 'task-progress-form','data-div-alter' => 'task-progress-bar','data-no-form-clear' => 1]) !!}
									  	<div class="form-group">
										    {!! Form::label('progress','Progress',[])!!}
											<div class="input-group">
												{!! Form::input('number','progress',isset($task->progress) ? $task->progress : '',['class'=>'form-control','placeholder'=>'Enter Task Progress'])!!}
									    		<span class="input-group-btn">
									    			<button class="btn btn-default btn-primary" type="submit">{{ trans('messages.save') }}</button>
												</span>
									    	</div>
									    </div>
									  {!! Form::close() !!}
									@endif
								</div>
								<div class="clear"></div>
							</div>
						</div>
						<div class="tab-pane animated fadeInRight" id="comment">
							<div class="user-profile-content">
								{!! Form::model($task,['method' => 'POST','route' => ['task-comment.store',$task->id] ,'class' => 'task-comment-form','id' => 'task-comment-form','data-list-alter' => 'task-comment-lists']) !!}
								  <div class="form-group">
								    {!! Form::textarea('comment','',['size' => '30x3', 'class' => 'form-control ', 'placeholder' => trans('messages.comment'),'data-autoresize' => 1])!!}
								    <span class="countdown"></span>
								  </div>
								  {!! Form::submit(trans('messages.post'),['class' => 'btn btn-primary pull-right btn-sm']) !!}
								{!! Form::close() !!}
								<div class="clear"></div>

								<h2><strong>{!! trans('messages.comment') !!}</strong> {!! trans('messages.list') !!}</h2>
								<div class="scroll-widget" id="task-comment-lists">
									<ul class="media-list">
									@if(count($task->TaskComment))
										@foreach($task->TaskComment->sortByDesc('id') as $task_comment)
										  <li class="media">
											<a class="pull-left" href="#">
											  {!! App\Classes\Helper::getAvatar($task_comment->user_id) !!}
											</a>
											<div class="media-body">
											  <h4 class="media-heading"><a href="#">{!! $task_comment->User->full_name !!}</a> <small>{!! showDateTime($task_comment->created_at) !!}</small>
											  @if(Auth::user()->id == $task_comment->user_id)
												<div class="pull-right">{!! delete_form(['task-comment.destroy',$task_comment->id]) !!}</div>
											  </h4>
										      @endif
											  <div class="the-notes danger" style="margin-top:10px; background-color:#f1f1f1;">{!! $task_comment->comment !!}</div>
											</div>
										  </li>
										@endforeach
									@endif
									</ul>
								</div>
							</div>
						</div>
						<div class="tab-pane animated fadeInRight" id="note">
							<div class="user-profile-content">
								{!! Form::model($task,['method' => 'POST','route' => ['task-note.store',$task->id] ,'class' => 'task-note-form','id' => 'task-note-form','data-no-form-clear' => 1]) !!}
								   <div class="form-group">
								    {!! Form::textarea('note',(count($task->TaskNote) && $task->TaskNote->whereLoose('user_id',Auth::user()->id)) ? $task->TaskNote->whereLoose('user_id',Auth::user()->id)->first()->note : '',['size' => '30x10', 'class' => 'form-control notebook', 'placeholder' => trans('messages.note'),'data-autoresize' => 1])!!}
								    <span class="countdown"></span>
								   </div>
							 	{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
								{!! Form::close() !!}
								<div class="clear"></div>
							</div>
						</div>
						<div class="tab-pane animated fadeInRight" id="attachment">
							<div class="user-profile-content">
								<h2><strong>{!! trans('messages.attachment') !!}</strong></h2>
								{!! Form::model($task,['files'=>'true','method' => 'POST','route' => ['task-attachment.store',$task->id] ,'class' => 'task-attachment-form','id' => 'task-attachment-form','data-table-alter' => 'task-attachment-table']) !!}
								  <div class="form-group">
								    {!! Form::label('title',trans('messages.title'),[])!!}
									{!! Form::input('text','title','',['class'=>'form-control','placeholder'=>trans('messages.title')])!!}
								  </div>
								  <div class="form-group">
								  	<input type="file" name="attachments" id="attachments" class="btn btn-default" title="{!! trans('messages.select').' '.trans('messages.file') !!}">
								  </div>
								  <div class="form-group">
								    {!! Form::textarea('description','',['size' => '30x3', 'class' => 'form-control', 'placeholder' => trans('messages.description')])!!}
								    <span class="countdown"></span>
								  </div>
								  {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}	
								{!! Form::close() !!}
								<div class="clear"></div>
								<h2><strong>{!! trans('messages.attachment') !!}</strong> {!! trans('messages.list') !!}</h2>
								<div class="table-responsive">
									<table class="table table-hover table-striped table-bordered table-ajax-load"  id="task-attachment-table" data-source="/task-attachment/{{$task->id}}/lists">
										<thead>
											<tr>
												<th>{!! trans('messages.option') !!}</th>
												<th>{!! trans('messages.title') !!}</th>
												<th>{!! trans('messages.description') !!}</th>
												<th>{!! trans('messages.date_time') !!}</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
				
	@stop