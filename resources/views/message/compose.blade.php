@extends('layouts.default')
	@section('content')
		<div class="box-info box-messages">
			<div class="row">
			@include('message.sidebar')			
				<div class="col-md-10">
					{!! Form::open(['files'=>'true','route' => 'message.store','role' => 'form', 'class'=>'compose-form','id' => 'compose-form','data-submit' => 'noAjax']) !!}
						<div class="form-group">
							{!! Form::label('message_category_id',trans('messages.category'),['class' => 'control-label'])!!}
							{!! Form::select('message_category_id', [null=>trans('messages.select_one')] + $message_categories, '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						</div>
						<div class="form-group">
							{!! Form::label('message_category_id',trans('messages.priority'),['class' => 'control-label'])!!}
							{!! Form::select('message_priority_id', [null=>trans('messages.select_one')] + $message_priorities, '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						</div>
						<div class="form-group">
							{!! Form::label('message_category_id',trans('messages.receiver'),['class' => 'control-label'])!!}
							{!! Form::select('to_user_id', [null=>trans('messages.select_one')] + $users, '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
						</div>
						<div class="form-group">
							{!! Form::input('text','subject','',['class'=>'form-control','placeholder'=>trans('messages.subject')])!!}
						</div>
						<div class="form-group">
							{!! Form::textarea('body','',['class' => 'form-control summernote-big', 'placeholder' => trans('messages.body')])!!}
						</div>
						<div class="form-group">
							<input type="file" name="file" id="file" class="btn btn-default" title="{!! trans('messages.select').' '.trans('messages.attachment') !!}">
						</div>
						<div class="row">
							<div class="col-xs-8">
								<button type="submit" class="btn btn-success btn-sm">{!! trans('messages.send') !!}</button>
								<a href="/message/compose" class="btn btn-danger btn-sm">{!! trans('messages.discard') !!}</a>
							</div>
						</div>	
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	@stop