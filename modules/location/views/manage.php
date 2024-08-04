<?php
if ( class_exists( 'FlipperCode_List_Table_Helper' ) and ! class_exists( 'wpomp_Location_Table' ) ) {

	class wpomp_Location_Table extends FlipperCode_List_Table_Helper {
		public function __construct( $tableinfo ) {
			parent::__construct( $tableinfo ); }  }

	// Minimal Configuration :)
	global $wpdb;
	$columns   = array(
		'location_title'     => esc_html__( 'Title', 'wp-leaflet-maps-pro' ),
		'location_address'   => esc_html__( 'Address', 'wp-leaflet-maps-pro' ),
		'location_city'      => esc_html__( 'City', 'wp-leaflet-maps-pro' ),
		'location_latitude'  => esc_html__( 'Latitude', 'wp-leaflet-maps-pro' ),
		'location_longitude' => esc_html__( 'Longitude', 'wp-leaflet-maps-pro' ),
	);
	$sortable  = array( 'location_title', 'location_address', 'location_city', 'location_latitude', 'location_longitude' );
	$tableinfo = array(
		'table'                   => WPLMP_TBL_LOCATION,
		'textdomain'              => 'wp-leaflet-maps-pro',
		'singular_label'          => esc_html__( 'location', 'wp-leaflet-maps-pro' ),
		'plural_label'            => esc_html__( 'locations', 'wp-leaflet-maps-pro' ),
		'admin_listing_page_name' => 'wpomp_manage_location',
		'admin_add_page_name'     => 'wpomp_form_location',
		'primary_col'             => 'location_id',
		'columns'                 => $columns,
		'sortable'                => $sortable,
		'per_page'                => 200,
		'actions'                 => array( 'edit', 'delete' ),
		'bulk_actions'            => array(
			'delete' => esc_html__( 'Delete', 'wp-leaflet-maps-pro' ),
			'export_location_csv_wplmp' => esc_html__( 'Export as CSV', 'wp-leaflet-maps-pro' ),
		),
		'col_showing_links'       => 'location_title',
		'translation' => array(
			'manage_heading'      => esc_html__( 'Manage Locations', 'wp-leaflet-maps-pro' ),
			'add_button'          => esc_html__( 'Add Location', 'wp-leaflet-maps-pro' ),
			'delete_msg'          => esc_html__( 'Location(s) deleted successfully', 'wp-leaflet-maps-pro' ),
			'insert_msg'          => esc_html__( 'Location added successfully', 'wp-leaflet-maps-pro' ),
			'update_msg'          => esc_html__( 'Location updated successfully', 'wp-leaflet-maps-pro' ),
			'search_text'		  => esc_html__( 'Search', 'wp-leaflet-maps-pro' ),
		),
	);
	return new wpomp_Location_Table( $tableinfo );

}

