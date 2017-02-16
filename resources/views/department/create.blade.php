
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.department') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'department.store','role' => 'form', 'class'=>'department-form','id' => 'department-form','data-form-table' => 'department_table']) !!}
			@include('department._form')
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>