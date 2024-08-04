<?php
/**
 * Class: WPLMP_MODEL_Shortcode
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package wp-leaflet-maps-pro
 */

if ( ! class_exists( 'WPLMP_MODEL_Shortcode' ) ) {

	/**
	 * Shortcode model to display output on frontend.
	 *
	 * @package wp-leaflet-maps-pro
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPLMP_MODEL_Shortcode extends FlipperCode_Model_Base {
		/**
		 * Intialize Shortcode object.
		 */
		function __construct() {
		}
		/**
		 * Admin menu for Settings Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array();
		}
	}
}
