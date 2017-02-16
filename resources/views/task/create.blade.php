
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.task') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'task.store','role' => 'form', 'class'=>'task-form','id' => 'task-form','data-form-table' => 'task_table']) !!}
			@include('task._form')
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>