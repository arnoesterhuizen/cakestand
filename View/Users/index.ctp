<div class="users index">
	<h2><?php echo __('Users');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('email');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	foreach ($data as $datum): ?>
	<tr>
		<td title="<?php echo $datum['User']['id'] ?>"><?php echo h(substr($datum['User']['id'], 0, 8)); ?>&nbsp;</td>
		<td><?php echo h($datum['User']['name']); ?>&nbsp;</td>
		<td><?php echo h($datum['User']['email']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $datum['User']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $datum['User']['id'])); ?>
			<?php echo $this->Html->link(__('Copy'), array('action' => 'copy', $datum['User']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $datum['User']['id']), null, __('Are you sure you want to delete # %s?', $datum['User']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->first('&laquo; ' . __('first'), array('escape' => false), null, array('class' => 'first disabled'));
		echo $this->Paginator->prev('&lsaquo; ' . __('previous'), array('escape' => false), null, array('class' => 'prev disabled', 'escape' => false));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' &rsaquo;', array('escape' => false), null, array('class' => 'next disabled', 'escape' => false));
		echo $this->Paginator->last(__('last') . ' &raquo;', array('escape' => false), null, array('class' => 'last disabled', 'escape' => false));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New User'), array('action' => 'add')); ?></li>
	</ul>
</div>
