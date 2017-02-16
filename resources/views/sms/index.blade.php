@extends('layouts.default')

	@section('content')
		<div class="row">
			<div class="col-sm-8">
				<div class="box-info">
					<h2><strong>{!! trans('messages.send_sms') !!} </strong> {!! $type_detail !!}
					<div class="additional-btn">
						<a href="/sms/designation"><button class="btn btn-sm btn-primary"><i class="fa fa-sitemap icon"></i> {!! trans('messages.sms_designation') !!}</button></a>
						<a href="/sms/employee"><button class="btn btn-sm btn-primary"><i class="fa fa-user icon"></i> {!! trans('messages.sms_individual_employee') !!}</button></a>
					</div>
					</h2>
					
					{!! Form::open(['route' => 'sms.store','role' => 'form', 'class'=>'sms-form']) !!}
						
					  <div class="form-group">
					    {!! Form::label('receivers',ucfirst($type_detail),[])!!}
						{!! Form::select('receivers', $receivers,'',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one'),'multiple' => 'true'])!!}
					  </div>
					  <div class="form-group">
					    {!! Form::label('sms',trans('messages.content'),[])!!}
					    {!! Form::textarea('sms','',['size' => '30x3', 'class' => 'form-control', 'placeholder' => trans('messages.content'),'onkeyup'=>'countChar(this)','data-limit' => 160])!!}
					  	<span class="countdown"></span>
					  </div>
					  	{!! Form::hidden('type',$type) !!}
					  	{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.send_sms'),['class' => 'btn btn-primary']) !!}

					{!! Form::close() !!}
				</div>
			</div>
			<div class="col-sm-4">
				<div class="the-notes info"><h4>{!! trans('messages.help') !!}</h4>To use this module, you need to have a working XML SMS API. You can integrate any SMS API as you want. This module only provide SMS interface. You can ask the developer to integrate the API.</div>
			</div>
		</div>
	@stop