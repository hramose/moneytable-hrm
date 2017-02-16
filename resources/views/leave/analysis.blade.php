@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/leave">{!! trans('messages.leave') !!}</a></li>
		    <li class="active">{!! trans('messages.leave') !!} Analysis</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-md-12">
				<div class="box-info">
					<h2><strong>{!! trans('messages.filter') !!}</strong></h2>
					{!! Form::open(['route' => 'leave.analysis','role' => 'form','class'=>'','id' => 'leave_analysis_form','data-submit' => 'noAjax']) !!}
					<div class="row">
						<div class="col-md-4">
						  	<div class="form-group">
								<label for="to_date">{!! trans('messages.location') !!}</label>
								{!! Form::select('location_id', [null => trans('messages.select_one')] + $locations,(isset($request) && $request->has('location_id')) ? $request->input('location_id') : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						  	</div>
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-default btn-success">{!! trans('messages.get') !!}</button>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
		@if(isset($leave_types) && count($leave_graph))
			<div class="row" id="leave-graph">
				@foreach($leave_types as $leave_type)
					<div class="col-md-12">
						<div class="box-info">
							<div id="{{\App\Classes\Helper::createSlug($leave_type)}}-graph"></div>
						</div>
					</div>
				@endforeach
			</div>
		@elseif(isset($leave_types))
			<div class="row">
				<div class="col-md-12">
					<div class="box-info">
						@include('common.notification',['message' => 'No employee found.','type' => 'danger'])
					</div>
				</div>
			</div>
		@endif
	@stop