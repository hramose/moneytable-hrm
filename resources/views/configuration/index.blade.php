@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li class="active">{!! trans('messages.configuration') !!}</li>
		</ul>
	@stop
	
	@section('content')

		<div class="row">
			<div class="col-sm-12">
				<div class="box-info full">
					<div class="tabs-left">	
						<ul class="nav nav-tabs col-md-2" style="padding-right:0;">
						  <li class="active"><a href="#general" data-toggle="tab"><span class="fa fa-cog"></span> {!! trans('messages.general') !!}</a></li>
						  <li><a href="#comp_logo" data-toggle="tab"><span class="fa fa-image"></span> {!! trans('messages.logo') !!}</a></li>
						  <li><a href="#system" data-toggle="tab"><span class="fa fa-cogs"></span> {!! trans('messages.system') !!}</a></li>
						  <li><a href="#menu" data-toggle="tab"><span class="fa fa-list"></span> {!! trans('messages.menu') !!}</a></li>
						  <li><a href="#role" data-toggle="tab"><span class="fa fa-key"></span> {!! trans('messages.role') !!}</a></li>
						  <li><a href="#attendance" data-toggle="tab"><span class="fa fa-book"></span> {!! trans('messages.attendance') !!}</a></li>
						  <li><a href="#payroll" data-toggle="tab"><span class="fa fa-credit-card"></span> {!! trans('messages.payroll') !!}</a></li>
						  <li><a href="#mail" data-toggle="tab"><span class="fa fa-envelope"></span> {!! trans('messages.mail') !!}</a></li>
						  <li><a href="#office-shift" data-toggle="tab"><span class="fa fa-clock-o"></span> {!! trans('messages.office_shift') !!}</a></li>
						  <li><a href="#qualification" data-toggle="tab"><span class="fa fa-book"></span> {!! trans('messages.qualification') !!}</a></li>
						  <li><a href="#contract-type" data-toggle="tab"><span class="fa fa-pencil"></span> {!! trans('messages.contract') !!}</a></li>
						  <li><a href="#award" data-toggle="tab"><span class="fa fa-trophy"></span> {!! trans('messages.award') !!}</a></li>
						  <li><a href="#leave" data-toggle="tab"><span class="fa fa-coffee"></span> {!! trans('messages.leave') !!}</a></li>
						  <li><a href="#message" data-toggle="tab"><span class="fa fa-envelope"></span> {!! trans('messages.message') !!}</a></li>
						  <li><a href="#document" data-toggle="tab"><span class="fa fa-file"></span> {!! trans('messages.document') !!}</a></li>
						  <li><a href="#salary" data-toggle="tab"><span class="fa fa-money"></span> {!! trans('messages.salary') !!}</a></li>
						  <li><a href="#expense" data-toggle="tab"><span class="fa fa-credit-card"></span> {!! trans('messages.expense') !!}</a></li>
						  <li><a href="#job" data-toggle="tab"><span class="fa fa-bullhorn"></span> {!! trans('messages.job') !!}</a></li>
						  <li><a href="#api" data-toggle="tab"><span class="fa fa-ellipsis-h"></span> {!! trans('messages.api_configuration') !!}</a></li>
						  <li><a href="#ip" data-toggle="tab"><span class="fa fa-ban"></span> {!! trans('messages.ip_restriction') !!}</a></li>
						  <li><a href="#scheduler" data-toggle="tab"><span class="fa fa-hourglass"></span> {!! trans('messages.scheduler') !!}</a></li>
				        </ul>
				        <div id="myTabContent" class="tab-content col-md-10"  style="padding:5px;">
				        
						  <div class="tab-pane animated active fadeInRight" id="general">
						    <div class="user-profile-content-wm">
								<h2><strong>{!! trans('messages.general') !!}</strong> {!! trans('messages.configuration') !!}</h2>
								{!! Form::open(['route' => 'configuration.store','role' => 'form', 'class'=>'config-form','id' => 'config-form','data-no-form-clear' => 1]) !!}
									@include('configuration._form')
								{!! Form::close() !!}
							</div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="menu">
						    <div class="user-profile-content-wm">
								{!! Form::open(['route' => 'configuration.menu-store','role' => 'form', 'class'=>'config-menu-form','id' => 'config-menu-form','data-draggable' => 1,'data-no-form-clear' => 1,'data-sidebar' => 1]) !!}
								<h2><strong>{!! trans('messages.menu') !!}</strong> {!! trans('messages.configuration') !!}</h2>
								<div class="draggable-container">
									<?php $i = 0; ?>
									@foreach(\App\Menu::orderBy('order')->orderBy('id')->get() as $menu_item)
										<?php $i++; ?>
									  <div class="draggable" data-name="{{$menu_item->name}}" data-index="{{$i}}">
									    <p><input type="checkbox" name="{{$menu_item->name}}-visible" value="1" {{($menu_item->visible) ? 'checked' : ''}}> <span style="margin-left:50px;">{{\App\Classes\Helper::toWord($menu_item->name)}}</span></p>
									  </div>
									@endforeach
								</div>
								{!! Form::hidden('config_type','menu')!!}
			  					{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
								{!! Form::close() !!}
							</div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="comp_logo">
						    <div class="user-profile-content-wm">
								<h2><strong>{!! trans('messages.logo') !!}</strong></h2>
								{!! Form::open(['files' => true, 'route' => 'configuration.logo-store','role' => 'form', 'class'=>'config-company-logo-form','id' => 'config-company-logo-form','data-submit' => 'noAjax']) !!}
								  	<div class="form-group">
										<input type="file" name="logo" id="logo" class="btn btn-default" title="{!! trans('messages.select').' '.trans('messages.logo') !!}">
										@if(config('config.logo') && File::exists(config('constants.upload_path.logo').config('config.logo')))
										<img src="{{ URL::to(config('constants.upload_path.logo').config('config.logo')) }}" width="150px" style="margin-left:20px;">
										<div class="checkbox">
											<label>
											  {!! Form::checkbox('remove_logo', 1) !!} {!! trans('messages.remove_logo') !!}
											</label>
										</div>
										@endif
									</div>
								{!! Form::hidden('config_type','comp_logo')!!}
			  					{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary']) !!}
								{!! Form::close() !!}
							</div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="scheduler">
						    <div class="user-profile-content-wm">
								<h2><strong>{!! trans('messages.scheduler') !!}</strong></h2>
								<p>Add below cron command in your server:</p>
								<div class="well">
									php /path-to-artisan schedule:run >> /dev/null 2>&1
								</div>
								<div class="table-responsive">
									<table class="table table-stripped table-bordered table-hover">
										<thead>
											<tr>
												<th>Action</th>
												<th>Status</th>
												<th>Frequency</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>Employee Status Update</td>
												<td>Yes</td>
												<td>Once per day</td>
											</tr>
											<tr>
												<td>Employee Designation Update</td>
												<td>Yes</td>
												<td>Once per day</td>
											</tr>
											<tr>
												<td>Daily Backup</td>
												<td>Yes</td>
												<td>Once per day</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="system">
						    <div class="user-profile-content-wm">
								<h2><strong>{!! trans('messages.system') !!}</strong> {!! trans('messages.configuration') !!}</h2>
								{!! Form::open(['route' => 'configuration.store','role' => 'form', 'class'=>'config-form','id' => 'system-form','data-no-form-clear' => 1]) !!}
									@include('configuration._system')
								{!! Form::close() !!}
							</div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="attendance">
						    <div class="user-profile-content-wm">
								<h2><strong>{!! trans('messages.attendance') !!}</strong> {!! trans('messages.configuration') !!}</h2>
								{!! Form::open(['route' => 'configuration.store','role' => 'form', 'class'=>'config-form','id' => 'attendance-form','data-no-form-clear' => 1]) !!}
									<div class="form-group">
										{!! Form::label('auto_clock_authentication',trans('messages.auto_clock_authentication'),['class' => ' control-label'])!!}
										<div class="radio">
											<label>
												{!! Form::radio('auto_clock_authentication', '1', (config('config.auto_clock_authentication')) ? 'checked' : '').' '.trans('messages.yes') !!}
											</label>
											<label>
												{!! Form::radio('auto_clock_authentication', '0', (!config('config.auto_clock_authentication')) ? 'checked' : '').' '.trans('messages.no') !!}
											</label>
										</div>
								  	</div>
									<!-- <div class="form-group">
										{!! Form::label('auto_lock_attendance_days',trans('messages.auto_lock_attendance_label'),[])!!}
										{!! Form::input('number','auto_lock_attendance_days',(config('config.auto_lock_attendance_days')) ? : '1',['class'=>'form-control','placeholder'=>trans('messages.auto_lock_attendance_label')])!!}
									</div>
									<div class="form-group">
										{!! Form::label('enable_future_attendance',trans('messages.enable_future_attendance'),[])!!}
										{!! Form::input('number','enable_future_attendance',(config('config.enable_future_attendance')) ? : '0',['class'=>'form-control','placeholder'=>'Allow future attendance for days'])!!}
									</div>
									<div class="form-group">
										{!! Form::label('late_comer_grace_time',trans('messages.grace_time_label').' ('.trans('messages.minute').')',[])!!}
										{!! Form::input('number','late_comer_grace_time',(config('config.late_comer_grace_time')) ? : '0',['class'=>'form-control','placeholder'=>trans('messages.grace_time_label')])!!}
									</div> -->
			  						{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
								{!! Form::close() !!}
							</div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="payroll">
						    <div class="user-profile-content-wm">
								<h2><strong>{!! trans('messages.payroll') !!}</strong> {!! trans('messages.configuration') !!}</h2>
								{!! Form::open(['route' => 'configuration.store','role' => 'form', 'class'=>'config-form','id' => 'payroll-form','data-no-form-clear' => 1]) !!}
									<div class="form-group">
										{!! Form::label('payroll_contribution_field',trans('messages.enable_payroll_contribution_field'),['class' => ' control-label'])!!}
										<div class="radio">
											<label>
												{!! Form::radio('payroll_contribution_field', '1', (config('config.payroll_contribution_field')) ? 'checked' : '').' '.trans('messages.yes') !!}
											</label>
											<label>
												{!! Form::radio('payroll_contribution_field', '0', (!config('config.payroll_contribution_field')) ? 'checked' : '').' '.trans('messages.no') !!}
											</label>
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('payroll_include_day_summary',trans('messages.payroll_include_day_summary'),['class' => ' control-label'])!!}
										<div class="radio">
											<label>
												{!! Form::radio('payroll_include_day_summary', '1', (config('config.payroll_include_day_summary')) ? 'checked' : '').' '.trans('messages.yes') !!}
											</label>
											<label>
												{!! Form::radio('payroll_include_day_summary', '0', (!config('config.payroll_include_day_summary')) ? 'checked' : '').' '.trans('messages.no') !!}
											</label>
										</div>
									</div>
									<div class="form-group">
										{!! Form::label('payroll_include_hour_summary',trans('messages.payroll_include_hour_summary'),['class' => ' control-label'])!!}
										<div class="radio">
											<label>
												{!! Form::radio('payroll_include_hour_summary', '1', (config('config.payroll_include_hour_summary')) ? 'checked' : '').' '.trans('messages.yes') !!}
											</label>
											<label>
												{!! Form::radio('payroll_include_hour_summary', '0', (!config('config.payroll_include_hour_summary')) ? 'checked' : '').' '.trans('messages.no') !!}
											</label>
										</div>
								  	</div>
									<div class="form-group">
										{!! Form::label('payroll_include_leave_summary',trans('messages.payroll_include_leave_summary'),['class' => ' control-label'])!!}
										<div class="radio">
											<label>
												{!! Form::radio('payroll_include_leave_summary', '1', (config('config.payroll_include_leave_summary')) ? 'checked' : '').' '.trans('messages.yes') !!}
											</label>
											<label>
												{!! Form::radio('payroll_include_leave_summary', '0', (!config('config.payroll_include_leave_summary')) ? 'checked' : '').' '.trans('messages.no') !!}
											</label>
										</div>
								  	</div>
			  						{!! Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
								{!! Form::close() !!}
							</div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="mail">
						    <div class="user-profile-content-wm">
								<h2><strong>{!! trans('messages.mail') !!}</strong> {!! trans('messages.configuration') !!}</h2>
								{!! Form::open(['route' => 'configuration.mail-store','role' => 'form', 'class'=>'mail-form','id' => 'mail-form','data-no-form-clear' => 1]) !!}
									@include('configuration._mail')
								{!! Form::close() !!}
						    </div>
						  </div>
						  <!-- <div class="tab-pane animated fadeInRight" id="sms">
						    <div class="user-profile-content-wm">
								<h2><strong>{!! trans('messages.sms') !!}</strong> {!! trans('messages.configuration') !!} (Default Twilio SMS Integrated)</h2>
								{!! Form::open(['route' => 'configuration.sms-store','role' => 'form', 'class'=>'sms-form','id' => 'sms-form','data-no-form-clear' => 1]) !!}
									@include('configuration._sms')
								{!! Form::close() !!}
						    </div>
						  </div> -->
						  <div class="tab-pane animated fadeInRight" id="job">
						    <div class="user-profile-content-wm">
								<h2><strong>{!! trans('messages.job') !!}</strong> {!! trans('messages.configuration') !!}</h2>
								{!! Form::open(['route' => 'configuration.store','role' => 'form', 'class'=>'job-configuration-form','id' => 'job-configuration-form','data-no-form-clear' => 1]) !!}
									@include('configuration._job')
								{!! Form::close() !!}
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="role">
						    <div class="user-profile-content-wm">
								
								<div class="col-sm-4">
									<div class="box-info">
										<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.role') !!} </h2>
										{!! Form::open(['route' => 'role.store','role' => 'form', 'class'=>'role-form','id' => 'role-form','data-table-alter' => 'role-table']) !!}
											@include('role._form')
										{!! Form::close() !!}
									</div>
								</div>
								<div class="col-sm-8">
									<div class="box-info full">
										<h2><strong>{!! trans('messages.list_all').'</strong> '.trans('messages.role') !!} </h2>
										<div class="table-responsive">
											<table data-sortable class="table table-hover table-striped table-ajax-load" id="role-table" data-source="/role/lists">
												<thead>
													<tr>
														<th>{!! trans('messages.role_name') !!}</th>
														<th data-sortable="false">{!! trans('messages.option') !!}</th>
													</tr>
												</thead>
												<tbody>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="box-info">
										{!! Form::open(['route' => 'configuration.store','role' => 'form', 'class'=>'configuraiton-role-form','id' => 'configuraiton-role-form','data-no-form-clear' => 1]) !!}
										
										  <div class="form-group">
											{!! Form::label('subordinate',trans('messages.subordinate'),['class' => ' control-label'])!!}
											<div class="radio">
												<label>
													{!! Form::radio('subordinate', '1', (config('config.subordinate')) ? 'checked' : '') !!} Show All Subordinate
												</label>
												<label>
													{!! Form::radio('subordinate', '0', (!config('config.subordinate')) ? 'checked' : '') !!} Show Direct Subordinate
												</label>
											</div>
										  </div>
										  <div class="form-group">
											{!! Form::label('employee_manage_own_contact',trans('messages.employee_manage_own_contact'),['class' => ' control-label'])!!}
											<div class="radio">
												<label>
													{!! Form::radio('employee_manage_own_contact', '1', (config('config.employee_manage_own_contact')) ? 'checked' : '').' '.trans('messages.yes') !!}
												</label>
												<label>
													{!! Form::radio('employee_manage_own_contact', '0', (!config('config.employee_manage_own_contact')) ? 'checked' : '').' '.trans('messages.no') !!}
												</label>
											</div>
										  </div>
										  <div class="form-group">
											{!! Form::label('employee_manage_own_bank_account',trans('messages.employee_manage_own_bank_account'),['class' => ' control-label'])!!}
											<div class="radio">
												<label>
													{!! Form::radio('employee_manage_own_bank_account', '1', (config('config.employee_manage_own_bank_account')) ? 'checked' : '').' '.trans('messages.yes') !!}
												</label>
												<label>
													{!! Form::radio('employee_manage_own_bank_account', '0', (!config('config.employee_manage_own_bank_account')) ? 'checked' : '').' '.trans('messages.no') !!}
												</label>
											</div>
										  </div>
										  <div class="form-group">
											{!! Form::label('employee_manage_own_qualification',trans('messages.employee_manage_own_qualification'),['class' => ' control-label'])!!}
											<div class="radio">
												<label>
													{!! Form::radio('employee_manage_own_qualification', '1', (config('config.employee_manage_own_qualification')) ? 'checked' : '').' '.trans('messages.yes') !!}
												</label>
												<label>
													{!! Form::radio('employee_manage_own_qualification', '0', (!config('config.employee_manage_own_qualification')) ? 'checked' : '').' '.trans('messages.no') !!}
												</label>
											</div>
										  </div>
										  <div class="form-group">
											{!! Form::label('employee_manage_own_work_experience',trans('messages.employee_manage_own_work_experience'),['class' => ' control-label'])!!}
											<div class="radio">
												<label>
													{!! Form::radio('employee_manage_own_work_experience', '1', (config('config.employee_manage_own_work_experience')) ? 'checked' : '').' '.trans('messages.yes') !!}
												</label>
												<label>
													{!! Form::radio('employee_manage_own_work_experience', '0', (!config('config.employee_manage_own_work_experience')) ? 'checked' : '').' '.trans('messages.no') !!}
												</label>
											</div>
										  </div>
										  <div class="form-group">
											{!! Form::label('employee_manage_own_document',trans('messages.employee_manage_own_document'),['class' => ' control-label'])!!}
											<div class="radio">
												<label>
													{!! Form::radio('employee_manage_own_document', '1', (config('config.employee_manage_own_document')) ? 'checked' : '').' '.trans('messages.yes') !!}
												</label>
												<label>
													{!! Form::radio('employee_manage_own_document', '0', (!config('config.employee_manage_own_document')) ? 'checked' : '').' '.trans('messages.no') !!}
												</label>
											</div>
										  </div>
			  								{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
										{!! Form::close() !!}
									</div>
								</div>
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="office-shift">
						    <div class="user-profile-content-wm">
								<h2><strong>{!! trans('messages.office').'</strong> '.trans('messages.shift') !!}</h2>
								<div class="row">
									<div class="col-sm-12">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.office_shift') !!}</h2>
											{!! Form::open(['route' => 'office-shift.store','role' => 'form', 'class'=>'office-shift-form','id' => 'office-shift-form','data-table-alter' => 'office-shift-table']) !!}
												@include('office_shift._form')
											{!! Form::close() !!}
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.office_shift') !!}</h2>
											<div class="table-responsive">
												<table data-sortable class="table table-hover table-striped table-ajax-load"  id="office-shift-table" data-source="/office-shift/lists">
													<thead>
														<tr>
															<th>{!! trans('messages.day') !!}</th>
															<th>{!! trans('messages.option') !!}</th>
															@foreach(config('lists.week') as $key => $day)
															<th>{!! trans('messages.'.$key) !!}</th>
															@endforeach
														</tr>
													</thead>
													<tbody>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="award">
						    <div class="user-profile-content-wm">
								<div class="row">
									<div class="col-sm-6">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.award_type') !!}</h2>
											{!! Form::open(['route' => 'award-type.store','role' => 'form', 'class'=>'award-type-form','id' => 'award-type-form','data-table-alter' => 'award-type-table']) !!}
												@include('award_type._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-6">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.award_type') !!}</h2>
												<div class="table-responsive">
													<table data-sortable class="table table-hover table-striped table-ajax-load"  id="award-type-table" data-source="/award-type/lists">
														<thead>
															<tr>
																<th>{!! trans('messages.award_type') !!}</th>
																<th data-sortable="false">{!! trans('messages.option') !!}</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
										</div>
									</div>
								</div>
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="qualification">
						    <div class="user-profile-content-wm">
								<div class="row">
									<div class="col-sm-6">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.education_level') !!}</h2>
											{!! Form::open(['route' => 'education-level.store','role' => 'form', 'class'=>'education-level-form','id' => 'education-level-form','data-table-alter' => 'education-level-table']) !!}
												@include('education_level._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-6">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.education_level') !!}</h2>
												<div class="table-responsive">
													<table data-sortable class="table table-hover table-striped table-ajax-load"  id="education-level-table" data-source="/education-level/lists">
														<thead>
															<tr>
																<th>{!! trans('messages.education_level') !!}</th>
																<th data-sortable="false">{!! trans('messages.option') !!}</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.language') !!}</h2>
											{!! Form::open(['route' => 'qualification-language.store','role' => 'form', 'class'=>'qualification-language-form','id' => 'qualification-language-form','data-table-alter' => 'qualification-language-table']) !!}
												@include('qualification_language._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-6">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.language') !!}</h2>
												<div class="table-responsive">
													<table data-sortable class="table table-hover table-striped table-ajax-load"  id="qualification-language-table" data-source="/qualification-language/lists">
														<thead>
															<tr>
																<th>{!! trans('messages.language') !!}</th>
																<th data-sortable="false">{!! trans('messages.option') !!}</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.skill') !!}</h2>
											{!! Form::open(['route' => 'qualification-skill.store','role' => 'form', 'class'=>'qualification-skill-form','id' => 'qualification-skill-form','data-table-alter' => 'qualification-skill-table']) !!}
												@include('qualification_skill._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-6">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.skill') !!}</h2>
												<div class="table-responsive">
													<table data-sortable class="table table-hover table-striped table-ajax-load"  id="qualification-skill-table" data-source="/qualification-skill/lists">
														<thead>
															<tr>
																<th>{!! trans('messages.skill') !!}</th>
																<th data-sortable="false">{!! trans('messages.option') !!}</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
										</div>
									</div>
								</div>
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="contract-type">
						    <div class="user-profile-content-wm">
								<div class="row">
									<div class="col-sm-6">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.contract_type') !!}</h2>
											{!! Form::open(['route' => 'contract-type.store','role' => 'form', 'class'=>'contract-type-form','id' => 'contract-type-form','data-table-alter' => 'contract-type-table']) !!}
												@include('contract_type._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-6">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.contract_type') !!}</h2>
												<div class="table-responsive">
													<table data-sortable class="table table-hover table-striped table-ajax-load" id="contract-type-table" data-source="/contract-type/lists">
														<thead>
															<tr>
																<th>{!! trans('messages.contract_type') !!}</th>
																<th data-sortable="false">{!! trans('messages.option') !!}</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
										</div>
									</div>
								</div>
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="leave">
						    <div class="user-profile-content-wm">
								<div class="row">
									<div class="col-sm-6">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.leave_type') !!}</h2>
											{!! Form::open(['route' => 'leave-type.store','role' => 'form', 'class'=>'leave-type-form','id' => 'leave-type-form','data-table-alter' => 'leave-type-table']) !!}
												@include('leave_type._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-6">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.leave_type') !!}</h2>
												<div class="table-responsive">
													<table data-sortable class="table table-hover table-striped table-ajax-load"  id="leave-type-table" data-source="/leave-type/lists">
														<thead>
															<tr>
																<th>{!! trans('messages.leave_type') !!}</th>
																<th data-sortable="false">{!! trans('messages.option') !!}</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="box-info">
											<h2><strong>{!! trans('messages.leave') !!}</strong> {!! trans('messages.approval') !!} </h2>
											{!! Form::open(['route' => 'configuration.store','role' => 'form', 'class'=>'configuraiton-leave-approval-form','id' => 'configuraiton-leave-approval-form','data-no-form-clear' => 1]) !!}
											
											  <div class="form-group">
												{!! Form::label('leave_approval_level',trans('messages.leave_approval_level'),['class' => ' control-label'])!!}
												<div class="radio">
													<label>
														{!! Form::radio('leave_approval_level', 'single', (config('config.leave_approval_level') == 'single') ? 'checked' : '').' '.trans('messages.single').' '.trans('messages.level') !!}
													</label>
												</div>
												<div class="radio">
													<label>
														<div class="row">
															<div class="col-md-6">
																{!! Form::radio('leave_approval_level', 'multiple', (config('config.leave_approval_level') == 'multiple') ? 'checked' : '').' '.trans('messages.multiple').' '.trans('messages.level') !!}
															</div>
															<div class="col-md-6">
																<input type="number" name="leave_approval_level_multiple" class="form-control" value="{{config('config.leave_approval_level_multiple')}}" placeholder="{{trans('messages.no_of_level')}}">
															</div>
														</div>
													</label>
												</div>
												<div class="radio">
													<label>
														{!! Form::radio('leave_approval_level', 'last', (config('config.leave_approval_level') == 'last') ? 'checked' : '').' '.trans('messages.last').' '.trans('messages.level') !!}
													</label>
												</div>
												<div class="radio">
													<label>
														<div class="row">
															<div class="col-md-6">
																{!! Form::radio('leave_approval_level', 'designation', (config('config.leave_approval_level') == 'designation') ? 'checked' : '').' '.trans('messages.designation') !!}
															</div>
															<div class="col-md-6">
																{!! Form::select('leave_approval_level_detail', [''=>''] + $designations,config('config.leave_approval_level_detail'),['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
															</div>
														</div>
													</label>
												</div>
											  </div>
				  								{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
											{!! Form::close() !!}
										</div>
									</div>
								</div>
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="message">
						    <div class="user-profile-content-wm">
								<div class="row">
									<div class="col-sm-6">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.category') !!}</h2>
											{!! Form::open(['route' => 'message-category.store','role' => 'form', 'class'=>'message-category-form','id' => 'message-category-form','data-table-alter' => 'message-category-table']) !!}
												@include('message_category._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-6">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.category') !!}</h2>
												<div class="table-responsive">
													<table data-sortable class="table table-hover table-striped table-ajax-load"  id="message-category-table" data-source="/message-category/lists">
														<thead>
															<tr>
																<th>{!! trans('messages.category') !!}</th>
																<th data-sortable="false">{!! trans('messages.option') !!}</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.priority') !!}</h2>
											{!! Form::open(['route' => 'message-priority.store','role' => 'form', 'class'=>'message-priority-form','id' => 'message-priority-form','data-table-alter' => 'message-priority-table']) !!}
												@include('message_priority._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-6">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.priority') !!}</h2>
												<div class="table-responsive">
													<table data-sortable class="table table-hover table-striped table-ajax-load"  id="message-priority-table" data-source="/message-priority/lists">
														<thead>
															<tr>
																<th>{!! trans('messages.priority') !!}</th>
																<th data-sortable="false">{!! trans('messages.option') !!}</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
										</div>
									</div>
								</div>
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="document">
						    <div class="user-profile-content-wm">
								<div class="row">
									<div class="col-sm-6">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.document_type') !!}</h2>
											{!! Form::open(['route' => 'document-type.store','role' => 'form', 'class'=>'document-type-form','id' => 'document-type-form','data-table-alter' => 'document-type-table']) !!}
												@include('document_type._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-6">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.document_type') !!}</h2>
												<div class="table-responsive">
													<table data-sortable class="table table-hover table-striped table-ajax-load"  id="document-type-table" data-source="/document-type/lists">
														<thead>
															<tr>
																<th>{!! trans('messages.document_type') !!}</th>
																<th data-sortable="false">{!! trans('messages.option') !!}</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
										</div>
									</div>
								</div>
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="salary">
						    <div class="user-profile-content-wm">
								<div class="row">
									<div class="col-sm-6">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.salary_type') !!}</h2>
											{!! Form::open(['route' => 'salary-type.store','role' => 'form', 'class'=>'salary-type-form','id' => 'salary-type-form','data-table-alter' => 'salary-type-table']) !!}
												@include('salary_type._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-6">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.salary_type') !!}</h2>
												<div class="table-responsive">
													<table data-sortable class="table table-hover table-striped table-ajax-load"  id="salary-type-table" data-source="/salary-type/lists">
														<thead>
															<tr>
																<th>{!! trans('messages.salary_type') !!}</th>
																<th>{!! trans('messages.type') !!}</th>
																<th data-sortable="false">{!! trans('messages.option') !!}</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
										</div>
									</div>
								</div>
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="expense">
						    <div class="user-profile-content-wm">
								<div class="row">
									<div class="col-sm-6">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.expense_head') !!}</h2>
											{!! Form::open(['route' => 'expense-head.store','role' => 'form', 'class'=>'expense-head-form','id' => 'expense-head-form','data-table-alter' => 'expense-head-table']) !!}
												@include('expense_head._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-6">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.expense_head') !!}</h2>
												<div class="table-responsive">
													<table data-sortable class="table table-hover table-striped table-ajax-load"  id="expense-head-table" data-source="/expense-head/lists">
														<thead>
															<tr>
																<th>{!! trans('messages.expense_head') !!}</th>
																<th data-sortable="false">{!! trans('messages.option') !!}</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="box-info">
											<h2><strong>{!! trans('messages.expense') !!}</strong> {!! trans('messages.approval') !!} </h2>
											{!! Form::open(['route' => 'configuration.store','role' => 'form', 'class'=>'configuraiton-expense-approval-form','id' => 'configuraiton-expense-approval-form','data-no-form-clear' => 1]) !!}
											
											  <div class="form-group">
												{!! Form::label('expense_approval_level',trans('messages.expense_approval_level'),['class' => ' control-label'])!!}
												<div class="radio">
													<label>
														{!! Form::radio('expense_approval_level', 'single', (config('config.expenseapproval_level') == 'single') ? 'checked' : '').' '.trans('messages.single').' '.trans('messages.level') !!}
													</label>
												</div>
												<div class="radio">
													<label>
														<div class="row">
															<div class="col-md-6">
																{!! Form::radio('expense_approval_level', 'multiple', (config('config.expense_approval_level') == 'multiple') ? 'checked' : '').' '.trans('messages.multiple').' '.trans('messages.level') !!}
															</div>
															<div class="col-md-6">
																<input type="number" name="expense_approval_level_multiple" class="form-control" value="{{config('config.expense_approval_level_multiple')}}" placeholder="{{trans('messages.no_of_level')}}">
															</div>
														</div>
													</label>
												</div>
												<div class="radio">
													<label>
														{!! Form::radio('expense_approval_level', 'last', (config('config.expense_approval_level') == 'last') ? 'checked' : '').' '.trans('messages.last').' '.trans('messages.level') !!}
													</label>
												</div>
												<div class="radio">
													<label>
														<div class="row">
															<div class="col-md-6">
																{!! Form::radio('expense_approval_level', 'designation', (config('config.expense_approval_level') == 'designation') ? 'checked' : '').' '.trans('messages.designation') !!}
															</div>
															<div class="col-md-6">
																{!! Form::select('expense_approval_level_detail', [''=>''] + $designations,config('config.expense_approval_level_detail'),['class'=>'form-control input-xlarge select2me','placeholder'=>trans('messages.select_one')])!!}
															</div>
														</div>
													</label>
												</div>
											  </div>
				  								{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
											{!! Form::close() !!}
										</div>
									</div>
								</div>
						    </div>
						  </div>
						  <div class="tab-pane animated fadeInRight" id="api">
						    <div class="user-profile-content-wm">
								<div class="row">
									<div class="col-sm-12">
										<div class="box-info">
											<h2><strong>{!! trans('messages.api').'</strong> '.trans('messages.configuration') !!}</h2>
											{!! Form::open(['route' => 'configuration.api','role' => 'form', 'class'=>'api-configuration-form','id'=>'api-configuration-form','data-no-form-clear' => 1]) !!}
												<div class="auth_token" id="auth_token" style="margin:20px 0px;">
												    {!! (Auth::user()->auth_token) ? Auth::user()->auth_token : '' !!}
												</div>
													{!! Form::hidden('config_type','api')!!}
													{!! Form::submit((Auth::user()->auth_token) ? trans('messages.regenerate_token') : trans('messages.generate_token'),['class' => 'btn btn-primary']) !!}
											{!! Form::close() !!}
										</div>
									</div>
								</div>
							</div>
						   </div>
						  <div class="tab-pane animated fadeInRight" id="ip">
						    <div class="user-profile-content-wm">
								<div class="row">
									<div class="col-sm-6">
										<div class="box-info">
											<h2><strong>{!! trans('messages.add_new') !!}</strong> {!! trans('messages.ip_restriction') !!}</h2>
											{!! Form::open(['route' => 'ip.store','role' => 'form', 'class'=>'ip-form','id' => 'ip-form','data-table-alter' => 'ip-table']) !!}
												@include('ip._form')
											{!! Form::close() !!}
										</div>
									</div>
									<div class="col-sm-6">
										<div class="box-info full">
											<h2><strong>{!! trans('messages.list_all') !!}</strong> {!! trans('messages.ip_restriction') !!}</h2>
											<div class="table-responsive">
												<table data-sortable class="table table-hover table-striped table-ajax-load"  id="ip-table" data-source="/ip/lists">
													<thead>
														<tr>
															<th>{{ trans('messages.start') }}</th>
															<th>{{ trans('messages.end') }}</th>
															<th data-sortable="false">{!! trans('messages.option') !!}</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
						    </div>
						   </div>
						</div>
					</div>
				</div>
			</div>
		</div>
				
	@stop