jQuery(document).ready(function ($) {
	$('#addRelated').on('click', e => {
		e.preventDefault();
		$('#related').append(rowTemplate);
	});
	$("#foreign-table-search")
		.autocomplete(
			{
				minLength: 3,
				source   : '/SimilarContent/similar_content/search/' + tables_to_get,
				select   : function (event, ui) {
					var target_id = ui.item.key;
					var text = ui.item.value;
					var target_table = ui.item.table;
					var rowTemplate = `
						<div class="alert alert-info">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<div class="row last">
								<div class="col-md-10">
									<span>${text} <small>(${target_table}/${target_id})</small></span>
									<input name="related-${target_table}[${nth_related}][id]" value="${target_id}" type="hidden">
									<input name="related-${target_table}[${nth_related}][_joinData][source_table_name]" value="${this_table}" type="hidden">
									<input name="related-${target_table}[${nth_related}][_joinData][target_table_name]" value="${target_table}" type="hidden">
								</div>
							</div>
						</div>
						`;

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
			})
		.autocomplete("instance")._renderItem = (ul, item) => {
		return $("<li>")
			.append("<a>" + item.label + "<br><small>" + item.model + ' / ' + item.key + "</small></a>")
			.appendTo(ul);
	};
});

