
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.salary_type') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($salary_type,['method' => 'PATCH','route' => ['salary-type.update',$salary_type->id] ,'class' => 'salary-type-form','id' => 'salary-type-form-edit','data-table-alter' => 'salary-type-table']) !!}
			@include('salary_type._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
