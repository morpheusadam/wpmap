<?php
/**
 * Import Location(s) Tool.
 *
 * @package wp-leaflet-maps-pro
 * @author Flipper Code <hello@flippercode.com>
 */

$form        = new WPLMP_Template();
$current_csv = get_option( 'wpomp_current_csv' );
$step        = 'step-1';

if ( is_array( $current_csv ) and file_exists( $current_csv['file'] ) ) {
	$step = 'step-2';
}

if ( $step == 'step-1' ) {
	$form->set_header( esc_html__( 'Step 1 - Upload CSV', 'wp-leaflet-maps-pro' ), $response );
	$form->add_element(
		'group', 'upload_step_1', array(
			'value'  => esc_html__( 'Step 1 - Upload CSV', 'wp-leaflet-maps-pro' ),
			'before' => '<div class="fc-12">',
			'after'  => '</div>',
		)
	);

} elseif ( $step == 'step-2' ) {
	$form->set_header( esc_html__( 'Step 2 - Columns Mapping', 'wp-leaflet-maps-pro' ), $response );
	$form->add_element(
		'group', 'upload_step_2', array(
			'value'  => esc_html__( 'Step 2 - Columns Mapping', 'wp-leaflet-maps-pro' ),
			'before' => '<div class="fc-12">',
			'after'  => '</div>',
		)
	);
}



if ( $step == 'step-1' ) {


	$form->add_element(
		'file', 'import_file', array(
			'label' => esc_html__( 'Choose File', 'wp-leaflet-maps-pro' ),
			'file_text' => esc_html__( 'Choose a File', 'wp-leaflet-maps-pro' ),
			'class' => 'file_input',
			'desc'  => esc_html__( 'Please upload a valid CSV file.', 'wp-leaflet-maps-pro' ),
		)
	);

	$form->add_element(
		'submit', 'import_loc', array(
			'value'     => esc_html__( 'Continue', 'wp-leaflet-maps-pro' ),
			'no-sticky' => true,

		)
	);

	$form->add_element(
		'html', 'instruction_html', array(
			'html'   => '',
			'before' => '<div class="fc-11">',
			'after'  => '</div>',
		)
	);


	$form->add_element(
		'hidden', 'operation', array(
			'value' => 'map_fields',
		)
	);
	$form->add_element(
		'hidden', 'import', array(
			'value' => 'location_import',
		)
	);
	$form->render();


} elseif ( $step == 'step-2' ) {

	$importer  = new FlipperCode_Export_Import();
	$file_data = $importer->import( 'csv', $current_csv['file'] );

	$datas = array();

	$csv_columns = array_values( $file_data[0] );

	$extra_fields = array();
	$core_fields  = array(
		''                     => esc_html__( 'Select Field', 'wp-leaflet-maps-pro' ),
		'location_title'       => esc_html__( 'Title', 'wp-leaflet-maps-pro' ),
		'location_address'     => esc_html__( 'Address', 'wp-leaflet-maps-pro' ),
		'location_latitude'    => esc_html__( 'Latitude', 'wp-leaflet-maps-pro' ),
		'location_longitude'   => esc_html__( 'Longitude', 'wp-leaflet-maps-pro' ),
		'location_city'        => esc_html__( 'City', 'wp-leaflet-maps-pro' ),
		'location_state'       => esc_html__( 'State', 'wp-leaflet-maps-pro' ),
		'location_country'     => esc_html__( 'Country', 'wp-leaflet-maps-pro' ),
		'location_postal_code' => esc_html__( 'Postal Code', 'wp-leaflet-maps-pro' ),
		'location_messages'    => esc_html__( 'Message', 'wp-leaflet-maps-pro' ),
		'onclick'              => esc_html__( 'Location Click', 'wp-leaflet-maps-pro' ),
		'redirect_link'        => esc_html__( 'Location Redirect URL', 'wp-leaflet-maps-pro' ),
		'category'             => esc_html__( 'Category', 'wp-leaflet-maps-pro' ),
		'extra_field'          => esc_html__( 'Extra Field', 'wp-leaflet-maps-pro' ),
		'location_id'          => esc_html__( 'ID', 'wp-leaflet-maps-pro' ),
	);

	foreach ( $core_fields as $key => $value ) {
		$csv_options[ $key ] = $value;
	}



	$html = '<p class="fc-msg"><b>' . ( count( $file_data ) - 1 ) . '</b> ' . esc_html__( 'records are ready to upload. Please map csv columns below and click on Import button.', 'wp-leaflet-maps-pro' ) . '. Leave ID field empty if you\'re adding new records. ID field is used to update existing location.</p>';

	$html .= '<div class="fc-table-responsive">
 <table class="fc-table">
 <thead><tr><th>CSV Field</th><th>Assign</th></tr></thead>
 <tbody>';

	foreach ( $csv_columns as $key => $value ) {

		if ( isset( $_POST['csv_columns'][ $key ] ) ) {
			$selected = $_POST['csv_columns'][ $key ];
		} elseif ( array_search( $value, $core_fields ) ) {
			$selected = array_search( $value, $core_fields );
		} else {
			$selected = '';
		}


		$html .= '<tr><td>' . $value . '</td><td>' . $form->field_select(
			'csv_columns[' . $key . ']', array(
				'options' => $csv_options,
				'current' => $selected,
			)
		) . '</td></tr>';
	}

	$html .= '</tbody></table>';
	$form->add_element(
		'html', 'instruction_html', array(
			'html'   => $html,
			'before' => '<div class="fc-11">',
			'after'  => '</div>',
		)
	);
	$form->add_element(
		'hidden', 'operation', array(
			'value' => 'import_location',
		)
	);
	$form->add_element(
		'hidden', 'import', array(
			'value' => 'location_import',
		)
	);


	$submit_button = $form->field_submit(
		'import_loc', array(
			'value'     => esc_html__( 'Import Locations', 'wp-leaflet-maps-pro' ),
			'no-sticky' => true,
			'class'     => 'fc-btn',
		)
	);

	$cancel_button = $form->field_button(
		'cancel_import', array(
			'value'     => esc_html__( 'Cancel', 'wp-leaflet-maps-pro' ),
			'no-sticky' => true,
			'class'     => 'fc-btn fc-danger fc-btn-big cancel_import',
		)
	);


	$html = "<div class='fc-row'><div class='fc-2'>" . $submit_button . "</div><div class='fc-9'>" . $cancel_button . '</div></div>';

	$form->add_element(
		'html', 'button_html', array(
			'html'   => $html,
			'before' => '<div class="fc-12">',
			'after'  => '</div>',
		)
	);


	$form->render();

}

