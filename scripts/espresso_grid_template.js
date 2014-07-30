jQuery(document).ready(function($){

	$("#ee_filter_cat").change(function() {
		var ee_filter_cat_id = $("option:selected").attr('class');
		var ee_filter_table_rows = $(".espresso-table-row");
		console.log(ee_filter_cat_id);
		ee_filter_table_rows.each(function() {
			if ( $(this).hasClass( ee_filter_cat_id ) ) {
				$(this).show();
			} else  {
				$(this).hide();
			}
		});
		if( ee_filter_cat_id === 'ee_filter_show_all') {
			ee_filter_table_rows.show();
		}
	});

});