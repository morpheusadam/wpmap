<?php
/**
 * Controller class
 *
 * @author Flipper Code<hello@flippercode.com>
 * @version 1.0.0
 * @package wp-leaflet-maps-pro
 */

if ( ! class_exists( 'WPLMP_Controller' ) ) {

	/**
	 * Controller class to display views.
	 *
	 * @author: Flipper Code<hello@flippercode.com>
	 * @version: 1.0.0
	 * @package: wp-leaflet-maps-pro
	 */

	class WPLMP_Controller extends Flippercode_Factory_Controller {


		function __construct() {

			parent::__construct( WPLMP_MODEL, 'WPLMP_MODEL_' );

		}

	}

}
