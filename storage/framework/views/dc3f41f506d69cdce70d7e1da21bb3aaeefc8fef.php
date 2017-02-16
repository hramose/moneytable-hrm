<!DOCTYPE html>
<html>
	<head>
	<title><?php echo config('config.application_name') ? : config('constants.default_title'); ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">

	<?php echo HTML::style('assets/css/bootstrap.min.css'); ?>

	<?php echo HTML::style('assets/third/select2/css/select2.css'); ?>

	<?php echo HTML::style('assets/css/style.css'); ?>

	<?php echo HTML::style('assets/css/style-responsive.css'); ?>

	<?php echo HTML::style('assets/third/toastr/toastr.min.css'); ?>

	<?php echo HTML::style('assets/css/animate.css'); ?>

	<?php echo HTML::style('assets/third/font-awesome/css/font-awesome.min.css'); ?>

	<?php echo HTML::style('assets/third/icheck/skins/flat/blue.css'); ?>

	<?php echo HTML::style('assets/css/custom.css'); ?>


	<body class="tooltips full-content">
	
	<div class="container">
	
		<?php echo $__env->yieldContent('content'); ?>

	</div>  
	
	<?php echo HTML::script('assets/js/jquery-1.11.3.min.js'); ?>

	<?php echo HTML::script('assets/js/bootstrap.min.js'); ?> 
	<?php echo HTML::script('assets/js/jquery.validate.min.js'); ?>

	<?php echo HTML::script('assets/third/toastr/toastr.min.js'); ?>


	<?php echo $__env->make('notification', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	
	<?php echo HTML::script('assets/third/slimscroll/jquery.slimscroll.min.js'); ?>

	<?php echo HTML::script('assets/third/select/bootstrap-select.min.js'); ?>

	<?php echo HTML::script('assets/third/input/bootstrap.file-input.js'); ?>

	<?php echo HTML::script('assets/third/datepicker/js/bootstrap-datepicker.js'); ?>

	<?php echo HTML::script('assets/third/icheck/icheck.min.js'); ?>

	<?php echo HTML::script('assets/third/wizard/jquery.snippet.js'); ?>

	<?php echo HTML::script('assets/third/wizard/jquery.easyWizard.js'); ?>

	<?php echo HTML::script('assets/js/validation-form.js'); ?>

	
    <script>
	$(document).ready(function() { 
		$('input').iCheck({
		checkboxClass: 'icheckbox_flat-blue',
		radioClass: 'iradio_flat-blue',
		increaseArea: '20%' 
		});
		Validate.init(); 
		$('#myWizard').easyWizard({
		buttonsClass: 'btn btn-default',
		submitButtonClass: 'btn btn-primary',
		showSteps: true,
	    showButtons: true,
	    submitButton: false
		});
	});
	</script>

	</body>
</html>