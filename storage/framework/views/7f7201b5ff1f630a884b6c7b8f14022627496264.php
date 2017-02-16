
    <?php $__env->startSection('content'); ?>
        
        <div class="full-content-center animated bounceIn">
            <?php if(File::exists(config('constants.upload_path.logo').config('config.logo'))): ?>
            <a href="/"><img src="/<?php echo config('constants.upload_path.logo').config('config.logo'); ?>" class="" alt="Logo"></a>
            <?php endif; ?>
            <h1>404</h1>
            <h2><?php echo $exception->getMessage(); ?></h2>
            <p><?php echo trans('messages.page_not_found'); ?></p>
            <p><?php echo trans('messages.back_to'); ?> <a href="/"><?php echo trans('messages.dashboard'); ?></a></p>
        </div>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.guest', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>