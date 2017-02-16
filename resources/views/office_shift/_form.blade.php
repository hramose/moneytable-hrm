				
			  <div class="form-group">
			    {!! Form::label('name',trans('messages.shift_name'),[])!!}
				{!! Form::input('text','name',isset($office_shift->name) ? $office_shift->name : '',['class'=>'form-control','placeholder'=>trans('messages.shift_name')])!!}
			  </div>
			  @foreach(config('lists.week') as $day => $day_name)
			  <div class="form-group row">
			  	  {!! Form::label('time',trans('messages.'.$day),['class' => 'col-md-2'])!!}
			  	  {!! Form::checkbox("overnight[$day]", 1, (isset($week) && strtotime($week['in_time'][$day]) > strtotime($week['out_time'][$day])) ? 'checked' : '') !!} {!! trans('messages.overnight') !!} 
			  	  <div class="col-md-4">
				  {!! Form::input('text',"week[in_time][$day]",(isset($week) && $week['in_time'][$day] != $week['out_time'][$day]) ? $week['in_time'][$day]  : '',['class'=>'form-control timepicker','placeholder'=>trans('messages.in_time'),'readonly' => true])!!}
				  </div>
				  <div class="col-md-4">
				  {!! Form::input('text',"week[out_time][$day]",(isset($week) && $week['in_time'][$day] != $week['out_time'][$day]) ? $week['out_time'][$day]  : '',['class'=>'form-control timepicker','placeholder'=>trans('messages.out_time'),'readonly' => true])!!}
				  </div>
			  </div>
			  @endforeach
			  {!! Form::hidden('config_type','office_shift')!!}
			  {!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}

