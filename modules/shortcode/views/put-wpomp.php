<?php

/**
 * Parse Shortcode and display maps.
 *
 * @package wp-leaflet-maps-pro
 * @author Flipper Code <hello@flippercode.com>
 */

if ( isset( $options['id'] ) ) {
	$map_id = $options['id'];
} else {
	return '';
}

// Fetch map information.
$modelFactory = new WPLMP_MODEL();
$map_obj      = $modelFactory->create_object( 'map' );
$map_record   = $map_obj->fetch( array( array( 'map_id', '=', $map_id ) ) );

if ( ! is_array( $map_record ) || empty( $map_record ) ) {
	return '';
} else {
	$map = $map_record[0];
}
$wpomp_settings = get_option( 'wpomp_settings', true );
$api_key = isset($wpomp_settings['wpomp_api_key'])? $wpomp_settings['wpomp_api_key'] : '';
// Hook accept cookies
if ( isset($wpomp_settings['wpomp_gdpr']) &&  $wpomp_settings['wpomp_gdpr'] == true ) {
	$auto_fix = apply_filters( 'wpomp_accept_cookies', false );
	if ( $auto_fix == false ) {
		if ( isset( $wpomp_settings['wpomp_gdpr_msg'] ) and $wpomp_settings['wpomp_gdpr_msg'] != '' ) {
			return $wpomp_settings['wpomp_gdpr_msg'];
		} else {
			return apply_filters( 'wpomp_nomap_notice', '', $map_id );
		}
	}
}

// End
if ( isset( $options['show'] ) ) {
	$show_option = $options['show'];
} else {
	$show_option = 'default';
}

$shortcode_filters = array();
if ( isset( $options['category'] ) ) {
	$shortcode_filters['category'] = $options['category'];
}
if ( ! empty( $map ) ) {
	$map->map_all_control             = maybe_unserialize( $map->map_all_control );
	$map->map_info_window_setting     = maybe_unserialize( $map->map_info_window_setting );
	$map->map_locations               = maybe_unserialize( $map->map_locations );
	$map->map_infowindow_setting      = maybe_unserialize( $map->map_infowindow_setting );
	$map->map_geotags                 = maybe_unserialize( $map->map_geotags );
}

$map_data_provider = !empty($map->map_all_control['wpomp_map_provider']) ? $map->map_all_control['wpomp_map_provider'] :'openstreet';
$default_map_tile_url = apply_filters('wpomp_default_tile_url','https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',$map);
$map_tile_url = !empty($map->map_all_control['openstreet_url']) ? $map->map_all_control['openstreet_url'] : $default_map_tile_url;

$category_obj          = $modelFactory->create_object( 'group_map' );
$categories            = $category_obj->fetch();
$all_categories        = array();
$all_child_categories  = array();
$all_parent_categories = array();
$all_categories_name   = array();
$location_obj          = $modelFactory->create_object( 'location' );
$marker_category_icons = array();
if ( ! empty( $categories ) ) {
	foreach ( $categories as $category ) {
		$all_categories[ $category->group_map_id ] = $category;
		$all_categories_name[ sanitize_title( $category->group_map_title ) ] = $category;
		$marker_category_icons[ $category->group_map_id ] = $category->group_marker;
		if ( $category->group_parent > 0 ) {
			$all_child_categories[ $category->group_map_id ]    = $category->group_parent;
			$all_parent_categories[ $category->group_parent ][] = $category->group_map_id;
		}
	}
}

if ( ! empty( $map->map_locations ) ) {
	$map_locations = $location_obj->fetch( array( array( 'location_id', 'IN', implode( ',', $map->map_locations ) ) ) );
}
$location_criteria = array(
	'show_all_locations' => false,
	'category__in'       => false,
	'limit'              => 0,
);

$location_criteria = apply_filters( 'wpomp_location_criteria', $location_criteria, $map );
if ( isset( $options['show_all_locations'] ) and $options['show_all_locations'] == 'true' ) {
	$location_criteria['show_all_locations'] = true;
}
if ( isset( $options['limit'] ) and $options['limit'] > 0 ) {
	$location_criteria['limit'] = $options['limit'];
} elseif ( !empty( $_GET['limit'] ) and $map->map_all_control['url_filter'] == 'true' ) {
	$location_criteria['limit'] = sanitize_text_field( $_GET['limit'] );
}
if ( isset( $location_criteria['show_all_locations'] ) and $location_criteria['show_all_locations'] == true ) {
	$map_locations = $location_obj->fetch();
}
if ( isset( $location_criteria['category__in'] ) and is_array( $location_criteria['category__in'] ) ) {
	$shortcode_filters['category'] = implode( ',', $location_criteria['category__in'] );
}
$map_data = array();
// Set map options.
$map_data['places'] = array();
if ( $map->map_all_control['infowindow_openoption'] == 'mouseclick' ) {
	$map->map_all_control['infowindow_openoption'] = 'click';
} elseif ( $map->map_all_control['infowindow_openoption'] == 'mousehover' ) {
	$map->map_all_control['infowindow_openoption'] = 'mouseover';
} elseif ( $map->map_all_control['infowindow_openoption'] == 'mouseover' ) {
	$map->map_all_control['infowindow_openoption'] = 'mouseover';
} else {
	$map->map_all_control['infowindow_openoption'] = 'click';
}
$infowindow_setting = isset($map->map_all_control['infowindow_setting'])? $map->map_all_control['infowindow_setting']:array();
$infowindow_sourcecode = apply_filters( 'wpomp_infowindow_message',$infowindow_setting , $map );
$infowindow_geotags_setting = isset($map->map_all_control['infowindow_geotags_setting'])? $map->map_all_control['infowindow_geotags_setting']:array();
$infowindow_post_view_source = apply_filters( 'wpomp_infowindow_post_message',$infowindow_geotags_setting , $map );
$wpomp_categorydisplayformat = isset($map->map_all_control['wpomp_categorydisplayformat'])? $map->map_all_control['wpomp_categorydisplayformat']:array();
$listing_placeholder_content = apply_filters( 'wpomp_listing_html', $wpomp_categorydisplayformat, $map );
if ( ( is_single() or is_page() ) && isset( $map->map_all_control['current_post'] ) && $map->map_all_control['current_post'] == 'true' ) {
	global $post;
	$post_center_lat = get_post_meta( $post->ID, '_wpomp_metabox_latitude', true );
	$post_center_lng = get_post_meta( $post->ID, '_wpomp_metabox_longitude', true );
	if ( $post_center_lat != '' ) {
		$map->map_all_control['map_center_latitude'] = $post_center_lat;
	}
	if ( $post_center_lng != '' ) {
		$map->map_all_control['map_center_longitude'] = $post_center_lng;
	}
}
if ( !empty( $_GET['zoom'] ) &&    $map->map_all_control['url_filter'] == 'true' ) {
	$options['zoom'] = sanitize_text_field( $_GET['zoom'] );
}
if ( ! isset( $map->map_all_control['nearest_location'] ) ) {
	$map->map_all_control['nearest_location'] = false;
}
if ( ! isset( $map->map_all_control['fit_bounds'] ) ) {
	$map->map_all_control['fit_bounds'] = false;
}
if ( ! isset( $map->map_all_control['show_center_circle'] ) ) {
	$map->map_all_control['show_center_circle'] = false;
}
if ( ! isset( $map->map_all_control['show_center_marker'] ) ) {
	$map->map_all_control['show_center_marker'] = false;
}
if ( ! isset( $map->map_all_control['map_draggable'] ) ) {
	$map->map_all_control['map_draggable'] = true;
}
if ( ! isset( $map->map_all_control['infowindow_close'] ) ) {
	$map->map_all_control['infowindow_close'] = false;
}
if ( ! isset( $map->map_all_control['infowindow_open'] ) ) {
	$map->map_all_control['infowindow_open'] = false;
}
if ( ! isset( $map->map_all_control['infowindow_filter_only'] ) ) {
	$map->map_all_control['infowindow_filter_only'] = false;
}
if ( ! isset( $map->map_all_control['infowindow_iscenter'] ) ) {
	$map->map_all_control['infowindow_iscenter'] = false;
}
if ( ! isset( $map->map_all_control['full_screen_control'] ) ) {
	$map->map_all_control['full_screen_control'] = false;
}

if ( ! isset( $map->map_all_control['search_control'] ) ) {
	$map->map_all_control['search_control'] = false;
}

if ( ! isset( $map->map_all_control['zoom_control'] ) ) {
	$map->map_all_control['zoom_control'] = false;
}
if ( ! isset( $map->map_all_control['scale_control'] ) ) {
	$map->map_all_control['scale_control'] = false;
}
if ( ! isset( $map->map_all_control['map_type_control'] ) ) {
	$map->map_all_control['map_type_control'] = false;
}

if ( ! isset( $map->map_all_control['locateme_control'] ) ) {
	$map->map_all_control['locateme_control'] = false;
}
if ( ! isset( $map->map_all_control['mobile_specific'] ) ) {
	$map->map_all_control['mobile_specific'] = false;
}
if ( ! isset( $map->map_all_control['mobile_specific'] ) ) {
	$map->map_all_control['mobile_specific'] = false;
}
if ( ! isset( $map->map_all_control['map_zoom_level_mobile'] ) ) {
	$map->map_all_control['map_zoom_level_mobile'] = 5;
}
if ( ! isset( $map->map_all_control['map_draggable_mobile'] ) ) {
	$map->map_all_control['map_draggable_mobile'] = true;
}
if ( ! isset( $map->map_all_control['map_scrolling_wheel_mobile'] ) ) {
	$map->map_all_control['map_scrolling_wheel_mobile'] = true;
}
if ( ! isset( $map->map_all_control['map_custom_control'] ) ) {
	$map->map_all_control['map_custom_control'] = false;
}
if ( ! isset( $map->map_all_control['map_infowindow_customisations'] ) ) {
	$map->map_all_control['map_infowindow_customisations'] = false;
}
if ( ! isset( $map->map_all_control['show_infowindow_header'] ) ) {
	$map->map_all_control['show_infowindow_header'] = false;
}
if ( ! isset( $map->map_all_control['url_filter'] ) ) {
	$map->map_all_control['url_filter'] = false;
}
if ( ! isset( $map->map_all_control['bound_map_after_filter'] ) ) {
	$map->map_all_control['bound_map_after_filter'] = false;
}
if ( ! isset( $map->map_all_control['display_reset_button'] ) ) {
	$map->map_all_control['display_reset_button'] = false;
}

