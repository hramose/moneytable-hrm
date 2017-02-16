		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					{!! Form::label('title',trans('messages.title'),[])!!}
					{!! Form::input('text','title',isset($task->title) ? $task->title : '',['class'=>'form-control','placeholder'=>trans('messages.title')])!!}
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							{!! Form::label('start_date',trans('messages.start_date'),[])!!}
							{!! Form::input('text','start_date',isset($task->start_date) ? $task->start_date : '',['class'=>'form-control datepicker','placeholder'=>trans('messages.start_date'),'readonly' => 'true'])!!}
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							{!! Form::label('due_date',trans('messages.date_of_due'),[])!!}
							{!! Form::input('text','due_date',isset($task->due_date) ? $task->due_date : '',['class'=>'form-control datepicker','placeholder'=>trans('messages.date_of_due'),'readonly' => 'true'])!!}
						</div>
					</div>
				</div>
				<div class="form-group">
					{!! Form::label('user_id',trans('messages.assigned_to'),['class' => 'control-label'])!!}
					{!! Form::select('user_id[]', $users, isset($selected_user) ? $selected_user : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one'),'multiple' => true])!!}
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					{!! Form::label('description',trans('messages.description'),[])!!}
					{!! Form::textarea('description',isset($task->description) ? $task->description : '',['size' => '30x9', 'class' => 'form-control summernote-big', 'placeholder' => trans('messages.description')])!!}
					<span class="countdown"></span>
				</div>
			</div>
		</div>
		{{ App\Classes\Helper::getCustomFields('task-form',$custom_field_values) }}
		{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
