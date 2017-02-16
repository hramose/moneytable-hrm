
			  <div class="form-group">
			    {!! Form::label('salary_type',trans('messages.type'),[])!!}
				{!! Form::select('salary_type', [null=>trans('messages.select_one'),'earning' => trans('messages.earning'),'deduction' => trans('messages.deduction')],isset($salary_type->salary_type) ? $salary_type->salary_type : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
			  </div>
			  <div class="form-group">
			    {!! Form::label('head',trans('messages.salary_head'),[])!!}
				{!! Form::input('text','head',isset($salary_type->head) ? $salary_type->head : '',['class'=>'form-control','placeholder'=>trans('messages.salary_head')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