$openstreet_styles = array(
		'OpenStreetMap.Mapnik'=>'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
		'OpenStreetMap.DE'=>'https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png',
		'OpenStreetMap.CH'=>'https://tile.osm.ch/switzerland/{z}/{x}/{y}.png',
		'OpenStreetMap.France'=>'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png',
		'OpenStreetMap.HOT'=>'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
		'OpenStreetMap.BZH'=>'https://tile.openstreetmap.bzh/br/{z}/{x}/{y}.png',
		'OpenTopoMap'=>'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
		'Thunderforest.OpenCycleMap'=>'https://dev.{s}.tile.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png',
		'OpenMapSurfer.Roads'=>'https://maps.heigit.org/openmapsurfer/tiles/roads/webmercator/{z}/{x}/{y}.png',
);

$openstreet_styles = apply_filters('wplmp_openstreet_style',$openstreet_styles);
$openstreet_styles_markup = '';
if(count($openstreet_styles)>0){
	$openstreet_styles_markup .='<select class="wpomp_map_type">';
	foreach ($openstreet_styles as $key => $style) {	
		$openstreet_styles_markup .='<option value="'.$key.'">'.$key.'</option>';
	}
	$openstreet_styles_markup .='</select>';
}
$map_box_styles = array(
	'streets-v11'=>'streets',
	'light-v10'=>'light',
	'dark-v10'=>'dark',
	'outdoors-v11'=>'outdoors',
	'satellite-v9'=>'satellite'
);

$map_box_styles = apply_filters('wplmp_mapbox_style',$map_box_styles);
$map_box_styles_markup = '';
if(count($map_box_styles)>0){
	$map_box_styles_markup .='<select class="wpomp_mapbox_type">';
	foreach ($map_box_styles as $mkey=>$mstyle) {	
		$map_box_styles_markup .='<option value="'.$mkey.'">'.ucfirst($mstyle).'</option>';
	}
	$map_box_styles_markup .='</select>';
}

$mapquest_styles = array('Map','Hybrid','Satellite','Dark','Light');
$mapquest_styles_markup = '';
if(count($mapquest_styles)>0){
	$mapquest_styles_markup .='<select class="wpomp_mapquest_type">';
	foreach ($mapquest_styles as $mstyle) {	
		$mapquest_styles_markup .='<option value="'.$mstyle.'">'.ucfirst($mstyle).'</option>';
	}
	$mapquest_styles_markup .='</select>';
}

$bingmaps_styles = array('CanvasGray','Aerial','AerialWithLabelsOnDemand','RoadOnDemand','CanvasDark','CanvasLight');
$binmaps_styles_markup = '';
if(count($bingmaps_styles)>0){
	$binmaps_styles_markup .='<select class="wpomp_bingmap_type">';
	foreach ($bingmaps_styles as $bstyle) {	
		$binmaps_styles_markup .='<option value="'.$bstyle.'">'.ucfirst($bstyle).'</option>';
	}
	$binmaps_styles_markup .='</select>';
}


$map_data['map_options'] = array(
	'center_lat'                     => sanitize_text_field( $map->map_all_control['map_center_latitude'] ),
	'center_lng'                     => sanitize_text_field( $map->map_all_control['map_center_longitude'] ),
	'zoom'                           => ( isset( $options['zoom'] ) ) ? intval( $options['zoom'] ) : intval( $map->map_zoom_level ),
	'center_by_nearest'              => ( 'true' == sanitize_text_field( $map->map_all_control['nearest_location'] ) ),
	'fit_bounds'                     => ( 'true' == sanitize_text_field( $map->map_all_control['fit_bounds'] ) ),
	'attribution_screen_control' => (!(isset($map->map_all_control['attribution_screen_control']))),
	'attribution_screen_control_position' => isset($map->map_all_control['attribution_screen_control_position']) ? $map->map_all_control['attribution_screen_control_position'] : '',

	'center_circle_fillcolor'        => sanitize_text_field( $map->map_all_control['center_circle_fillcolor'] ),
	'center_circle_strokecolor'      => sanitize_text_field( $map->map_all_control['center_circle_strokecolor'] ),
	'center_circle_fillopacity'      => !empty($map->map_all_control['center_circle_fillopacity']) ? sanitize_text_field( $map->map_all_control['center_circle_fillopacity'] ) : 1,
	'center_circle_strokeopacity'    => !empty($map->map_all_control['center_circle_strokeopacity']) ? sanitize_text_field( $map->map_all_control['center_circle_strokeopacity'] ):1,
	'center_circle_radius'           => !empty($map->map_all_control['center_circle_radius']) ? sanitize_text_field( $map->map_all_control['center_circle_radius'] ) : 5,
	'show_center_circle'             => ( sanitize_text_field( $map->map_all_control['show_center_circle'] ) == 'true' ),
	'show_center_marker'             => ( sanitize_text_field( $map->map_all_control['show_center_marker'] ) == 'true' ),
	'center_marker_icon'             => !empty($map->map_all_control['marker_center_icon']) ?  esc_url( $map->map_all_control['marker_center_icon'] ) : WPLMP_IMAGES.'default_marker.png',
	'center_marker_infowindow'       => wpautop( wp_unslash( $map->map_all_control['show_center_marker_infowindow'] ) ),
	'center_circle_strokeweight'     => !empty($map->map_all_control['center_circle_strokeweight']) ?   sanitize_text_field( $map->map_all_control['center_circle_strokeweight'] ) : 1,
	'draggable'                      => ( sanitize_text_field( $map->map_all_control['map_draggable'] ) != 'false' ),
	'scroll_wheel'                   => ( sanitize_text_field( $map->map_scrolling_wheel ) != 'false' ),
	'marker_default_icon'            => !empty($map->map_all_control['marker_default_icon']) ? esc_url( $map->map_all_control['marker_default_icon'] ) : WPLMP_ICONS . 'marker_default_icon.png',
	'infowindow_setting'             => wpautop( wp_unslash( $infowindow_sourcecode ) ),
	'infowindow_geotags_setting'     => wpautop( wp_unslash( $infowindow_post_view_source ) ),
	'infowindow_skin'                => ( isset( $map->map_all_control['location_infowindow_skin'] ) ) ? $map->map_all_control['location_infowindow_skin'] : array(
		'name'       => 'default',
		'type'       => 'infowindow',
		'sourcecode' => $infowindow_sourcecode,
	),
	'infowindow_post_skin'           => ( isset( $map->map_all_control['post_infowindow_skin'] ) ) ? $map->map_all_control['post_infowindow_skin'] : array(
		'name'       => 'default',
		'type'       => 'post',
		'sourcecode' => $infowindow_post_view_source,
	),
	'close_infowindow_on_map_click'  => ( 'true' == $map->map_all_control['infowindow_close'] ),
	'default_infowindow_open'        => ( 'true' == $map->map_all_control['infowindow_open'] ),
	'infowindow_open_event'          => ( $map->map_all_control['infowindow_openoption'] ) ? $map->map_all_control['infowindow_openoption'] : 'click',
	'infowindow_filter_only'         => ( $map->map_all_control['infowindow_filter_only'] == 'true' ),
	'infowindow_click_change_zoom'   => isset($map->map_all_control['infowindow_zoomlevel']) ? (int) $map->map_all_control['infowindow_zoomlevel'] : 5,
	'infowindow_click_change_center' => ( 'true' == $map->map_all_control['infowindow_iscenter'] ),
	'full_screen_control'            => ( $map->map_all_control['full_screen_control'] != 'false' ),
	'search_control'                 => ( $map->map_all_control['search_control'] != false),
	'zoom_control'                   => ( $map->map_all_control['zoom_control'] != 'false' ),
	'scale_control'                   => ( $map->map_all_control['scale_control'] != 'false' ),
	'map_type_control'               => ( $map->map_all_control['map_type_control'] != 'false' ),
	'locateme_control'               => ( $map->map_all_control['locateme_control'] == 'true' ),
	'mobile_specific'                => ( $map->map_all_control['mobile_specific'] == 'true' ),
	'zoom_mobile'                    => intval( $map->map_all_control['map_zoom_level_mobile'] ),
	'draggable_mobile'               => ( sanitize_text_field( $map->map_all_control['map_draggable_mobile'] ) != 'false' ),
	'scroll_wheel_mobile'            => ( sanitize_text_field( $map->map_all_control['map_scrolling_wheel_mobile'] ) != 'false' ),
	'full_screen_control_position'   => isset($map->map_all_control['full_screen_control_position']) ? $map->map_all_control['full_screen_control_position']:'topleft',
	'search_control_position'        => isset($map->map_all_control['search_control_position']) ? $map->map_all_control['search_control_position'] : 'topleft' ,
	'locateme_control_position'      => isset($map->map_all_control['locateme_control_position']) ? $map->map_all_control['locateme_control_position'] : 'topleft' ,
	'zoom_control_position'          => $map->map_all_control['zoom_control_position'],
	'map_type_control_position'      => (isset($map->map_all_control['map_type_control_position']) && !empty($map->map_all_control['map_type_control_position'])) ? $map->map_all_control['map_type_control_position'] : 'bottomright',
	'screens'                        => isset( $map->map_all_control['screens']) ?  $map->map_all_control['screens'] :'',
	'map_infowindow_customisations'  => ( $map->map_all_control['map_infowindow_customisations'] == 'true' ),
	'infowindow_width'               => ( empty( $map->map_all_control['infowindow_width'] ) || $map->map_all_control['infowindow_width'] == '0' ) ? '100%' : $map->map_all_control['infowindow_width'] . 'px',
	'infowindow_border_color'        => ( isset($map->map_all_control['infowindow_border_color']) && $map->map_all_control['infowindow_border_color'] != '' && $map->map_all_control['infowindow_border_color'] != '#' ) ? sanitize_text_field( $map->map_all_control['infowindow_border_color'] ) : 'rgba(0, 0, 0, 0.0980392)',
	'infowindow_bg_color'            => ( isset($map->map_all_control['infowindow_bg_color']) && $map->map_all_control['infowindow_bg_color'] != '' && $map->map_all_control['infowindow_bg_color'] != '#' ) ? sanitize_text_field( $map->map_all_control['infowindow_bg_color'] ) : '#fff',
	'show_infowindow_header'         => ( $map->map_all_control['show_infowindow_header'] == 'true' ),
	'min_zoom'                       => $map->map_all_control['map_minzoom_level'],
	'max_zoom'                       => $map->map_all_control['map_maxzoom_level'],
	'url_filters'                    => ( $map->map_all_control['url_filter'] == 'true' ),
	'doubleclickzoom'                => ( isset( $map->map_all_control['doubleclickzoom'] ) ),
	'map_data_provider'				 => $map_data_provider,
	'map_tile_url'				     => $map_tile_url,
	'openstreet_styles'				 => $openstreet_styles,
	'openstreet_styles_markup'	     => $openstreet_styles_markup,
	'map_box_styles_markup'	         => $map_box_styles_markup,
	'mapquest_styles_markup'	     => $mapquest_styles_markup,
	'binmaps_styles_markup'	         => $binmaps_styles_markup,

);

