

    <?php $__env->startSection('content'); ?>
        <?php if(config('config.enable_job_application_candidates')): ?>
        <a class="btn btn-primary btn-sm pull-right" style='margin:15px;' role="button" href="/apply"><?php echo trans('messages.apply_for_job'); ?></a>
        <?php endif; ?>
        <?php if(config('config.enable_registration')): ?>
        <a class="btn btn-primary btn-sm pull-right" style='margin:15px;' role="button" href="/register"><?php echo trans('messages.register'); ?></a>
        <?php endif; ?>
        <div class="full-content-center animated fadeInDownBig">
            <?php if(File::exists(config('constants.upload_path.logo').config('config.logo'))): ?>
            <a href="/"><img src="/<?php echo config('constants.upload_path.logo').config('config.logo'); ?>" class="" alt="Logo"></a>
            <?php endif; ?>
            <div class="login-wrap">
                <div class="box-info">
                <h2 class="text-center"><strong><?php echo trans('messages.user'); ?></strong> <?php echo trans('messages.login'); ?></h2>

                    <form role="form" action="<?php echo URL::to('/login'); ?>" method="post" class="login-form" id="login-form" data-submit="noAjax">
                        <?php echo csrf_field(); ?>

                        <div class="form-group login-input">
                        <i class="fa fa-user overlay"></i>
                        <?php if(config('config.login_with') == 'email'): ?>
                            <input type="email" name="email" id="email" class="form-control text-input" placeholder="<?php echo trans('messages.email'); ?>">
                        <?php else: ?>
                            <input type="text" name="username" id="username" class="form-control text-input" placeholder="<?php echo trans('messages.username'); ?>">
                        <?php endif; ?>
                        </div>
                        <div class="form-group login-input">
                        <i class="fa fa-key overlay"></i>
                        <input type="password" name="password" id="password" class="form-control text-input" placeholder="<?php echo trans('messages.password'); ?>">
                        </div>
                        <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember" value="1"> <?php echo trans('messages.remember_me'); ?>

                        </label>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-12">
                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-unlock"></i> <?php echo trans('messages.login'); ?></button>
                            </div>
                        </div>
                        
                    </form>
                    <p class="text-center"><a href="<?php echo URL::to('/password/email'); ?>"><i class="fa fa-lock"></i> <?php echo trans('messages.forgot_password'); ?>?</a></p>

                        <?php if(!getMode()): ?>
                        <div class="row" style="margin-bottom: 15px;">
                            <h4 class="text-center">For Demo Purpose</h4>
                            <div class="col-md-12">
                                <a href="#" data-username="admin" data-email="support@wmlab.in" data-password="123456" class="btn btn-block btn-default login-as">Login as Admin</a>
                            </div>
                            <div class="col-md-12">
                                <a href="#" data-username="john.doe" data-email="john@example.com" data-password="123456" class="btn btn-block btn-default login-as">Login as Manager</a>
                            </div>
                            <div class="col-md-12">
                                <a href="#" data-username="jack.aristal" data-email="jack@example.com" data-password="123456" class="btn btn-block btn-default login-as">Login as Staff</a>
                            </div>
                        </div>
                        <?php endif; ?>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.guest', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>