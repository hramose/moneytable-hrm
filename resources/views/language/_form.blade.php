
			  <div class="form-group">
			    {!! Form::label('locale',trans('messages.locale'),[])!!}
			  	@if(!isset($locale))
					{!! Form::input('text','locale',isset($locale) ? $locale : '',['class'=>'form-control','placeholder'=>trans('messages.locale')])!!}
				@else
					{!! Form::input('text','locale',isset($locale) ? $locale : '',['class'=>'form-control','placeholder'=>trans('messages.locale'),'readonly' => 'true'])!!}
				@endif	
				<div class="help-box">{!! trans('messages.not_editable') !!}</div>
			  </div>
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.language_name'),[])!!}
				{!! Form::input('text','name',isset($locale) ? config('lang.'.$locale.'.language') : '',['class'=>'form-control','placeholder'=>trans('messages.language_name')])!!}
			  </div>
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
