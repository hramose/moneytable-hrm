
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				{!! Form::label('subject',trans('messages.subject'),[])!!}
				{!! Form::input('text','subject',isset($ticket->subject) ? $ticket->subject : '',['class'=>'form-control','placeholder'=>trans('messages.subject')])!!}
			</div>
			<div class="form-group">
				{!! Form::label('priority',trans('messages.priority'),['class' => 'control-label'])!!}
				{!! Form::select('priority', [
				null=>trans('messages.select_one')] + $priorities
				, isset($ticket->priority) ? $ticket->priority : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				{!! Form::label('description',trans('messages.description'),[])!!}
				{!! Form::textarea('description',isset($ticket->description) ? $ticket->description : '',['size' => '30x5', 'class' => 'form-control', 'placeholder' => trans('messages.description'),'data-show-counter' => 1,'data-limit' => config('config.textarea_limit')])!!}
				<span class="countdown"></span>
			</div>
		</div>
	</div>
	{{ App\Classes\Helper::getCustomFields('ticket-form',$custom_field_values) }}
	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
