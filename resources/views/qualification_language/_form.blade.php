
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.language'),[])!!}
				{!! Form::input('text','name',isset($qualification_language->name) ? $qualification_language->name : '',['class'=>'form-control','placeholder'=>trans('messages.language')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
