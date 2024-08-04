<?php
/**
 * Class: WPLMP_MODEL_Location
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package wp-leaflet-maps-pro
 */

if ( ! class_exists( 'WPLMP_MODEL_Location' ) ) {

	/**
	 * Location model for CRUD operation.
	 *
	 * @package wp-leaflet-maps-pro
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPLMP_MODEL_Location extends FlipperCode_Model_Base {

		/**
		 * Validations on location properies.
		 *
		 * @var array
		 */
		protected $validations;
		/**
		 * Intialize location object.
		 */
		public function __construct() {
			$this->table  = WPLMP_TBL_LOCATION;
			$this->unique = 'location_id';
			$this->validations = array(
				'location_title'     => array( 'req' => esc_html__( 'Please enter location title.', 'wp-leaflet-maps-pro' ) ),
				'location_address'     => array( 'req' => esc_html__( 'Please enter location address.', 'wp-leaflet-maps-pro' ) ),
			);
		}
		/**
		 * Admin menu for CRUD Operation
		 *
		 * @return array Admin meny navigation(s).
		 */
		public function navigation() {

			return array(
				'wpomp_form_location'   => esc_html__( 'Add Location', 'wp-leaflet-maps-pro' ),
				'wpomp_manage_location' => esc_html__( 'Manage Locations', 'wp-leaflet-maps-pro' ),
				'wpomp_import_location' => esc_html__( 'Import Locations', 'wp-leaflet-maps-pro' ),
			);
		}
		/**
		 * Install table associated with Location entity.
		 *
		 * @return string SQL query to install map_locations table.
		 */
		public function install() {

			global $wpdb;
			$map_location = 'CREATE TABLE ' . $this->table . ' (
location_id int(11) NOT NULL AUTO_INCREMENT,
location_title varchar(255) DEFAULT NULL,
location_address varchar(255) DEFAULT NULL,
location_draggable varchar(255) DEFAULT NULL,
location_infowindow_default_open varchar(255) DEFAULT NULL,
location_animation varchar(255) DEFAULT NULL,
location_latitude varchar(255) DEFAULT NULL,
location_longitude varchar(255) DEFAULT NULL,
location_city varchar(255) DEFAULT NULL,
location_state varchar(255) DEFAULT NULL,
location_country varchar(255) DEFAULT NULL,
location_postal_code varchar(255) DEFAULT NULL,
location_author int(11) DEFAULT NULL,
location_messages text DEFAULT NULL,
location_settings text DEFAULT NULL,
location_group_map text DEFAULT NULL,
location_extrafields text DEFAULT NULL,
PRIMARY KEY  (location_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;';

			return $map_location;
		}
		/**
		 * Get Location(s)
		 *
		 * @param  array $where  Conditional statement.
		 * @return array         Array of Location object(s).
		 */
		public function fetch( $where = array() ) {

			$objects = $this->get( $this->table, $where );

			if ( isset( $objects ) ) {
				foreach ( $objects as $object ) {
					$object->location_settings    = unserialize( $object->location_settings );
					$object->location_extrafields = unserialize( $object->location_extrafields );
					// Data convertion for version < 3.0.
					$is_category = unserialize( $object->location_group_map );
					if ( ! is_array( $is_category ) ) {
						$object->location_group_map = array( $object->location_group_map );
					} else {
						$object->location_group_map = $is_category;
					}
					// Data convertion for version < 3.0.
					$is_message = maybe_serialize( base64_decode( $object->location_messages ) );
					if ( is_array( $is_message ) ) {
						$object->location_messages = $is_message['googlemap_infowindow_message_one'];
					}
				}
				return $objects;
			}
		}
		public function cancel_import() {
			$current_csv = get_option( 'wpomp_current_csv' );
			unlink( $current_csv['file'] );
			delete_option( 'wpomp_current_csv' );
		}

		public function update_loc() {
			global $_POST;

			$entityID = '';
			
			if ( ! current_user_can('administrator') )
			die( 'You are not allowed to save changes!' );
			
			//Nonce Verification
			if( !isset( $_REQUEST['_wpnonce'] ) || ( isset( $_REQUEST['_wpnonce'] ) && empty($_REQUEST['_wpnonce']) ) )
			die( 'You are not allowed to save changes!' );
			
			if ( isset( $_REQUEST['_wpnonce'] ) && ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpomp-nonce' ) || wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpgmp-nonce' )) ) {
				
				$all_new_locations = json_decode( wp_unslash( $_POST['fc-location-new-set'] ) );
				if ( is_array( $all_new_locations ) and ! empty( $all_new_locations ) ) {
					foreach ( $all_new_locations as $location ) {
							$data['location_latitude']  = sanitize_text_field( $location->latitude );
							$data['location_longitude'] = sanitize_text_field( $location->longitude );
						if ( $location->id > 0 ) {
							$where[ $this->unique ] = $location->id;
							$result                 = FlipperCode_Database::insert_or_update( WPLMP_TBL_LOCATION, $data, $where );
						}
					}
				}

				if ( false === $result ) {
					$response['error'] = esc_html__( 'Something went wrong. Please try again.', 'wp-leaflet-maps-pro' );
				} elseif ( $entityID > 0 ) {
					$response['success'] = esc_html__( 'Location updated successfully', 'wp-leaflet-maps-pro' );
				} else {
					$response['success'] = esc_html__( 'Location added successfully.', 'wp-leaflet-maps-pro' );
				}

				return $response;

			}else{

				die( 'You are not allowed to save changes!' );
			}

		}
		/**
		 * Add or Edit Operation.
		 */
		public function save() {
			
			global $_POST;
			$entityID = '';

			if ( ! current_user_can('administrator') ){
			 die( 'You are not allowed to save changes!' );
			}

			//Nonce Verification
			if( !isset( $_REQUEST['_wpnonce'] ) || ( isset( $_REQUEST['_wpnonce'] ) && empty($_REQUEST['_wpnonce']) ) ){
				die( 'You are not allowed to save changes!' );
			}

			if ( isset( $_REQUEST['_wpnonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ); }

			if ( isset( $_REQUEST['_wpnonce'] ) && ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpomp-nonce' ) || wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpgmp-nonce' )) ) {


			$this->verify( $_POST );

			if ( is_array( $this->errors ) and ! empty( $this->errors ) )
			$this->throw_errors();
			
			if ( isset( $_POST['entityID'] ) )
			$entityID = intval( wp_unslash( $_POST['entityID'] ) );
			
			if ( isset( $_POST['location_messages'] ) )
			$data['location_messages'] = wp_unslash( $_POST['location_messages'] );
			
			if ( isset( $_POST['extensions_fields'] ) )
			$_POST['location_settings']['extensions_fields'] = $_POST['extensions_fields'];
			
			$data['location_settings']                = serialize( wp_unslash( $_POST['location_settings'] ) );

			if ( isset( $_POST['location_group_map'] ) ) {
				$data['location_group_map']               = serialize( wp_unslash( $_POST['location_group_map'] ) );
			} else {
				$data['location_group_map'] = '';
			}

			if( isset( $_POST['location_extrafields'] ) ) {
				$extra_fields                             = wp_unslash( $_POST['location_extrafields'] );	
			} else {
				$extra_fields = '';
			}

			$data['location_extrafields']             = serialize( wp_unslash( $extra_fields ) );
			$data['location_title']                   = sanitize_text_field( wp_unslash( $_POST['location_title'] ) );
			$data['location_address']                 = sanitize_text_field( wp_unslash( $_POST['location_address'] ) );
			$data['location_latitude']                = sanitize_text_field( wp_unslash( $_POST['location_latitude'] ) );
			$data['location_longitude']               = sanitize_text_field( wp_unslash( $_POST['location_longitude'] ) );
			$data['location_city']                    = sanitize_text_field( wp_unslash( $_POST['location_city'] ) );
			$data['location_state']                   = sanitize_text_field( wp_unslash( $_POST['location_state'] ) );
			$data['location_country']                 = sanitize_text_field( wp_unslash( $_POST['location_country'] ) );
			$data['location_postal_code']             = sanitize_text_field( wp_unslash( $_POST['location_postal_code'] ) );

			if ( isset( $_POST['location_draggable'] ) ) {
				$data['location_draggable'] = sanitize_text_field( wp_unslash( $_POST['location_draggable'] ) );
			} else {
				$data['location_draggable'] = '';
			}

			if ( isset( $_POST['location_infowindow_default_open'] ) ) {
				$data['location_infowindow_default_open'] = sanitize_text_field( wp_unslash( $_POST['location_infowindow_default_open'] ) );
			} else {
				$data['location_infowindow_default_open'] = '';
			}

			$data['location_animation']               = '';
			$data['location_author']                  = get_current_user_id();
			if ( $entityID > 0 ) {
				$where[ $this->unique ] = $entityID;
			} else {
				$where = '';
			}

			$result = FlipperCode_Database::insert_or_update( $this->table, $data, $where );

			if ( false === $result ) {
				$response['error'] = esc_html__( 'Something went wrong. Please try again.', 'wp-leaflet-maps-pro' );
			} elseif ( $entityID > 0 ) {
				$response['success'] = esc_html__( 'Location updated successfully', 'wp-leaflet-maps-pro' );
			} else {
				$response['success'] = esc_html__( 'Location added successfully.', 'wp-leaflet-maps-pro' );
			}
			return $response;
			



			}else{



				die( 'You are not allowed to save changes!' );

			}

		}

		/**
		 * Delete location object by id.
		 */
		public function delete() {
			if ( isset( $_GET['location_id'] ) ) {
				$id          = intval( wp_unslash( $_GET['location_id'] ) );
				$connection  = FlipperCode_Database::connect();
				$this->query = $connection->prepare( "DELETE FROM $this->table WHERE $this->unique='%d'", $id );
				return FlipperCode_Database::non_query( $this->query, $connection );
			}
		}
		/**
		 * Export data into csv
		 *
		 * @param  string $type File Type.
		 */
		function export( $type = 'csv' ) {

			$selected_locations = array();
			$data_locations = array();
			if ( isset( $_POST['id'] ) and is_array( $_POST['id'] ) ) {
				$selected_locations = $_POST['id'];
			}

			$all_locations = $this->fetch();
			$file_name     = sanitize_file_name( 'location_' . $type . '_' . time() );
			$modelFactory  = new WPLMP_MODEL();
			$category      = $modelFactory->create_object( 'group_map' );
			$categories    = $category->fetch();
			if ( ! empty( $categories ) ) {
				$categories_data = array();
				foreach ( $categories as $cat ) {
					$categories_data[ $cat->group_map_id ] = $cat->group_map_title;
				}
			}
			// get extra columns added.
			$extra_fields = unserialize( get_option( 'wpomp_location_extrafields' ) );
			if ( ! is_array( $extra_fields ) ) {
				$extra_fields = array();
			}
			foreach ( $all_locations as $location ) {
				if ( ! empty( $selected_locations ) and ! in_array( $location->location_id, $selected_locations ) ) {
					continue;
				}

				$location_extrafields     = $location->location_extrafields;
				$location_settings_fields = $location->location_settings;

				$assigned_categories = array();
				if ( isset( $location->location_group_map ) and is_array( $location->location_group_map ) ) {

					foreach ( $location->location_group_map as $c => $cat ) {
						if(isset($categories_data[ $cat ])){
							$assigned_categories[] = $categories_data[ $cat ];
						}
					}
				}
				$assigned_categories = implode( ',', $assigned_categories );
				$loc_data            = array(
					'location_id'          => $location->location_id,
					'location_title'       => $location->location_title,
					'location_address'     => $location->location_address,
					'location_latitude'    => $location->location_latitude,
					'location_longitude'   => $location->location_longitude,
					'location_city'        => $location->location_city,
					'location_state'       => $location->location_state,
					'location_country'     => $location->location_country,
					'location_postal_code' => $location->location_postal_code,
					'location_messages'    => $location->location_messages,
					'location_group_map'   => $assigned_categories,
				);

				if ( is_array( $extra_fields ) and ! empty( $extra_fields ) ) {

					foreach ( $extra_fields as $e_key => $extra_name ) {

						$extra_value = sanitize_title( $extra_name );

						if ( isset( $location_extrafields[ $extra_value ] ) ) {
							$loc_data[ $extra_name ] = $location_extrafields[ $extra_value ];
						} else {
							$loc_data[ $extra_name ] = '';
						}
					}
				}

				if ( ! empty( $location_settings_fields ) ) {

					foreach ( $location_settings_fields as $s_key => $setting_name ) {

						$loc_data[ $s_key ] = ( ! empty( $setting_name ) ) ? $setting_name : '';

					}
				}

				$data_locations[] = $loc_data;
			}

			$head_columns = array(
				'ID',
				'Title',
				'Address',
				'Latitude',
				'Longitude',
				'City',
				'State',
				'Country',
				'Postal Code',
				'Message',
				'Categories',
			);

			$location_settings_fields_headings = array( 'Click Action', 'Redirect URL' );
			$head_columns                      = array_merge( $head_columns, $extra_fields, $location_settings_fields_headings );
			$exporter                          = new FlipperCode_Export_Import( $head_columns, $data_locations );
			$exporter->export( $type, $file_name );
			die();
		}
		/**
		 * Import Location via CSV,JSON,XML and Excel.
		 *
		 * @return array Success or Failure error message.
		 */
		public function map_fields() {

			$response = false;
			$result = false;

			//Permission Verification
			if ( ! current_user_can('administrator') )
			die( 'You are not allowed to save changes!' );
			
			//Nonce Verification
			if( !isset( $_REQUEST['_wpnonce'] ) || ( isset( $_REQUEST['_wpnonce'] ) && empty($_REQUEST['_wpnonce']) ) )
			die( 'You are not allowed to save changes!' );
			
			if ( isset( $_REQUEST['_wpnonce'] ) && ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpomp-nonce' ) || wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpgmp-nonce' )) ) {
				
				if ( isset( $_POST['import_loc'] ) ) {
					if ( isset( $_FILES['import_file']['tmp_name'] ) and '' == sanitize_file_name( wp_unslash( $_FILES['import_file']['tmp_name'] ) ) ) {
						$response['error'] = esc_html__( 'Please select file to be imported.', 'wp-leaflet-maps-pro' );
					} elseif ( isset( $_FILES['import_file']['name'] ) and ! $this->validate_extension( sanitize_file_name( wp_unslash( $_FILES['import_file']['name'] ) ) ) ) {
						$response['error'] = esc_html__( 'Please upload a valid csv file.', 'wp-leaflet-maps-pro' );
					} else {

						if ( ! function_exists( 'wp_handle_upload' ) ) {
							require_once ABSPATH . 'wp-admin/includes/file.php';
						}

						$uploadedfile     = $_FILES['import_file'];
						$upload_overrides = array( 'test_form' => false );

						$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

						if ( $movefile && ! isset( $movefile['error'] ) ) {
							update_option( 'wpomp_current_csv', $movefile );
						} else {
							$response['error'] = $movefile['error'];
						}
					}
					return $response;
				}

			}else{

				die( 'You are not allowed to save changes!' );
			}


		}
