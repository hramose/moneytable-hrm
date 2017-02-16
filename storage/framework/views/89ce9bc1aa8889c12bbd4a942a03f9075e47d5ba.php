	<?php echo HTML::script('assets/js/jquery-1.11.3.min.js'); ?>

	<?php echo HTML::script('assets/js/bootstrap.min.js'); ?> 
	<?php echo HTML::script('assets/js/jquery.validate.min.js'); ?>

	<?php echo HTML::script('assets/js/textAvatar.js'); ?>

	<?php echo HTML::script('assets/js/sidemenu.js'); ?>

	<?php echo HTML::script('assets/third/toastr/toastr.min.js'); ?>

	<?php echo HTML::script('assets/third/sortable/sortable.min.js'); ?>

	<?php echo HTML::script('assets/js/jquery.knob.min.js'); ?>

	<?php echo $__env->make('notification', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<?php echo HTML::script('assets/js/bootbox.js'); ?>

	<?php echo HTML::script('assets/third/slimscroll/jquery.slimscroll.min.js'); ?>

    <?php if(in_array('calendar',$assets)): ?>
	<?php echo HTML::script('assets/third/fullcalendar/moment.min.js'); ?>

	<?php echo HTML::script('assets/third/fullcalendar/fullcalendar.min.js'); ?>

	<?php echo HTML::script('assets/third/fullcalendar/lang-all.js'); ?>

	<?php endif; ?>
    <?php if(in_array('graph',$assets)): ?>
	<?php echo HTML::script('https://www.gstatic.com/charts/loader.js'); ?>

	<?php endif; ?>
    <?php if(in_array('rte',$assets)): ?>
	<?php echo HTML::script('assets/third/summernote/summernote.js'); ?>

	<?php endif; ?>
    <?php if(in_array('timepicker',$assets)): ?>
	<?php echo HTML::script('assets/third/timepicker/bootstrap-clockpicker.min.js'); ?>

	<?php endif; ?>
    <?php if(in_array('tour',$assets)): ?>
	<?php echo HTML::script('assets/third/bootstrap-tour/bootstrap-tour.min.js'); ?>

	<?php endif; ?>
	<?php echo HTML::script('assets/third/jquery-ui/jquery-ui.min.js'); ?>

	<?php echo HTML::script('assets/third/select2/js/select2.min.js'); ?>

	<?php echo HTML::script('assets/third/datatable/datatables.min.js'); ?>

	<?php echo HTML::script('assets/third/nifty-modal/js/classie.js'); ?>

	<?php echo HTML::script('assets/third/nifty-modal/js/modalEffects.js'); ?>

	<?php echo HTML::script('assets/third/select/bootstrap-select.min.js'); ?>

	<?php echo HTML::script('assets/third/input/bootstrap.file-input.js'); ?>

	<?php echo HTML::script('assets/third/datepicker/js/bootstrap-datepicker.js'); ?>

	<?php echo HTML::script('assets/third/icheck/icheck.min.js'); ?>


    <?php if(config('lang.'.session('lang').'.datepicker') != 'en'): ?>
	<?php echo HTML::script('assets/third/datepicker/locale/bootstrap-datepicker.'.config('lang.'.session('lang').'.datepicker').'.js',array('charset' => 'UTF-8')); ?>

    <?php endif; ?>

    <?php if(in_array('datetimepicker',$assets) || in_array('timepicker',$assets)): ?>
	<?php echo HTML::script('assets/third/datetimepicker/bootstrap-datetimepicker.js'); ?>

	<?php if(config('lang.'.session('lang').'.datetimepicker') != 'en'): ?>
	<?php echo HTML::script('assets/third/datetimepicker/locale/bootstrap-datetimepicker.'.config('lang.'.session('lang').'.datetimepicker').'.js',array('charset' => 'UTF-8')); ?>

	<?php endif; ?>
    <?php endif; ?>

    <?php if(config('lang.'.session('lang').'.validation') != 'en'): ?>
	<?php echo HTML::script('assets/js/validation-localization/messages_'.config('lang.'.session('lang').'.validation').'.js',array('charset' => 'UTF-8')); ?>

    <?php endif; ?>
	<?php echo HTML::script('assets/js/validation-form.js'); ?>

    <script>
    	$.ajaxSetup({
		   headers: { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
		});
		var datatable_language = "//cdn.datatables.net/plug-ins/1.10.9/i18n/<?php echo config('lang.'.session('lang').'.datatable'); ?>.json";
		var datetimepicker_language = "<?php echo e(config('lang.'.session('lang').'.datetimepicker')); ?>";
		var datepicker_language = "<?php echo e(config('lang.'.session('lang').'.datepicker')); ?>";
		var calendar_language = "<?php echo config('lang.'.session('lang').'.calendar'); ?>";
		var character_remaining = "<?php echo e(trans('messages.character_remaining')); ?>";
		var present_employee = "<?php echo trans('messages.present_employee'); ?>";
		var something_error_message = 'Bug: Something went wrong, Please try again.';
		var page_not_found = 'Bug: Page not found. Please contact your administrator.';
		var toastr_position = "<?php echo e(config('config.notification_position')); ?>";
		var assets = <?php echo json_encode($assets); ?>;
		<?php if(isset($events)): ?>
			var calendar_events = <?php echo json_encode($events); ?>;
		<?php endif; ?>
		<?php if(isset($daily_employee_attendance)): ?>
			var daily_employee_attendance_data = <?php echo json_encode($daily_employee_attendance); ?>;
		<?php endif; ?>
		<?php if(isset($employee_graph_data)): ?>
			var employee_graph_data = <?php echo json_encode($employee_graph_data); ?>;
		<?php endif; ?>
		<?php if(isset($month_holiday_count)): ?>
			var month_holiday_count = <?php echo json_encode($month_holiday_count); ?>;
		<?php endif; ?>
		<?php if(isset($leave_graph)): ?>
			var leave_graph = <?php echo json_encode($leave_graph); ?>;
		<?php endif; ?>
    	<?php if(in_array('graph',$assets)): ?>
    		var enable_graph = 1;
    	<?php else: ?>
    		var enable_graph = 0;
    	<?php endif; ?>
    	var availableDates = <?php echo json_encode($available_date); ?>;
    	var defaultDatepickerDate = <?php echo isset($default_datepicker_date) ? json_encode(datepickerDefaultDate($default_datepicker_date)) : json_encode(datepickerDefaultDate()); ?>;
    	var default_datetimepicker_date = <?php echo isset($default_datetimepicker_date) ? json_encode($default_datetimepicker_date) : json_encode(date('Y-m-d')); ?>;

		<?php if(in_array('mail_config',$assets)): ?>
			$('.mail_config').hide();
			<?php if(config('mail.driver') == 'mail'): ?>
			$('#mail_configuration').show();
			<?php elseif(config('mail.driver') == 'sendmail'): ?>
			$('#sendmail_configuration').show();
			<?php elseif(config('mail.driver') == 'log'): ?>
			$('#log_configuration').show();
			<?php elseif(config('mail.driver') == 'smtp'): ?>
			$('#smtp_configuration').show();
			<?php elseif(config('mail.driver') == 'mandrill'): ?>
			$('#mandrill_configuration').show();
			<?php elseif(config('mail.driver') == 'mailgun'): ?>
			$('#mailgun_configuration').show();
			<?php endif; ?>
			$(document).on('change', '#mail_driver', function(){
				$('.mail_config').hide();
			 	var field = $('#mail_driver').val();
				if(field == 'smtp')
					$('#smtp_configuration').show();
				else if(field == 'mandrill')
					$('#mandrill_configuration').show();
				else if(field == 'mailgun')
					$('#mailgun_configuration').show();
				else if(field == 'mail')
					$('#mail_configuration').show();
				else if(field == 'sendmail')
					$('#sendmail_configuration').show();
				else if(field == 'log')
					$('#log_configuration').show();
			});
		<?php endif; ?>
	</script>
	<?php echo HTML::script('assets/js/wmlab.js'); ?>

	</body>
</html>