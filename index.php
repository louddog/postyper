<?php
/*
Plugin Name: Postyper
Description: Create custom post types through the admin system.
Author: Loud Dog
Version: 0.1
Author URI: http://postyper.louddog.com/
*/

/* TOOD: Add version option for the plugin */

require_once(dirname(__FILE__).'/postype.php');

new Postyper();
class Postyper {
	var $postypes = array();
	var $postype = null;
	
	public static $types = array(
		'text',
		'int',
		'date',
		'time',
		'date-time',
		'time-range',
		'money',
		'radio',
		'select',
		'checkbox',
		'textarea',
		'slider',
		'range'
	);
	
	function __construct() {
		global $wpdb;
		$wpdb->postypes = $wpdb->prefix.'postypes';
		$wpdb->postype_fields = $wpdb->prefix.'postype_fields';
		
		$postype_ids = $wpdb->get_col("SELECT postype_id FROM $wpdb->postypes ORDER BY singular");
		foreach ($postype_ids as $postype_id) {
			$this->postypes[] = new Postype($postype_id);
		}
		
		register_activation_hook(__FILE__, array($this, 'activate'));
		register_deactivation_hook(__FILE__, array($this, 'deactivate'));
		add_action('admin_enqueue_scripts', array(&$this, 'includes'));
		add_action('admin_menu', array(&$this, 'admin_menu'));
		add_action('admin_init', array(&$this, 'save_postype'));
		add_action('admin_notices', array(&$this, 'admin_notices'));
	}
	
	function activate() {
		global $wpdb;
		
		$wpdb->query(
			"CREATE TABLE $wpdb->postypes (
				postype_id bigint(20) unsigned NOT NULL auto_increment,
				slug varchar(255) default NULL,
				archive varchar(255) default NULL,
				singular varchar(255) default NULL,
				plural varchar(255) default NULL,
				PRIMARY KEY  (postype_id),
				KEY slug (slug)
			) $wpdb->collate;"
		);
		
		$wpdb->query(
			"CREATE TABLE $wpdb->postype_fields (
				postype_field_id bigint(20) unsigned NOT NULL auto_increment,
				postype_id bigint(20) unsigned NOT NULL,
				name varchar(255) default NULL,
				type varchar(255) default NULL,
				label varchar(255) default NULL,
				description longtext NOT NULL,
				options longtext NOT NULL,
				PRIMARY KEY  (postype_field_id),
				KEY postype_id (postype_id)
			) $wpdb->collate;"
		);
	}
	
	function deactivate() {
		global $wpdb;
		$wpdb->query("DROP TABLE $wpdb->postypes");
		$wpdb->query("DROP TABLE $wpdb->postype_fields");
	}
	
	function includes() {
		$dir = plugin_dir_url(__FILE__);
		
		wp_register_script('postyper_settings', $dir.'js/settings.js', array('jquery'), '0.1', true);
		wp_enqueue_script('postyper_settings');
		
		wp_register_style('postyper_settings', $dir.'css/settings.css', false, '0.1');
		wp_enqueue_style('postyper_settings');
	}

	function admin_menu() {
		add_menu_page(
			_x("Postyper Settings", 'admin settings page title'),
			_x("Postyper", 'postyper admin menu title'),
			'manage_options',
			'postyper',
			array($this, 'settings')
		);

		add_submenu_page(
			'postyper',
			"Postyper | New Post Type",
			"New Post Type",
			'manage_options',
			'postyper-new',
			array($this, 'postype_settings')
		);
		
		foreach ($this->postypes as $postype) {
			add_submenu_page(
				'postyper',
				"Postyper | ".$postype->singular,
				$postype->singular,
				'manage_options',
				'postyper_'.$postype->slug,
				array($this, 'postype_settings')
			);
		}
	}
	
	function settings() { ?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br /></div>
			<h2>Postyper</h2>

			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		</div>
	<?php }
	
	function postype_settings() { ?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br /></div>
			<h2>Custom Post Type: <em><?php echo $this->postype->singular; ?></em></h2>

			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

				<?php wp_nonce_field(plugin_basename(__FILE__), 'postyper_save_nonce'); ?>

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
	<?php }
	
	function save_postype() {
		if (!isset($_GET['page'])) return;
		
		$this->postype = new Postype(str_replace('postyper_', '', $_GET['page']));

		if (
			isset($_POST['postyper_save_nonce']) &&
			wp_verify_nonce($_POST['postyper_save_nonce'], plugin_basename(__FILE__))
		) {
			$post = stripslashes_deep($_POST);	
			
			$this->postype->slug = trim($post['slug']);
			$this->postype->archive = trim($post['archive']);
			$this->postype->singular = trim($post['singular']);
			$this->postype->plural = trim($post['plural']);
			$this->postype->fields = array();

			if (is_array($post['fields'])) {
				foreach ($post['fields'] as $field) {
					$options = array();
					if (isset($field['options'])) {
						foreach ($field['options'] as $option) {
							$option = trim($option);
							if (!empty($option)) $options[] = $option;
						}
					}
					
					$this->postype->fields[] = (object) array(
						'postype_field_id' => is_numeric($field['id']) ? $field['id'] : false,
						'label' => trim($field['label']),
						'name' => trim($field['name']),
						'type' => $field['type'],
						'description' => trim($field['description']),
						'options' => $options,
					);
				}
			}
			
			$this->postype->save();
			$this->add_admin_notice($this->postype->singular." postype saved");
			// TODO: Why isn't this message displaying?
			wp_redirect($_SERVER['REQUEST_URI'], 302);
		}
		
		if ($_GET['page'] == 'postyper-new' && $this->postype->id) {
			wp_redirect(admin_url("admin.php?page=postyper_".$this->postype->slug), 302);
			exit;
		}
	}
	
	function add_admin_notice($notice) {
		$notices = get_option('postyper_notices', array());
		$notices[] = $notice;
		update_option('postyper_notices', $notices);
	}
	
	function admin_notices() {
		$notices = get_option('postyper_notices', array());
		if (count($notices)) {
			foreach ($notices as $notice) { ?>
				<div class="updated">
					<p><?php echo $notice; ?></p>
				</div>
			<?php }
			delete_option('postyper_notices');
			die;
		}
	}
}