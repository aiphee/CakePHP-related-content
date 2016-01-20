<?php
	use Cake\Utility\Inflector;

	$thisTable = Inflector::underscore($this->name);
?>

<fieldset class="well last">
	<?php if (!isset($show_legend) || $show_legend): ?>
		<legend><?= isset($custom_title) ? $custom_title : __('Managing related') ?></legend>
	<?php endif ?>
	<?= $this->Form->input('foreign_table_search', ['label' => false, 'placeholder' => __('Start typing to add related')]) ?>

	<div class="row last" id="related">
		<?php if (count($entity->related)) foreach ($entity->related as $key => $related) {
			echo $this->Html->alert(
				"<div class='row last'><div class='col-md-10'><span>
					{$related->name}<small> (" . Inflector::humanize($related->table_name) . ' / ' . $related->id . ")</small>
				</span>" .
				$this->Form->hidden("related-{$related->table_name}.$key.id", ['value' => $related->id]) .
				$this->Form->hidden("related-{$related->table_name}.$key._joinData.source_table_name", ['value' => $thisTable]) .
				$this->Form->hidden("related-{$related->table_name}.$key._joinData.target_table_name", ['value' => $related->table_name]) .
				'</div></div>',
				'info'
			);
		}
		?>
		<script>
			var nth_related = <?= count($entity->related) ?>;
			var this_table = '<?= $thisTable ?>';
			var tables_to_get = '<?= isset($tables_to_get) ? serialize($tables_to_get) : '' ?>';

			var just_one_related = <?= isset($just_one_related) && $just_one_related ? 'true' : 'false' ?>;
		</script>
	</div>

</fieldset>
<script>
</script>
<?= $this->Html->script('SimilarContent.managing-related-compiled') ?>
