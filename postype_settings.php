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
	
		<h3>Fields</h3>
		
		<?php if (empty($this->postype->fields)) { ?>
			
			<p>There aren't any fields for this type.</p>
			
		<?php } else { ?>
		
			<table>
				<tr>
					<th>Title</th>
					<th>Name</th>
					<th>Type</th>
					<th>Options</th>
				</tr>
		
				<?php foreach ($this->postype->fields as $field) { ?>
					<tr>
						<td class="label">
							<input type="hidden" name="field_id[]" value="<?php echo $field->postype_field_id; ?>" />
							<input type="text" name="field_label[]" value="<?php echo esc_attr($field->label); ?>" />
						</td>

						<td class="name">
							postyper_<input type="text" name="field_name[]" value="<?php echo esc_attr($field->name); ?>" />
						</td>

						<td class="type">
							<select name="field_type[]" id="postyer_type">
								<?php foreach (Postyper::$types as $type) { ?>
									<option value="<?php echo esc_attr($type); ?>" <?php if ($type == $field->type) echo 'selected'; ?>>
										<?php echo $type; ?>
									</option>
								<?php } ?>
							</select>
						</td>

						<td class="desc">
							<input type="text" name="field_description[]" value="<?php echo esc_attr($field->description); ?>" />
						</td>
					</tr>
				<?php } ?>
			</table>
		
		<?php } ?>
		
		<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"></p>
	</form>
</div>