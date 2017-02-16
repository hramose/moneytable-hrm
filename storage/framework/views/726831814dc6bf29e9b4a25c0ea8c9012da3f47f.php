
		<div class="left side-menu">
			
			
            <div class="body rows scroll-y">
                <div class="sidebar-inner slimscroller">
					<div class="media">
						<div class="media-body">
						<a class="pull-left" href="#">
							<?php echo \App\Classes\Helper::getAvatar(Auth::user()->id); ?>

						</a>
							<?php echo trans('messages.welcome_back'); ?>,
							<h4 class="media-heading"><strong><?php echo Auth::user()->full_name; ?> </strong></h4>
							<h6><?php echo Auth::user()->Designation->full_designation; ?></h6>
							<div class="clear"></div>
							<span style="font-size:12px;">

							<a href="/profile" style="font-size:12px;color:#fff;"><?php echo e(trans('messages.my').' '.trans('messages.profile')); ?></a>
							<?php if(showDateTime(Auth::user()->last_login)): ?>
							| <?php echo trans('messages.last_login'); ?> <br /><?php echo showDateTime(Auth::user()->last_login).' '.trans('messages.from').' '.Auth::user()->last_login_ip; ?></span>
							<?php endif; ?>

						</div>
					</div> 
					<div id="sidebar-menu">
						<ul id="sidebar-menu-list">
						</ul>
						<div class="clear"></div>
					</div>
				</div>
            </div>
            
            <div class="footer rows animated fadeInUpBig">
				<div class="logo-brand header sidebar rows">
					<div class="logo">
						<h1><a href="<?php echo URL::to('/'); ?>"><?php echo config('config.application_name'); ?> <?php echo config('code.version'); ?> </a> </h1>
						<button class="sidebar-toggle">toggle</button>
					</div>
				</div>
            </div>
        </div>