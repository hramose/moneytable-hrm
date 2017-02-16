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
					<h2><strong>{!! trans('messages.assigned_to') !!}</strong>
					</h2>
					  {!! Form::model($task,['method' => 'POST','route' => ['task.assign-task',$task->id] ,'class' => 'task-assign-form','id' => 'task-assign-form','data-div-alter' => 'task-assigned-user','data-no-form-clear' => 1]) !!}
					  	<div class="form-group">
						    {!! Form::label('user_id',trans('messages.employee'),[])!!}
						    {!! Form::select('user_id[]', [''=>'']+$users
						    	, $selected_user,['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one'),'multiple' => true])!!}
					    </div>
					    {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
					  {!! Form::close() !!}
				</div>
				@endif
			</div>
			<div class="col-sm-8">
				<div class="box-info full">
					<ul class="nav nav-tabs nav-justified">
					  <li class="active"><a href="#detail-tab" data-toggle="tab"><i class="fa fa-home"></i> {!! trans('messages.detail') !!}</a></li>
					  <li><a href="#sub-task-tab" data-toggle="tab"><i class="fa fa-tasks"></i> {!! trans('messages.sub_task') !!}</a></li>
					  <li><a href="#comment-tab" data-toggle="tab"><i class="fa fa-comment"></i> {!! trans('messages.comment') !!}</a></li>
					  <li><a href="#note-tab" data-toggle="tab"><i class="fa fa-pencil"></i> {!! trans('messages.note') !!}</a></li>
					  <li><a href="#attachment-tab" data-toggle="tab"><i class="fa fa-paperclip"></i> {!! trans('messages.attachment') !!}</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane animated active fadeInRight" id="detail-tab">
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
						<div class="tab-pane animated fadeInRight" id="sub-task-tab">
							<div class="user-profile-content">
								<div class="row">
									<div class="col-md-12">
										{!! Form::model($task,['method' => 'POST','route' => ['task.add-sub-task',$task->id] ,'class' => 'task-sub-task-form','id' => 'task-sub-task-form','data-table-alter' => 'user-shift-table']) !!}
											{!! Form::input('hidden','task_id',$task->id)!!}
											<div class="form-group">
												{!! Form::label('title',trans('messages.title'),[])!!}
												{!! Form::input('text','title','',['class'=>'form-control','placeholder'=>trans('messages.title')])!!}
											</div>
										  <div class="form-group">
										    {!! Form::textarea('description','',['size' => '30x3', 'class' => 'form-control ', 'placeholder' => trans('messages.description'),'data-autoresize' => 1,"data-show-counter" => 1,"data-limit" => config('config.textarea_limit')])!!}
										    <span class="countdown"></span>
										  </div>
										  <div class="form-group">
										  	{!! Form::submit(trans('messages.post'),['class' => 'btn btn-primary pull-right btn-sm']) !!}
										  </div>
										{!! Form::close() !!}
									</div>
								</div>
								<div class="row" style="margin-top: 15px;">
									<div class="col-md-12">
										<div class="table-responsive">
											<table data-sortable class="table table-hover table-striped table-bordered table-ajax-load"  id="user-shift-table" data-source="/sub-task/lists" data-extra="&task_id={{$task->id}}">
												<thead>
													<tr>
														<th>{!! trans('messages.title') !!}</th>
														<th>{!! trans('messages.description') !!}</th>
														<th>{!! trans('messages.date') !!}</th>
														<th data-sortable="false" >{!! trans('messages.option') !!}</th>
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
						<div class="tab-pane animated fadeInRight" id="comment-tab">
							<div class="user-profile-content">
								{!! Form::model($task,['method' => 'POST','route' => ['task-comment.store',$task->id] ,'class' => 'task-comment-form','id' => 'task-comment-form','data-list-alter' => 'task-comment-lists']) !!}
								  <div class="form-group">
								    {!! Form::textarea('comment','',['size' => '30x1', 'class' => 'form-control ', 'placeholder' => 'Enter Your '.trans('messages.comment'),'data-autoresize' => 1,'style' => 'border:0px;border-bottom:1px solid #cccccc;'])!!}
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
						<div class="tab-pane animated fadeInRight" id="note-tab">
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
						<div class="tab-pane animated fadeInRight" id="attachment-tab">
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

				@if(!config('config.sub_task_rating'))
					@if($task->user_id == Auth::user()->id)
						<div class="box-info collapse" id="box-detail">
							<h2><strong>{{trans('messages.rating')}}</strong>
								<div class="additional-btn">
									<button class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#box-detail"><i class="fa fa-minus icon"></i> {!! trans('messages.hide') !!}</button>
								</div></h2>
							</h2>
							  {!! Form::model($task,['method' => 'POST','route' => ['task.store-rating',$task->id] ,'class' => 'task-rating-form','id' => 'task-rating-form','data-submit' => 'noAjax']) !!}
							  	<div class="row">
							  		<div class="col-md-6">
									  	<div class="form-group">
										    {!! Form::label('user_id',trans('messages.employee'),[])!!}
										    {!! Form::select('user_id', [''=>'']+$rating_users
										    	, '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
									    </div>
							  		</div>
							  		<div class="col-md-6">
									    <div class="form-group">
										    {!! Form::label('rating',trans('messages.rating'),[])!!}
										    {!! Form::select('rating', [''=>'','1' => '1 Star','2' => '2 Star','3' => '3 Star','4' => '4 Star','5' => '5 Star']
										    	, '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
									    </div>
							  		</div>
							  	</div>
								<div class="form-group">
									{!! Form::label('comment',trans('messages.comment'),[])!!}
									{!! Form::textarea('comment','',['size' => '30x6', 'class' => 'form-control', 'placeholder' => trans('messages.comment'),"data-show-counter" => 1,"data-limit" => config('config.textarea_limit'),'data-autoresize' => 1,'size' => '30x3'])!!}
									<span class="countdown"></span>
								</div>
							    {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
							  {!! Form::close() !!}
						</div>
					@endif
					<div class="box-info full">
						<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.rating') !!}
						<div class="additional-btn">
							@if($task->user_id == Auth::user()->id)
								<a href="#" data-toggle="collapse" data-target="#box-detail"><button class="btn btn-sm btn-primary"><i class="fa fa-plus icon"></i> {!! trans('messages.add_new') !!}</button></a>
							@endif
						</div>
						</h2>

						<div class="table-responsive">
							<table data-sortable class="table table-hover table-striped table-bordered">
								<thead>
									<tr>
										@if($task->user_id == Auth::user()->id)
											<th data-sortable="false">{{ trans('messages.option') }}</th>
										@endif
										<th>{{ trans('messages.employee') }}</th>
										<th>{{ trans('messages.rating') }}</th>
										<th>{{ trans('messages.rating').' Star' }}</th>
										<th>{{ trans('messages.comment') }}</th>
										<th>{{ trans('messages.date') }}</th>
									</tr>
								</thead>
								<tbody>
									@foreach($task->User as $user)
										@if($user->pivot->rating)
										<tr>
											@if($task->user_id == Auth::user()->id)
												<td>
													<div class="btn-group btn-group-xs">
								                        <a href="/delete-task-rating/{{$user->id}}/{{$task->id}}" class="btn btn-xs btn-danger alert_delete"><i class="fa fa-trash" data-toggle="tooltip" title="{{trans('messages.delete')}}"></i></a>
								                    </div>
												</td>
											@endif
											<td>{{ $user->full_name_with_designation }}</td>
											<td>{!! \App\Classes\Helper::getRatingStar($user->pivot->rating,1) !!}
											<td>{!! \App\Classes\Helper::getRatingStar($user->pivot->rating) !!}</td>
											<td>{{ $user->pivot->comment }}</td>
											<td>{{ showDateTime($user->pivot->updated_at) }}</td>
										</tr>
										@endif
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				@elseif(config('config.sub_task_rating'))
					<div class="box-info full">
						<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.rating') !!}
						</h2>

						<div class="table-responsive">
							<table data-sortable class="table table-hover table-striped table-bordered">
								<thead>
									<tr>
										@if($task->user_id == Auth::user()->id)
											<th data-sortable="false">{{ trans('messages.option') }}</th>
										@endif
										<th>{{ trans('messages.employee') }}</th>
										<th>{{ trans('messages.rating') }}</th>
										<th>{{ trans('messages.rating').' Star' }}</th>
									</tr>
								</thead>
								<tbody>
									@foreach($task->User as $user)
										<tr>
											<td>
												<div class="btn-group btn-group-xs">
							                        <a href="#" data-href="/sub-task-rating/{{$user->id}}/{{$task->id}}/show" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"><i class="fa fa-arrow-circle-right" data-toggle="tooltip" title="{{trans('messages.view')}}"></i></a>
													@if($task->user_id == Auth::user()->id)
							                        	<a href="#" data-href="/sub-task-rating/{{$user->id}}/{{$task->id}}" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal"><i class="fa fa-star" data-toggle="tooltip" title="{{trans('messages.rating')}}"></i></a>
								                        <a href="/delete-task-rating/{{$user->id}}/{{$task->id}}" class="btn btn-xs btn-danger alert_delete"><i class="fa fa-trash" data-toggle="tooltip" title="{{trans('messages.delete')}}"></i></a>
													@endif
							                    </div>
											</td>
											<td>{{ $user->full_name_with_designation }}</td>
											<td>{!! \App\Classes\Helper::getSubTaskRating($task->id,$user->id,1) !!}</td>
											<td>{!! \App\Classes\Helper::getSubTaskRating($task->id,$user->id) !!}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				@endif
			</div>
		</div>
				
	@stop