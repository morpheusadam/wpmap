<?php
/**
 * Contro Positioning over leaflet maps.
 *
 * @package wp-leaflet-maps-pro
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element(
	'group', 'map_elements_setting', array(
		'value'  => esc_html__( 'Custom Filters', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox_toggle', 'map_all_control[wpomp_display_custom_filters]', array(
		'label'   => esc_html__( 'Display Custom Filters', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_wpomp_display_custom_filters',
		'current' => isset( $data['map_all_control']['wpomp_display_custom_filters'] ) ? $data['map_all_control']['wpomp_display_custom_filters'] : '',
		'desc'    => esc_html__( 'Check to enable custom filters for extra fields, custom fields & taxonomies.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class switch_onoff ',
		'data'    => array( 'target' => '.wpomp_custom_filters' ),
	)
);
$form->set_col( 3 );
$next_index = 0;
if ( isset( $data['map_all_control']['custom_filters'] ) && isset( $data['map_all_control']['wpomp_display_custom_filters'] ) && $data['map_all_control']['wpomp_display_custom_filters'] == true ) {
	$ex = 0;
	foreach ( $data['map_all_control']['custom_filters'] as $i => $label ) {
		$form->add_element(
			'text', 'map_all_control[custom_filters][' . $ex . '][slug]', array(
				'value'       => ( isset( $data['map_all_control']['custom_filters'][ $i ]['slug'] ) and ! empty( $data['map_all_control']['custom_filters'][ $i ]['slug'] ) ) ? $data['map_all_control']['custom_filters'][ $i ]['slug'] : '',
				'desc'        => esc_html__( 'Enter placeholder for marker taxonomies, extra fields or custom fields as {%custom_field_slug_here%}, {extra_field_slug_here}, {%taxonomy%}, e.g: {color}.', 'wp-leaflet-maps-pro' ),
				'class'       => 'wpomp_custom_filters form-control sortable_child',
				'placeholder' => esc_html__( 'Enter placeholder', 'wp-leaflet-maps-pro' ),
				'before'      => '<div class="fc-4">',
				'after'       => '</div>',
				'show'        => 'false',
				'lable'       => '&nbsp;',
			)
		);
		$form->add_element(
			'text', 'map_all_control[custom_filters][' . $ex . '][text]', array(
				'value'       => ( isset( $data['map_all_control']['custom_filters'][ $i ]['text'] ) and ! empty( $data['map_all_control']['custom_filters'][ $i ]['text'] ) ) ? $data['map_all_control']['custom_filters'][ $i ]['text'] : '',
				'desc'        => esc_html__( 'Enter text here for the filter to be shown, e.g: Select Colors.', 'wp-leaflet-maps-pro' ),
				'class'       => 'wpomp_custom_filters form-control',
				'placeholder' => esc_html__( 'Enter filter text', 'wp-leaflet-maps-pro' ),
				'before'      => '<div class="fc-3">',
				'after'       => '</div>',
				'show'        => 'false',
			)
		);
		$form->add_element(
			'button', 'custom_filters_add_btn[' . $ex . ']', array(
				'value'  => esc_html__( 'Remove', 'wp-leaflet-maps-pro' ),
				'desc'   => '',
				'class'  => 'repeat_remove_button fc-btn fc-btn-blue btn-sm wpomp_custom_filters',
				'before' => '<div class="fc-2">',
				'after'  => '</div>',
				'show'   => 'false',
			)
		);
		$ex++;
	}
	$next_index = $ex;
}

$form->add_element(
	'text', 'map_all_control[custom_filters][' . $next_index . '][slug]', array(
		'value'       => ( isset( $data['map_all_control']['custom_filters'][ $next_index ]['slug'] ) and ! empty( $data['map_all_control']['custom_filters'][ $next_index ]['slug'] ) ) ? $data['map_all_control']['custom_filters'][ $next_index ]['slug'] : '',
		'desc'        => esc_html__( 'Enter placeholder here for marker taxonomies, extra fields or custom fields as {%custom_field_slug_here%}, {extra_field_slug_here}, {%taxonomy%}, e.g: {color}.', 'wp-leaflet-maps-pro' ),
		'class'       => 'wpomp_custom_filters form-control sortable_child',
		'placeholder' => esc_html__( 'Enter placeholder', 'wp-leaflet-maps-pro' ),
		'before'      => '<div class="fc-4">',
		'after'       => '</div>',
		'show'        => 'false',
		'lable'       => '&nbsp;',
	)
);

$form->add_element(
	'text', 'map_all_control[custom_filters][' . $next_index . '][text]', array(
		'value'       => ( isset( $data['map_all_control']['custom_filters'][ $next_index ]['text'] ) and ! empty( $data['map_all_control']['custom_filters'][ $next_index ]['text'] ) ) ? $data['map_all_control']['custom_filters'][ $next_index ]['text'] : '',
		'desc'        => esc_html__( 'Enter text here for the filter to be shown, e,g, : Select Colors.', 'wp-leaflet-maps-pro' ),
		'class'       => 'wpomp_custom_filters form-control',
		'placeholder' => esc_html__( 'Enter filter text', 'wp-leaflet-maps-pro' ),
		'before'      => '<div class="fc-3">',
		'after'       => '</div>',
		'show'        => 'false',
	)
);

$form->add_element(
	'button', 'custom_filters_add_btn[' . $next_index . ']', array(
		'value'  => esc_html__( 'Add More...', 'wp-leaflet-maps-pro' ),
		'desc'   => '',
		'class'  => 'repeat_button fc-btn fc-btn-blue btn-sm wpomp_custom_filters',
		'before' => '<div class="fc-2">',
		'after'  => '</div>',
		'show'   => 'false',
	)
);

$form->set_col( 1 );


$form->add_element(
	'group', 'custom_filters_bound', array(
		'value'  => esc_html__( 'Advance Custom Filters Functionality', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[bound_map_after_filter]', array(
		'label'   => esc_html__( 'Fitbound Map After Filteration', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['bound_map_after_filter'] ) ? $data['map_all_control']['bound_map_after_filter'] : '',
		'desc'    => esc_html__( 'Fit bound the map with resultant markers after filteration process', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',

	)
);

$form->add_element(
	'checkbox', 'map_all_control[display_reset_button]', array(
		'label'   => esc_html__( 'Display Reset Map Button', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['display_reset_button'] ) ? $data['map_all_control']['display_reset_button'] : '',
		'desc'    => esc_html__( 'Check to enable display reset map button on frontend.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.map_reset_button_text' ),
	)
);

$form->add_element(
	'text', 'map_all_control[map_reset_button_text]', array(
		'label'       => esc_html__( 'Reset Map Button Text', 'wp-leaflet-maps-pro' ),
		'value'       => ( isset( $data['map_all_control']['map_reset_button_text'] ) and ! empty( $data['map_all_control']['map_reset_button_text'] ) ) ? $data['map_all_control']['map_reset_button_text'] : esc_html__( 'Reset', 'wp-leaflet-maps-pro' ),
		'desc'        => esc_html__( 'Enter text to be displayed on Reset Map Button', 'wp-leaflet-maps-pro' ),
		'class'       => 'form-control map_reset_button_text',
		'placeholder' => esc_html__( 'Enter Reset Map Text', 'wp-leaflet-maps-pro' ),
		'show'        => 'false',
	)
);

$form->add_element(
	'group', 'map_listing_setting', array(
		'value'  => esc_html__( 'Listing Settings', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[display_listing]', array(
		'label'   => esc_html__( 'Display Listing', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_display_listing',
		'current' => isset( $data['map_all_control']['display_listing'] ) ? $data['map_all_control']['display_listing'] : '',
		'desc'    => esc_html__( 'Display locations listing below the map.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.wpomp_display_listing' ),
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpomp_search_display]', array(
		'label'   => esc_html__( 'Display Search Form', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_wpomp_search_display',
		'current' => isset( $data['map_all_control']['wpomp_search_display'] ) ? $data['map_all_control']['wpomp_search_display'] : '',
		'desc'    => esc_html__( 'Check to display search form below the map.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class wpomp_display_listing switch_onoff',
		'show'    => 'false',
		'data'    => array( 'target' => '.wpomp_search_display' ),

	)
);

$form->add_element(
	'checkbox', 'map_all_control[search_field_autosuggest]', array(
		'label'   => esc_html__( 'Enable Autosuggest', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['search_field_autosuggest'] ) ? $data['map_all_control']['search_field_autosuggest'] : '',
		'desc'    => esc_html__( 'Apply autosuggest on search field.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class wpomp_display_listing wpomp_search_display',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpomp_display_category_filter]', array(
		'label'   => esc_html__( 'Display Category Filter', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_display_category_filter',
		'current' => isset( $data['map_all_control']['wpomp_display_category_filter'] ) ? $data['map_all_control']['wpomp_display_category_filter'] : '',
		'desc'    => esc_html__( 'Check to display category filter.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class wpomp_display_listing',
		'show'    => 'false',
	)
);


$form->add_element(
	'checkbox', 'map_all_control[wpomp_display_sorting_filter]', array(
		'label'   => esc_html__( 'Display Sorting Filter', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_wpomp_display_sorting_filter',
		'current' => isset( $data['map_all_control']['wpomp_display_sorting_filter'] ) ? $data['map_all_control']['wpomp_display_sorting_filter'] : '',
		'desc'    => esc_html__( 'Check to display sorting filter.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class wpomp_display_listing',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpomp_display_radius_filter]', array(
		'label'   => esc_html__( 'Display Radius Filter', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_display_radius_filter',
		'current' => isset( $data['map_all_control']['wpomp_display_radius_filter'] ) ? $data['map_all_control']['wpomp_display_radius_filter'] : '',
		'desc'    => esc_html__( 'Check to display radius filter. Recommended to display search results within certian radius.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class wpomp_display_listing switch_onoff',
		'show'    => 'false',
		'data'    => array( 'target' => '.wpomp_radius_filter' ),
	)
);

$dimension_options = array(
	'miles' => esc_html__( 'Miles', 'wp-leaflet-maps-pro' ),
	'km'    => esc_html__( 'KM', 'wp-leaflet-maps-pro' ),
);
$form->add_element(
	'select', 'map_all_control[wpomp_radius_dimension]', array(
		'label'   => esc_html__( 'Dimension', 'wp-leaflet-maps-pro' ),
		'current' => isset( $data['map_all_control']['wpomp_radius_dimension'] ) ? $data['map_all_control']['wpomp_radius_dimension'] : '',
		'desc'    => esc_html__( 'Choose radius dimension in miles or km.', 'wp-leaflet-maps-pro' ),
		'options' => $dimension_options,
		'class'   => 'form-control  wpomp_radius_filter',
		'show'    => 'false',
	)
);

$form->add_element(
	'text', 'map_all_control[wpomp_radius_options]', array(
		'label'         => esc_html__( 'Radius Options', 'wp-leaflet-maps-pro' ),
		'value'         => isset( $data['map_all_control']['wpomp_radius_options'] ) ? $data['map_all_control']['wpomp_radius_options'] : '',
		'desc'          => esc_html__( 'Set radius options. Enter comma seperated numbers.', 'wp-leaflet-maps-pro' ),
		'class'         => 'form-control  wpomp_radius_filter',
		'show'          => 'false',
		'default_value' => '5,10,15,20,25,50,100,200,500',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpomp_display_location_per_page_filter]', array(
		'label'   => esc_html__( 'Display Per Page Filter', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_wpomp_display_location_per_page_filter',
		'current' => isset( $data['map_all_control']['wpomp_display_location_per_page_filter'] ) ? $data['map_all_control']['wpomp_display_location_per_page_filter'] : '',
		'desc'    => esc_html__( 'Check to enable locations per page filter.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class wpomp_display_listing',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpomp_display_grid_option]', array(
		'label'   => esc_html__( 'Display Grid/List Option', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_display_grid_option',
		'current' => isset( $data['map_all_control']['wpomp_display_grid_option'] ) ? $data['map_all_control']['wpomp_display_grid_option'] : '',
		'desc'    => esc_html__( 'Switch between list/grid view.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class wpomp_display_listing',
		'show'    => 'false',
	)
);

$form->add_element(
	'text', 'map_all_control[wpomp_listing_number]', array(
		'label'         => esc_html__( 'Locations Per Page', 'wp-leaflet-maps-pro' ),
		'value'         => isset( $data['map_all_control']['wpomp_listing_number'] ) ? $data['map_all_control']['wpomp_listing_number'] : '',
		'desc'          => esc_html__( 'Set locations to display per page. Default is 10.', 'wp-leaflet-maps-pro' ),
		'class'         => 'form-control wpomp_display_listing',
		'show'          => 'false',
		'default_value' => 10,
	)
);


$form->add_element(
	'textarea', 'map_all_control[wpomp_before_listing]', array(
		'label'         => esc_html__( 'Before Listing Placeholder', 'wp-leaflet-maps-pro' ),
		'value'         => ( isset( $data['map_all_control']['wpomp_before_listing']) && !empty($data['map_all_control']['wpomp_before_listing']) ) ? $data['map_all_control']['wpomp_before_listing'] : esc_html__( 'Locations Listing', 'wp-leaflet-maps-pro' ),
		'desc'          => esc_html__( 'Display a text/html content before display listing.', 'wp-leaflet-maps-pro' ),
		'textarea_rows' => 10,
		'textarea_name' => 'map_all_control[wpomp_before_listing]',
		'class'         => 'form-control wpomp_display_listing',
		'show'          => 'false',
		'default_value' => esc_html__( 'Map Locations', 'wp-leaflet-maps-pro' ),
	)
);

$list_grid = array(
	'wpomp_listing_list' => 'List',
	'wpomp_listing_grid' => 'Grid',
);
$form->add_element(
	'select', 'map_all_control[wpomp_list_grid]', array(
		'label'   => esc_html__( 'List/Grid', 'wp-leaflet-maps-pro' ),
		'current' => isset( $data['map_all_control']['wpomp_list_grid'] ) ? $data['map_all_control']['wpomp_list_grid'] : '',
		'desc'    => esc_html__( 'Choose listing style for frontend display.', 'wp-leaflet-maps-pro' ),
		'options' => $list_grid,
		'class'   => 'form-control wpomp_display_listing',
		'show'    => 'false',
	)
);

$default_place_holder = '
<div class="wpomp_locations">
<div class="wpomp_locations_head">
<div class="wpomp_location_title">
<a href="" class="place_title" data-zoom="{marker_zoom}" data-marker="{marker_id}">{marker_title}</a>
</div>
<div class="wpomp_location_meta">
<span class="wpomp_location_category fc-badge info">{marker_category}</span>
</div>
</div>
<div class="wpomp_locations_content">
{marker_message}
</div>
<div class="wpomp_locations_foot"></div>
</div>';
$listing_place_holder = stripslashes( trim( $default_place_holder ) );
$listing_place_holder = ( isset( $data['map_all_control']['wpomp_categorydisplayformat'] ) ? $data['map_all_control']['wpomp_categorydisplayformat'] : $listing_place_holder );

$form->add_element(
	'select', 'map_all_control[wpomp_categorydisplaysort]', array(
		'label'   => esc_html__( 'Sort By', 'wp-leaflet-maps-pro' ),
		'current' => isset( $data['map_all_control']['wpomp_categorydisplaysort'] ) ? $data['map_all_control']['wpomp_categorydisplaysort'] : '',
		'desc'    => esc_html__( 'Select Sort By.', 'wp-leaflet-maps-pro' ),
		'options' => array(
			'title'     => esc_html__( 'Title', 'wp-leaflet-maps-pro' ),
			'address'   => esc_html__( 'Address', 'wp-leaflet-maps-pro' ),
			'category'  => esc_html__( 'Category', 'wp-leaflet-maps-pro' ),
			'listorder' => esc_html__( 'Category Priority', 'wp-leaflet-maps-pro' ),
		),
		'class'   => 'form-control wpomp_display_listing',
		'show'    => 'false',
	)
);


$form->add_element(
	'select', 'map_all_control[wpomp_categorydisplaysortby]', array(
		'label'         => esc_html__( 'Sort Order', 'wp-leaflet-maps-pro' ),
		'current'       => isset( $data['map_all_control']['wpomp_categorydisplaysortby'] ) ? $data['map_all_control']['wpomp_categorydisplaysortby'] : '',
		'desc'          => esc_html__( 'Select sorting order.', 'wp-leaflet-maps-pro' ),
		'options'       => array(
			'asc'  => esc_html__( 'Ascending', 'wp-leaflet-maps-pro' ),
			'desc' => esc_html__( 'Descending', 'wp-leaflet-maps-pro' ),
		),
		'class'         => 'form-control wpomp_display_listing',
		'show'          => 'false',
		'default_value' => 'asc',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpomp_apply_radius_only]', array(
		'label'   => esc_html__( 'Apply Default Radius Filter', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['wpomp_apply_radius_only'] ) ? $data['map_all_control']['wpomp_apply_radius_only'] : '',
		'desc'    => esc_html__( 'Show markers available in certain radius based on user search.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class wpomp_display_listing switch_onoff',
		'show'    => 'false',
		'data'    => array( 'target' => '.wpomp_radius_filter_apply' ),
	)
);

$form->add_element(
	'text', 'map_all_control[wpomp_default_radius]', array(
		'label'         => esc_html__( 'Default Radius', 'wp-leaflet-maps-pro' ),
		'value'         => isset( $data['map_all_control']['wpomp_default_radius'] ) ? $data['map_all_control']['wpomp_default_radius'] : '',
		'desc'          => esc_html__( 'Set default radius options.', 'wp-leaflet-maps-pro' ),
		'class'         => 'form-control wpomp_radius_filter_apply',
		'show'          => 'false',
		'default_value' => '100',
	)
);

$dimension_options = array(
	'miles' => esc_html__( 'Miles', 'wp-leaflet-maps-pro' ),
	'km'    => esc_html__( 'KM', 'wp-leaflet-maps-pro' ),
);
$form->add_element(
	'select', 'map_all_control[wpomp_default_radius_dimension]', array(
		'label'   => esc_html__( 'Dimension', 'wp-leaflet-maps-pro' ),
		'current' => isset( $data['map_all_control']['wpomp_default_radius_dimension'] ) ? $data['map_all_control']['wpomp_default_radius_dimension'] : '',
		'desc'    => esc_html__( 'Choose default radius dimension in miles or km.', 'wp-leaflet-maps-pro' ),
		'options' => $dimension_options,
		'class'   => 'form-control  wpomp_radius_filter_apply',
		'show'    => 'false',
	)
);


$location_placeholders = array(
	'{marker_id}',
	'{marker_title}',
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
	'{marker_image}',
	'{marker_postal_code}',
	'{extra_field_slug}',
	'{post_title}',
	'{post_link}',
	'{post_excerpt}',
	'{post_content}',
	'{post_categories}',
	'{post_tags}',
	'{%custom_field_slug_here%}',
);

$form->add_element(
	'templates', 'map_all_control[item_skin]', array(
		'label' => esc_html__( 'Listing Item Skin', 'wp-leaflet-maps-pro' ),
		'template_types'      => 'item',
		'data_placeholders'   => $location_placeholders,
		'templatePath'        => WPLMP_TemplateS,
		'templateURL'         => WPLMP_TemplateS_URL,
		'customiser'          => 'true',
		'show'                => 'false',
		'current'             => ( isset( $data['map_all_control']['item_skin'] ) ) ? $data['map_all_control']['item_skin'] : array(
			'name'       => 'default',
			'type'       => 'item',
			'sourcecode' => $listing_place_holder,
		),
		'customiser_controls' => array( 'edit_mode', 'placeholder', 'sourcecode', 'mobile', 'desktop', 'grid' ),
	)
);

$form->add_element(
	'group', 'map_filters_setting', array(
		'value'  => esc_html__( 'Map Filters Settings', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$filters_position = array(
	'default' => esc_html__( 'Bottom of the Map', 'wp-leaflet-maps-pro' ),
	'top_map' => esc_html__( 'Top of the Map', 'wp-leaflet-maps-pro' ),
);
$form->add_element(
	'select', 'map_all_control[filters_position]', array(
		'label'   => esc_html__( 'Filters Position', 'wp-leaflet-maps-pro' ),
		'current' => isset( $data['map_all_control']['filters_position'] ) ? $data['map_all_control']['filters_position'] : '',
		'desc'    => esc_html__( 'Choose filters position. Default is below the map.', 'wp-leaflet-maps-pro' ),
		'options' => $filters_position,
		'class'   => 'form-control',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[hide_locations]', array(
		'label'   => esc_html__( 'Show Filters Only', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['hide_locations'] ) ? $data['map_all_control']['hide_locations'] : '',
		'desc'    => esc_html__( 'Check to display filters only.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[hide_map]', array(
		'label'   => esc_html__( "Don't Show Maps", 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['hide_map'] ) ? $data['map_all_control']['hide_map'] : '',
		'desc'    => esc_html__( 'Check to display filters & locations only. Maps will be invisible.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);

