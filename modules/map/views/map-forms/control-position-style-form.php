<?php
/**
 * Contro Positioning over leaflet maps.
 *
 * @package wp-leaflet-maps-pro
 * @author Flipper Code <hello@flippercode.com>
 */

$positions = array(
'topleft' => esc_html__('Top Left', 'wp-leaflet-maps-pro'),

'topright' => esc_html__('Top Right', 'wp-leaflet-maps-pro'),

'bottomleft' => esc_html__('Bottom Left', 'wp-leaflet-maps-pro'),

'bottomright' => esc_html__('Bottom Right', 'wp-leaflet-maps-pro'),
);
$form->add_element(
	'group', 'map_control_position_setting', array(
		'value'  => esc_html__( 'Control Position(s) Settings', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'select', 'map_all_control[zoom_control_position]', array(
		'label'   => esc_html__( 'Zoom Control', 'wp-leaflet-maps-pro' ),
		'current' => isset( $data['map_all_control']['zoom_control_position'] ) ? $data['map_all_control']['zoom_control_position'] : '',
		'desc'    => esc_html__( 'Please select position of zoom control.', 'wp-leaflet-maps-pro' ),
		'options' => $positions,
	)
);


$form->add_element(
	'select', 'map_all_control[map_type_control_position]', array(
		'label'         => esc_html__( 'Map Type Control', 'wp-leaflet-maps-pro' ),
		'default_value' => 'TOP_RIGHT',
		'current'       => isset( $data['map_all_control']['map_type_control_position'] ) ? $data['map_all_control']['map_type_control_position'] : 'bottomright',
		'desc'          => esc_html__( 'Please select position of map type control.', 'wp-leaflet-maps-pro' ),
		'options'       => $positions,
	)
);




$form->add_element(
	'select', 'map_all_control[full_screen_control_position]', array(
		'label'         => esc_html__( 'Full Screen Control', 'wp-leaflet-maps-pro' ),
		'default_value' => 'TOP_RIGHT',
		'current'       => isset( $data['map_all_control']['full_screen_control_position'] ) ? $data['map_all_control']['full_screen_control_position'] : '',
		'desc'          => esc_html__( 'Please select position of full screen control.', 'wp-leaflet-maps-pro' ),
		'options'       => $positions,
	)
);



$form->add_element(
	'select', 'map_all_control[search_control_position]', array(
		'label'   => esc_html__( 'Search Control', 'wp-leaflet-maps-pro' ),
		'current' => isset( $data['map_all_control']['search_control_position'] ) ? $data['map_all_control']['search_control_position'] : 'topright',
		'desc'    => esc_html__( 'Please select position of search box control.', 'wp-leaflet-maps-pro' ),
		'options' => $positions,
	)
);

$form->add_element(
	'select', 'map_all_control[locateme_control_position]', array(
		'label'   => esc_html__( 'Locate Me Control', 'wp-leaflet-maps-pro' ),
		'current' => isset( $data['map_all_control']['locateme_control_position'] ) ? $data['map_all_control']['locateme_control_position'] : '',
		'desc'    => esc_html__( 'Please select position of locate me control.', 'wp-leaflet-maps-pro' ),
		'options' => $positions,
	)
);