public function import_location() {
			$result = false;

			if ( ! current_user_can('administrator') )
			die( 'You are not allowed to save changes!' );
			
			//Nonce Verification
			if( !isset( $_REQUEST['_wpnonce'] ) || ( isset( $_REQUEST['_wpnonce'] ) && empty($_REQUEST['_wpnonce']) ) )
			die( 'You are not allowed to save changes!' );
			
			if ( isset( $_REQUEST['_wpnonce'] ) && ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpomp-nonce' ) || wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpgmp-nonce' )) ) {
				
				if ( isset( $_POST['import_loc'] ) ) {

					$current_csv = get_option( 'wpomp_current_csv' );
				if ( ! is_array( $current_csv ) or ! file_exists( $current_csv['file'] ) ) {
					$response['error'] = esc_html__( 'Something went wrong. Please start import process again.', 'wp-leaflet-maps-pro' );
					return $response;
				}

					$csv_columns = wp_unslash( $_POST['csv_columns'] );

					$colums_mapping    = array();
					$duplicate_columns = array();

					// Unset unasigned field
				foreach ( $csv_columns as $key => $value ) {

					if ( $value == '' ) {
						unset( $csv_columns[ $key ] );
					}
				}

					// Find duplicate fields
					$duplicate_columns = array_count_values( $csv_columns );

					$not_allowed = array();
				foreach ( $duplicate_columns as $name => $count ) {

					if ( $count > 1 and $name != 'category' and $name != 'extra_field' ) {
						$not_allowed[] = $name;
					}
				}

				if ( count( $csv_columns ) == 0 ) {
					$response['error'] = _( 'Please map locations fields to csv columns.', 'wp-leaflet-maps-pro' );

					return $response;
				}

					$is_update_process = false;

				if ( in_array( 'location_id', $csv_columns ) !== false ) {
					$is_update_process = true;
				}

				if ( count( $not_allowed ) > 0 ) {
					$response['error'] = _( 'Duplicate mapping is not allowed except category and extra field.', 'wp-leaflet-maps-pro' );

					return $response;
				}

					// Address and title is required if add process.
				if ( $is_update_process == false ) {

					if ( in_array( 'location_address', $csv_columns ) === false or in_array( 'location_title', $csv_columns ) === false ) {
						$response['error'] = esc_html__( 'Title & Address fields are required.', 'wp-leaflet-maps-pro' );
						return $response;
					}
				}

				if ( count( $csv_columns ) > 0 ) {
					$importer             = new FlipperCode_Export_Import();
					$file_data            = $importer->import( 'csv', $current_csv['file'] );
					$current_extra_fields = unserialize( get_option( 'wpomp_location_extrafields' ) );
					if ( ! is_array( $current_extra_fields ) ) {
						$current_extra_fields = array();
					}

					if ( ! empty( $file_data ) ) {
						$modelFactory = new WPLMP_MODEL();
						$category     = $modelFactory->create_object( 'group_map' );
						$categories   = $category->fetch();
						$first_row    = $file_data[0];
						unset( $file_data[0] );
						if ( ! empty( $categories ) ) {
							$categories_data = array();
							foreach ( $categories as $cat ) {
								$categories_data[ $cat->group_map_id ] = strtolower( sanitize_text_field( $cat->group_map_title ) );
							}
						}
						foreach ( $file_data as $data ) {

							$all_data_in_string = implode(' ',$data);
							if( empty( trim($all_data_in_string) ) || trim($all_data_in_string) == '' )
							continue;
							
							$datas             = array();
							$category_ids      = array();
							$extra_fields      = array();
							$categories        = array();
							$location_settings = array();
							foreach ( $data as $key => $value ) {

								if ( ! isset( $csv_columns[ $key ] ) || trim( $csv_columns[ $key ] ) == '' ) {
									continue;
								}

								if ( $value != '' and ( $csv_columns[ $key ] == 'location_longitude' or $csv_columns[ $key ] == 'location_latitude' ) ) {
									$datas[ $csv_columns[ $key ] ] = (float) filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
								} elseif ( $csv_columns[ $key ] == 'category' ) {

									if ( trim( $value != '' ) ) {
										$all_categories = explode( ',', $value );
										if ( is_array( $all_categories ) ) {
											foreach ( $all_categories as $ci => $cname ) {
												$categories[] = strtolower( $cname );
											}
										}
									}
								} elseif ( $csv_columns[ $key ] == 'onclick' || $csv_columns[ $key ] == 'redirect_link' ) {
										$location_settings[ $csv_columns[ $key ] ] = $value;
								} elseif ( $csv_columns[ $key ] == 'extra_field' ) {
									   $current_extra_fields[]                               = $first_row[ $key ];
									   $extra_fields[ sanitize_title( $first_row[ $key ] ) ] = $value;
								} else {
									$datas[ $csv_columns[ $key ] ] = trim( $value );
								}
							}

							if ( ! isset( $categories_data ) ) {
								$categories_data = array();
							}

							// Find out categories id or insert new category.
							if ( isset( $categories ) and ! empty( $categories ) ) {
								$all_cat = $categories;
								if ( is_array( $all_cat ) ) {
									foreach ( $all_cat as $cat ) {
										$cat_id = array_search( sanitize_text_field( $cat ), (array) $categories_data );
										if ( false == $cat_id ) {
											// Create a new category.
											$new_cat_id                     = FlipperCode_Database::insert_or_update(
												WPLMP_TBL_GROUPMAP, array(
													'group_map_title' => sanitize_text_field( $cat ),
													'group_marker' => WPLMP_IMAGES . 'default_marker.png',
												)
											);
											$category_ids[]                 = $new_cat_id;
											$categories_data[ $new_cat_id ] = sanitize_text_field( $cat );

										} else {
											$category_ids[] = $cat_id;
										}
									}
								}
							}

							if ( is_array( $category_ids ) and ! empty( $category_ids ) ) {
								$datas['location_group_map'] = serialize( (array) $category_ids );
							}

							if ( is_array( $extra_fields ) and ! empty( $extra_fields ) ) {
								$datas['location_extrafields'] = serialize( $extra_fields );
							}

							if ( is_array( $location_settings ) and ! empty( $location_settings ) ) {
								$datas['location_settings'] = serialize( $location_settings );
							}

							if ( isset( $datas['location_latitude'] ) && trim( $datas['location_latitude'] ) == '' ) {
								unset( $datas['location_latitude'] );
							}

							if ( isset( $datas['location_longitude'] ) && trim( $datas['location_longitude'] ) == '' ) {
								unset( $datas['location_longitude'] );
							}

							$entityID = '';
							if ( isset( $datas['location_id'] ) ) {
								$entityID = intval( wp_unslash( $datas['location_id'] ) );
								unset( $datas['location_id'] );
							}

							// Rest Columns are extra fields.
							if ( $entityID > 0 ) {
								$where[ $this->unique ] = $entityID;
							} else {
								$where = '';
							}
							
							$datas = array_filter( $datas );
							if(  count( $datas ) == 0 )
							continue;
														
							$result = FlipperCode_Database::insert_or_update( $this->table, $datas, $where );

						}

						$current_extra_fields = array_unique( $current_extra_fields );
						update_option( 'wpomp_location_extrafields', serialize( $current_extra_fields ) );
						$response['success'] = count( $file_data ) . ' ' . esc_html__( 'records imported successfully.', 'wp-leaflet-maps-pro' );
						// Here remove the temp file.
						unlink( $current_csv['file'] );
						delete_option( 'wpomp_current_csv' );

					} else {
						$response['error'] = esc_html__( 'No records found in the csv file.', 'wp-leaflet-maps-pro' );
					}
				} else {
					$response['error'] = esc_html__( 'Please assign fields to the csv columns.', 'wp-leaflet-maps-pro' );
				}

				return $response;
			}
			
			}else{

				die( 'You are not allowed to save changes!' );
			}
		}
	}
}
