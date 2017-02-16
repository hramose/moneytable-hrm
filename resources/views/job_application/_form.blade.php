	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				{!! Form::label('job_id',trans('messages.job'),[])!!}
				{!! Form::select('job_id', [null => trans('messages.select_one')] + $jobs,isset($job_application->job_id) ? $job_application->job_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
			</div>
			<div class="form-group">
				{!! Form::label('name',trans('messages.name'),[])!!}
				{!! Form::input('text','name',isset($job_application->name) ? $job_application->name : '',['class'=>'form-control','placeholder'=>trans('messages.name')])!!}
			</div>
			<div class="form-group">
				{!! Form::label('email',trans('messages.email'),[])!!}
				{!! Form::input('email','email',isset($job_application->email) ? $job_application->email : '',['class'=>'form-control','placeholder'=>trans('messages.email')])!!}
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				{!! Form::label('contact_number',trans('messages.contact_number'),[])!!}
				{!! Form::input('text','contact_number',isset($job_application->contact_number) ? $job_application->contact_number : '',['class'=>'form-control','placeholder'=>trans('messages.contact_number')])!!}
			</div>
			@if(isset($job_application) && $job_application->source != 'website')
				<div class="form-group">
				{!! Form::label('source',trans('messages.application_source'),[])!!}
				{!! Form::select('source', [null => trans('messages.select_one')] + $source,isset($job_application->source) ? $job_application->source : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
				</div>
			@endif
			<div class="form-group">
				{!! Form::label('date_of_application',trans('messages.date_of_application'),[])!!}
				{!! Form::input('text','date_of_application',isset($job_application->date_of_application) ? $job_application->date_of_application : '',['class'=>'form-control datepicker','placeholder'=>trans('messages.date_of_application'),'readonly' => 'true'])!!}
			</div>
			<div class="form-group">
				<input type="file" name="resume" id="resume" class="btn btn-default" title="{!! trans('messages.select').' '.trans('messages.resume') !!}">
			</div>
		</div>
	</div>
	{{ App\Classes\Helper::getCustomFields('job-application-form',$custom_field_values) }}
	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.add'),['class' => 'btn btn-primary pull-right']) !!}