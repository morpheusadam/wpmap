<?php
/**
 * Control Setting(s).
 *
 * @package wp-leaflet-maps-pro
 */

$form->add_element(
	'group', 'map_control_setting', array(
		'value'  => esc_html__( 'Control Setting', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[zoom_control]', array(
		'label'   => esc_html__( 'Turn Off Zoom Control', 'wp-leaflet-maps-pro' ),
		'value'   => 'false',
		'id'      => 'wpomp_zoom_control',
		'current' => isset( $data['map_all_control']['zoom_control'] ) ? $data['map_all_control']['zoom_control'] : '',
		'desc'    => esc_html__( 'Please check to disable zoom control.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);


$form->add_element(
	'checkbox', 'map_all_control[full_screen_control]', array(
		'label'   => esc_html__( 'Turn Off Full Screen Control', 'wp-leaflet-maps-pro' ),
		'value'   => 'false',
		'id'      => 'full_screen_control',
		'current' => isset( $data['map_all_control']['full_screen_control'] ) ? $data['map_all_control']['full_screen_control'] : '',
		'desc'    => esc_html__( 'Please check to disable full screen control.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);


$form->add_element(
	'checkbox', 'map_all_control[map_type_control]', array(
		'label'   => esc_html__( 'Turn Off Map Type Control', 'wp-leaflet-maps-pro' ),
		'value'   => 'false',
		'id'      => 'map_type_control',
		'current' => isset( $data['map_all_control']['map_type_control'] ) ? $data['map_all_control']['map_type_control'] : '',
		'desc'    => esc_html__( 'Please check to disable map type control.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);
$form->add_element(
	'checkbox', 'map_all_control[scale_control]', array(
		'label'   => esc_html__( 'Turn Off Scale Control', 'wp-leaflet-maps-pro' ),
		'value'   => 'false',
		'id'      => 'scale_control',
		'current' => isset( $data['map_all_control']['scale_control'] ) ? $data['map_all_control']['scale_control'] : '',
		'desc'    => esc_html__( 'Please check to disable scale control.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);


$form->add_element(
	'checkbox', 'map_all_control[search_control]', array(
		'label'   => esc_html__( 'Turn On Search Control', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'search_control',
		'current' => isset( $data['map_all_control']['search_control'] ) ? $data['map_all_control']['search_control'] : '',
		'desc'    => esc_html__( 'Please check to enable search box control.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[locateme_control]', array(
		'label'   => esc_html__( 'Turn On Locate Me Control', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'search_control',
		'current' => isset( $data['map_all_control']['locateme_control'] ) ? $data['map_all_control']['locateme_control'] : '',
		'desc'    => esc_html__( 'Please check to enable locate me control.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);
