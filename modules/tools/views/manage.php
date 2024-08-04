<?php
/**
 * This class used to backup all tables for this plugins.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package wp-leaflet-maps-pro
 */


if ( isset( $_POST['operation'] ) and 'clean_database' == $_POST['operation'] ) {
	$clean_database = $response;
} else {
	$clean_database = array();
}

if ( isset( $_POST['operation'] ) and 'upload_sampledata' == $_POST['operation'] ) {
	$upload_sampledata = $response;
} else {
	$upload_sampledata = array();
}

	$form = new WPLMP_Template();
	$form->set_header( esc_html__( 'Clean Database', 'wp-leaflet-maps-pro' ), $clean_database );

	$form->add_element(
		'group', 'clean_database', array(
			'value'  => esc_html__( 'Clean Database', 'wp-leaflet-maps-pro' ),
			'before' => '<div class="fc-12">',
			'after'  => '</div>',
		)
	);

	$form->add_element(
		'hidden', 'operation', array(
			'value' => 'clean_database',
		)
	);

	$form->add_element(
		'message', 'backup_message', array(
			'value' => esc_html__( "Click below to remove all locations, maps, & categories from database. This method is not recommended on live site as it will delete all the data you've created.", 'wp-leaflet-maps-pro' ),
			'class' => 'fc-msg fc-danger',
			'before' => '<div class="fc-12">',
			'after'  => '</div>',
		)
	);

	$form->add_element(
		'text', 'wpomp_clean_consent', array(
			'label'  => esc_html__( 'Verify Action', 'wp-leaflet-maps-pro' ),
			'id'     => 'wpomp_consent',
			'class'  => 'form-control',
			'desc'   => esc_html__( 'Type "DELETE" to give consent that you actually want to remove all maps data.', 'wp-leaflet-maps-pro' ),
	
		)
	);

	$form->add_element(
		'submit', 'wpomp_cleandatabase_tools', array(
			'value' => esc_html__( 'Clear Database', 'wp-leaflet-maps-pro' ),
		)
	);

	$form->render();

	$import_form = new WPLMP_Template( array( 'no_header' => true ) );
	$import_form->set_header( esc_html__( 'Create Sample Map', 'wp-leaflet-maps-pro' ), $upload_sampledata );

	$import_form->add_element(
	'group', 'sample_data', array(
		'value'  => esc_html__( 'Create Sample Map', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
	);

	$import_form->add_element(
		'hidden', 'operation', array(
			'value' => 'upload_sampledata',
		)
	);

	$import_form->add_element(
		'message', 'sampledata_message', array(
			'value' => esc_html__( 'Click below to install sample data. This is very useful to get started. 1 map will be created with 5 locations and 2 categories for demonstration purpose.', 'wp-leaflet-maps-pro' ),
			'class' => 'fc-msg  fc-success',
			'before' => '<div class="fc-12">',
			'after'  => '</div>',
		)
	);

	$import_form->add_element(
		'text', 'wpomp_sampledata_consent', array(
			'label'  => esc_html__( 'Verify Action', 'wp-leaflet-maps-pro' ),
			'class'  => 'form-control',
			'desc'   => esc_html__( ' Type "YES" to create sample map populated with sample data.', 'wp-leaflet-maps-pro' ),
	
		)
	);


	$import_form->add_element(
		'submit', 'wpomp_sampledata_submit', array(
			'value' => esc_html__( 'Create Sample Data', 'wp-leaflet-maps-pro' ),
		)
	);

	$import_form->render();

