
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title">{!! trans('messages.sub_task').' '.trans('messages.rating') !!}</h4>
	</div>
	<div class="modal-body">
		{!! Form::model($task,['method' => 'POST','route' => ['task.store-rating',$task] ,'class' => 'sub-task-rating-form','id' => 'sub-task-rating-form','data-submit' => 'noAjax']) !!}
		  
			@foreach($task->SubTask as $sub_task)
				<div class="form-group">
					{!! Form::label('',$sub_task->title,['class' => 'control-label'])!!}
					<div class="row">
						<div class="col-md-4">
							{!! Form::select('rating['.$sub_task->id.']', ['' => '', '1' => '1 Star','2' => '2 Star','3' => '3 Star','4' => '4 Star','5' => '5 Star']
								,($sub_task->SubTaskRating->whereLoose('user_id',$user->id)->count()) ? $sub_task->SubTaskRating->whereLoose('user_id',$user->id)->first()->rating : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						</div>
						<div class="col-md-8">
							{!! Form::textarea('comment['.$sub_task->id.']',($sub_task->SubTaskRating->whereLoose('user_id',$user->id)->count()) ? $sub_task->SubTaskRating->whereLoose('user_id',$user->id)->first()->comment : '',['size' => '30x6', 'class' => 'form-control', 'placeholder' => trans('messages.comment'),"data-show-counter" => 1,"data-limit" => config('config.textarea_limit'),'data-autoresize' => 1,'size' => '30x2'])!!}
							<span class="countdown"></span>
						</div>
					</div>
				</div>
			@endforeach
			<input type="hidden" name="user_id" readonly value="{{$user->id}}">
		  <div class="form-group">
		  	{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right btn-sm']) !!}
		  </div>
		{!! Form::close() !!}
		<div class="clear"></div>
	</div>
	<div class="modal-footer">
	</div>