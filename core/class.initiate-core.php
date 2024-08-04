<?php

	/**
	 *  Load All Core Initialisation class
	 *
	 *  @package Core
	 *  @author Flipper Code <hello@flippercode.com>
	 */

if ( ! class_exists( 'FlipperCode_Initialise_Core' ) ) {


	class FlipperCode_Initialise_Core {

		public function __construct() {

			$this->_load_core_files();
			$this->_register_flippercode_globals();
		}

		public function _register_flippercode_globals() {

			add_action( 'wp_ajax_fc_communication', array( $this, 'fc_communication' ) );
			add_action( 'wp_ajax_check_products_updates', array( $this, 'check_products_updates' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_products_common_resources' ) );
			add_action( 'wp_ajax_core_templates', array( $this, 'fc_load_template' ) );

		}

		function fc_load_template() {

			check_ajax_referer( 'fc-call-nonce', 'nonce' );
			$response      = array();
			$data          = $_POST;
			$core_dir_path = plugin_dir_path( dirname( __FILE__ ) );
			$core_dir_url  = plugin_dir_url( dirname( __FILE__ ) );
			$template      = $data['template_name'];
			$template_type = $data['template_type'];

			if ( isset( $data['template_name'] ) ) {
				$layout_file = $core_dir_path . 'templates/' . $template_type . '/' . $template . '/' . $template . '.html';
				$layout_url  = $core_dir_url . 'templates/' . $template_type . '/' . $template . '/' . $template . '.html';
				ob_start();
				include_once $layout_file;
				$content = ob_get_contents();
				ob_clean();
			}

			if ( isset( $data['template_source'] ) ) {
				$content = stripcslashes( $data['template_source'] );
			}

			if ( $content == '' ) {
				$response['html'] = '<div id="messages" class="error">Sorry layout ' . $layout_id . ' not found.</div>';
			} else {
				$temp_content = $content;
				$content      = "<div class='fc-" . $template_type . '-' . $template . "'>" . apply_filters( 'fc-dummy-placeholders', $content ) . '</div>';
				$columns      = $data['columns'];
				if ( $columns == '' ) {
					$columns = 1;}
				$parent_div = '<div class="fc-component-block fc-columns-' . $columns . '">';
				for ( $i = 0;$i < $columns;$i++ ) {
					$parent_div .= '<div class="fc-component-content">' . $content . '</div>';
				}
				$parent_div            .= '</div>';
				$response['html']       = $parent_div;
				$response['sourcecode'] = $temp_content;
			}
			echo json_encode( $response );
			exit;

		}

		function fc_communication() {

			$result = array();

			if ( isset( $_POST['action'] ) and wp_unslash( $_POST['action'] ) == 'fc_communication' and isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'fc_communication' ) ) {
				$url                  = 'https://www.flippercode.com/logs/wunpupdates/';
				$data                 = array();
				$data['wunpu_action'] = sanitize_text_field( $_POST['operation'] );
				$product              = sanitize_text_field( wp_unslash( $_POST['product'] ) );
				foreach ( $_POST as $key => $value ) {
					$data[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
				}

				$args     = array(
					'method'  => 'POST',
					'timeout' => 45,
					'body'    => $data,
				);
				$response = wp_remote_post( $url, $args );

				if ( is_wp_error( $response ) ) {
					$result = array(
						'status' => '0',
						'error'  => $response->get_error_message(),
					);
				} else {
					$result = (array) json_decode( $response['body'] );
					if ( $data['wunpu_action'] == 'get_plugin_details' ) {
						$plugin_updates = update_option( 'fc_' . $product, serialize( (array) $result['plugin_details'] ) );
					}
					$result = array(
						'status'  => '1',
						'title'   => $result['title'],
						'content' => $result['content'],
					);
				}
			} else {
					$result = array(
						'status'  => '0',
						'title'   => 'Error',
						'content' => 'Something went wrong. Try again in few minutes.',
					);
			}

			echo json_encode( $result );
			exit;

		}

		function load_products_common_resources( $hook ) {

			if ( strpos( $hook, 'view_overview' ) !== false ) {

				// One of our product's overview page. Load necessary resources on this page only.
			}

		}

		function is_localhost() {

			$isLocalhost = ( $_SERVER['SERVER_NAME'] != 'localhost' ) ? true : false;
			return $isLocalhost;
		}


		public function check_products_updates() {

			$url      = 'https://www.flippercode.com/logs/wunpupdates/';
			$plugin   = wp_unslash( $_POST['productslug'] );
			$bodyargs = array(
				'wunpu_action' => 'updates',
				'plugin'       => $plugin,
				'get_info'     => 'version',
			);

			$args     = array(
				'method'  => 'POST',
				'timeout' => 45,
				'body'    => $bodyargs,
			);
			$response = wp_remote_post( $url, $args );
			$response = (array) unserialize( $response['body'] );

			if ( is_wp_error( $response ) ) {
				$summary = array(
					'status' => '0',
					'error'  => $response->get_error_message(),
				);
			} else {

				update_option( $plugin . '_latest_version', serialize( $response ) );

				$version = trim( $response['new_version'], '"' );
				$summary = array(
					'status'        => '1',
					'latestversion' => wp_unslash( trim( $version ) ),
				);
			}

			echo json_encode( $summary );
			exit;

		}

		public function _load_core_files() {

			$corePath  = plugin_dir_path( __FILE__ );
			$coreFiles = array(
				'class.tabular.php',
				'class.template.php',
				'abstract.factory.php',
				'class.controller-factory.php',
				'class.model-factory.php',
				'class.controller.php',
				'class.model.php',
				'class.validation.php',
				'class.database.php',
				'class.importer.php',
				'class.plugin-overview.php',
			);

			/**
			 *  Load All Core Initialisation class from core folder
			 */
			foreach ( $coreFiles as $file ) {

				if ( file_exists( $corePath . $file ) ) {
						require_once $corePath . $file;
				}
			}

		}

	}

}

	return new FlipperCode_Initialise_Core();

