@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/language">{!! trans('messages.language') !!}</a></li>
		    <li class="active">{!! config('lang.'.$locale.'.language') !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info full">
					<div class="tabs-left">	
						<ul class="nav nav-tabs col-md-2" style="padding-right:0;">
						  <li class="active"><a href="#basic" data-toggle="tab"> {{ trans('messages.basic') }} </a></li>
						  @foreach($modules as $module)
						  <li><a href="#_{{ $module }}" data-toggle="tab"> {!! trans('messages.'.$module) !!} ({{ $word_count[$module] }})</a></li>
						  @endforeach
						</ul>
				        <div id="myTabContent" class="tab-content col-md-10" style="padding:5px;">
						  <div class="tab-pane active animated fadeInRight" id="basic">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.basic') }}</strong> {{ trans('messages.configuration') }}</h2>

						    	{!! Form::model($language,['method' => 'PATCH','route' => ['language.plugin',$locale] ,'class' => 'language-plugin-form','id'=>'language-plugin-form','data-no-form-clear' => 1]) !!}
								  <div class="form-group">
								    {!! Form::label('datatable',trans('messages.table_language'),[])!!}
									{!! Form::select('datatable', [''=>''] + config('datatable_lang'),isset($locale) ? config('lang.'.$locale.'.datatable') : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
								  </div>
								  <div class="form-group">
								    {!! Form::label('calendar',trans('messages.calendar_language'),[])!!}
									{!! Form::select('calendar', [''=>''] + config('calendar_lang'),isset($locale) ? config('lang.'.$locale.'.calendar') : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
								  </div>
								  <div class="form-group">
								    {!! Form::label('datepicker',trans('messages.datepicker_language'),[])!!}
									{!! Form::select('datepicker', [''=>''] + config('datepicker_lang'),isset($locale) ? config('lang.'.$locale.'.datepicker') : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
								  </div>
								  <div class="form-group">
								    {!! Form::label('datetimepicker',trans('messages.datetimepicker_language'),[])!!}
									{!! Form::select('datetimepicker', [''=>''] + config('datetimepicker_lang'),isset($locale) ? config('lang.'.$locale.'.datetimepicker') : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
								  </div>
								  <div class="form-group">
								    {!! Form::label('validation',trans('messages.validation_language'),[])!!}
									{!! Form::select('validation', [''=>''] + config('validation_lang'),isset($locale) ? config('lang.'.$locale.'.validation') : '',['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
								  </div>
								{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
								{!! Form::close() !!}

								<br /><br />

						    	<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.word_for_translation') !!}</h2>
								{!! Form::open(['route' => 'language.add-words','role' => 'form', 'class'=>'language-entry-form','id' => 'language-entry-form','data-submit' => 'noAjax']) !!}
								  
						  		  <div class="form-group">
								    {!! Form::label('text',trans('messages.key'),[])!!}
									{!! Form::input('text','key','',['class'=>'form-control','placeholder'=>trans('messages.key')])!!}
								  </div>
						  		  <div class="form-group">
								    {!! Form::label('text',trans('messages.word_or_sentence'),[])!!}
									{!! Form::input('text','text','',['class'=>'form-control','placeholder'=>trans('messages.word_or_sentence')])!!}
								  </div>
						  		  <div class="form-group">
								    {!! Form::label('module',trans('messages.module'),[])!!}
									{!! Form::input('text','module','',['class'=>'form-control','placeholder'=>trans('messages.module')])!!}
								  </div>
								{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
								{!! Form::close() !!}
						    </div>
						  </div>
				          @foreach($modules as $module)
						  <div class="tab-pane animated fadeInRight" id="_{{ $module }}">
						    <div class="user-profile-content-wm">
						    	<h2><strong>{{ trans('messages.'.$module) }}</strong> {{ trans('messages.translation') }}</h2>
						    		{!! Form::model($language,['method' => 'PATCH','route' => ['language.update-translation',$locale] ,'class' => 'form-horizontal','id'=>'language_translation_'.$module, 'data-no-form-clear' => 1]) !!}
									@foreach($words as $key => $word)
										@if($word['module'] == $module)
										<div class="form-group">
									    	{!! Form::label($key,$word['value'],['class'=>'col-sm-6 control-label pull-left'])!!}
											<div class="col-sm-6">
												@if($locale == 'en')
												{!! Form::input('text',$key,(array_key_exists($key, $translation)) ? $translation[$key] : $word['value'],['class'=>'form-control','placeholder'=>trans('messages.translation')])!!}
												@else
												{!! Form::input('text',$key,(array_key_exists($key, $translation)) ? $translation[$key] : '',['class'=>'form-control','placeholder'=>trans('messages.translation')])!!}
												@endif
											</div>
									  	</div>
									  	@endif
									@endforeach
									{!! Form::hidden('module',$module) !!}
									{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
								{!! Form::close() !!}
						    </div>
						  </div>
						  @endforeach
						</div>
					</div>
				</div>
			</div>
		</div>

	@stop