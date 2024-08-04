<?php
/**
 * Controller class
 *
 * @author Flipper Code<hello@flippercode.com>
 * @version 1.0.0
 * @package wp-leaflet-maps-pro
 */

if ( ! class_exists( 'WPLMP_MODEL' ) ) {

	/**
	 * Controller class to display views.
	 *
	 * @author: Flipper Code<hello@flippercode.com>
	 * @version 1.0.0
 	 * @package wp-leaflet-maps-pro
	 */

	class WPLMP_MODEL extends Flippercode_Factory_Model {


		function __construct() {

			parent::__construct( WPLMP_MODEL, 'WPLMP_MODEL_' );

		}

	}

}
