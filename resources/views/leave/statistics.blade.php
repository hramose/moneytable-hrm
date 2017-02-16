@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.leave').' '.trans('messages.statistics') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.leave').' '.trans('messages.statistics') !!}
					<div class="additional-btn">
						<a href="/leave"><button class="btn btn-sm btn-primary"><i class="fa fa-coffee icon"></i> {!! trans('messages.leave').' '.trans('messages.request') !!}</button></a>
					</div>
					</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>
	@stop