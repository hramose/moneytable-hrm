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
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />

	<?php echo HTML::style('assets/css/bootstrap.min.css'); ?>

	<?php echo HTML::style('assets/third/select2/css/select2.css'); ?>

	<?php echo HTML::style('assets/css/bootstrap.vertical-tabs.css'); ?>

	<?php echo HTML::style('assets/css/style.css'); ?>

	<?php echo HTML::style('assets/css/style-responsive.css'); ?>

	<?php echo HTML::style('assets/css/sidemenu.css'); ?>

	<?php echo HTML::style('assets/third/sortable/sortable-theme-bootstrap.css'); ?>

	<?php if($direction == 'rtl'): ?>
	<?php echo HTML::style('assets/css/bootstrap-rtl.css'); ?>

	<?php echo HTML::style('assets/css/bootstrap-flipped.css'); ?>

	<?php endif; ?>
	
    <?php if(in_array('tour',$assets)): ?>
	<?php echo HTML::style('assets/third/bootstrap-tour/bootstrap-tour.min.css'); ?>

    <?php endif; ?>

    <?php if(in_array('timepicker',$assets)): ?>
	<?php echo HTML::style('assets/third/timepicker/bootstrap-clockpicker.min.css'); ?>

    <?php endif; ?>

    <?php if(in_array('calendar',$assets)): ?>
	<?php echo HTML::style('assets/third/fullcalendar/fullcalendar.min.css'); ?>

	<?php echo HTML::style('assets/third/fullcalendar/fullcalendar.print.css', array('media' => 'print')); ?>

    <?php endif; ?>
    
    <?php if(in_array('datetimepicker',$assets) || in_array('timepicker',$assets)): ?>
	<?php echo HTML::style('assets/third/datetimepicker/bootstrap-datetimepicker.css'); ?>

    <?php endif; ?>

    <?php if(in_array('rte',$assets)): ?>
	<?php echo HTML::style('assets/third/summernote/summernote.css'); ?>

    <?php endif; ?>

	<?php echo HTML::style('assets/third/toastr/toastr.min.css'); ?>

	<?php echo HTML::style('assets/third/jquery-ui/jquery-ui.min.css'); ?>


	<?php echo HTML::style('assets/css/animate.css'); ?>

	<?php echo HTML::style('assets/third/font-awesome/css/font-awesome.min.css'); ?>

	<?php echo HTML::style('assets/third/nifty-modal/css/component.css'); ?>

	<?php echo HTML::style('assets/third/icheck/skins/flat/blue.css'); ?>

	<?php echo HTML::style('assets/third/select/bootstrap-select.min.css'); ?>

	<?php echo HTML::style('assets/third/datepicker/css/datepicker.css'); ?>

	<?php echo HTML::style('assets/third/datatable/datatables.min.css'); ?>

	<?php echo HTML::style('assets/css/custom.css'); ?>


	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
	
	<link rel="shortcut icon" href="<?php echo URL::to('assets/img/favicon.ico'); ?>">
	<script> var public_path = "<?php echo URL::to('/');; ?>/"; </script>
	</head>
	<body class="tooltips k-rtl">
	
	