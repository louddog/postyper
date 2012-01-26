jQuery(function($) {
	var templates = {};
	$('.postyper_template').each(function() {
		templates[$(this).attr('rel')] = $(this).html();
		$(this).remove();
	});	
	
	var newFieldCount = 0;
	$('.postyper_add_field').click(function() {
		var ndx = 'new-' + newFieldCount++;

		$('.postyper_no_fields').remove();
		
		$(templates.row)
			.appendTo('.postyper_fields')
			.attr('rel', ndx)
			.find(':input').each(function(n, input) {
				$(input).attr('name', $(input).attr('name').replace('[new]', '[' + ndx + ']'));
			});
			
		return false;
	});
	
	$('.postyper_fields').delegate('.options .new', 'click', function() {
		var ndx = $(this).closest('tr').attr('rel');		
		$('<input type="text" name="fields[' + ndx + '][options][]" />').insertBefore(this).focus();
		return false;
	});
	
	$('.postyper_fields').delegate('.delete a', 'click', function() {
		if (confirm("Are you sure?")) {
			$(this).closest('tr').remove();
		}
		return false;
	});
	
	$('.postyper_delete_postype').click(function() {
		return confirm("Are you sure?  This can't be undone.  Your posts' information will not be deleted, but the definition will, so you will not be able to access the information.");
	});
});