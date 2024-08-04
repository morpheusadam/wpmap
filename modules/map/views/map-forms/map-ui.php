<?php
/**
 * Contro Positioning over leaflet maps.
 *
 * @package wp-leaflet-maps-pro
 * @author Flipper Code <hello@flippercode.com>
 */


$form->add_element(
	'group', 'map_ui_setting', array(
		'value'  => esc_html__( 'Design Settings', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[apply_custom_design]', array(
		'label'   => esc_html__( 'Apply Custom Design', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['apply_custom_design'] ) ? $data['map_all_control']['apply_custom_design'] : '',
		'desc'    => esc_html__( 'Apply your own design everywhere.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.wpomp_design_listing' ),
	)
);

$form->add_element(
	'textarea', 'map_all_control[wpomp_custom_css]', array(
		'label'         => esc_html__( 'Custom CSS', 'wp-leaflet-maps-pro' ),
		'value'         => isset( $data['map_all_control']['wpomp_custom_css'] ) ? $data['map_all_control']['wpomp_custom_css'] : '',
		'desc'          => esc_html__( 'Write here your custom css if any.', 'wp-leaflet-maps-pro' ),
		'textarea_rows' => 10,
		'textarea_name' => 'map_all_control[wpomp_custom_css]',
		'class'         => 'form-control wpomp_design_listing',
		'show'          => 'false',
	)
);

$form->add_element(
	'text', 'map_all_control[wpomp_base_font_size]', array(
		'label'         => esc_html__( 'Base Font Size', 'wp-leaflet-maps-pro' ),
		'value'         => isset( $data['map_all_control']['wpomp_base_font_size'] ) ? $data['map_all_control']['wpomp_base_font_size'] : '',
		'desc'          => esc_html__( 'Change it according to your site\'sfont family and font size. Default base font size is 16px.', 'wp-leaflet-maps-pro' ),
		'class'         => 'form-control wpomp_design_listing',
		'show'          => 'false',
		'default_value' => '16px',
	)
);

$color_schema = array(
	'#29B6F6_#212121' => "<span class='wpomp-color-schema' style='background-color:#29B6F6'></span>",
	'#212F3D_#212121' => "<span class='wpomp-color-schema' style='background-color:#212F3D'></span>",
	'#dd3333_#616161' => "<span class='wpomp-color-schema' style='background-color:#dd3333'></span>",
	'#FFB74D_#212121' => "<span class='wpomp-color-schema' style='background-color:#FF7043'></span>",
	'#FFC107_#616161' => "<span class='wpomp-color-schema' style='background-color:#FFC107'></span>",
	'#9C27B0_#616161' => "<span class='wpomp-color-schema' style='background-color:#9C27B0'></span>",
	'#673AB7_#616161' => "<span class='wpomp-color-schema' style='background-color:#673AB7'></span>",
	'#3F51B5_#616161' => "<span class='wpomp-color-schema' style='background-color:#3F51B5'></span>",
	'#00BCD4_#616161' => "<span class='wpomp-color-schema' style='background-color:#00BCD4'></span>",
	'#009688_#616161' => "<span class='wpomp-color-schema' style='background-color:#009688'></span>",
	'#4CAF50_#616161' => "<span class='wpomp-color-schema' style='background-color:#4CAF50'></span>",
	'#FF9800_#616161' => "<span class='wpomp-color-schema' style='background-color:#FF9800'></span>",
	'#FF5722_#616161' => "<span class='wpomp-color-schema' style='background-color:#FF5722'></span>",
	'#795548_#616161' => "<span class='wpomp-color-schema' style='background-color:#795548'></span>",
	'#9E9E9E_#616161' => "<span class='wpomp-color-schema' style='background-color:#9E9E9E'></span>",
);

$form->add_element(
	'radio', 'map_all_control[color_schema]', array(
		'label'           => esc_html__( 'Color Schema', 'wp-leaflet-maps-pro' ),
		'radio-val-label' => $color_schema,
		'current'         => isset( $data['map_all_control']['color_schema'] ) ? $data['map_all_control']['color_schema'] : '',
		'class'           => 'chkbox_class wpomp_design_listing',
		'show'            => 'false',
		'default_value'   => '4.png',
	)
);

$form->add_element(
	'checkbox', 'map_all_control[apply_own_schema]', array(
		'label'   => esc_html__( 'Apply Own Schema', 'wp-leaflet-maps-pro' ),
		'value'   => 'true',
		'current' => isset( $data['map_all_control']['apply_own_schema'] ) ? $data['map_all_control']['apply_own_schema'] : '',
		'desc'    => esc_html__( 'Apply your own color schema. Above selected schema will be ignored.', 'wp-leaflet-maps-pro' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.wpomp_own_schema' ),
	)
);

$form->add_element(
	'text', 'map_all_control[wpomp_primary_color]', array(
		'label' => esc_html__( 'Primary Color', 'wp-leaflet-maps-pro' ),
		'value' => isset( $data['map_all_control']['wpomp_primary_color'] ) ? $data['map_all_control']['wpomp_primary_color'] : '',
		'desc'  => esc_html__( 'Choose your primary color.', 'wp-leaflet-maps-pro' ),
		'class' => 'color {pickerClosable:true} form-control wpomp_own_schema',
		'show'  => 'false',
	)
);



