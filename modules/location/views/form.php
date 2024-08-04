<?php
/**
 * Template for Add & Edit Location
 *
 * @author  Flipper Code <hello@flippercode.com>
 * @package wp-leaflet-maps-pro
 */

global $wpdb;

$wpomp_settings = get_option( 'wpomp_settings', true );

$modelFactory = new WPLMP_MODEL();
$category_obj = $modelFactory->create_object( 'group_map' );
$categories   = $category_obj->fetch();
if ( is_array( $categories ) and ! empty( $categories ) ) {
	$all_categories = array();
	foreach ( $categories as $category ) {
		$all_categories [ $category->group_map_id ] = $category;
	}
}
$location_obj = $modelFactory->create_object( 'location' );
if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['location_id'] ) ) {
	$location_obj = $location_obj->fetch( array( array( 'location_id', '=', intval( wp_unslash( $_GET['location_id'] ) ) ) ) );
	$data         = (array) $location_obj[0];
} elseif ( ! isset( $_GET['doaction'] ) and isset( $response['success'] ) ) {
	// Reset $_POST object for antoher entry.
	unset( $data );
}
$form = new WPLMP_Template();
$form->set_header( esc_html__( 'Location Information', 'wp-leaflet-maps-pro' ), $response, $accordion = true, esc_html__( 'Manage Locations', 'wp-leaflet-maps-pro' ), 'wpomp_manage_location' );

