<?php
/**
 * Class: WPLMP_MODEL_General
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package wp-leaflet-maps-pro
 */

if ( ! class_exists( 'WPLMP_MODEL_General' ) ) {

	/**
	 * Shortcode model to display output on frontend.
	 *
	 * @package wp-leaflet-maps-pro
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPLMP_MODEL_General extends FlipperCode_Model_Base {
		/**
		 * Intialize General object.
		 */
		function __construct() {
		}
		/**
		 * Admin menu for Settings Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array();
		}

		function wplmp_check_vc_depenency() {

			if ( defined( 'WPB_VC_VERSION' ) ) {
				$isVCInstalled = true;
				$this->wplmp_map_component_vc($isVCInstalled);
			}
		}

		function wplmp_map_component_vc($isVCInstalled) {

			if ( $isVCInstalled ) {

				global $wpdb;

				$map_options = array();

				$map_options[ esc_html__( 'Select Map', 'wp-leaflet-maps-pro' ) ] = '';
				$map_records = $wpdb->get_results( 'SELECT map_id,map_title FROM ' . WPLMP_TBL_MAP . '' );

				if ( ! empty( $map_records ) ) {
					foreach ( $map_records as $key => $map_record ) {
						$map_options[ $map_record->map_title ] = $map_record->map_id;
					}
				}

				$shortcodeParams = array();

				$shortcodeParams[] = array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Choose Maps', 'wp-leaflet-maps-pro' ),
					'param_name'  => 'id',
					'description' => esc_html__( 'Choose here the map you want to show.', 'wp-leaflet-maps-pro' ),
					'value'       => $map_options,
				);

				$wpomp_maps_component = array(
					'name'        => esc_html__( 'WP Leaflet Maps Pro', 'wp-leaflet-maps-pro' ),
					'base'        => 'put_wplmp_map',
					'class'       => '',
					'category'    => esc_html__( 'Content', 'wp-leaflet-maps-pro' ),
					'description' => esc_html__( 'Leaflet Maps', 'wp-leaflet-maps-pro' ),
					'params'      => $shortcodeParams,
					'icon'        => WPLMP_IMAGES . 'flippercode.png',
				);
				vc_map( $wpomp_maps_component );

			}

		}
		function wplmp_apply_placeholders( $content ) {

			 $data['marker_id']                 = 1;
			 $data['marker_title']              = 'New York, NY, United States';
			 $data['marker_address']            = 'New York, NY, United States';
			 $data['marker_message']            = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.';
			 $data['marker_category']           = 'Real Estate';
			 $data['marker_icon']               = WPLMP_IMAGES . 'default_marker.png';
			 $data['marker_latitude']           = '40.7127837';
			 $data['marker_longitude']          = '-74.00594130000002';
			 $data['marker_city']               = 'New York';
			 $data['marker_state']              = 'NY';
			 $data['marker_country']            = 'United States';
			 $data['marker_zoom']               = '5';
			 $data['marker_postal_code']        = '10002';
			 $data['extra_field_slug']          = 'color';
			 $data['marker_featured_image_src'] = WPLMP_IMAGES . 'sample.jpg';
			 $data['marker_image']              = '<img class="fc-item-featured_image  fc-item-large" src="' . WPLMP_IMAGES . 'sample.jpg' . '" />';
			 $data['marker_featured_image']     = '<img class="fc-item-featured_image  fc-item-large" src="' . WPLMP_IMAGES . 'sample.jpg' . '" />';
			 $data['post_title']                = 'Lorem ipsum dolor sit amet, consectetur';
			 $data['post_link']                 = '#';
			 $data['post_excerpt']              = 'Lorem ipsum dolor sit amet, consectetur';
			 $data['post_content']              = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
			 $data['post_categories']           = 'city tour';
			 $data['post_tags']                 = 'WordPress, plugins, leaflet maps';
			 $data['post_featured_image']       = '<img class="fc-item-featured_image  fc-item-large" src="' . WPLMP_IMAGES . 'sample.jpg' . '" />';
			 $data['post_author']               = 'FlipperCode';
			 $data['post_comments']             = '<i class="fci fci-comment"></i> 10';
			 $data['view_count']                = '<i class="fci fci-heart"></i> 1';

			foreach ( $data as $key => $value ) {
				if ( strstr( $key, 'marker_featured_image_src' ) === false && strstr( $key, 'marker_icon' ) === false && strstr( $key, 'post_link' ) === false && strstr( $key, 'marker_zoom' ) === false && strstr( $key, 'marker_id' ) === false && strstr( $key, 'post_title' ) === false ) {
					$content = str_replace( "{{$key}}", $value . '<span class="fc-hidden-placeholder">{' . $key . '}</span>', $content );
				} else {
					$content = str_replace( "{{$key}}", $value, $content );
				}
			}
			return $content;
		}

		function wplmp_save_meta_box_data($post_id){


			if ( isset( $_REQUEST['wpomp-nonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['wpomp-nonce'] ) ); }

			if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpomp-nonce' ) ) {
   				return false;
			}

			
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
   				return false;
            }
            
			if ( isset( $_POST['wpomp_hidden_flag'] ) ) {

				$wpomp_enter_location = $_POST['wpomp_metabox_location_hidden'];

				$wpomp_enter_city    = sanitize_text_field( wp_unslash( $_POST['wpomp_metabox_location_city'] ) );
				$wpomp_enter_state   = sanitize_text_field( wp_unslash( $_POST['wpomp_metabox_location_state'] ) );
				$wpomp_enter_country = sanitize_text_field( wp_unslash( $_POST['wpomp_metabox_location_country'] ) );

				$wpomp_metabox_latitude          = sanitize_text_field( wp_unslash( $_POST['wpomp_metabox_latitude'] ) );
				$wpomp_metabox_longitude         = sanitize_text_field( wp_unslash( $_POST['wpomp_metabox_longitude'] ) );
				$wpomp_map_id                    = serialize( wp_unslash( $_POST['wpomp_metabox_mapid'] ) );
				$wpomp_metabox_marker_id         = serialize( wp_unslash( $_POST['wpomp_metabox_marker_id'] ) );
				$wpomp_metabox_location_redirect = sanitize_text_field( wp_unslash( $_POST['wpomp_metabox_location_redirect'] ) );
				$wpomp_metabox_custom_link       = sanitize_text_field( wp_unslash( $_POST['wpomp_metabox_custom_link'] ) );
				$wpomp_metabox_taxomomies_terms  = serialize( wp_unslash( $_POST['wpomp_metabox_taxomomies_terms'] ) );
				$wpomp_extensions_fields         = serialize( wp_unslash( $_POST['wpomp_extensions_fields'] ) );

				// Update the meta field in the database.
				update_post_meta( $post_id, '_wpomp_location_address', $wpomp_enter_location );
				update_post_meta( $post_id, '_wpomp_location_city', $wpomp_enter_city );
				update_post_meta( $post_id, '_wpomp_location_state', $wpomp_enter_state );
				update_post_meta( $post_id, '_wpomp_location_country', $wpomp_enter_country );

				update_post_meta( $post_id, '_wpomp_metabox_latitude', $wpomp_metabox_latitude );
				update_post_meta( $post_id, '_wpomp_metabox_longitude', $wpomp_metabox_longitude );
				update_post_meta( $post_id, '_wpomp_metabox_location_redirect', $wpomp_metabox_location_redirect );
				update_post_meta( $post_id, '_wpomp_metabox_custom_link', $wpomp_metabox_custom_link );
				update_post_meta( $post_id, '_wpomp_map_id', $wpomp_map_id );
				update_post_meta( $post_id, '_wpomp_metabox_marker_id', $wpomp_metabox_marker_id );
				update_post_meta( $post_id, '_wpomp_metabox_taxomomies_terms', $wpomp_metabox_taxomomies_terms );
				update_post_meta( $post_id, '_wpomp_extensions_fields', $wpomp_extensions_fields );
			}
		}

		function wplmp_get_frontend_localized_data(){

			$wpomp_local                              = array();
			$wpomp_local['select_radius']             = esc_html__( 'Select Radius', 'wp-leaflet-maps-pro' );
			$wpomp_local['search_placeholder']        = esc_html__( 'Enter address or latitude or longitude or title or city or state or country or postal code here...', 'wp-leaflet-maps-pro' );
			
			$wpomp_local['autocomplete_placeholder']        = esc_html__( 'Enter address or latitude or longitude or title or city or state or country or postal code here...', 'wp-leaflet-maps-pro' );

			$wpomp_local['select']                    = esc_html__( 'Select', 'wp-leaflet-maps-pro' );
			$wpomp_local['default_center_msg']        = esc_html__( 'This is your searched location.', 'wp-leaflet-maps-pro' );


			$wpomp_local['select_all']                = esc_html__( 'Select All', 'wp-leaflet-maps-pro' );
			$wpomp_local['select_category']           = esc_html__( 'Select Category', 'wp-leaflet-maps-pro' );
			$wpomp_local['all_location']              = esc_html__( 'All', 'wp-leaflet-maps-pro' );
			$wpomp_local['show_locations']            = esc_html__( 'Show Locations', 'wp-leaflet-maps-pro' );
			$wpomp_local['sort_by']                   = esc_html__( 'Sort by', 'wp-leaflet-maps-pro' );
			$wpomp_local['wpomp_not_working']         = esc_html__( 'not working...', 'wp-leaflet-maps-pro' );
			$wpomp_local['place_icon_url']            = WPLMP_ICONS;
			$wpomp_local['wpomp_location_no_results'] = esc_html__( 'No results found.', 'wp-leaflet-maps-pro' );
			$wpomp_local['wpomp_route_not_avilable']  = esc_html__( 'Route is not available for your requested route.', 'wp-leaflet-maps-pro' );
			$wpomp_local['img_grid']                  = "<span class='span_grid'><a class='wpomp_grid'><img src='" . WPLMP_IMAGES . "grid.png'></a></span>";
			$wpomp_local['img_list']                  = "<span class='span_list'><a class='wpomp_list'><img src='" . WPLMP_IMAGES . "list.png'></a></span>";
			
			$wpomp_local['img_print']                 = "<span class='span_print'><a class='wpomp_print' data-action='wpomp-print'><img src='" . WPLMP_IMAGES . "print.png'></a></span>";

			
			$wpomp_local['hide']                      = esc_html__( 'Hide', 'wp-leaflet-maps-pro' );
			$wpomp_local['show']                      = esc_html__( 'Show', 'wp-leaflet-maps-pro' );
			$wpomp_local['start_location']            = esc_html__( 'Start Location', 'wp-leaflet-maps-pro' );
			$wpomp_local['start_point']               = esc_html__( 'Start Point', 'wp-leaflet-maps-pro' );
			$wpomp_local['radius']                    = esc_html__( 'Radius', 'wp-leaflet-maps-pro' );
			$wpomp_local['end_location']              = esc_html__( 'End Location', 'wp-leaflet-maps-pro' );
			$wpomp_local['take_current_location']     = esc_html__( 'Take Current Location', 'wp-leaflet-maps-pro' );
			$wpomp_local['center_location_message']   = esc_html__( 'Your Location', 'wp-leaflet-maps-pro' );
			$wpomp_local['center_location_message']   = esc_html__( 'Your Location', 'wp-leaflet-maps-pro' );
			$wpomp_local['driving']                   = esc_html__( 'Driving', 'wp-leaflet-maps-pro' );
			$wpomp_local['bicycling']                 = esc_html__( 'Bicycling', 'wp-leaflet-maps-pro' );
			$wpomp_local['walking']                   = esc_html__( 'Walking', 'wp-leaflet-maps-pro' );
			$wpomp_local['transit']                   = esc_html__( 'Transit', 'wp-leaflet-maps-pro' );
			$wpomp_local['metric']                    = esc_html__( 'Metric', 'wp-leaflet-maps-pro' );
			$wpomp_local['imperial']                  = esc_html__( 'Imperial', 'wp-leaflet-maps-pro' );
			$wpomp_local['find_direction']            = esc_html__( 'Find Direction', 'wp-leaflet-maps-pro' );
			$wpomp_local['miles']                     = esc_html__( 'Miles', 'wp-leaflet-maps-pro' );
			$wpomp_local['km']                        = esc_html__( 'KM', 'wp-leaflet-maps-pro' );
			$wpomp_local['show_amenities']            = esc_html__( 'Show Amenities', 'wp-leaflet-maps-pro' );
			$wpomp_local['find_location']             = esc_html__( 'Find Locations', 'wp-leaflet-maps-pro' );
			$wpomp_local['locate_me']                 = esc_html__( 'Locate Me', 'wp-leaflet-maps-pro' );
			$wpomp_local['prev']                      = esc_html__( 'Prev', 'wp-leaflet-maps-pro' );
			$wpomp_local['next']                      = esc_html__( 'Next', 'wp-leaflet-maps-pro' );
			$wpomp_local['ajax_url']                  = admin_url( 'admin-ajax.php' );
			$wpomp_local['nonce']                     = wp_create_nonce( 'fc-call-nonce' );

			return $wpomp_local;
		}

		function wplmp_add_meta_box($post){

			global $wpdb,$pagehook3, $pagehook6, $pagehook9, $pagehook11;

			$wpomp_settings = get_option( 'wpomp_settings', true );

			$modelFactory                   = new WPLMP_MODEL();
			$category_obj                   = $modelFactory->create_object( 'group_map' );
			$categories                     = $category_obj->fetch();
			$map_obj                        = $modelFactory->create_object( 'map' );
			$all_maps                       = $map_obj->fetch();
			$wpomp_location_address         = get_post_meta( $post->ID, '_wpomp_location_address', true );
			$wpomp_metabox_location_city    = get_post_meta( $post->ID, '_wpomp_location_city', true );
			$wpomp_metabox_location_state   = get_post_meta( $post->ID, '_wpomp_location_state', true );
			$wpomp_metabox_location_country = get_post_meta( $post->ID, '_wpomp_location_country', true );

			$wpomp_map_ids = get_post_meta( $post->ID, '_wpomp_map_id', true );
			$wpomp_map_id  = unserialize( $wpomp_map_ids );
			if ( ! is_array( $wpomp_map_id ) ) {
				$wpomp_map_id = array( $wpomp_map_ids );
			}
			$wpomp_metabox_marker_id         = get_post_meta( $post->ID, '_wpomp_metabox_marker_id', true );
			$wpomp_metabox_latitude          = get_post_meta( $post->ID, '_wpomp_metabox_latitude', true );
			$wpomp_metabox_longitude         = get_post_meta( $post->ID, '_wpomp_metabox_longitude', true );
			$wpomp_metabox_location_redirect = get_post_meta( $post->ID, '_wpomp_metabox_location_redirect', true );
			$wpomp_metabox_custom_link       = get_post_meta( $post->ID, '_wpomp_metabox_custom_link', true );

			$language = 'en';
			$language = apply_filters( 'wpomp_map_lang', $language );

			$wpomp_apilocation = WPLMP_JS.'leaflet.js';
			$hide_map = isset($wpomp_settings['wpomp_metabox_map']) ? $wpomp_settings['wpomp_metabox_map'] : false;

			$center_lat = '38.555475';
			$center_lng = '-95.665';

			if ( $wpomp_metabox_latitude != '' ) {
				$center_lat = $wpomp_metabox_latitude;
			}

			if ( $wpomp_metabox_longitude != '' ) {
				$center_lng = $wpomp_metabox_longitude;
			}

			$center_lat = apply_filters( 'wpomp_metabox_lat', $center_lat );
			$center_lng = apply_filters( 'wpomp_metabox_lng', $center_lng );


			$wpomp_settings = get_option( 'wpomp_settings', true );


			$wpomp_apilocation = WPLMP_JS.'leaflet.js';

			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'wp-color-picker' );
			$wp_scripts = array( 'jQuery', 'thickbox', 'wp-color-picker', 'jquery-ui-datepicker', 'jquery-ui-sortable' );

			if ( $wp_scripts ) {
				foreach ( $wp_scripts as $wp_script ) {
					wp_enqueue_script( $wp_script );
				}
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
				'handle' => 'wplmp_backend_google_api',
				'src'    => $wpomp_apilocation,
				'deps'   => array(),
			);

			
			$scripts[] = array(
				'handle' => 'wplmp_map',
				'src'    => WPLMP_JS . 'maps.js',
				'deps'   => array(),
			);


			if(!empty($wpomp_settings['wpomp_mapquest_key'])){
				$map_quest_key    = $wpomp_settings['wpomp_mapquest_key'];
				$mapquest_sdk_url = 'https://www.mapquestapi.com/sdk/leaflet/v2.2/mq-map.js?key=%s';
				$mapquest_sdk_url = sprintf($mapquest_sdk_url, $map_quest_key);
				$scripts[] = array(
					'handle' => 'mapquest',
					'src'    => $mapquest_sdk_url,
					'deps'   => array('wplmp_backend_google_api'),
				);
			}

			$scripts[] = array(
				'handle' => 'wplmp-metabox',
				'src'    => WPLMP_JS . 'wplmp-metabox.js',
				'deps'   => array(),
			);

	
			if(!empty($wpomp_settings['wpomp_api_key'])){

				$scripts[] = array(
					'handle' => 'mapbox_script',
					'src'    => WPLMP_JS . 'mapbox.js',
					'deps'   => array(),
				);
			}		

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
			$wpomp_local['accesstoken'] = isset($wpomp_settings['wpomp_api_key']) ? $wpomp_settings['wpomp_api_key'] : '';


			wp_localize_script( 'wplmp_map', 'wpomp_local', $wpomp_local );
			wp_localize_script( 'wplmp_flippercode_ui', 'wpomp_local', $wpomp_local );

			$WPLMP_JS_lang            = array();
			$WPLMP_JS_lang['confirm'] = esc_html__( 'Are you sure to delete item?', 'wp-leaflet-maps-pro' );
			wp_localize_script( 'wplmp_backend_google_maps', 'WPLMP_JS_lang', $WPLMP_JS_lang );
			$admin_styles = array(
				'leaflet-style' => WPLMP_CSS.'leaflet.css',
				'leaflet-autocomplete-style' => WPLMP_PLUGINS.'/autocomplete/leaflet-autocomplete.css',
			);
			if(!empty($wpomp_settings['wpomp_api_key'])){

				$admin_styles['mapbox_style'] = WPLMP_CSS. 'mapbox.css'; 
			}
			if(!empty($wpomp_settings['wpomp_mapquest_key'])){

				$admin_styles['mapquest_style'] = WPLMP_CSS. 'mapquest.css'; 
			}

			if ( $admin_styles ) {
				foreach ( $admin_styles as $admin_style_key => $admin_style_value ) {
					wp_enqueue_style( $admin_style_key, $admin_style_value );
				}
			}

			$data = array();
			$infowindow_message = ( isset( $data['location_messages'] ) and ! empty( $data['location_messages'] ) ) ? $data['location_messages'] : '';
			$infowindow_disable = ( isset( $data['location_settings'] ) and ! empty( $data['location_settings'] ) ) ? $data['location_settings'] : '';
			$category = new stdClass();
			if ( isset( $_GET['group_map_id'] ) ) {
				$category_obj       = $category_obj->get( array( array( 'group_map_id', '=', intval( wp_unslash( $_GET['group_map_id'] ) ) ) ) );
				$category           = (array) $category_obj[0];
			}

			if ( ! empty( $category->group_marker ) ) {
				$category_group_marker = $category->group_marker;
			} else {
				$category_group_marker = WPLMP_IMAGES . 'default_marker.png';
			}
			
			$map_data['map_options'] = array(
				'center_lat' => ( isset( $center_lat ) and ! empty( $center_lat ) ) ? $center_lat : '40.6153983',
				'center_lng' => ( isset( $center_lng ) and ! empty( $center_lng ) ) ? $center_lng : '-74.2535216',
			);
			$map_data['places'][]    = array(
				'id'         => ( isset( $data['location_id'] ) and ! empty( $data['location_id'] ) ) ? $data['location_id'] : '',
				'title'      => ( isset( $data['location_title'] ) and ! empty( $data['location_title'] ) ) ? $data['location_title'] : '',
				'content'    => ( isset( $wpomp_location_address ) and ! empty( $wpomp_location_address ) ) ? $wpomp_location_address : '',
				'location'   => array(
					'icon'                    => ( $category_group_marker ),
					'lat'                     => ( isset( $center_lat ) and ! empty( $center_lat ) ) ? $center_lat : '40.6153983',
					'lng'                     => ( isset( $center_lng ) and ! empty( $center_lng ) ) ? $center_lng : '-74.2535216',
					'draggable'               => true,
					'infowindow_default_open' => ( isset( $data['location_infowindow_default_open'] ) and ! empty( $data['location_infowindow_default_open'] ) ) ? $data['location_infowindow_default_open'] : '',
					'animation'               => ( isset( $data['location_animation'] ) and ! empty( $data['location_animation'] ) ) ? $data['location_animation'] : '',
					'infowindow_disable'      => ( isset( $infowindow_disable['hide_infowindow'] ) && 'false' === $infowindow_disable['hide_infowindow'] ),
				),
				'categories' => array(
					array(
						'id'   => isset( $category->group_map_id ) ? $category->group_map_id : '',
						'name' => isset( $category->group_map_title ) ? $category->group_map_title : '',
						'type' => 'category',
						'icon' => $category_group_marker,
					),
				),
			);
			$map_data['page']        = 'edit_location';
			$map_data['map_options']['images_url'] = WPLMP_IMAGES;
			$map_data['map_options']['marker_default_icon'] = WPLMP_ICONS . 'marker_default_icon.png';

			wp_localize_script( 'wplmp-metabox', 'map_data', $map_data );

		?>
		
		<div class="wpomp_metabox_container">
			<?php if ( $hide_map != 'true' ) { ?>
		<div class="row_metabox">
			<div id="wpomp_meta_map1" class="wpomp_meta_map"></div>

		</div>
		<?php } ?>
		<div class="row_metabox">
		<div class="wpomp_metabox_left">
		<label for="wpomp_metabox_location"><?php esc_html_e( 'Enter Location :', 'wp-leaflet-maps-pro' ); ?></label>
		</div>
		<div class="wpomp_metabox_right">
		<input type="text" id="searchBox" class="wpomp_metabox_location wpomp_auto_suggest" name="wpomp_metabox_location" value="<?php echo htmlspecialchars( stripslashes( $wpomp_location_address ) ); ?>" size="25" />
		<input type="hidden" id="wpomp_metabox_location_hidden" class="wpomp_metabox_location_hidden"  name="wpomp_metabox_location_hidden" value="<?php echo htmlspecialchars( stripslashes( $wpomp_location_address ) ); ?>" />
		<input type="hidden" id="wpomp_metabox_location_city" class="google_city" name="wpomp_metabox_location_city" value="<?php echo htmlspecialchars( stripslashes( $wpomp_metabox_location_city ) ); ?>" />
		<input type="hidden" id="wpomp_metabox_location_state" class="google_state" name="wpomp_metabox_location_state" value="<?php echo htmlspecialchars( stripslashes( $wpomp_metabox_location_state ) ); ?>" />
		<input type="hidden" id="wpomp_metabox_location_country" class="google_country" name="wpomp_metabox_location_country" value="<?php echo htmlspecialchars( stripslashes( $wpomp_metabox_location_country ) ); ?>" />
		</div>
		</div>
		<div class="row_metabox">
		<div class="wpomp_metabox_left">
		<label for="wpomp_enter_location"><?php esc_html_e( 'Latitude', 'wp-leaflet-maps-pro' ); ?>&nbsp;/&nbsp;<?php esc_html_e( 'Longitude', 'wp-leaflet-maps-pro' ); ?>&nbsp;:</label>
		</div>
		<div class="wpomp_metabox_right">
		<input type="text" class="wpomp_metabox_latitude google_latitude" id="wpomp_metabox_latitude" name="wpomp_metabox_latitude" value="<?php echo esc_attr( $wpomp_metabox_latitude ); ?>" placeholder="Latitude" />
		<input type="text" class="wpomp_metabox_longitude google_longitude" id="wpomp_metabox_longitude" name="wpomp_metabox_longitude" value="<?php echo esc_attr( $wpomp_metabox_longitude ); ?>" placeholder="Longitude" />
		</div>
		</div>
		<div class="row_metabox">
		<div class="wpomp_metabox_left">
		<label><?php esc_html_e( 'Select Categories:', 'wp-leaflet-maps-pro' ); ?></label>
		</div>
		<div class="wpomp_metabox_right">
				<?php
				$selected_categories = unserialize( $wpomp_metabox_marker_id );

				if ( ! is_array( $selected_categories ) ) {
					$selected_categories = array( $wpomp_metabox_marker_id );
				}

				if ( $categories ) {
					foreach ( $categories as $category ) {
						if ( in_array( $category->group_map_id, $selected_categories ) ) {
							$s = "checked='checked'";
						} else {
							$s = '';
						}
						?>
			<span class="wpomp_check">
			<input type="checkbox" id="wpomp_location_group_map<?php echo esc_attr( $category->group_map_id ); ?>" <?php echo esc_attr( $s ); ?> name="wpomp_metabox_marker_id[]" value="<?php echo esc_attr( $category->group_map_id ); ?>">
						<?php echo esc_html( $category->group_map_title ); ?>
		</span>
						<?php
					}
				} else {
					echo '<p class="description">';

					$link = "<a href='" . esc_url( admin_url( 'admin.php?page=wpomp_form_group_map' ) ) . "' target='_blank'>" . esc_html__( 'Here', 'wp-leaflet-maps-pro' ) . '</a>';

					printf(
						/* translators: %s: Add Category Link */
							esc_html__( 'Do you want to assign a category? Please create category %s.', 'wp-leaflet-maps-pro' ),
						$link
					);

					echo '</p>';
				}
				?>
			</div>
			</div>
			<div class="row_metabox">
			</div>
			<div class="row_metabox">
			<div class="wpomp_metabox_left">
			<label for="wpomp_enter_location"><?php esc_html_e( 'Location Redirect :', 'wp-leaflet-maps-pro' ); ?></label>
		</div>
			<div class="wpomp_metabox_right">
			<select name="wpomp_metabox_location_redirect" id="wpomp_metabox_location_redirect">
				<option value="marker"<?php selected( $wpomp_metabox_location_redirect, 'marker' ); ?>><?php esc_html_e( 'Marker', 'wp-leaflet-maps-pro' ); ?></option>
				<option value="post"<?php selected( $wpomp_metabox_location_redirect, 'post' ); ?>><?php esc_html_e( 'Post', 'wp-leaflet-maps-pro' ); ?></option>
				<option value="custom_link"<?php selected( $wpomp_metabox_location_redirect, 'custom_link' ); ?>><?php esc_html_e( 'Custom Link', 'wp-leaflet-maps-pro' ); ?></option>
			</select>
			</div>
			</div>

				<?php
				if ( ! empty( $wpomp_metabox_custom_link ) && 'custom_link' == $wpomp_metabox_location_redirect ) {
					$display_custom_link = 'display:block';
				} else {
					$display_custom_link = 'display:none';
				}
				?>

			<div class="row_metabox" style="<?php echo esc_attr( $display_custom_link ); ?>" id="wpomp_toggle_custom_link">
		<div class="wpomp_metabox_left">
		<label for="wpomp_metabox_custom_link">&nbsp;</label>
		</div>
		<div class="wpomp_metabox_right">
		<input type="textbox" value="<?php echo esc_attr( $wpomp_metabox_custom_link ); ?>" name="wpomp_metabox_custom_link" class="wpomp_metabox_location" />
		<p class="description"><?php esc_html_e( 'Please enter link.', 'wp-leaflet-maps-pro' ); ?></p>
		</div>
		</div>
				<?php do_action( 'wpomp_meta_box_fields' ); ?>
		<div class="row_metabox">
		<div class="wpomp_metabox_left">
		<label><?php esc_html_e( 'Select Map :', 'wp-leaflet-maps-pro' ); ?></label>
		</div>
		<div class="wpomp_metabox_right">
		
				<?php

				if ( count( $all_maps ) > 0 ) {
					foreach ( $all_maps as $map ) :

						if ( is_array( $wpomp_map_id ) and in_array( $map->map_id, $wpomp_map_id ) ) {
							$c = 'checked=checked';
						} else {
							$c = ''; }

						?>
		   
			 <span class="wpomp_check"><input <?php echo esc_attr( $c ); ?> type="checkbox" name="wpomp_metabox_mapid[]" value="<?php echo esc_attr( $map->map_id ); ?>">&nbsp; <?php echo esc_html( $map->map_title ); ?></span>
		
						<?php
		endforeach;
				} else {

					$link = "<a href='" . admin_url( 'admin.php?page=wpomp_create_map' ) . "'>" . esc_html__( 'create a map', 'wp-leaflet-maps-pro' ) . '</a>';

					printf(
						/* translators: %s: Add Map Link */
							esc_html__( 'Please %s first.', 'wp-leaflet-maps-pro' ),
						$link
					);

				}
				?>
	   
		<input type="hidden" name="wpomp_hidden_flag" value="true" />
		<input type="hidden" name="wpomp_nonce" class="wpomp_nonce" value="<?php echo wp_create_nonce('wpomp_nonce') ?>" />
		
		</div>
		</div>

		</div>

		<?php

		}

	}
}
