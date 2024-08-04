<?php
/**
 * Parse Shortcode and display maps.
 *
 * @package wp-leaflet-maps-pro
 * @author Flipper Code <hello@flippercode.com>
 */

global $wpomp_dbobj;
$wpomp_settings = get_option( 'wpomp_settings', true );

$wpomp_dbobj   = new FlipperCode_Database();
$atts          = $options;
$marker_array  = array();
$address_array = array();
// Fetch map information.
$modelFactory        = new WPLMP_MODEL();
$category_obj        = $modelFactory->create_object( 'group_map' );
$categories          = $category_obj->fetch();
$all_categories      = array();
$all_categories_name = array();

if ( ! empty( $categories ) ) {
	foreach ( $categories as $category ) {
		$all_categories[ $category->group_map_id ]                           = $category;
		$all_categories_name[ sanitize_title( $category->group_map_title ) ] = $category;
	}
}

$center_lat           = '40.6153983';
$center_lng           = '-74.2535216';

if ( $atts ) {
	foreach ( $atts as $key => $value ) {
		if ( strpos( $key, 'marker' ) === 0 ) {
			$marker_array[ $key ] = $value;
			$first_marker         = current( $marker_array );
			$explode_marker       = explode( '|', $first_marker );
			$center_lat           = $explode_marker[0];
			$center_lng           = $explode_marker[1];
		}
		if ( strpos( $key, 'address' ) === 0 ) {
			$address_array[ $key ] = $value;
			$first_address         = current( $address_array );
			$rm_space_ads          = str_replace( ' ', '+', $first_address );
			$explode_ads           = explode( '|', $rm_space_ads );
			$geocode               = wp_remote_get( 'https://nominatim.openstreetmap.org/search?format=json&q='.$explode_ads[0]);

			if ( ! isset( $geocode->errors ) ) {
				$output     = json_decode( $geocode['body'] );
				if(is_array($output) && count($output)>0){
					$center_lat = $output[0]->lat;
					$center_lng = $output[0]->lon;
				}
			}
		}
	}
}

if ( empty( $atts['width'] ) ) {
	$map_width = '100%';
} else {
	$map_width = str_replace( 'px', '', $atts['width'] ) . 'px'; }

if ( empty( $atts['height'] ) ) {
	$map_height = '500px';
} else {
	$map_height = str_replace( 'px', '', $atts['height'] ) . 'px'; }

if ( empty( $atts['zoom'] ) ) {
	$zoom = 14;
} else {
	$zoom = $atts['zoom']; }

if ( empty( $atts['map_type'] ) ) {
	$map_type = 'mapbox.streets';
} else {
	$map_type = $atts['map_type']; }

if ( empty( $atts['scroll_wheel'] ) ) {
	$scroll_wheel = 'true';
} else {
	$scroll_wheel = $atts['scroll_wheel']; }

if ( empty( $atts['map_draggable'] ) ) {
	$map_draggable = 'true';
} else {
	$map_draggable = $atts['map_draggable']; }

$wpomp_local = array();
if (isset($atts['language'])   ) {
	$wpomp_local['language'] = $atts['language'];
} else {
	$wpomp_local['language'] = 'en';

}

$wpomp_local['accesstoken'] = $wpomp_settings['wpomp_api_key'];
$wpomp_local['select_radius']             = esc_html__( 'Select Radius', 'wp-leaflet-maps-pro' );
$wpomp_local['search_placeholder']        = esc_html__( 'Enter address or latitude or longitude or title or city or state or country or postal code here...', 'wp-leaflet-maps-pro' );
$wpomp_local['select']                    = esc_html__( 'Select', 'wp-leaflet-maps-pro' );
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


wp_enqueue_script( 'L.Control.Locate');
wp_enqueue_script( 'Leaflet.fullscreen.min');

wp_enqueue_script('wplmp_osm_api');
wp_enqueue_script('leaflet-autocomplete');
wp_enqueue_script('datatable');

wp_enqueue_script('leaflet-providers');

wp_enqueue_script( 'wplmp_google_map_main' );
wp_enqueue_script( 'wplmp_frontend' );

wp_enqueue_style( 'wplmp-frontend-style' );
wp_enqueue_style( 'L.Control.Locate-style' );
wp_enqueue_style( 'leaflet.fullscreen-style' );
wp_enqueue_style( 'leaflet-autocomplete-style' );


wp_localize_script( 'wplmp_google_map_main', 'wpomp_local', $wpomp_local );

$map_data['map_options'] = array(
	'center_lat'            => $center_lat,
	'center_lng'            => $center_lng,
	'zoom'                  => $zoom,
	'scroll_wheel'          => $scroll_wheel,
	'map_type_id'           => $map_type,
	'draggable'             => ( 'false' === $map_draggable ? false : true ),
	'infowindow_open_event' => 'click',

);