if ( $map->map_all_control['map_infowindow_customisations'] == 'true' ) {
	?>
<style type="text/css">
#map<?php echo esc_attr( $map_id ); ?> .wpomp_infowindow .wpomp_iw_head, #map<?php echo esc_attr( $map_id ); ?> .post_body .geotags_link, #map<?php echo esc_attr( $map_id ); ?> .post_body .geotags_link a{
height: 28px;
font-weight: 600;
line-height: 27px;
font-size:16px;
	<?php echo esc_attr(isset($map->map_all_control['infowindow_header_font_color']) && ( $map->map_all_control['infowindow_header_font_color'] != '' && $map->map_all_control['infowindow_header_font_color'] != '#' ) ? 'color: ' . sanitize_text_field( $map->map_all_control['infowindow_header_font_color'] ) . ';' : 'color:#fff;' ); ?>
	<?php echo esc_attr( isset($map->map_all_control['infowindow_header_bgcolor']) && ( $map->map_all_control['infowindow_header_bgcolor'] != '' && $map->map_all_control['infowindow_header_bgcolor'] != '#' ) ? 'background-color: ' . sanitize_text_field( $map->map_all_control['infowindow_header_bgcolor'] ) . ';' : 'background-color:#3498db;' ); ?>
}
#map<?php echo esc_attr( $map_id ); ?> .wpomp_infowindow .wpomp_iw_head_content, .wpomp_infowindow .wpomp_iw_content, #map<?php echo esc_attr( $map_id ); ?> .post_body .geotags_link{padding-left:5px;}
#map<?php echo esc_attr( $map_id ); ?> .wpomp_infowindow .wpomp_iw_content{
min-height: 50px!important;
min-width: 150px!important;
padding-top:5px;
}
#map<?php echo esc_attr( $map_id ); ?> .leaflet-popup-content{
	position: relative;
    overflow: hidden;
	<?php echo esc_attr( ( $map->map_all_control['infowindow_border_color'] != '' && $map->map_all_control['infowindow_border_color'] != '#' ) ? 'box-shadow: ' . sanitize_text_field( $map->map_all_control['infowindow_border_color'] ) . ' 0px 1px 4px -1px;' : 'box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px;' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_border_color'] != '' && $map->map_all_control['infowindow_border_color'] != '#' ) ? 'border: 1px solid ' . sanitize_text_field( $map->map_all_control['infowindow_border_color'] ) . ';' : 'border: 1px solid rgba(0, 0, 0, 0.2);' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_bg_color'] != '' && $map->map_all_control['infowindow_bg_color'] != '#' ) ? 'background-color: ' . sanitize_text_field( $map->map_all_control['infowindow_bg_color'] ) . ';' : 'background-color:#fff;' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_border_radius'] != '' ) ? 'border-radius: ' . sanitize_text_field( $map->map_all_control['infowindow_border_radius'] ) . 'px;' : 'border-radius:3px;' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_width'] != '' ) ? 'width: ' . sanitize_text_field( $map->map_all_control['infowindow_width'] ) . 'px !important;' : '' ); ?>
}
#map<?php echo esc_attr( $map_id ); ?> .wpomp_infowindow, #map<?php echo esc_attr( $map_id ); ?> .post_body{
float: left;
position: relative;
	<?php echo esc_attr( ( $map->map_all_control['infowindow_border_color'] != '' && $map->map_all_control['infowindow_border_color'] != '#' ) ? 'box-shadow: ' . sanitize_text_field( $map->map_all_control['infowindow_border_color'] ) . ' 0px 1px 4px -1px;' : 'box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px;' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_border_color'] != '' && $map->map_all_control['infowindow_border_color'] != '#' ) ? 'border: 1px solid ' . sanitize_text_field( $map->map_all_control['infowindow_border_color'] ) . ';' : 'border: 1px solid rgba(0, 0, 0, 0.2);' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_bg_color'] != '' && $map->map_all_control['infowindow_bg_color'] != '#' ) ? 'background-color: ' . sanitize_text_field( $map->map_all_control['infowindow_bg_color'] ) . ';' : 'background-color:#fff;' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_border_radius'] != '' ) ? 'border-radius: ' . sanitize_text_field( $map->map_all_control['infowindow_border_radius'] ) . 'px;' : 'border-radius:3px;' ); ?>
	<?php echo esc_attr( ( $map->map_all_control['infowindow_width'] != '' ) ? 'width: ' . sanitize_text_field( $map->map_all_control['infowindow_width'] ) . 'px;' : '' ); ?>
}
#map<?php echo esc_attr( $map_id ); ?> .leaflet-popup-content-wrapper, #map<?php echo esc_attr( $map_id ); ?> .leaflet-popup-tip{
	<?php echo esc_attr( ( $map->map_all_control['infowindow_border_color'] != '' && ($map->map_all_control['infowindow_border_color'] != '#') ) ? 'background: ' . sanitize_text_field( $map->map_all_control['infowindow_border_color'] ) : 'background: '.sanitize_text_field($map->map_all_control['infowindow_bg_color'] ) ); ?>
}
</style>
	<?php
}
$map_data['map_options']['bound_map_after_filter'] = ( 'true' == $map->map_all_control['bound_map_after_filter'] );
$map_data['map_options']['display_reset_button']   = ( 'true' == $map->map_all_control['display_reset_button'] );
$map_data['map_options']['map_reset_button_text']  = isset( $map->map_all_control['map_reset_button_text']) ?  $map->map_all_control['map_reset_button_text'] : esc_html__('Reset','wp-leaflet-maps-pro') ;
$map_data['map_options']['width'] = sanitize_text_field( $map->map_width );
$map_data['map_options']['width'] = sanitize_text_field( $map->map_width );
$map_data['map_options']['height'] = sanitize_text_field( $map->map_height );
$map_data['map_options'] = apply_filters( 'wpomp_maps_options', $map_data['map_options'], $map );
if ( isset( $options['width'] ) and $options['width'] != '' ) {
	$map_data['map_options']['width'] = $options['width'];
}
if ( isset( $options['height'] ) and $options['height'] != '' ) {
	$map_data['map_options']['height'] = $options['height'];
}
if ( isset( $map_data['map_options']['width'] ) ) {
	$width = $map_data['map_options']['width'];
} else {$width = '100%'; }
if ( isset( $map_data['map_options']['height'] ) ) {
	$height = $map_data['map_options']['height'];
} else {$height = '300px'; }
if ( '' != $width and strstr( $width, '%' ) === false ) {
	$width = str_replace( 'px', '', $width ) . 'px';
}

if ( '' == $width ) {
	$width = '100%';
}
if ( strstr( $height, '%' ) === false ) {
	$height = str_replace( 'px', '', $height ) . 'px';
} else {
	$height = str_replace( '%', '', $height ) . 'px';
}


wp_enqueue_style( 'L.Control.Locate-style');
wp_enqueue_style('leaflet.fullscreen-style');
wp_enqueue_style('leaflet-autocomplete-style');
wp_enqueue_style( 'wplmp-frontend-style');

if($map_data_provider=='mapbox'){
	wp_enqueue_style('mapbox_style');
	wp_enqueue_script('mapbox_script');
}

if($map_data_provider=='mapquest'){
	wp_enqueue_script('mapquest');
	wp_enqueue_style('mapquest_style');
}



wp_enqueue_script('L.Control.Locate');
wp_enqueue_script('Leaflet.fullscreen.min');
wp_enqueue_script('wplmp_osm_api');
wp_enqueue_script('leaflet-autocomplete');

wp_enqueue_script('accordion-script');
wp_enqueue_script('datatable');
wp_enqueue_script('webfont');
wp_enqueue_script('leaflet-providers');

if($map_data_provider=='bingmap'){
	wp_enqueue_script('Bing');
}

wp_enqueue_script( 'wplmp_frontend');
wp_enqueue_script( 'wplmp_google_map_main' );

