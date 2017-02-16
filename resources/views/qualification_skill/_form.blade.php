
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.skill'),[])!!}
				{!! Form::input('text','name',isset($qualification_skill->name) ? $qualification_skill->name : '',['class'=>'form-control','placeholder'=>trans('messages.skill')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
