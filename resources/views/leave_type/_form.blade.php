
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.leave_type'),[])!!}
				{!! Form::input('text','name',isset($leave_type->name) ? $leave_type->name : '',['class'=>'form-control','placeholder'=>trans('messages.leave_type')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
			  	
