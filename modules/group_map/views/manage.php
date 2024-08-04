<?php
/**
 * Manage Marker Categories
 *
 * @package wp-leaflet-maps-pro
 */

  $form = new WPLMP_Template();
  $form->show_header();

if ( class_exists( 'FlipperCode_List_Table_Helper' ) and ! class_exists( 'wpomp_Manage_Group_Table' ) ) {

	/**
	 * Display categories manager.
	 */
	class wpomp_Manage_Group_Table extends FlipperCode_List_Table_Helper {

		/**
		 * Intialize manage category table.
		 *
		 * @param array $tableinfo Table's properties.
		 */
		public function __construct( $tableinfo ) {
			parent::__construct( $tableinfo ); }
		/**
		 * Show marker image assigned to category.
		 *
		 * @param  array $item Category row.
		 * @return html       Image tag.
		 */
		public function column_group_marker( $item ) {
			if ( strstr( $item->group_marker, 'wp-google-map-pro/icons/' ) !== false ) {
				$item->group_marker = str_replace( 'icons', 'assets/images/icons', $item->group_marker );
			}
			return sprintf( '<img src="' . $item->group_marker . '" name="group_image[]" value="%s" />', $item->group_map_id );
		}
		/**
		 * Show category's parent name.
		 *
		 * @param  [type] $item Category row.
		 * @return string       Category name.
		 */
		public function column_group_parent( $item ) {

			 global $wpdb;
			 $parent = $wpdb->get_col( $wpdb->prepare( 'SELECT group_map_title FROM ' . $this->table . ' where group_map_id = %d', $item->group_parent ) );
			 $parent = ( ! empty( $parent ) ) ? ucwords( $parent[0] ) : '---';
			 return $parent;

		}

		public function column_extensions_fields( $item ) {

			 global $wpdb;
			 $order = unserialize( $item->extensions_fields );
			 
			 return isset($order['cat_order']) ? $order['cat_order'] : '';

		}

	}
	global $wpdb;
	$columns   = array(
		'group_map_title'   => esc_html__( 'Category Title', 'wp-leaflet-maps-pro' ),
		'group_marker'      => esc_html__( 'Marker Image', 'wp-leaflet-maps-pro' ),
		'group_parent'      => esc_html__( 'Parent Category', 'wp-leaflet-maps-pro' ),
		'extensions_fields' => esc_html__( 'Priority Order', 'wp-leaflet-maps-pro' ),
		'group_added'       => esc_html__( 'Updated On', 'wp-leaflet-maps-pro' ),
	);
	$sortable  = array( 'group_map_title', 'extensions_fields' );
	$tableinfo = array(
		'table'                   => $wpdb->prefix . 'wpomp_group_map',
		'textdomain'              => 'wp-leaflet-maps-pro',
		'singular_label'          => esc_html__( 'marker category', 'wp-leaflet-maps-pro' ),
		'plural_label'            => esc_html__( 'Categories', 'wp-leaflet-maps-pro' ),
		'admin_listing_page_name' => 'wpomp_manage_group_map',
		'admin_add_page_name'     => 'wpomp_form_group_map',
		'primary_col'             => 'group_map_id',
		'columns'                 => $columns,
		'sortable'                => $sortable,
		'per_page'                => 20,
		'col_showing_links'       => 'group_map_title',
		'searchExclude'           => array( 'group_parent' ),
		'bulk_actions'            => array( 'delete' => esc_html__( 'Delete', 'wp-leaflet-maps-pro' ) ),
		'translation' => array(
			'manage_heading'      => esc_html__( 'Manage Categories', 'wp-leaflet-maps-pro' ),
			'add_button'          => esc_html__( 'Add Category', 'wp-leaflet-maps-pro' ),
			'delete_msg'          => esc_html__( 'Category deleted successfully', 'wp-leaflet-maps-pro' ),
			'insert_msg'          => esc_html__( 'Category added successfully', 'wp-leaflet-maps-pro' ),
			'update_msg'          => esc_html__( 'Category updated successfully', 'wp-leaflet-maps-pro' ),
			'search_text'		  => esc_html__( 'Search', 'wp-leaflet-maps-pro' ),
		),
	);
	return new wpomp_Manage_Group_Table( $tableinfo );

}