if ( !empty( $map->map_all_control['location_infowindow_skin'] ) and is_array( $map->map_all_control['location_infowindow_skin'] )  ) {

	$skin_data = $map->map_all_control['location_infowindow_skin'];
	$css_file  = WPLMP_URL . 'templates/' . $skin_data['type'] . '/' . $skin_data['name'] . '/' . $skin_data['name'] . '.css';
	wp_enqueue_style( 'fc_wplmp_' . $skin_data['type'] . '_' . $skin_data['name'], $css_file );
}
if ( !empty( $map->map_all_control['post_infowindow_skin'] ) and is_array( $map->map_all_control['post_infowindow_skin'] ) ) {
	$skin_data = $map->map_all_control['post_infowindow_skin'];
	$css_file  = WPLMP_URL . 'templates/' . $skin_data['type'] . '/' . $skin_data['name'] . '/' . $skin_data['name'] . '.css';
	wp_enqueue_style( 'fc_wplmp_' . $skin_data['type'] . '_' . $skin_data['name'], $css_file );
}
if ( !empty( $map->map_all_control['item_skin'] ) and is_array( $map->map_all_control['item_skin'] ) ) {
	$skin_data = $map->map_all_control['item_skin'];
	$css_file  = WPLMP_URL . 'templates/' . $skin_data['type'] . '/' . $skin_data['name'] . '/' . $skin_data['name'] . '.css';
	wp_enqueue_style( 'fc_wplmp_' . $skin_data['type'] . '_' . $skin_data['name'], $css_file );
}
$map_custom_filters = array();
if ( isset( $map->map_all_control['wpomp_display_custom_filters'] ) && $map->map_all_control['wpomp_display_custom_filters'] == 'true' ) {
	$map_custom_filters = array_map( array( $map_obj, 'wpomp_array_map' ), $map->map_all_control['custom_filters'] );
	$map_custom_filters = array_map( 'trim', $map_custom_filters );
}
if ( isset( $map_locations ) && is_array( $map_locations ) ) {
	$added_extra_fields = maybe_unserialize( get_option( 'wpomp_location_extrafields' ) );
	$loc_count          = 0;
	foreach ( $map_locations as $location ) {

		if(empty($location->location_latitude) || empty($location->location_longitude) )
			continue;
		
		$location_categories = array();
		$is_continue         = true;
		if ( empty( $location->location_group_map ) ) {
			$location_categories[] = array(
				'id'               => '',
				'name'             => 'Uncategories',
				'type'             => 'category',
				'extension_fields' => $loc_category->extensions_fields,
				'icon'             => WPLMP_ICONS.'marker_default_icon.png',
			);
		} else {
			foreach ( $location->location_group_map as $key => $loc_category_id ) {
				if( isset($all_categories[ $loc_category_id ]) ) {
					$loc_category = $all_categories[ $loc_category_id ];
					$location_categories[] = array(
						'id'               => $loc_category->group_map_id,
						'name'             => $loc_category->group_map_title,
						'type'             => 'category',
						'extension_fields' => $loc_category->extensions_fields,
						'icon'             => $loc_category->group_marker,
					);	
				}
			}
		}
		// Extra Fields in location.
		$extra_fields          = array();
		$location_extra_fields = array();
		$extra_fields_filters  = array();
		if ( isset( $added_extra_fields ) &&  !empty($added_extra_fields) && (count($added_extra_fields)>0) ) {


			foreach ( $added_extra_fields as $i => $label ) {
				$field_name                  = sanitize_title( $label );
				if( isset($location->location_extrafields[ $field_name ]) ) {
					$extra_fields[ $field_name ] = $location->location_extrafields[ $field_name ];
				}
				if ( array_search( '{' . $field_name . '}', $map_custom_filters ) !== false ) {
					$values = array();
					if ( isset($location->location_extrafields[ $field_name ]) && strpos( $location->location_extrafields[ $field_name ], ',' ) !== false ) {
						$values = explode( ',', $location->location_extrafields[ $field_name ] );
					}
					if ( ! empty( $values ) ) {
						foreach ( $values as $k => $val ) :
							if ( isset($extra_fields_filters[ $field_name ]) && is_array($extra_fields_filters[ $field_name ]) && ! in_array( trim( $val ), $extra_fields_filters [ $field_name ] ) && trim( $val ) != '' && trim( $val ) != null ) {
								$extra_fields_filters[ $field_name ][] = trim( $val );
							}
							$location_extra_fields[ $field_name ][] = trim( $val );
			endforeach;

					} elseif ( isset($extra_fields_filters[ $field_name ]) && is_array($extra_fields_filters[ $field_name ]) && ! in_array( trim( $location->location_extrafields[ $field_name ] ), $extra_fields_filters [ $field_name ] ) ) {
						$extra_fields_filters[ $field_name ][]  = trim( $location->location_extrafields[ $field_name ] );
						$location_extra_fields[ $field_name ][] = trim( $location->location_extrafields[ $field_name ] );
					} else {
						$location_extra_fields[ $field_name ][] = (isset($location->location_extrafields[ $field_name ])) ? trim( $location->location_extrafields[ $field_name ] ) : '';
					}
				}
			}
		}
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $name => $term ) {
				$name = trim( $name );
				foreach ( $term as $t ) {
					if ( array_search( '{' . $name . '}', $map_custom_filters ) !== false && isset( $location->location_settings[ 'teaxonomy_' . $name . '_terms' ] ) && in_array( $t[0], $location->location_settings[ 'teaxonomy_' . $name . '_terms' ] ) ) {
						$extra_fields_filters[ $name ][] = trim( $t[1] );
					}
				}
			}
		}
		ksort( $extra_fields_filters );
		if ( is_array( $location_categories ) ) {
			$high_order = 0;
			foreach ( $location_categories as $cat_order ) {
				if ( $cat_order['extension_fields']['cat_order'] ) {
					if ( $cat_order['extension_fields']['cat_order'] > $high_order ) {
						$high_order = $cat_order['extension_fields']['cat_order'];
					}
				}
			}
			$extra_fields['listorder'] = $high_order;
		} else {
			$extra_fields['listorder'] = 0;
		}
		$onclick = isset( $location->location_settings['onclick'] ) ? $location->location_settings['onclick'] : 'marker';
		if ( isset( $location->location_settings['featured_image'] ) and $location->location_settings['featured_image'] != '' ) {
			$marker_image = "<div class='fc-feature-img'><img alt='" . esc_attr( $location->location_title ) . "' src='" . $location->location_settings['featured_image'] . "' class='wpomp_marker_image fc-item-featured_image fc-item-large' /></div>";
		} else {
			$marker_image = '';
		}
		if( !isset($location->location_settings['hide_infowindow']) ) {
			$location->location_settings['hide_infowindow'] = false;
		}
		$cats_with_order_id = array();
		$c_icon = isset( $location_categories[0]['icon'] ) ? $location_categories[0]['icon'] : $map_data['map_options']['marker_default_icon'];
		foreach($location_categories as $key1 => $cat) {
			if(!empty($cat['extension_fields']['cat_order'])){
				$cats_with_order_id[$key1] = $cat['extension_fields']['cat_order'];	
			}
		}
		if(!empty($cats_with_order_id) && count($cats_with_order_id)>0){
			$top_priority_key = min(array_keys($cats_with_order_id, min($cats_with_order_id)));
			$c_icon = isset( $location_categories[$top_priority_key]['icon'] ) ? $location_categories[$top_priority_key]['icon'] : $map_data['map_options']['marker_default_icon'];
		}

		$map_data['places'][ $loc_count ] = array(
			'id'             => $location->location_id,
			'title'          => $location->location_title,
			'address'        => $location->location_address,
			'source'         => 'manual',
			'content'        => ( '' != $location->location_messages ) ? do_shortcode( stripcslashes( $location->location_messages ) ) : '',
			'location'       => array(
				'icon'                    => $c_icon ,
				'lat'                     => $location->location_latitude,
				'lng'                     => $location->location_longitude,
				'city'                    => $location->location_city,
				'state'                   => $location->location_state,
				'country'                 => $location->location_country,
				'onclick_action'          => $onclick,
				'redirect_custom_link'    => isset($location->location_settings['redirect_link']) ? $location->location_settings['redirect_link'] : '',
				'marker_image'            => $marker_image,
				'open_new_tab'            => isset($location->location_settings['redirect_link_window']) ? $location->location_settings['redirect_link_window'] : '',
				'postal_code'             => $location->location_postal_code,
				'draggable'               => ( 'true' == $location->location_draggable ),
				'infowindow_default_open' => ( 'true' == $location->location_infowindow_default_open ),
				'infowindow_disable'      => ( $location->location_settings['hide_infowindow'] !== 'false' ),
				'zoom'                    => 5,
				'extra_fields'            => $extra_fields,
			),
			'categories'     => $location_categories,
			'custom_filters' => $extra_fields_filters,
		);
		$loc_count++;
	}
}
// Geo tags for leaflet maps pro.
if ( ! empty( $map->map_all_control['geo_tags'] ) && $map->map_all_control['geo_tags'] == 'true' ) {

	$geo_filters = array_filter( $map->map_geotags );
	if ( is_array( $geo_filters ) ) {
		foreach ( $geo_filters as $filter_post_type => $filter ) {
			$filter_array[] = array( $filter_post_type => $filter );
		}
	}
}
$screens = array( 'post', 'page' );
$args = array(
	'public'   => true,
	'_builtin' => false,
);
$output            = 'names';
$operator          = 'and';
$post_types        = get_post_types( $args, $output, $operator );
$custom_post_types = array( 'post', 'page' );
$all_post_types    = array_merge( $post_types, $custom_post_types );
$all_post_types    = apply_filters( 'wpomp_post_types', $all_post_types, $map );
if ( is_array( $all_post_types ) ) {
	$selected_values = maybe_unserialize( $wpomp_settings['wpomp_allow_meta'] );
	
	foreach ( $all_post_types as $post_type ) {
		if ( is_array( $selected_values ) ) {
			if ( in_array( $post_type, $selected_values ) ) {
				continue;
			}
		}
		$filter_array[] = array(
			$post_type => array(
				'address'   => '_wpomp_location_address',
				'latitude'  => '_wpomp_metabox_latitude',
				'longitude' => '_wpomp_metabox_longitude',
				'category'  => '_wpomp_metabox_marker_id',
				'acf_key'   => (isset($map->map_all_control['wpomp_acf_field_name']) && $map->map_all_control['wpomp_acf_field_name'] != '' ) ? $map->map_all_control['wpomp_acf_field_name'] : '',
			),
		);
	}
}

