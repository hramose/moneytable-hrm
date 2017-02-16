
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.document_type'),[])!!}
				{!! Form::input('text','name',isset($document_type->name) ? $document_type->name : '',['class'=>'form-control','placeholder'=>trans('messages.document_type')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
			  	
