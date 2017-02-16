
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.education_level'),[])!!}
				{!! Form::input('text','name',isset($education_level->name) ? $education_level->name : '',['class'=>'form-control','placeholder'=>trans('messages.education_level')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
