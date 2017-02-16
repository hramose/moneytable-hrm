
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<h4 class="modal-title"><?php echo trans('messages.to_do'); ?></h4>
	</div>
	<div class="modal-body">
		<?php echo Form::open(['route' => 'todo.store','role' => 'form', 'class'=>'todo-form','id' => 'todo-form']); ?>

			<?php echo $__env->make('todo._form', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
		<?php echo Form::close(); ?>

	</div>