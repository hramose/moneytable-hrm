
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.language') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($qualification_language,['method' => 'PATCH','route' => ['qualification-language.update',$qualification_language->id] ,'class' => 'qualification-language-form','id' => 'qualification-language-form-edit','data-table-alter' => 'qualification-language-table']) !!}
			@include('qualification_language._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
	</div>
