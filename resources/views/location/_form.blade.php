
			  <div class="form-group">
			    {!! Form::label('top_location_id',trans('messages.top_location'),[])!!}
				{!! Form::select('top_location_id', [null =>trans('messages.select_one')] + $top_locations,(isset($location->top_location_id)) ? $location->top_location_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
			  </div>
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.location'),[])!!}
				{!! Form::input('text','name',isset($location->name) ? $location->name : '',['class'=>'form-control','placeholder'=>trans('messages.location')])!!}
			  </div>
			  	{{ App\Classes\Helper::getCustomFields('location-form',$custom_field_values) }}
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
