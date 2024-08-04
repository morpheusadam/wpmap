<?php
/**
 * Controller class
 *
 * @author Flipper Code<hello@flippercode.com>
 * @version 1.0.0
 * @package wp-leaflet-maps-pro
 */

if ( ! class_exists( 'wpomp_Translation' ) ) {

	/**
	 * Controller class to display views.
	 *
	 * @author: Flipper Code<hello@flippercode.com>
	 * @version 1.0.0
 	 * @package wp-leaflet-maps-pro
	 */

	class wpomp_Translation {


		function __construct() {

			parent::__construct( WPLMP_MODEL, 'WPLMP_MODEL_' );

		}

	}

}
