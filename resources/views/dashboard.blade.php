@extends('layouts.default')

	@section('content')
		@if($count_office_shift)
		<div class="row">
			<div class="col-md-4">
				<div class="box-info full">
					<h2><strong>{{ trans('messages.attendance') }}</strong> </h2>
					@if(isset($my_shift))
					<div class="help-block" style="padding:10px;">{!! trans('messages.my').' '.trans('messages.office_shift') !!} : <strong>{!! showTime($my_shift->in_time).' to '.showTime($my_shift->out_time) !!}</strong></div>
					@endif

					<div class="table-responsive">
						<table class="table table-hover table-striped table-ajax-load" id="clock-table" data-source="/my-clock/lists">
							<thead>
								<tr>
									<th>{!! trans('messages.clock_in') !!}</th>
									<th>{!! trans('messages.clock_out') !!}</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>

					<div style="padding:10px;" id="clock-button">
						@if($clock_status == 'clock_in')
							{!! Form::open(['route' => 'clock.in','role' => 'form', 'class'=>'form-inline','id' => 'clock-in-form','data-table-alter' => 'clock-table','data-clock-button' => 1]) !!}
								<button type="submit" class="btn btn-success">{!! trans('messages.clock_in') !!}</button>
							{!! Form::close() !!}
						@elseif($clock_status == 'clock_out')
							<button class="btn btn-success btn-md"><i class="fa fa-arrow-circle-right"></i> {!! trans('messages.you_are_clock_in') !!}.</button>
							{!! Form::open(['route' => 'clock.out','role' => 'form', 'class'=>'form-inline','id' => 'clock-out-form','data-table-alter' => 'clock-table','data-clock-button' => 1]) !!}
								<button type="submit" class="btn btn-danger">{!! trans('messages.clock_out') !!}</button>
							{!! Form::close() !!}
						@endif
					</div>
					<div class="clear"></div>
					<br />
					@if(Entrust::can('upload_attendance'))
						<div style="padding:10px;">
						{!! Form::open(['files' => 'true','route' => 'clock.upload-attendance','role' => 'form', 'class'=>'form-inline upload-attendance-form','id' => 'upload-attendance-form', 'data-submit' => 'noAjax']) !!}
						  <div class="form-group">
							<label class="sr-only" for="file">{!! trans('messages.upload_file') !!}</label>
							<input type="file" name="file" id="file" class="btn btn-info" title="{!! trans('messages.select').' '.trans('messages.file') !!}">
						  </div>
						  {!! Form::submit(trans('messages.upload'),['class' => 'btn btn-primary']) !!}
						  <div class="help-block"><strong>{!! trans('messages.note') !!}</strong> {!! trans('messages.only_csv_file_allowed') !!} <br /><a href="/attendance-sample-file" data-toggle="modal" data-target="#myModal">Click here for Sample</a></div>
						  </div>
						{!! Form::close() !!}
					@endif
				</div>
			</div>
			<div class="col-md-4">
				<div class="box-info full">
					<h2><strong>{{ trans('messages.attendance') }}</strong> {{ trans('messages.statistics') }}</h2>
					<div style="padding:0 20px 20px 20px;">
						{!! Form::open(['route' => 'clock.list-date-wise-attendance','role' => 'form','class'=>'form-inline','id' => 'date_wise_attendance','data-no-form-clear' => 1,'data-table-alter' => 'attendance-statistics']) !!}
									<div class="form-group">
										{!! Form::input('text','from_date',isset($from_date) ? $from_date : date('Y-m-d'),['class'=>'form-control datepicker','placeholder'=>trans('messages.from_date'),'readonly' => 'true','style' => 'width:120px;'])!!}
									</div>
									<div class="form-group">
										{!! Form::input('text','to_date',isset($to_date) ? $to_date : date('Y-m-d'),['class'=>'form-control datepicker','placeholder'=>trans('messages.to_date'),'readonly' => 'true','style' => 'width:120px;'])!!}
									</div>
									{!! Form::submit(trans('messages.get'),['class' => 'btn btn-primary']) !!}
						{!! Form::close() !!}
					</div>
					<div class="table-responsive">
						<table class="table table-hover table-striped show-table" id="attendance-statistics">
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="box-info">
					<h2><strong>{{ trans('messages.leave') }}</strong> {{ trans('messages.statistics') }}</h2>
					<div id="leave-statistics">
					</div>
				</div>
			</div>
		</div>
		@endif

		@if(defaultRole())
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info">
					<h2><strong>{!! trans('messages.employee') !!}</strong> {!! trans('messages.statistics') !!}</h2>
					<div id="website-statistic" class="statistic-chart collapse in">
						<div id="daily-employee-attendance"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<a href="/location"><div class="col-sm-3 col-xs-6">
				<div class="box-info">
					<div class="icon-box">
						<span class="fa-stack">
						  <i class="fa fa-circle fa-stack-2x danger"></i>
						  <i class="fa fa-tasks fa-stack-1x fa-inverse"></i>
						  <!-- <strong class="fa-stack-1x icon-stack">R</strong> -->
						</span>
					</div>
					<div class="text-box">
						<h3>{!! \App\Location::count() !!}</h3>
						<p>{!! trans('messages.location') !!}</p>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			</a>
			<a href="/department"><div class="col-sm-3 col-xs-6">
				<div class="box-info">
					<div class="icon-box">
						<span class="fa-stack">
						  <i class="fa fa-circle fa-stack-2x info"></i>
						  <i class="fa fa-sitemap fa-stack-1x fa-inverse"></i>
						</span>
					</div>
					<div class="text-box">
						<h3>{!! $dept_count !!}</h3>
						<p>{!! trans('messages.department') !!}</p>
					</div>
					<div class="clear"></div>
				</div>
			</div></a>
			<a href="/employee"><div class="col-sm-3 col-xs-6">
				<div class="box-info">
					<div class="icon-box">
						<span class="fa-stack">
						  <i class="fa fa-circle fa-stack-2x warning"></i>
						  <i class="fa fa-users fa-stack-1x fa-inverse"></i>
						</span>
					</div>
					<div class="text-box">
						<h3>{!! $user_count !!}</h3>
						<p>{!! trans('messages.total_employee') !!}</p>
					</div>
					<div class="clear"></div>
				</div>
			</div></a>
			<a href="/attendance"><div class="col-sm-3 col-xs-6">
				<div class="box-info">
					<div class="icon-box">
						<span class="fa-stack">
						  <i class="fa fa-circle fa-stack-2x success"></i>
						  <i class="fa fa-user fa-stack-1x fa-inverse"></i>
						</span>
					</div>
					<div class="text-box">
						<h3>{!! $leave_count !!}</h3>
						<p>{!! trans('messages.leave') !!}</p>
					</div>
					<div class="clear"></div>
				</div>
			</div></a>
		</div>
		@endif

		<div class="row">
			<div class="col-sm-6">
				<div class="box-info">
					<h2><strong>{!! trans('messages.announcement') !!}</strong> </h2>
					<div class="notice-widget" >
					@if(count($announcements))
						@foreach($announcements as $announcement)
							<a href="/announcement/{{$announcement->id}}">
							<div class="the-notes info">
								<h4>{!! $announcement->title !!}</h4>
								<span style="color:green;"><i class="fa fa-clock-o"></i> {!! showDateTime($announcement->created_at) !!}</span>
								<p class="time pull-right" style="text-align:right;">{!! trans('messages.by').' '.$announcement->User->full_name.'<br />'.$announcement->User->Designation->full_designation !!}</p>
							</div>
							</a>
						@endforeach
					@else
						@include('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
					@endif
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box-info">
					<h2><strong>{!! trans('messages.company_hierarchy') !!}</strong></h2>
					<div class="notice-widget" >
						<p class="alert alert-info"><strong>{!! Auth::user()->full_name.', '.trans('messages.no_of_employee_under_you').' : '.$child_staff_count !!}
						</strong></p>
						<h4><strong>{!! trans('messages.you').' : '.Auth::user()->Designation->full_designation !!}
						</strong></h4>
			   			{!! App\Classes\Helper::createLineTreeView($tree,Auth::user()->designation_id) !!}
		   			</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-8">
				<div class="box-info">
					<div id="draw_calendar"></div>
				</div>

			</div>
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{{ trans('messages.celebration') }}</strong></h2>
					<div class="scroll-widget">
						<ul class="media-list">
						@foreach($celebrations as $celebration)
						  <li class="media">
							<a class="pull-left" href="#">
							  {!! \App\Classes\Helper::getAvatar($celebration['id']) !!}
							</a>
							<div class="media-body">
							  <h4 class="media-heading"><i class="fa fa-{{ $celebration['icon'] }} icon" style="margin-right:10px;"></i> <small><strong>{{ $celebration['title'] }}</strong></small> <a href="#">{!! $celebration['name'] !!}</a> </h4>
							  <p><strong>{!! $celebration['number']. ' </strong><br /><small>' .$celebration['designation'] !!}</small>
							  </p>
							</div>
						  </li>
						@endforeach
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{!! trans('messages.recent') !!}</strong> {!! trans('messages.activity') !!}</h2>
					<div class="scroll-widget" id="recent-activity">
						
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{!! trans('messages.quick') !!}</strong> {!! trans('messages.message') !!}</h2>
					<div class="additional-btn">
						  <a class="additional-icon" id="dropdownMenu5" data-toggle="dropdown">
							<i class="fa fa-cog"></i>
						  </a>
						  <ul class="dropdown-menu pull-right animated half fadeInDown" role="menu" aria-labelledby="dropdownMenu5">
							<li role="presentation"><a role="menuitem" tabindex="-1" href="/message/compose">{!! trans('messages.compose') !!}</a></li>
							<li role="presentation"><a role="menuitem" tabindex="-1" href="/message">{!! trans('messages.go_to_inbox') !!}</a></li>
							<li role="presentation"><a role="menuitem" tabindex="-1" href="/message/sent">{!! trans('messages.go_to_sent_folder') !!}</a></li>
						  </ul>
					</div>
					
					<div id="quick-post" class="collapse in">
						{!! Form::open(['route' => 'message.store','role' => 'form', 'class'=>'quick-message-form','id' => 'quick-message-form']) !!}
							<div class="form-group">
								{!! Form::select('to_user_id', [null=>trans('messages.select_one')] + $compose_users, '',['class'=>'form-control input-xlarge select2me','placeholder'=> trans('messages.select_one')])!!}
							</div>
							<div class="form-group">
								{!! Form::input('text','subject','',['class'=>'form-control','placeholder'=> trans('messages.subject')])!!}
							</div>
							<div class="form-group">
								{!! Form::textarea('body','',['class' => 'form-control summernote', 'placeholder' => trans('messages.body')])!!}
							</div>
							<div class="row">
								<div class="col-md-6">
									<button type="submit" class="btn btn-sm btn-success">{!! trans('messages.send') !!}</button>
								</div>
							</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{!! trans('messages.recent') !!}</strong> {!! trans('messages.task') !!}</h2>
					@foreach($tasks as $task)
					<p><strong>{!! $task->title.' ('.$task->progress.'%)' !!}</strong></p>
					<div class="progress">
					  <div class="progress-bar progress-bar-{!! App\Classes\Helper::activityTaskProgressColor($task->progress) !!}" role="progressbar" aria-valuenow="{!! $task->progress!!}" aria-valuemin="0" aria-valuemax="100" style="width: {!! $task->progress!!}%;">
					  </div>
					</div>
					@endforeach
					@if($task_count > 5)
					<div class="box-footer">
						<p><a href="{!! URL::to('/task') !!}" class="btn btn-primary btn-block btn-sm"><i class="fa fa-arrow-circle-right"></i> {!! trans('messages.see_all') !!}</a></p>
					</div>
					@endif
				</div>
			</div>
		</div>
		
	@stop