if ( ! empty( $filter_array ) ) {
	foreach ( $filter_array as $filter ) {
		foreach ( $filter as $key => $value ) {
			if ( 'geo_tags' != $key ) {
				$custom_meta_keys = array();
				if ( ! empty( $value['acf_key'] ) ) {
					$custom_meta_keys['relation'] = 'OR';
					$custom_meta_keys[0]          = array(
						'key'     => $value['acf_key'],
						'value'   => '',
						'compare' => '!=',
					);
					if ( ! empty( $value['latitude'] ) ) {
						$custom_meta_keys[1]['relation'] = 'AND';
						$custom_meta_keys[1][0]          = array(
							'key'     => $value['latitude'],
							'value'   => '',
							'compare' => '!=',
						);
					}
					if ( ! empty( $value['longitude'] ) ) {
						$custom_meta_keys[1][1] = array(
							'key'     => $value['longitude'],
							'value'   => '',
							'compare' => '!=',
						);
					}
				} else {
					if ( ! empty( $value['latitude'] ) ) {
						$custom_meta_keys[] = array(
							'key'     => $value['latitude'],
							'value'   => '',
							'compare' => '!=',
						);
					}

					if ( ! empty( $value['longitude'] ) ) {
						$custom_meta_keys[] = array(
							'key'     => $value['longitude'],
							'value'   => '',
							'compare' => '!=',
						);

					}
					$custom_meta_keys = array( $custom_meta_keys );
				}
				if ( ( is_single() or is_page() ) and isset( $options['current_post_only'] ) and $options['current_post_only'] == 'true' ) {
					global $post;
					$args = array(
						'p'              => $post->ID,
						'fields'		 => 'ids',
						'post_type'      => $key,
						'posts_per_page' => -1,
						'meta_query'     => array( $custom_meta_keys ),
						'post_status'    => array( 'publish' ),
					);

				} else {
					$args = array(
						'post_type'      => $key,
						'fields'		 => 'ids',
						'posts_per_page' => -1,
						'meta_query'     => array( $custom_meta_keys ),
						'post_status'    => array( 'publish' ),
					);
				}
				$args            = apply_filters( 'wpomp_post_args', $args, $map );
				$wpomp_the_query = new WP_Query( $args );
				if ( $wpomp_the_query->have_posts() ) {
					while ( $wpomp_the_query->have_posts() ) {
						$wpomp_the_query->the_post();
						global $post;

						$places         = array();
						$content        = $infowindow_post_view_source;
						$category_names = '';
						if ( isset( $value['acf_key'] ) ) {
							$acf_key        = get_post_meta( $post, $value['acf_key'], true );
						} else {
							$acf_key = array();
						}
						if ( empty( $acf_key['lat'] ) && empty( $acf_key['lng'] ) ) {
							if ( empty( $value['latitude'] ) or empty( $value['longitude'] ) ) {
								continue; }
								// Check if meta post is assigned to $map->map_id.
							if ( '_wpomp_location_address' == $value['address'] ) {
								$wpomp_map_ids = get_post_meta( $post, '_wpomp_map_id', true );
								$wpomp_map_id  = maybe_unserialize( $wpomp_map_ids );
								if ( ! is_array( $wpomp_map_id ) ) {
									$wpomp_map_id = array( $wpomp_map_ids );
								}
								if ( ! in_array( $map->map_id, $wpomp_map_id ) ) {
									continue;
								}
							}
						}
						$replace_data['post_title']   = get_the_title();
						$replace_data['post_excerpt'] = get_the_excerpt();
						$replace_data['post_content'] = get_the_content();
						$replace_data['post_link']    = get_permalink( $post );
						$categories                   = get_the_category();
						$category_names               = array();
						if ( ! empty( $categories ) ) {
							foreach ( $categories as $category ) {
								$category_names[] = $category->name;
							}
						}
						$delimiter      = apply_filters( 'wpomp_taxonomy_separator', ', ', $map );
						$category_names = implode( $delimiter, $category_names );
						$replace_data['post_categories'] = $category_names;
						$posttags  = get_the_tags();
						$tag_names = array();
						if ( $posttags ) {
							foreach ( $posttags as $tag ) {
								$tag_names[] = $tag->name;
							}
						}
						$tag_names = implode( $delimiter, $tag_names );
						$post_featured_image       = '';
						$replace_data['post_tags'] = $tag_names;
						$feature_image_size        = apply_filters( 'wpomp_featured_image_size', 'medium', $post, $map );
						$thumbnail_id = get_post_thumbnail_id( $post );
						$featured_image            = wp_get_attachment_image_src( $thumbnail_id, $feature_image_size );
						$image_alt                 = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
						if ( $image_alt == '' ) {
							$image_alt = $replace_data['post_title'];
						}
						if ( isset( $featured_image[0] ) && $featured_image[0] != '' ) {
							$post_featured_image = '<div class="fc-feature-img"><img loading="lazy" decoding="async" alt="' . esc_attr( $image_alt ) . '" width="' . $featured_image[1] . '" height="' . $featured_image[2] . '" src="' . $featured_image[0] . '" class="wp-post-image   wpomp_featured_image" ></div>';

						} else {
							$post_featured_image = '';
						}
						$replace_data['post_featured_image'] = apply_filters( 'wpomp_featured_image', $post_featured_image, $post, $map->map_id );
						$replace_data['marker_image']        = $replace_data['post_featured_image'];
						// Display custom fields here.
						$matches        = array();
						$custom_fields  = array();
						$custom_filters = array();
						preg_match_all( '/{%(.*?)%}/', $content, $matches );
						if ( isset( $matches[0] ) ) {
							foreach ( $matches[0] as $k => $m ) {
								$post_meta_key                               = $matches[1][ $k ];
								$post_meta_data 							 = get_post_meta( $post, $post_meta_key, true );
								$meta_value                                  = !empty( $post_meta_data) ? $post_meta_data : '';
								$replace_data[ '%' . $post_meta_key . '%' ]  = $meta_value;
								$custom_fields[ '%' . $post_meta_key . '%' ] = $meta_value;
							}
						}
						if ( empty( $custom_fields ) ) {
							$listing_content = stripslashes( trim( $listing_placeholder_content ) );
							preg_match_all( '/{%(.*?)%}/', $listing_content, $matches );
							if ( isset( $matches[0] ) ) {
								foreach ( $matches[0] as $k => $m ) {
											$post_meta_key                               = $matches[1][ $k ];
											$post_meta_data = get_post_meta( $post, $post_meta_key, true );
											$meta_value                                  = !empty( $post_meta_data ) ? $post_meta_data : '';
											$replace_data[ '%' . $post_meta_key . '%' ]  = $meta_value;
											$custom_fields[ '%' . $post_meta_key . '%' ] = $meta_value;
								}
							}
						}
						preg_match_all( '/{\s*taxonomy\s*=\s*(.*?)}/', $content, $matches );
						if ( isset( $matches[0] ) ) {
							foreach ( $matches[0] as $k => $m ) {
								$post_meta_key = $matches[1][ $k ];
								$terms         = wp_get_post_terms( $post, $post_meta_key, array( 'fields' => 'all' ) );
								$meta_value    = '';
								if ( $terms ) {
									$tags_links = array();
									foreach ( $terms as $tag ) {
										$tags_links[] = $tag->name;
									}
									if ( ! empty( $tags_links ) ) {
										$meta_value = implode( ', ', $tags_links );
									}
								}
								$replace_data[ 'taxonomy=' . $post_meta_key ]  = $meta_value;
								$custom_fields[ 'taxonomy=' . $post_meta_key ] = $meta_value;
							}
						}
						if ( empty( $custom_fields ) ) {
							$listing_content = stripslashes( trim( $listing_placeholder_content ) );
							preg_match_all( '/{\s*taxonomy\s*=\s*(.*?)}/', $listing_content, $matches );
							if ( isset( $matches[0] ) ) {
								foreach ( $matches[0] as $k => $m ) {
									$post_meta_key = $matches[1][ $k ];
									$terms         = wp_get_post_terms( $post, $post_meta_key, array( 'fields' => 'all' ) );
									$meta_value    = '';
									if ( $terms ) {
										$tags_links = array();
										foreach ( $terms as $tag ) {
											$tags_links[] = $tag->name;
										}
										if ( ! empty( $tags_links ) ) {
											$meta_value = implode( ', ', $tags_links );
										}
									}
										$replace_data[ 'taxonomy=' . $post_meta_key ]  = $meta_value;
										$custom_fields[ 'taxonomy=' . $post_meta_key ] = $meta_value;
								}
							}
						}
						$replace_data = apply_filters( 'wpomp_post_placeholder', $replace_data, $post, $map );
						$places['source'] = 'post';
						$places['title']  = $replace_data['post_title'];
						foreach ( $replace_data as $placeholder => $holder_value ) {
							$content = str_replace( '{' . $placeholder . '}', $holder_value, $content );
						}
						$places['infowindow_content'] = $content;
						$places['content']            = $replace_data['post_excerpt'];
						if ( ! empty( $value['address'] ) ) {
							$places['address'] = get_post_meta( $post, $value['address'], true );
						} else {
							$places['address'] = '';
						}
						if ( !empty($acf_key['lat']) ) {
							$places['location']['lat'] = $acf_key['lat'];
						} elseif ( ! empty( $value['latitude'] ) ) {
							$places['location']['lat'] = get_post_meta( $post, $value['latitude'], true );
						} else {
							$places['location']['lat'] = '';

						}
						$post_city    = get_post_meta( $post, '_wpomp_location_city', true );
						$post_state   = get_post_meta( $post, '_wpomp_location_state', true );
						$post_country = get_post_meta( $post, '_wpomp_location_country', true );
						if ( ! empty( $post_city ) ) {
							$places['location']['city'] = $post_city;
						}
						if ( ! empty( $post_state ) ) {
							$places['location']['state'] = $post_state;
						}
						if ( ! empty( $post_country ) ) {
							$places['location']['country'] = $post_country;
						}
						if ( ! empty( $acf_key['lng'] ) ) {
							$places['location']['lng'] = $acf_key['lng'];
						} elseif ( ! empty( $value['longitude'] ) ) {
							$places['location']['lng'] = get_post_meta( $post, $value['longitude'], true );
						} else {
							$places['location']['lng'] = '';
						}
						$category_name ='';
						if ( ! empty( $value['category'] ) ) {
							$category_name = get_post_meta( $post, $value['category'], true ); }
						$assigned_category = maybe_unserialize( $category_name );
						if ( ! is_array( $assigned_category ) && '' != $category_name ) {
							$assigned_category[] = $category_name;
						}
						$places['id']                               = $post;
						$onclick                                    = get_post_meta( $post, '_wpomp_metabox_location_redirect', true );
						$onclick                                    = ( $onclick ) ? $onclick : 'marker';
						$wpomp_metabox_custom_link                  = get_post_meta( $post, '_wpomp_metabox_custom_link', true );
						$places['location']['redirect_custom_link'] = $wpomp_metabox_custom_link;
						$places['location']['onclick_action']       = $onclick;
						$places['location']['redirect_permalink']   = $replace_data['post_link'];
						$places['location']['zoom']                 = intval( $map->map_zoom_level );
						$custom_fields['post_excerpt']              = $replace_data['post_excerpt'];
						$custom_fields['post_content']              = $replace_data['post_content'];
						$custom_fields['post_title']                = $replace_data['post_title'];
						$custom_fields['post_link']                 = $replace_data['post_link'];
						$custom_fields['post_featured_image']       = $replace_data['post_featured_image'];
						$custom_fields['post_categories']           = $replace_data['post_categories'];
						$custom_fields['post_tags']                 = $replace_data['post_tags'];
						$places['location']['extra_fields']         = $custom_fields;
						$post_custom_fields                         = get_post_custom( $post );
						$post_custom_fields = apply_filters('wpomp_skip_cf_list',$post_custom_fields, $post, $map->map_id);
						if ( $post_custom_fields ) {
							foreach ( $post_custom_fields as $k => $cvalue ) {
								$k = trim( $k );
								$custom_fields[ '%' . $k . '%' ] = maybe_unserialize( $cvalue[0] );
								if ( is_array( $custom_fields[ '%' . $k . '%' ] ) ) {
										$is_nested_level = false;
										foreach($custom_fields[ '%' . $k . '%' ] as $key1 => $value1) {
											if(is_array($value1)){
												$is_nested_level = true;
												break;
											}
										}
										if(!$is_nested_level){
											$custom_fields[ '%' . $k . '%' ] = implode( $delimiter, $custom_fields[ '%' . $k . '%' ] );
										}
								}
								if ( in_array( '{%' . $k . '%}', $map_custom_filters ) ) {
									$filter_value = maybe_unserialize( $cvalue[0] );
									if ( is_array( $filter_value ) ) {
										$custom_filters[ $k ] = $filter_value;
									} else {
										$custom_filters[ $k ] = $cvalue[0];
									}
								}
							}
						}
						$post_taxonomies = get_post_taxonomies( $post );
						if ( $post_taxonomies ) {
							foreach ( $post_taxonomies as $k => $tax ) {
								$term_list  = wp_get_post_terms( $post, $tax, array( 'fields' => 'all' ) );
								$meta_value = '';
								$tags_links = array();
								if ( $term_list ) {
									foreach ( $term_list as $tag ) {
										$tags_links[] = $tag->name;
									}
									if ( ! empty( $tags_links ) ) {
										$meta_value = implode( ', ', $tags_links );
									}
								}
								$custom_fields[ 'taxonomy=' . $tax ] = $meta_value;
								if ( in_array( '{%' . $tax . '%}', $map_custom_filters ) ) {
									$custom_filters[ '%' . $tax . '%' ] = $tags_links;

								}
							}
						}
						$places['location']['extra_fields'] = $custom_fields;
						$places['custom_filters']           = $custom_filters;
						$places['infowindow_disable']       = false;
						if ( is_array( $assigned_category ) ) {
							$category_count = 0;
							$cats_with_order_id_post = array();
							foreach ( $assigned_category as $category_name ) {
								if ( ! empty( $category_name ) ) {
									$loc_category = isset($all_categories_name[ sanitize_title( $category_name ) ]) ?  $all_categories_name[ sanitize_title( $category_name ) ] : "";

									if ( empty( $loc_category ) ) {
										$loc_category = isset($all_categories[ sanitize_title( $category_name ) ]) ? $all_categories[ sanitize_title( $category_name ) ] : "";
									}
									if( is_object($loc_category) and !empty($loc_category) ) {
									$places['categories'][ $category_count ]['icon']             = $loc_category->group_marker;
									$places['categories'][ $category_count ]['name']             = $loc_category->group_map_title;
									$places['categories'][ $category_count ]['id']               = $loc_category->group_map_id;
									$places['categories'][ $category_count ]['type']             = 'category';
									$places['categories'][ $category_count ]['extension_fields'] = $loc_category->extensions_fields;
									}
									if(!empty($loc_category->extensions_fields['cat_order'])){
										$cats_with_order_id_post[$loc_category->group_map_title] = $loc_category->extensions_fields['cat_order'];	
									}
									if (  isset($loc_category->group_marker) && $loc_category->group_marker != '' ) {
										$places['location']['icon'] = $loc_category->group_marker;
									} else {
										$places['location']['icon'] = $map_data['map_options']['marker_default_icon'];
									}
									if(!empty($cats_with_order_id_post) && count($cats_with_order_id_post)>0){
										$top_priority_key_post = min(array_keys($cats_with_order_id_post, min($cats_with_order_id_post)));
										$places['location']['icon'] = isset( $all_categories_name[ sanitize_title( $top_priority_key_post)]->group_marker ) ? $all_categories_name[ sanitize_title( $top_priority_key_post)]->group_marker : $map_data['map_options']['marker_default_icon'];

									}
								}
								$category_count++;
							}
						}
						$map_data['places'][] = $places;
					}
				}
				wp_reset_postdata();
			}
		}
	}
}
// Add  new places from external data source.
$custom_markers     = array();
$map_id             = $map->map_id;
$all_custom_markers = apply_filters( 'wpomp_marker_source', $custom_markers, $map_id );
if ( is_array( $all_custom_markers ) ) {
	foreach ( $all_custom_markers as $marker ) {
		$places                               = array();
		if ( isset( $all_categories_name[ sanitize_title( $marker['category'] ) ] ) ) {
				$new_catagory = $all_categories_name[ sanitize_title( $marker['category'] ) ];
		} else {
				$new_catagory = '';
		}

		$places['id']                         = isset( $marker['id'] ) ? $marker['id'] : rand( 4000, 9999 );
		$places['title']                      = $marker['title'];
		$places['source']                     = 'external';
		$places['address']                    = $marker['address'];
		$places['content']                    = $marker['message'];
		$places['location']['onclick_action'] = 'marker';
		$places['location']['lat']            = $marker['latitude'];
		$places['location']['lng']            = $marker['longitude'];
		$places['infowindow_disable']         = false;
		$places['location']['zoom']           = intval( $map->map_zoom_level );
		if ( $new_catagory != '' ) {
			$places['categories'][0]['icon']             = $new_catagory->group_marker;
			$places['categories'][0]['name']             = $new_catagory->group_map_title;
			$places['categories'][0]['id']               = $new_catagory->group_map_id;
			$places['categories'][0]['type']             = 'category';
			$places['categories'][0]['extension_fields'] = $new_catagory->extensions_fields;
			$places['location']['icon']                  = ( $marker['icon'] ) ? $marker['icon'] : $new_catagory->group_marker;
		}
		$places['location']['marker_image'] = isset( $marker['marker_image'] ) ? $marker['marker_image'] : '';
		$places['location']['extra_fields'] = isset( $marker['extra_fields'] ) ? $marker['extra_fields'] : '';
		$map_data['places'][] = $places;
	}
}
// Here loop through all places and apply filter. Shortcode Awesome.
$filterd_places   = array();
$render_shortcode = apply_filters( 'wpomp_render_shortcode', true, $map );
if ( is_array( $map_data['places'] ) ) {
	foreach ( $map_data['places'] as $place ) {
		$use_me = true;
		// Category filter here.
		if ( $map->map_all_control['url_filter'] == 'true' ) {
			if ( isset( $_GET['category'] ) and $_GET['category'] != '' ) {
				$shortcode_filters['category'] = sanitize_text_field( $_GET['category'] );
			}
		}
		if ( isset( $shortcode_filters['category'] ) ) {
			$found_category       = false;
			$show_categories_only = explode( ',', strtolower( $shortcode_filters['category'] ) );
			if( isset($place['categories']) ) {
				foreach ( $place['categories'] as $cat ) {
					if ( in_array( strtolower( $cat['name'] ), $show_categories_only ) or in_array( strtolower( $cat['id'] ), $show_categories_only ) ) {
						$found_category = true;
					}
				}
			}

			if ( false == $found_category ) {
				$use_me = false;
			}
		}
		if ( true == $render_shortcode ) {
			$place['content'] = do_shortcode( $place['content'] );
		}
		$use_me = apply_filters( 'wpomp_show_place', $use_me, $place, $map );
		if ( true == $use_me ) {
			$filterd_places[] = $place;
		}
	}
	unset( $map_data['places'] );
}
if ( isset( $location_criteria['limit'] ) and $location_criteria['limit'] > 0 ) {
	$how_many       = intval( $location_criteria['limit'] );
	$filterd_places = array_slice( $filterd_places, 0, $how_many );
}
$map_data['places'] = apply_filters( 'wpomp_markers', $filterd_places, $map->map_id );
if ( '' == $map_data['map_options']['center_lat'] && isset($map_data['places'][0]) ) {
	$map_data['map_options']['center_lat'] = $map_data['places'][0]['location']['lat'];
}
if ( '' == $map_data['map_options']['center_lng'] && isset($map_data['places'][0]) ) {
	$map_data['map_options']['center_lng'] = $map_data['places'][0]['location']['lng'];
}

