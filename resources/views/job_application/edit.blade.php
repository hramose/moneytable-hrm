
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.job').' '.trans('messages.application') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($job_application,['files' => true,'method' => 'PATCH','route' => ['job-application.update',$job_application] ,'class' => 'job-application-form','data-submit' => 'noAjax']) !!}
			@include('job_application._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>