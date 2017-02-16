
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.role_name'),[])!!}
				{!! Form::input('text','name',isset($role->name) ? $role->name : '',['class'=>'form-control','placeholder'=>trans('messages.role_name')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
