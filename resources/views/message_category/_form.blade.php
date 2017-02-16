
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.category'),[])!!}
				{!! Form::input('text','name',isset($message_category->name) ? $message_category->name : '',['class'=>'form-control','placeholder'=>trans('messages.category')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
			  	
