		<?php if(!config('code.mode')): ?>
		<div class="row">
        		<div class="col-md-12">
	        		<div class="box-info">
					<div class="alert alert-info">You are free to perform all actions. It gets reset in every 30 minutes.</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
        <?php if(config('config.application_setup_info') && defaultRole()): ?>
        	<div class="row" id="setup_panel">
        		<div class="col-md-12">
	        		<div class="box-info">
	        			<h2>
	    					<strong><?php echo trans('messages.application_setup_info'); ?></strong>
	    					<div class="additional-btn">
							<?php echo Form::open(['route' => 'setup-complete','role' => 'form', 'class'=>'form-inline','id' => 'setup-complete-form','data-setup-complete' => 1]); ?>

							<button type="submit" class="btn btn-danger btn-sm"><?php echo e(trans('messages.hide')); ?></button>
							<?php echo Form::close(); ?>

							</div>
	        			</h2>
	        			<div id="setup_info">
        					<?php echo $setup_info; ?>

        				</div>
        			</div>
        		</div>
			</div>
        <?php endif; ?>