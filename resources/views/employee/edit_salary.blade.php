
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.salary').' : '.$contract->full_contract_name !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($contract,['method' => 'PATCH','route' => ['salary.update',$contract->id] ,'class' => 'salary-form', 'role' => 'form','id' => 'salary-form-edit', 'data-table-alter' => 'salary-table']) !!}
		  	@include('employee._salary_form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
