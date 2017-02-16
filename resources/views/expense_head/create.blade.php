
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.add_new').' '.trans('messages.expense_head') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::open(['route' => 'expense-head.store','role' => 'form', 'class'=>'expense-head-form','id' => 'expense-head-form']) !!}
			@include('expense_head._form')
		{!! Form::close() !!}
	</div>
