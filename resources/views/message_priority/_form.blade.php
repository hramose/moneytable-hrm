
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.priority'),[])!!}
				{!! Form::input('text','name',isset($message_priority->name) ? $message_priority->name : '',['class'=>'form-control','placeholder'=>trans('messages.priority')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
			  	
