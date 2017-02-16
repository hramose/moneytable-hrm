
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.leave_type') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($leave_type,['method' => 'PATCH','route' => ['leave-type.update',$leave_type->id] ,'class' => 'leave-type-form','id' => 'leave-type-form-edit','data-table-alter' => 'leave-type-table']) !!}
			@include('leave_type._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