$form->add_element(
	'group', 'location_information', array(
		'value'  => esc_html__( 'Location Information', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'text', 'location_title', array(
		'label'       => esc_html__( 'Location Title', 'wp-leaflet-maps-pro' ),
		'value'       => ( isset( $data['location_title'] ) and ! empty( $data['location_title'] ) ) ? $data['location_title'] : '',
		'required'    => true,
		'placeholder' => esc_html__( 'Enter Location Title', 'wp-leaflet-maps-pro' ),
	)
);

$form->add_element(
	'text', 'location_address', array(
		'label'       => esc_html__( 'Location Address', 'wp-leaflet-maps-pro' ),
		'value'       => ( isset( $data['location_address'] ) and ! empty( $data['location_address'] ) ) ? $data['location_address'] : '',
		'desc'        => esc_html__( 'Please choose an address for the location from the list of addresses displayed by places autosuggest control above.', 'wp-leaflet-maps-pro' ),
		'required'    => true,
		'class'       => 'form-control wpomp_auto_suggest',
		'placeholder' => esc_html__( 'Type Location Address', 'wp-leaflet-maps-pro' ),
		'id'=>'searchBox'
	)
);
$form->set_col( 2 );
$form->add_element(
	'text', 'location_latitude', array(
		'label'       => esc_html__( 'Latitude and Longitude', 'wp-leaflet-maps-pro' ),
		'value'       => ( isset( $data['location_latitude'] ) and ! empty( $data['location_latitude'] ) ) ? $data['location_latitude'] : '',
		'id'          => 'googlemap_latitude',
		'class'       => 'google_latitude form-control',
		'placeholder' => esc_html__( 'Latitude', 'wp-leaflet-maps-pro' ),
		'before'      => '<div class="fc-4">',
		'after'       => '</div>',
	)
);
$form->add_element(
	'text', 'location_longitude', array(
		'value'       => ( isset( $data['location_longitude'] ) and ! empty( $data['location_longitude'] ) ) ? $data['location_longitude'] : '',
		'id'          => 'googlemap_longitude',
		'class'       => 'google_longitude form-control',
		'placeholder' => esc_html__( 'Longitude', 'wp-leaflet-maps-pro' ),
		'before'      => '<div class="fc-4">',
		'after'       => '</div>',
	)
);
$form->add_element(
	'text', 'location_city', array(
		'label'       => esc_html__( 'City and State', 'wp-leaflet-maps-pro' ),
		'value'       => ( isset( $data['location_city'] ) and ! empty( $data['location_city'] ) ) ? $data['location_city'] : '',
		'id'          => 'googlemap_city',
		'class'       => 'google_city form-control',
		'placeholder' => esc_html__( 'City', 'wp-leaflet-maps-pro' ),
		'before'      => '<div class="fc-4">',
		'after'       => '</div>',
	)
);
$form->add_element(
	'text', 'location_state', array(
		'value'       => ( isset( $data['location_state'] ) and ! empty( $data['location_state'] ) ) ? $data['location_state'] : '',
		'id'          => 'googlemap_state',
		'class'       => 'google_state form-control',
		'placeholder' => esc_html__( 'State', 'wp-leaflet-maps-pro' ),
		'before'      => '<div class="fc-4">',
		'after'       => '</div>',
	)
);
$form->add_element(
	'text', 'location_country', array(
		'label'       => esc_html__( 'Country and Postal Code', 'wp-leaflet-maps-pro' ),
		'value'       => ( isset( $data['location_country'] ) and ! empty( $data['location_country'] ) ) ? $data['location_country'] : '',
		'id'          => 'googlemap_country',
		'class'       => 'google_country form-control',
		'placeholder' => esc_html__( 'Country', 'wp-leaflet-maps-pro' ),
		'before'      => '<div class="fc-4">',
		'after'       => '</div>',
	)
);
$form->add_element(
	'text', 'location_postal_code', array(
		'value'       => ( isset( $data['location_postal_code'] ) and ! empty( $data['location_postal_code'] ) ) ? $data['location_postal_code'] : '',
		'id'          => 'googlemap_postal_code',
		'class'       => 'google_postal_code form-control',
		'placeholder' => esc_html__( 'Postal Code', 'wp-leaflet-maps-pro' ),
		'before'      => '<div class="fc-4">',
		'after'       => '</div>',
	)
);
$form->set_col( 1 );
$form->add_element(
	'div', 'wpomp_map', array(
		'label' => esc_html__( 'Current Location', 'wp-leaflet-maps-pro' ),
		'id'    => 'wpomp_map',
		'style' => array(
			'width'  => '100%',
			'height' => '300px',
		),
	)
);


$form->add_element(
	'radio', 'location_settings[onclick]', array(
		'label'           => esc_html__( 'On Click', 'wp-leaflet-maps-pro' ),
		'radio-val-label' => array(
			'marker'      => esc_html__( 'Display Infowindow', 'wp-leaflet-maps-pro' ),
			'custom_link' => esc_html__( 'Redirect', 'wp-leaflet-maps-pro' ),
		),
		'current'         => isset( $data['location_settings']['onclick'] ) ? $data['location_settings']['onclick'] : '',
		'class'           => 'chkbox_class switch_onoff',
		'default_value'   => 'marker',
		'data'            => array( 'target' => '.wpomp_location_onclick' ),
	)
);


$form->add_element(
	'textarea', 'location_messages', array(
		'label'         => esc_html__( 'Infowindow Message', 'wp-leaflet-maps-pro' ),
		'value'         => ( isset( $data['location_messages'] ) and ! empty( $data['location_messages'] ) ) ? $data['location_messages'] : '',
		'desc'          => esc_html__( 'Enter here the infoWindow message.', 'wp-leaflet-maps-pro' ),
		'textarea_rows' => 10,
		'textarea_name' => 'location_messages',
		'class'         => 'form-control wpomp_location_onclick wpomp_location_onclick_marker',
		'id'            => 'googlemap_infomessage',
		'show'          => 'false',
	)
);

$form->add_element(
	'text', 'location_settings[redirect_link]', array(
		'label'  => esc_html__( 'Redirect Url', 'wp-leaflet-maps-pro' ),
		'value'  => isset( $data['location_settings']['redirect_link'] ) ? $data['location_settings']['redirect_link'] : '',
		'desc'   => esc_html__( 'Enter here the redirect url. e.g http://www.flippercode.com', 'wp-leaflet-maps-pro' ),
		'class'  => 'wpomp_location_onclick_custom_link wpomp_location_onclick form-control',
		'before' => '<div class="fc-8">',
		'after'  => '</div>',
		'show'   => 'false',
	)
);

$form->add_element(
	'select', 'location_settings[redirect_link_window]', array(
		'options' => array(
			'yes' => esc_html__( 'YES', 'wp-leaflet-maps-pro' ),
			'no'  => esc_html__( 'NO', 'wp-leaflet-maps-pro' ),
		),
		'label'   => esc_html__( 'Open in new tab', 'wp-leaflet-maps-pro' ),
		'current' => isset( $data['location_settings']['redirect_link_window'] ) ? $data['location_settings']['redirect_link_window'] : '',
		'desc'    => esc_html__( 'Open a new window tab.', 'wp-leaflet-maps-pro' ),
		'class'   => 'wpomp_location_onclick_custom_link wpomp_location_onclick form-control',
		'before'  => '<div class="fc-8">',
		'after'   => '</div>',
		'show'    => 'false',
	)
);

$form->add_element(
	'image_picker', 'location_settings[featured_image]', array(
		'label'         => esc_html__( 'Location Image', 'wp-leaflet-maps-pro' ),
		'src'           => isset( $data['location_settings']['featured_image'] ) ? wp_unslash( $data['location_settings']['featured_image'] ) : '',
		'required'      => false,
		'choose_button' => esc_html__( 'Choose', 'wp-leaflet-maps-pro' ),
		'remove_button' => esc_html__( 'Remove', 'wp-leaflet-maps-pro' ),
		'id' => 'loc_img',
	)
);



$form->add_element(
	'checkbox', 'location_settings[hide_infowindow]', array(
		'label'   => esc_html__( 'Disable Infowindow', 'wp-leaflet-maps-pro' ),
		'value'   => 'false',
		'id'      => 'location_settings',
		'current' => isset( $data['location_settings']['hide_infowindow'] ) ? $data['location_settings']['hide_infowindow'] : '',
		'desc'    => esc_html__( 'Do you want to disable infowindow for this location?', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);
$form->add_element(
	'checkbox', 'location_infowindow_default_open', array(
		'label'   => esc_html__( 'Infowindow Default Open', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'location_infowindow_default_open',
		'current' => isset( $data['location_infowindow_default_open'] ) ? $data['location_infowindow_default_open'] : '',
		'desc'    => esc_html__( 'Check to enable infowindow default open.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);
$form->add_element(
	'checkbox', 'location_draggable', array(
		'label'   => esc_html__( 'Marker Draggable', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'location_draggable',
		'current' => isset( $data['location_draggable'] ) ? $data['location_draggable'] : '',
		'desc'    => esc_html__( 'Check if you want to allow visitors to drag the marker.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);

$form->add_element(
	'group', 'location_extra_fields', array(
		'value'  => esc_html__( 'Extra Fields Values', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);
$extra_data['location_extrafields'] = unserialize( get_option( 'wpomp_location_extrafields' ) );

if ( isset( $extra_data['location_extrafields'] ) ) {
	if ( ! empty( $extra_data['location_extrafields'] ) ) {
		foreach ( $extra_data['location_extrafields'] as $i => $label ) {
			if ( $label == '' ) {
				continue;
			}
			$field_name = sanitize_title( $label );
			$form->add_element(
				'text', 'location_extrafields[' . $field_name . ']', array(
					'label'       => ( isset( $label ) and ! empty( $label ) ) ? $label : '',
					'value'       => ( isset( $data['location_extrafields'][ $field_name ] ) and ! empty( $data['location_extrafields'][ $field_name ] ) ) ? $data['location_extrafields'][ $field_name ] : '',
					'desc'        => '',
					'class'       => 'location_newfields form-control',
					'placeholder' => esc_html__( 'Field Value', 'wp-leaflet-maps-pro' ),
					'before'      => '<div class="fc-4">',
					'after'       => '</div>',
				)
			);

		}
	} else {

		$setting_link = '<a target="_blank" href="' . admin_url( 'admin.php?page=wpomp_manage_settings' ) . '">'.esc_html__('Settings','wp-leaflet-maps-pro').'</a>';

		$form->add_element(
			'message', 'extra_fields_instruction', array(
				'value' => sprintf( esc_html__( 'No extra fields found. You can create dynamic extra fields for locations from %1$s page.', 'wp-leaflet-maps-pro' ), $setting_link ),
				'class' => 'fc-msg fc-danger',
				'before'      => '<div class="fc-12">',
				'after'       => '</div>'
			)
		);
	}
}


$form->add_element(
	'group', 'marker_category_listing', array(
		'value'  => esc_html__( 'Marker Categories', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

if ( ! empty( $all_categories ) ) {
	$category_data        = array();
	$parent_category_data = array();
	if ( ! isset( $data['location_group_map'] ) ) {
		$data['location_group_map'] = array(); }

	foreach ( $categories as $category ) {
		if ( is_null( $category->group_parent ) or 0 == $category->group_parent ) {
			$parent_category_data = ' ---- ';
		} else {
			$parent_category_data = $all_categories[ $category->group_parent ]->group_map_title;
		}
		if ( '' != $category->group_marker ) {
			$icon_src = "<img src='" . $category->group_marker . "' />";
		} else {
			$icon_src = "<img src='" . WPLMP_IMAGES . "default_marker.png' />";

		}
		$select_input    = $form->field_checkbox(
			'location_group_map[]', array(
				'value'   => $category->group_map_id,
				'current' => ( in_array( $category->group_map_id, $data['location_group_map'] ) ? $category->group_map_id : '' ),
				'class'   => 'chkbox_class',
				'before'  => '<div class="fc-1">',
				'after'   => '</div>',
			)
		);
		$category_data[] = array( $select_input, $category->group_map_title, $parent_category_data, $icon_src );
	}
	$category_data = $form->add_element(
		'table', 'location_group_map', array(
			'heading' => array( esc_html__( 'Select', 'wp-leaflet-maps-pro' ), esc_html__( 'Category', 'wp-leaflet-maps-pro' ), esc_html__( 'Parent', 'wp-leaflet-maps-pro' ), esc_html__( 'Icon', 'wp-leaflet-maps-pro' ) ),
			'data'    => $category_data,
			'class'   => 'fc-table fc-table-layout3',
			'before'  => '<div class="fc-12">',
			'after'   => '</div>',
		)
	);
} else {

	$add_marker_category = '<a target="_blank" href="' . admin_url( 'admin.php?page=wpomp_form_group_map' ) . '">'.esc_html__('here','wp-leaflet-maps-pro').'</a>';
	 	
	$form->add_element(
		'message', 'no_marker_category_message', array(
			'value'  => sprintf( esc_html__( 'You don\'t have marker categories right now. You can create marker categories from %1$s', 'wp-leaflet-maps-pro' ), $add_marker_category ),
			'class'  => 'fc-msg fc-danger',
			'before' => '<div class="fc-12">',
			'after'  => '</div>',
		)
	);
}

$form->add_element(
	'extensions', 'wpomp_location_form', array(
		'value'  => isset( $data['location_settings']['extensions_fields'] ) ? $data['location_settings']['extensions_fields'] : '',
		'before' => '<div class="fc-11">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'submit', 'save_entity_data', array(
		'value' => esc_html__( 'Save Location', 'wp-leaflet-maps-pro' ),
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
			'value' => intval( wp_unslash( $_GET['location_id'] ) ),
		)
	);
}


$infowindow_message = ( isset( $data['location_messages'] ) and ! empty( $data['location_messages'] ) ) ? $data['location_messages'] : '';
$infowindow_disable = ( isset( $data['location_settings'] ) and ! empty( $data['location_settings'] ) ) ? $data['location_settings'] : '';

$category = new stdClass();

if ( isset( $_GET['group_map_id'] ) ) {

	$category_obj       = $category_obj->get( array( array( 'group_map_id', '=', intval( wp_unslash( $_GET['group_map_id'] ) ) ) ) );

	$category           = (array) $category_obj[0];

}


if ( ! empty( $category->group_marker ) ) {
	$category_group_marker = $category->group_marker;
} else {
	$category_group_marker = WPLMP_IMAGES . 'default_marker.png';
}
$map_data['map_options'] = array(
	'center_lat' => ( isset( $data['location_latitude'] ) and ! empty( $data['location_latitude'] ) ) ? $data['location_latitude'] : '40.6153983',
	'center_lng' => ( isset( $data['location_longitude'] ) and ! empty( $data['location_longitude'] ) ) ? $data['location_longitude'] : '-74.2535216',
	'marker_default_icon' =>   WPLMP_ICONS . 'marker_default_icon.png'
);
$map_data['places'][]    = array(
	'id'         => ( isset( $data['location_id'] ) and ! empty( $data['location_id'] ) ) ? $data['location_id'] : '',
	'title'      => ( isset( $data['location_title'] ) and ! empty( $data['location_title'] ) ) ? $data['location_title'] : '',
	'content'    => $infowindow_message,
	'location'   => array(
		'icon'                    => ( $category_group_marker ),
		'lat'                     => ( isset( $data['location_latitude'] ) and ! empty( $data['location_latitude'] ) ) ? $data['location_latitude'] : '40.6153983',
		'lng'                     => ( isset( $data['location_longitude'] ) and ! empty( $data['location_longitude'] ) ) ? $data['location_longitude'] : '-74.2535216',
		'draggable'               => true,
		'infowindow_default_open' => ( isset( $data['location_infowindow_default_open'] ) and ! empty( $data['location_infowindow_default_open'] ) ) ? $data['location_infowindow_default_open'] : '',
		'animation'               => ( isset( $data['location_animation'] ) and ! empty( $data['location_animation'] ) ) ? $data['location_animation'] : '',
		'infowindow_disable'      => ( isset( $infowindow_disable['hide_infowindow'] ) && 'false' === $infowindow_disable['hide_infowindow'] ),
	),
	'categories' => array(
		array(
			'id'   => isset( $category->group_map_id ) ? $category->group_map_id : '',
			'name' => isset( $category->group_map_title ) ? $category->group_map_title : '',
			'type' => 'category',
			'icon' => $category_group_marker,
		),
	),
);
$map_data['page']        = 'edit_location';

$map_data['map_options'] = apply_filters( 'wpomp_add_location_center_lat_long',$map_data['map_options'] );

$map_data['places'] = apply_filters( 'wpomp_add_marker_center_lat_long', $map_data['places']);

$form->render();

?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	var map = $("#wpomp_map").osm_maps(<?php echo wp_json_encode( $map_data ); ?>).data('wpomp_maps');
});
</script>
