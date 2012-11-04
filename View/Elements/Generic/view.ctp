<h2><?php  echo __($meta['singularHumanName']);?></h2>
<?php
    foreach ($data as $datum) { ?>
<div class="<?php echo $meta['singularVar'] ?> view">
    <dl>
    <?php
        foreach ($meta['scaffoldFields'] as $field) { ?>
		<dt><?php echo __(Inflector::humanize($field)); ?></dt>
		<dd>
			<?php echo h($datum[$meta['modelClass']][$field]); ?>
			&nbsp;
		</dd>
    <?php } ?>
	</dl>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Edit %s', $meta['singularHumanName']), array('action' => 'edit', $datum[$meta['modelClass']]['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete %s', $meta['singularHumanName']), array('action' => 'delete', $datum[$meta['modelClass']]['id']), null, __('Are you sure you want to delete this %s?', $meta['singularHumanName'])); ?> </li>
	</ul>
</div>
</div>
<hr style="clear: both" />
<?php } ?>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List %s', $meta['pluralHumanName']), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New %s', $meta['singularHumanName']), array('action' => 'add')); ?> </li>
    </ul>
</div>
