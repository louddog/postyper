jQuery(function($) {
	$('.postyper_date').datepicker();
	
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