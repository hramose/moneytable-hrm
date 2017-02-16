
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
							<?php if(!config('code.mode')): ?>
							<li><a href="#" data-href="/whats-new" data-toggle="modal" data-target="#myModal"><strong>Whats New in 3.1?</strong></a></li>
							<?php endif; ?>
							<li>
								<a href="#" data-href="/todo" data-toggle='modal' data-target='#myModal' id="to-do-modal"><i class="fa fa-list-ul fa-lg" data-toggle="tooltip" title="<?php echo trans('messages.to_do'); ?>" data-placement="bottom"></i></a>
							</li>
							<?php if(config('config.multilingual')): ?>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-language fa-lg icon" data-toggle="tooltip" title="<?php echo trans('messages.language'); ?>" data-placement="bottom"></i> </a>
								<ul class="dropdown-menu animated half flipInX">
									<li class="active"><a href="#" style="color:white;cursor:default;"><?php echo config('lang.'.$default_language.'.language').' ('.$default_language.')'; ?></a></li>
									<?php if(Entrust::can('change_language')): ?>
									<?php foreach(config('lang') as $key => $language): ?>
										<?php if($default_language != $key): ?>
										<li><a href="/set-language/<?php echo e($key); ?>"><?php echo $language['language']." (".$key.")"; ?></a></li>
										<?php endif; ?>
									<?php endforeach; ?>
									<?php endif; ?>
								</ul>
							</li>
							<?php endif; ?>

							<?php if(defaultRole() || Entrust::can('manage_configuration') || Entrust::can('manage_custom_field') || Entrust::can('manage_template') ||
							Entrust::can('list_department') ||
							Entrust::can('list_location') || Entrust::can('list_designation') || Entrust::can('manage_email_log') || Entrust::can('manage_backup') || Entrust::can('upload_attendance')
							): ?>
								<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog fa-lg" data-toggle="tooltip" title="<?php echo trans('messages.configuration'); ?>" data-placement="bottom"></i></a>
									<ul class="dropdown-menu animated half flipInX">
									<?php if(defaultRole() || Entrust::can('manage_configuration')): ?>
											<li><a href="/configuration"><?php echo trans('messages.configuration'); ?></a></li>
									<?php endif; ?>
									<?php if(defaultRole() || Entrust::can('manage_configuration')): ?>
										<li><a href="/permission"><?php echo trans('messages.permission'); ?></a></li>
									<?php endif; ?>
									<?php if(Entrust::can('list_location')): ?>
										<li><a href="/location"><?php echo trans('messages.location'); ?></a></li>
									<?php endif; ?>
									<?php if(Entrust::can('list_department')): ?>
										<li><a href="/department"><?php echo trans('messages.department'); ?></a></li>
									<?php endif; ?>
									<?php if(Entrust::can('list_designation')): ?>
										<li><a href="/designation"><?php echo trans('messages.designation'); ?></a></li>
									<?php endif; ?>
									<?php if(Entrust::can('manage_custom_field')): ?>
										<li><a href="/custom-field"><?php echo trans('messages.custom_field'); ?></a></li>
									<?php endif; ?>
									<?php if(Entrust::can('manage_language') && config('config.multilingual')): ?>
									<li><a href="/language"><?php echo trans('messages.language'); ?></a></li>
									<?php endif; ?>
									<?php if(Entrust::can('manage_template')): ?>
									<li><a href="/template"><?php echo trans('messages.email_template'); ?></a></li>
									<?php endif; ?>
									<?php if(Entrust::can('manage_email_log')): ?>
									<li><a href="/email"><?php echo trans('messages.email').' '.trans('messages.log'); ?></a></li>
									<?php endif; ?>
									<?php if(Entrust::can('manage_backup')): ?>
									<li><a href="/backup"><?php echo trans('messages.backup').' '.trans('messages.log'); ?></a></li>
									<?php endif; ?>
									<?php if(Entrust::can('upload_attendance')): ?>
									<li><a href="/attendance-upload-log"><?php echo trans('messages.attendance').' '.trans('messages.upload').' '.trans('messages.log'); ?></a></li>
									<?php endif; ?>
									</ul>
								</li>
							<?php endif; ?>
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope fa-lg" data-toggle="tooltip" title="<?php echo trans('messages.message'); ?>" data-placement="bottom"></i>
										<?php echo (count($header_inbox)) ? '<span class="label label-danger absolute">'.count($header_inbox).'</span>' : ''; ?>

									</a>
									<ul class="dropdown-menu dropdown-message animated half flipInX">
										<?php if(!count($header_inbox)): ?>
											<li class="dropdown-header notif-header">
												<?php echo trans('messages.no_unread_message'); ?>

											</li>
										<?php else: ?>
											<li class="dropdown-header notif-header"><?php echo trans('messages.new_message'); ?></li>
											<?php foreach($header_inbox->take(5) as $inbox): ?>
											<li class="unread">
												<a href="/message/view/<?php echo e($inbox->id.'/'.$token); ?>">
													<?php echo \App\Classes\Helper::getAvatar($inbox->UserFrom->id); ?>

													<div style="margin-left:75px;">
														<strong><?php echo $inbox->UserFrom->full_name; ?></strong><br />
														<p><?php echo $inbox->UserFrom->Designation->full_designation; ?>

														<br />
														<i><?php echo showDateTime($inbox->created_at); ?></i>
														</p>
													</div>
												</a>
											</li>
											<?php endforeach; ?>
										<?php endif; ?>

										<?php if(count($header_inbox) > count($header_inbox->take(5))): ?>
										<li class="dropdown-footer">
											<a href="/message">
												<i class="fa fa-arrow-circle-right"></i> <?php echo trans('messages.see_all'); ?>

											</a>
										</li>
										<?php endif; ?>
									</ul>
								</li>

								<?php if(count($header_leave)): ?>
									<li class="dropdown">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-globe fa-lg" data-toggle="tooltip" title="<?php echo trans('messages.notification'); ?>" data-placement="bottom"></i>
											<?php echo (count($header_leave)) ? '<span class="label label-danger absolute">'.count($header_leave).'</span>' : ''; ?>

										</a>
										<ul class="dropdown-menu dropdown-message animated half flipInX">
											<?php if(!count($header_leave)): ?>
											<li class="dropdown-header notif-header">
												<?php echo trans('messages.no_pending_leave'); ?>

											</li>
											<?php else: ?>
												<li class="dropdown-header notif-header"><?php echo trans('messages.new_leave'); ?></li>
												<?php foreach($header_leave->take(5) as $leave_status_detail): ?>
												<li class="unread">
												<a href="/leave/<?php echo e($leave_status_detail->Leave->id); ?>">
														<?php echo \App\Classes\Helper::getAvatar($leave_status_detail->Leave->user_id); ?>

														<div style="margin-left:75px;">
														<strong><?php echo $leave_status_detail->Leave->User->full_name; ?></strong><br />
														<p><i><?php echo showDateTime($leave_status_detail->Leave->created_at); ?></i><br />
														<?php echo $leave_status_detail->Leave->LeaveType->name.' 
														from '.showDate($leave_status_detail->Leave->from_date).' 
														to '.showDate($leave_status_detail->Leave->to_date); ?></p>
														</div>
													</a>
												</li>
												<?php endforeach; ?>
											<?php endif; ?>

											<?php if(count($header_leave) > count($header_leave->take(5))): ?>
											<li class="dropdown-footer">
												<a href="/leave">
													<i class="fa fa-arrow-circle-right"></i> <?php echo trans('messages.see_all'); ?>

												</a>
											</li>
											<?php endif; ?>
										</ul>
									</li>
								<?php endif; ?>
								<li class="dropdown" id="user-profile">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">
										<i class="fa fa-user fa-lg" data-toggle="tooltip" title="<?php echo trans('messages.profile'); ?>" data-placement="bottom"></i>
									</a>
									<ul class="dropdown-menu animated half flipInX">
										<li><a href="/profile"><?php echo trans('messages.my').' '.trans('messages.profile'); ?></a></li>
										<li><a href="#" data-href="/change-password" data-toggle='modal' data-target='#myModal'><?php echo trans('messages.change_password'); ?></a></li>
										<?php if(config('code.mode') && defaultRole()): ?>
										<li><a href="#" data-href="/check-update" data-toggle='modal' data-target='#myModal'><?php echo trans('messages.check').' '.trans('messages.update'); ?></a></li>
										<li><a href="/release-license"><?php echo trans('messages.release_license'); ?></a></li>
										<li><a href="#" data-href="/whats-new" data-toggle="modal" data-target="#myModal"><strong>Whats New in 3.1?</strong></a></li>
										<?php endif; ?>
										<li><a href="/logout"><?php echo trans('messages.logout'); ?></a></li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
				</div>
            </div>
				