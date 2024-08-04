<?php
/**
 * WP Leaflet Maps Pro
 *
 * @package wp-leaflet-maps-pro
 * @author Flipper Code <hello@flippercode.com>
 * @copyright 2019 flippercode
 * 
 * @wordpress-plugin
 * Plugin Name: WP Leaflet Maps Pro
 * Plugin URI: http://www.flippercode.com/
 * Description: The most advanced plugin to create simple to complex maps based on world's most trusted open-source JavaScript library Leaflet. Create maps with OpenStreetMap, MapBox, MapQuest & Bing.
 * Version: 1.0.9
 * Author: 20script
 * Author URI: http://www.20script.ir
 * Text Domain: wp-leaflet-maps-pro
 * Domain Path: /lang/
 */
 
if ( ! class_exists( 'FC_Plugin_Base' ) ) {
	$pluginClass = plugin_dir_path( __FILE__ ) . '/core/class.plugin.php';
	if ( file_exists( $pluginClass ) ) {
		include( $pluginClass );
	}
}

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( ! class_exists( 'WPLMP_Maps_Pro' ) ) {

	/**
	 * Main plugin class
	 *
	 * @author Flipper Code <hello@flippercode.com>
	 * @package wp-leaflet-maps-pro
	 */
	class WPLMP_Maps_Pro {

		/**
		 * List of Modules.
		 *
		 * @var array
		 */

		private $modules = array();

		/**
		 * Intialize variables, files and call actions.
		 *
		 * @var array
		 */

		public function __construct() {

			$this->wplmp_define_constants();
			$this->wplmp_load_files();
			$this->wplmp_register_hooks();
		}

		function wplmp_register_hooks(){

			register_activation_hook( __FILE__,   array( $this, 'wplmp_plugin_activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'wplmp_plugin_deactivation' ) );

			if ( is_multisite() ) {
				add_action( 'wpmu_new_blog',      array( $this, 'wplmp_on_blog_new_generate' ), 10, 6 );
				add_filter( 'wpmu_drop_tables',   array( $this, 'wplmp_on_blog_delete' ) );
			}

			add_action( 'plugins_loaded',         array( $this, 'wplmp_load_plugin_languages' ) );
			add_action( 'init', 				  array( $this, 'wplmp_on_init' ) );
			add_action( 'widgets_init', 		  array( $this, 'wplmp_google_map_widget' ) );
			add_filter( 'fc-dummy-placeholders',  array( $this, 'wplmp_apply_placeholders' ) );

		}
	

		function wplmp_on_init() {

			add_action( 'wp_enqueue_scripts', array( $this, 'wplmp_frontend_scripts' ) );
			add_action( 'wp_ajax_wplmp_ajax_call', array( $this, 'wplmp_ajax_call' ) );
			add_action( 'wp_ajax_nopriv_wplmp_ajax_call', array( $this, 'wplmp_ajax_call' ) );

			if ( is_admin() ) {

				$this->wplmp_check_vc_depenency();
     			add_action( 'admin_menu', array( $this, 'wplmp_create_menu' ) );
				add_action( 'media_upload_ell_insert_gmop_tab', array( $this, 'wplmp_google_map_media_upload_tab' ) );
				add_action( 'admin_print_scripts', array( $this, 'wplmp_backend_styles' ) );
				add_action( 'admin_init', array( $this, 'wplmp_export_data' ) );
				add_action( 'add_meta_boxes', array( $this, 'wplmp_call_meta_box' ) );
				add_action( 'save_post', array( $this, 'wplmp_save_meta_box_data' ) );
				add_filter( 'media_upload_tabs', array( $this, 'wplmp_google_map_tabs_filter' ) );
				add_action( 'admin_head', array($this,'wplmp_customizer_font_family' ));
			}

			add_shortcode( 'put_wplmp_map', array( $this, 'wplmp_show_location_in_map' ) );
			add_shortcode( 'wplmp_display_map', array( $this, 'wplmp_display_map' ) );
		}

		function wplmp_check_vc_depenency() {

			if( class_exists( 'WPLMP_MODEL_General' ) ){

				$generalModule = new WPLMP_MODEL_General();
				$generalModule->wplmp_check_vc_depenency();

			}

		}
	
		function wplmp_apply_placeholders( $content ) {

			if( class_exists( 'WPLMP_MODEL_General' ) ){

				$generalModule = new WPLMP_MODEL_General();
				$content = $generalModule->wplmp_apply_placeholders($content);

			}

			return $content;

		}

		/**
		 * Register WP Leaflet Maps Widget
		 */

		function wplmp_google_map_widget() { register_widget( 'WPLMP_Osm_Map_Widget_Class' ); }

		/**

		 * Display WP Leaflet Maps meta box on pages/posts and custom post type(s).

		 */

		function wplmp_call_meta_box() {
    		$screens        = array( 'post', 'page' );
			$wpomp_settings = get_option( 'wpomp_settings', true );
			$args = array(	'public'   => true,	'_builtin' => false );
			$custom_post_types = get_post_types( $args, 'names' );
			$screens = array_merge( $screens, $custom_post_types );
			$screens = apply_filters( 'wpomp_meta_boxes', $screens );
			$selected_values = isset($wpomp_settings['wpomp_allow_meta']) ? maybe_unserialize( $wpomp_settings['wpomp_allow_meta'] ) :'';

			foreach ( $screens as $screen ) {

				if ( is_array( $selected_values ) ) {
					if ( in_array( $screen, $selected_values ) or in_array( 'all', $selected_values ) ) {
						continue;
					}
				}

				add_meta_box(
					'wpomp_google_map_metabox',
					esc_html__( 'WP Leaflet Maps Pro', 'wp-leaflet-maps-pro' ),
					array( $this, 'wplmp_add_meta_box' ),
					$screen
				);

			}

		}

		/**
		 * Callback to display WP Leaflet Maps pro meta box.
		 *
		 * @param  string $post Post Type.
		 */

		function wplmp_add_meta_box( $post ) {

			if( class_exists( 'WPLMP_MODEL_General' ) ){
				$generalModule = new WPLMP_MODEL_General();
				$generalModule->wplmp_add_meta_box($post);
			}
		}

		/**
		 * Save meta box data
		 *
		 * @param  int $post_id Post ID.
		 */

		function wplmp_save_meta_box_data( $post_id ) {

			if( class_exists( 'WPLMP_MODEL_General' ) ){

				$generalModule = new WPLMP_MODEL_General();
				$generalModule->wplmp_save_meta_box_data($post_id);

			}

		}

		/**
		 * Enqueue scripts at frontend.
		 */

		function wplmp_frontend_scripts() {

			$wpomp_settings = get_option( 'wpomp_settings', true );
			$auto_fix = isset($wpomp_settings['wpomp_auto_fix']) ? $wpomp_settings['wpomp_auto_fix'] :false ;
			$scripts = array();

			if ( $auto_fix == 'true' ) {
				wp_enqueue_script( 'jquery' );
			}

			$language = 'en';
			$language = apply_filters( 'wpomp_map_lang', $language );

			$scripts[] = array(
					'handle' => 'jscrollpane',
					'src'    => WPLMP_JS . 'vendor/jscrollpane/jscrollpane.js',
					'deps'   => array(),
			);

			$scripts[] = array(
				'handle' => 'accordion-script',
				'src'    => WPLMP_JS . 'vendor/accordion/accordion.js',
				'deps'   => array(),
			);

			$scripts[] = array(
				'handle' => 'webfont',
				'src'    => WPLMP_JS . 'vendor/webfont/webfont.js',
				'deps'   => array(),
			);


			if( isset( $wpomp_settings['wpomp_scripts_minify']) && $wpomp_settings['wpomp_scripts_minify'] == 'yes') {

				$scripts[] = array(
					'handle' => 'wplmp_frontend',
					'src'    => WPLMP_JS . 'frontend.min.js',
					'deps'   => array('jscrollpane','jquery-masonry', 'imagesloaded','accordion-script' ),
				);

			} else {

				$scripts[] = array(

					'handle' => 'wplmp_frontend',
					'src'    => WPLMP_JS . 'frontend.js',
					'deps'   => array('jscrollpane','jquery-masonry', 'imagesloaded','accordion-script' ),

				);

			}

			$where = isset($wpomp_settings['wpomp_scripts_place'])? $wpomp_settings['wpomp_scripts_place'] : '';
			if ( $where == 'header' ) {
				$where = false;

			} else {
				$where = true;
			}

			if (isset( $wpomp_settings['wpomp_gdpr']) && $wpomp_settings['wpomp_gdpr'] == true ) {
				$auto_fix = apply_filters( 'wpomp_accept_cookies', false );
			}

			if ( $scripts ) {
				foreach ( $scripts as $script ) {
					if ( $auto_fix == 'true' ) {
					   wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], WPLMP_VERSION, $where );

					} else {
						wp_register_script( $script['handle'], $script['src'], $script['deps'], WPLMP_VERSION, $where );
					}
				}
			}
     		$wpomp_fjs_lang                     = array();
			$wpomp_fjs_lang['ajax_url']         = admin_url( 'admin-ajax.php' );
			$wpomp_fjs_lang['nonce']            = wp_create_nonce( 'fc-call-nonce' );
			wp_localize_script( 'wplmp_frontend', 'wpomp_flocal', $wpomp_fjs_lang );
			$wpomp_local = array();
			if( class_exists( 'WPLMP_MODEL_General' ) ){
				$generalModule = new WPLMP_MODEL_General();
				$wpomp_local = $generalModule->wplmp_get_frontend_localized_data();
			}
			$wpomp_local['accesstoken'] = isset($wpomp_settings['wpomp_api_key']) ? $wpomp_settings['wpomp_api_key'] : '';
			$wpomp_local['wpomp_bingmap_key'] = isset($wpomp_settings['wpomp_bingmap_key']) ? $wpomp_settings['wpomp_bingmap_key'] : '' ;


			$wpomp_local  = apply_filters( 'wpomp_text_settings', $wpomp_local );
			$scripts = array();
			$wpomp_apilocation = WPLMP_JS.'leaflet.js';
		

			$scripts[] = array(
			'handle'  => 'wplmp_osm_api',
			'src'   => $wpomp_apilocation,
			'deps'    => array(),
			);

			$scripts[] = array(
				'handle' => 'datatable',
				'src'    => WPLMP_JS . 'vendor/datatables/datatables.js',
				'deps'   => array(),
			);

			$scripts[] = array(
			'handle'  => 'leaflet-autocomplete',
			'src'   => WPLMP_PLUGINS.'autocomplete/leaflet-autocomplete.js',
			'deps'    => array(),
			);	


			$scripts[] = array(
			'handle'  => 'L.Control.Locate',
			'src'   => WPLMP_PLUGINS.'locate/L.Control.Locate.js',
			'deps'    => array('wplmp_osm_api'),
			);

			$scripts[] = array(
			'handle'  => 'Leaflet.fullscreen.min',
			'src'   => WPLMP_PLUGINS.'fullscreen/Leaflet.fullscreen.min.js',
			'deps'    => array('wplmp_osm_api'),
			);


			$scripts[] = array(
			'handle'  => 'leaflet-providers',
			'src'   => WPLMP_PLUGINS.'provider/leaflet-providers.js',
			'deps'    => array('wplmp_osm_api'),
			);

			if(!empty($wpomp_settings['wpomp_mapquest_key'])){
				$map_quest_key    = $wpomp_settings['wpomp_mapquest_key'];
				$mapquest_sdk_url = 'https://www.mapquestapi.com/sdk/leaflet/v2.2/mq-map.js?key=%s';
				$mapquest_sdk_url = sprintf($mapquest_sdk_url, $map_quest_key);
				$scripts[] = array(
					'handle' => 'mapquest',
					'src'    => $mapquest_sdk_url,
					'deps'   => array('wplmp_osm_api'),
				);
			}

			if(!empty($wpomp_settings['wpomp_bingmap_key'])){

				$scripts[] = array(
					'handle' => 'Bing',
					'src'    => WPLMP_JS . 'Bing.js',
					'deps'   => array(),
				);
			}

			if( isset( $wpomp_settings['wpomp_scripts_minify']) && $wpomp_settings['wpomp_scripts_minify'] == 'yes') {

				$scripts[] = array(
					'handle' => 'wplmp_google_map_main',
					'src'    => WPLMP_JS . 'maps.min.js',
					'deps'   => array('datatable', 'jquery-masonry', 'imagesloaded' ),
				);	

			} else {

				$scripts[] = array(
					'handle' => 'wplmp_google_map_main',
					'src'    => WPLMP_JS . 'maps.js',
					'deps'   => array('datatable', 'jquery-masonry', 'imagesloaded'),
				);
			}	

			if(!empty($wpomp_settings['wpomp_api_key'])){

				$scripts[] = array(
					'handle' => 'mapbox_script',
					'src'    => WPLMP_JS . 'mapbox.js',
					'deps'   => array(),
				);
			}

					

			if ( $scripts ) {

				foreach ( $scripts as $script ) {

					if ( $auto_fix == 'true' ) {
						wp_register_script( $script['handle'], $script['src'], $script['deps'], WPLMP_VERSION, $where );
					} else {
						wp_register_script( $script['handle'], $script['src'], $script['deps'], WPLMP_VERSION, $where );
					}
				}
			}

			if ( isset( $wpomp_settings['wpomp_country_specific'] ) ) {
				$wpomp_local['wpomp_country_specific'] = ( $wpomp_settings['wpomp_country_specific'] == 'true' );
			} else {
				$wpomp_local['wpomp_country_specific'] = false;
			}

			if ( isset( $wpomp_settings['wpomp_countries'] ) ) {
				$wpomp_local['wpomp_countries'] = $wpomp_settings['wpomp_countries'];
			} else {
				$wpomp_local['wpomp_countries'] = false;
			}

			wp_localize_script( 'wplmp_google_map_main', 'wpomp_local', $wpomp_local );
			wp_enqueue_style( 'masonry' );
			if( isset( $wpomp_settings['wpomp_scripts_minify']) && $wpomp_settings['wpomp_scripts_minify'] == 'yes') {
				$frontend_styles = array( 'wplmp-frontend-style' => WPLMP_CSS . 'frontend.min.css');
			} else {
				$frontend_styles = array( 'wplmp-frontend-style' => WPLMP_CSS . 'frontend.css');	
			}	

			$frontend_styles['leaflet-autocomplete-style'] = WPLMP_PLUGINS.'autocomplete/leaflet-autocomplete.css';
			$frontend_styles['L.Control.Locate-style'] = WPLMP_PLUGINS.'locate/L.Control.Locate.css';
			$frontend_styles['leaflet.fullscreen-style'] = WPLMP_PLUGINS.'fullscreen/leaflet.fullscreen.css';
			if(!empty($wpomp_settings['wpomp_api_key'])){

				$frontend_styles[] = array(
					'handle' => 'mapbox_style',
					'src'    => WPLMP_CSS. 'mapbox.css',
					'deps'   => array(),
				);
			}
			if(!empty($wpomp_settings['wpomp_mapquest_key'])){

				$frontend_styles[] = array(
					'handle' => 'mapquest_style',
					'src'    => WPLMP_CSS. 'mapquest.css',
					'deps'   => array(),
				);
			}
			if ( $frontend_styles ) {
				foreach ( $frontend_styles as $frontend_style_key => $frontend_style_value ) {
					wp_register_style( $frontend_style_key, $frontend_style_value );
				}

			}

		}

		/**

		 * Display map at the frontend using put_wpomp shortcode.
		 *
		 * @param  array  $atts   Map Options.
		 * @param  string $content Content.
		 */

		function wplmp_show_location_in_map( $atts, $content = null ) {

			try {

				$factoryObject = new WPLMP_Controller();
				$viewObject    = $factoryObject->create_object( 'shortcode' );
				$output        = $viewObject->display('put-wpomp', $atts );
     			 return $output;

			} catch ( Exception $e ) {
				echo WPLMP_Template::show_message( array( 'error' => $e->getMessage() ) );
			}

		}

		/**
		 * Display map at the frontend using display_map shortcode.
		 *
		 * @param  array $atts    Map Options.
		 */

		function wplmp_display_map( $atts ) {

			try {
				$factoryObject = new WPLMP_Controller();
				$viewObject    = $factoryObject->create_object( 'shortcode' );
				$output       = $viewObject->display( 'display-map', $atts );
			    return $output;



			} catch ( Exception $e ) {	
				echo WPLMP_Template::show_message( array( 'error' => $e->getMessage() ) );

			}

		}

		/**
		 * Ajax Call
		 */

		function wplmp_ajax_call() {

			check_ajax_referer( 'fc-call-nonce', 'nonce' );
			$operation = sanitize_text_field( wp_unslash( $_POST['operation'] ) );
			$value     = wp_unslash( $_POST );
			if ( isset( $operation ) ) {
				$this->$operation( $value );
			}
			exit;

		}

		/**
		 * Process slug and display view in the backend.
		 */

		function wplmp_processor() {

			$return = '';
			if ( isset( $_GET['page'] ) ) {
				$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
			} else {
				$page = 'wpomp_view_overview';
			}

			$pageData      = explode( '_', $page );
			$obj_type      = $pageData[2];
			$obj_operation = $pageData[1];

			if ( count( $pageData ) < 3 ) {
				die( 'Cheating!' );
			}
			try {
				if ( count( $pageData ) > 3 ) {
					$obj_type = $pageData[2] . '_' . $pageData[3];
				}
				$factoryObject = new WPLMP_Controller();
				$viewObject    = $factoryObject->create_object( $obj_type );
				$viewObject->display( $obj_operation );

			} catch ( Exception $e ) {
				echo WPLMP_Template::show_message( array( 'error' => $e->getMessage() ) );
			}

		}

		/**
		 * Create backend navigation.
		 */

		function wplmp_create_menu() {

			$pagehook1 = add_menu_page(
				esc_html__( 'WP Leaflet Maps Pro', 'wp-leaflet-maps-pro' ),
				esc_html__( 'WP Leaflet Maps Pro', 'wp-leaflet-maps-pro' ),
				'wpomp_admin_overview',
				WPLMP_SLUG,
				array( $this, 'wplmp_processor' ),
				esc_url(WPLMP_IMAGES . '/flippercode.png')

			);

			if ( current_user_can( 'manage_options' ) ) {
				$role = get_role( 'administrator' );
				$role->add_cap( 'wpomp_admin_overview' );
			}

			$this->wplmp_load_modules_menu();
			add_action( 'load-' . $pagehook1, array( $this, 'wplmp_backend_scripts' ) );

		}

		/**
		 * Read models and create backend navigation.
		 */

		function wplmp_load_modules_menu() {

			$modules   = $this->modules;
			$pagehooks = array();
			if ( is_array( $modules ) ) {

				foreach ( $modules as $module ) {
				  $object = new $module();
					if ( method_exists( $object, 'navigation' ) ) {
						if ( ! is_array( $object->navigation() ) ) 
						continue;

						foreach ( $object->navigation() as $nav => $title ) {

							if ( current_user_can( 'manage_options' ) && is_admin() ) {

								$role = get_role( 'administrator' );
								$role->add_cap( $nav );

							}

							$pagehooks[] = add_submenu_page( WPLMP_SLUG, $title,$title, $nav,

										   $nav,array( $this, 'wplmp_processor' ) );

						}

					}

				}

			}

			if ( is_array( $pagehooks ) ) {

				foreach ( $pagehooks as $key => $pagehook ) {

					add_action( 'load-' . $pagehooks[ $key ], array( $this, 'wplmp_backend_scripts' ) );

				}

			}

		}

		/**
		 * Enqueue scripts in the backend.
		 */

		function wplmp_backend_scripts() {

			$wpomp_settings = get_option( 'wpomp_settings', true );
			$wpomp_apilocation = WPLMP_JS.'leaflet.js';
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'wp-color-picker' );
			$wp_scripts = array( 'jQuery', 'thickbox', 'wp-color-picker', 'jquery-ui-datepicker', 'jquery-ui-sortable');

			if ( $wp_scripts ) {
				foreach ( $wp_scripts as $wp_script ) {	 wp_enqueue_script( $wp_script ); }
			}

			$scripts = array();



			$scripts[] = array(
					'handle' => 'jscrollpane',
					'src'    => WPLMP_JS . 'vendor/jscrollpane/jscrollpane.js',
					'deps'   => array(),
			);

			$scripts[] = array(
				'handle' => 'accordion-script',
				'src'    => WPLMP_JS . 'vendor/accordion/accordion.js',
				'deps'   => array(),
				);

			$scripts[] = array(
				'handle' => 'datatable',
				'src'    => WPLMP_JS . 'vendor/datatables/datatables.js',
				'deps'   => array(),
			);

			$scripts[] = array(
				'handle' => 'webfont',
				'src'    => WPLMP_JS . 'vendor/webfont/webfont.js',
				'deps'   => array(),
			);

			$scripts[] = array(
				'handle' => 'select2',
				'src'    => WPLMP_JS . 'vendor/select2/select2.js',
				'deps'   => array(),
			);

			$scripts[] = array(
				'handle' => 'slick',
				'src'    => WPLMP_JS . 'vendor/slick/slick.js',
				'deps'   => array(),
			);

			$scripts[] = array(
				'handle' => 'wpomp_backend_js',
				'src'    => WPLMP_JS . 'backend.js',
				'deps'   => array(),
			);

			$scripts[] = array(
				'handle' => 'wplmp_backend_google_api',
				'src'    => $wpomp_apilocation,
				'deps'   => array(),
			);

			
			if(!empty($wpomp_settings['wpomp_bingmap_key'])){

				$scripts[] = array(
					'handle' => 'Bing',
					'src'    => WPLMP_JS . 'Bing.js',
					'deps'   => array(),
				);
			}

			$scripts[] = array(
				'handle' => 'wplmp_map',
				'src'    => WPLMP_JS . 'maps.js',
				'deps'   => array(),
			);



			$scripts[] = array(
				'handle' => 'wplmp_flippercode_ui',
				'src'    => WPLMP_JS . 'flippercode-ui.js',
				'deps'   => array(),
			);

			$scripts[] = array(
			'handle'  => 'leaflet-autocomplete',
			'src'   => WPLMP_PLUGINS.'autocomplete/leaflet-autocomplete.js',
			'deps'    => array(),
			);

			if ( $scripts ) {
				foreach ( $scripts as $script ) {
					wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], '2.3.4' );
				}
			}


			$WPLMP_JS_lang                    = array();
			$WPLMP_JS_lang['ajax_url']        = admin_url( 'admin-ajax.php' );
			$WPLMP_JS_lang['nonce']           = wp_create_nonce( 'fc-call-nonce' );
			$WPLMP_JS_lang['confirm']         = esc_html__( 'Are you sure to delete item?', 'wp-leaflet-maps-pro' );
			$WPLMP_JS_lang['text_editable']   = array( '.fc-text', '.fc-post-link', '.place_title', '.fc-item-content', '.wpomp_locations_content' );
			$WPLMP_JS_lang['bg_editable']     = array( '.fc-bg', '.fc-item-box', '.fc-pagination', '.wpomp_locations' );
			$WPLMP_JS_lang['margin_editable'] = array( '.fc-margin', '.fc-item-title', '.wpomp_locations_head', '.fc-item-content', '.fc-item-meta' );
			$WPLMP_JS_lang['full_editable']   = array( '.fc-css', '.fc-item-title', '.wpomp_locations_head', '.fc-readmore-link', '.fc-item-meta', 'a.page-numbers', '.current', '.wpomp_location_meta' );
			$WPLMP_JS_lang['image_path']      = WPLMP_IMAGES;
			$WPLMP_JS_lang['geocode_stats']   = esc_html__( 'locations geocoded', 'wp-leaflet-maps-pro' );
			$WPLMP_JS_lang['geocode_success'] = esc_html__( 'Click below to save geocoded locations', 'wp-leaflet-maps-pro' );
			wp_localize_script( 'wplmp_flippercode_ui', 'settings_obj', $WPLMP_JS_lang );

			$wpomp_local               = array();
			$wpomp_local['language']   = 'en';
			$wpomp_local['urlforajax'] = admin_url( 'admin-ajax.php' );
			$wpomp_local['hide']       = esc_html__( 'Hide', 'wp-leaflet-maps-pro' );
			$wpomp_local['nonce']      = wp_create_nonce( 'fc_communication' );
			$wpomp_local['accesstoken'] = isset($wpomp_settings['wpomp_api_key'])? $wpomp_settings['wpomp_api_key'] : '';

			wp_localize_script( 'wplmp_map', 'wpomp_local', $wpomp_local );
			wp_localize_script( 'wplmp_flippercode_ui', 'wpomp_local', $wpomp_local );

			$WPLMP_JS_lang            = array();
			$WPLMP_JS_lang['confirm'] = esc_html__( 'Are you sure to delete item?', 'wp-leaflet-maps-pro' );
			wp_localize_script( 'wplmp_backend_google_maps', 'WPLMP_JS_lang', $WPLMP_JS_lang );
			$admin_styles = array(
				'font-awesome.min'   => WPLMP_CSS . 'font-awesome.min.css',
				'flippercode-ui-style'      => WPLMP_CSS . 'flippercode-ui.css',
				'wplmp_backend_google_maps_css' => WPLMP_CSS . 'backend.css',
				'leaflet-style' => WPLMP_CSS.'leaflet.css',
				'leaflet-autocomplete-style' => WPLMP_PLUGINS.'/autocomplete/leaflet-autocomplete.css',
			);

			if ( $admin_styles ) {
				foreach ( $admin_styles as $admin_style_key => $admin_style_value ) {
					wp_enqueue_style( $admin_style_key, $admin_style_value );
				}
			}
		}

		/**
		 * Metabox stylesheet.
		 */

		function wplmp_backend_styles() {
			wp_enqueue_style( 'wplmp_backend_metabox', WPLMP_CSS . 'wpomp-metabox-css.css' );
			wp_register_script( 'wplmp_leaflet_icons', WPLMP_JS.'wplmp-leaflet-icons.js' );
			wp_register_style( 'wplmp_leaflet_icons_style', WPLMP_CSS.'wplmp-leaflet-icons.css' );

		}

		/**
		 * Load plugin language file.
		 */

		function wplmp_load_plugin_languages() {

			$this->modules = apply_filters( 'wpomp_extensions', $this->modules );
			load_plugin_textdomain( 'wp-leaflet-maps-pro', false, WPLMP_FOLDER . '/lang/' );

		}

		/**
		 * Call hook on plugin activation for both multi-site and single-site.
		 */

		function wplmp_plugin_activation( $network_wide ) {

			if ( is_multisite() && $network_wide ) {

				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated   = array();
				$sql         = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids    = $wpdb->get_col( $wpdb->prepare( $sql, null ) );

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					$this->wplmp_activation();
					$activated[] = $blog_id;

				}
				switch_to_blog( $currentblog );
				update_site_option( 'wplmp_activated', $activated );

			} else {
				$this->wplmp_activation();
			}

		}

		/**
		 * Call hook on plugin deactivation for both multi-site and single-site.
		 */

		function wplmp_plugin_deactivation() {

			if ( is_multisite() && $network_wide ) {

				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated   = array();
				$sql         = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids    = $wpdb->get_col( $wpdb->prepare( $sql, null ) );

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					$this->wplmp_deactivation();
					$activated[] = $blog_id;

				}

				switch_to_blog( $currentblog );
				update_site_option( 'wplmp_activated', $activated );

			} else {
				$this->wplmp_deactivation();
			}

		}
		
		function wplmp_customizer_font_family() {

				$font_families  = array();
				
				if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['map_id'] ) ) {

			    	$modelFactory = new WPLMP_MODEL();
					$map_obj      = $modelFactory->create_object( 'map' );
					$map_obj = $map_obj->fetch( array( array( 'map_id', '=', intval( wp_unslash( $_GET['map_id'] ) ) ) ) );
					$map     = $map_obj[0];
					if ( ! empty( $map ) ) {
						$map->map_all_control = maybe_unserialize( $map->map_all_control );
					}
					$data = (array) $map;
					if ( isset( $data['map_all_control']['fc_custom_styles'] ) ) {
						$fc_custom_styles = json_decode( $data['map_all_control']['fc_custom_styles'], true );
						if ( ! empty( $fc_custom_styles ) && is_array( $fc_custom_styles ) ) {
							$fc_skin_styles = '';

							foreach ( $fc_custom_styles as $fc_style ) {
								if ( is_array( $fc_style ) ) {
									foreach ( $fc_style as $skin => $class_style ) {
										if ( is_array( $class_style ) ) {
											foreach ( $class_style as $class => $style ) {
																			$ind_style         = explode( ';', $style );

												foreach ($ind_style as $css_value) {
													if ( strpos( $css_value, 'font-family' ) !== false ) {
															$font_family_properties   = explode( ':', $css_value );
															if(!empty($font_family_properties['1'])){
																$multiple_family = explode( ',', $font_family_properties['1']);
																if(count($multiple_family)==1){
																	$font_families[] = $font_family_properties['1'];
																}
															}
													}
												}
												if ( strpos( $class, '.' ) !== 0 ) {
													$class = '.' . $class;
												}
												$fc_skin_styles .= ' .fc-' . $skin . ' ' . $class . '{' . $style . '}';
											}
										}
									}
								}
							}
							if ( ! empty( $fc_skin_styles ) ) {
								echo '<style>' . $fc_skin_styles . '</style>';
							}
						}
					}
				}
				
				if ( ! empty( $font_families ) ) {
					$font_families = array_unique($font_families);
					?>
					<script type="text/javascript">
						var customizer_fonts = <?php echo json_encode($font_families,JSON_FORCE_OBJECT);?>;
					</script>	
				<?php 
				}
			
		}
		
		/**
		 * Perform tasks on new blog create and table install.
		 */

		function wplmp_on_blog_new_generate( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

			if ( is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {

				switch_to_blog( $blog_id );
				$this->wplmp_activation();
				restore_current_blog();

			}

		}

		/**
		 * Perform tasks on when blog deleted and remove plugin tables.
		 */

		function wplmp_on_blog_delete( $tables ) {

			global $wpdb;
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, WPLMP_TBL_LOCATION );
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, WPLMP_TBL_MAP );
			return $tables;

		}

		/**
		 * Create choose icon tab in media manager.
		 *
		 * @param  array $tabs Current Tabs.
		 * @return array       New Tabs.
		 */

		function wplmp_google_map_tabs_filter( $tabs ) {

			$newtab = array( 'ell_insert_gmop_tab' => esc_html__( 'Choose Icons - Leaflet', 'wp-leaflet-maps-pro' ) );
			return array_merge( $tabs, $newtab );

		}

		/**
		 * Intialize wp_iframe for icons tab
		 *
		 * @return [type] [description]
		 */

		function wplmp_google_map_media_upload_tab() {

			return wp_iframe( array( $this, 'media_wpomp_google_map_icon' ), array() );

		}

		/**
		 * Read images/icons folder.
		 */
		function media_wpomp_google_map_icon() {

			wp_enqueue_style( 'media' );
			wp_enqueue_script( 'wplmp_leaflet_icons' );
			wp_enqueue_style( 'wplmp_leaflet_icons_style' );

			media_upload_header();
			$form_action_url = site_url( "wp-admin/media-upload.php?type={$GLOBALS['type']}&tab=ell_insert_gmop_tab", 'admin' );
			?>
			<form enctype="multipart/form-data" method="post" action="<?php echo esc_url($form_action_url); ?>" class="media-upload-form wplml_media_form" id="library-form">
				<h3 class="media-title">
					<?php esc_html_e( 'Choose icon', 'wp-leaflet-maps-pro' ); ?> 
					<input name="wpomp_search_icon" id="wpomp_search_icon" type='text' value="" placeholder="<?php esc_html_e( 'Search icons', 'wp-leaflet-maps-pro' ); ?>" />

				</h3>

				<div class="wplmp_media_container">

					<ul id="select_icons">

						<?php

						$dir          = WPLMP_ICONS_DIR;
						$file_display = array( 'jpg', 'jpeg', 'png', 'gif' );

						if ( file_exists( $dir ) == false ) {
							echo sprintf( esc_html__('Directory %s not found!' , 'wp-leaflet-maps-pro'), $dir );

						} else {

							$dir_contents = scandir( $dir );

							foreach ( $dir_contents as $file ) {

								$image_data = explode( '.', $file );
      							$file_type  = strtolower( end( $image_data ) );
								if ( '.' !== $file && '..' !== $file && true == in_array( $file_type, $file_display ) ) {

									?>

						<li class="read_icons">

						<img alt="<?php echo esc_attr( $image_data[0] ); ?>" title="<?php echo esc_attr( $image_data[0] ); ?>" src="<?php echo esc_url( WPLMP_ICONS . $file ); ?>" />

						</li>
									<?php

								}

							}

						}
						if ( isset( $_GET['target'] ) ) {
							$target = esc_js( $_GET['target'] );
						} else {
							$target = '';
						}
						?>
					</ul>
					<button type="button" class="button wplmp-insert-icons" data-target="<?php echo esc_attr( $target ); ?>" value="1"  name="send"><?php esc_html_e( 'Insert into Post', 'wp-leaflet-maps-pro' ); ?></button>

				</div>

			</form>

			<?php

		}

		/**
		 * Perform tasks on plugin deactivation.
		 */

		function wplmp_deactivation() {}

		/**
		 * Perform tasks on plugin deactivation.
		 */

		function wplmp_activation() {

			// Migrate options data from previous version.

			if ( ! get_option( 'wpomp_settings' ) and get_option( 'wpomp_language' ) ) {

				$wpomp_settings['wpomp_language']      = get_option( 'wpomp_language', 'en' );
				$wpomp_settings['wpomp_api_key']       = get_option( 'wpomp_api_key', '' );
				$wpomp_settings['wpomp_scripts_place'] = get_option( 'wpomp_scripts_place', true );
				$wpomp_settings['wpomp_allow_meta']    = get_option( 'wpomp_allow_meta', true );
				$wpomp_settings['wpomp_scripts_minify']    = get_option( 'wpomp_scripts_minify', true );
				update_option( 'wpomp_settings', $wpomp_settings );

			}

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$modules   = $this->modules;

			$pagehooks = array();

			if ( is_array( $modules ) ) {

				foreach ( $modules as $module ) {

					$object = new $module();
					if ( method_exists( $object, 'install' ) ) {
						$tables[] = $object->install();
					}

				}

			}
			if ( is_array( $tables ) ) {

				foreach ( $tables as $i => $sql ) {

					dbDelta( $sql );

				}

			}

		}
		
     	/**
		 * Export data into csv,xml,json or excel file
		 */

		function wplmp_export_data() {

			if ( isset( $_POST['action'] ) && isset( $_REQUEST['_wpnonce'] ) && $_POST['action'] == 'export_location_csv_wplmp' ) {

				if ( ! current_user_can('administrator') )
				die( 'You are not allowed to save changes!' );
				
				//Nonce Verification
				if( !isset( $_REQUEST['_wpnonce'] ) || ( isset( $_REQUEST['_wpnonce'] ) && empty($_REQUEST['_wpnonce']) ) )
				die( 'You are not allowed to save changes!' );
				
				if ( isset( $_REQUEST['_wpnonce'] ) && ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpomp-nonce' ) || wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpgmp-nonce' )) ) {


					if ( isset( $_POST['action'] ) and false != strstr( $_POST['action'], 'export_' ) ) {
						$export_action = explode( '_', sanitize_text_field( $_POST['action'] ) );
						if ( 4 == count( $export_action ) and 'export' == $export_action[0] ) {
							$model_class = 'WPLMP_MODEL_' . ucwords( $export_action[1] );
							$entity      = new $model_class();
							$entity->export( $export_action[2] );

						}

					}


				}else{

					die( 'You are not allowed to save changes!' );

				}
			}

		}

		/**
		 * Define all constants.
		 */

		private function wplmp_define_constants() {
			
			global $wpdb;
			if ( ! defined( 'WPLMP_SLUG' ) ) {
				define( 'WPLMP_SLUG', 'wpomp_view_overview' );
			}
			if ( ! defined( 'WPLMP_VERSION' ) ) {
				define( 'WPLMP_VERSION', '1.0.9' );
			}
			if ( ! defined( 'WPLMP_FOLDER' ) ) {
				define( 'WPLMP_FOLDER', basename( dirname( __FILE__ ) ) );
			}
			if ( ! defined( 'WPLMP_DIR' ) ) {
				define( 'WPLMP_DIR', plugin_dir_path( __FILE__ ) );
			}
			if ( ! defined( 'WPLMP_ICONS_DIR' ) ) {
				define( 'WPLMP_ICONS_DIR', WPLMP_DIR.'/assets/images/icons/' );
			}
			if ( ! defined( 'WPLMP_CORE_CLASSES' ) ) {
				define( 'WPLMP_CORE_CLASSES', WPLMP_DIR.'core/' );
			}
			if ( ! defined( 'WPLMP_PLUGIN_CLASSES' ) ) {
				define( 'WPLMP_PLUGIN_CLASSES', WPLMP_DIR . 'classes/' );
			}
			if ( ! defined( 'WPLMP_TemplateS' ) ) {
				define( 'WPLMP_TemplateS', WPLMP_DIR . 'templates/' );
			}
			if ( ! defined( 'WPLMP_MODEL' ) ) {
				define( 'WPLMP_MODEL', WPLMP_DIR . 'modules/' );
			}
			
			if ( ! defined( 'WPLMP_URL' ) ) {
				define( 'WPLMP_URL', plugin_dir_url( WPLMP_FOLDER ).WPLMP_FOLDER.'/' );
			}
			if ( ! defined( 'WPLMP_ICONS' ) ) {
				define( 'WPLMP_ICONS', WPLMP_URL . 'assets/images/icons/' );
			}
			if ( ! defined( 'WPLMP_TemplateS_URL' ) ) {
				define( 'WPLMP_TemplateS_URL', WPLMP_URL.'templates/' );
			}
			if ( ! defined( 'WPLMP_CSS' ) ) {
				define( 'WPLMP_CSS', WPLMP_URL.'assets/css/' );
			}
			if ( ! defined( 'WPLMP_JS' ) ) {
				define( 'WPLMP_JS', WPLMP_URL.'assets/js/' );
			}
			if ( ! defined( 'WPLMP_IMAGES' ) ) {
				define( 'WPLMP_IMAGES', WPLMP_URL.'assets/images/' );
			}
			if ( ! defined( 'WPLMP_PLUGINS' ) ) {
				define( 'WPLMP_PLUGINS', WPLMP_URL.'assets/plugins/' );
			}
			if ( ! defined( 'WPLMP_ICONS' ) ) {
				define( 'WPLMP_ICONS', WPLMP_URL.'assets/images/icons/' );
			}
			if ( ! defined( 'WPLMP_TBL_LOCATION' ) ) {
				define( 'WPLMP_TBL_LOCATION', $wpdb->prefix.'wpomp_locations' );
			}
			
			if ( ! defined( 'WPLMP_TBL_MAP' ) ) {
				define( 'WPLMP_TBL_MAP', $wpdb->prefix.'wpomp_maps' );
			}
			if ( ! defined( 'WPLMP_TBL_GROUPMAP' ) ) {
				define( 'WPLMP_TBL_GROUPMAP', $wpdb->prefix .'wpomp_group_map' );
			}

		}

		/**
		 * Load all required core classes.
		 */

		private function wplmp_load_files() {

			$coreInitialisationFile = plugin_dir_path( __FILE__ ) . 'core/class.initiate-core.php';
			if ( file_exists( $coreInitialisationFile ) ) {
				require_once $coreInitialisationFile;
			}

			// Load Plugin Files

			$plugin_files_to_include = array( 'wpomp-template.php','wpomp-controller.php',
											  'wpomp-model.php','wpomp-map-widget.php' );

			foreach ( $plugin_files_to_include as $file ) {
				if ( file_exists( WPLMP_PLUGIN_CLASSES . $file ) ) {
					require_once WPLMP_PLUGIN_CLASSES . $file;
				}
			}

			// Load all modules.

			$core_modules = array( 'overview', 'location', 'map','group_map', 'settings', 'tools','general' );

			if ( is_array( $core_modules ) ) {

				foreach ( $core_modules as $module ) {

					$file = WPLMP_MODEL.$module.'/model.'.$module.'.php';

					if ( file_exists( $file ) ) {
						include_once $file;
						$class_name = 'WPLMP_MODEL_' . ucwords( $module );
						array_push( $this->modules, $class_name );

					}

				}

			}

		}

	}
	new WPLMP_Maps_Pro();
}
