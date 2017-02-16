
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.office_shift') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($office_shift,['method' => 'PATCH','route' => ['office-shift.update',$office_shift->id] ,'class' => 'office-shift-form','id' => 'office-shift-form-edit','data-table-alter' => 'office-shift-table']) !!}
			@include('office_shift._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
