<?php
	$postype = new Postype(str_replace('postyper_', '', $_GET['page']));
	
	if (
		isset($_POST['postyper_meta_nonce']) &&
		wp_verify_nonce($_POST['postyper_meta_nonce'], plugin_basename(__FILE__))
	) {
		$postype->slug = trim($_POST['slug']);
		$postype->archive = trim($_POST['arvhice']);
		$postype->singular = trim($_POST['singular']);
		$postype->plural = trim($_POST['plural']);
		
		if (isset($_POST['field_id'])) {
			foreach ($_POST['field_id'] as $ndx => $id) {
				$postype->fields[] = array(
					'id' => $id == 'new' ? false : $id,
					'label' => trim($_POST['field_label'][$ndx]),
					'name' => trim($_POST['field_name'][$ndx]),
					'type' => $_POST['field_type'][$ndx],
					'description' => trim($_POST['field_description'][$ndx]),
				);
			}
		}
		
		$postype->save();
	}
?>

<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>Custom Post Type: <em><?php echo $postype->singular; ?></em></h2>

	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		
		<?php wp_nonce_field(plugin_basename(__FILE__), 'postyper_save_nonce'); ?>
		
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
	
		<h3>Fields</h3>
		
		<?php if (empty($postype->fields)) { ?>
			
			<p>There aren't any fields for this type.</p>
			
		<?php } else { ?>
		
			<table>
				<tr>
					<th>Title</th>
					<th>Name</th>
					<th>Type</th>
					<th>Options</th>
				</tr>
		
				<?php foreach ($postype->fields as $field) { ?>
					<tr>
						<td class="label">
							<input type="hidden" name="field_id[]" value="<?php echo $field->id; ?>" />
							<input type="text" name="field_label[]" value="<?php echo esc_attr($field->label); ?>" />
						</td>

						<td class="name">
							postyper_<input type="text" name="field_name[]" value="<?php echo esc_attr($field->name); ?>" />
						</td>

						<td class="type">
							<select name="field_type" id="postyer_type">
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
	</form>
</div>