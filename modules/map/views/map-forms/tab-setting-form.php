<?php
/**
 * Display Tabs over leaflet maps.
 *
 * @package wp-leaflet-maps-pro
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element(
	'group', 'map_tabs_setting', array(
		'value'  => esc_html__( 'Tabs Settings', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[display_marker_category]', array(
		'label'   => esc_html__( 'Display Tabs', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_display_marker_category',
		'current' => isset( $data['map_all_control']['display_marker_category'] ) ? $data['map_all_control']['display_marker_category'] : '',
		'desc'    => esc_html__( 'Display various tabs on the map.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.map_tabs_setting' ),
	)
);

$form->add_element(
	'checkbox', 'map_all_control[hide_tabs_default]', array(
		'label'   => esc_html__( 'Hide Tabs on Load', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_hide_tabs_default',
		'current' => isset( $data['map_all_control']['hide_tabs_default'] ) ? $data['map_all_control']['hide_tabs_default'] : '',
		'desc'    => esc_html__( 'Hide tabs by default.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpomp_category_tab]', array(
		'label'   => esc_html__( 'Display Categories Tab', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_wpomp_category_tab',
		'current' => isset( $data['map_all_control']['wpomp_category_tab'] ) ? $data['map_all_control']['wpomp_category_tab'] : '',
		'desc'    => esc_html__( 'Display Categories/Locations Tab.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class map_tabs_setting switch_onoff',
		'show'    => 'false',
		'data'    => array( 'target' => '.wpomp_category_tab_setting' ),

	)
);

$form->add_element(
	'text', 'map_all_control[wpomp_category_tab_title]', array(
		'label'         => esc_html__( 'Category Tab Title', 'wp-leaflet-maps-pro' ),
		'value'         => isset( $data['map_all_control']['wpomp_category_tab_title'] ) ? $data['map_all_control']['wpomp_category_tab_title'] : '',
		'id'            => 'wpomp_category_tab_title',
		'desc'          => esc_html__( 'Title of the category tab.', 'wp-leaflet-maps-pro' ),
		'class'         => 'form-control wpomp_category_tab_setting',
		'show'          => 'false',
		'default_value' => esc_html__( 'Categories', 'wp-leaflet-maps-pro' ),
	)
);

$form->add_element(
	'select', 'map_all_control[wpomp_category_order]', array(
		'label'   => esc_html__( 'Sort Category By', 'wp-leaflet-maps-pro' ),
		'current' => isset( $data['map_all_control']['wpomp_category_order'] ) ? $data['map_all_control']['wpomp_category_order'] : '',
		'desc'    => esc_html__( 'Select Sort Criteria For Categories Tab.', 'wp-leaflet-maps-pro' ),
		'options' => array(
			'title'    => esc_html__( 'Title', 'wp-leaflet-maps-pro' ),
			'count'    => esc_html__( 'Location Count.', 'wp-leaflet-maps-pro' ),
			'category' => esc_html__( 'Category Order', 'wp-leaflet-maps-pro' ),
		),
		'class'   => 'form-control wpomp_category_tab_setting',
		'show'    => 'false',
		'before'  => '<div class="fc-4">',
		'after'   => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpomp_category_tab_show_count]', array(
		'label'   => esc_html__( 'Show Location Count', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_category_tab_show_count',
		'current' => isset( $data['map_all_control']['wpomp_category_tab_show_count'] ) ? $data['map_all_control']['wpomp_category_tab_show_count'] : '',
		'desc'    => esc_html__( 'Display location count next to category name.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class wpomp_category_tab_setting',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpomp_category_tab_hide_location]', array(
		'label'   => esc_html__( 'Hide Locations', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_category_tab_hide_location',
		'current' => isset( $data['map_all_control']['wpomp_category_tab_hide_location'] ) ? $data['map_all_control']['wpomp_category_tab_hide_location'] : '',
		'desc'    => esc_html__( 'Hide locations below category selection.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class wpomp_category_tab_setting',
		'show'    => 'false',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[wpomp_category_tab_show_all]', array(
		'label'   => esc_html__( 'Select All', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'id'      => 'wpomp_category_tab_show_all',
		'current' => isset( $data['map_all_control']['wpomp_category_tab_show_all'] ) ? $data['map_all_control']['wpomp_category_tab_show_all'] : '',
		'desc'    => esc_html__( 'Display select all checkbox to select all categories at once.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class wpomp_category_tab_setting',
		'show'    => 'false',
	)
);

$form->set_col( 1 );
