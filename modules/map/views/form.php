<?php
/**
 * Template for Add & Edit Map
 *
 * @author  Flipper Code <hello@flippercode.com>
 * @package wp-leaflet-maps-pro
 */


global $wpdb;
$modelFactory = new WPLMP_MODEL();
$map_obj      = $modelFactory->create_object( 'map' );
if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['map_id'] ) ) {
	$map_obj = $map_obj->fetch( array( array( 'map_id', '=', intval( wp_unslash( $_GET['map_id'] ) ) ) ) );
	$map     = $map_obj[0];
	if ( ! empty( $map ) ) {
		$map->map_all_control             = maybe_unserialize( $map->map_all_control );
		$map->map_info_window_setting     = maybe_unserialize( $map->map_info_window_setting );		
		$map->map_locations               = maybe_unserialize( $map->map_locations );
		$map->map_infowindow_setting      = maybe_unserialize( $map->map_infowindow_setting );
		$map->map_geotags                 = maybe_unserialize( $map->map_geotags );
	}

	$data = (array) $map;
} elseif ( ! isset( $_GET['doaction'] ) and isset( $response['success'] ) ) {
	// Reset $_POST object for antoher entry.
	unset( $data );
}

$wpomp_settings = get_option('wpomp_settings');
$form = new WPLMP_Template();
$form->set_header( esc_html__( 'Map Information', 'wp-leaflet-maps-pro' ), $response, $accordion = true, esc_html__( 'Manage Maps', 'wp-leaflet-maps-pro' ), 'wpomp_manage_map' );

$table_group = $form->add_element(
	'group', 'map_info', array(
		'value'  => esc_html__( 'Map Information', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

require 'map-forms/general-setting-form.php';
require 'map-forms/mobile-specific-settings.php';

require 'map-forms/map-center-settings.php';
require 'map-forms/locations-form.php';

require 'map-forms/control-setting-form.php';
require 'map-forms/control-position-style-form.php';

require 'map-forms/layers-form.php';
require 'map-forms/geotag-form.php';

require 'map-forms/tab-setting-form.php';
require 'map-forms/listing-setting-form.php';

require 'map-forms/map-ui.php';
require 'map-forms/url-filter.php';

require 'map-forms/import-maps.php';
require 'map-forms/extensible-settings.php';

$form->add_element(
	'extensions', 'wpomp_map_form', array(
		'value'  => $data,
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);
$form->add_element(
	'submit', 'save_entity_data', array(
		'value' => esc_html__( 'Save Map', 'wp-leaflet-maps-pro' ),
	)
);
$form->add_element(
	'hidden', 'operation', array(
		'value' => 'save',
	)
);
$form->add_element(
	'hidden', 'map_locations', array(
		'value' => '',
	)
);
$form->add_element(
	'hidden', 'map_all_control[fc_custom_styles]', array(
		'value' => '',
		'id'    => 'fc_custom_styles',
	)
);
if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['map_id'] ) ) {

	$form->add_element(
		'hidden', 'entityID', array(
			'value' => intval( wp_unslash( $_GET['map_id'] ) ),
		)
	);
}
$form->render();
