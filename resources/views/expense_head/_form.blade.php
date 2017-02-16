
			  <div class="form-group">
			    {!! Form::label('head',trans('messages.expense_head'),[])!!}
				{!! Form::input('text','head',isset($expense_head->head) ? $expense_head->head : '',['class'=>'form-control','placeholder'=>trans('messages.expense_head')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
