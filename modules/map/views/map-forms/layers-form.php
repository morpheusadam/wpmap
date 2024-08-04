<?php

/**

 * Contro Positioning over leaflet maps.

 *

 * @package wp-leaflet-maps-pro

 * @author Flipper Code <hello@flippercode.com>

 */



$form->add_element(

	'group', 'map_control_settings', array(

		'value'  => esc_html__( 'Infowindow Settings', 'wp-leaflet-maps-pro' ),

		'before' => '<div class="fc-12">',

		'after'  => '</div>',

	)

);

$url  = admin_url( 'admin.php?page=wpomp_how_overview' );

$link = sprintf(

	wp_kses(

		esc_html__( 'Enter placeholders {marker_title},{marker_address},{marker_message},{marker_image},{marker_latitude},{marker_longitude}, {extra_field_slug_here}. View complete list <a target="_blank" href="%s">here</a>.', 'wp-leaflet-maps-pro' ), array(

			'a' => array(

				'href'   => array(),

				'target' => '_blank',

			),

		)

	), esc_url( $url )

);



$form->add_element(

	'checkbox', 'map_all_control[infowindow_filter_only]', array(

		'label'   => esc_html__( 'Hide Markers on Page Load', 'wp-leaflet-maps-pro' ),

		'value'   => 'true',

		'id'      => 'infowindow_default_open',

		'current' => isset( $data['map_all_control']['infowindow_filter_only'] ) ? $data['map_all_control']['infowindow_filter_only'] : '',

		'desc'    => esc_html__( "Don't display markers on page load. Display markers after filtration only.", 'wp-leaflet-maps-pro' ),

		'class'   => 'chkbox_class',

	)

);



$info_default_value = '<div class="fc-item-box fc-itemcontent-padding ">
    <div class="fc-item-title">{marker_title} <span class="fc-badge info">{marker_category}</span></div>
    <div class="fc-item-content fc-item-body-text-color">
        <div class="fc-item-featured fc-left fc-item-top_space">
            {marker_image}
        </div>
        {marker_message}
    </div>
    <address class="fc-text">{marker_address}</address>
</div>';



$info_default_value = ( isset( $data['map_all_control']['infowindow_setting'] ) and '' != $data['map_all_control']['infowindow_setting'] ) ? $data['map_all_control']['infowindow_setting'] : $info_default_value;



$default_value = '<div class="fc-item-box fc-itemcontent-padding ">
    <div class="fc-item-title">{marker_title} <span class="fc-badge info">{marker_category}</span></div>
    <div class="fc-item-content fc-item-body-text-color">
        <div class="fc-item-featured fc-left fc-item-top_space">
            {marker_image}
        </div>
        {marker_message}
    </div>
    <address class="fc-text">{marker_address}</address>
</div>';

$default_value = ( isset( $data['map_all_control']['infowindow_geotags_setting'] ) and '' != $data['map_all_control']['infowindow_geotags_setting'] ) ? $data['map_all_control']['infowindow_geotags_setting'] : $default_value;



