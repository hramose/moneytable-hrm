
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.contract') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($contract,['method' => 'PATCH','route' => ['contract.update',$contract->id] ,'class' => 'contract-form','id' => 'contract-edit-form','data-table-alter' => 'contract-table']) !!}
			@include('employee._contract_form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
