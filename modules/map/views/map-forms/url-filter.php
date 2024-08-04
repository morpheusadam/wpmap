<?php
/**
 * Map's Advanced setting(s).
 *
 * @package wp-leaflet-maps-pro
 */

$form->add_element(
	'group', 'map_advanced_setting', array(
		'value'  => esc_html__( 'Advanced Settings', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox_toggle', 'map_all_control[url_filter]', array(
		'label'   => esc_html__( 'URL Filters', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_url_filter',
		'current' => isset( $data['map_all_control']['url_filter'] ) ? $data['map_all_control']['url_filter'] : '',
		'desc'    => esc_html__( 'Check to enable filters by url parameters.', 'wp-leaflet-maps-pro' ),
		'class'   => 'checkbox_toggle switch_onoff',
		'data'    => array( 'target' => '.url_filer_options' ),
	)
);

$form->add_element(
	'message', 'url_instruction', array(
		'value' => esc_html__( 'You can filter markers/locations/posts on maps using url parameters. Following default parameters are supported :', 'wp-leaflet-maps-pro' ),
		'class' => 'fc-msg fc-success url_filer_options',
		'show'  => 'false',
	)
);

$url_parameters = array(
	array( 'search', esc_html__( 'Search Term', 'wp-leaflet-maps-pro' ) ),
	array( 'category', esc_html__( 'Category ID or Name.', 'wp-leaflet-maps-pro' ) ),
	array( 'limit', esc_html__( '# of Locations.', 'wp-leaflet-maps-pro' ) ),
	array( 'perpage', esc_html__( '# of Locations per page.', 'wp-leaflet-maps-pro' ) ),
	array( 'zoom', esc_html__( 'Zoom Level.', 'wp-leaflet-maps-pro' ) ),
	array( 'hide_map', esc_html__( 'To hide the map. Filters & listing will be visible if enabled.', 'wp-leaflet-maps-pro' ) ),
	array( 'maps_only', esc_html__( 'To show only maps. Tabs, filters, listing will be hide.', 'wp-leaflet-maps-pro' ) ),
);

$form->add_element(
	'table', 'wpomp_urlparameters_table', array(
		'heading' => array( 'Query Parameter', 'Value' ),
		'data'    => $url_parameters,
		'before'  => '<div class="fc-8">',
		'after'   => '</div>',
		'class'   => 'fc-table fc-table-layout5 url_filer_options',
		'show'    => 'false',
	)
);
