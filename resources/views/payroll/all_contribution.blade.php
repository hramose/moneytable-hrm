@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/payroll">{!! trans('messages.payroll') !!}</a></li>
		    <li class="active">{!! trans('messages.contribution') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.contribution') !!}
					<div class="additional-btn">
						<a href="/payroll/create"><button class="btn btn-sm btn-primary"><i class="fa fa-plus icon"></i> {!! trans('messages.generate_new_payroll') !!}</button></a>
					</div>
					</h2>
					{!! Form::open(['route' => 'payroll.all-contribution','role' => 'form','class'=>'form-inline']) !!}
					  
					  <div class="form-group">
					    <div class="row">
							<div class="col-xs-6">
								{!! Form::select('month', [''=>''] + $months, isset($month) ? $month : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
							</div>
							<div class="col-xs-6">
								{!! Form::select('year', [''=>''] + App\Classes\Helper::getYears(), isset($year) ? $year : date('Y'),['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
							</div>
						</div>
					  </div>
					  <div class="form-group">
							{!! Form::select('user_id', [null => trans('messages.select_one')] + $users, isset($user_id) ? $user_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
					  </div>
					  {!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.get'),['class' => 'btn btn-primary']) !!}
					{!! Form::close() !!}
					<br /><br />
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>

	@stop