<?php
	$search_field_id     = isset($search_field_id) ? $search_field_id : 'foreign-table-search';
	$field_id_selector   = isset($field_id_selector) ? $field_id_selector : '#foreign-table-id';
	$field_name_selector = isset($field_name_selector) ? $field_name_selector : '#foreign-table-name';
	$label            = isset($label) ? $label : null;
?>
<?= $this->Form->input('foreign_table_search', ['label' => $label, 'id' => $search_field_id, 'append' => [$this->Form->button('×', ['class' => 'btn-danger clearForeign'])]]) ?>

<script type="text/javascript">
	var tables_to_get = '<?= isset($tables_to_get) ? serialize($tables_to_get) : '' ?>';
	jQuery(document).ready(function ($) {
		var $field_id_selector = $("<?= $field_id_selector ?>");
		var $field_name_selector = $("<?= $field_name_selector ?>");


		var $foreign_table_search = $("#<?= $search_field_id ?>");

		if (($field_name_selector.val().length > 2) && $field_id_selector.val() > 0) {
			$.post('/RelatedContent/RelatedContent/getActionName/' + $field_name_selector.val() + '/' + $field_id_selector.val(), function (data) {
				$foreign_table_search.prop('placeholder', data);
			});
		} else {
			$foreign_table_search.prop('placeholder', '<?= __('Start typing to search in content') ?>');
		}
		$foreign_table_search
			.autocomplete(
				{
					minLength: 3,
					source   : '/RelatedContent/RelatedContent/search/' + tables_to_get,
					select   : function (event, ui) {
						$(this).prop('placeholder', ui.item.value);
						$field_id_selector.val(ui.item.key);
						$field_name_selector.val(ui.item.model);
					}
				})
			.autocomplete("instance")._renderItem = function (ul, item) {
			return $("<li>")
				.append("<a>" + item.label + "<br><small>" + item.model + ' / ' + item.key + "</small></a>")
				.appendTo(ul);
		};

		$foreign_table_search.parent().find('.clearForeign').on('click', function (e) {
			e.preventDefault();
			$foreign_table_search.prop('placeholder', '<?= __('Start typing to search in content') ?>');
			$foreign_table_search.val('');
			$field_id_selector.val('');
			$field_name_selector.val('');
		});
	});
</script>
