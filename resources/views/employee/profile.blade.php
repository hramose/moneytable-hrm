@extends('layouts.default')

	@section('breadcrumb')
		<ul class="breadcrumb">
		    <li><a href="/dashboard">{!! trans('messages.dashboard') !!}</a></li>
		    <li><a href="/employee">{!! trans('messages.employee') !!}</a></li>
		    <li class="active">{!! $user->full_name_with_designation !!}</li>
		</ul>
	@stop
	
	@section('content')
		<div class="row">
			<div class="col-sm-4">
				<div class="box-info full">
					<br />
					<div class="media box-padding">
						<div class="media-body">
							<a class="pull-right" href="#">
								{!! \App\Classes\Helper::getAvatar($user->id) !!}
							</a>
							<h4 class="media-heading"><strong>{!! $user->full_name!!} </strong></h4>
							<h6>{!! $user->Designation->full_designation !!}</h6>
							<div class="clear"></div>
							{!! trans('messages.last_login') !!} <strong>{!! ((showDateTime($user->last_login)) ? : trans('messages.na')).'</strong><br />'.trans('messages.from').' <strong>'.(($user->last_login_ip) ? : trans('messages.na')) !!}</strong></span>
						</div>
					</div>
					<div id="social">
						<a href="{{ ($user->Profile->facebook_link) ? : '#' }}" target="_blank" data-toggle="tooltip" title="Facebook">
							<span class="fa-stack fa-lg">
							  <i class="fa fa-circle facebook fa-stack-2x"></i>
							  <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
							</span>
						</a>
						<a href="{{ ($user->Profile->twitter_link) ? : '#' }}" target="_blank" data-toggle="tooltip" title="Twitter">
							<span class="fa-stack fa-lg">
							  <i class="fa fa-circle twitter fa-stack-2x"></i>
							  <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
							</span>
						</a>
						<a href="{{ ($user->Profile->googleplus_link) ? : '#' }}" target="_blank" data-toggle="tooltip" title="Google Plus">
							<span class="fa-stack fa-lg">
							  <i class="fa fa-circle gplus fa-stack-2x"></i>
							  <i class="fa fa-google-plus fa-stack-1x fa-inverse"></i>
							</span>
						</a>
						<a href="{{ ($user->Profile->blogger_link) ? : '#' }}" target="_blank" data-toggle="tooltip" title="Blogger">
							<span class="fa-stack fa-lg">
							  <i class="fa fa-circle blogger fa-stack-2x"></i>
							  <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
							</span>
						</a>
						<a href="{{ ($user->Profile->linkedin_link) ? : '#' }}" target="_blank" data-toggle="tooltip" title="Linkedin">
							<span class="fa-stack fa-lg">
							  <i class="fa fa-circle linkedin fa-stack-2x"></i>
							  <i class="fa fa-linkedin fa-stack-1x fa-inverse"></i>
							</span>
						</a>
					</div>
					@if($user->id == Auth::user()->id)
					<div class="user-button box-padding">
						<div class="row">
							<div class="col-md-6">
								<a href="/message" class="btn btn-primary btn-sm btn-block"><i class="fa fa-envelope"></i> {{ trans('messages.go_to_inbox') }}</a>
							</div>
							<div class="col-md-6">
								<a href="#" data-href="/change-password" data-toggle="modal" data-target="#myModal" class="btn btn-default btn-sm btn-block"><i class="fa fa-key"></i> {{ trans('messages.change_password') }}</a>
							</div>
						</div>
					</div>
					@endif

					<div class="table-responsive">
						<table class="table show-table table-hover table-striped table-condensed">
							<tbody>
								<tr>
									<td>{{ trans('messages.employee_code') }}</td>
									<td>{{ $user->Profile->employee_code }}</td>
								</tr>
								<tr>
									<td>{{ trans('messages.primary').' '.trans('messages.email') }}</td>
									<td>{{ $user->email }}</td>
								</tr>
								<tr>
									<td>{{ trans('messages.primary').' '.trans('messages.contact_number') }}</td>
									<td>{{ $user->Profile->contact_number }}</td>
								</tr>
								<tr>
									<td>{{ trans('messages.gender') }}</td>
									<td>{{ ($user->Profile->gender) ? trans('messages.'.$user->Profile->gender) : '' }}</td>
								</tr>
								<tr>
									<td>{{ trans('messages.marital_status') }}</td>
									<td>{{ ($user->Profile->marital_status) ? trans('messages.'.$user->Profile->marital_status) : '' }}</td>
								</tr>
								<tr>
									<td>{{ trans('messages.date_of_birth') }}</td>
									<td>{{ showDate($user->Profile->date_of_birth) }}</td>
								</tr>
								<tr>
									<td>{{ trans('messages.date_of_joining') }}</td>
									<td>{{ showDate($user->Profile->date_of_joining) }}</td>
								</tr>
								<tr>
									<td>{{ trans('messages.date_of_leaving') }}</td>
									<td>{{ showDate($user->Profile->date_of_leaving) }}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="box-info full">
					<h2><i class="fa fa-trophy icon" style="margin-right:5px;"></i> <strong>{{ trans('messages.award').' '.trans('messages.received') }}</strong>
					<span class="badge badge-{{ count($user->award) ? 'success' : 'danger' }} pull-right">{{ count($user->award) }}</span>
					</h2>
					@if(count($user->award))
						<div class="table-responsive">
							<table data-sortable class="table show-table table-hover table-striped ">
								<thead>
									<tr>
										<th>{!! trans('messages.award') !!}</th>
										<th>{!! trans('messages.month_and_year') !!}</th>
									</tr>
								</thead>
								<tbody>
								@foreach($user->award as $award)
									<tr>
										<td>{!! $award->AwardType->name !!}</td>
										<td>{!! trans('messages.'.$award->month).' '.$award->year !!}</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
					@else
						@include('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
					@endif
				</div>
			</div>
			<div class="col-sm-8">
				<div class="box-info full">
					<ul class="nav nav-tabs nav-justified">
					  <li class="active"><a href="#contact" data-toggle="tab"><i class="fa fa-phone"></i> {!! trans('messages.contact') !!}</a></li>
					  <li><a href="#bank-account" data-toggle="tab"><i class="fa fa-laptop"></i> {!! trans('messages.account') !!}</a></li>
					  <li><a href="#qualification" data-toggle="tab"><i class="fa fa-book"></i> {!! trans('messages.qualification') !!}</a></li>
					  <li><a href="#work-experience" data-toggle="tab"><i class="fa fa-hourglass-2"></i> {!! trans('messages.work_experience') !!}</a></li>
					  <li><a href="#document" data-toggle="tab"><i class="fa fa-file"></i> {!! trans('messages.document') !!}</a></li>
					  <li><a href="#contract" data-toggle="tab"><i class="fa fa-pencil"></i> {!! trans('messages.contract') !!}</a></li>
					  <li><a href="#office-shift" data-toggle="tab"><i class="fa fa-clock-o"></i> {!! trans('messages.shift') !!}</a></li>
					</ul>

					<div class="tab-content">
						<div class="tab-pane animated active fadeInRight" id="contact">
							<div class="user-profile-content">
						    	@if(config('config.employee_manage_own_contact'))
									<h2>{!! trans('messages.add_new').' '.trans('messages.contact') !!}</h2>
									{!! Form::model($user,['method' => 'POST','route' => ['contact.store',$user->id] ,'class' => 'employee-contact-form', 'role' => 'form','id' => 'employee-contact-form','data-table-alter' => 'employee-contact-table']) !!}
				    				  	@include('employee._contact_form')
									{!! Form::close() !!}
									<h2>{!! trans('messages.list_all').' '.trans('messages.contact') !!}</h2>
						    	@endif

						    	@if(count($user->Contact))
									<div class="table-responsive">
										<table data-sortable class="table table-hover table-striped table-bordered table-ajax-load"  id="employee-contact-table" data-source="/contact/lists" data-extra="&employee_id={{$user->id}}">
											<thead>
												<tr>
													<th>{!! trans('messages.name') !!}</th>
													<th>{!! trans('messages.relation') !!}</th>
													<th>{!! trans('messages.email') !!}</th>
													<th>{!! trans('messages.mobile') !!}</th>
						    						@if(config('config.employee_manage_own_contact'))
													<th data-sortable="false">{!! trans('messages.option') !!}</th>
													@endif
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
									@else
										@include('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
									@endif
						    </div>
						</div>
						<div class="tab-pane animated fadeInRight" id="bank-account">
						    <div class="user-profile-content">
						    	@if(config('config.employee_manage_own_bank_account'))
									<h2>{!! trans('messages.add_new').' '.trans('messages.bank_account') !!}</h2>
						 			{!! Form::model($user,['method' => 'POST','route' => ['bank-account.store',$user->id] ,'class' => 'bank-account-form','id' => 'bank-account-form','data-table-alter' => 'bank-account-table']) !!}
				    				  	@include('employee._bank_account_form')
									{!! Form::close() !!}

									<div class="clear"></div>
									<h2>{!! trans('messages.list_all').' '.trans('messages.bank_account') !!}</h2>
						    	@endif

						    	@if(count($user->BankAccount))
						    	<div class="table-responsive">
									<table data-sortable class="table table-hover table-striped table-bordered table-ajax-load" id="bank-account-table" data-source="/bank-account/lists" data-extra="&employee_id={{$user->id}}">
										<thead>
											<tr>
												<th>{!! trans('messages.account_name') !!}</th>
												<th>{!! trans('messages.account_number') !!}</th>
												<th>{!! trans('messages.bank_name') !!}</th>
												<th>{!! trans('messages.bank_code') !!}</th>
												<th>{!! trans('messages.branch') !!}</th>
												@if(config('config.employee_manage_own_bank_account'))
												<th data-sortable="false" >{!! trans('messages.option') !!}</th>
												@endif
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
								@else
									@include('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
								@endif
						    </div>
						</div>
						<div class="tab-pane animated fadeInRight" id="qualification">
						    <div class="user-profile-content">
						    	@if(config('config.employee_manage_own_qualification'))
									<h2>{!! trans('messages.add_new').' '.trans('messages.qualification') !!}</h2>
						    		{!! Form::model($user,['method' => 'POST','route' => ['qualification.store',$user->id] ,'class' => 'qualification-form','id' => 'qualification-form','data-table-alter' => 'qualification-table']) !!}
			    				  		@include('employee._qualification_form')
									{!! Form::close() !!}
						    	<div class="clear-fix"></div>
								<h2>{!! trans('messages.list_all').' '.trans('messages.qualification') !!}</h2>
						    	@endif
						    	@if(count($user->Qualification))
							    	<div class="table-responsive">
										<table data-sortable class="table table-hover table-striped table-bordered table-ajax-load" id="qualification-table" data-source="/qualification/lists" data-extra="&employee_id={{$user->id}}">
											<thead>
												<tr>
													<th>{!! trans('messages.institute').' '.trans('messages.name') !!}</th>
													<th>{!! trans('messages.from_year') !!}</th>
													<th>{!! trans('messages.to_year') !!}</th>
													<th>{!! trans('messages.education_level') !!}</th>
													<th>{!! trans('messages.language') !!}</th>
													<th>{!! trans('messages.skill') !!}</th>
													@if(config('config.employee_manage_own_qualification'))
													<th data-sortable="false" >{!! trans('messages.option') !!}</th>
													@endif
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
									@else
										@include('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
									@endif
						    </div>
						</div>
						<div class="tab-pane animated fadeInRight" id="work-experience">
						    <div class="user-profile-content">
						    	@if(config('config.employee_manage_own_work_experience'))
									<h2>{!! trans('messages.add_new').' '.trans('messages.work_experience') !!}</h2>
						    		{!! Form::model($user,['method' => 'POST','route' => ['work-experience.store',$user->id] ,'class' => 'work-experience-form','id' => 'work-experience-form','data-table-alter' => 'work-experience-table']) !!}
			    				  		@include('employee._work_experience_form')
									{!! Form::close() !!}
						    	<div class="clear-fix"></div>
								<h2>{!! trans('messages.list_all').' '.trans('messages.work_experience') !!}</h2>
						    	@endif
						    	@if(count($user->WorkExperience))
							    	<div class="table-responsive">
										<table data-sortable class="table table-hover table-striped table-bordered table-ajax-load" id="work-experience-table" data-source="/work-experience/lists" data-extra="&employee_id={{$user->id}}">
											<thead>
												<tr>
													<th>{!! trans('messages.company_name') !!}</th>
													<th>{!! trans('messages.from_date') !!}</th>
													<th>{!! trans('messages.to_date') !!}</th>
													<th>{!! trans('messages.post') !!}</th>
													<th>{!! trans('messages.description') !!}</th>
													@if(config('config.employee_manage_own_work_experience'))
													<th data-sortable="false" >{!! trans('messages.option') !!}</th>
													@endif
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
									@else
										@include('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
									@endif
						    </div>
						</div>
						<div class="tab-pane animated fadeInRight" id="document">
						    <div class="user-profile-content">
						    	@if(config('config.employee_manage_own_document'))
									<h2>{!! trans('messages.add_new').' '.trans('messages.document') !!}</h2>
						 			{!! Form::model($user,['files'=>true,'method' => 'POST','route' => ['document.store',$user->id] ,'class' => 'document-form','id' => 'document-form','data-table-alter' => 'document-table']) !!}
				    				  	<div class="col-sm-6">
					    				  	<div class="form-group">
											    {!! Form::label('document_type_id',trans('messages.document_type'),[])!!}
												{!! Form::select('document_type_id', [null=>trans('messages.select_one')] + $document_types,'',['class'=>'form-control input-xlarge select2me','id'=>'document_type_id','placeholder'=>trans('messages.select_one')])!!}
												@if(defaultRole() || Entrust::can('manage_configuration'))
													<div class="help-block"><a href="#" data-href="/document-type/create" data-toggle="modal" data-target="#myModal">{!! trans('messages.add_new') !!}</a></div>
												@endif
											</div>
											<div class="form-group">
											    {!! Form::label('title',trans('messages.document').' '.trans('messages.title'))!!}
												{!! Form::input('text','title','',['class'=>'form-control','placeholder'=>trans('messages.document').' '.trans('messages.title')])!!}
											</div>
			  								<div class="form-group">
												<input type="file" name="attachments" id="attachments" class="btn btn-default" title="{!! trans('messages.select').' '.trans('messages.file') !!}">
											</div>
										</div>
										<div class="col-sm-6">	
											<div class="form-group">
											    {!! Form::label('date_of_expiry',trans('messages.date_of_expiry'))!!}
												{!! Form::input('text','date_of_expiry','',['class'=>'form-control datepicker','placeholder'=>trans('messages.date_of_expiry'),'readonly' => 'true'])!!}
											</div>
											<div class="form-group">
											    {!! Form::label('description',trans('messages.description'),[])!!}
											    {!! Form::textarea('description','',['size' => '30x3', 'class' => 'form-control', 'placeholder' => trans('messages.description'),"data-show-counter" => 1,"data-limit" => config('config.textarea_limit'),'data-autoresize' => 1])!!}
											    <span class="countdown"></span>
											</div>
											{!! Form::submit(trans('messages.save'),['class' => 'btn btn-primary pull-right']) !!}
										</div>
									{!! Form::close() !!}

									<div class="clear"></div>
									<h2>{!! trans('messages.list_all').' '.trans('messages.document') !!}</h2>

						    	@endif
						    	@if(count($user->Document))
						    		<div class="table-responsive">
										<table data-sortable  class="table table-hover table-striped table-bordered table-ajax-load"  id="document-table" data-source="/document/lists" data-extra="&employee_id={{$user->id}}">
											<thead>
												<tr>
													<th>{!! trans('messages.document_type') !!}</th>
													<th>{!! trans('messages.title') !!}</th>
													<th>{!! trans('messages.date_of_expiry') !!}</th>
													<th>{!! trans('messages.description') !!}</th>
													<th>{!! trans('messages.status') !!}</th>
						    						@if(config('config.employee_manage_own_document'))
													<th data-sortable="false">{!! trans('messages.option') !!}</th>
													@endif
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								@else
									@include('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
								@endif
						    </div>
						</div>
						<div class="tab-pane animated fadeInRight" id="contract">
						    <div class="user-profile-content">
						    @if(count($user->contract))
								<div class="table-responsive">
									<table data-sortable class="table table-hover table-striped table-bordered">
										<thead>
											<tr>
												<th>{!! trans('messages.duration') !!}</th>
												<th>{!! trans('messages.title') !!}</th>
												<th>{!! trans('messages.contract_type') !!}</th>
											</tr>
										</thead>
										<tbody>
											@foreach($user->contract as $contract_user)
												<tr>
													<td>
														{!! showDate($contract_user->from_date) !!} to
														{!! showDate($contract_user->to_date).' '.
														((date('Y-m-d') >= $contract_user->from_date && date('Y-m-d') <= $contract_user->to_date) ? '<span class="label label-success">'.trans('messages.active').'</span>' : '') !!}
													</td>
													<td>{!! $contract_user->title !!}</td>
													<td>{!! $contract_user->ContractType->name !!}</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								@else
									@include('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
								@endif
						    </div>
						</div>
						<div class="tab-pane animated fadeInRight" id="office-shift">
						    <div class="user-profile-content">
						    @if(count($user->userShift))
								<div class="table-responsive">
									<table data-sortable class="table table-hover table-striped table-bordered">
										<thead>
											<tr>
												<th>{!! trans('messages.date') !!}</th>
												<th>{!! trans('messages.shift') !!}</th>
											</tr>
										</thead>
										<tbody>
											@foreach($user->userShift as $user_shift)
												<tr>
													<td>
														{!! showDate($user_shift->from_date) !!} to
														{!! showDate($user_shift->to_date) !!}
													</td>
													<td>{!! $user_shift->OfficeShift->name !!}</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								@else
								@include('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
								@endif
						    </div>
						</div>
					</div>
				</div>
				@if(\App\Classes\Helper::getContract())
					<div class="box-info full">
						<h2><strong>{{ trans('messages.leave').': '.\App\Classes\Helper::getContract()->full_contract_date }}</strong>
						</h2>
						@if(count($contract))
							<div class="table-responsive">
								<table data-sortable class="table show-table table-hover table-striped ">
									<thead>
										<tr>
											@foreach(\App\LeaveType::all() as $leave_type)
											<th>{!! $leave_type->name !!}</th>
											@endforeach
										</tr>
									</thead>
									<tbody>
										<tr>
										@foreach(\App\LeaveType::all() as $leave_type)
											<td>
												{!! ($contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->count()) ? $contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->first()->leave_used.'/'.$contract->UserLeave->whereLoose('leave_type_id',$leave_type->id)->first()->leave_count : 0 !!}
											</td>
										@endforeach
										</tr>
									</tbody>
								</table>
							</div>
						@else
							@include('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
						@endif
					</div>
					<div class="box-info full">
						<h2><strong>{{ trans('messages.salary').': '.\App\Classes\Helper::getContract()->full_contract_date }}</strong>
						</h2>
						@if(count($contract))
							<div class="table-responsive">
								<table data-sortable class="table show-table table-hover table-striped ">
									<thead>
										<tr>
											@foreach(\App\SalaryType::all() as $salary_type)
											<th>{!! $salary_type->head.' <br />'.(($salary_type->salary_type == 'earning') ? '<span class="badge badge-success">+</span>' : '<span class="badge badge-danger">-</span>') !!}</th>
											@endforeach
										</tr>
									</thead>
									<tbody>
										<tr>
										@foreach(\App\SalaryType::all() as $salary_type)
											<td>
												{!! ($contract->Salary->whereLoose('salary_type_id',$salary_type->id)->count()) ? currency($contract->Salary->whereLoose('salary_type_id',$salary_type->id)->first()->amount) : 0 !!}
											</td>
										@endforeach
										</tr>
									</tbody>
								</table>
							</div>
						@else
							@include('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')])
						@endif
					</div>
				@endif
			</div>
		</div>
	@stop