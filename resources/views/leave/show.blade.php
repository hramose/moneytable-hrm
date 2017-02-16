@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/leave">{!! trans('messages.leave') !!}</a></li>
		    <li class="active">{!! trans('messages.leave').' '.trans('messages.detail') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.leave').'</strong> '.trans('messages.detail') !!}</h2>
					<div class="table-responsive">
						<table class="table table-hover table-striped show-table">
							<tbody>
								<tr><th>{!! trans('messages.leave') !!} #</th><td>{!! str_pad($leave->id, 3, 0, STR_PAD_LEFT) !!}</td></tr>
								<tr><th>{!! trans('messages.employee') !!}</th><td>{!! $leave->User->full_name_with_designation !!}</td></tr>
								<tr><th>{!! trans('messages.leave_type') !!}</th><td>{!! $leave->LeaveType->name !!}</td></tr>
								<tr><th>{!! trans('messages.date_of_request') !!}</th><td>{!! showDate($leave->created_at) !!}</td></tr>
								<tr><th>{!! trans('messages.from_date') !!}</th><td>{!! showDate($leave->from_date) !!}</td></tr>
								<tr><th>{!! trans('messages.to_date') !!}</th><td>{!! showDate($leave->to_date) !!}</td></tr>
							</tbody>
						</table>
					</div>
					<br />
					<div class="the-notes info">{!! $leave->remarks !!}</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="box-info">
					@if(Entrust::can('update_leave_status') && ($leave->user_id != Auth::user()->id || defaultRole()))
					 {!! Form::model($leave,['method' => 'POST','route' => ['leave.update-status',$leave->id] ,'class' => 'leave-status-form','id' => 'leave-status-form','data-no-form-clear'=>1,'data-leave-statistics' => 1]) !!}
					<h2><strong>{!! trans('messages.update') !!}</strong> {!! trans('messages.status') !!}</h2>
					  <div class="form-group">
					    {!! Form::label('status',trans('messages.leave').' '.trans('messages.status'),[])!!}
						{!! Form::select('status', $status, $leave->status,['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one'),'id' => 'status'])!!}
					  </div>
					  <div class="form-group show-hide-approved-date">
					    {!! Form::label('approved_date',trans('messages.date'),[])!!}
						{!! Form::input('text','approved_date',isset($leave->approved_date) ? $leave->approved_date : '',['class'=>'form-control mdatepicker','placeholder'=>trans('messages.date'),'readonly' => 'true'])!!}
					  </div>
					  <div class="form-group">
					    {!! Form::label('admin_remarks',trans('messages.remarks'),[])!!}
					    {!! Form::textarea('admin_remarks',isset($leave->admin_remarks) ? $leave->admin_remarks : '',['size' => '30x3', 'class' => 'form-control', 'placeholder' => trans('messages.remarks'),"data-show-counter" => 1,"data-limit" => config('config.textarea_limit'),'data-autoresize' => 1])!!}
					    <span class="countdown"></span>
					  </div>
					  {!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary']) !!}
					{!! Form::close() !!}
					@else
					<h2><strong>{!! trans('messages.leave') !!}</strong> {!! trans('messages.status') !!}</h2>
						@if($leave->status == 'pending')
							<span class="label label-info btn-lg">{!! trans('messages.pending') !!}</span>
						@elseif($leave->status == 'approved')
							<span class="label label-success btn-lg">{!! trans('messages.approved') !!}</span>
						@else
							<span class="label label-danger btn-lg">{!! trans('messages.rejected') !!}</span>
						@endif
						<div class="the-success info">{!! $leave->admin_remarks !!}</div>
					@endif
				</div>
			</div>
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{{ trans('messages.leave') }}</strong> {{ trans('messages.statistics') }}</h2>
					<div id="leave-statistics" data-user-id="{{$leave->User->id}}">
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.other') !!}</strong> {!! trans('messages.leave').' "'.$leave->User->full_name_with_designation.'"' !!}</h2>

					<div class="table-responsive">
						<table class="table table-hover table-striped">
							<thead>
								<tr>
									<th>{!! trans('messages.from_date') !!}</th>
									<th>{!! trans('messages.to_date') !!}</th>
									<th>{!! trans('messages.leave_type') !!}</th>
									<th>{!! trans('messages.remarks') !!}</th>
									<th>{!! trans('messages.status') !!}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($other_leaves as $other_leave)
									<tr>
										<td>{!! showDate($other_leave->from_date) !!}</td>
										<td>{!! showDate($other_leave->to_date) !!}</td>
										<td>{!! $leave->LeaveType->name !!}</td>
										<td>{!! $other_leave->remarks !!}</td>
										<td>{!! trans('messages.'.$other_leave->status) !!}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	@stop