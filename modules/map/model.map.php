<?php
/**
 * Class: WPLMP_MODEL_Map
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package wp-leaflet-maps-pro
 */

if ( ! class_exists( 'WPLMP_MODEL_Map' ) ) {

	/**
	 * Map model for CRUD operation.
	 *
	 * @package wp-leaflet-maps-pro
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPLMP_MODEL_Map extends FlipperCode_Model_Base {
		/**
		 * Validations on route properies.
		 *
		 * @var array
		 */
		protected $validations;
		/**
		 * Intialize map object.
		 */
		function __construct() {

			$this->validations = array(
			'map_title'  => array( 'req' => esc_html__('Please enter map title.','wp-leaflet-maps-pro') ),
			'map_height' => array( 'req' => esc_html__('Please enter map height.','wp-leaflet-maps-pro') ),
		);

			$this->table  = WPLMP_TBL_MAP;
			$this->unique = 'map_id';
		}
		/**
		 * Admin menu for CRUD Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wpomp_form_map'   => esc_html__( 'Add Map', 'wp-leaflet-maps-pro' ),
				'wpomp_manage_map' => esc_html__( 'Manage Maps', 'wp-leaflet-maps-pro' ),
			);

		}
		/**
		 * Install table associated with map entity.
		 *
		 * @return string SQL query to install create_map table.
		 */
		function install() {
			global $wpdb;
			$create_map = 'CREATE TABLE ' . $this->table. ' (
			map_id int(11) NOT NULL AUTO_INCREMENT,
			map_title varchar(255) DEFAULT NULL,
			map_width varchar(255) DEFAULT NULL,
			map_height varchar(255) DEFAULT NULL,
			map_zoom_level varchar(255) DEFAULT NULL,
			map_type varchar(255) DEFAULT NULL,
			map_scrolling_wheel varchar(255) DEFAULT NULL,
			map_visual_refresh varchar(255) DEFAULT NULL,
			map_45imagery varchar(255) DEFAULT NULL,
			map_street_view_setting text DEFAULT NULL,
			map_route_direction_setting text DEFAULT NULL,
			map_all_control text DEFAULT NULL,
			map_info_window_setting text DEFAULT NULL,
			style_google_map text DEFAULT NULL,
			map_locations longtext DEFAULT NULL,
			map_layer_setting text DEFAULT NULL,
			map_polygon_setting longtext DEFAULT NULL,
			map_polyline_setting longtext DEFAULT NULL,
			map_cluster_setting text DEFAULT NULL,
			map_overlay_setting text DEFAULT NULL,
			map_geotags text DEFAULT NULL,
			map_infowindow_setting text DEFAULT NULL,
			PRIMARY KEY  (map_id)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;';

			return $create_map;
		}

		public function wpomp_array_map( $element ) {
			return $element['slug']; }

		public function clear_empty_array_values( $array ) {

			foreach ( $array as $k => &$v ) {

				if ( $k == 'extra_fields' ) {
					continue;
				}

				if ( is_array( $v ) ) {
					$v = $this->clear_empty_array_values( $v );
					if ( ! sizeof( $v ) ) {
						unset( $array[ $k ] );
					}
				} elseif ( ! is_object( $v ) and ! strlen( $v ) and ! is_bool( $v ) ) {
					unset( $array[ $k ] );
				}
			}
			return $array;

		}

		public function find_font( $element ) {

			if ( strpos( $element, 'font-family' ) !== false ) {
				$f_family = str_replace( 'font-family:', '', $element );
				if ( strpos( $f_family, 'Open Sans' ) === false ) {
					return $f_family;
				}
			}

		}
		/**
		 * Get Map(s)
		 *
		 * @param  array $where  Conditional statement.
		 * @return array         Array of Map object(s).
		 */
		public function fetch( $where = array() ) {

			$objects = $this->get( $this->table, $where );
			if ( isset( $objects ) ) {
				return $objects;
			}
		}
		/**
		 * Add or Edit Operation.
		 */
		function save() {
			global $_POST;
			$data     = array();
			$entityID = '';
			
			if ( ! current_user_can('administrator') )
			die( 'You are not allowed to save changes!' );
			
			//Nonce Verification
			if( !isset( $_REQUEST['_wpnonce'] ) || ( isset( $_REQUEST['_wpnonce'] ) && empty($_REQUEST['_wpnonce']) ) )
			die( 'You are not allowed to save changes!' );
			

			if ( isset( $_REQUEST['_wpnonce'] ) && ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpomp-nonce' ) || wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpgmp-nonce' )) ) {


				if ( ! isset( $_POST['wpomp_import_code'] ) or $_POST['wpomp_import_code'] == '' ) {

					$this->verify( $_POST );

				}
				if ( is_array( $this->errors ) and ! empty( $this->errors ) ) {
					$this->throw_errors();
				}

				if ( isset( $_POST['entityID'] ) ) {
					$entityID = intval( wp_unslash( $_POST['entityID'] ) );
				}

				if ( isset( $_POST['wpomp_import_code'] ) and $_POST['wpomp_import_code'] != '' ) {
					$import_code = wp_unslash( $_POST['wpomp_import_code'] );
					if ( trim( $import_code ) != '' ) {
						$map_settings = unserialize( base64_decode( $import_code ) );
						if ( is_object( $map_settings ) ) {
							$_POST = (array) $map_settings;
						}
					}
				}

				if ( ! is_array( $_POST['map_locations'] ) and '' != sanitize_text_field( $_POST['map_locations'] ) ) {
					$map_locations = explode( ',', sanitize_text_field( $_POST['map_locations'] ) );
				} elseif ( is_array( $_POST['map_locations'] ) and ! empty( $_POST['map_locations'] ) ) {
					$map_locations = $_POST['map_locations'];
				} else {
					$map_locations = array(); }

				if ( isset( $_POST['extensions_fields'] ) ) {
					$_POST['map_all_control']['extensions_fields'] = $_POST['extensions_fields'];
				}

				if ( isset( $_POST['map_all_control']['map_control_settings'] ) ) {
					$arr = array();
					$i   = 0;
					foreach ( $_POST['map_all_control']['map_control_settings'] as $key => $val ) {
						if ( $val['html'] != '' ) {
							$arr[ $i ]['html']     = $val['html'];
							$arr[ $i ]['position'] = $val['position'];
							$i++;
						}
					}
					$_POST['map_all_control']['map_control_settings'] = $arr;
				}

				if ( isset( $_POST['map_all_control']['custom_filters'] ) ) {
					$custom_filters = array();
					foreach ( $_POST['map_all_control']['custom_filters'] as $k => $val ) {
						if ( $val['slug'] == '' ) {
							unset( $_POST['map_all_control']['custom_filters'][ $k ] );
						} else {
							$custom_filters[] = $val;
						}
					}
					$_POST['map_all_control']['custom_filters'] = $custom_filters;
				}

				if ( isset( $_POST['map_all_control']['location_infowindow_skin']['sourcecode'] ) ) {
					$_POST['map_all_control']['infowindow_setting'] = $_POST['map_all_control']['location_infowindow_skin']['sourcecode'];
				}

				if ( isset( $_POST['map_all_control']['post_infowindow_skin']['sourcecode'] ) ) {
					$_POST['map_all_control']['infowindow_geotags_setting'] = $_POST['map_all_control']['post_infowindow_skin']['sourcecode'];
				}

				if ( isset( $_POST['map_all_control']['item_skin']['sourcecode'] ) ) {
					$_POST['map_all_control']['wpomp_categorydisplayformat'] = $_POST['map_all_control']['item_skin']['sourcecode'];
				}

				$data['map_title']                   = sanitize_text_field( wp_unslash( $_POST['map_title'] ) );
				$data['map_width']                   = str_replace( 'px', '', sanitize_text_field( wp_unslash( $_POST['map_width'] ) ) );
				$data['map_height']                  = str_replace( 'px', '', sanitize_text_field( wp_unslash( $_POST['map_height'] ) ) );
				$data['map_zoom_level']              = intval( wp_unslash( $_POST['map_zoom_level'] ) );
				$data['map_type']                    = '';
				$data['map_scrolling_wheel']         = isset($_POST['map_scrolling_wheel']) ? sanitize_text_field( wp_unslash( $_POST['map_scrolling_wheel'] ) ):'';
				$data['map_street_view_setting']     = '';	
				
				$data['map_all_control']             = serialize( wp_unslash( $_POST['map_all_control'] ) );

				if ( isset( $_POST['map_info_window_setting'] ) ) {
					$data['map_info_window_setting']     = serialize( wp_unslash( $_POST['map_info_window_setting'] ) );
				}
				$data['map_locations']               = serialize( wp_unslash( $map_locations ) );
				
				
				if ( isset( $_POST['map_infowindow_setting'] ) ) {
					$data['map_infowindow_setting']      = serialize( wp_unslash( $_POST['map_infowindow_setting'] ) );
				}

				$data['map_geotags']                 = serialize( wp_unslash( $_POST['map_geotags'] ) );
				if ( $entityID > 0 ) {
					$where[ $this->unique ] = $entityID;
				} else {
					$where = '';
				}
				// Hook to insert/update extension data.
				if ( isset( $_POST['fc_entity_type'] ) ) {

					$extension_name = strtolower( trim( sanitize_text_field( wp_unslash( $_POST['fc_entity_type'] ) ) ) );

					if ( $extension_name != '' ) {
						$data = apply_filters( $extension_name . '_save', $data, $this->table, $where );
					}
				}

				$result = FlipperCode_Database::insert_or_update( $this->table, $data, $where );
				if ( false === $result ) {
					$response['error'] = esc_html__( 'Something went wrong. Please try again.', 'wp-leaflet-maps-pro' );
				} elseif ( $entityID > 0 ) {
					$response['success'] = esc_html__( 'Map updated successfully', 'wp-leaflet-maps-pro' );
				} else {
					$response['success'] = esc_html__( 'Map added successfully.', 'wp-leaflet-maps-pro' );
				}
				return $response;


			}else{
				die( 'You are not allowed to save changes!' );
			}

		}
		/**
		 * Delete map object by id.
		 */
		function delete() {
			if ( isset( $_GET['map_id'] ) ) {
				$id          = intval( wp_unslash( $_GET['map_id'] ) );
				$connection  = FlipperCode_Database::connect();
				$this->query = $connection->prepare( "DELETE FROM $this->table WHERE $this->unique='%d'", $id );
				return FlipperCode_Database::non_query( $this->query, $connection );
			}
		}
		/**
		 * Clone map object by id.
		 */
		function copy( $map_id ) {
			if ( isset( $map_id ) ) {
				$id   = intval( wp_unslash( $map_id ) );
				$map  = $this->get( $this->table, array( array( 'map_id', '=', $id ) ) );
				$data = array();
				foreach ( $map[0] as $column => $value ) {

					if ( $column == 'map_id' ) {
						continue; } elseif ( $column == 'map_title' ) {
						$data[ $column ] = $value . ' ' . esc_html__( 'Copy', 'wp-leaflet-maps-pro' );
						} else {
							$data[ $column ] = $value; }
				}

				$result = FlipperCode_Database::insert_or_update( $this->table, $data );
			}
		}

	}
}
