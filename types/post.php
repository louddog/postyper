<?php

Postyper::register_field_type('PostypePost');

class PostypePost extends PostypeField {
	var $type = 'post';
	var $settings = array(
		'post_type' => array(
			'options' => array(), // filled with defined post types in constructor
			'value' => 'post',
		)
	);
	
	function __construct($options = array()) {
		$this->add_post_type_options();
		parent::__construct($options);
	}
	
	var $wp_loaded = false;
	function add_post_type_options() {
		if ($this->wp_loaded) $this->_add_post_type_options();
		else add_action('wp_loaded', array(&$this, '_add_post_type_options'));
	}
	
	function _add_post_type_options() {
		foreach (get_post_types(array(), 'objects') as $slug => $type) {
			if (in_array($slug, array('revision', 'nav_menu_item'))) continue;
			$this->settings['post_type']['options'][$slug] = $type->labels->singular_name;
		}
	}
	
	function output($post_id) {
		$slug = $this->settings['post_type']['value'];
		$posts = get_posts("post_type=$slug&numberofposts=-1");
		$id = $this->output_value($post_id);
		?>
		
		<select name="postype[<?php echo $this->name; ?>]">
			<option value=""></option>
			<?php foreach ($posts as $post) { ?>
				<option
					value="<?php echo $post->ID; ?>"
					<?php if ($id == $post->ID) echo "selected"; ?>
				><?php echo get_the_title($post->ID); ?></option>
			<?php } ?>
		</select><br />

		<label for="postype_field_<?php echo $this->name; ?>">
			<?php echo empty($this->description) ? $this->label : $this->description; ?>
		</label>

		<?php if (has_post_thumbnail($id)) echo "<br />".get_the_post_thumbnail($id, 'thumbnail'); ?>
		
	<?php }
}