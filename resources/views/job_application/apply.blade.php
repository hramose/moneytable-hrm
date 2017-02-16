@extends('layouts.blank')

	@section('content')
		<div class="row">
			<div class="col-sm-6">
				<div class="box-info">
					<h2><strong>{!! trans('messages.job').'</strong> '.trans('messages.openings') !!}
					</h2>

					<ul class="media-list search-result">
					@foreach($jobs as $job)
					  <li class="media">
						<div class="media-body">
						  <h4 class="media-heading"><a href="#" target="_blank">{!! $job->title !!}</a> <span class="label label-warning pull-right">{!! trans('messages.date_of_closing').' '.showDate($job->date_of_closing) !!}</span></h4>
						  <a href="#" target="_blank">{!! $job->Designation->full_designation !!} <span class="label label-success">{!! $job->location !!}</span></a>
						  <p>{!! $job->description !!} <span class="label label-primary pull-right"> <i class="fa fa-clock-o"></i> {!! trans('messages.posted_on').' '.showDateTime($job->created_at) !!}</span></p>
						</div>
					  </li>
					@endforeach
					</ul>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box-info">
					<h2><strong>{!! trans('messages.apply').'</strong> '.trans('messages.now') !!}</h2>

					{!! Form::open(['files' => true, 'route' => 'job-application.store','role' => 'form', 'class'=>'job-application-form','id' => 'job-application-form']) !!}
					  <div class="form-group">
					    {!! Form::label('job_id',trans('messages.job'),[])!!}
						{!! Form::select('job_id', [null => trans('messages.select_one')] + $job_list,'',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
					  </div>
					  <div class="form-group">
					    {!! Form::label('name',trans('messages.name'),[])!!}
						{!! Form::input('text','name','',['class'=>'form-control','placeholder'=>trans('messages.name')])!!}
					  </div>
					  <div class="form-group">
					    {!! Form::label('email',trans('messages.email'),[])!!}
						{!! Form::input('email','email','',['class'=>'form-control','placeholder'=>trans('messages.email')])!!}
					  </div>
					  <div class="form-group">
					    {!! Form::label('contact_number',trans('messages.contact_number'),[])!!}
						{!! Form::input('text','contact_number','',['class'=>'form-control','placeholder'=>trans('messages.contact_number')])!!}
					  </div>
					  	<div class="form-group">
							<input type="file" name="resume" id="resume" class="btn btn-default" title="{!! trans('messages.select').' '.trans('messages.resume') !!}">
						</div>
					  	{{ App\Classes\Helper::getCustomFields('job-application-form',$custom_field_values) }}
					  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.submit'),['class' => 'btn btn-primary pull-right']) !!}
					{!! Form::close() !!}

					</h2>
				</div>
			</div>
		</div>
	@stop