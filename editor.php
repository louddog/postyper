<?php $postype = new Postype(str_replace('postyper_', '', $_GET['page'])); ?>

<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>Custom Post Type: <em><?php echo $postype->singular; ?></em></h2>

	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

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

		<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Changes"></p>

		<h3>Fields</h3>

		<table class="postyper_fields">
			<tr>
				<th>Title</th>
				<th>Name</th>
				<th>Type</th>
				<th>Description</th>
				<th>Options</th>
			</tr>

			<?php if (empty($postype->fields)) { ?>

				<tr class="postyper_no_fields"><td colspan="5">There aren't yet any fields for this type.  <a href="#" class="postyper_add_field">Add</a> the first one now.</td></tr>

			<?php } else foreach ($postype->fields as $ndx => $field) { ?>
				
				<?php $field_type = get_class($field); ?>
				
				<tr rel="<?php echo $ndx; ?>">
					<td class="label">
						<input type="hidden" name="fields[<?php echo $ndx; ?>][postype_field_id]" value="<?php echo $field->postype_field_id; ?>" />
						<input type="text" name="fields[<?php echo $ndx; ?>][label]" value="<?php echo esc_attr($field->label); ?>" />
					</td>

					<td class="name">
						<input type="text" name="fields[<?php echo $ndx; ?>][name]" value="<?php echo esc_attr($field->name); ?>" />
					</td>

					<td class="type">
						<select name="fields[<?php echo $ndx; ?>][type]" id="postyer_type">
							<?php foreach ($this->field_types as $type => $attrs) { ?>
								<option
									value="<?php echo esc_attr($attrs['className']); ?>"
									<?php if ($field_type == $attrs['className']) echo 'selected'; ?>
								>
									<?php echo $type; ?>
								</option>
							<?php } ?>
						</select>
					</td>

					<td class="desc">
						<input type="text" name="fields[<?php echo $ndx; ?>][description]" value="<?php echo esc_attr($field->description); ?>" />
					</td>

					<td class="options">
						<?php if (in_array($field_type, array('PostypeMultiChoice'))) { ?>
							<?php if (is_array($field->options)) foreach ($field->options as $option) { ?>
								<input type="text" name="fields[<?php echo $ndx; ?>][options][]" value="<?php echo esc_attr($option); ?>" />
							<?php } ?>
							<a href="#" class="new">new</a>
						<?php } ?>
					</td>
					
					<td class="delete"><a href="#">x</a></td>
				</tr>

			<?php } ?>
			
			<p class="tip">Tip: If you start a field's name with an underscore (_), then it will be hidden from the "Custom Fields" meta box.</p>

			<tbody class="postyper_template" rel="row">
				<tr>
					<td class="label">
						<input type="hidden" name="fields[new][postype_field_id]" />
						<input type="text" name="fields[new][label]"  />
					</td>

					<td class="name">
						<input type="text" name="fields[new][name]" />
					</td>

					<td class="type">
						<select name="fields[new][type]">
							<?php foreach ($this->field_types as $type => $attrs) { ?>
								<option value="<?php echo esc_attr($attrs['className']); ?>">
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
					
					<td class="delete"><a href="#">x</a></td>
				</tr>
			</tbody>

		</table>

		<div class="postyper_template" rel="radio">
			<input type="radio" /><label />
		</div>
		
		<p class="submit"><input type="button" class="postyper_add_field" value="add field" /></p>
		<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Changes" /></p>
		<p class="submit"><input type="submit" name="delete" class="button-secondary postyper_delete_postype" value="Delete Postype" /></p>
	</form>
</div>
