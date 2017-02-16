			<?php if($type == 'success'): ?>
				<div class="alert alert-success"><i class="fa fa-check icon"></i> <?php echo $message; ?></div>
			<?php elseif($type == 'danger'): ?>
				<div class="alert alert-danger"><i class="fa fa-times icon"></i> <?php echo $message; ?></div>
			<?php elseif($type == 'warning'): ?>
				<div class="alert alert-warning"><i class="fa fa-exclamation-circle icon"></i> <?php echo $message; ?></div>
			<?php elseif($type == 'info'): ?>
				<div class="alert alert-info"><i class="fa fa-info-circle icon"></i> <?php echo $message; ?></div>
			<?php else: ?>
				<div class="alert alert-default"><i class="fa fa-question-circle icon"></i> <?php echo $message; ?></div>
			<?php endif; ?>