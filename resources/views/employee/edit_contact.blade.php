
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.contact') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($contact,['method' => 'PATCH','route' => ['contact.update',$contact->id] ,'class' => 'employee-contact-form', 'id' => 'employee-contact-form-edit','data-table-alter' => 'employee-contact-table']) !!}
		  	@include('employee._contact_form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
