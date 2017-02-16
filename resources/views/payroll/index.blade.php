@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.payroll') !!}</li>
		</ul>
	@stop
	
	@section('content')

		<div class="row">
			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.payroll') !!}
					<div class="additional-btn">
					@if(Entrust::can('generate_multiple_payroll'))
						<a href="/payroll/create/multiple"><button class="btn btn-sm btn-primary"><i class="fa fa-users icon"></i> {!! trans('messages.generate_multiple_payroll') !!}</button></a>
					@endif
					@if(Entrust::can('generate_payroll'))
						<a href="/payroll/create"><button class="btn btn-sm btn-primary"><i class="fa fa-user icon"></i> {!! trans('messages.generate_new_payroll') !!}</button></a>
					@endif
					</div>
					</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>

	@stop