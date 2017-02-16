
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.document_type') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($document_type,['method' => 'PATCH','route' => ['document-type.update',$document_type->id] ,'class' => 'document-type-form','id' => 'document-type-form-edit','data-table-alter' => 'document-type-table']) !!}
			@include('document_type._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
