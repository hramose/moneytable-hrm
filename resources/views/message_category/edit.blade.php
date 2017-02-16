
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.category') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($message_category,['method' => 'PATCH','route' => ['message-category.update',$message_category->id] ,'class' => 'message-category-form','id' => 'message-category-form-edit','data-table-alter' => 'message-category-table']) !!}
			@include('message_category._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
