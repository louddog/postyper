jQuery(function($) {
	var loading = true;

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
	
	// conditionally display options and settings
	// TODO: preserve values across type changes (jQuery.data()?)
	$('.postyper_fields').delegate('.edit .type', 'change', function() {
		var type = $(this).val();
		var edit_box = $(this).closest('.edit');
		var name_prefix = edit_box.closest('.postyper_field').find('.id').attr('name').replace('[postype_field_id]', '');
		var options_values = $('.options .values', edit_box);
		var settings_values = $('.settings .values', edit_box);
		var settings = type_attributes['with_settings'][type];
		
		// initially hide all options and settings
		$('.options, .settings', edit_box).hide();
		
		// if not while loading, clear values
		if (!loading) {
			options_values.html('');
			settings_values.html('');
		}
		
		// show options if the type is right
		if ($.inArray(type, type_attributes['with_options']) >= 0) {
			$('.options', edit_box).show();
		}

		// show settings if the type is right
		if (settings) {
			$('.settings', edit_box).show();

			// if not while loading, create default elements
			if (!loading) {
				for (var key in settings) {
					var setting = settings[key];
					var name = name_prefix + '[settings]['+key+']';
					var html = '<span class="value"><label>' + key + '</label>';
					
					if (setting.options) {
						html += '<select name="'+name+'">';
						for (var slug in setting.options) {
							html += '<option value="'+slug+'">'+setting.options[slug]+'</option>';
						}
						html += '</select>';
					} else {
						html += '<input type="text" name="'+name+'" />';
					}
					
					html += '</span>';
					
					$(html)
						.appendTo(settings_values)
						.find(':input').val(setting.value);
				}
			}
		}
	});
	$('.edit .type').change(); // run on page load

	// change options buttons into inputs
	$('.postyper_fields').delegate('.options .value span', 'click', function() {
		var container = $(this).closest('.value');
		$(this).remove();
		var html = container.html().replace('type="hidden"', 'type="text"');
		html += "<a href='#' class='delete'>x</a>";
		container.html(html);
	});
	
	// add new option
	$('.postyper_fields').delegate('.postyper_new_option', 'click', function() {
		var field = $(this).closest('.postyper_field')
		var name_prefix = field.find('.id').attr('name').replace('[postype_field_id]', '');
		field.find('.options .values').append('<span class="value"><input type="text" name="' + name_prefix + '[options][]" /><a href="#" class="delete">x</a></span>');

		return false;
	});
	
	// delete list option
	$('.postyper_fields').delegate('.options .delete', 'click', function() {
		$(this).closest('.value').remove();
		return false;
	});
	
	// TODO: allow options to be reordered by dragging?
		
	// update summary on change
	// TODO: update type "post" postype hint
	$(['label', 'name', 'type']).each(function(ndx, className) {
		$('.postyper_fields').delegate('.edit .'+className, 'change', function() {
			$(this).closest('.postyper_field').find('.summary .'+className).text($(this).val());
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
		var field_ndx = 'new-' + newFieldCount++;
		var clone = $('.postyper_field:first').clone();
		clone.removeClass('postyper_field_blank')
			.appendTo('.postyper_fields')
			.find('.summary').text("New Field").end()
			.find('.edit').slideToggle().end()
			.find(':text,textarea').val('').end()
			.find('select').val('text').end()
			.find('.edit .type').change().end()
			.find('.id').val('').end()
			.find(':input').each(function(ndx, input) {
				this.name = this.name.replace(/fields\[[0-9]+\]/, "fields["+field_ndx+"]");
			});

		// TODO: reset options
		
		$(':text:first', clone).focus();

		return false;
	});

	// confirm deletes
	$('.postyper_fields').delegate('.postyper_delete', 'click', function(event) {
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
			
		event.stopPropagation();
		return false;
	});
	
	// delete field
	$('.postyper_fields').delegate('.postyper_delete_field', 'click', function(event) {
		if (event.isPropagationStopped()) return false;
		$(this).closest('.postyper_field').slideUp('slow', function() {
			$(this).remove();
		});
		return false;
	});
	
	loading = false;
});