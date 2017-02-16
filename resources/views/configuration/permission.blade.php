@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.permission') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info full">
					<h2><strong>{!! trans('messages.manage'). '</strong> '.trans('messages.permission') !!}</h2>
					{!! Form::open(['route' => 'configuration.save-permission','role' => 'form', 'class'=>'permission-form','id' => 'permission-form','data-no-form-clear' => 1]) !!}
					  <p class="alert alert-info"><strong>{!! trans('messages.subordinate_definition') !!}</strong></p>
					  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right','style' => 'margin:10px;']) !!}
					  	<br /><br />
					  <table class="table table-hover table-striped">
					  	<thead>
					  		<tr>
					  			<th>{!! trans('messages.permission') !!}</th>
					  			@foreach(\App\Role::all() as $role)
					  			<th>{!! \App\Classes\Helper::toWord($role->name) !!}</th>
					  			@endforeach
					  		</tr>
					  		</tr>
					  	</thead>
					  	<tbody>
					  		@foreach($permissions as $permission)
					  			@if($permission->category != $category)
					  			<tr style="background-color:#3498DB;color:#ffffff;"><td colspan="{!! count(\App\Role::all())+1 !!} "><strong>{!! \App\Classes\Helper::toWord($permission->category).' '.trans('messages.module') !!}</strong></td></tr>
					  			<?php $category = $permission->category; ?>
					  			@endif
					  			<tr>
					  				<td>{!! \App\Classes\Helper::toWord($permission->name) !!}</td>
						  			@foreach(\App\Role::all() as $role)
						  			<th><input type="checkbox" name="permission[{!!$role->id!!}][{!!$permission->id!!}]" value = '1' {!! (in_array($role->id.'-'.$permission->id,$permission_role)) ? 'checked' : '' !!} @if($role->is_hidden) disabled @endif></th>
						  			@endforeach
					  			</tr>
					  		@endforeach
					  	</tbody>
					  </table>
					  <br /><br />
					  {!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right','style' => 'margin:10px;']) !!}
					{!! Form::close() !!}
					<div class="clear"></div>
				</div>
			</div>
		</div>
	@stop
