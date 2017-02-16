@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/payroll">{!! trans('messages.payroll') !!}</a></li>
		    <li class="active">{!! trans('messages.generate') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong>{!! trans('messages.select') !!} </strong> {!! trans('messages.option') !!}</h2>
					{!! Form::open(['route' => 'payroll.create','role' => 'form', 'class'=>'payroll-form','id' => 'payroll-form','data-submit' => 'noAjax']) !!}
					  <div class="form-group">
					    {!! Form::label('from_date',trans('messages.from_date'),[])!!}
						{!! Form::input('text','from_date',isset($from_date) ? $from_date : '',['class'=>'form-control datepicker','placeholder'=>trans('messages.from_date'),'readonly' => 'true'])!!}
					  </div>
					  <div class="form-group">
					    {!! Form::label('to_date',trans('messages.to_date'),[])!!}
						{!! Form::input('text','to_date',isset($to_date) ? $to_date : '',['class'=>'form-control datepicker','placeholder'=>trans('messages.to_date'),'readonly' => 'true'])!!}
					  </div>
					  <div class="form-group">
					    {!! Form::label('user_id',trans('messages.employee'),['class' => 'control-label'])!!}
					    {!! Form::select('user_id', [''=>''] + $users, isset($user_id) ? $user_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
					  </div>
					  {!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.get'),['class' => 'btn btn-primary pull-right','name' => 'submit']) !!}
					{!! Form::close() !!}
				</div>
				@if(isset($att_summary))
				<div class="box-info full">
					<h2><strong>{!!trans('messages.attendance').' </strong>'.trans('messages.summary') !!}</h2>
					<div class="table-responsive">
						<table class="table table-hover table-striped show-table">
							<tbody>
								<tr>
									<th>{!! trans('messages.absent') !!}</th>
									<td>{!! $att_summary['A'] !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.holiday') !!}</th>
									<td>{!! $att_summary['H'] !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.present') !!}</th>
									<td>{!! $att_summary['P'] !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.leave') !!}</th>
									<td>{!! $att_summary['L'] !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.late') !!}</th>
									<td>{!! $att_summary['Late'] !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.overtime') !!}</th>
									<td>{!! $att_summary['Overtime'] !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.early').' '.trans('messages.leaving') !!}</th>
									<td>{!! $att_summary['Early'] !!}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				@endif
				@if(isset($summary))
				<div class="box-info full">
					<h2><strong>{!! trans('messages.hour').' </strong>'.trans('messages.summary') !!}</h2>
					<div class="table-responsive">
						<table class="table table-hover table-striped show-table">
							<tbody>
								<tr>
									<th>{!! trans('messages.total_late') !!}</th>
									<td>{!! array_key_exists('total_late',$summary) ? $summary['total_late'] : '-' !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.total_early') !!}</th>
									<td>{!! array_key_exists('total_early',$summary) ? $summary['total_early'] : '-' !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.total_rest') !!}</th>
									<td>{!! array_key_exists('total_rest',$summary) ? $summary['total_rest'] : '-' !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.total_overtime') !!}</th>
									<td>{!! array_key_exists('total_overtime',$summary) ? $summary['total_overtime'] : '-' !!}</td>
								</tr>
								<tr>
									<th>{!! trans('messages.total_work') !!}</th>
									<td>{!! array_key_exists('total_working',$summary) ? $summary['total_working'] : '-' !!}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				@endif
			</div>

			@if(isset($user))
			<div class="col-sm-8">
				<div class="box-info">
					<h2><strong>{{trans('messages.payroll')}}</strong></h2>
					{!! Form::open(['route' => 'payroll.store','role' => 'form', 'class'=>'payroll-store-form','id' => 'payroll-store-form','data-submit' => 'noAjax']) !!}
					{!! Form::hidden('user_id',$user_id)!!}
					{!! Form::hidden('from_date',$from_date)!!}
					{!! Form::hidden('to_date',$to_date)!!}
						@include('payroll._form')
					{!! Form::close() !!}	
				</div>
				<div class="box-info full">
					<h2><strong>{!! trans('messages.monthly') !!}</strong> {!! trans('messages.salary') !!} {!! $user->full_name_with_designation !!}</h2>
					@if(count($salaries))
					<div class="table-responsive">
						<table class="table table-hover table-striped">
							<thead>
								<tr>
									<th>{!! trans('messages.salary_head') !!}</th>
									<th>{!! trans('messages.type') !!}</th>
									<th>{!! trans('messages.amount') !!}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($salaries as $salary)
									<tr>
										<td>{!! $salary->SalaryType->head !!}</td>
										<td>{!! ucfirst($salary->SalaryType->salary_type) !!}</td>
										<td>{!! currency($salary->amount) !!}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					@else
						@include('common.notification',['message' => trans('messages.salary_not_set'),'type' => 'danger'])
					@endif
				</div>
			</div>
			@endif
		</div>
	@stop