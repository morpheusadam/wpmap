<?php
/**
 * Map's general setting(s).
 *
 * @package wp-leaflet-maps-pro
 */

$map_type_providers = array(
			'openstreet'    => esc_html__( 'OpenStreet', 'wp-leaflet-maps-pro' ),
		);

if(!empty($wpomp_settings['wpomp_api_key'])){
	$map_type_providers['mapbox'] = esc_html__( 'MapBox', 'wp-leaflet-maps-pro' ); 
}


if(!empty($wpomp_settings['wpomp_mapquest_key'])){
	$map_type_providers['mapquest'] = esc_html__( 'MapQuest', 'wp-leaflet-maps-pro' ); 
}

if(!empty($wpomp_settings['wpomp_bingmap_key'])){
	$map_type_providers['bingmap'] = esc_html__( 'BingMap', 'wp-leaflet-maps-pro' ); 
}



$form->add_element(
	'select', 'map_all_control[wpomp_map_provider]', array(
		'label'   => esc_html__( 'Mapdata Provider', 'wp-leaflet-maps-pro' ),
		'current' => isset( $data['map_all_control']['wpomp_map_provider'] ) ? $data['map_all_control']['wpomp_map_provider'] : 'openstreet',
		'desc'    => esc_html__( 'Select Mapdata provider.', 'wp-leaflet-maps-pro' ),
		'options' => $map_type_providers ,
		'class'   => 'form-control wpomp_mapdata_providers',
		'before'  => '<div class="fc-4">',
		'data'  => array( 'target' => '.openstreet_tile_url' ),
		'after'   => '</div>',
	)
);
$default_tile_url = apply_filters('wpomp_default_osm_tile_url','https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');

$form->add_element(
	'text', 'map_all_control[openstreet_url]', array(
		'label'       => esc_html__( 'Map Tile URL', 'wp-leaflet-maps-pro' ),
		'value'       => isset( $data['map_all_control']['openstreet_url'] ) ? $data['map_all_control']['openstreet_url'] : $default_tile_url,
		'desc'        => esc_html__( 'Enter here the tile url for openstreet. Leave blank to use default tile.'.$default_tile_url, 'wp-leaflet-maps-pro' ),
		'placeholder' => '',
		'class'   => 'form-control openstreet_tile_url',
		//'show'=>false
	)
);


$form->add_element(
	'text', 'map_title', array(
		'label'       => esc_html__( 'Map Title', 'wp-leaflet-maps-pro' ),
		'value'       => isset( $data['map_title'] ) ? $data['map_title'] : '',
		'desc'        => esc_html__( 'Enter here the map title.', 'wp-leaflet-maps-pro' ),
		'required'    => true,
		'placeholder' => '',
	)
);

$form->add_element(
	'text', 'map_width', array(
		'label'       => esc_html__( 'Map Width', 'wp-leaflet-maps-pro' ),
		'value'       => isset( $data['map_width'] ) ? $data['map_width'] : '',
		'desc'        => esc_html__( 'Enter here the map width in pixel. Leave it blank for 100% width.', 'wp-leaflet-maps-pro' ),
		'placeholder' => '',
	)
);
$form->add_element(
	'text', 'map_height', array(
		'label'       => esc_html__( 'Map Height', 'wp-leaflet-maps-pro' ),
		'value'       => isset( $data['map_height'] ) ? $data['map_height'] : '',
		'desc'        => esc_html__( 'Enter here the map height in pixel.', 'wp-leaflet-maps-pro' ),
		'required'    => true,
		'placeholder' => '',
	)
);

$zoom_level = array();
for ( $i = 0; $i < 20; $i++ ) {
	$zoom_level[ $i ] = $i;
}

$form->add_element(
	'select', 'map_all_control[map_minzoom_level]', array(
		'label'         => esc_html__( 'Minimum Zoom Level', 'wp-leaflet-maps-pro' ),
		'current'       => isset( $data['map_all_control']['map_minzoom_level'] ) ? $data['map_all_control']['map_minzoom_level'] : '',
		'desc'          => esc_html__( 'The minimum zoom level which will be displayed on the map.', 'wp-leaflet-maps-pro' ),
		'options'       => $zoom_level,
		'default_value' => 0,
	)
);

$form->add_element(
	'select', 'map_all_control[map_maxzoom_level]', array(
		'label'         => esc_html__( 'Maximum Zoom Level', 'wp-leaflet-maps-pro' ),
		'current'       => isset( $data['map_all_control']['map_maxzoom_level'] ) ? $data['map_all_control']['map_maxzoom_level'] : '',
		'desc'          => esc_html__( 'The maximum zoom level which will be displayed on the map.', 'wp-leaflet-maps-pro' ),
		'options'       => $zoom_level,
		'default_value' => 19,
	)
);

$form->add_element(
	'select', 'map_zoom_level', array(
		'label'         => esc_html__( 'Default Zoom Level', 'wp-leaflet-maps-pro' ),
		'current'       => isset( $data['map_zoom_level'] ) ? $data['map_zoom_level'] : '',
		'desc'          => esc_html__( 'Default zoom level when page is loaded.', 'wp-leaflet-maps-pro' ),
		'options'       => $zoom_level,
		'default_value' => 5,
	)
);


$form->add_element(
	'checkbox', 'map_scrolling_wheel', array(
		'label'   => esc_html__( 'Turn Off Scrolling Wheel', 'wp-leaflet-maps-pro' ),
		'value'   => 'false',
		'id'      => 'wpomp_map_scrolling_wheel',
		'current' => isset( $data['map_scrolling_wheel'] ) ? $data['map_scrolling_wheel'] : '',
		'desc'    => esc_html__( 'Please check to disable scroll wheel zoom.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class ',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[doubleclickzoom]', array(
		'label'   => esc_html__( 'Double Click Zoom', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'doubleclickzoom',
		'current' => isset( $data['map_all_control']['doubleclickzoom'] ) ? $data['map_all_control']['doubleclickzoom'] : '',
		'desc'    => esc_html__( 'Please check to enable zoom on double click on the map.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class ',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[map_draggable]', array(
		'label'   => esc_html__( 'Map Draggable', 'wp-leaflet-maps-pro' ),
		'value'   => 'false',
		'id'      => 'wpomp_map_draggable',
		'current' => isset( $data['map_all_control']['map_draggable'] ) ? $data['map_all_control']['map_draggable'] : '',
		'desc'    => esc_html__( 'Please check to disable map draggable.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);

