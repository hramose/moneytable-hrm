@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.backup').' '.trans('messages.log') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.backup').' '.trans('messages.log') !!}
						<div class="additional-btn">
							{!! Form::open(['route' => 'backup.store','role' => 'form', 'class'=>'backup-form','id' => 'backup-form','data-form-table' => 'backup_table']) !!}
								{!! Form::checkbox('delete_old_backup', '1', '').' <span style="font-size:12px;"> '.trans('messages.delete_old_backup') !!}</span>
								{!! Form::submit(trans('messages.create').' '.trans('messages.backup'),['class' => 'btn btn-primary btn-sm']) !!}
							{!! Form::close() !!}
						</div>
					</h2>
					@include('common.datatable',['col_heads' => $col_heads])
				</div>
			</div>
		</div>

	@stop