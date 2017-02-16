
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.role_name'),[])!!}
				{!! Form::input('text','name',isset($role->name) ? $role->name : '',['class'=>'form-control','placeholder'=>trans('messages.role_name')])!!}
			  </div>
			  @if(isset($role) && $role->is_default)
			  	<div class="form-group">
			  		<span class="label label-danger">{{trans('messages.user').' '.trans('messages.default')}}</span>
			  	</div>
			  @else
			  <div class="checkbox">
			  	<label>
			  		<input type="checkbox" name="is_default" value="1"> {{trans('messages.user').' '.trans('messages.default')}}
			  	</label>
			  </div>
			  @endif
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
