
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.leave').': '.$contract->full_contract_name !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($contract,['method' => 'PATCH','route' => ['user-leave.update',$contract->id] ,'class' => 'user-leave-form','id' => 'user-leave-form-edit', 'data-table-alter' => 'user-leave-table']) !!}
			@include('employee._user_leave_form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
