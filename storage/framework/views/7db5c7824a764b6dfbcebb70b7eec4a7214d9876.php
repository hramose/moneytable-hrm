
							<li <?php echo (in_array('dashboard',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'dashboard'); ?>><a href="<?php echo URL::to('/'); ?>"><i class="fa fa-home icon"></i> <?php echo trans('messages.dashboard'); ?></a></li>
							<?php if(Entrust::can('list_employee')): ?>
							<li <?php echo (in_array('employee',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'employee'); ?>><a href="<?php echo URL::to('/employee'); ?>"><i class="fa fa-users icon"></i> <?php echo trans('messages.employee'); ?></a></li>
							<?php endif; ?>
							<li <?php echo (in_array('attendance',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'attendance'); ?>><a href=""><i class="fa fa-book icon"></i><i class="fa fa-angle-double-down i-right"></i> <?php echo trans('messages.attendance'); ?></a>
								<ul <?php echo (in_array('daily_attendance',$menu) ||
											in_array('date_wise_attendance',$menu) ||
											in_array('date_wise_summary_attendance',$menu) ||
											in_array('shift_detail',$menu) ||
											in_array('update_attendance',$menu)
								) ? 'class="visible"' : ''; ?>>
									<li <?php echo (in_array('daily_attendance',$menu)) ? 'class="active"' : ''; ?> class="no-sort"><a href="<?php echo URL::to('/attendance'); ?>"><i class="fa fa-angle-right"></i> <?php echo trans('messages.daily_attendance'); ?> </a></li>
									<li <?php echo (in_array('date_wise_attendance',$menu)) ? 'class="active"' : ''; ?> class="no-sort"><a href="<?php echo URL::to('/date-wise-attendance'); ?>"><i class="fa fa-angle-right"></i> <?php echo trans('messages.date_wise').' '.trans('messages.attendance'); ?> </a></li>
									<li <?php echo (in_array('date_wise_summary_attendance',$menu)) ? 'class="active"' : ''; ?> class="no-sort"><a href="<?php echo URL::to('/date-wise-summary-attendance'); ?>"><i class="fa fa-angle-right"></i> <small><?php echo trans('messages.date_wise').' '.trans('messages.summary').' '.trans('messages.attendance'); ?></small> </a></li>
									<li <?php echo (in_array('shift_detail',$menu)) ? 'class="active"' : ''; ?> class="no-sort"><a href="<?php echo URL::to('/shift-detail'); ?>"><i class="fa fa-angle-right"></i> <?php echo trans('messages.shift_detail'); ?> </a></li>
									<?php if(Entrust::can('update_attendance')): ?>
									<li <?php echo (in_array('update_attendance',$menu)) ? 'class="active"' : ''; ?> class="no-sort"><a href="<?php echo URL::to('/update-attendance'); ?>"><i class="fa fa-angle-right"></i> <?php echo trans('messages.update_attendance'); ?> </a></li>
									<?php endif; ?>
								</ul>
							</li>
							<?php if(Entrust::can('list_holiday')): ?>
							<li <?php echo (in_array('holiday',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'holiday'); ?>><a href="<?php echo URL::to('/holiday'); ?>"><i class="fa fa-fighter-jet icon"></i> <?php echo trans('messages.holiday'); ?></a></li>
							<?php endif; ?>
							<li <?php echo (in_array('leave',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'leave'); ?>><a href="<?php echo URL::to('/leave'); ?>"><i class="fa fa-coffee icon"></i> <?php echo trans('messages.leave'); ?> 
							<?php if(count($header_leave)): ?>
								<span class="badge badge-danger animated double shake pull-right"><?php echo e(count($header_leave).' '.trans('messages.request')); ?></span>
							<?php endif; ?>
							</a></li>
							<li <?php echo (in_array('payroll',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'payroll'); ?>><a href="<?php echo URL::to('/payroll'); ?>"><i class="fa fa-money icon"></i> <?php echo trans('messages.payroll'); ?></a></li>
							<?php if(Entrust::can('list_announcement')): ?>
							<li <?php echo (in_array('announcement',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'announcement'); ?>><a href="<?php echo URL::to('/announcement'); ?>"><i class="fa fa-list-alt icon"></i> <?php echo trans('messages.announcement'); ?></a></li>
							<?php endif; ?>
							<?php if(Entrust::can('list_award')): ?>
							<li <?php echo (in_array('award',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'award'); ?>><a href="<?php echo URL::to('/award'); ?>"><i class="fa fa-trophy icon"></i> <?php echo trans('messages.award'); ?></a></li>
							<?php endif; ?>
							<?php if(Entrust::can('list_expense')): ?>
							<li <?php echo (in_array('expense',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'expense'); ?>><a href="<?php echo URL::to('/expense'); ?>"><i class="fa fa-credit-card icon"></i> <?php echo trans('messages.expense'); ?>

							<?php if(count($header_expense)): ?>
								<span class="badge badge-danger animated double shake pull-right"><?php echo e(count($header_expense).' '.trans('messages.request')); ?></span>
							<?php endif; ?>
							</a></li>
							<?php endif; ?>
							<?php if(Entrust::can('list_daily_report')): ?>
							<li <?php echo (in_array('daily_report',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'daily_report'); ?>><a href="<?php echo URL::to('/daily-report'); ?>"><i class="fa fa-bars icon"></i> <?php echo trans('messages.daily_report'); ?></a></li>
							<?php endif; ?>
							<?php if(Entrust::can('list_task')): ?>
							<li <?php echo (in_array('task',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'task'); ?>><a href="<?php echo URL::to('/task'); ?>"><i class="fa fa-tasks icon"></i> <?php echo trans('messages.task'); ?></a></li>
							<?php endif; ?>
							<?php if(Entrust::can('list_ticket')): ?>
							<li <?php echo (in_array('ticket',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'ticket'); ?>><a href="<?php echo URL::to('/ticket'); ?>"><i class="fa fa-ticket icon"></i> <?php echo trans('messages.ticket'); ?></a></li>
							<?php endif; ?>
							<?php if(Entrust::can('manage_message')): ?>
							<li <?php echo (in_array('message',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'message'); ?>><a href="<?php echo URL::to('/message'); ?>"><i class="fa fa-envelope icon"></i> <?php echo trans('messages.message'); ?></a></li>
							<?php endif; ?>
							<?php if(Entrust::can('list_job')): ?>
								<?php if(Entrust::can('create_job')): ?>
									<li <?php echo (in_array('list_job',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'job'); ?>><a href="<?php echo URL::to('/job'); ?>"><i class="fa fa-bullhorn icon"></i> <?php echo trans('messages.list_all').' '.trans('messages.job'); ?></a></li>
								<?php endif; ?>
								<li <?php echo (in_array('job_application',$menu)) ? 'class="active"' : ''; ?> <?php echo menuAttr($menus,'job_application'); ?>><a href="<?php echo URL::to('/job-application'); ?>"><i class="fa fa-file-text-o icon"></i> <?php echo trans('messages.job').' '.trans('messages.application'); ?></a></li>
							<?php endif; ?>