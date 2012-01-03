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
	
	// Field actions
	
	$('.postyper_date').datepicker({
		changeMonth: true,
		changeYear: true
	});
	
	$('.postyper_slider').each(function() {
		var rel = $(this).attr('rel');
		var input = $('#' + rel);
		
		$(this).slider({
			min: parseInt($('#' + rel + '__min').val(), 10),
			max: parseInt($('#' + rel + '__max').val(), 10),
			value: input.val(),
			slide: function (event, ui) {
				input.val(ui.value);
			}
		});
	});
	
	$('.postyper_range').each(function() {
		var rel = $(this).attr('rel');
		var low_input = $('#' + rel + '__low');
		var high_input = $('#' + rel + '__high');

		$(this).slider({
			range: true,
			min: parseInt($('#' + rel + '__min').val(), 10),
			max: parseInt($('#' + rel + '__max').val(), 10),
			values: [low_input.val(), high_input.val()],
			slide: function(event, ui) {
				low_input.val(ui.values[0]);
				high_input.val(ui.values[1]);
			}
		});
	});
});