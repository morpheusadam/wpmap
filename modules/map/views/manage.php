<?php
/**
 * Manage Maps
 *
 * @package Maps
 */
  $form = new WPLMP_Template();
  $form->show_header();
if ( class_exists( 'FlipperCode_List_Table_Helper' ) and ! class_exists( 'wpomp_Maps_Table' ) ) {

	/**
	 * Display maps manager.
	 */
	class wpomp_Maps_Table extends FlipperCode_List_Table_Helper {
		/**
		 * Intialize manage category table.
		 *
		 * @param array $tableinfo Table's properties.
		 */
		public function __construct( $tableinfo ) {
			parent::__construct( $tableinfo ); }
		/**
		 * Output for Shortcode column.
		 *
		 * @param array $item Map Row.
		 */
		public function column_shortcodes( $item ) {
			echo '[put_wplmp_map id="' . $item->map_id . '"]'; }
		/**
		 * Clone of the map.
		 *
		 * @param  integer $item Map ID.
		 */
		public function copy() {
			$map_id       = intval( $_GET['map_id'] );
			$modelFactory = new WPLMP_MODEL();
			$map_obj      = $modelFactory->create_object( 'map' );
			$map          = $map_obj->copy( $map_id );
			$this->prepare_items();
			$this->listing();
		}

	}

	global $wpdb;
	$columns   = array(
		'map_title'      => esc_html__( 'Title', 'wp-leaflet-maps-pro' ),
		'map_width'      => esc_html__( 'Map Width', 'wp-leaflet-maps-pro' ),
		'map_height'     => esc_html__( 'Map Height', 'wp-leaflet-maps-pro' ),
		'map_zoom_level' => esc_html__( 'Map Zoom Level', 'wp-leaflet-maps-pro' ),
		'shortcodes'     => esc_html__( 'Shortcodes', 'wp-leaflet-maps-pro' ),
	);
	$sortable  = array( 'map_title', 'map_width', 'map_height', 'map_zoom_level', 'map_type' );
	$tableinfo = array(
		'table'                   => WPLMP_TBL_MAP,
		'textdomain'              => 'wp-leaflet-maps-pro',
		'singular_label'          => esc_html__( 'map', 'wp-leaflet-maps-pro' ),
		'plural_label'            => esc_html__( 'maps', 'wp-leaflet-maps-pro' ),
		'admin_listing_page_name' => 'wpomp_manage_map',
		'admin_add_page_name'     => 'wpomp_form_map',
		'primary_col'             => 'map_id',
		'columns'                 => $columns,
		'sortable'                => $sortable,
		'per_page'                => 20,
		'actions'                 => array( 'edit', 'delete', 'copy' ),
		'bulk_actions'            => array( 'delete' => esc_html__( 'Delete', 'wp-leaflet-maps-pro' ) ),
		'col_showing_links'       => 'map_title',
		'searchExclude'           => array( 'shortcodes' ),
		'translation' => array(
			'manage_heading'      => esc_html__( 'Manage Maps', 'wp-leaflet-maps-pro' ),
			'add_button'          => esc_html__( 'Add Map', 'wp-leaflet-maps-pro' ),
			'delete_msg'          => esc_html__( 'Maps deleted successfully', 'wp-leaflet-maps-pro' ),
			'insert_msg'          => esc_html__( 'Map added successfully', 'wp-leaflet-maps-pro' ),
			'update_msg'          => esc_html__( 'Map updated successfully', 'wp-leaflet-maps-pro' ),
			'search_text'		  => esc_html__( 'Search', 'wp-leaflet-maps-pro' ),
		),
	);
	$obj       = new wpomp_Maps_Table( $tableinfo );
}
