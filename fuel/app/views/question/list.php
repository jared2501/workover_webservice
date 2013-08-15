<div class="span12">
	<table class="table table-hover table-quickclick">
		<thead>
			<tr><th>Group</th><th>Number</th><th>Difficulty</th><th>Title</th></tr>
		</thead>
		<tbody>
			<?php foreach($questions as $q): ?>
				<tr>
					<td><?php echo $q->group; ?></td>
					<td><?php echo $q->position; ?></td>
					<td><?php echo $q->difficulty; ?></td>
					
					
					<td><?php echo Html::anchor($sys_root_wrap.'question/'.$q->id, "<strong>{$q->title}</strong>"); ?></td>
					<td><?php echo Html::anchor($sys_root_nowrap.'question/delete', '<i class="icon-remove-sign icon-white"></i> Delete', array('class' => 'btn btn-danger')); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	
	<?php echo View::forge('js/table/quickclick'); ?>
</div>