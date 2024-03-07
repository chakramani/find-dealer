<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://chakramanijoshi.com.np
 * @since      1.0.0
 *
 * @package    Find_Dealer
 * @subpackage Find_Dealer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Find_Dealer
 * @subpackage Find_Dealer/public
 * @author     Chakramani <chakramanijoshi@gmail.com>
 */
class Find_Dealer_Public
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_shortcode('find-dealer', array($this, 'find_the_dealers'));
		add_action('update_dealer_table_lang', array($this, 'update_dealer_table_lang'));
		add_filter('cron_schedules', array($this, 'notification_time_intervals'));

		add_action('wp_ajax_get_lat_long_ajax_action', array($this, 'get_lat_logn_ajax_handler'));
		add_action('wp_ajax_nopriv_get_lat_long_ajax_action', array($this, 'get_lat_logn_ajax_handler'));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/find-dealer-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/find-dealer-public.js', array('jquery'), $this->version, false);
		wp_localize_script($this->plugin_name, 'frontend_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));
	}


	public function find_the_dealers()
	{
		ob_start();
		if(isset($_POST['zipcode'])) $zipcode = sanitize_text_field( $_POST['zipcode'] );
		$google_api_key = get_option('google_api_key'); ?>
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key; ?>&libraries=geometry&callback=findDealerInitMap"></script>

		<!-- HTML code here -->
		<div class="clark-find-dealer">
			<div class="clark-find-dealer-search">
			<?php the_title( '<h1>', '</h1>' ); ?>
				<div class="clark-find-dealer-search-input">
					<div id="clark-submit-map" class="clark-form-dealer">
						<label class="clark-search-label" for="clark-zip-code">Enter a Zip or Postal Code</label>
						<div class="input-group search">
							<input type="search" class="clark-input-text" id="clark-zip-code" maxlength="7" placeholder="Enter a Zip or Postal Code" name="find-dealer-zip-code" value="<?php echo isset($zipcode) ? $zipcode : ''; ?>">
							<span class="clark-dealer-search-icon search-button">
								<i class="fas fa-search"></i>
							</span>
						</div>
						<div class="clark-input-group-radio">
							<div class="clark-radio-btn">
								<input type="radio" id="ontario" name="clark-country-select" value="US" checked="checked">
								<label for="ontario">USA</label>
							</div>
							<div class="clark-radio-btn">
								<input type="radio" id="canada" name="clark-country-select" value="CA">
								<label for="canada">Canada</label>
							</div>
							<div class="clark-radio-btn">
								<input type="radio" id="mexico" name="clark-country-select" value="MX">
								<label for="mexico">Mexico</label>
							</div>
						</div>
						<select id="clark-dropdown-milezone" name="Milezone">
							<option value="30">30 miles</option>
							<option value="50">50 miles</option>
							<option value="75">75 miles</option>
							<option value="100">100 miles</option>
						</select>
						<div class="clark-error-msg" role="alert" aria-atomic="true">
							Enter a Zip or Postal Code
						</div>
						<div class="submit-group">
							<button type="button" class="btn submit-button" id="clark-submit-button">
								<span class="submit-button-text">Find a Dealer</span>
							</button>
						</div>
						<!-- <div class="clark-back-to-county">
						<span class="clark-back-arrow"></span>
						<span class="clark-back-link">Back</span>
					</div> -->
					</div>
					<div class="clark-loading-icon">
						<div class="clark-loader"></div>
					</div>
					<!-- <p class="map_description">A Clark Material Handling solution is just a few clicks away. Begin your search and find out why we are the best in the business.</p> -->
					<div class="clark-map-location-details">
					</div>

					<div class="clark-map-detalis-extend">

					</div>
					<div class="clark-results-not-found">
						<img class="clark-no-result-img" src="https://clarkmhcdev.mediawebdev.com/wp-content/uploads/2024/02/search-1.png" alt="No Results Found">
						<h3>No Results Found</h3>
						<p>Please enter another Zip or Postal Code.</p>
					</div>
				</div>
			</div>
			<div class="clark-dealer-map">
				<div id="find-dealer-map" style="height:100%; width:100%;"></div>
			</div>
		</div>
		<?php if(isset($_POST['zipcode'])){ ?>
<script>
	jQuery(document).ready(function(){
		jQuery("#clark-submit-button").click();
	});
</script>
<?php }
		$output = ob_get_contents();
		ob_get_clean();
		return $output;
	}

	public function update_dealer_table_lang()
	{
		global $wpdb;
		$query = "SELECT * FROM {$wpdb->prefix}dealers";
		$results = $wpdb->get_results($query);
		$google_api_key = get_option('google_api_key');

		foreach ($results as $result) {

			$countryCode = $result->country_name;
			$zipcode = $result->zipcode;
			$dealerName = $result->dealer_name;
			$address = $result->dealer_address;
			$stateCode = $result->state;
			$city = $result->city;

			// Base URL for Google Maps Geocoding API
			$baseUrl = 'https://maps.googleapis.com/maps/api/geocode/json?';

			// Build the address string
			$addressString = urlencode($dealerName . ', ' . $address . ', ' . $city . ', ' . $stateCode . ', ' . $zipcode . ', ' . $countryCode);
			$requestUrl = $baseUrl . 'address=' . $addressString . '&key=' . $google_api_key;
			$response = file_get_contents($requestUrl);
			$data = json_decode($response, true);
			if ($data['status'] == 'OK') {
				// Extract latitude and longitude
				$latitude = $data['results'][0]['geometry']['location']['lat'];
				$longitude = $data['results'][0]['geometry']['location']['lng'];
				$sql = $wpdb->prepare("UPDATE {$wpdb->prefix}dealers SET `latitude` = $latitude, `longitude` = $longitude WHERE `{$wpdb->prefix}dealers`.`sales_force_id` = '$result->sales_force_id'");
				// echo $sql . '<br />';
				$wpdb->query($sql);
			} else {
				return false;
			}
		}
	}

	public function notification_time_intervals($schedules)
	{
		$schedules['yearly'] = array(
			'interval' => 31540000,
			'display' => __('Once Year')
		);
		$schedules['monthly'] = array(
			'interval' => 2635200,
			'display' => __('Once a month')
		);
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display' => __('Once Weekly')
		);
		$schedules['once_in_2_days'] = array(
			'interval' => 172800,
			'display' => __('Once In 2 days')
		);
		$schedules['hourly'] = array(
			'interval' => 60,
			'display' => __('Once a hour')
		);
		return $schedules;
	}

	// AJAX handler function
	public function get_lat_logn_ajax_handler()
	{
		global $wpdb; // WordPress database access object
		$zipcode = $_POST['zipcode'];
		$countryCode = $_POST['country_code'];
		$google_api_key = get_option('google_api_key');
		$baseUrl = 'https://maps.googleapis.com/maps/api/geocode/json?';
		$res = [];

		// Build the address string
		$addressString = urlencode($zipcode . ', ' . $countryCode);
		$requestUrl = $baseUrl . 'address=' . $addressString . '&key=' . $google_api_key;
		$response = file_get_contents($requestUrl);
		$data = json_decode($response, true);

		// var_dump($data);
		if ($data['status'] == 'OK') {
			// Extract latitude and longitude
			$latitude = $data['results'][0]['geometry']['location']['lat'];
			$longitude = $data['results'][0]['geometry']['location']['lng'];
		}

		// Perform SQL query
		$results = $wpdb->get_results("SELECT dealer_name,dealer_address,url,email,phone_number,state,zipcode,city,latitude as lat,longitude as lng FROM {$wpdb->prefix}dealers WHERE `zipcode` = '$zipcode' ORDER BY `sales_force_id` ASC");
		array_push($res, $results);
		array_push($res, $latitude);
		array_push($res, $longitude);

		if (empty($res)) {
			wp_send_json([], 404);
		} else {
			wp_send_json($res, 200);
		}
		die();
	}
}
if (!wp_next_scheduled('update_dealer_table_lang')) {
	wp_schedule_event(time(), 'weekly', 'update_dealer_table_lang');
}
