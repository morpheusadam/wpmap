<?php
/**
 * Class: WPLMP_MODEL_Settings
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package wp-leaflet-maps-pro
 */

if ( ! class_exists( 'WPLMP_MODEL_Settings' ) ) {

	/**
	 * Setting model for Plugin Options.
	 *
	 * @package wp-leaflet-maps-pro
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPLMP_MODEL_Settings extends FlipperCode_Model_Base {
		/**
		 * Intialize Backup object.
		 */
		function __construct() {
		}
		/**
		 * Admin menu for Settings Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wpomp_manage_settings' => esc_html__( 'Plugin Settings', 'wp-leaflet-maps-pro' ),
			);
		}
		/**
		 * Add or Edit Operation.
		 */
		function save() {
			global $_POST;
			if ( ! current_user_can('administrator') )
			die( 'You are not allowed to save changes!' );
			
			//Nonce Verification
			if( !isset( $_REQUEST['_wpnonce'] ) || ( isset( $_REQUEST['_wpnonce'] ) && empty($_REQUEST['_wpnonce']) ) )
			die( 'You are not allowed to save changes!' );
			
			if ( isset( $nonce ) && (!wp_verify_nonce( $nonce, 'wpomp-nonce' ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpgmp-nonce' )) )
			die( 'You are not allowed to save changes!' );


			$this->verify( $_POST );

			if ( is_array( $this->errors ) and ! empty( $this->errors ) ) {
				$this->throw_errors();
			}
			$extra_fields = array();
			if ( isset( $_POST['location_extrafields'] ) ) {
				foreach ( $_POST['location_extrafields'] as $index => $label ) {
					if ( $label != '' ) {
						$extra_fields[ $index ] = sanitize_text_field( wp_unslash( $label ) );
					}
				}
			}

			$meta_hide = array();
			if ( isset( $_POST['wpomp_allow_meta'] ) ) {
				foreach ( $_POST['wpomp_allow_meta'] as $index => $label ) {
					if ( $label != '' ) {
						$meta_hide[ $index ] = sanitize_text_field( wp_unslash( $label ) );
					}
				}
			}
			$wpomp_settings = array();

			$wpomp_settings['wpomp_api_key']          = sanitize_text_field( wp_unslash( $_POST['wpomp_api_key'] ) );
			$wpomp_settings['wpomp_mapquest_key']     = sanitize_text_field( wp_unslash( $_POST['wpomp_mapquest_key'] ) );
			$wpomp_settings['wpomp_bingmap_key']      = sanitize_text_field( wp_unslash( $_POST['wpomp_bingmap_key'] ) );
			
			$wpomp_settings['wpomp_scripts_place']    = sanitize_text_field( wp_unslash( $_POST['wpomp_scripts_place'] ) );
			$wpomp_settings['wpomp_scripts_minify']    = sanitize_text_field( wp_unslash( $_POST['wpomp_scripts_minify'] ) );
			$wpomp_settings['wpomp_allow_meta']       = serialize( $meta_hide );

			if ( isset( $_POST['wpomp_metabox_map'] ) ) {
				$wpomp_settings['wpomp_metabox_map']      = sanitize_text_field( wp_unslash( $_POST['wpomp_metabox_map'] ) );
			} else {
				$wpomp_settings['wpomp_metabox_map'] = '';
			}

			if ( isset( $_POST['wpomp_auto_fix'] ) ) {

				$wpomp_settings['wpomp_auto_fix']         = sanitize_text_field( wp_unslash( $_POST['wpomp_auto_fix'] ) );
			} else {
				$wpomp_settings['wpomp_auto_fix']         = '';
			}

			if ( isset( $_POST['wpomp_debug_mode'] ) ) {
				$wpomp_settings['wpomp_debug_mode']             = sanitize_text_field( wp_unslash( $_POST['wpomp_debug_mode'] ) );
			} else {
				$wpomp_settings['wpomp_debug_mode']             = '';
			}

			if ( isset( $_POST['wpomp_gdpr'] ) ) {
				$wpomp_settings['wpomp_gdpr']             = sanitize_text_field( wp_unslash( $_POST['wpomp_gdpr'] ) );
			} else {
				$wpomp_settings['wpomp_gdpr']             = '';
			}

			$wpomp_settings['wpomp_gdpr_msg']         = wp_unslash( $_POST['wpomp_gdpr_msg'] );

			if ( isset( $_POST['wpomp_country_specific'] ) ) {
				$wpomp_settings['wpomp_country_specific'] = sanitize_text_field( wp_unslash( $_POST['wpomp_country_specific'] ) );
			} else {
				$wpomp_settings['wpomp_country_specific'] = '';
			}

			if( isset($_POST['wpomp_countries']) ) {
				$wpomp_settings['wpomp_countries']        = wp_unslash( $_POST['wpomp_countries'] );
			}

			update_option( 'wpomp_settings', $wpomp_settings );
			update_option( 'wpomp_location_extrafields', serialize( $extra_fields ) );

			$response['success'] = esc_html__( 'Setting(s) saved successfully.', 'wp-leaflet-maps-pro' );
			return $response;

		}
	}
}
