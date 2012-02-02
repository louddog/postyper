<?php

postyper_register_field_type('PostypePost');

class PostypePost extends PostypeField {
	var $type = 'post';
	
	function output($post_id) {
		// TODO: Until we have the UI for it, we can't specify post type yet
		// $postype = $this->options['post_type'];
		$postype = 'employee';
		$postype = Postype::$postypes[$postype];
		$posts = get_posts("post_type=$postype->slug&numberofposts=-1");
		?>
		
		<select name="postype[<?php echo $this->name; ?>]">
			<option value=""></option>
			<?php foreach ($posts as $post) { ?>
				<option
					value="<?php echo $post->ID; ?>"
					<?php if ($this->output_value($post_id) == $post->ID) echo "selected"; ?>
				><?php echo get_the_title($post->ID); ?></option>
			<?php } ?>
		</select><br />

		<label for="postype_field_<?php echo $this->name; ?>">
			<?php echo empty($this->description) ? $this->label : $this->description; ?>
		</label>
		
	<?php }
}