
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.contract_type') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($contract_type,['method' => 'PATCH','route' => ['contract-type.update',$contract_type->id] ,'class' => 'contract-type-form','id' => 'contract-type-form-edit','data-table-alter' => 'contract-type-table']) !!}
			@include('contract_type._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
