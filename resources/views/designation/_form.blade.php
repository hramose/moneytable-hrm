
			  <div class="form-group">
			    {!! Form::label('department_id',trans('messages.department'),[])!!}
				{!! Form::select('department_id', [''=>''] + $departments,isset($designation->department_id) ? $designation->department_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
			  </div>
			  <div class="form-group">
			    {!! Form::label('top_designation_id',trans('messages.top_designation'),[])!!}
				{!! Form::select('top_designation_id', [null =>trans('messages.select_one')] + $top_designations,(isset($designation->top_designation_id) && $designation->top_designation_id != 1) ? $designation->top_designation_id : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
			  </div>
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.designation'),[])!!}
				{!! Form::input('text','name',isset($designation->name) ? $designation->name : '',['class'=>'form-control','placeholder'=>trans('messages.designation')])!!}
			  </div>
			  	{{ App\Classes\Helper::getCustomFields('designation-form',$custom_field_values) }}
			  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
