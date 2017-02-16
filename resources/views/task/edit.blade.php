
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.task') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($task,['method' => 'PATCH','route' => ['task.update',$task] ,'class' => 'task-form','id' => 'task-form-edit','data-form-table' => 'task_table']) !!}
			@include('task._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>
