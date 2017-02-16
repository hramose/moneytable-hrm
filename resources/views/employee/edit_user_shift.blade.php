
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.shift') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($user_shift,['method' => 'PATCH','route' => ['user-shift.update',$user_shift->id] ,'class' => 'user-shift-form','id' => 'user-shift-form-edit','data-table-alter' => 'user-shift-table']) !!}
			@include('employee._user_shift_form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>