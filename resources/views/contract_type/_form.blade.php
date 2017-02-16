
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.contract_type'),[])!!}
				{!! Form::input('text','name',isset($contract_type->name) ? $contract_type->name : '',['class'=>'form-control','placeholder'=>trans('messages.contract_type')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
