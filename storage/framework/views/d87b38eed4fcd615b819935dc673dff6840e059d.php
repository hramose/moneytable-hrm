<?php echo $__env->make('layouts.head', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	
<div class="container">
	<div class="logo-brand header sidebar rows">
		<div class="logo">
			<h1><a href="<?php echo URL::to('/'); ?>"><?php echo config('config.application_name').' '.config('code.version'); ?></a></h1>
		</div>
	</div>
	<?php echo $__env->make('layouts.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="right content-page">

		<?php echo $__env->make('layouts.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>	
		
        <div class="body content rows scroll-y">
			<?php echo $__env->yieldContent('breadcrumb'); ?>
			
			<?php echo $__env->make('message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

			<?php echo $__env->yieldContent('content'); ?>

			<?php echo $__env->make('layouts.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>	
        </div>
    </div>
</div>
<div id="overlay"></div>
<div class="modal fade" id="myModal" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		</div>
	</div>
</div>
	
<?php echo $__env->make('layouts.foot', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>	

		
	
	
	