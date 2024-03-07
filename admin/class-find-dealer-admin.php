<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://chakramanijoshi.com.np
 * @since      1.0.0
 *
 * @package    Find_Dealer
 * @subpackage Find_Dealer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Find_Dealer
 * @subpackage Find_Dealer/admin
 * @author     Chakramani <chakramanijoshi@gmail.com>
 */
class Find_Dealer_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('admin_menu', array($this, 'find_dealer_menu_page'));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Find_Dealer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Find_Dealer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'find-dealer') {
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/find-dealer-admin.css', array(), $this->version, 'all');
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Find_Dealer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Find_Dealer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/find-dealer-admin.js', array('jquery'), $this->version, false);
	}
	/**
	 * Register a find dealer page.
	 */
	public function find_dealer_menu_page()
	{
		add_menu_page(
			__('Find Dealer', 'find-dealer'),
			'Find Dealer',
			'manage_options',
			'find-dealer',
			'find_dealer_page',
			'dashicons-location-alt',
			6
		);
	}
}
/**
 * Display a find dealer page
 */
function find_dealer_page()
{
	if (isset($_POST['save'])) {
		$google_api_key = sanitize_text_field($_POST['google_api_key']);
		update_option('google_api_key', $google_api_key);

		echo '<div id="message" class="updated inline"><p><strong>Your settings have been saved.</strong></p></div>';
	}
?>
	<div class='container'>
		<div class='main'>
			<div class='main__header'>
				<h2>Find Dealer Setting</h2>
			</div>
			<div class='main__content'>
				<div class='main__settings-form'>
					<form method='post'>
						<label class='main__input-label'>Google API Key:</label>
						<input class='main__input' placeholder='Google API Key' type='text' name="google_api_key" value="<?php echo !empty(google_api_key()) ? google_api_key() : ''; ?>">
						<input type="submit" name="save" value="Save" class='btn main__save-button'>
					</form>
				</div>
			</div>
		</div>
	</div>

<?php
}

function google_api_key()
{
    $api_key = get_option('google_api_key');
    return $api_key;
}