if ( is_array( $marker_array ) ) {
	if ( $marker_array ) {
		foreach ( $marker_array as $marker ) {
			$explode_marker = explode( '|', $marker );
			if ( ! empty( $explode_marker[4] ) ) {
				$wpomp_marker_category_results = $all_categories_name[ sanitize_title( $explode_marker[4] ) ];
				$cat_id                        = $wpomp_marker_category_results->group_map_id;
				$cat_title                     = $wpomp_marker_category_results->group_map_title;
				$icon                          = $wpomp_marker_category_results->group_marker;
			} else {
				$icon = '';
			}

			$id = rand( 1000000, 10000000 );

			$map_data['places'][ $id ] = array(
				'id'       => $id,
				'title'    => $explode_marker[2],
				'content'  => ( $explode_marker[3] ) ? stripcslashes( $explode_marker[3] ) : $explode_marker[2],
				'location' => array(
					'icon'           =>!empty($icon )  ? stripcslashes( $icon ) : WPLMP_ICONS.'marker_default_icon.png',
					'lat'            => $explode_marker[0],
					'lng'            => $explode_marker[1],
					'onclick_action' => 'marker',


				),
			);

			$map_data['places'][ $id ]['categories'][] = array(
				'id'   => stripcslashes( $cat_id ),
				'name' => stripcslashes( $cat_title ),
				'type' => 'category',
				'icon' => !empty($icon) ? stripcslashes( $icon ) : WPLMP_ICONS.'marker_default_icon.png',
			);
		}
	}
}

if ( is_array( $address_array ) ) {
	if ( $address_array ) {
		foreach ( $address_array as $address ) {
			$explode_address = explode( '|', $address );
			$rm_space_ads    = str_replace( ' ', '+', $explode_address[0] );
			$geocode         = wp_remote_get( 'https://nominatim.openstreetmap.org/search?format=json&q='.$rm_space_ads);
			if ( ! isset( $geocode->errors ) ) {
				$output = json_decode( $geocode['body'] );
				$lat    = $output[0]->lat;
				$lng    = $output[0]->lon;
			}
			if ( ! empty( $explode_address[3] ) ) {
				$wpomp_marker_category_results = $all_categories_name[ sanitize_title( $explode_address[3] ) ];
				$cat_id                        = $wpomp_marker_category_results->group_map_id;
				$cat_title                     = $wpomp_marker_category_results->group_map_title;
				$icon                          = $wpomp_marker_category_results->group_marker;
			} else {
				$icon = '';
			}

			$id = rand( 1000000, 10000000 );

			$map_data['places'][ $id ] = array(
				'id'       => $id,
				'title'    =>!empty($explode_address[1]) ? $explode_address[1] : '' ,
				'content'  => !empty($explode_address[2] ) ? stripcslashes( $explode_address[2] ) :(!empty($explode_address[1]) ? $explode_address[1] : ''),
				'location' => array(
					'icon'           => !empty($icon) ? stripcslashes( $icon ) : WPLMP_ICONS.'marker_default_icon.png',
					'lat'            => !empty($lat) ? $lat :'',
					'lng'            => !empty($lng) ? $lng :'' ,
					'onclick_action' => 'marker',
				),
			);

			$map_data['places'][ $id ]['categories'][] = array(
				'id'   => !empty($cat_id) ?   stripcslashes($cat_id) :'',
				'name' => !empty($cat_title) ? stripcslashes( $cat_title ) : '',
				'type' => 'category',
				'icon' => !empty($icon) ? stripcslashes( $icon ) : WPLMP_ICONS.'marker_default_icon.png',
			);
		}
	}
}

$map_data_json = json_encode( $map_data );

$rand_mapid  = rand( 1000000, 10000000 );
$map_output  = '';
$map_output .= "
<script type='text/javascript'>
	/* <![CDATA[ */
	var map_data_" . $rand_mapid . ' = ' . $map_data_json . '
    /* ]]> */
</script>
<style>
    .wpomp_display_map_' . $rand_mapid . ' { width : ' . $map_width . '; height: ' . $map_height . '; }
    .wpomp_display_map_' . $rand_mapid . " img { max-width:none !important; }
</style>

<div class='wpomp_display_map_" . $rand_mapid . "' id='wpomp_display_map_" . $rand_mapid . "'></div>";
$map_output .= "<script>
	jQuery(document).ready(function($) {
	  var map = $('.wpomp_display_map_" . $rand_mapid . "').osm_maps(map_data_" . $rand_mapid . ").data('wpomp_maps');
	});
</script>";
return $map_output;
