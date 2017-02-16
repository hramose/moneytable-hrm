
			  <div class="form-group">
			    <?php echo Form::label('date',trans('messages.date'),[]); ?>

				<?php echo Form::input('text','date',isset($todo->date) ? $todo->date : '',['class'=>'form-control datepicker','placeholder'=>trans('messages.date'),'readonly' => 'true']); ?>

			  </div>
			  <div class="form-group">
			    <?php echo Form::label('title',trans('messages.title'),[]); ?>

				<?php echo Form::input('text','title',isset($todo->title) ? $todo->title : '',['class'=>'form-control','placeholder'=>trans('messages.title')]); ?>

			  </div>
				<div class="form-group">
				    <?php echo Form::label('description',trans('messages.description'),[]); ?>

				    <?php echo Form::textarea('description',isset($todo->description) ? $todo->description : '',['size' => '30x3', 'class' => 'form-control', 'placeholder' => trans('messages.description')]); ?>

				    <span class="countdown"></span>
				</div>
				<div class="form-group">
				  <?php echo Form::label('visibility',trans('messages.visibility'),['class' => 'col-sm-2']); ?>

					<div class="col-sm-10">
						<label class="checkbox-inline">
							<input type="radio" name="visibility" id="visibility" value="private" <?php echo e((isset($todo->visibility) && $todo->visibility != 'public') ? 'checked' : ''); ?>  > <?php echo trans('messages.private'); ?>

						</label>
						<label class="checkbox-inline">
							<input type="radio" name="visibility" id="visibility" value="public" <?php echo e((isset($todo->visibility) && $todo->visibility == 'public') ? 'checked' : ''); ?>> <?php echo trans('messages.public'); ?>

						</label>
					</div>
					<div class="clear"></div>
			  	<?php echo Form::submit(isset($buttonText) ? $buttonText : trans('messages.save'),['class' => 'btn btn-primary pull-right']); ?>

			  	<br />
			  	