$map_data['map_tabs'] = array(
		'hide_tabs_default'    => ( (isset($map->map_all_control['hide_tabs_default']) && $map->map_all_control['hide_tabs_default']=='true') ),
		'category_tab'         => array(
			'cat_tab'        => ( isset($map->map_all_control['wpomp_category_tab']) && 'true' == $map->map_all_control['wpomp_category_tab'] ),
			'cat_tab_title'  => ( !empty( $map->map_all_control['wpomp_category_tab_title'] )) ? $map->map_all_control['wpomp_category_tab_title'] : esc_html__( 'Categories', 'wp-leaflet-maps-pro' ),
			'cat_order_by'   => isset($map->map_all_control['wpomp_category_order']) ? $map->map_all_control['wpomp_category_order'] : '',
			'show_count'     => (isset($map->map_all_control['wpomp_category_tab_show_count']) && 'true' == $map->map_all_control['wpomp_category_tab_show_count'] ),
			'hide_location'  => ( isset($map->map_all_control['wpomp_category_tab_hide_location']) && $map->map_all_control['wpomp_category_tab_hide_location'] == 'true' ),
			'select_all'     => ( isset($map->map_all_control['wpomp_category_tab_show_all']) && $map->map_all_control['wpomp_category_tab_show_all'] == 'true' ),
			'child_cats'     => (array) $all_child_categories,
			'parent_cats'    => (array) $all_parent_categories,
			'all_cats'       => (array) $all_categories,
		)

	);
