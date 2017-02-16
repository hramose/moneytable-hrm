@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.update_attendance') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{!! trans('messages.update_attendance') !!}</strong></h2>
					
					{!! Form::open(['route' => 'clock.update-attendance','role' => 'form','class'=>'update-attendance-form','id'=>'update-attendance-form','data-submit' => 'noAjax']) !!}
					  <div class="form-group">
					    {!! Form::label('user_id',trans('messages.employee'),['class' => 'control-label'])!!}
					    {!! Form::select('user_id', [null => trans('messages.select_one')] + $users, $user->id,['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
					  </div>
					  <div class="form-group">
					    {!! Form::label('date',trans('messages.date'),[])!!}
						{!! Form::input('text','date',$date,['class'=>'form-control datepicker','placeholder'=>trans('messages.date'),'readonly' => 'true'])!!}
					  </div>
					  {!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.get'),['class' => 'btn btn-primary']) !!}
					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-sm-8">
				<div class="box-info">
					<h2><strong>{!! trans('messages.update_attendance') !!}</strong></h2>
					<h4>{!! $user->full_name_with_designation !!}</h4>
					<p><strong>{!! showDate($date).' '.$label !!}</strong></p>
					<p><strong>Office Shift: {!! showDateTime($my_shift->in_time) !!} to {!! showDateTime($my_shift->out_time) !!}</strong></p>
					
					<div class="row">
						{!! Form::model($user,['method' => 'POST','route' => ['clock.clock-update',$user->id,$date] ,'class' => 'clock-form','id' => 'clock-form','data-table-alter' => 'clock-list-table','data-submit' => 'noAjax']) !!}
							<div class="col-md-4">{!! Form::input('text','clock_in','',['class'=>'form-control datetimepicker','placeholder'=>trans('messages.clock_in'),'readonly' => true])!!}</div>
							<div class="col-md-4">{!! Form::input('text','clock_out','',['class'=>'form-control datetimepicker','placeholder'=>trans('messages.clock_out'),'readonly' => true])!!}</div>
							<div class="col-md-4"><button type="submit" class="btn btn-success">{!! trans('messages.add_new') !!}</button></div>
						{!! Form::close() !!}
					</div>

					<div class="table-responsive">
						<table class="table table-hover table-striped" id="clock-list-table">
							<thead>
								<tr>
									<th>{!! trans('messages.in_time') !!}</th>
									<th>{!! trans('messages.out_time') !!}</th>
									<th>{!! trans('messages.option') !!}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($clocks as $clock)
								<tr>
									<td>{!! showDateTime($clock->clock_in) !!}</td>
									<td>{!! showDateTime($clock->clock_out) !!}</td>
									<td>
										<div class="btn-group btn-group-xs">
									  		<a href="#" data-href="/clock/{{$clock->id}}/edit" class='btn btn-xs btn-default' data-toggle="modal" data-target="#myModal"> <i class='fa fa-edit' data-toggle="tooltip" title="{!! trans('messages.edit') !!}"></i> </a>
									  		{!! delete_form(['clock.destroy',$clock->id]) !!}
									  	</div>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

	@stop