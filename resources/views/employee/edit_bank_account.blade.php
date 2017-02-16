
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.bank_account') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($bank_account,['method' => 'PATCH','route' => ['bank-account.update',$bank_account->id] ,'class' => 'bank-account-form', 'role' => 'form','id' => 'bank-account-edit-form','data-table-alter' => 'bank-account-table']) !!}
		  	@include('employee._bank_account_form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
