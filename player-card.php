<?php
/*
Plugin Name: Player-card
Version: 1.0
Description: Add-on to Blubrry PowerPress to add a twitter player card on any post that includes an enclosure.
Author: Cal Evans
Author URI: http://blog.calevans.com
Plugin URI: http://blog.calevans.com
Text Domain: player-card
Domain Path: /languages
*/

register_activation_hook( __FILE__, ['PowerpressPlayerCard','activate'] );
$pppc = new PowerpressPlayerCard();


/*
 * Process the form
 */
if (isset($_POST['action']) and $_POST['action'] === 'update') {   
	$pppc->process_form();
}


Class PowerpressPlayerCard {


	public function __construct() {
		if ( ! function_exists( 'is_plugin_inactive' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}


		if (is_plugin_inactive('powerpress/powerpress.php')) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}

		$this->init();		
	}


	function init() {
		add_action( 'wp_head', [$this,'main'] );
		add_action( 'admin_enqueue_scripts', [$this,'enqueue_scripts'] );
        add_action( 'admin_menu', [$this,'admin_menu'],11 );
		return;		
	}


	public function main() {
		$post = $this->fetch_post_options();	

		/*
		 * Check to see if we are in a post. If not, bail.
		 */
		if (empty($post)) {
			echo "\n<!-- No Post -->\n";
			return;
		}

		$media_file = $this->locate_media_file($post);

		if (empty($media_file)) {
			echo "\n<!-- No Media File -->\n";
			return;
		}

		$media_file_type = $this->identify_media_type($post);

		$plugin_options = get_option('pppc_plugin_options');
		
		if (empty($plugin_options) or 
			(isset($plugin_options['twitter_account']) and empty($plugin_options['twitter_account']))) {
			echo "\n<!-- Plugin Not properly setup -->\n";			
			return;
		}
		
		$card = $this->buildCard($plugin_options, $media_file, $post, $media_file_type);
		echo $card;

		return;
	}

	public function buildCard( $plugin_options, $media_file, $post, $media_file_type ) {

		$returnValue = '';
		$returnValue .= "\n<!-- Begin Cal's Twitter Player Card Insert-a-tron -->\n";
		$returnValue .= '<meta name="twitter:card" content="player">' ."\n";
		$returnValue .= '<meta name="twitter:site" content="'. $plugin_options['twitter_account'] .'">' ."\n";
		$returnValue .= '<meta name="twitter:title" content="'. $plugin_options['title'] .'">' ."\n";
		$returnValue .= '<meta name="twitter:description" content="' . $post['post']->post_title . '">' ."\n";
		$returnValue .= '<meta name="twitter:image" content="'. $plugin_options['default_graphic'] .'">' ."\n";
		$returnValue .= '<meta name="twitter:player" content="' . plugins_url('player-card') . '/container.php?a=' . urlencode($media_file) . '&t=' . $media_file_type .'">' ."\n";
		$returnValue .= '<meta name="twitter:player:width" content="'. $plugin_options['player_width'] .'">' ."\n";
		$returnValue .= '<meta name="twitter:player:height" content="'. $plugin_options['player_height'] .'">' ."\n";
		$returnValue .= "<!-- End Cal's Twitter Player Card Insert-a-tron -->\n\n";

		return $returnValue;

	}

	public function init_option() {
		$returnValue = array();
		$returnValue['twitter_account'] = '';
		$returnValue['title']           = '';
		$returnValue['player_height']   = 150;
		$returnValue['player_width']    = 20;
		$returnValue['default_graphic'] = '';

		return $returnValue;
	}


	public function load_options() {
	    $returnValue = get_option('pppc_plugin_options');

	    if (empty($returnValue)) {
	    	$returnValue = $this->init_option();
	    }

	    return $returnValue; 
	}



	public function locate_media_file($post) {

		$returnValue = '';

		if (!empty($post['enclosure'])) {
			$returnValue = explode("\n",$post['enclosure'])[0];
			$returnValue = str_replace('http://', 'https://', $returnValue);
		}

		return $returnValue;
	}


	public function identify_media_type($post) {

		$returnValue = 'audio';

		if (!empty($post['enclosure'])) {
			$media_type = explode("\n",$post['enclosure'])[2];
		}

		if ( $media_type === 'audio/mpeg' ) { 
			$returnValue = 'audio';
		} else if ( $media_type === 'video/mp4' ) {
			$returnValue = 'video';	
		} 
echo "\n<!-- media type = " . $returnValue . "-->";
		return $returnValue;
	}


	public function fetch_post_options() {
		global $wp_query;

		$returnValue = array();

		if (isset($wp_query->posts[0])) {
			$returnValue['post']      = $wp_query->posts[0];
			$returnValue['enclosure'] = get_post_meta($wp_query->posts[0]->ID, 'enclosure');

			if (is_array($returnValue['enclosure'])) {
				$returnValue['enclosure'] = $returnValue['enclosure'][0];
			}

		}

		return $returnValue;
	}

	public function admin_menu() {
		add_submenu_page('powerpress/powerpressadmin_basic.php', 'Player Card Options', 'Player Card', POWERPRESS_CAPABILITY_EDIT_PAGES, __FILE__, [$this,'admin_options_page']);
	}


	public function admin_options_page() {
		$plugin_options = $this->load_options();
		include 'options_page.php';
		return;
	}

	public function process_form() {
		$current_options = $this->load_options();
		$current_options['twitter_account'] = filter_input(INPUT_POST, 'twitter_account', FILTER_SANITIZE_STRING);
		$current_options['title']           = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
		$current_options['player_height']   = filter_input(INPUT_POST, 'player_height', FILTER_SANITIZE_NUMBER_INT);
		$current_options['player_width']    = filter_input(INPUT_POST, 'player_width', FILTER_SANITIZE_NUMBER_INT);
		$current_options['default_graphic'] = filter_input(INPUT_POST, 'default_graphic', FILTER_SANITIZE_STRING);

		update_option('pppc_plugin_options',$current_options);
	}

	public function enqueue_scripts() {
	    wp_enqueue_media();
	    wp_register_script('default_uploader', plugins_url( 'default_uploader.js', __FILE__ ), array('jquery'));
	    wp_enqueue_script('default_uploader');
	    return;
	}


	public static function activate() {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_list = get_plugins();

		if (!isset($plugin_list['powerpress/powerpress.php']) or is_plugin_inactive('powerpress/powerpress.php')) {
			exit('<div class="error"><p>This plugin requires Blubrry PowerPress to operate. Please install that first and then activate this plugin.</p></div>');
		}

		$plugin_options = Self::load_options('pppc_plugin_options');
		update_option('pppc_plugin_options',$plugin_options);

		return;
	}

}

