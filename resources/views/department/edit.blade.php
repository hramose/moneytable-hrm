
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.department') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($department,['method' => 'PATCH','route' => ['department.update',$department] ,'class' => 'department-form','id' => 'department-form-edit','data-form-table' => 'department_table']) !!}
			@include('department._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>