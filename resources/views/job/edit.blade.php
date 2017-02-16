
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.edit').' '.trans('messages.job') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($job,['method' => 'PATCH','route' => ['job.update',$job] ,'class' => 'job-form','id' => 'job-form-edit','data-form-table' => 'job_table']) !!}
			@include('job._form', ['buttonText' => trans('messages.update')])
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>