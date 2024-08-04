<?php
/**
 * Template for Add & Edit Category
 *
 * @author  Flipper Code <hello@flippercode.com>
 * @package wp-leaflet-maps-pro
 */


global $wpdb;
$modelFactory = new WPLMP_Model();
$category     = $modelFactory->create_object( 'group_map' );
$categories   = (array) $category->fetch();
if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['group_map_id'] ) ) {
	$category_obj = $category->fetch( array( array( 'group_map_id', '=', intval( wp_unslash( $_GET['group_map_id'] ) ) ) ) );
	$_POST        = (array) $category_obj[0];
} elseif ( ! isset( $_GET['doaction'] ) and isset( $response['success'] ) ) {
	// Reset $_POST object for antoher entry.
	unset( $_POST );
}
$form = new WPLMP_Template();
$form->set_header( esc_html__( 'Marker Category', 'wp-leaflet-maps-pro' ), $response, $accordion = false, esc_html__( 'Manage Marker Categories', 'wp-leaflet-maps-pro' ), 'wpomp_manage_group_map' );

$form->add_element(
	'group', 'marker_category', array(
		'value'  => esc_html__( 'Marker Category', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

if ( is_array( $categories ) ) {
	$markers = array( ' ' => esc_html__( 'Please Select', 'wp-leaflet-maps-pro' ) );
	foreach ( $categories as $i => $single_category ) {
			$markers[ $single_category->group_map_id ] = $single_category->group_map_title;
	}

	$form->add_element(
		'select', 'group_parent', array(
			'label'   => esc_html__( 'Parent Category', 'wp-leaflet-maps-pro' ),
			'current' => ( isset( $_POST['group_parent'] ) and ! empty( $_POST['group_parent'] ) ) ? intval( wp_unslash( $_POST['group_parent'] ) ) : '',
			'desc'    => esc_html__( 'Assign parent category if any.', 'wp-leaflet-maps-pro' ),
			'options' => $markers,
		)
	);

}

$form->add_element(
	'text', 'group_map_title', array(
		'label'       => esc_html__( 'Marker Category Title', 'wp-leaflet-maps-pro' ),
		'value'       => ( isset( $_POST['group_map_title'] ) and ! empty( $_POST['group_map_title'] ) ) ? sanitize_text_field( wp_unslash( $_POST['group_map_title'] ) ) : '',
		'id'          => 'group_map_title',
		'desc'        => esc_html__( 'Please enter marker category title / name.', 'wp-leaflet-maps-pro' ),
		'class'       => 'create_map form-control',
		'placeholder' => esc_html__( 'Marker Category Title', 'wp-leaflet-maps-pro' ),
		'required'    => true,
	)
);


$form->add_element(
	'image_picker', 'group_marker', array(
		'label'         => esc_html__( 'Choose Marker Image', 'wp-leaflet-maps-pro' ),
		'src'           => ( isset( $_POST['group_marker'] ) ) ? wp_unslash( $_POST['group_marker'] ) : WPLMP_IMAGES . '/default_marker.png',
		'required'      => false,
		'choose_button' => esc_html__( 'Choose', 'wp-leaflet-maps-pro' ),
		'remove_button' => esc_html__( 'Remove', 'wp-leaflet-maps-pro' ),
		'id'            => 'marker_category_icon',
	)
);

$form->set_col( 1 );
$form->add_element(
	'text', 'extensions_fields[cat_order]', array(
		'label'         => esc_html__( 'Marker Category Order', 'wp-leaflet-maps-pro' ),
		'value'         => ( isset( $_POST['extensions_fields']['cat_order'] ) and ! empty( $_POST['extensions_fields']['cat_order'] ) ) ? sanitize_text_field( wp_unslash( $_POST['extensions_fields']['cat_order'] ) ) : '',
		'id'            => 'group_map_title',
		'desc'          => esc_html__( 'Enter a numeric value for priority for this category. Please note no two cagegories must have same priority number. On frontend map, you can display categories in tabs sorted by priority number specified here.', 'wp-leaflet-maps-pro' ),
		'class'         => 'create_map form-control',
		'placeholder'   => esc_html__( 'Enter category order in numeric value.', 'wp-leaflet-maps-pro' ),
		'default_value' => 0,
	)
);

$form->add_element(
	'extensions', 'wpomp_category_form', array(
		'value'  => isset( $_POST['extensions_fields'] ) ? $_POST['extensions_fields'] : '',
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);


$form->add_element(
	'submit', 'create_group_map_location', array(
		'value'  => esc_html__( 'Save Marker Category', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',

	)
);

$form->add_element(
	'hidden', 'operation', array(
		'value' => 'save',
	)
);

if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] ) {
	$form->add_element(
		'hidden', 'entityID', array(
			'value' => intval( wp_unslash( $_GET['group_map_id'] ) ),
		)
	);
}

$form->render();