if ( isset( $map_data['map_tabs'] ) ) {
	$map_data['map_tabs']['category_tab'] = apply_filters( 'wpomp_category_tab', $map_data['map_tabs']['category_tab'], $map );
}
if ( isset( $options['maps_only'] ) and $options['maps_only'] == 'true' ) {
	$map->map_all_control['display_marker_category'] = false;
	$map->map_all_control['display_listing']         = false;
} elseif ( isset( $_GET['maps_only'] ) and $_GET['maps_only'] == 'true' and $map->map_all_control['url_filter'] == 'true' ) {
	$map->map_all_control['display_marker_category'] = false;
	$map->map_all_control['display_listing']         = false;
}
if ( ! empty( $map->map_all_control['display_listing'] ) && $map->map_all_control['display_listing'] == true ) {
	$filcate       = array( 'place_category' );
	$sorting_array = array(
		'category__asc'  => esc_html__( 'A-Z Category', 'wp-leaflet-maps-pro' ),
		'category__desc' => esc_html__( 'Z-A Category', 'wp-leaflet-maps-pro' ),
		'title__asc'     => esc_html__( 'A-Z Title', 'wp-leaflet-maps-pro' ),
		'title__desc'    => esc_html__( 'Z-A Title', 'wp-leaflet-maps-pro' ),
		'address__asc'   => esc_html__( 'A-Z Address', 'wp-leaflet-maps-pro' ),
		'address__desc'  => esc_html__( 'Z-A Address', 'wp-leaflet-maps-pro' ),
	);
	$sorting_array = apply_filters( 'wpomp_sorting', $sorting_array, $map );
	if ( empty( $map->map_all_control['wpomp_listing_number'] ) ) {
		$map->map_all_control['wpomp_listing_number'] = 10; }
	if ( ! isset( $map->map_all_control['wpomp_categorydisplaysortby'] ) or $map->map_all_control['wpomp_categorydisplaysortby'] == '' ) {
		$map->map_all_control['wpomp_categorydisplaysortby'] = 'asc';
	}
	$render_shortcode = apply_filters( 'wpomp_listing_render_shortcode', true, $map );
	$listing_placeholder_pre_content =  !empty($listing_placeholder_content) ? $listing_placeholder_content : '';
	if ( $render_shortcode == true ) {
		  $listing_placeholder_text = do_shortcode( stripslashes( trim( $listing_placeholder_pre_content ) ) );
	} else {
		  $listing_placeholder_text = stripslashes( trim($listing_placeholder_pre_content) );
	}

	if ( isset( $options['hide_map'] ) and $options['hide_map'] == 'true' ) {
		$map->map_all_control['hide_map'] = 'true';
	} elseif ( isset( $_GET['hide_map'] ) and $_GET['hide_map'] == 'true' and $map->map_all_control['url_filter'] == 'true' ) {
		$map->map_all_control['hide_map'] = 'true';
	}
	if ( isset( $options['perpage'] ) and $options['perpage'] > 0 ) {
		$map->map_all_control['wpomp_listing_number'] = sanitize_text_field( $options['perpage'] );
	} elseif ( isset( $_GET['perpage'] ) and $map->map_all_control['url_filter'] == 'true' ) {
		$map->map_all_control['wpomp_listing_number'] = sanitize_text_field( $_GET['perpage'] );
	}
	if ( ! isset( $map->map_all_control['wpomp_display_sorting_filter'] ) ) {
		$map->map_all_control['wpomp_display_sorting_filter'] = false;
	}
	if ( ! isset( $map->map_all_control['wpomp_display_radius_filter'] ) ) {
		$map->map_all_control['wpomp_display_radius_filter'] = false;
	}
	if ( ! isset( $map->map_all_control['hide_locations'] ) ) {
		$map->map_all_control['hide_locations'] = false;
	}
	if ( ! isset( $map->map_all_control['hide_map'] ) ) {
		$map->map_all_control['hide_map'] = false;
	}
	if ( ! isset( $map->map_all_control['wpomp_apply_radius_only'] ) ) {
		$map->map_all_control['wpomp_apply_radius_only'] = false;
	}
	if ( ! isset( $map->map_all_control['wpomp_display_grid_option'] ) ) {
		$map->map_all_control['wpomp_display_grid_option'] = false;
	}
	if ( ! isset( $map->map_all_control['wpomp_search_display'] ) ) {
		$map->map_all_control['wpomp_search_display'] = false;
	}
	if ( ! isset( $map->map_all_control['search_field_autosuggest'] ) ) {
		$map->map_all_control['search_field_autosuggest'] = false;
	}
	if ( ! isset( $map->map_all_control['wpomp_display_sorting_filter'] ) ) {
		$map->map_all_control['wpomp_display_sorting_filter'] = false;
	}
	if ( ! isset( $map->map_all_control['wpomp_display_radius_filter'] ) ) {
		$map->map_all_control['wpomp_display_radius_filter'] = false;
	}
	if ( ! isset( $map->map_all_control['wpomp_apply_radius_only'] ) ) {
		$map->map_all_control['wpomp_apply_radius_only'] = false;
	}
	if ( ! isset( $map->map_all_control['wpomp_display_category_filter'] ) ) {
		$map->map_all_control['wpomp_display_category_filter'] = false;
	}
	if ( ! isset( $map->map_all_control['wpomp_display_location_per_page_filter'] ) ) {
		$map->map_all_control['wpomp_display_location_per_page_filter'] = false;
	}
	if ( ! isset( $map->map_all_control['wpomp_display_print_option'] ) ) {
		$map->map_all_control['wpomp_display_print_option'] = false;
	}
	$map_data['listing'] = array(
		'listing_header'                   => isset($map->map_all_control['wpomp_before_listing']) ? $map->map_all_control['wpomp_before_listing'] : '',
		'display_search_form'              => ( 'true' == $map->map_all_control['wpomp_search_display'] ),
		'search_field_autosuggest'         => ( $map->map_all_control['search_field_autosuggest'] == 'true' ),
		'display_category_filter'          => ( $map->map_all_control['wpomp_display_category_filter'] == 'true' ),
		'display_sorting_filter'           => ( 'true' == $map->map_all_control['wpomp_display_sorting_filter'] ),
		'display_radius_filter'            => ( 'true' == $map->map_all_control['wpomp_display_radius_filter'] ),
		'radius_dimension'                 => isset($map->map_all_control['wpomp_radius_dimension']) ? $map->map_all_control['wpomp_radius_dimension'] :'',
		'radius_options'                   => isset($map->map_all_control['wpomp_radius_options']) ? $map->map_all_control['wpomp_radius_options'] :'',
		'apply_default_radius'             => ( 'true' == $map->map_all_control['wpomp_apply_radius_only'] ),
		'default_radius'                   => isset($map->map_all_control['wpomp_default_radius']) ? $map->map_all_control['wpomp_default_radius'] :'',
		'default_radius_dimension'         => isset($map->map_all_control['wpomp_default_radius_dimension']) ? $map->map_all_control['wpomp_default_radius_dimension'] :'' ,
		'display_location_per_page_filter' => ( 'true' == $map->map_all_control['wpomp_display_location_per_page_filter'] ),
		'display_print_option'             => ( $map->map_all_control['wpomp_display_print_option'] == 'true' ),
		'display_grid_option'              => ( $map->map_all_control['wpomp_display_grid_option'] == 'true' ),
		'filters'                          => array( 'place_category' ),
		'sorting_options'                  => $sorting_array,
		'default_sorting'                  => array(
			'orderby' => isset($map->map_all_control['wpomp_categorydisplaysort']) ? $map->map_all_control['wpomp_categorydisplaysort'] :'' ,
			'inorder' => isset($map->map_all_control['wpomp_categorydisplaysortby']) ? $map->map_all_control['wpomp_categorydisplaysortby'] :'' ,
		),
		'listing_container'                => '.location_listing' . $map->map_id,
		'tabs_container'                   => '.location_listing' . $map->map_id,
		'hide_locations'                   => ( $map->map_all_control['hide_locations'] == 'true' ),
		'filters_position'                 => ( $map->map_all_control['filters_position'] ) ? $map->map_all_control['filters_position'] : '',
		'hide_map'                         => ( $map->map_all_control['hide_map'] == 'true' ),
		'pagination'                       => array( 'listing_per_page' => $map->map_all_control['wpomp_listing_number'] ),
		'list_grid'                        => isset($map->map_all_control['wpomp_list_grid'] ) ? $map->map_all_control['wpomp_list_grid'] : 'wpomp_listing_list',
		'listing_placeholder'              => $listing_placeholder_text,
		'list_item_skin'                   => ( isset( $map->map_all_control['item_skin'] ) ) ? $map->map_all_control['item_skin'] : array(
			'name'       => 'default',
			'type'       => 'item',
			'sourcecode' => $listing_placeholder_text,
		),
	);
} else {
	$map_data['listing'] = '';
}
$map_data['listing']      = apply_filters( 'wpomp_listing', $map_data['listing'], $map );
$map_data['map_property'] = array(
	'map_id'     => $map->map_id,
	'debug_mode' => ( isset($wpomp_settings['wpomp_debug_mode']) && $wpomp_settings['wpomp_debug_mode'] == 'true' ),
);
if ( isset($map->map_all_control['geojson_url']) && '' != sanitize_text_field( $map->map_all_control['geojson_url'] ) ) {
	$map_data['geojson'] = sanitize_text_field( $map->map_all_control['geojson_url'] );
}
$all_filters = array();
if ( isset( $map->map_all_control['wpomp_display_custom_filters'] ) && $map->map_all_control['wpomp_display_custom_filters'] == 'true' ) {
	if ( isset( $map->map_all_control['custom_filters'] ) and ! empty( $map->map_all_control['custom_filters'] ) ) {
		foreach ( $map->map_all_control['custom_filters'] as $key => $val ) {
			$val['slug'] = preg_replace( '/[{}]/', '', $val['slug'] );
			if ( $val['slug'] == 'category' ) {
				$val['slug'] = 'post_categories';}
			$listing_custom_filters['dropdown'][ $val['slug'] ] = $val['text'];
		}
		$all_filters['filters'] = $listing_custom_filters;
	}
}
$all_filters             = apply_filters( 'wpomp_filters', $all_filters, $map );
$custom_filter_container = apply_filters( 'wpomp_filter_container', '[data-container="wpomp-filters-container"]', $map );
$map_data['filters'] = array(
	'custom_filters'    => $all_filters,
	'filters_container' => $custom_filter_container,
);
$map_output = apply_filters( 'wpomp_before_container', '', $map );
$map_output .= '<div class="wpomp_map_container ' . apply_filters( 'wpomp_container_class', 'wpomp-map-' . $map->map_id, $map ) . '" rel="map' . $map->map_id . '">';
$map_div = apply_filters( 'wpomp_before_map', '', $map );
$hide_map ='';
if ( isset( $map->map_all_control['hide_map'] ) && $map->map_all_control['hide_map'] == 'true' ) {
	$width  = '0px';
	$height = '0px';
	$hide_map = 'display:none;';
}


