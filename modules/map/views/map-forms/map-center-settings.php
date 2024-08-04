<?php
/**
 * Map's Center Location setting(s).
 *
 * @package wp-leaflet-maps-pro
 */

$form->add_element(
	'group', 'map_center_setting', array(
		'value'  => esc_html__( 'Map\'s Center', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'text', 'map_all_control[map_center_latitude]', array(
		'label'       => esc_html__( 'Center Latitude', 'wp-leaflet-maps-pro' ),
		'value'       => isset( $data['map_all_control']['map_center_latitude'] ) ? $data['map_all_control']['map_center_latitude'] : '',
		'desc'        => esc_html__( 'Enter here the center latitude.', 'wp-leaflet-maps-pro' ),
		'placeholder' => '',
	)
);
$form->add_element(
	'text', 'map_all_control[map_center_longitude]', array(
		'label'       => esc_html__( 'Center Longitude', 'wp-leaflet-maps-pro' ),
		'value'       => isset( $data['map_all_control']['map_center_longitude'] ) ? $data['map_all_control']['map_center_longitude'] : '',
		'desc'        => esc_html__( 'Enter here the center longitude.', 'wp-leaflet-maps-pro' ),
		'placeholder' => '',
	)
);


$form->add_element(
	'checkbox', 'map_all_control[nearest_location]', array(
		'label'   => esc_html__( 'Center by Current Location', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'class'   => 'chkbox_class',
		'id'      => 'wpomp_nearest_location',
		'current' => isset( $data['map_all_control']['nearest_location'] ) ? $data['map_all_control']['nearest_location'] : '',
		'desc'    => esc_html__( 'Center the map based on visitor\'s current location. SSL (https://) is required on the website.', 'wp-leaflet-maps-pro' ),
	)
);

$form->add_element(
	'checkbox', 'map_all_control[fit_bounds]', array(
		'label'   => esc_html__( 'Center by Locations Assigned', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'class'   => 'chkbox_class',
		'id'      => 'wpomp_fit_bounds_location',
		'current' => isset( $data['map_all_control']['fit_bounds'] ) ? $data['map_all_control']['fit_bounds'] : '',
		'desc'    => esc_html__( 'Center the map based on locations assigned to the map to show all locations at once.', 'wp-leaflet-maps-pro' ),
	)
);

$form->add_element(
	'checkbox', 'map_all_control[current_post]', array(
		'label'   => esc_html__( 'Center by Current Post', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'class'   => 'chkbox_class',
		'current' => isset( $data['map_all_control']['current_post'] ) ? $data['map_all_control']['current_post'] : '',
		'desc'    => esc_html__( 'To display a map centred on the current post', 'wp-leaflet-maps-pro' ),
	)
);

$form->add_element(
	'checkbox', 'map_all_control[show_center_circle]', array(
		'label'   => esc_html__( 'Display Circle', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'show_center_circle',
		'current' => isset( $data['map_all_control']['show_center_circle'] ) ? $data['map_all_control']['show_center_circle'] : '',
		'desc'    => esc_html__( 'Display a circle around the center location.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.center_circle_settings' ),
	)
);
$form->set_col( 6 );
$color = ( empty( $data['map_all_control']['center_circle_fillcolor'] ) ) ? '8CAEF2' : sanitize_text_field( wp_unslash( $data['map_all_control']['center_circle_fillcolor'] ) );
$form->add_element(
	'text', 'map_all_control[center_circle_fillcolor]', array(
		'value'  => $color,
		'class'  => 'color form-control center_circle_settings',
		'id'     => 'center_circle_fillcolor',
		'desc'   => esc_html__( 'Circle fill color.', 'wp-leaflet-maps-pro' ),
		'show'   => 'false',
		'before' => '<div class="fc-2">',
		'after'  => '</div>',
	)
);
$form->add_element(
	'text', 'map_all_control[center_circle_fillopacity]', array(
		'value'         => isset( $data['map_all_control']['center_circle_fillopacity'] ) ? $data['map_all_control']['center_circle_fillopacity'] : '',
		'class'         => 'form-control center_circle_settings',
		'id'            => 'center_circle_fillopacity',
		'desc'          => esc_html__( 'Circle fill opacity.', 'wp-leaflet-maps-pro' ),
		'show'          => 'false',
		'before'        => '<div class="fc-2">',
		'after'         => '</div>',
		'default_value' => '.5',
	)
);
$color = ( empty( $data['map_all_control']['center_circle_strokecolor'] ) ) ? '8CAEF2' : sanitize_text_field( wp_unslash( $data['map_all_control']['center_circle_strokecolor'] ) );
$form->add_element(
	'text', 'map_all_control[center_circle_strokecolor]', array(
		'value'  => $color,
		'class'  => 'color {pickerClosable:true} form-control center_circle_settings',
		'id'     => 'center_circle_strokecolor',
		'desc'   => esc_html__( 'Circle stroke color.', 'wp-leaflet-maps-pro' ),
		'show'   => 'false',
		'before' => '<div class="fc-2">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'text', 'map_all_control[center_circle_strokeopacity]', array(
		'value'         => isset( $data['map_all_control']['center_circle_strokeopacity'] ) ? $data['map_all_control']['center_circle_strokeopacity'] : '',
		'class'         => 'form-control center_circle_settings',
		'id'            => 'center_circle_strokeopacity',
		'desc'          => esc_html__( 'Circle stroke opacity.', 'wp-leaflet-maps-pro' ),
		'show'          => 'false',
		'before'        => '<div class="fc-2">',
		'after'         => '</div>',
		'default_value' => '.5',
	)
);

$form->add_element(
	'text', 'map_all_control[center_circle_strokeweight]', array(
		'value'         => isset( $data['map_all_control']['center_circle_strokeweight'] ) ? $data['map_all_control']['center_circle_strokeweight'] : '',
		'class'         => 'form-control center_circle_settings',
		'id'            => 'center_circle_strokeweight',
		'desc'          => esc_html__( 'Circle stroke weight.', 'wp-leaflet-maps-pro' ),
		'show'          => 'false',
		'before'        => '<div class="fc-2">',
		'after'         => '</div>',
		'default_value' => '1',
	)
);

$form->add_element(
	'text', 'map_all_control[center_circle_radius]', array(
		'value'         => isset( $data['map_all_control']['center_circle_radius'] ) ? $data['map_all_control']['center_circle_radius'] : '',
		'class'         => 'form-control center_circle_settings',
		'id'            => 'center_circle_radius',
		'desc'          => esc_html__( 'Circle radius around center location.', 'wp-leaflet-maps-pro' ),
		'show'          => 'false',
		'before'        => '<div class="fc-2">',
		'after'         => '</div>',
		'default_value' => '5',
	)
);

$form->set_col( 1 );
$form->add_element(
	'checkbox', 'map_all_control[show_center_marker]', array(
		'label'   => esc_html__( 'Display Marker', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'show_center_marker',
		'current' => isset( $data['map_all_control']['show_center_marker'] ) ? $data['map_all_control']['show_center_marker'] : '',
		'desc'    => esc_html__( 'Display a marker on center location.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.center_marker_settings' ),
	)
);

$form->add_element(
	'textarea', 'map_all_control[show_center_marker_infowindow]', array(
		'label'         => esc_html__( 'Infowindow Message for Center Marker', 'wp-leaflet-maps-pro' ),
		'value'         => isset( $data['map_all_control']['show_center_marker_infowindow'] ) ? $data['map_all_control']['show_center_marker_infowindow'] : '',
		'desc'          => esc_html__( 'Display custom message on center location.', 'wp-leaflet-maps-pro' ),
		'textarea_rows' => 10,
		'textarea_name' => 'show_center_marker_infowindow',
		'class'         => 'form-control center_marker_settings',
		'id'            => 'show_center_marker_infowindow',
		'show'          => 'false',
	)
);

$form->add_element(
	'image_picker', 'map_all_control[marker_center_icon]', array(
		'label'         => esc_html__( 'Choose Center Marker Image', 'wp-leaflet-maps-pro' ),
		'src'           => ( isset( $data['map_all_control']['marker_center_icon'] ) ? wp_unslash( $data['map_all_control']['marker_center_icon'] ) : WPLMP_IMAGES . '/default_marker.png' ),
		'required'      => false,
		'before'        => '<div class="fc-8 center_marker_settings">',
		'after'         => '</div>',
		'show'          => 'false',
		'choose_button' => esc_html__( 'Choose', 'wp-leaflet-maps-pro' ),
		'remove_button' => esc_html__( 'Remove', 'wp-leaflet-maps-pro' ),
		'id'            => 'marker_center_icon',
	)
);
