<?php if (!defined('POSTYPER_VERSION')) die('do not load directly'); // included from within Postyper::postype_settings() ?>

<?php
	$slug = str_replace('postyper_', '', $_GET['page']);
	$postype = array_key_exists($slug, $this->postypes)
		? $this->postypes[$slug]
		: new Postype();
?>

<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>Custom Post Type: <em><?php echo $postype->singular; ?></em></h2>

	<form id="postyper_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		
		<?php
			$type_attributes = array(
				'with_options' => array(),
				'with_settings' => array(),
			);

			foreach ($this->field_types as $type) {
				if (is_array($type->options)) $type_attributes['with_options'][] = $type->type;
				if (is_array($type->settings) && count($type->settings)) $type_attributes['with_settings'][$type->type] = $type->settings;
			}
		?>
		<script>
			var type_attributes = <?php echo json_encode($type_attributes); ?>;
		</script>
				
		<div class="errors"></div>

		<?php wp_nonce_field($nonce, 'postyper_save_nonce'); ?>
		
		<input type="hidden" name="postype_id" value="<?php echo $postype->id ? $postype->id : 'new'; ?>" />

		<table>
			<tr>
				<th><label for="slug">Slug</label></th>
				<td><input type="text" name="slug" value="<?php echo esc_attr($postype->slug); ?>" /></td>
			</tr>
			<tr>
				<th><label for="archive">Archive</label></th>
				<td><input type="text" name="archive" value="<?php echo esc_attr($postype->archive); ?>" /></td>
			</tr>
			<tr>
				<th><label for="singular">Singular</label></th>
				<td><input type="text" name="singular" value="<?php echo esc_attr($postype->singular); ?>" /></td>
			</tr>
			<tr>
				<th><label for="plural">Plural</label></th>
				<td><input type="text" name="plural" value="<?php echo esc_attr($postype->plural); ?>" /></td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="submit" class="button-primary" value="Save Changes">
			<input type="submit" name="delete" class="button-secondary postyper_delete" value="Delete Postype" />
		</p>

		<h3>Fields</h3>
		
		<p>Sometimes there is more to a type of information that just a title, and description.  Add fields to your post type to all editors to more describe their posts in more depth.</p>
		
		<div class="postyper_fields">
			<?php foreach (count($postype->fields) ? $postype->fields : array(new PostypeText) as $ndx => $field) { ?>
				<div class="postyper_field <?php if (!$field->label) echo 'postyper_field_blank'; ?>">
					
					<input type="hidden" class="id" name="fields[<?php echo $ndx; ?>][postype_field_id]" value="<?php echo $field->postype_field_id; ?>" />
					
					<p class="summary">
						<?php if ($field->label) { ?>
							<span class="label"><?php echo $field->label; ?></span>
							(<span class="name"><?php echo $field->name; ?></span>)
							of type
							<span class="type">
								<?php echo $field->type; ?>
								<?php if ($field->type == 'post') echo ': '.$field->settings['post_type']['value']; ?>
							</span>
						<?php } ?>
					</p>
				
					<div class="edit">
						<p>
							<label>Label</label> <input type="text" class="label" name="fields[<?php echo $ndx; ?>][label]" value="<?php echo esc_attr($field->label); ?>" /><br />
							<span class="description">This is what WordPress editors will see in the post's edit form.</span>
						</p>

						<p>
							<label>Name</label> <input type="text" class="name" name="fields[<?php echo $ndx; ?>][name]" value="<?php echo esc_attr($field->name); ?>" /><br />
							<span class="description">This the field's variable name.  It's used in templates to display the post's information.</span><br />
							<span class="description">Tip: Names that begin with "_" (underscore) do not show up in a post's Custom Fields box.</span><br />
							<span class="postyper_type_warning">Be careful changing this. Templates may depend on this value.</span>
						</p>

						<p>
							<label>Type</label>
							<select name="fields[<?php echo $ndx; ?>][type]" class="type">
								<?php foreach ($this->field_types as $type) { ?>
									<option
										value="<?php echo esc_attr($type->type); ?>"
										<?php if ($field->type == $type->type) echo 'selected'; ?>
										>
										<?php echo $type->type; ?>
									</option>
								<?php } ?>
							</select><br /><?php // TODO: put a link to open a help page in a a new window ?>
							<span class="description">This the field's type.</span>
						</p>
					
						<p>
							<label>Description</label><br />
							<textarea name="fields[<?php echo $ndx; ?>][description]"><?php echo $field->description; ?></textarea><br />
							<span class="description">This text helps the WordPress editor know what this field is for.</span>
						</p>

						<p class="options">
							<label>Options:</label>
							<button class="button-secondary postyper_new_option">+</button>
							<span class="values">
								<?php if ($field->options && count($field->options)) foreach ($field->options as $key => $value) { ?>
									<span class="value">
										<input
											type="hidden"
											name="fields[<?php echo $ndx; ?>][options][]"
											value="<?php echo esc_attr($value); ?>"
										/>
										<span><?php echo $value; ?></span>
									</span>
								<?php } ?>
							</span>
						</p>
												
						<p class="settings">
							<label>Settings:</label>
							<span class="values">
								<?php if ($field->settings && count($field->settings)) foreach ($field->settings as $key => $setting) { ?>
									<?php $name = "fields[$ndx][settings][$key]"; ?>
									
									<span class="value">
										<label><?php echo $key; ?></label>
										
										<?php if (isset($setting['options']) && is_array($setting['options'])) { ?>
											<select name="<?php echo $name; ?>">
												<?php foreach ($setting['options'] as $key => $value) { ?>
													<option
														value="<?php echo $key; ?>"
														<?php if ($key == $setting['value']) echo 'selected'; ?>
													>
														<?php echo $value; ?>
													</option>
												<?php } ?>
											</select>
										<?php } else { ?>
											<input
												type="text"
												name="<?php echo $name; ?>"
												value="<?php echo esc_attr($setting['value']); ?>"
											/>
										<?php } ?>
									</span>
								<?php } ?>
							</span>
						</p>
						
						<button class="button-secondary postyper_delete postyper_delete_field">delete field</button>
						
					</div> <!-- .edit -->
				
				</div> <!-- .postyper_field -->
			<?php } ?>
		</div> <!-- .postyper_fields -->
		
		<p class="submit"><input type="button" class="postyper_add_field" value="add field" /></p>
		
		<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Changes" /></p>
	</form>
</div>