if ( isset( $data['map_all_control']['infowindow_openoption'] ) && 'mouseclick' == $data['map_all_control']['infowindow_openoption'] ) {

	$data['map_all_control']['infowindow_openoption'] = 'click'; } elseif ( isset( $data['map_all_control']['infowindow_openoption'] ) && 'mousehover' == $data['map_all_control']['infowindow_openoption'] ) {

	$data['map_all_control']['infowindow_openoption'] = 'mouseover'; }

	$event = array(

		'click'     => 'Mouse Click',

		'mouseover' => 'Mouse Hover',

	);

	$form->add_element(

		'select', 'map_all_control[infowindow_openoption]', array(

			'label'   => esc_html__( 'Show Infowindow on', 'wp-leaflet-maps-pro' ),

			'current' => isset( $data['map_all_control']['infowindow_openoption'] ) ? $data['map_all_control']['infowindow_openoption'] : '',

			'desc'    => esc_html__( 'Open infowindow on Mouse Click or Mouse Hover.', 'wp-leaflet-maps-pro' ),

			'options' => $event,

		)

	);



	$form->add_element(

		'image_picker', 'map_all_control[marker_default_icon]', array(

			'label'         => esc_html__( 'Choose Marker Image', 'wp-leaflet-maps-pro' ),

			'src'           => ( isset( $data['map_all_control']['marker_default_icon'] ) ? wp_unslash( $data['map_all_control']['marker_default_icon'] ) : WPLMP_IMAGES . '/default_marker.png' ),

			'required'      => false,

			'choose_button' => esc_html__( 'Choose', 'wp-leaflet-maps-pro' ),

			'remove_button' => esc_html__( 'Remove', 'wp-leaflet-maps-pro' ),

			'id'            => 'marker_category_icon',

		)

	);



	$form->add_element(

		'checkbox', 'map_all_control[infowindow_open]', array(

			'label'   => esc_html__( 'InfoWindow Open', 'wp-leaflet-maps-pro' ),

			'value'   => 'true',

			'id'      => 'wpomp_infowindow_open',

			'current' => isset( $data['map_all_control']['infowindow_open'] ) ? $data['map_all_control']['infowindow_open'] : '',

			'desc'    => esc_html__( 'Please check to enable infowindow default open.', 'wp-leaflet-maps-pro' ),

			'class'   => 'chkbox_class',

		)

	);



	$form->add_element(

		'checkbox', 'map_all_control[infowindow_close]', array(

			'label'   => esc_html__( 'Close InfoWindow', 'wp-leaflet-maps-pro' ),

			'value'   => 'true',

			'id'      => 'wpomp_infowindow_close',

			'current' => isset( $data['map_all_control']['infowindow_close'] ) ? $data['map_all_control']['infowindow_close'] : '',

			'desc'    => esc_html__( 'Please check to close infowindow on map click.', 'wp-leaflet-maps-pro' ),

			'class'   => 'chkbox_class',

		)

	);





	$zoom_level     = array();

	$zoom_level[''] = esc_html__( 'Select Zoom', 'wp-leaflet-maps-pro' );

	for ( $i = 1; $i < 20; $i++ ) {

		$zoom_level[ $i ] = $i;

	}



	$form->add_element(

		'select', 'map_all_control[infowindow_zoomlevel]', array(

			'label'   => esc_html__( 'Change Zoom on Click', 'wp-leaflet-maps-pro' ),

			'current' => isset( $data['map_all_control']['infowindow_zoomlevel'] ) ? $data['map_all_control']['infowindow_zoomlevel'] : '',

			'desc'    => esc_html__( 'Change zoom level of the map on marker click.', 'wp-leaflet-maps-pro' ),

			'options' => $zoom_level,

			'before'  => '<div class="fc-3">',

			'after'   => '</div>',

		)

	);



	$form->add_element(

		'checkbox', 'map_all_control[infowindow_iscenter]', array(

			'label'   => esc_html__( 'Center the Map', 'wp-leaflet-maps-pro' ),

			'value'   => 'true',

			'current' => isset( $data['map_all_control']['infowindow_iscenter'] ) ? $data['map_all_control']['infowindow_iscenter'] : '',

			'desc'    => esc_html__( 'Set as center point on marker click', 'wp-leaflet-maps-pro' ),

			'class'   => 'chkbox_class',

		)

	);



	$form->add_element(

		'group', 'map_infowindow_settings', array(

			'value'  => esc_html__( 'Infowindow Customization Settings', 'wp-leaflet-maps-pro' ),

			'before' => '<div class="fc-12">',

			'after'  => '</div>',

		)

	);



	$form->add_element(

		'checkbox', 'map_all_control[map_infowindow_customisations]', array(

			'label'   => esc_html__( 'Turn On Infowindow Customization', 'wp-leaflet-maps-pro' ),

			'value'   => 'true',

			'id'      => 'map_infowindow_customisations',

			'current' => isset( $data['map_all_control']['map_infowindow_customisations'] ) ? $data['map_all_control']['map_infowindow_customisations'] : '',

			'desc'    => esc_html__( 'Please check to enable infowindow customization.', 'wp-leaflet-maps-pro' ),

			'class'   => 'switch_onoff chkbox_class',

			'data'    => array( 'target' => '.map_iw_customisations' ),

		)

	);



	$form->add_element(

		'text', 'map_all_control[infowindow_width]', array(

			'label'         => esc_html__( 'Width', 'wp-leaflet-maps-pro' ),

			'value'         => isset( $data['map_all_control']['infowindow_width'] ) ? $data['map_all_control']['infowindow_width'] : '',

			'class'         => 'form-control map_iw_customisations',

			'desc'          => esc_html__( 'Enter infowindow width in px. Leave blank for default settings.', 'wp-leaflet-maps-pro' ),

			'show'          => 'false',

			'default_value' => '',

		)

	);



	$form->add_element(

		'text', 'map_all_control[infowindow_border_color]', array(

			'label'         => esc_html__( 'Border Color', 'wp-leaflet-maps-pro' ),

			'value'         => isset( $data['map_all_control']['infowindow_border_color'] ) ? $data['map_all_control']['infowindow_border_color'] : '',

			'class'         => 'color {pickerClosable:true} form-control map_iw_customisations',

			'desc'          => esc_html__( 'Choose color for the border of infowindow. Leave blank for default settings.', 'wp-leaflet-maps-pro' ),

			'show'          => 'false',

			'default_value' => '',

		)

	);



	$form->add_element(

		'text', 'map_all_control[infowindow_border_radius]', array(

			'label'         => esc_html__( 'Border Radius', 'wp-leaflet-maps-pro' ),

			'value'         => isset( $data['map_all_control']['infowindow_border_radius'] ) ? $data['map_all_control']['infowindow_border_radius'] : '',

			'class'         => 'form-control map_iw_customisations',

			'desc'          => esc_html__( 'Enter border radius in px for the infowindow. Leave blank for default settings.', 'wp-leaflet-maps-pro' ),

			'show'          => 'false',

			'default_value' => '',

		)

	);



	$form->add_element(

		'text', 'map_all_control[infowindow_bg_color]', array(

			'label'         => esc_html__( 'Background Color', 'wp-leaflet-maps-pro' ),

			'value'         => isset( $data['map_all_control']['infowindow_bg_color'] ) ? $data['map_all_control']['infowindow_bg_color'] : '',

			'class'         => 'color {pickerClosable:true} form-control map_iw_customisations',

			'desc'          => esc_html__( 'Choose color for the background of infowindow text. Leave blank for default settings.', 'wp-leaflet-maps-pro' ),

			'show'          => 'false',

			'default_value' => '',

		)

	);


	$location_placeholders = array(

		'{marker_id}',

		'{marker_title}',

		'{marker_image}',

		'{marker_address}',

		'{marker_message}',

		'{marker_category}',

		'{marker_icon}',

		'{marker_latitude}',

		'{marker_longitude}',

		'{marker_city}',

		'{marker_state}',

		'{marker_country}',

		'{marker_zoom}',

		'{marker_postal_code}',

		'{extra_field_slug}',

	);

	$form->add_element(

		'templates', 'map_all_control[location_infowindow_skin]', array(

			'label' => esc_html__( 'Infowindow Message for Locations', 'wp-leaflet-maps-pro' ),

			'template_types'      => 'infowindow',

			'templatePath'        => WPLMP_TemplateS,

			'templateURL'         => WPLMP_TemplateS_URL,

			'data_placeholders'   => $location_placeholders,

			'customiser'          => 'true',

			'current'             => ( isset( $data['map_all_control']['location_infowindow_skin'] ) ) ? $data['map_all_control']['location_infowindow_skin'] : array(

				'name'       => 'default',

				'type'       => 'infowindow',

				'sourcecode' => $info_default_value,

			),

			'customiser_controls' => array( 'edit_mode', 'placeholder', 'sourcecode' ),

		)

	);


	$post_placeholders = array(

		'{post_title}',

		'{post_link}',

		'{post_excerpt}',

		'{post_content}',

		'{post_featured_image}',

		'{post_categories}',

		'{post_tags}',

		'{%custom_field_slug_here%}'	);

	$form->add_element(

		'templates', 'map_all_control[post_infowindow_skin]', array(

			'label' => esc_html__( 'Infowindow Message for Posts', 'wp-leaflet-maps-pro' ),

			'template_types'      => 'post',

			'data_placeholders'   => $post_placeholders,

			'templatePath'        => WPLMP_TemplateS,

			'templateURL'         => WPLMP_TemplateS_URL,

			'customiser'          => 'true',

			'current'             => ( isset( $data['map_all_control']['post_infowindow_skin'] ) ) ? $data['map_all_control']['post_infowindow_skin'] : array(

				'name'       => 'default',

				'type'       => 'post',

				'sourcecode' => $default_value,

			),

			'customiser_controls' => array( 'edit_mode', 'placeholder', 'sourcecode' ),

		)

	);