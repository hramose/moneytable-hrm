
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.award_type'),[])!!}
				{!! Form::input('text','name',isset($award_type->name) ? $award_type->name : '',['class'=>'form-control','placeholder'=>trans('messages.award_type')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
