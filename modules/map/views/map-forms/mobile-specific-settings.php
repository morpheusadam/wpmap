<?php
/**
 * Map's mobile specific setting(s).
 *
 * @package wp-leaflet-maps-pro
 */

$form->add_element(
	'group', 'mobile_specific_settings', array(
		'value'  => esc_html__( 'Screens Specific Setting', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[mobile_specific]', array(
		'label'   => esc_html__( 'Apply Screens Settings', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_overlay',
		'current' => isset( $data['map_all_control']['mobile_specific'] ) ? $data['map_all_control']['mobile_specific'] : '',
		'desc'    => esc_html__( 'Apply screen specific settings for desktop, mobile and tablets.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.map_mobile_specific' ),
	)
);

$screens_options = array();


$zoom_level = array();
for ( $i = 0; $i < 20; $i++ ) {
	$zoom_level[ $i ] = $i;
}


$supported_screens = array( 'Smartphones', 'iPads', 'Large screens' );


foreach ( $supported_screens as $key => $screen ) {
	$screen_slug = sanitize_title( $screen );
	$width       = $form->field_text(
		'map_all_control[screens][' . $screen_slug . '][map_width_mobile]', array(
			'label'       => esc_html__( 'Map Width', 'wp-leaflet-maps-pro' ),
			'value'       => isset( $data['map_all_control']['screens'][ $screen_slug ]['map_width_mobile'] ) ? $data['map_all_control']['screens'][ $screen_slug ]['map_width_mobile'] : '',
			'placeholder' => esc_html__( 'Map width in pixel.', 'wp-leaflet-maps-pro' ),
		)
	);

	$height = $form->field_text(
		'map_all_control[screens][' . $screen_slug . '][map_height_mobile]', array(
			'label'       => esc_html__( 'Map Height', 'wp-leaflet-maps-pro' ),
			'value'       => isset( $data['map_all_control']['screens'][ $screen_slug ]['map_height_mobile'] ) ? $data['map_all_control']['screens'][ $screen_slug ]['map_height_mobile'] : '',
			'placeholder' => esc_html__( 'Map height in pixel.', 'wp-leaflet-maps-pro' ),
		)
	);


	$zoom = $form->field_select(
		'map_all_control[screens][' . $screen_slug . '][map_zoom_level_mobile]', array(
			'label'         => esc_html__( 'Map Zoom Level', 'wp-leaflet-maps-pro' ),
			'current'       => isset( $data['map_all_control']['screens'][ $screen_slug ]['map_zoom_level_mobile'] ) ? $data['map_all_control']['screens'][ $screen_slug ]['map_zoom_level_mobile'] : '',
			'options'       => $zoom_level,
			'class'         => 'form-controls',
			'default_value' => '5',
		)
	);

	$draggable = $form->field_checkbox(
		'map_all_control[screens][' . $screen_slug . '][map_draggable_mobile]', array(
			'label'         => esc_html__( 'Map Draggable', 'wp-leaflet-maps-pro' ),
			'value'         => 'false',
			'id'            => 'wpomp_map_draggable_mobile',
			'current'       => isset( $data['map_all_control']['screens'][ $screen_slug ]['map_draggable_mobile'] ) ? $data['map_all_control']['screens'][ $screen_slug ]['map_draggable_mobile'] : '',
			'desc'          => esc_html__( 'Tick to off map draggable.', 'wp-leaflet-maps-pro' ),
			'class'         => 'chkbox_class',
			'default_value' => 'true',
		)
	);

	$scrolling = $form->field_checkbox(
		'map_all_control[screens][' . $screen_slug . '][map_scrolling_wheel_mobile]', array(
			'label'         => esc_html__( 'Turn Off Scrolling Wheel', 'wp-leaflet-maps-pro' ),
			'value'         => 'false',
			'id'            => 'map_scrolling_wheel_mobile',
			'current'       => isset( $data['map_all_control']['screens'][ $screen_slug ]['map_scrolling_wheel_mobile'] ) ? $data['map_all_control']['screens'][ $screen_slug ]['map_scrolling_wheel_mobile'] : '',
			'desc'          => esc_html__( 'Tick to off scrolling wheel.', 'wp-leaflet-maps-pro' ),
			'class'         => 'chkbox_class ',
			'default_value' => 'true',

		)
	);

	$screens_options[] = array( $screen, $width, $height, $zoom, $draggable, $scrolling );
}

$form->add_element(
	'table', 'screen_specific_settings', array(
		'heading' => array( 'Screen', 'Width', 'Height', 'Zoom', 'Draggable', 'Scrolling Wheel' ),
		'data'    => $screens_options,
		'before'  => '<div class="fc-12 map_mobile_specific">',
		'after'   => '</div>',
		'show'    => 'false',
	)
);
