<div>
	<h4>Done!</h4>
	<p>Below is an outline of your results for this question. You can either redo the above question or move onto the next question.</p>
	<table class="table" style="width: 600px; margin: 0 auto;">
		<thead>
			<tr><th>Section</th><th>Attemps</th><th>Score</th></tr>
		</thead>
		<tbody>
			<tr class="success"><td>1</td><td>2</td><td>100</td></tr>
			<tr class="warning"><td>2</td><td>1</td><td>200</td></tr>
		</tbody>
	</table>
	<div class="form-actions">
		<?php echo Html::anchor($sys_root_wrap.'question/'.$this_question->id, 'Redo <i class="icon-repeat icon-white"></i>', array('class' => 'btn btn-primary')); ?>
		<?php if(!empty($next_question)): ?>
			<?php echo Html::anchor($sys_root_wrap.'question/'.$next_question->id, 'Next <i class="icon-arrow-right icon-white"></i>', array('class' => 'btn btn-primary')); ?>
		<?php else: ?>
			<?php echo Html::anchor($sys_root_wrap.'question/list', 'Done <i class="icon-thumbs-up icon-white"></i>', array('class' => 'btn btn-primary')); ?>
		<?php endif; ?>
	</div>
</div>