
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.expense') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'expense.store','role' => 'form', 'files' => 'true', 'class'=>'expense-form','id' => 'expense-form','data-form-table' => 'expense_table']) !!}
			@include('expense._form')
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>