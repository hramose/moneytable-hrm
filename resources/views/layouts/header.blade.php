
            <div class="header content rows-content-header">
				<button class="button-menu-mobile show-sidebar">
					<i class="fa fa-bars"></i>
				</button>
				
				<div class="navbar navbar-default flip" role="navigation">
					<div class="container">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
								<i class="fa fa-angle-double-down"></i>
							</button>
						</div>
						
						<div class="navbar-collapse collapse">
							
							<ul class="nav navbar-nav navbar-right top-navbar">
							@if(!config('code.mode'))
							<li><a href="#" data-href="/whats-new" data-toggle="modal" data-target="#myModal"><strong>Whats New in 3.1?</strong></a></li>
							@endif
							<li>
								<a href="#" data-href="/todo" data-toggle='modal' data-target='#myModal' id="to-do-modal"><i class="fa fa-list-ul fa-lg" data-toggle="tooltip" title="{!! trans('messages.to_do') !!}" data-placement="bottom"></i></a>
							</li>
							@if(config('config.multilingual'))
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-language fa-lg icon" data-toggle="tooltip" title="{!! trans('messages.language') !!}" data-placement="bottom"></i> </a>
								<ul class="dropdown-menu animated half flipInX">
									<li class="active"><a href="#" style="color:white;cursor:default;">{!! config('lang.'.$default_language.'.language').' ('.$default_language.')' !!}</a></li>
									@if(Entrust::can('change_language'))
									@foreach(config('lang') as $key => $language)
										@if($default_language != $key)
										<li><a href="/set-language/{{$key}}">{!! $language['language']." (".$key.")" !!}</a></li>
										@endif
									@endforeach
									@endif
								</ul>
							</li>
							@endif

							@if(defaultRole() || Entrust::can('manage_configuration') || Entrust::can('manage_custom_field') || Entrust::can('manage_template') ||
							Entrust::can('list_department') ||
							Entrust::can('list_location') || Entrust::can('list_designation') || Entrust::can('manage_email_log') || Entrust::can('manage_backup') || Entrust::can('upload_attendance')
							)
								<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog fa-lg" data-toggle="tooltip" title="{!! trans('messages.configuration') !!}" data-placement="bottom"></i></a>
									<ul class="dropdown-menu animated half flipInX">
									@if(defaultRole() || Entrust::can('manage_configuration'))
											<li><a href="/configuration">{!! trans('messages.configuration') !!}</a></li>
									@endif
									@if(defaultRole() || Entrust::can('manage_configuration'))
										<li><a href="/permission">{!! trans('messages.permission') !!}</a></li>
									@endif
									@if(Entrust::can('list_location'))
										<li><a href="/location">{!! trans('messages.location') !!}</a></li>
									@endif
									@if(Entrust::can('list_department'))
										<li><a href="/department">{!! trans('messages.department') !!}</a></li>
									@endif
									@if(Entrust::can('list_designation'))
										<li><a href="/designation">{!! trans('messages.designation') !!}</a></li>
									@endif
									@if(Entrust::can('manage_custom_field'))
										<li><a href="/custom-field">{!! trans('messages.custom_field') !!}</a></li>
									@endif
									@if(Entrust::can('manage_language') && config('config.multilingual'))
									<li><a href="/language">{!! trans('messages.language') !!}</a></li>
									@endif
									@if(Entrust::can('manage_template'))
									<li><a href="/template">{!! trans('messages.email_template') !!}</a></li>
									@endif
									@if(Entrust::can('manage_email_log'))
									<li><a href="/email">{!! trans('messages.email').' '.trans('messages.log') !!}</a></li>
									@endif
									@if(Entrust::can('manage_backup'))
									<li><a href="/backup">{!! trans('messages.backup').' '.trans('messages.log') !!}</a></li>
									@endif
									@if(Entrust::can('upload_attendance'))
									<li><a href="/attendance-upload-log">{!! trans('messages.attendance').' '.trans('messages.upload').' '.trans('messages.log') !!}</a></li>
									@endif
									</ul>
								</li>
							@endif
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope fa-lg" data-toggle="tooltip" title="{!! trans('messages.message') !!}" data-placement="bottom"></i>
										{!! (count($header_inbox)) ? '<span class="label label-danger absolute">'.count($header_inbox).'</span>' : '' !!}
									</a>
									<ul class="dropdown-menu dropdown-message animated half flipInX">
										@if(!count($header_inbox))
											<li class="dropdown-header notif-header">
												{!! trans('messages.no_unread_message') !!}
											</li>
										@else
											<li class="dropdown-header notif-header">{!! trans('messages.new_message') !!}</li>
											@foreach($header_inbox->take(5) as $inbox)
											<li class="unread">
												<a href="/message/view/{{ $inbox->id.'/'.$token}}">
													{!! \App\Classes\Helper::getAvatar($inbox->UserFrom->id) !!}
													<div style="margin-left:75px;">
														<strong>{!! $inbox->UserFrom->full_name !!}</strong><br />
														<p>{!! $inbox->UserFrom->Designation->full_designation !!}
														<br />
														<i>{!! showDateTime($inbox->created_at) !!}</i>
														</p>
													</div>
												</a>
											</li>
											@endforeach
										@endif

										@if(count($header_inbox) > count($header_inbox->take(5)))
										<li class="dropdown-footer">
											<a href="/message">
												<i class="fa fa-arrow-circle-right"></i> {!! trans('messages.see_all') !!}
											</a>
										</li>
										@endif
									</ul>
								</li>

								@if(count($header_leave))
									<li class="dropdown">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-globe fa-lg" data-toggle="tooltip" title="{!! trans('messages.notification') !!}" data-placement="bottom"></i>
											{!! (count($header_leave)) ? '<span class="label label-danger absolute">'.count($header_leave).'</span>' : '' !!}
										</a>
										<ul class="dropdown-menu dropdown-message animated half flipInX">
											@if(!count($header_leave))
											<li class="dropdown-header notif-header">
												{!! trans('messages.no_pending_leave') !!}
											</li>
											@else
												<li class="dropdown-header notif-header">{!! trans('messages.new_leave') !!}</li>
												@foreach($header_leave->take(5) as $leave_status_detail)
												<li class="unread">
												<a href="/leave/{{ $leave_status_detail->Leave->id }}">
														{!! \App\Classes\Helper::getAvatar($leave_status_detail->Leave->user_id) !!}
														<div style="margin-left:75px;">
														<strong>{!! $leave_status_detail->Leave->User->full_name !!}</strong><br />
														<p><i>{!! showDateTime($leave_status_detail->Leave->created_at) !!}</i><br />
														{!! $leave_status_detail->Leave->LeaveType->name.' 
														from '.showDate($leave_status_detail->Leave->from_date).' 
														to '.showDate($leave_status_detail->Leave->to_date) !!}</p>
														</div>
													</a>
												</li>
												@endforeach
											@endif

											@if(count($header_leave) > count($header_leave->take(5)))
											<li class="dropdown-footer">
												<a href="/leave">
													<i class="fa fa-arrow-circle-right"></i> {!! trans('messages.see_all') !!}
												</a>
											</li>
											@endif
										</ul>
									</li>
								@endif
								<li class="dropdown" id="user-profile">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">
										<i class="fa fa-user fa-lg" data-toggle="tooltip" title="{!! trans('messages.profile') !!}" data-placement="bottom"></i>
									</a>
									<ul class="dropdown-menu animated half flipInX">
										<li><a href="/profile">{!! trans('messages.my').' '.trans('messages.profile') !!}</a></li>
										<li><a href="#" data-href="/change-password" data-toggle='modal' data-target='#myModal'>{!! trans('messages.change_password') !!}</a></li>
										@if(config('code.mode') && defaultRole())
										<li><a href="#" data-href="/check-update" data-toggle='modal' data-target='#myModal'>{!! trans('messages.check').' '.trans('messages.update') !!}</a></li>
										<li><a href="/release-license">{!! trans('messages.release_license') !!}</a></li>
										<li><a href="#" data-href="/whats-new" data-toggle="modal" data-target="#myModal"><strong>Whats New in 3.1?</strong></a></li>
										@endif
										<li><a href="/logout">{!! trans('messages.logout') !!}</a></li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
				</div>
            </div>
				