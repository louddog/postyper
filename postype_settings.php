<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>Custom Post Type: <em><?php echo $this->postype->singular; ?></em></h2>

	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		
		<?php wp_nonce_field(POSTYPER_NONCE_PATH, 'postyper_save_nonce'); ?>
		
		<table>
			<tr>
				<th><label for="slug">Slug</label></th>
				<td><input type="text" name="slug" value="<?php echo esc_attr($this->postype->slug); ?>" /></td>
			</tr>
			<tr>
				<th><label for="archive">Archive</label></th>
				<td><input type="text" name="archive" value="<?php echo esc_attr($this->postype->archive); ?>" /></td>
			</tr>
			<tr>
				<th><label for="singular">Singular</label></th>
				<td><input type="text" name="singular" value="<?php echo esc_attr($this->postype->singular); ?>" /></td>
			</tr>
			<tr>
				<th><label for="plural">Plural</label></th>
				<td><input type="text" name="plural" value="<?php echo esc_attr($this->postype->plural); ?>" /></td>
			</tr>
		</table>
	
		<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Changes"></p>

		<h3>Fields</h3>
		
		<p><a href="#" class="postyper_add_field">add field</a></p>
		
		
		<table class="postyper_fields">
			<tr>
				<th>Title</th>
				<th>Name</th>
				<th>Type</th>
				<th>Description</th>
				<th>Options</th>
			</tr>
	
			<?php if (empty($this->postype->fields)) { ?>

				<tr class="postyper_no_fields"><td colspan="5">There aren't yet any fields for this type.  <a href="#" class="postyper_add_field">Add</a> the first one now.</td></tr>

			<?php } else foreach ($this->postype->fields as $ndx => $field) { ?>
				
				<tr rel="<?php echo $ndx; ?>">
					<td class="label">
						<input type="hidden" name="fields[<?php echo $ndx; ?>][id]" value="<?php echo $field->postype_field_id; ?>" />
						<input type="text" name="fields[<?php echo $ndx; ?>][label]" value="<?php echo esc_attr($field->label); ?>" />
					</td>

					<td class="name">
						<input type="text" name="fields[<?php echo $ndx; ?>][name]" value="<?php echo esc_attr($field->name); ?>" />
					</td>
					
					<td class="type">
						<select name="fields[<?php echo $ndx; ?>][type]" id="postyer_type">
							<?php foreach (Postyper::$types as $type) { ?>
								<option value="<?php echo esc_attr($type); ?>" <?php if ($type == $field->type) echo 'selected'; ?>>
									<?php echo $type; ?>
								</option>
							<?php } ?>
						</select>
					</td>

					<td class="desc">
						<input type="text" name="fields[<?php echo $ndx; ?>][description]" value="<?php echo esc_attr($field->description); ?>" />
					</td>

					<td class="options">
						<?php if (in_array($field->type, array('radio', 'select'))) { ?>
							<?php if (is_array($field->options)) foreach ($field->options as $option) { ?>
								<input type="text" name="fields[<?php echo $ndx; ?>][options][]" value="<?php echo esc_attr($option); ?>" />
							<?php } ?>
							<a href="#" class="new">new</a>
						<?php } ?>
					</td>
				</tr>
				
			<?php } ?>
			
			<tbody class="postyper_template" rel="row">
				<tr>
					<td class="label">
						<input type="hidden" name="fields[new][id]" />
						<input type="text" name="fields[new][label]"  />
					</td>

					<td class="name">
						<input type="text" name="fields[new][name]" />
					</td>
					
					<td class="type">
						<select name="fields[new][type]">
							<?php foreach (Postyper::$types as $type) { ?>
								<option value="<?php echo esc_attr($type); ?>">
									<?php echo $type; ?>
								</option>
							<?php } ?>
						</select>
					</td>

					<td class="desc">
						<input type="text" name="fields[new][description]" />
					</td>

					<td class="options">
						<a href="#" class="new">new</a>
					</td>
				</tr>
				
			</tbody>
			
		</table>
		
		<div class="postyper_template" rel="radio">
			<input type="radio" /><label />
		</div>
		
		<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Changes"></p>
	</form>
</div>