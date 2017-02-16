
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.leave') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($leave,['method' => 'PATCH','files'=>'true','route' => ['leave.update',$leave] ,'class' => 'leave-form','id' => 'leave-form-edit','data-form-table' => 'leave_table']) !!}
			@include('leave._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>