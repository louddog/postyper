<fieldset class="postyper_fields">
	<table>
		<?php foreach($fields as $name => $field) { ?>
			<?php
				$value = false;
				if (isset($db_meta[$name])) $value = $db_meta[$name];
				else if (isset($field['default']) && $field['default']) $value = $field['default'];

				$label = isset($field['label']) ? $field['label'] : $name;

				$name = "postyper_$name";
			?>
			
			<tr>
				<td class="label">
					<label for="<?php echo $name; ?>">
						<?php echo $label; ?>
					</label>
				</td>
				
				<td class="input">
					<?php if ($field['type'] == 'text') { ?>
						<input
							class="text"
							type="text"
							name="<?php echo $name; ?>"
							id="<?php echo $name; ?>"
							value="<?php echo esc_attr($value); ?>"
						/>
					<?php } else if ($field['type'] == 'int') { ?>
						<input
							class="int"
							type="text"
							name="<?php echo $name; ?>"
							id="<?php echo $name; ?>"
							value="<?php echo esc_attr($value); ?>"
						/>
					<?php } else if ($field['type'] == 'date-time') { ?>
						<input
							class="date"
							type="text"
							name="<?php echo $name; ?>[date]"
							id="<?php echo $name.'_date'; ?>"
							value="<?php if ($value) echo date('n/j/Y', $value); ?>"
							placeholder="mm/dd/yyyy"
						/>
						<input
							class="time"
							type="text"
							name="<?php echo $name; ?>[time]"
							id="<?php echo $name.'_time'; ?>"
							value="<?php if ($value) echo date('g:ia', $value); ?>"
							placeholder="hh:mm am"
						/>
					<?php } else if ($field['type'] == 'money') { ?>
						<input
							class="money"
							type="text"
							name="<?php echo $name; ?>"
							id="<?php echo $name; ?>"
							value="<?php echo esc_attr($value); ?>"
						/>
					<?php } else if ($field['type'] == 'select') { ?>
						<select
							name="<?php echo $name; ?>"
							id="<?php echo $name; ?>"
						>
							<option value=""></option>
							<?php foreach ($field['options'] as $val => $text) { ?>
								<option
									value="<?php echo $val; ?>"
									<?php if ($val == $value) echo 'selected'; ?>>
									<?php echo $text; ?>
								</option>
							<?php } ?>
						</select>
					<?php } else if ($field['type'] == 'textarea') { ?>
						<textarea
							name="<?php echo $name; ?>"
							id="<?php echo $name; ?>"
						><?php echo $value; ?></textarea>
					<?php } else if ($field['type'] == 'boolean') { ?>
						<input
							type="checkbox"
							name="<?php echo $name; ?>"
							id="<?php echo $name; ?>"
							<?php if ($value) echo 'checked'; ?>
						/>
					<?php } else echo 'unknown option type'; ?>
				</td>
			</tr>
		<?php } ?>
	</table>
</fieldset>