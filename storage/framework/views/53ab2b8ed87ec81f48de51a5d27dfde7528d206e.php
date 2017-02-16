

	<?php $__env->startSection('content'); ?>
		<?php if($count_office_shift): ?>
		<div class="row">
			<div class="col-md-4">
				<div class="box-info full">
					<h2><strong><?php echo e(trans('messages.attendance')); ?></strong> </h2>
					<?php if(isset($my_shift)): ?>
					<div class="help-block" style="padding:10px;"><?php echo trans('messages.my').' '.trans('messages.office_shift'); ?> : <strong><?php echo showTime($my_shift->in_time).' to '.showTime($my_shift->out_time); ?></strong></div>
					<?php endif; ?>

					<div class="table-responsive">
						<table class="table table-hover table-striped table-ajax-load" id="clock-table" data-source="/my-clock/lists">
							<thead>
								<tr>
									<th><?php echo trans('messages.clock_in'); ?></th>
									<th><?php echo trans('messages.clock_out'); ?></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>

					<div style="padding:10px;" id="clock-button">
						<?php if($clock_status == 'clock_in'): ?>
							<?php echo Form::open(['route' => 'clock.in','role' => 'form', 'class'=>'form-inline','id' => 'clock-in-form','data-table-alter' => 'clock-table','data-clock-button' => 1]); ?>

								<button type="submit" class="btn btn-success"><?php echo trans('messages.clock_in'); ?></button>
							<?php echo Form::close(); ?>

						<?php elseif($clock_status == 'clock_out'): ?>
							<button class="btn btn-success btn-md"><i class="fa fa-arrow-circle-right"></i> <?php echo trans('messages.you_are_clock_in'); ?>.</button>
							<?php echo Form::open(['route' => 'clock.out','role' => 'form', 'class'=>'form-inline','id' => 'clock-out-form','data-table-alter' => 'clock-table','data-clock-button' => 1]); ?>

								<button type="submit" class="btn btn-danger"><?php echo trans('messages.clock_out'); ?></button>
							<?php echo Form::close(); ?>

						<?php endif; ?>
					</div>
					<div class="clear"></div>
					<br />
					<?php if(Entrust::can('upload_attendance')): ?>
						<div style="padding:10px;">
						<?php echo Form::open(['files' => 'true','route' => 'clock.upload-attendance','role' => 'form', 'class'=>'form-inline upload-attendance-form','id' => 'upload-attendance-form', 'data-submit' => 'noAjax']); ?>

						  <div class="form-group">
							<label class="sr-only" for="file"><?php echo trans('messages.upload_file'); ?></label>
							<input type="file" name="file" id="file" class="btn btn-info" title="<?php echo trans('messages.select').' '.trans('messages.file'); ?>">
						  </div>
						  <?php echo Form::submit(trans('messages.upload'),['class' => 'btn btn-primary']); ?>

						  <div class="help-block"><strong><?php echo trans('messages.note'); ?></strong> <?php echo trans('messages.only_csv_file_allowed'); ?> <br /><a href="/attendance-sample-file" data-toggle="modal" data-target="#myModal">Click here for Sample</a></div>
						  </div>
						<?php echo Form::close(); ?>

					<?php endif; ?>
				</div>
			</div>
			<div class="col-md-4">
				<div class="box-info full">
					<h2><strong><?php echo e(trans('messages.attendance')); ?></strong> <?php echo e(trans('messages.statistics')); ?></h2>
					<div style="padding:0 20px 20px 20px;">
						<?php echo Form::open(['route' => 'clock.list-date-wise-attendance','role' => 'form','class'=>'form-inline','id' => 'date_wise_attendance','data-no-form-clear' => 1,'data-table-alter' => 'attendance-statistics']); ?>

									<div class="form-group">
										<?php echo Form::input('text','from_date',isset($from_date) ? $from_date : date('Y-m-d'),['class'=>'form-control datepicker','placeholder'=>trans('messages.from_date'),'readonly' => 'true','style' => 'width:120px;']); ?>

									</div>
									<div class="form-group">
										<?php echo Form::input('text','to_date',isset($to_date) ? $to_date : date('Y-m-d'),['class'=>'form-control datepicker','placeholder'=>trans('messages.to_date'),'readonly' => 'true','style' => 'width:120px;']); ?>

									</div>
									<?php echo Form::submit(trans('messages.get'),['class' => 'btn btn-primary']); ?>

						<?php echo Form::close(); ?>

					</div>
					<div class="table-responsive">
						<table class="table table-hover table-striped show-table" id="attendance-statistics">
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="box-info">
					<h2><strong><?php echo e(trans('messages.leave')); ?></strong> <?php echo e(trans('messages.statistics')); ?></h2>
					<div id="leave-statistics">
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<?php if(defaultRole()): ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="box-info">
					<h2><strong><?php echo trans('messages.employee'); ?></strong> <?php echo trans('messages.statistics'); ?></h2>
					<div id="website-statistic" class="statistic-chart collapse in">
						<div id="daily-employee-attendance"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<a href="/location"><div class="col-sm-3 col-xs-6">
				<div class="box-info">
					<div class="icon-box">
						<span class="fa-stack">
						  <i class="fa fa-circle fa-stack-2x danger"></i>
						  <i class="fa fa-tasks fa-stack-1x fa-inverse"></i>
						  <!-- <strong class="fa-stack-1x icon-stack">R</strong> -->
						</span>
					</div>
					<div class="text-box">
						<h3><?php echo \App\Location::count(); ?></h3>
						<p><?php echo trans('messages.location'); ?></p>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			</a>
			<a href="/department"><div class="col-sm-3 col-xs-6">
				<div class="box-info">
					<div class="icon-box">
						<span class="fa-stack">
						  <i class="fa fa-circle fa-stack-2x info"></i>
						  <i class="fa fa-sitemap fa-stack-1x fa-inverse"></i>
						</span>
					</div>
					<div class="text-box">
						<h3><?php echo $dept_count; ?></h3>
						<p><?php echo trans('messages.department'); ?></p>
					</div>
					<div class="clear"></div>
				</div>
			</div></a>
			<a href="/employee"><div class="col-sm-3 col-xs-6">
				<div class="box-info">
					<div class="icon-box">
						<span class="fa-stack">
						  <i class="fa fa-circle fa-stack-2x warning"></i>
						  <i class="fa fa-users fa-stack-1x fa-inverse"></i>
						</span>
					</div>
					<div class="text-box">
						<h3><?php echo $user_count; ?></h3>
						<p><?php echo trans('messages.total_employee'); ?></p>
					</div>
					<div class="clear"></div>
				</div>
			</div></a>
			<a href="/attendance"><div class="col-sm-3 col-xs-6">
				<div class="box-info">
					<div class="icon-box">
						<span class="fa-stack">
						  <i class="fa fa-circle fa-stack-2x success"></i>
						  <i class="fa fa-user fa-stack-1x fa-inverse"></i>
						</span>
					</div>
					<div class="text-box">
						<h3><?php echo $leave_count; ?></h3>
						<p><?php echo trans('messages.leave'); ?></p>
					</div>
					<div class="clear"></div>
				</div>
			</div></a>
		</div>
		<?php endif; ?>

		<div class="row">
			<div class="col-sm-6">
				<div class="box-info">
					<h2><strong><?php echo trans('messages.announcement'); ?></strong> </h2>
					<div class="notice-widget" >
					<?php if(count($announcements)): ?>
						<?php foreach($announcements as $announcement): ?>
							<a href="/announcement/<?php echo e($announcement->id); ?>">
							<div class="the-notes info">
								<h4><?php echo $announcement->title; ?></h4>
								<span style="color:green;"><i class="fa fa-clock-o"></i> <?php echo showDateTime($announcement->created_at); ?></span>
								<p class="time pull-right" style="text-align:right;"><?php echo trans('messages.by').' '.$announcement->User->full_name.'<br />'.$announcement->User->Designation->full_designation; ?></p>
							</div>
							</a>
						<?php endforeach; ?>
					<?php else: ?>
						<?php echo $__env->make('common.notification',['type' => 'danger','message' => trans('messages.no_data_found')], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
					<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box-info">
					<h2><strong><?php echo trans('messages.company_hierarchy'); ?></strong></h2>
					<div class="notice-widget" >
						<p class="alert alert-info"><strong><?php echo Auth::user()->full_name.', '.trans('messages.no_of_employee_under_you').' : '.$child_staff_count; ?>

						</strong></p>
						<h4><strong><?php echo trans('messages.you').' : '.Auth::user()->Designation->full_designation; ?>

						</strong></h4>
			   			<?php echo App\Classes\Helper::createLineTreeView($tree,Auth::user()->designation_id); ?>

		   			</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-8">
				<div class="box-info">
					<div id="draw_calendar"></div>
				</div>

			</div>
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong><?php echo e(trans('messages.celebration')); ?></strong></h2>
					<div class="scroll-widget">
						<ul class="media-list">
						<?php foreach($celebrations as $celebration): ?>
						  <li class="media">
							<a class="pull-left" href="#">
							  <?php echo \App\Classes\Helper::getAvatar($celebration['id']); ?>

							</a>
							<div class="media-body">
							  <h4 class="media-heading"><i class="fa fa-<?php echo e($celebration['icon']); ?> icon" style="margin-right:10px;"></i> <small><strong><?php echo e($celebration['title']); ?></strong></small> <a href="#"><?php echo $celebration['name']; ?></a> </h4>
							  <p><strong><?php echo $celebration['number']. ' </strong><br /><small>' .$celebration['designation']; ?></small>
							  </p>
							</div>
						  </li>
						<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong><?php echo trans('messages.recent'); ?></strong> <?php echo trans('messages.activity'); ?></h2>
					<div class="scroll-widget" id="recent-activity">
						
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong><?php echo trans('messages.quick'); ?></strong> <?php echo trans('messages.message'); ?></h2>
					<div class="additional-btn">
						  <a class="additional-icon" id="dropdownMenu5" data-toggle="dropdown">
							<i class="fa fa-cog"></i>
						  </a>
						  <ul class="dropdown-menu pull-right animated half fadeInDown" role="menu" aria-labelledby="dropdownMenu5">
							<li role="presentation"><a role="menuitem" tabindex="-1" href="/message/compose"><?php echo trans('messages.compose'); ?></a></li>
							<li role="presentation"><a role="menuitem" tabindex="-1" href="/message"><?php echo trans('messages.go_to_inbox'); ?></a></li>
							<li role="presentation"><a role="menuitem" tabindex="-1" href="/message/sent"><?php echo trans('messages.go_to_sent_folder'); ?></a></li>
						  </ul>
					</div>
					
					<div id="quick-post" class="collapse in">
						<?php echo Form::open(['route' => 'message.store','role' => 'form', 'class'=>'quick-message-form','id' => 'quick-message-form']); ?>

							<div class="form-group">
								<?php echo Form::select('to_user_id', [null=>trans('messages.select_one')] + $compose_users, '',['class'=>'form-control input-xlarge select2me','placeholder'=> trans('messages.select_one')]); ?>

							</div>
							<div class="form-group">
								<?php echo Form::input('text','subject','',['class'=>'form-control','placeholder'=> trans('messages.subject')]); ?>

							</div>
							<div class="form-group">
								<?php echo Form::textarea('body','',['class' => 'form-control summernote', 'placeholder' => trans('messages.body')]); ?>

							</div>
							<div class="row">
								<div class="col-md-6">
									<button type="submit" class="btn btn-sm btn-success"><?php echo trans('messages.send'); ?></button>
								</div>
							</div>
						<?php echo Form::close(); ?>

					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="box-info">
					<h2><strong><?php echo trans('messages.recent'); ?></strong> <?php echo trans('messages.task'); ?></h2>
					<?php foreach($tasks as $task): ?>
					<p><strong><?php echo $task->title.' ('.$task->progress.'%)'; ?></strong></p>
					<div class="progress">
					  <div class="progress-bar progress-bar-<?php echo App\Classes\Helper::activityTaskProgressColor($task->progress); ?>" role="progressbar" aria-valuenow="<?php echo $task->progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $task->progress; ?>%;">
					  </div>
					</div>
					<?php endforeach; ?>
					<?php if($task_count > 5): ?>
					<div class="box-footer">
						<p><a href="<?php echo URL::to('/task'); ?>" class="btn btn-primary btn-block btn-sm"><i class="fa fa-arrow-circle-right"></i> <?php echo trans('messages.see_all'); ?></a></p>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
	<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.default', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>