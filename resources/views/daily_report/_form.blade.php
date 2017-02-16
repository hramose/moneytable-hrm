			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('user_id',trans('messages.employee'),['class' => 'control-label'])!!}
						{!! Form::select('user_id',[''=>''] + $users, isset($daily_report->user_id) ? $daily_report->user_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('date',trans('messages.date'),[])!!}
						{!! Form::input('text','date',isset($daily_report->date) ? $daily_report->date : '',['class'=>'form-control datepicker','placeholder'=>trans('messages.date'),'readonly' => 'true'])!!}
					</div>
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('description',trans('messages.description'),[])!!}
				{!! Form::textarea('description',isset($daily_report->description) ? $daily_report->description : '',['size' => '30x6', 'class' => 'form-control summernote-big', 'placeholder' => trans('messages.description')])!!}
			</div>
		  	{{ App\Classes\Helper::getCustomFields('daily-report-form',$custom_field_values) }}
		  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}