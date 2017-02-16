			<div class="col-sm-12">
				{!! Form::label('enable_job_application_candidates',trans('messages.enable_job_application_candidates'),['class' => 'control-label'])!!}
				<div class="radio">
					<label>
						{!! Form::radio('enable_job_application_candidates', '1', (config('config.enable_job_application_candidates') == '1') ? 'checked' : '') !!} {!! trans('messages.yes') !!}
					</label>
					<label>
						{!! Form::radio('enable_job_application_candidates', '0', (config('config.enable_job_application_candidates') != '1') ? 'checked' : '') !!} {!! trans('messages.no') !!}
					</label>
				</div>
			  <div class="form-group">
			    {!! Form::label('application_format',trans('messages.job_application_file_format'),['class' => 'control-label'])!!}
				{!! Form::input('text','application_format',(config('config.application_format')) ? : '',['class'=>'form-control select2dynamictag','placeholder'=>trans('messages.application_format')])!!}
			  </div>
			  {!! Form::hidden('config_type','job')!!}
				{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
			</div>
			<div class="clear"></div>