$filters_div = '<div class="wpomp_filter_wrappers"></div>';

if( !class_exists('Listing_Designs_For_Leaflet_Maps') || wp_is_mobile() ){
	
	if( isset( $map->map_all_control['display_listing'] ) && $map->map_all_control['display_listing'] == 'true'){
		
		if($map->map_all_control['filters_position'] == 'top_map'){			
			
			$map_div .= $filters_div.'<div class="wpomp_map_parent"><div class="wpomp_map ' . apply_filters( 'wpomp_map_class', '', $map ) . '" style="width:' . $width . '; height:' . $height . ';" id="map' . $map->map_id . '" ></div></div>';
		}else{

			$map_div .= '<div class="wpomp_map_parent"><div class="wpomp_map ' . apply_filters( 'wpomp_map_class', '', $map ) . '" style="width:' . $width . '; height:' . $height . ';" id="map' . $map->map_id . '" ></div></div>'.$filters_div;
			
		}
			
	}else{ 
		
		$map_div .= '<div class="wpomp_map_parent"><div class="wpomp_map ' . apply_filters( 'wpomp_map_class', '', $map ) . '" style="width:' . $width . '; height:' . $height . ';'.$hide_map.'" id="map' . $map->map_id . '" ></div></div>';

	}

}else{

	$map_div .= '<div class="wpomp_map_parent"><div class="wpomp_map ' . apply_filters( 'wpomp_map_class', '', $map ) . '" style="width:' . $width . '; height:' . $height . ';'.$hide_map.'" id="map' . $map->map_id . '" ></div></div>';
}

$map_div .= apply_filters( 'wpomp_after_map', '', $map );
$listing_div = apply_filters( 'wpomp_before_listing', '', $map );
if ( ! empty( $map->map_all_control['display_listing'] ) && $map->map_all_control['display_listing'] == true ) {
	$listing_div .= '<div class="location_listing' . $map->map_id . ' ' . apply_filters( 'wpomp_listing_class', '', $map ) . '" style="float:left; width:100%;"></div>';
	if ( $map->map_all_control['hide_locations'] != true ) {
		$listing_div .= '<div class="location_pagination' . $map->map_id . ' ' . apply_filters( 'wpomp_pagination_class', '', $map ) . ' wpomp_pagination" style="float:left; width:100%;"></div>';
	}
}
$listing_div .= apply_filters( 'wpomp_after_listing', '', $map );
$output = $map_div . $listing_div;


if(class_exists('Listing_Designs_For_Leaflet_Maps')){ 
 	
	$map_output .= apply_filters( 'wpomp_map_output', $output, $map_div, $filters_div, $listing_div, $map->map_id );
}
else { 
 	
 	$map_output .= apply_filters( 'wpomp_map_output', $output, $map_div, $listing_div, $map->map_id );
}


$map_output .= '</div>';
$map_output .= apply_filters( 'wpomp_after_container', '', $map );
if ( isset( $map->map_all_control['fc_custom_styles'] ) ) {
	$fc_custom_styles = json_decode( $map->map_all_control['fc_custom_styles'], true );
	if ( ! empty( $fc_custom_styles ) && is_array( $fc_custom_styles ) ) {
		$fc_skin_styles = '';
		$font_families  = array();
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
							if ( strpos( $skin, 'infowindow' ) !== false ) {
								$class = ' .wpomp_infowindow ' . $class;
							} elseif ( strpos( $skin, 'post' ) !== false ) {
								$class = ' .wpomp_infowindow.wpomp_infowindow_post ' . $class;
							} elseif ( strpos( $class, 'fc-item-title' ) !== false ) {
								$fc_skin_styles .= ' ' . $class . ' a, ' . $class . ' a:hover, ' . $class . ' a:focus, ' . $class . ' a:visited{' . $style . '}';
							}
							$fc_skin_styles .= ' ' . '.wpomp-map-' . $map->map_id . ' ' . $class . '{' . $style . '}';
						}
					}
				}
			}
		}
		if ( ! empty( $fc_skin_styles ) ) {
			$map_output .= '<style>' . $fc_skin_styles . '</style>';
		}
		if ( ! empty( $font_families ) ) {
			$font_families = array_unique($font_families);
			$map_data['map_options']['google_fonts'] = $font_families;
		}
	}
}
$map_data['map_options']['images_url'] = WPLMP_IMAGES;
$map_data['marker_category_icons'] = $marker_category_icons;
$map_data = apply_filters( 'wpomp_map_data', $map_data, $map );
$map_data = $map_obj->clear_empty_array_values( $map_data );
$map_data = apply_filters( 'wpomp_final_map_data', $map_data, $map );

$auto_fix = isset($wpomp_settings['wpomp_auto_fix']) ? $wpomp_settings['wpomp_auto_fix'] : false;
if ( $auto_fix == 'true' ) { 
    $map_data_obj = json_encode( $map_data , JSON_UNESCAPED_SLASHES );
}else{
	$map_data_obj = json_encode( $map_data );
}
$map_data_obj = base64_encode($map_data_obj);

$map_output    .= '<script>jQuery(document).ready(function($) {var map' . $map_id . ' = $("#map' . $map_id . '").osm_maps("' . $map_data_obj . '").data("wpomp_maps");});</script>';

$base_class     = '.wpomp-map-' . $map->map_id . ' ';
if ( isset( $map->map_all_control['apply_custom_design'] ) && $map->map_all_control['apply_custom_design'] == 'true' ) {
$base_font_size = isset($map->map_all_control['wpomp_base_font_size']) ? trim( str_replace( 'px', '', $map->map_all_control['wpomp_base_font_size'] ) ) : '';
$css_rules      = array();
if ( $base_font_size != '' ) {
	$base_font_size = $base_font_size . 'px';
	$css_rules[]    = $base_class . ',' . $base_class . ' .wpomp_tabs_container,' . $base_class . ' .wpomp_listing_container { font-size : ' . $base_font_size . ' !important;}';
}
if (isset($map->map_all_control['wpomp_custom_css']) && trim( $map->map_all_control['wpomp_custom_css'] ) != '' ) {
	$css_rules[] = $map->map_all_control['wpomp_custom_css'];
}
if ( ! isset( $map->map_all_control['apply_own_schema'] ) ) {
	$map->map_all_control['apply_own_schema'] = false;
}
if ( isset( $map->map_all_control['color_schema'] ) && trim( $map->map_all_control['color_schema'] ) != '' and $map->map_all_control['apply_own_schema'] != true ) {
	$color_schema                                  = $map->map_all_control['color_schema'];
	$color_schema_colors                           = explode( '_', $color_schema );
	$map->map_all_control['wpomp_primary_color']   = $color_schema_colors[0];
}
if ( trim( $map->map_all_control['wpomp_primary_color'] ) != '' && $map->map_all_control['wpomp_primary_color'] != '#' ) {
$secondary_color = $map->map_all_control['wpomp_primary_color'];
$css_rules[] = $base_class . '.wpomp_tabs_container .wpomp_tabs li a.active, ' . $base_class . '.fc-primary-bg, ' . $base_class . '.wpomp_infowindow .fc-badge.info, ' . $base_class . '.wpomp_toggle_main_container .amenity_type:hover, ' . $base_class . '
.wpomp_direction_container p input.wpomp_find_direction,
' . $base_class . '.wpomp_nearby_container .wpomp_find_nearby_button, ' . $base_class . '.fc-label-info, ' . $base_class . '.fc-badge.info, ' . $base_class . '.wpomp_pagination span,
' . $base_class . '.wpomp_pagination a, ' . $base_class . 'div.categories_filter select,  ' . $base_class . '.wpomp_toggle_container, ' . $base_class . ' .categories_filter_reset_btn,' . $base_class . '.categories_filter input[type="button"], ' . $base_class . '.categories_filter_reset_btn:hover {
        background-color: ' . $secondary_color . ';
}
' . $base_class . '.wpomp-select-all,' . $base_class . '.fc-primary-fg{
        color: ' . $secondary_color . ';
} 
' . $base_class . '.fc-label-info, ' . $base_class . '.fc-badge.info {
    border: 1px solid ' . $secondary_color . ';
}
' . $base_class . 'div.wpomp_search_form input.wpomp_search_input {
	border-bottom: 1px solid ' . $secondary_color . ';
} ' . $base_class . '.wpomp_iw_content .fc-item-title span{color:#fff;}' . $base_class . '.wpomp_location_category.fc-badge.info{color:#fff;}';
	}
}
if ( isset( $map->map_all_control['apply_own_schema'] ) && $map->map_all_control['apply_own_schema'] == 'true' ) {
	/* End Primary Color */
	if ( trim( $map->map_all_control['wpomp_primary_color'] ) != '' && $map->map_all_control['wpomp_primary_color'] != '#' ) {
		$secondary_color = $map->map_all_control['wpomp_primary_color'];
		$css_rules[] = $base_class . '.wpomp_tabs_container .wpomp_tabs li a.active, ' . $base_class . '.fc-primary-bg, ' . $base_class . '.wpomp_infowindow .fc-badge.info, ' . $base_class . '.wpomp_toggle_main_container .amenity_type:hover, ' . $base_class . '.wpomp_direction_container p input.wpomp_find_direction,' . $base_class . '.wpomp_nearby_container .wpomp_find_nearby_button, ' . $base_class . '.fc-label-info, ' . $base_class . '.fc-badge.info, ' . $base_class . '.wpomp_pagination span,' . $base_class . '.wpomp_pagination a, ' . $base_class . 'div.categories_filter select,  ' . $base_class . '.wpomp_toggle_container, ' . $base_class . '.categories_filter_reset_btn,' . $base_class . '.categories_filter input[type="button"], ' . $base_class . '.categories_filter_reset_btn:hover {background-color: ' . $secondary_color . ';}' . $base_class . '.wpomp-select-all,' . $base_class . '.fc-primary-fg {color: ' . $secondary_color . ';} ' . $base_class . '.fc-label-info, ' . $base_class . '.fc-badge.info { border: 1px solid ' . $secondary_color . ';}' . $base_class . 'div.wpomp_search_form input.wpomp_search_input {border-bottom: 1px solid ' . $secondary_color . ';}';
	}
}
if ( ! empty( $css_rules ) ) {
	$map_output .= '<style>' . implode( ' ', $css_rules ) . '</style>';
}
return $map_output;