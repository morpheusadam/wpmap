<?php
/**
 * Contro Positioning over leaflet maps.
 *
 * @package wp-leaflet-maps-pro
 * @author Flipper Code <hello@flippercode.com>
 */


$form->add_element(
	'group', 'map_import_setting', array(
		'value'  => esc_html__( 'Import Settings', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'textarea', 'wpomp_import_code', array(
		'label'         => esc_html__( 'Import Code', 'wp-leaflet-maps-pro' ),
		'value'         => '',
		'desc'          => esc_html__( 'Paste here import json code to overwrite map settings. Your map settings will be overwrite permanately.', 'wp-leaflet-maps-pro' ),
		'textarea_rows' => 10,
		'textarea_name' => 'wpomp_import_code',
		'class'         => 'form-control',
	)
);

if ( ! empty( $map ) ) {

	$json_hash = base64_encode( serialize( $map ) );

	$form->add_element(
		'textarea', 'wpomp_export_code', array(
			'label'         => esc_html__( 'Export Code', 'wp-leaflet-maps-pro' ),
			'value'         => $json_hash,
			'desc'          => esc_html__( 'Copy above export code and paste on your map import setting to migrate maps settings from one site to another site.', 'wp-leaflet-maps-pro' ),
			'textarea_rows' => 10,
			'textarea_name' => 'wpomp_export_code',
			'class'         => 'form-control',
		)
	);

}
