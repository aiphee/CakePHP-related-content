'use strict';

jQuery(document).ready(function ($) {
	$('#addRelated').on('click', function (e) {
		e.preventDefault();
		$('#related').append(rowTemplate);
	});
	$("#foreign-table-search").autocomplete({
		minLength: 3,
		source: '/SimilarContent/similar_content/search/' + tables_to_get,
		select: function select(event, ui) {
			var target_id = ui.item.key;
			var text = ui.item.value;
			var target_table = ui.item.table;
			var rowTemplate = '\n\t\t\t\t\t\t<div class="alert alert-info">\n\t\t\t\t\t\t\t<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n\t\t\t\t\t\t\t<div class="row last">\n\t\t\t\t\t\t\t\t<div class="col-md-10">\n\t\t\t\t\t\t\t\t\t<span>' + text + ' <small>(' + target_table + '/' + target_id + ')</small></span>\n\t\t\t\t\t\t\t\t\t<input name="related-' + target_table + '[' + nth_related + '][id]" value="' + target_id + '" type="hidden">\n\t\t\t\t\t\t\t\t\t<input name="related-' + target_table + '[' + nth_related + '][_joinData][source_table_name]" value="' + this_table + '" type="hidden">\n\t\t\t\t\t\t\t\t\t<input name="related-' + target_table + '[' + nth_related + '][_joinData][target_table_name]" value="' + target_table + '" type="hidden">\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t';

			if (just_one_related) {
				$('#related').html(rowTemplate);
				nth_related = 1;
			} else {
				$('#related').append(rowTemplate);
				nth_related++;
			}

			$(this).val('');
			event.preventDefault();
		}
	}).autocomplete("instance")._renderItem = function (ul, item) {
		return $("<li>").append("<a>" + item.label + "<br><small>" + item.model + ' / ' + item.key + "</small></a>").appendTo(ul);
	};
});

//# sourceMappingURL=managing-related-compiled.js.map