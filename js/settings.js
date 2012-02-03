jQuery(function($) {
	// validate
	$('#postyper_form').submit(function() {
		$(this).find('.errors').html('');
		var errors = [];
		var slug = $('[name=slug]');
		
		slug.val(slug.val().replace(/^ *| $/, ''));
		
		if (slug.val() == '') errors.push("Please provide a slug.");
		
		if (errors.length) {
			$(this).find('.errors').html('<ul><li>' + errors.join('</li><li>') + '</li></ul>');
			return false;
		}
	});
	
	// type editing drawer
	$('.postyper_fields').delegate('.summary', 'click', function() {
		$(this).closest('.postyper_field').find('.edit').slideToggle();
	});
	
	// update summary on change
	$(['label', 'name', 'type']).each(function(ndx, className) {
		$('.postyper_fields').delegate('.edit .'+className, 'change', function() {
			$(this).closest('.postyper_field').find('.'+className).text($(this).val());
		});
	});
	
	// warn if they try to edit the name
	$('.postyper_fields').delegate('.edit .name', 'click', function() {
		if ($(this).val() != '') {
			$(this).closest('.postyper_field').find('.postyper_type_warning').slideToggle();
		}
	});
	
	// add new field
	var newFieldCount = 0;
	$('.postyper_add_field').click(function() {
		var ndx = 'new-' + newFieldCount++;
		var clone = $('.postyper_field:first').clone()
			.removeClass('postyper_field_blank')
			.insertBefore($(this).closest('p'))
			.find('.summary').text("New Field").end()
			.find('.edit').slideToggle().end()
			.find(':text,textarea').val('').end()
			.find('select').val('text').end()
			.find(':input').each(function(ndx, input) {
				this.name = this.name.replace(/fields\[[0-9]+\]/, "fields["+ndx+"]");
			})
			// TODO: reset options
		;

		return false;
	});
	
	// confirm deletes
	$('.postyper_delete').click(function() {
		var del = $(this);
		
		if (del.data('sure')) return true;
		
		var prompt = $("<span class='prompt'>Are you sure?</span> ").insertBefore(del);
		
		del
			.data('initVal', del.val())
			.val('yes')
			.data('sure', true);
			
		$(" <button class='button-secondary postyper_delete_cancel'>no</button>")
			.insertAfter(del)
			.click(function() {
				$(prompt).remove();
				$(this).remove();
				del
					.val(del.data('initVal'))
					.data('sure', false);
			});
			
		return false;
	});
});