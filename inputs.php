<?php // defined: $post, $field, $name, $value, $desc ?>

<?php if ($field['type'] == 'text') { ?>

	<input
		type="text"
		name="<?php echo $name; ?>"
		id="<?php echo $name; ?>"
		value="<?php echo esc_attr($value); ?>"
	/>

	<?php echo $desc; ?>

<?php } else if ($field['type'] == 'int') { ?>

	<input
		type="text"
		name="<?php echo $name; ?>"
		id="<?php echo $name; ?>"
		value="<?php echo esc_attr($value); ?>"
		style="width: 50px;"
	/>

	<?php echo $desc; ?>

<?php } else if ($field['type'] == 'date') { ?>

	<input
		type="text"
		class="postyper_date"
		name="<?php echo $name; ?>"
		id="<?php echo $name; ?>"
		value="<?php if (is_numeric($value)) echo esc_attr(date('n/j/Y', $value)); ?>"
		placeholder="mm/dd/yyyy"
	/>

	<?php echo $desc; ?>

<?php } else if ($field['type'] == 'time') { ?>

	<input
		type="text"
		name="<?php echo $name; ?>"
		id="<?php echo $name; ?>"
		value="<?php if (is_numeric($value)) echo esc_attr(date('g:ia', $value)); ?>"
		placeholder="hh:mm am"
	/>

	<?php echo $desc; ?>

<?php } else if ($field['type'] == 'date-time') { ?>

	<input
		type="text"
		class="postyper_date"
		name="<?php echo $name; ?>[date]"
		id="<?php echo $name.'__date'; ?>"
		value="<?php if (is_numeric($value)) echo esc_attr(date('n/j/Y', $value)); ?>"
		placeholder="mm/dd/yyyy"
	/>
	<input
		type="text"
		name="<?php echo $name; ?>[time]"
		id="<?php echo $name.'__time'; ?>"
		value="<?php if (is_numeric($value)) echo esc_attr(date('g:ia', $value)); ?>"
		placeholder="hh:mm am"
	/>

	<?php echo $desc; ?>

<?php } else if ($field['type'] == 'time-range') { ?>
	
	<input
		type="text"
		name="<?php echo $name; ?>[starts]"
		id="<?php echo $name.'__starts'; ?>"
		value="<?php if (isset($value['starts']) && is_numeric($value['starts'])) echo esc_attr(date('g:ia', $value['starts'])); ?>"
		placeholder="hh:mm am"
	/>
	<input
		type="text"
		name="<?php echo $name; ?>[ends]"
		id="<?php echo $name.'__ends'; ?>"
		value="<?php if (isset($value['ends']) && is_numeric($value['ends'])) echo esc_attr(date('g:ia', $value['ends'])); ?>"
		placeholder="hh:mm am"
	/>

	<?php echo $desc; ?>

<?php } else if ($field['type'] == 'money') { ?>

	<input
		type="text"
		name="<?php echo $name; ?>"
		id="<?php echo $name; ?>"
		value="<?php echo esc_attr($value); ?>"
	/>

	<?php echo $desc; ?>

<?php } else if ($field['type'] == 'select') { ?>

	<select
		name="<?php echo $name; ?>"
		id="<?php echo $name; ?>"
	>
		<option value=""></option>
		<?php foreach ($field['options'] as $val => $text) { ?>
			<option
				value="<?php echo esc_attr($val); ?>"
				<?php if ($val == $value) echo 'selected'; ?>
			>
				<?php echo $text; ?>
			</option>
		<?php } ?>
	</select>
	
	<?php echo $desc; ?>

<?php } else if ($field['type'] == 'radio') { ?>

	<?php $first = true; ?>

	<?php foreach ($field['options'] as $val => $text) { ?>
		<?php
			if ($first) $first = false;
			else echo "<br />";
		?>

		<input
			type="radio"
			name="<?php echo $name; ?>"
			id="<?php echo esc_attr($name.'_'.$val); ?>"
			value="<?php echo esc_attr($val); ?>"
			<?php if ($val == $value) echo "checked"; ?>
		/>
		<label for="<?php echo esc_attr($name.'_'.$val); ?>">
			<?php echo $text; ?>
		</label>
	<?php } ?>
	
	<?php echo $desc; ?>

<?php } else if ($field['type'] == 'textarea') { ?>

	<textarea
		name="<?php echo $name; ?>"
		id="<?php echo $name; ?>"
		rows="4"
		style="width: 98%;"
	><?php echo $value; ?></textarea>

	<?php echo $desc; ?>

<?php } else if ($field['type'] == 'checkbox') { ?>

	<input
		type="checkbox"
		name="<?php echo $name; ?>"
		id="<?php echo $name; ?>"
		<?php if ($value) echo 'checked'; ?>
	/>

	<?php if (isset($field['desc']) && !empty($field['desc'])) { ?>
		<label for="<?php echo $name; ?>">
			<?php echo $field['desc']; ?>
		</label>
	<?php } ?>

<?php } else if ($field['type'] == 'slider') { ?>
	
	<?php
		$min = isset($field['min']) ? $field['min'] : 0;
		$max = isset($field['max']) ? $field['max'] : 100;
	?>

	<input
		type="hidden"
		name="<?php echo $name; ?>__min"
		id="<?php echo $name; ?>__min"
		value="<?php echo $min; ?>"
	/>
	
	<input
		type="hidden"
		name="<?php echo $name; ?>__max"
		id="<?php echo $name; ?>__max"
		value="<?php echo $max; ?>"
	/>
	
	<input
		type="text"
		name="<?php echo $name; ?>"
		id="<?php echo $name; ?>"
		value="<?php echo empty($value) ? $min : $value; ?>"
	/>
	
	<div class="postyper_slider" rel="<?php echo $name; ?>"></div>
		
	<?php echo $desc; ?>

<?php } else if ($field['type'] == 'range') { ?>
	
	<?php
		$min = isset($field['min']) ? $field['min'] : 0;
		$max = isset($field['max']) ? $field['max'] : 100;
	?>
	
	<input
		type="hidden"
		name="<?php echo $name; ?>[min]"
		id="<?php echo $name; ?>__min"
		value="<?php echo $min; ?>"
	/>
	
	<input
		type="hidden"
		name="<?php echo $name; ?>[max]"
		id="<?php echo $name; ?>__max"
		value="<?php echo $max; ?>"
	/>
	
	<input
		type="text"
		name="<?php echo $name; ?>[low]"
		id="<?php echo $name; ?>__low"
		value="<?php echo isset($value['low']) ? $value['low'] : $min; ?>"
	/>
	
	<input
		type="text"
		name="<?php echo $name; ?>[high]"
		id="<?php echo $name; ?>__high"
		value="<?php echo isset($value['high']) ? $value['high'] : $max; ?>"
	/>
	
	<div
		class="postyper_range"
		rel="<?php echo $name; ?>"
	></div>
		
	<?php echo $desc; ?>

<?php } ?>